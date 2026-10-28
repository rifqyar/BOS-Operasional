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
     * Database lama: MySQL, database tpk_ipc.
     * Digunakan untuk membaca data sumber dan job slip lama.
     */
    protected function readDb()
    {
        return DB::connection('prod');
    }

    /**
     * Database baru: Oracle.
     * Digunakan untuk proses tulis yang diarahkan ke database baru.
     */
    protected function writeDb()
    {
        return DB::connection('prod_new');
    }

    /**
     * Mengambil daftar pekerjaan Marshalling CIC.
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
     * Mencari pekerjaan berdasarkan nomor kontainer.
     */
    public function search(string $keyword)
    {
        $keyword = strtoupper(trim($keyword));

        if ($keyword === '') {
            return collect();
        }

        $result = $this->readDb()->select(
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
                AND NOT EXISTS (
                    SELECT 1
                    FROM t_atensi_p2 P2
                    WHERE P2.NO_CONT = C.NO_CONT
                      AND P2.NO_SPK = B.NO_SPK
                )
                AND A.JENIS IN (
                    'BEHANDLE 1',
                    'BEHANDLE 2',
                    'EX BEHANDLE 1',
                    'EX BEHANDLE 2'
                )
            ORDER BY A.ID_JOB_SLIP DESC",
            ['%' . $keyword . '%']
        );

        return collect($result);
    }

    /**
     * Mengambil detail pekerjaan berdasarkan ID job slip.
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
                AND NOT EXISTS (
                    SELECT 1
                    FROM t_atensi_p2 P2
                    WHERE P2.NO_CONT = C.NO_CONT
                      AND P2.NO_SPK = B.NO_SPK
                )
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
     * Master data jenis pekerjaan.
     */
    public function getJobActivities()
    {
        return $this->readDb()->select(
            'SELECT * FROM m_job_activity'
        );
    }

    /**
     * Master data alat.
     */
    public function getEquipments()
    {
        return $this->readDb()->select(
            'SELECT * FROM t_reff_alat'
        );
    }

    /**
     * Master data truk.
     */
    public function getTrucks()
    {
        return $this->readDb()->select(
            'SELECT * FROM reff_truck'
        );
    }

    /**
     * Master data operator.
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
     * Menyimpan proses Marshalling CIC.
     *
     * Job slip sumber dibaca dari MySQL (prod).
     * Operasi tulis tetap menggunakan Oracle (prod_new),
     * mengikuti pemisahan koneksi yang digunakan implementasi saat ini.
     */
    public function storeMarshallingCic(
        array $data,
        string $username
    ): array {
        $readDb = $this->readDb();
        $db = $this->writeDb();

        $idJobSlip = $data['idJobSlip'] ?? null;

        $noCont = strtoupper(
            trim((string) ($data['nocont'] ?? ''))
        );

        $lokasiAkhir = strtoupper(
            trim((string) ($data['lokak'] ?? ''))
        );

        $job = strtoupper(
            trim((string) ($data['job'] ?? ''))
        );

        $note = $data['note'] ?? null;
        $fumigasi = $data['fumigasi'] ?? null;

        if (
            empty($idJobSlip)
            || $noCont === ''
            || $lokasiAkhir === ''
        ) {
            throw new RuntimeException(
                'ID job slip, nomor kontainer, dan lokasi akhir wajib diisi.'
            );
        }

        /*
         * Mengikuti legacy:
         * NM_BLOCK = lima karakter pertama lokasi akhir.
         * NM_TIER  = karakter terakhir lokasi akhir.
         */
        $nmBlock = strtoupper(substr($lokasiAkhir, 0, 5));
        $nmTier = strtoupper(substr($lokasiAkhir, -1));

        try {
            $db->beginTransaction();

            /*
             * 1. Ambil tipe kontainer.
             *
             * Sesuai implementasi saat ini, tabel ini dibaca dari Oracle.
             */
            $container = $db->table('t_cocostscont')
                ->where('NO_CONT', $noCont)
                ->orderByDesc('ID')
                ->first();

            if (!$container) {
                throw new RuntimeException(
                    "Kontainer {$noCont} tidak ditemukan di database Oracle."
                );
            }

            /*
             * 2. Ambil job slip lama dari MySQL.
             *
             * PERBAIKAN:
             * Sebelumnya query ini menggunakan $db, yaitu prod_new/Oracle.
             * Sekarang menggunakan $readDb, yaitu prod/MySQL tpk_ipc.
             */
            $jobSlip = $readDb->table('t_job_slip')
                ->where('ID_JOB_SLIP', $idJobSlip)
                ->where('NO_CONT', $noCont)
                ->first();

            if (!$jobSlip) {
                throw new RuntimeException(
                    "Job slip {$idJobSlip} untuk kontainer {$noCont} tidak ditemukan di MySQL tpk_ipc."
                );
            }

            /*
             * 3. Insert detail pekerjaan ke Oracle.
             */
            $db->table('t_job_detail')->insert([
                'ID_JOB_SLIP' => $idJobSlip,
                'NO_SPK' => $jobSlip->NO_SPK,
                'NO_CONT' => $noCont,
                'JENIS' => 'CIC',
                'TYPE_CONT' => $container->KD_CONT_TIPE,

                'ID_PEKERJAAN' =>
                    $data['jenispekerjaan1'] ?? null,

                'ID_ALAT' =>
                    $data['alat1'] ?? null,

                'OPERATOR' =>
                    $data['operator1'] ?? null,

                'ID_PEKERJAAN_2' =>
                    $data['jenispekerjaan2'] ?? null,

                'ID_TRUCK' =>
                    $data['truck1'] ?? null,

                'OPERATOR_2' =>
                    $data['operator2'] ?? null,

                'ID_PEKERJAAN_3' =>
                    $data['jenispekerjaan3'] ?? null,

                'ID_ALAT_3' =>
                    $data['alat3'] ?? null,

                'OPERATOR_3' =>
                    $data['operator3'] ?? null,

                'ID_USER' => 'admin2',
                'WK_STATUS' => now(),
            ]);

            /*
             * 4. Update job slip.
             *
             * Tetap menggunakan Oracle dalam versi ini,
             * sesuai alur tulis yang sudah ada.
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
             * 5. Query denah dan lokasi.
             *
             * Dipertahankan mengikuti legacy.
             * Hasil query ini tidak digunakan dalam proses selanjutnya.
             */
            $db->table('t_denah_lapangan')
                ->selectRaw(
                    "NM_BLOK || '0' || LEVEL_4 AS DENAH"
                )
                ->where('NM_BLOK', $nmBlock)
                ->where('LEVEL_4', $nmTier)
                ->first();

            $db->table('t_spk_cont')
                ->where('LOKASI', $nmBlock)
                ->where('TIER', $nmTier)
                ->whereIn('STATUS_CONT', [
                    '450',
                    '460',
                    '510',
                    '530',
                ])
                ->get();

            /*
             * 6. Ambil SPK dari Oracle.
             */
            $spk = $db->table('t_spk')
                ->where('NO_SPK', $jobSlip->NO_SPK)
                ->orderByDesc('ID')
                ->first();

            if (!$spk) {
                throw new RuntimeException(
                    "SPK {$jobSlip->NO_SPK} tidak ditemukan di Oracle."
                );
            }

            /*
             * 7. Update lokasi dan status kontainer di Oracle.
             *
             * Periksa skema tabel: kode legacy yang diberikan
             * menggunakan nama kolom 'TIER ' dengan spasi di belakang.
             * Di sini tetap menggunakan 'TIER' seperti kode Laravel awal.
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
             * 8. Insert job slip baru ke Oracle.
             *
             * ID_JOB_SLIP tidak disertakan karena mekanisme sequence,
             * trigger, atau default kolom Oracle belum dikonfirmasi.
             */
            $db->table('t_job_slip')->insert([
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
             * 9. Cari job slip baru.
             *
             * Pencarian kombinasi field ini mengikuti implementasi awal.
             * Untuk transaksi paralel, mekanisme pengambilan ID hasil
             * insert perlu disesuaikan dengan sequence/trigger Oracle.
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
                    'Job slip baru tidak ditemukan setelah INSERT. ' .
                    'Periksa sequence, trigger, atau default ID_JOB_SLIP di Oracle.'
                );
            }

            /*
             * 10. Insert log pergerakan kontainer ke Oracle.
             */
            $db->table('log_pergerakan_kontainer')->insert([
                'NO_CONT' => $noCont,
                'NO_SPK' => $jobSlip->NO_SPK,
                'ACTIVITY' => 'MARSHALLING_CIC',
            ]);

            /*
             * 11. Insert data operasi ke Oracle.
             */
            $db->table('t_operation_new')->insert([
                'ID_SPK' => $spk->ID,
                'NO_CONT' => $noCont,
                'KD_STATUS' => '460',
                'WK_STATUS' => now(),
                'OPERATOR' => $username,
            ]);

            /*
             * 12. Ambil laporan behandle terbaru dari Oracle.
             */
            $report = $db->table('report_behandle')
                ->where('NO_CONT', $noCont)
                ->where('REQ_NO_DOK', $jobSlip->NO_DOK)
                ->where('RB1_NO_SPK', $jobSlip->NO_SPK)
                ->orderByDesc('ID')
                ->first();

            /*
             * Update laporan jika ditemukan, mengikuti legacy.
             */
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

            /*
             * Commit transaksi Oracle.
             */
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
                'marshalling-cic',
                'Transaksi Marshalling CIC berhasil.',
                $result + [
                    'username' => $username,
                ]
            );

            return $result;
        } catch (Throwable $e) {
            if ($db->transactionLevel() > 0) {
                $db->rollBack();
            }

            $this->logError(
                'marshalling-cic',
                'Transaksi Marshalling CIC gagal dan transaksi Oracle di-rollback.',
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