<?php

namespace App\Http\Controllers\Operation;

use App\CheckHoldP2Class;
use App\Http\Controllers\Controller;
use App\Http\Traits\LogTraits;
use App\Services\BehandleInServices;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class BehandleController extends Controller
{
    use LogTraits;
    public function __construct(protected BehandleInServices $behandleInServices) {}
    public function search(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'no_cont' => ['required', 'string', 'max:11'],
            ]);

            $checkHoldP2 = checkHoldP2($request->no_cont);
            if ($checkHoldP2) {
                throw new Exception("Container on Hold P2, Harap release Container terlebih dahulu", 500);
            }

            $rows = $this->behandleInServices->getDataContainer($validated['no_cont']);
            if (empty($rows)) {
                throw new Exception("Data Container tidak ditemukan. Pastikan nomor container sudah benar.", 404);
            } else if($rows->STATUS_CONT > 200) {
                throw new Exception("Container sudah di Behandle In, harap cek kembali data container.", 422);
            } else if($rows->STATUS_CONT < 200) {
                throw new Exception("Container belum diambil (Pickup), harap cek kembali data container.", 422);
            }

            $trucks = $this->behandleInServices->getTruck((string) ($rows->ID_FLAT ?? ''));
            $references = [
                'job_activities' => $this->behandleInServices->getJobActivity(),
                'equipments' => $this->behandleInServices->getAlat(),
                'operators' => $this->behandleInServices->getOperator(),
                'trucks' => $trucks,
                'container_conditions' => $this->behandleInServices->getCondition(),
            ];

            $job_activities = $references['job_activities'];
            $equipments = $references['equipments'];
            $operators = $references['operators'];
            $container_conditions = $references['container_conditions'];

            $view = view('livewire.partials.behandlein.form', compact(
                'rows',
                'job_activities',
                'equipments',
                'operators',
                'trucks',
                'container_conditions',
                'references'
            ))->render();

            return response()->json([
                'data' => $view,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage() ?? 'Terjadi kesalahan saat mencari data Container.',
                'details' => $e->getTrace(),
            ], $e->getCode() ?: 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $dbLegacy = DB::connection('prod');
        try {
            $validated = $request->validate([
                'id_request' => ['required', 'string', 'max:50'],
                'nomerkon' => ['required', 'string', 'max:20'],
                'nospk' => ['required', 'string', 'max:50'],
                'isocode' => ['nullable', 'string', 'max:10'],
                'optradio' => ['nullable', 'string'],
                'noseal' => ['nullable', 'string', 'max:50'],
                'kondisi' => ['nullable'],
                'trucknya' => ['nullable', 'string', 'max:30'],
                'nolok' => ['nullable', 'string', 'max:30'],
                'ukuran' => ['nullable', 'string', 'max:10'],
                'tipe' => ['nullable', 'string', 'max:10'],
                'jenisPekerjaan' => ['nullable'],
                'alat' => ['nullable'],
                'operator' => ['nullable', 'string'],
                'optradiostatus' => ['required'],
            ]);

            $lokasi = $validated['nolok'];
            $strLenLokasi  = strlen($lokasi);
            $jenis_status = json_decode($validated['optradiostatus']);
            $stakoncoar    = $jenis_status->key1;
            $kd_jenis    = $jenis_status->key2;

            // Check if the location string is longer than 8 characters to determine how to extract the block and tier information
            if ($strLenLokasi > 8) {
                $nmBlok = strtoupper(substr($lokasi, 0, 7));
                $nmTier = strtoupper(substr($lokasi, -1));
            } else {
                $nmBlok = strtoupper(substr($lokasi, 0, 6));
                $nmTier = strtoupper(substr($lokasi, -1));
            }

            /** Check data status container */
            $dataSpk = $dbLegacy->selectOne(
                "SELECT ts.ID, ts.NO_SPK, ts.TGL_SPK, ts.NO_DOK, tsc.NO_CONT, tsc.STATUS_CONT FROM t_spk_cont tsc inner join t_spk ts ON tsc.ID = ts.ID and ts.NO_SPK = ? WHERE tsc.NO_CONT = ?",
                [$validated['nospk'], $validated['nomerkon']]
            );

            if($dataSpk->STATUS_CONT < 200) {
                $this->logInfo('behandlein', 'Gagal Behandle In Container', [
                    'error'        => 'Container belum diambil (Pickup). Pastikan container sudah diambil (Pickup)',
                    'no_spk'       => $validated['nospk'] ?? null,
                    'no_container' => $validated['nomerkon'] ?? null,
                    'lokasi'     => $validated['nolok'] ?? null,
                ]);

                throw new Exception("Container belum diambil (Pickup). Pastikan container sudah diambil (Pickup)", 422);
            } else if ($dataSpk->STATUS_CONT > 200) {
                $this->logInfo('behandlein', 'Gagal Behandle In Container', [
                    'error'        => 'Container sudah di Behandle In. Harap cek kembali data container.',
                    'no_spk'       => $validated['nospk'] ?? null,
                    'no_container' => $validated['nomerkon'] ?? null,
                    'lokasi'     => $validated['nolok'] ?? null,
                ]);

                throw new Exception("Container sudah di Behandle In. Harap cek kembali data container.", 422);
            } else if (empty($dataSpk)) {
                $this->logInfo('behandlein', 'Gagal Behandle In Container', [
                    'error'        => 'Container tidak ditemukan atau belum diambil. Pastikan container sudah diambil (Pickup)',
                    'no_spk'       => $validated['nospk'] ?? null,
                    'no_container' => $validated['nomerkon'] ?? null,
                    'lokasi'     => $validated['nolok'] ?? null,
                ]);

                throw new Exception("Container tidak ditemukan atau belum diambil. Pastikan container sudah diambil (Pickup)", 404);
            }

            $validateLocation = ['1B', '1b', 'YB', 'CI'];
            if (!in_array(substr($lokasi, 0, 2), $validateLocation)) {
                $this->logInfo('behandlein', 'Gagal Behandle In Container', [
                    'error'        => 'Lokasi tidak valid. Lokasi harus berada di area 1B, YB, atau CI.',
                    'no_spk'       => $validated['nospk'] ?? null,
                    'no_container' => $validated['nomerkon'] ?? null,
                    'lokasi'     => $validated['nolok'] ?? null,
                ]);

                throw new Exception("Lokasi tidak valid. Lokasi harus berada di area 1B, YB, atau CI.", 422);
            }

            // Check Location and Tier
            $checkLocation = $dbLegacy->selectOne(
                "SELECT NO_CONT FROM t_spk_cont WHERE LOKASI = ? AND TIER = ? AND NO_CONT <> ?",
                [$nmBlok, $nmTier, $validated['nomerkon']]
            );
            if (!empty($checkLocation)) {
                $this->logInfo('behandlein', 'Gagal Behandle In Container', [
                    'error'        => 'Lokasi tersebut sudah digunakan, harap ganti lokasi.',
                    'no_spk'       => $validated['nospk'] ?? null,
                    'no_container' => $validated['nomerkon'] ?? null,
                    'lokasi'     => $validated['nolok'] ?? null,
                ]);

                throw new Exception("Lokasi tersebut sudah digunakan, harap ganti lokasi.", 422);
            }

            // Check location exists in denah table
            $checkDenah = $dbLegacy->selectOne(
                "SELECT CONCAT(NM_BLOK,'0',LEVEL_4) as LOKASI FROM t_denah_lapangan WHERE NM_BLOK = ? AND LEVEL_4 = ?",
                [$nmBlok, $nmTier]
            );
            if (empty($checkDenah)) {
                $this->logInfo('behandlein', 'Gagal Behandle In Container', [
                    'error'        => 'Lokasi tidak valid. Lokasi tidak ditemukan di denah lapangan',
                    'no_spk'       => $validated['nospk'] ?? null,
                    'no_container' => $validated['nomerkon'] ?? null,
                    'lokasi'     => $validated['nolok'] ?? null,
                ]);

                throw new Exception("Lokasi tidak valid. Lokasi tidak ditemukan di denah lapangan", 422);
            }

            if($checkDenah->LOKASI !== $lokasi) {
                $this->logInfo('behandlein', 'Gagal Behandle In Container', [
                    'error'        => 'Lokasi tidak valid. Lokasi tidak sesuai dengan denah lapangan',
                    'no_spk'       => $validated['nospk'] ?? null,
                    'no_container' => $validated['nomerkon'] ?? null,
                    'lokasi'     => $validated['nolok'] ?? null,
                ]);

                throw new Exception("Lokasi tidak valid. Lokasi tidak sesuai dengan denah lapangan", 422);
            }

            $setBehandleIn = $this->behandleInServices->setBehandleIn([
                'id_request' => $validated['id_request'],
                'no_cont' => $validated['nomerkon'],
                'fl_dg' => $request->test ?? null,
                'no_spk' => $validated['nospk'],
                'iso_code' => $validated['isocode'] ?? null,
                'seal_no' => $validated['noseal'] ?? null,
                'kondisi' => $validated['kondisi'] ?? null,
                'kondisi_seal' => $validated['optradio'] ?? null,
                'truck_no' => $validated['trucknya'] ?? null,
                'lokasi' => $lokasi,
                'nm_blok' => $nmBlok,
                'nm_tier' => $nmTier,
                'ukuran' => $validated['ukuran'] ?? null,
                'tipe' => $validated['tipe'] ?? null,
                'kd_jenis' => $kd_jenis,
                'stakoncoar' => $stakoncoar,
                'job_activity_id' => $validated['jenisPekerjaan'] ?? null,
                'alat_id' => $validated['alat'] ?? null,
                'operator_id' => $validated['operator'] ?? null,
                'spk' => $dataSpk,
            ]);

            $responseCode = $setBehandleIn->getStatusCode();
            $setBehandleIn = $setBehandleIn->getContent();
            $response = json_decode($setBehandleIn, true);

            if ($responseCode !== 200 || $response['status'] !== 'success') {
                throw new Exception($response['message'] ?? 'Terjadi kesalahan saat menyimpan data Behandle In.', $responseCode);
            }

            return response()->json([
                'status'  => 'success',
                'message' => $response['message'] ?? 'Data Container berhasil diproses behandle in',
                'cont'    => $response['cont'] ?? '',
            ]);
        } catch (Exception $e) {
            $errorCode = ($e->getCode() === 404) ? 404 : 500;

            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage() ?: 'Terjadi kesalahan saat memproses behandle in container',
                'error'   => $e->getMessage(),
            ], $errorCode);
        }
    }
}
