<?php

namespace App\Http\Controllers\Operation;

use App\Http\Controllers\Controller;
use App\Http\Traits\LogTraits;
use App\Http\Traits\SendNPCTTraits;
use App\Services\PickupServices;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class PickupController extends Controller
{
    use LogTraits, SendNPCTTraits;

    public function __construct(protected PickupServices $services) {}

    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'no_spk' => ['required', 'string', 'max:50'],
        ]);

        try {
            $rows = DB::connection('prod_new')->select(
                "SELECT
                    ts.NO_SPK AS no_spk,
                    tsc.NO_CONT AS no_container,
                    tsc.UKR_CONT AS ukuran,
                    tsc.STATUS_CONT AS status_cont,
                    tsc.FL_SEND_NPCT1 AS fl_send_npct1,
                    tsc.ID_FLAT AS id_flat
                FROM t_spk ts
                INNER JOIN t_spk_cont tsc ON ts.ID = tsc.ID
                WHERE ts.NO_SPK = ?
                    AND tsc.STATUS_CONT IN (100)
                    AND ts.WK_REQ >= DATE '2026-01-01'
                ORDER BY tsc.NO_CONT",
                [$validated['no_spk']]
            );

            $trucks = $this->services->getTrucks();;

            if (blank($rows)) {
                return response()->json([
                    'message' => 'Data SPK tidak ditemukan. Pastikan nomor SPK sudah benar.',
                ], 404);
            }

            $view = view('livewire.partials.pickup.form', compact('rows', 'trucks'))->render();

            return response()->json([
                'data' => $view,
            ]);
        } catch (Throwable $th) {
            $this->logError('pickup', 'Gagal mencari data SPK pickup', [
                'no_spk' => $validated['no_spk'],
                'error' => $th->getMessage(),
                'exception' => get_class($th),
            ]);

            return response()->json([
                'message' => 'Terjadi kesalahan saat mencari data SPK.',
            ], 500);
        }
    }

    public function sendPickup(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'no_spk' => ['required', 'string', 'max:50'],
            'no_container' => ['required', 'string', 'max:20'],
            'no_truck' => ['nullable', 'string', 'max:30'],
        ]);

        $db = DB::connection('prod_new');
        $db->beginTransaction();
        try {
            $cont = $validated['no_container'];
            $plat = $validated['no_truck'] ?? null;
            $datacont = $this->services->getDetailCont($validated['no_container'], $validated['no_truck'], $validated['no_spk']);

            if ($datacont == null || empty($datacont) || $datacont->STATUS_CONT > 100) {
                throw new Exception('Data pickup/container tidak ditemukan atau sudah diproses.', 404);
            }

            // 2. Pemanggilan message2a dan message2b (kirim data ke NPCT)
            $res1 = $this->message2a($datacont);
            $res2 = $this->message2b($datacont);

            $data1 = $res1->code ?? null;
            $data2 = $res2->code ?? null;

            // 3. Validasi status kode respon NPCT
            if ($data1 == '00' && $data2 == '00') {
                $db->table('t_spk_cont')
                    ->where('NO_CONT', $cont)
                    ->where('status_cont', '100')
                    ->update([
                        'FL_SEND_NPCT1' => 'Y',
                        'ID_FLAT'       => $plat,
                    ]);

                $db->commit();

                $this->logInfo('pickup', 'Data pickup berhasil dikirim ke NPCT', [
                    'no_spk' => $validated['no_spk'],
                    'no_container' => $validated['no_container'],
                    'no_truck' => $validated['no_truck'] ?? null,
                    'response_code_message2a' => $data1,
                    'response_code_message2b' => $data2,
                ]);

                return response()->json([
                    'status'  => 'success',
                    'message' => 'Data pickup berhasil dikirim.',
                    'cont'    => $datacont,
                ]);
            } else {
                $this->logError('pickup', 'Gagal mengirim data pickup ke NPCT', [
                    'no_spk' => $validated['no_spk'],
                    'no_container' => $validated['no_container'],
                    'no_truck' => $validated['no_truck'] ?? null,
                    'response_code_message2a' => $data1,
                    'response_code_message2b' => $data2,
                ]);

                throw new Exception('Gagal mengirim data pickup ke NPCT. Kode respon: ' . $data1 . ', ' . $data2, 500);
            }
        } catch (Exception $th) {
            if ($db->transactionLevel() > 0) {
                $db->rollBack();
            }
            $this->logError('pickup', 'Gagal mengirim data pickup ke NPCT', [
                'error' => $th->getMessage(),
                'no_spk' => $validated['no_spk'],
                'no_container' => $validated['no_container'],
                'no_truck' => $validated['no_truck'] ?? null,
            ]);
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengirim data pickup.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'no_spk' => ['required', 'string', 'max:50'],
            'no_container' => ['required', 'string', 'max:20'],
            'no_truck' => ['nullable', 'string', 'max:30'],
        ]);

        $no_spk = $validated['no_spk'];
        $cont   = $validated['no_container'];
        $plat   = $validated['no_truck'] ?? null;

        $db = DB::connection('prod_new');
        $db->beginTransaction();
        try {
            $datacont = $this->services->getDetailCont($cont, $plat, $no_spk);

            if (empty($datacont) || $datacont->STATUS_CONT > 100) {
                throw new Exception('Data pickup/container tidak ditemukan atau sudah diproses.', 404);
            }

            $now = now();

            // a. INSERT TO t_op_pickup (Oracle)
            $user = Auth::user()?->username ?? Auth::user()?->user_name ?? 'SYSTEM';
            $db->table('t_op_pickup')->insert([
                'OPERATOR' => $user,
                'NO_SPK'   => $no_spk,
                'NO_CONT'  => $cont,
                'JNS_PICK' => 'S',
                'W_PICKUP' => $now,
            ]);

            // b. UPDATE t_spk_cont
            $contUpdated = 0;
            if (!empty($datacont->ID)) {
                $contUpdated = $db->table('t_spk_cont')
                    ->where('ID', $datacont->ID)
                    ->where('NO_CONT', $cont)
                    ->update([
                        'STATUS_CONT'   => '200',
                        'ID_FLAT'       => $plat,
                        'FL_SEND_NPCT1' => 'Y',
                    ]);
            }

            if ($contUpdated === 0) {
                $db->table('t_spk_cont')
                    ->whereIn('ID', function ($q) use ($no_spk) {
                        $q->select('ID')->from('t_spk')->where('NO_SPK', $no_spk);
                    })
                    ->where('NO_CONT', $cont)
                    ->update([
                        'STATUS_CONT'   => '200',
                        'ID_FLAT'       => $plat,
                        'FL_SEND_NPCT1' => 'Y',
                    ]);
            }

            // c. UPDATE t_job_slip
            $db->table('t_job_slip')
                ->where('NO_SPK', $no_spk)
                ->where('NO_CONT', $cont)
                ->update([
                    'KD_STATUS' => '30',
                    'STATUS'    => 'WAITING',
                ]);

            // d. INSERT TO t_operation (Oracle)
            $db->table('t_operation')->insert([
                'NO_SPK'    => $no_spk,
                'NO_CONT'   => $cont,
                'JNS_DOK'   => $datacont->JNS_DOK ?? null,
                'NO_DOK'    => $datacont->NO_DOK ?? null,
                'TGL_DOK'   => $datacont->TGL_DOK ?? null,
                'WK_PICKUP' => $now,
            ]);

            // e. UPDATE report_behandle (Oracle)
            $reportQuery = $db->table('report_behandle')
                ->where('NO_CONT', $cont)
                ->where('RB1_NO_SPK', $no_spk);

            if (!empty($datacont->NO_DOK)) {
                $reportQuery->where('REQ_NO_DOK', $datacont->NO_DOK);
            }

            $report = $reportQuery->orderBy('ID', 'desc')->first();

            if ($report) {
                $db->table('report_behandle')
                    ->where('ID', $report->ID)
                    ->update([
                        'PRN_PICKUP' => $now,
                        'PRN_TID'    => $plat,
                    ]);
            }

            // f. SELECT & UPDATE STATUS SPK
            $spkKont100 = $db->table('t_spk')
                ->join('t_spk_cont', 't_spk.ID', '=', 't_spk_cont.ID')
                ->where('t_spk.NO_SPK', $no_spk)
                ->where('t_spk_cont.STATUS_CONT', '100')
                ->count();

            if ($spkKont100 === 0) {
                $db->table('t_spk')
                    ->where('NO_SPK', $no_spk)
                    ->update(['KD_STATUS' => '200']);
            }

            $db->commit();

            $this->logInfo('pickup', 'Berhasil Pickup Container', [
                'no_spk'       => $no_spk,
                'no_container' => $cont,
                'no_truck'     => $plat,
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Data pickup berhasil diproses.',
                'cont'    => $datacont,
            ]);
        } catch (Exception $th) {
            if ($db->transactionLevel() > 0) {
                $db->rollBack();
            }

            $errorCode = ($th->getCode() === 404) ? 404 : 500;

            $this->logError('pickup', 'Gagal memproses data pickup', [
                'error'        => $th->getMessage(),
                'no_spk'       => $validated['no_spk'] ?? null,
                'no_container' => $validated['no_container'] ?? null,
                'no_truck'     => $validated['no_truck'] ?? null,
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => $th->getMessage() ?: 'Terjadi kesalahan saat memproses data pickup.',
                'error'   => $th->getMessage(),
            ], $errorCode);
        }
    }
}
