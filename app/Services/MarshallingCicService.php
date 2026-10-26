<?php

namespace App\Services;

use App\Http\Traits\LogTraits;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class MarshallingCicService
{
    use LogTraits;

    /**
     * Koneksi untuk monitoring dan pembacaan master data.
     */
    protected function readDb()
    {
        return DB::connection('prod');
    }

    /**
     * Koneksi untuk operasi tulis.
     */
    protected function writeDb()
    {
        return DB::connection('prod_new');
    }

    /**
     * Get all Marshalling CIC jobs with pagination.
     */
    public function getAllJobs(int $perPage = 10): LengthAwarePaginator
    {
        $perPage = max(1, min($perPage, 50));

        $query = $this->readDb()
            ->table('t_job_slip as A')
            ->join('t_spk as B', 'A.NO_SPK', '=', 'B.NO_SPK')
            ->join('t_spk_cont as C', 'B.ID', '=', 'C.ID')
            ->join('t_gatepass as D', 'A.NO_GATEPASS', '=', 'D.ID')
            ->leftJoinSub(
                $this->readDb()
                    ->table('t_ppk_hdr')
                    ->where('LNSW_KD_RESPON', '005'),
                'E',
                function ($join) {
                    $join->on('D.NO_DOK', '=', 'E.NO_RESPON')
                        ->on('D.TGL_DOK', '=', 'E.TG_RESPON');
                }
            )
            ->select([
                'A.ID_JOB_SLIP',
                'A.NO_CONT',
                'C.UKR_CONT',
                'A.LOKASI_AWAL',
                'A.LOKASI_AKHIR',
                'A.TIER_AWAL',
                'A.TIER_AKHIR',
                'A.JENIS',
                'D.RESPON',
                'E.LNSW_NOAJU',
            ])
            ->whereNotExists(function ($query) {
                $query->select(DB::raw('1'))
                    ->from('t_atensi_p2')
                    ->whereColumn('t_atensi_p2.NO_CONT', 'C.NO_CONT')
                    ->whereColumn('t_atensi_p2.NO_SPK', 'B.NO_SPK');
            })
            ->where('A.STATUS', 'WAITING')
            ->where('A.KD_STATUS', '20')
            ->where('A.LOKASI_AKHIR', 'LIKE', 'CIC%')
            ->where('C.STATUS_CONT', '!=', 900)
            ->where('D.FL_ACTIVE', 'Y')
            ->whereIn('A.JENIS', [
                'BEHANDLE 1',
                'BEHANDLE 2',
                'EX BEHANDLE 1',
                'EX BEHANDLE 2',
            ])
            ->distinct()
            ->orderByDesc('A.ID_JOB_SLIP');

        return $query->paginate($perPage);
    }

    /**
     * Search Marshalling CIC by container.
     */
    public function search(string $keyword)
    {
        return $this->readDb()->select(
            "SELECT DISTINCT
                A.ID_JOB_SLIP,
                A.NO_CONT,
                C.UKR_CONT,
                A.LOKASI_AWAL,
                A.LOKASI_AKHIR,
                A.TIER_AWAL,
                A.TIER_AKHIR,
                A.JENIS,
                D.RESPON
            FROM t_job_slip A
            INNER JOIN t_spk B ON A.NO_SPK = B.NO_SPK
            INNER JOIN t_spk_cont C ON B.ID = C.ID
            INNER JOIN t_gatepass D ON A.NO_GATEPASS = D.ID
            WHERE A.NO_CONT LIKE ?
              AND A.STATUS = 'WAITING'
              AND A.KD_STATUS = '20'
              AND A.LOKASI_AKHIR LIKE 'CIC%'
              AND C.STATUS_CONT != 900
              AND D.FL_ACTIVE = 'Y'
              AND A.JENIS IN ('BEHANDLE 1', 'BEHANDLE 2')
            ORDER BY A.ID_JOB_SLIP DESC",
            ['%' . $keyword . '%']
        );
    }

    /**
     * Get detail Marshalling CIC job.
     */
    public function getDetail(int $idJobSlip)
    {
        return $this->readDb()->selectOne(
            "SELECT DISTINCT
                A.ID_JOB_SLIP,
                A.NO_CONT,
                C.UKR_CONT,
                A.LOKASI_AWAL,
                A.LOKASI_AKHIR,
                A.TIER_AWAL,
                A.TIER_AKHIR,
                A.JENIS,
                D.RESPON,
                E.LNSW_NOAJU
            FROM t_job_slip A
            INNER JOIN t_spk B ON A.NO_SPK = B.NO_SPK
            INNER JOIN t_spk_cont C ON B.ID = C.ID
            INNER JOIN t_gatepass D ON A.NO_GATEPASS = D.ID
            LEFT JOIN (
                SELECT *
                FROM t_ppk_hdr
                WHERE LNSW_KD_RESPON = '005'
            ) E ON D.NO_DOK = E.NO_RESPON
                AND D.TGL_DOK = E.TG_RESPON
            WHERE A.STATUS = 'WAITING'
              AND A.KD_STATUS = '20'
              AND C.STATUS_CONT != 900
              AND D.FL_ACTIVE = 'Y'
              AND A.JENIS IN (
                  'BEHANDLE 1',
                  'BEHANDLE 2',
                  'EX BEHANDLE 1',
                  'EX BEHANDLE 2'
              )
              AND A.ID_JOB_SLIP = ?
            ORDER BY A.ID_JOB_SLIP DESC",
            [$idJobSlip]
        );
    }

    /**
     * Get job activities.
     */
    public function getJobActivities()
    {
        return $this->readDb()->select(
            'SELECT * FROM m_job_activity'
        );
    }

    /**
     * Get equipments.
     */
    public function getEquipments()
    {
        return $this->readDb()->select(
            'SELECT * FROM t_reff_alat'
        );
    }

    /**
     * Get trucks.
     */
    public function getTrucks()
    {
        return $this->readDb()->select(
            'SELECT * FROM reff_truck'
        );
    }

    /**
     * Get operators.
     */
    public function getOperators()
    {
        return $this->readDb()->select(
            "SELECT *
             FROM reff_user
             WHERE KD_GROUP LIKE '%OPR%'"
        );
    }

    /**
     * Simpan proses Marshalling CIC berdasarkan kode legacy.
     *
     * Catatan:
     * Mekanisme ID_JOB_SLIP belum terkonfirmasi. Method ini berhenti
     * sebelum transaksi database dimulai sampai mekanisme ID tersedia.
     */
    public function storeMarshallingCic(
        array $data,
        string $username
    ): array {
        $newJobSlipId = $this->generateJobSlipId();

        $db = $this->writeDb();

        $db->beginTransaction();

        try {
            $idJobSlip = $data['idJobSlip'];
            $noCont = strtoupper(trim($data['nocont']));
            $lokasiAkhir = strtoupper(trim($data['lokak']));
            $nmBlock = strtoupper(substr($lokasiAkhir, 0, 5));
            $nmTier = strtoupper(substr($lokasiAkhir, -1));

            $note = $data['note'] ?? null;
            $fumigasi = $data['fumigasi'] ?? null;
            $job = strtoupper(trim($data['job']));

            /*
             * 1. Ambil tipe container.
             */
            $container = $db->table('t_cocostscont')
                ->where('NO_CONT', $noCont)
                ->orderByDesc('ID')
                ->first();

            if (!$container) {
                throw new RuntimeException(
                    "Container {$noCont} tidak ditemukan di t_cocostscont."
                );
            }

            /*
             * 2. Ambil job slip lama.
             */
            $jobSlip = $db->table('t_job_slip')
                ->where('ID_JOB_SLIP', $idJobSlip)
                ->where('NO_CONT', $noCont)
                ->first();

            if (!$jobSlip) {
                throw new RuntimeException(
                    "Job slip {$idJobSlip} tidak ditemukan untuk container {$noCont}."
                );
            }

            /*
             * 3. Insert t_job_detail.
             */
            $db->table('t_job_detail')->insert([
                'ID_JOB_SLIP' => $idJobSlip,
                'NO_SPK' => $jobSlip->NO_SPK,
                'NO_CONT' => $noCont,
                'JENIS' => 'CIC',
                'TYPE_CONT' => $container->KD_CONT_TIPE,
                'ID_PEKERJAAN' => $data['jenispekerjaan1'] ?? null,
                'ID_ALAT' => $data['alat1'] ?? null,
                'OPERATOR' => $data['operator1'] ?? null,
                'ID_PEKERJAAN_2' => $data['jenispekerjaan2'] ?? null,
                'ID_TRUCK' => $data['truck1'] ?? null,
                'OPERATOR_2' => $data['operator2'] ?? null,
                'ID_PEKERJAAN_3' => $data['jenispekerjaan3'] ?? null,
                'ID_ALAT_3' => $data['alat3'] ?? null,
                'OPERATOR_3' => $data['operator3'] ?? null,
                'ID_USER' => 'admin2',
                'WK_STATUS' => now(),
            ]);

            /*
             * 4. Update job slip lama.
             */
            $db->table('t_job_slip')
                ->where('ID_JOB_SLIP', $idJobSlip)
                ->update([
                    'STATUS' => 'DONE',
                    'STATUS_JOB' => 'Y',
                    'STATUS_FUMIGASI' => $fumigasi,
                    'NOTE' => $note,
                    'KD_STATUS' => '50',
                    'OPERATOR' => $username,
                    'WK_STATUS' => now(),
                ]);

            /*
             * 5. Query denah; hasilnya tidak dipakai oleh legacy.
             * Sintaks concatenation Oracle menggunakan ||.
             */
            $db->table('t_denah_lapangan')
                ->selectRaw("NM_BLOK || '0' || LEVEL_4 AS DENAH")
                ->where('NM_BLOK', $nmBlock)
                ->where('LEVEL_4', $nmTier)
                ->first();

            /*
             * 6. Query status lokasi; hasilnya tidak dipakai oleh legacy.
             */
            $db->table('t_spk_cont')
                ->where('LOKASI', $nmBlock)
                ->where('TIER', $nmTier)
                ->whereIn('STATUS_CONT', ['450', '460', '510', '530'])
                ->get();

            /*
             * 7. Ambil SPK berdasarkan NO_SPK.
             */
            $spk = $db->table('t_spk')
                ->where('NO_SPK', $jobSlip->NO_SPK)
                ->orderByDesc('ID')
                ->first();

            if (!$spk) {
                throw new RuntimeException(
                    "SPK {$jobSlip->NO_SPK} tidak ditemukan."
                );
            }

            /*
             * 8. Update t_spk_cont.
             */
            $db->table('t_spk_cont')
                ->where('NO_CONT', $noCont)
                ->where('ID', $spk->ID)
                ->update([
                    'LOKASI' => $nmBlock,
                    'TIER' => $nmTier,
                    'STATUS_CONT' => '460',
                ]);

            /*
             * 9. Insert job slip baru.
             */
            $db->table('t_job_slip')->insert([
                'ID_JOB_SLIP' => $newJobSlipId,
                'NO_SPK' => $jobSlip->NO_SPK,
                'TGL_SPK' => $jobSlip->TGL_SPK,
                'JNS_DOK' => $jobSlip->JNS_DOK,
                'NO_DOK' => $jobSlip->NO_DOK,
                'NO_CONT' => $jobSlip->NO_CONT,
                'NO_GATEPASS' => $jobSlip->NO_GATEPASS,
                'TGL_GATEPASS' => $jobSlip->TGL_GATEPASS,
                'LOKASI_AWAL' => $nmBlock,
                'TIER_AWAL' => $nmTier,
                'STATUS' => 'WAITING',
                'KD_STATUS' => '20',
                'NOTE' => $note,
                'WK_STATUS' => now(),
                'OPERATOR' => $username,
            ]);

            /*
             * 10. Insert log pergerakan container.
             * Ini tabel bisnis legacy, bukan NPCT.
             */
            $db->table('log_pergerakan_kontainer')->insert([
                'NO_CONT' => $noCont,
                'NO_SPK' => $jobSlip->NO_SPK,
                'ACTIVITY' => 'MARSHALLING_CIC',
            ]);

            /*
             * 11. Insert t_operation_new.
             */
            $db->table('t_operation_new')->insert([
                'ID_SPK' => $spk->ID,
                'NO_CONT' => $noCont,
                'KD_STATUS' => '460',
                'WK_STATUS' => now(),
                'OPERATOR' => $username,
            ]);

            /*
             * 12. Update report_behandle.
             */
            $reportQuery = $db->table('report_behandle')
                ->where('NO_CONT', $noCont)
                ->where('REQ_NO_DOK', $jobSlip->NO_DOK)
                ->where('RB1_NO_SPK', $jobSlip->NO_SPK);

            $report = (clone $reportQuery)
                ->orderByDesc('ID')
                ->first();

            if ($report) {
                if ($job === 'BEHANDLE 1') {
                    $db->table('report_behandle')
                        ->where('ID', $report->ID)
                        ->update([
                            'PB1_MARSHALLING_B1' => now(),
                            'PB1_LOKASI_CIC' => $nmBlock . '0' . $nmTier,
                        ]);
                } else {
                    $db->table('report_behandle')
                        ->where('ID', $report->ID)
                        ->update([
                            'PB2_MARSHALLING_B2' => now(),
                            'PB2_LOKASI_CIC' => $nmBlock . '0' . $nmTier,
                        ]);
                }
            }

            $db->commit();

            return [
                'id_job_slip' => $idJobSlip,
                'new_job_slip_id' => $newJobSlipId,
                'no_cont' => $noCont,
                'no_spk' => $jobSlip->NO_SPK,
                'lokasi' => $nmBlock,
                'tier' => $nmTier,
            ];
        } catch (Throwable $e) {
            if ($db->transactionLevel() > 0) {
                $db->rollBack();
            }

            $this->logError(
                'marshalling_cic',
                'Transaksi Marshalling CIC gagal dan di-rollback.',
                [
                    'id_job_slip' => $data['idJobSlip'] ?? null,
                    'no_cont' => $data['nocont'] ?? null,
                    'username' => $username,
                    'error' => $e->getMessage(),
                ]
            );

            throw $e;
        }
    }

    /**
     * Pengaman sementara sampai mekanisme ID_JOB_SLIP dikonfirmasi.
     */
    protected function generateJobSlipId()
    {
        throw new RuntimeException(
            'Penyimpanan belum dijalankan karena mekanisme pembentukan ID_JOB_SLIP belum dikonfirmasi.'
        );
    }
}