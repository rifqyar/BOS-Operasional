<?php

namespace App\Services;

use App\Http\Traits\LogTraits;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class MarshallingYardServices
{
    use LogTraits;

    public function getAllJobsYard(int $perPage = 10): LengthAwarePaginator
    {
        return $this->baseQueryYard()->orderByDesc('A.ID_JOB_SLIP')->paginate($perPage);
    }

    public function searchYard(string $keyword, int $perPage = 10): LengthAwarePaginator
    {
        return $this->baseQueryYard()
            ->where('A.NO_CONT', 'like', '%' . strtoupper(trim($keyword)) . '%')
            ->orderByDesc('A.ID_JOB_SLIP')
            ->paginate($perPage);
    }

    private function baseQueryYard()
    {
        return DB::connection('prod_new')->table('t_job_slip as A')
            ->join('t_spk as B', 'A.NO_SPK', '=', 'B.NO_SPK')
            ->join('t_gatepass as C', 'A.NO_GATEPASS', '=', 'C.ID')
            ->select([
                'A.ID_JOB_SLIP', 'A.NO_CONT', 'C.UKR_CONT', 'A.LOKASI_AWAL',
                'A.LOKASI_AKHIR', 'A.TIER_AWAL', 'A.TIER_AKHIR', 'A.JENIS',
            ])
            ->where('A.STATUS', 'WAITING')
            ->where('A.KD_STATUS', '20')
            ->where('A.LOKASI_AKHIR', 'like', '1A%')
            ->where('C.FL_ACTIVE', 'Y')
            ->whereIn('A.JENIS', ['EX BEHANDLE 1', 'EX BEHANDLE 2'])
            ->groupBy([
                'A.ID_JOB_SLIP', 'A.NO_CONT', 'C.UKR_CONT', 'A.LOKASI_AWAL',
                'A.LOKASI_AKHIR', 'A.TIER_AWAL', 'A.TIER_AKHIR', 'A.JENIS',
            ]);
    }

    public function getDetailYard(string|int $idJobSlip): ?object
    {
        $sql = "
            SELECT DISTINCT A.ID_JOB_SLIP, A.NO_CONT, C.UKR_CONT,
                A.LOKASI_AWAL, A.LOKASI_AKHIR, A.TIER_AWAL, A.TIER_AKHIR,
                A.JENIS, D.RESPON, E.LNSW_NOAJU
            FROM t_job_slip A
            INNER JOIN t_spk B ON A.NO_SPK = B.NO_SPK
            INNER JOIN t_spk_cont C ON B.ID = C.ID
            INNER JOIN t_gatepass D ON A.NO_GATEPASS = D.ID
            LEFT JOIN (SELECT * FROM t_ppk_hdr WHERE LNSW_KD_RESPON = ?) E
                ON D.NO_DOK = E.NO_RESPON AND D.TGL_DOK = E.TG_RESPON
            WHERE A.STATUS = 'WAITING' AND A.KD_STATUS = '20'
                AND C.STATUS_CONT != 900 AND D.FL_ACTIVE = 'Y'
                AND A.JENIS IN ('BEHANDLE 1', 'BEHANDLE 2', 'EX BEHANDLE 1', 'EX BEHANDLE 2')
                AND A.ID_JOB_SLIP = ?
            GROUP BY A.ID_JOB_SLIP, A.NO_CONT, C.UKR_CONT,
                A.LOKASI_AWAL, A.LOKASI_AKHIR, A.TIER_AWAL, A.TIER_AKHIR,
                A.JENIS, D.RESPON, E.LNSW_NOAJU
            ORDER BY A.ID_JOB_SLIP DESC";

        try {
            return DB::connection('prod_new')->selectOne($sql, ['005', $idJobSlip]);
        } catch (Throwable $e) {
            $this->logError('marshalling-yard', 'Gagal mengambil detail Marshalling Yard.', [
                'stage' => 'getDetailYard', 'id_job_slip' => $idJobSlip,
                'error' => $e->getMessage(), 'exception' => get_class($e),
            ]);
            throw $e;
        }
    }

    public function getJobActivity()
    {
        return DB::connection('prod_new')->table('m_job_activity')->get();
    }

    public function getEquipment()
    {
        return DB::connection('prod_new')->table('t_reff_alat')->get();
    }

    public function getOperator()
    {
        return DB::connection('prod_new')->table('reff_user')->where('KD_GROUP', 'like', '%OPR%')->get();
    }

    public function getTruck()
    {
        return DB::connection('prod_new')->table('reff_truck')->orderBy('NO_TRUCK')->get();
    }

    /** Seluruh pemeriksaan dan mutasi dilakukan pada prod_new. */
    public function storeMarshallingYard(array $data, string $username): array
    {
        $db = DB::connection('prod_new');
        $idJobSlip = (int) ($data['idJobSlip'] ?? 0);
        $noCont = strtoupper(trim((string) ($data['nocont'] ?? '')));
        $lokasiAkhir = strtoupper(trim((string) ($data['lokak'] ?? '')));
        $stage = 'begin_transaction';
        $transactionStarted = false;

        try {
            $db->beginTransaction();
            $transactionStarted = true;

            if ($idJobSlip <= 0 || $noCont === '' || $lokasiAkhir === '') {
                throw new RuntimeException('ID pekerjaan, nomor kontainer, dan lokasi akhir wajib diisi.', 422);
            }
            if (trim($username) === '') {
                throw new RuntimeException('Username operator tidak ditemukan.', 422);
            }

            $stage = 'validasi_job_slip';
            $jobSlip = $db->table('t_job_slip')
                ->where('ID_JOB_SLIP', $idJobSlip)->where('NO_CONT', $noCont)
                ->where('STATUS', 'WAITING')->where('KD_STATUS', '20')
                ->lockForUpdate()->first();
            if (!$jobSlip) {
                throw new RuntimeException('Data Job Slip tidak ditemukan atau sudah diproses.', 404);
            }

            $stage = 'validasi_spk';
            $spk = $db->table('t_spk')->where('NO_SPK', $jobSlip->NO_SPK)->orderByDesc('ID')->first();
            if (!$spk) {
                throw new RuntimeException('Data SPK untuk pekerjaan ini tidak ditemukan.', 404);
            }
            $spkCont = $db->table('t_spk_cont')->where('ID', $spk->ID)->where('NO_CONT', $noCont)
                ->lockForUpdate()->first();
            if (!$spkCont) {
                throw new RuntimeException('Kontainer tidak ditemukan pada SPK pekerjaan ini.', 404);
            }

            // Teks status diambil dari reff_status_spk agar pesan validasi mudah dipahami operator.
            $statusSekarang = $db->table('reff_status_spk')->where('ID', $spkCont->STATUS_CONT)->value('KETERANGAN');
            $statusYard = $db->table('reff_status_spk')->where('ID', '450')->value('KETERANGAN');
            if (!$statusSekarang || !$statusYard) {
                throw new RuntimeException('Referensi keterangan status SPK tidak ditemukan. Hubungi administrator.', 500);
            }
            if (mb_strtoupper(trim((string) $statusSekarang)) !== mb_strtoupper(trim((string) $statusYard))) {
                throw new RuntimeException(
                    "Status SPK saat ini '{$statusSekarang}'. Proses Marshalling Yard memerlukan status '{$statusYard}'.",
                    422
                );
            }

            $stage = 'validasi_lokasi';
            // Aturan panjang dan pemotongan lokasi mengikuti legacy; $cekJumlah legacy belum didefinisikan.
            $cekJumlah = strlen($lokasiAkhir);
            $nmBlock = $cekJumlah > 8 ? substr($lokasiAkhir, 0, 8) : substr($lokasiAkhir, 0, 7);
            $nmTier = substr($lokasiAkhir, -1);
            if ($nmBlock === '' || $nmTier === '') {
                throw new RuntimeException('Format lokasi akhir tidak valid.', 422);
            }

            $stage = 'validasi_pemeriksaan';
            $pemeriksaan = $db->table('t_op_inspection')->where('NO_CONT', $noCont)
                ->where('NO_SPK', $jobSlip->NO_SPK)->orderByDesc('ID')->first();
            if (!$pemeriksaan || strtoupper((string) $pemeriksaan->STATUS) !== 'DONE') {
                throw new RuntimeException('Kontainer belum selesai diperiksa.', 422);
            }

            $stage = 'validasi_tipe_kontainer';
            $tipeKontainer = $db->table('t_cocostscont')->where('NO_CONT', $noCont)->orderByDesc('ID')->first();
            if (!$tipeKontainer) {
                throw new RuntimeException('Tipe kontainer tidak ditemukan.', 404);
            }

            $now = now()->format('Y-m-d H:i:s');
            $stage = 'update_job_slip_lama';
            $updated = $db->table('t_job_slip')->where('ID_JOB_SLIP', $idJobSlip)->where('NO_CONT', $noCont)
                ->where('STATUS', 'WAITING')->where('KD_STATUS', '20')->update([
                    'STATUS' => 'DONE', 'STATUS_JOB' => 'Y', 'KD_STATUS' => '50',
                    'OPERATOR' => $username, 'WK_STATUS' => $now,
                ]);
            if ($updated < 1) {
                throw new RuntimeException('Job Slip tidak berhasil diperbarui atau sudah diproses.', 409);
            }

            $stage = 'insert_job_detail';
            $db->table('t_job_detail')->insert([
                'ID_JOB_SLIP' => $idJobSlip, 'NO_SPK' => $jobSlip->NO_SPK, 'NO_CONT' => $noCont,
                'JENIS' => 'EX BEHANDLE 1', 'TYPE_CONT' => $tipeKontainer->KD_CONT_TIPE,
                'ID_PEKERJAAN' => $data['jenispekerjaan1'] ?? 0, 'ID_ALAT' => $data['alat1'] ?? null,
                'OPERATOR' => $data['operator1'] ?? null, 'ID_PEKERJAAN_2' => $data['jenispekerjaan2'] ?? 0,
                'ID_TRUCK' => $data['truck1'] ?? null, 'OPERATOR_2' => $data['operator2'] ?? null,
                'ID_PEKERJAAN_3' => $data['jenispekerjaan3'] ?? 0, 'ID_ALAT_3' => $data['alat3'] ?? null,
                'OPERATOR_3' => $data['operator3'] ?? null, 'ID_USER' => 'admin2', 'WK_STATUS' => $now,
            ]);

            $stage = 'update_spk_cont';
            $db->table('t_spk_cont')->where('ID', $spk->ID)->where('NO_CONT', $noCont)->update([
                'LOKASI' => $nmBlock, 'TIER' => $nmTier, 'STATUS_CONT' => '450',
            ]);

            $stage = 'update_gatepass';
            $db->table('t_gatepass')->where('NO_CONT', $noCont)->where('ID', $jobSlip->NO_GATEPASS)
                ->update(['STATUS' => 'DONE']);

            $stage = 'insert_job_slip_baru';
            $db->table('t_job_slip')->insert([
                'NO_SPK' => $jobSlip->NO_SPK, 'TGL_SPK' => $jobSlip->TGL_SPK,
                'JNS_DOK' => $jobSlip->JNS_DOK, 'NO_DOK' => $jobSlip->NO_DOK,
                'NO_CONT' => $noCont, 'NO_GATEPASS' => $jobSlip->NO_GATEPASS,
                'TGL_GATEPASS' => $jobSlip->TGL_GATEPASS, 'LOKASI_AWAL' => $nmBlock,
                'TIER_AWAL' => $nmTier, 'STATUS' => 'WAITING', 'KD_STATUS' => '20',
                'WK_STATUS' => $now, 'OPERATOR' => $username,
            ]);

            $stage = 'insert_log_pergerakan';
            $db->table('log_pergerakan_kontainer')->insert([
                'NO_CONT' => $noCont, 'NO_SPK' => $jobSlip->NO_SPK, 'ACTIVITY' => 'MARSHALLING_YARD',
            ]);

            $stage = 'update_report_behandle';
            $report = $db->table('report_behandle')->where('NO_CONT', $noCont)
                ->where('REQ_NO_DOK', $jobSlip->NO_DOK)->where('RB1_NO_SPK', $jobSlip->NO_SPK)
                ->orderByDesc('ID')->first();
            if ($report) {
                $after = $nmBlock . '0' . $nmTier;
                $reportValues = strtoupper(trim((string) $jobSlip->JENIS)) === 'BEHANDLE 1'
                    ? ['PB1_MARSHALLING_B1' => $now, 'PB1_LOKASI_AFTER' => $after]
                    : ['PB2_MARSHALLING_EX_B2' => $now, 'PB2_LOKASI_AFTER' => $after];
                $db->table('report_behandle')->where('ID', $report->ID)->where('NO_CONT', $noCont)
                    ->where('REQ_NO_DOK', $jobSlip->NO_DOK)->update($reportValues);
            }

            $db->commit();
            $transactionStarted = false;
            $this->logInfo('marshalling-yard', 'Marshalling Yard berhasil diproses.', [
                'stage' => 'selesai', 'id_job_slip' => $idJobSlip, 'no_container' => $noCont,
                'no_spk' => $jobSlip->NO_SPK, 'status_spk' => $statusYard,
                'lokasi_akhir' => $lokasiAkhir, 'operator' => $username,
            ]);

            return [
                'success' => true, 'status' => 'success', 'message' => 'Marshalling Yard berhasil disimpan.',
                'data' => ['id_job_slip' => $idJobSlip, 'no_cont' => $noCont],
            ];
        } catch (Throwable $e) {
            if ($transactionStarted && $db->transactionLevel() > 0) {
                $db->rollBack();
            }
            $this->logError('marshalling-yard', 'Gagal menyimpan Marshalling Yard.', [
                'stage' => $stage, 'id_job_slip' => $idJobSlip, 'no_container' => $noCont,
                'lokasi_akhir' => $lokasiAkhir, 'exception' => get_class($e),
                'error' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine(),
                'code' => $e->getCode(), 'operator' => $username,
            ]);
            throw $e;
        }
    }
}
