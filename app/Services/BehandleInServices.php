<?php

namespace App\Services;

use App\Http\Traits\LogTraits;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BehandleInServices
{
    use LogTraits;
    public function getDataContainer(string $no_cont)
    {
        $data = DB::connection('prod_new')->selectOne(
            "SELECT
                    b.*,
	                a.NO_SPK,
                    a.NO_DOK,
                    a.TGL_DOK,
                    E.ID as ID_REQUEST,
                    E.UKR_CONT,
                    E.TIPE_CONT,
                    E.STATUS_BILLING,
                    E.FL_PERBAIKI,
                    b.STATUS_CONT
                from
                    t_spk a
                join t_spk_cont b on
                    a.ID = b.ID
                left join (
                    select
                        d.ID,
                        c.NO_CONT,
                        d.NO_DOK,
                        d.TGL_DOK,
                        c.UKR_CONT,
                        c.TIPE_CONT,
                        c.STATUS_BILLING,
                        c.FL_PERBAIKI
                    from
                        t_request_cont c
                    inner join t_request d on
                        c.ID = d.ID ) E on
                    E.NO_CONT = b.NO_CONT
                    and E.NO_DOK = a.NO_DOK
                    and E.TGL_DOK = a.TGL_DOK
                where
                	b.NO_CONT like ?
                    and E.FL_PERBAIKI = ?",
            ["%" . $no_cont . "%", 'N']
        );

        if ($data === null) {
            return null;
        }

        $normalizedData = new \stdClass();
        foreach ((array) $data as $column => $value) {
            $normalizedData->{strtoupper($column)} = $value;
        }

        $data = $normalizedData;

        return $data;
    }

    public function getJobActivity()
    {
        $data = DB::connection('prod_new')->select("SELECT * FROM m_job_activity");
        return $data;
    }

    public function getAlat()
    {
        $data = DB::connection('prod_new')->select("SELECT * FROM t_reff_alat");
        return $data;
    }

    public function getOperator()
    {
        $rows = DB::connection('prod_new')->select(
            "SELECT * FROM reff_user WHERE KD_GROUP LIKE '%OPR%'"
        );

        return array_map(function ($row) {
            $normalizedRow = new \stdClass();
            foreach ((array) $row as $column => $value) {
                $normalizedRow->{strtoupper($column)} = $value;
            }

            return $normalizedRow;
        }, $rows);
    }

    public function getTruck(string $id_flat)
    {
        $data = DB::connection('prod_new')->select("SELECT NO_TRUCK from reff_truck where NOT NO_TRUCK = ?", [$id_flat]);
        return $data;
    }

    public function getCondition()
    {
        $data = DB::connection('prod_new')->select("SELECT ID,KONDISI from REFF_KONDISI");
        return $data;
    }

    public function setBehandleIn(array $data)
    {
        $dbLegacy = DB::connection('prod');
        $dbNew = DB::connection('prod_new');

        $dataSPK = $data['spk'] ?? null;
        $user = Auth::user()?->username ?? Auth::user()?->user_name ?? 'SYSTEM';

        $dbNew->beginTransaction();
        try {
            // Percepatan, Save ke LOG_PERGERAKAN_KONTAINER
            $dbNew->insert(
                "INSERT INTO LOG_PERGERAKAN_KONTAINER (NO_CONT, NO_SPK, ACTIVITY)
                VALUES (?, ?, ?)",
                [
                    $data['no_cont'],
                    $data['no_spk'],
                    'BEHANDLEIN',
                ]
            );

            // Insert data behandle in ke table t_op_behandle_in
            $dataBehandleIn = [
                'NO_SPK'         => $dataSPK->NO_SPK ?? null,
                'OPERATOR'         => $user,
                'NO_CONT'         => $data['no_cont'] ?? null,
                'KONDISI_CONT'     => $data['kondisi'] ?? null,
                'KONDISI_SEAL'     => $data['kondisi_seal'] ?? null,
                'NO_SEAL'         => $data['seal_no'] ?? null,
                'ROOM'            => $data['lokasi'] ?? null,
                'ISO_CODE'         => $data['iso_code'] ?? null,
                'W_BEHANDLE'     => date('Y-m-d H:i:s'),
            ];
            $insertBehandleIn = $dbNew->table('T_OP_BEHANDLEIN')->insert($dataBehandleIn);

            // Update SPK dan SPK_CONT di table legacy (prod) untuk menandai container sudah di behandle in
            $execUpdate = $this->prosesUpdateData($data);
            $responseCode = $execUpdate->getStatusCode();
            $execUpdate = $execUpdate->getContent();
            $response = json_decode($execUpdate, true);

            if ($responseCode !== 200 || $response['status'] !== 'success') {
                throw new Exception($response['message'] ?? 'Terjadi kesalahan saat update SPK & Job Slip Container.', $responseCode);
            }

            $this->logInfo('behandlein', 'Berhasil Behandle In Container', [
                'no_spk'       => $data['no_spk'],
                'no_container' => $data['no_cont'],
                'lokasi'     => $data['nm_blok'] . $data['nm_tier'],
            ]);

            $dbNew->commit();
            return response()->json([
                'status'  => 'success',
                'message' => 'Data Container berhasil diproses behandle in',
                'cont'    => $data['no_cont'] ?? '',
            ], 200);
        } catch (Exception $th) {
            $dbNew->rollBack();
            $errorCode = ($th->getCode() === 404) ? 404 : 500;

            $this->logError('behandlein', 'Gagal Behandle In Container', [
                'error'        => $th->getMessage(),
                'no_spk'       => $data['no_spk'] ?? null,
                'no_container' => $data['no_cont'] ?? null,
                'lokasi'     => ($data['nm_blok'] ?? '') . ($data['nm_tier'] ?? ''),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => $th->getMessage() ?: 'Terjadi kesalahan saat memproses behandle in container',
                'error'   => $th->getMessage(),
            ], $errorCode);
        }
    }

    private function prosesUpdateData(array $data)
    {
        $dbLegacy = DB::connection('prod_new');
        $dbLegacy->beginTransaction();

        $dataSPK = $data['spk'] ?? null;
        $user = Auth::user()?->username ?? Auth::user()?->user_name ?? 'SYSTEM';

        try {
            // Check gatepass behandle
            $dataGatepass = $dbLegacy->selectOne(
                "SELECT ID, WK_REK, NM_KAPAL, NO_VOY, NO_DOK, JNS_DOK FROM t_gatepass WHERE NO_CONT = ? AND NO_DOK = ? AND STATUS = 'WAITING' AND JNS_KEGIATAN = '1'",
                [$data['no_cont'], $dataSPK->NO_DOK]
            );

            // get KD_CONT_JENIS
            if (!empty($dataGatepass)) {
                $kdCont = $dbLegacy->table('t_cocostscont')
                    ->join('t_cocostshdr', 't_cocostscont.ID', '=', 't_cocostshdr.ID')
                    ->select(['t_cocostscont.ID', 't_cocostscont.KD_CONT_TIPE'])
                    ->where('t_cocostscont.NO_CONT', $data['no_cont'])
                    ->where('t_cocostshdr.NM_ANGKUT', $dataGatepass->NM_KAPAL)
                    ->where('t_cocostshdr.NO_VOY_FLIGHT', $dataGatepass->NO_VOY)
                    ->orderByDesc('t_cocostshdr.ID')
                    ->first();
            } else {
                $kdCont = $dbLegacy->table('t_cocostscont')
                    ->select(['ID', 'KD_CONT_TIPE'])
                    ->where('NO_CONT', $data['no_cont'])
                    ->orderByDesc('ID')
                    ->first();
            }

            $dataAfInspection = array(
                'ISO_CODE'             => $data['iso_code'] ?? null,
                'ID_FLAT'             => $data['truck_no'] ?? null,
                'LOKASI'             => $data['nm_blok'] ?? null,
                'TIER'                 => $data['nm_tier'] ?? null,
                'STATUS_CONT_COARI' => $data['stakoncoar'] ?? null,
                'STATUS_CONT'         => '450',
                'NO_SEAL'            => $data['seal_no'] ?? null
            );

            $dbLegacy->table('t_spk_cont')
                ->where('NO_CONT', $data['no_cont'])
                ->where('ID', $dataSPK->ID ?? null)
                ->update($dataAfInspection);

            $dbLegacy->table('t_spk')
                ->where('ID', $dataSPK->ID ?? null)
                ->where('NO_SPK', $dataSPK->NO_SPK ?? null)
                ->update(['STATUS_SPK' => '400']);

            $dbLegacy->table('t_operation')
                ->where('NO_SPK', $dataSPK->NO_SPK ?? null)
                ->where('NO_CONT', $data['no_cont'] ?? null)
                ->update(['WK_IN' => Carbon::now()->format('Y-m-d H:i:s')]);

            if ($data['tipe'] == 'RFR') {
                $dataUpdateRequestCont = [
                    'FL_PERBAIKI' => 'Y',
                    'KD_CONT_JENIS' => $data['kd_jenis'] ?? null,
                    'UKR_CONT' => $data['ukuran'] ?? null,
                    'STATUS_DG' => null,
                    'TIPE_CONT' => $data['tipe'] ?? null,
                    'STATUS_BILLING' => $data['tipe'] ?? null,
                ];
            } else if ($data['fl_dg'] == null) {
                $dataUpdateRequestCont = [
                    'FL_PERBAIKI' => 'Y',
                    'KD_CONT_JENIS' => $data['kd_jenis'] ?? null,
                    'UKR_CONT' => $data['ukuran'] ?? null,
                    'STATUS_DG' => null,
                    'STATUS_BILLING' => $data['tipe'] ?? null,
                ];
            } else {
                $dataUpdateRequestCont = [
                    'FL_PERBAIKI' => 'Y',
                    'KD_CONT_JENIS' => $data['kd_jenis'] ?? null,
                    'UKR_CONT' => $data['ukuran'] ?? null,
                    'STATUS_DG' => $data['fl_dg'] ?? null,
                    'STATUS_BILLING' => $data['tipe'] ?? null,
                ];
            }

            $dbLegacy->table('t_request_cont')
                ->where('ID', $data['id_request'] ?? null)
                ->where('NO_CONT', $data['no_cont'] ?? null)
                ->update($dataUpdateRequestCont);

            // 1. Select Data (Gunakan Query Builder, bukan Raw Query untuk mencegah SQL Injection)
            $cekJobSlip = $dbLegacy->table('t_job_slip')
                ->where([
                    'NO_SPK'  => $dataSPK->NO_SPK ?? null,
                    'NO_CONT' => $data['no_cont'] ?? null,
                    'NO_DOK'  => $dataSPK->NO_DOK ?? null,
                    'STATUS'  => 'WAITING'
                ])
                ->orderByDesc('ID_JOB_SLIP')
                ->first();

            // 2. Validasi Ketersediaan Data (Best Practice: hindari error "Trying to get property of non-object")
            if ($cekJobSlip) {
                // Inisialisasi variabel agar tidak dieksekusi berulang (DRY - Don't Repeat Yourself)
                $now = now()->format('Y-m-d H:i:s');

                // 3. Update Data (Cukup gunakan ID_JOB_SLIP jika itu Primary Key, tidak perlu where atribut lain)
                $dbLegacy->table('t_job_slip')
                    ->where('ID_JOB_SLIP', $cekJobSlip->ID_JOB_SLIP)
                    ->update([
                        'NO_DOK'       => $dataSPK->NO_DOK ?? null,
                        'NO_GATEPASS'  => $dataGatepass->ID ?? null,
                        'TGL_GATEPASS' => $dataGatepass->WK_REK ?? null,
                        'STATUS'       => 'DONE',
                        'KD_STATUS'    => '50',
                        'WK_STATUS'    => $now,
                        'OPERATOR'     => $user,
                        'STATUS_JOB'   => 'Y',
                    ]);

                // 4. Insert Data Detail
                $dbLegacy->table('t_job_detail')->insert([
                    'ID_JOB_SLIP'  => $cekJobSlip->ID_JOB_SLIP,
                    'NO_SPK'       => $dataSPK->NO_SPK, // Asumsi $noSPK sudah didefinisikan sebelumnya
                    'NO_CONT'      => $data['no_cont'],
                    'JENIS'        => 'BEHANDLE 1',
                    'TYPE_CONT'    => $kdCont->KD_CONT_TIPE ?? null,
                    'ID_PEKERJAAN' => $data['job_activity_id'] ?? null,
                    'ID_ALAT'      => $data['alat_id'],
                    'OPERATOR'     => $data['operator_id'],
                    'I_PENGGUNAAN' => null,
                    'ID_USER'      => $user,
                    'WK_STATUS'    => $now,
                ]);
            }

            if (!empty($dataGatepass)) {
                $arrdata = array(
                    'NO_SPK'        => $dataSPK->NO_SPK,
                    'TGL_SPK'        => $dataSPK->TGL_SPK,
                    'NO_CONT'         => $data['no_cont'],
                    'NO_DOK'        => $dataGatepass->NO_DOK,
                    'JNS_DOK'        => $dataGatepass->JNS_DOK,
                    'NO_GATEPASS'    => $dataGatepass->ID,
                    'LOKASI_AWAL'    => $data['nm_blok'],
                    'JNS_JOB_SLIP'    => 'MARSHALLING',
                    'JENIS'            => 'BEHANDLE 1',
                    'TIER_AWAL'        => $data['nm_tier'],
                    'TGL_GATEPASS'    => $dataGatepass->WK_REK,
                    'KD_STATUS'        => '20',
                    'STATUS'        => 'WAITING',
                    'WK_STATUS'        => date('Y-m-d H:i:s'),
                    'OPERATOR'        => $user
                );
                $dbLegacy->table('t_job_slip')->insert($arrdata);
            }

            $dataReport = $dbLegacy->table('report_behandle')
                ->where('NO_CONT', $data['no_cont'])
                ->where('RB1_NO_SPK', $dataSPK->NO_SPK)
                ->orderByDesc('ID')
                ->first();

            if (!empty($dataReport)) {
                $updateReport = [
                    'NO_CONT' => $data['no_cont'],
                    'PRN_BEHANDLE_IN' => Carbon::now()->format('Y-m-d H:i:s'),
                    'PRN_KONDISI_CONT' => $data['kondisi'] ?? null,
                    'PRN_NO_SEAL' => $data['seal_no'] ?? null,
                    'PRN_ISO_CODE' => $data['iso_code'] ?? null,
                    'PRN_LOKASI' => ($data['nm_blok'] ?? '') . '0' . ($data['nm_tier'] ?? '')
                ];
                $dbLegacy->table('report_behandle')
                    ->where('ID', $dataReport->ID ?? null)
                    ->where('NO_CONT', $data['no_cont'] ?? null)
                    ->where('REQ_NO_DOK', $dataSPK->NO_DOK ?? null)
                    ->update($updateReport);
            }

            $dbLegacy->commit();
            return response()->json([
                'status'  => 'success',
                'message' => 'Data Container berhasil diproses update spk & jobslip',
                'cont'    => $data['no_cont'] ?? '',
            ], 200);
        } catch (Exception $th) {
            $dbLegacy->rollBack();
            $errorCode = ($th->getCode() === 404) ? 404 : 500;

            $this->logError('behandlein', 'Gagal Behandle In pada saat update SPK & Job Slip', [
                'error'        => $th->getMessage(),
                'no_spk'       => $data['no_spk'] ?? null,
                'no_container' => $data['no_cont'] ?? null,
                'lokasi'     => ($data['nm_blok'] ?? '') . ($data['nm_tier'] ?? ''),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => $th->getMessage() ?: 'Terjadi kesalahan saat memproses behandle in container',
                'error'   => $th->getMessage(),
            ], $errorCode);
        }
    }
}
