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

    protected function readDb()
    {
        return DB::connection('prod');
    }

    protected function writeDb()
    {
        return DB::connection('prod_new');
    }

    /**
     * Data monitoring Marshalling CIC.
     */
    public function getAllJobs(int $perPage = 10): LengthAwarePaginator
    {
        $perPage = max(1, min($perPage, 50));
        $db = $this->readDb();

        $query = $db->table('t_job_slip as A')
            ->join('t_spk as B', 'A.NO_SPK', '=', 'B.NO_SPK')
            ->join('t_spk_cont as C', 'B.ID', '=', 'C.ID')
            ->join('t_gatepass as D', 'A.NO_GATEPASS', '=', 'D.ID')
            ->leftJoinSub(
                $db->table('t_ppk_hdr')
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
            ->whereNotExists(function ($subQuery) {
                $subQuery->select(DB::raw('1'))
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
     * Pencarian berdasarkan nomor container.
     */
    public function search(string $keyword)
    {
        $keyword = strtoupper(trim($keyword));

        if ($keyword === '') {
            return collect();
        }

        return collect($this->readDb()->select(
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
            INNER JOIN t_spk B
                ON A.NO_SPK = B.NO_SPK
            INNER JOIN t_spk_cont C
                ON B.ID = C.ID
            INNER JOIN t_gatepass D
                ON A.NO_GATEPASS = D.ID
            WHERE UPPER(A.NO_CONT) LIKE ?
                AND A.STATUS = 'WAITING'
                AND A.KD_STATUS = '20'
                AND A.LOKASI_AKHIR LIKE 'CIC%'
                AND C.STATUS_CONT != 900
                AND D.FL_ACTIVE = 'Y'
                AND A.JENIS IN (
                    'BEHANDLE 1',
                    'BEHANDLE 2',
                    'EX BEHANDLE 1',
                    'EX BEHANDLE 2'
                )
            ORDER BY A.ID_JOB_SLIP DESC",
            ['%' . $keyword . '%']
        ));
    }

    /**
     * Detail job berdasarkan ID_JOB_SLIP.
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
            INNER JOIN t_spk B
                ON A.NO_SPK = B.NO_SPK
            INNER JOIN t_spk_cont C
                ON B.ID = C.ID
            INNER JOIN t_gatepass D
                ON A.NO_GATEPASS = D.ID
            LEFT JOIN (
                SELECT *
                FROM t_ppk_hdr
                WHERE LNSW_KD_RESPON = '005'
            ) E
                ON D.NO_DOK = E.NO_RESPON
                AND D.TGL_DOK = E.TG_RESPON
            WHERE A.STATUS = 'WAITING'
                AND A.KD_STATUS = '20'
                AND A.LOKASI_AKHIR LIKE 'CIC%'
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
     * Master jenis kegiatan.
     */
    public function getJobActivities()
    {
        return $this->readDb()->select(
            'SELECT * FROM m_job_activity'
        );
    }

    /**
     * Master alat.
     */
    public function getEquipments()
    {
        return $this->readDb()->select(
            'SELECT * FROM t_reff_alat'
        );
    }

    /**
     * Master truck.
     */
    public function getTrucks()
    {
        return $this->readDb()->select(
            'SELECT * FROM reff_truck'
        );
    }

    /**
     * Master operator.
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
     * Simpan proses Marshalling CIC.
     *
     * Catatan:
     * - Seluruh penulisan dilakukan pada prod_new.
     * - Mekanisme ID_JOB_SLIP otomatis harus diverifikasi di Oracle.
     * - Nama kolom harus sesuai skema database aktual.
     */
    public function storeMarshallingCic(
        array $data,
        string $username
    ): array {
        $db = $this->writeDb();

        $idJobSlip = (int) ($data['idJobSlip'] ?? 0);
        $noCont = strtoupper(trim((string) ($data['nocont'] ?? '')));
        $lokasiAkhir = strtoupper(trim((string) ($data['lokak'] ?? '')));
        $job = strtoupper(trim((string) ($data['job'] ?? '')));
        $note = $data['note'] ?? null;

        if ($idJobSlip <= 0 || $noCont === '' || $lokasiAkhir === '') {
            throw new RuntimeException(
                'ID job slip, nomor container, dan lokasi akhir wajib diisi.'
            );
        }

        $db->beginTransaction();

        try {
            /*
             * Ambil job slip lama.
             */
            $jobSlip = $db->table('t_job_slip')
                ->where('ID_JOB_SLIP', $idJobSlip)
                ->where('NO_CONT', $noCont)
                ->lockForUpdate()
                ->first();

            if (!$jobSlip) {
                throw new RuntimeException(
                    "Job slip {$idJobSlip} tidak ditemukan untuk container {$noCont}."
                );
            }

            if (
                $jobSlip->STATUS !== 'WAITING'
                || (string) $jobSlip->KD_STATUS !== '20'
            ) {
                throw new RuntimeException(
                    'Job slip sudah diproses atau statusnya tidak sesuai.'
                );
            }

            /*
             * Ambil tipe container.
             */
            $container = $db->table('t_cocostscont')
                ->where('NO_CONT', $noCont)
                ->orderByDesc('ID')
                ->first();

            if (!$container) {
                throw new RuntimeException(
                    "Data tipe container {$noCont} tidak ditemukan."
                );
            }

            /*
             * Parsing lokasi mengikuti pola legacy.
             */
            $nmBlock = strtoupper(substr($lokasiAkhir, 0, 5));
            $nmTier = strtoupper(substr($lokasiAkhir, -1));

            /*
             * Ambil SPK.
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
             * Insert detail pekerjaan.
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
                'ID_USER' => $username,
                'WK_STATUS' => now(),
            ]);

            /*
             * Update job slip lama.
             *
             * Periksa nilai STATUS_FUMIGASI sesuai aturan legacy.
             */
            $db->table('t_job_slip')
                ->where('ID_JOB_SLIP', $idJobSlip)
                ->where('NO_CONT', $noCont)
                ->update([
                    'STATUS' => 'DONE',
                    'STATUS_JOB' => 'Y',
                    'STATUS_FUMIGASI' => $data['fumigasi'] ?: null,
                    'NOTE' => $note,
                    'KD_STATUS' => '50',
                    'OPERATOR' => $username,
                    'WK_STATUS' => now(),
                ]);

            /*
             * Update lokasi/status container.
             *
             * Pastikan nama kolom TIER sesuai skema Oracle.
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
             * Insert job slip baru.
             *
             * ID_JOB_SLIP sengaja tidak diisi sesuai alur legacy.
             * Database harus memiliki mekanisme ID otomatis.
             */
            $db->table('t_job_slip')->insert([
                'NO_SPK' => $jobSlip->NO_SPK,
                'TGL_SPK' => $jobSlip->TGL_SPK,
                'JNS_DOK' => $jobSlip->JNS_DOK,
                'NO_DOK' => $jobSlip->NO_DOK,
                'NO_CONT' => $noCont,
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
             * Verifikasi job slip baru.
             *
             * Ini masih mengandalkan kombinasi kolom untuk menemukan
             * record baru. Untuk lingkungan konkuren, gunakan ID hasil
             * sequence/trigger Oracle jika mekanismenya sudah diketahui.
             */
            $newJobSlip = $db->table('t_job_slip')
                ->where('NO_SPK', $jobSlip->NO_SPK)
                ->where('NO_CONT', $noCont)
                ->where('NO_GATEPASS', $jobSlip->NO_GATEPASS)
                ->where('STATUS', 'WAITING')
                ->where('KD_STATUS', '20')
                ->where('LOKASI_AWAL', $nmBlock)
                ->where('TIER_AWAL', $nmTier)
                ->where('OPERATOR', $username)
                ->orderByDesc('ID_JOB_SLIP')
                ->first();

            if (!$newJobSlip) {
                throw new RuntimeException(
                    'Job slip baru tidak ditemukan. Periksa sequence, trigger, atau default ID_JOB_SLIP pada Oracle.'
                );
            }

            /*
             * Log pergerakan container.
             */
            $db->table('log_pergerakan_kontainer')->insert([
                'NO_CONT' => $noCont,
                'NO_SPK' => $jobSlip->NO_SPK,
                'ACTIVITY' => 'MARSHALLING_CIC',
            ]);

            /*
             * Catat operasi.
             */
            $db->table('t_operation_new')->insert([
                'ID_SPK' => $spk->ID,
                'NO_CONT' => $noCont,
                'KD_STATUS' => '460',
                'WK_STATUS' => now(),
                'OPERATOR' => $username,
            ]);

            /*
             * Update report behandle bila record ditemukan.
             */
            $report = $db->table('report_behandle')
                ->where('NO_CONT', $noCont)
                ->where('REQ_NO_DOK', $jobSlip->NO_DOK)
                ->where('RB1_NO_SPK', $jobSlip->NO_SPK)
                ->orderByDesc('ID')
                ->first();

            if ($report) {
                if ($job === 'BEHANDLE 1') {
                    $db->table('report_behandle')
                        ->where('ID', $report->ID)
                        ->where('NO_CONT', $noCont)
                        ->where('REQ_NO_DOK', $jobSlip->NO_DOK)
                        ->update([
                            'PB1_MARSHALLING_B1' => now(),
                            'PB1_LOKASI_CIC' => $nmBlock . '0' . $nmTier,
                        ]);
                } else {
                    $db->table('report_behandle')
                        ->where('ID', $report->ID)
                        ->where('NO_CONT', $noCont)
                        ->where('REQ_NO_DOK', $jobSlip->NO_DOK)
                        ->update([
                            'PB2_MARSHALLING_B2' => now(),
                            'PB2_LOKASI_CIC' => $nmBlock . '0' . $nmTier,
                        ]);
                }
            }

            $db->commit();

            $result = [
                'id_job_slip' => $idJobSlip,
                'new_job_slip_id' => $newJobSlip->ID_JOB_SLIP,
                'no_cont' => $noCont,
                'no_spk' => $jobSlip->NO_SPK,
                'lokasi' => $nmBlock,
                'tier' => $nmTier,
            ];

            $this->logInfo(
                'marshalling_cic',
                'Transaksi Marshalling CIC berhasil.',
                $result + ['username' => $username]
            );

            return $result;
        } catch (Throwable $e) {
            if ($db->transactionLevel() > 0) {
                $db->rollBack();
            }

            $this->logError(
                'marshalling_cic',
                'Transaksi Marshalling CIC gagal dan di-rollback.',
                [
                    'id_job_slip' => $idJobSlip,
                    'no_cont' => $noCont,
                    'username' => $username,
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            );

            throw $e;
        }
    }
}