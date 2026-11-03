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
     * MySQL lama:
     * Digunakan untuk membaca dan memvalidasi data sumber.
     */
    protected function readDb()
    {
        return DB::connection('prod');
    }

    /**
     * Oracle baru:
     * Digunakan untuk penyimpanan operasional.
     */
    protected function writeDb()
    {
        return DB::connection('prod_new');
    }

    /**
     * Mengambil ID_JOB_SLIP baru dari sequence Oracle.
     *
     * .env:
     * ORACLE_JOB_SLIP_SEQUENCE=NAMA_SEQUENCE
     * atau
     * ORACLE_JOB_SLIP_SEQUENCE=SCHEMA.NAMA_SEQUENCE
     */
    protected function getNextJobSlipId(): int
    {
        $sequence = trim(
            (string) env('ORACLE_JOB_SLIP_SEQUENCE', '')
        );

        if ($sequence === '') {
            throw new RuntimeException(
                'Sequence ID_JOB_SLIP belum dikonfigurasi. ' .
                'Periksa ORACLE_JOB_SLIP_SEQUENCE pada file .env.'
            );
        }

        // Validasi nama sequence agar tidak menerima SQL sembarang.
        if (!preg_match(
            '/^[A-Z][A-Z0-9_$#]*(\.[A-Z][A-Z0-9_$#]*)?$/i',
            $sequence
        )) {
            throw new RuntimeException(
                'Nama sequence ID_JOB_SLIP tidak valid.'
            );
        }

        $result = $this->writeDb()->selectOne(
            "SELECT {$sequence}.NEXTVAL AS ID_JOB_SLIP FROM DUAL"
        );

        $id = $result->ID_JOB_SLIP
            ?? $result->id_job_slip
            ?? null;

        if ($id === null) {
            throw new RuntimeException(
                'Gagal mengambil ID_JOB_SLIP dari sequence Oracle.'
            );
        }

        return (int) $id;
    }

    /**
     * Mengambil daftar pekerjaan Marshalling CIC.
     *
     * Semua data daftar dibaca dari MySQL prod.
     */
    public function getAllJobs(
        int $perPage = 10
    ): LengthAwarePaginator {
        $perPage = max(1, min($perPage, 50));

        $db = $this->readDb();

        $query = $db->table('t_job_slip as A')
            ->join(
                't_spk as B',
                'A.NO_SPK',
                '=',
                'B.NO_SPK'
            )
            ->join(
                't_spk_cont as C',
                'B.ID',
                '=',
                'C.ID'
            )
            ->join(
                't_gatepass as D',
                'A.NO_GATEPASS',
                '=',
                'D.ID'
            )
            ->leftJoinSub(
                $db->table('t_ppk_hdr')
                    ->where('LNSW_KD_RESPON', '005'),
                'E',
                function ($join) {
                    $join->on(
                        'D.NO_DOK',
                        '=',
                        'E.NO_RESPON'
                    )->on(
                        'D.TGL_DOK',
                        '=',
                        'E.TG_RESPON'
                    );
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
                    ->whereColumn(
                        't_atensi_p2.NO_CONT',
                        'C.NO_CONT'
                    )
                    ->whereColumn(
                        't_atensi_p2.NO_SPK',
                        'B.NO_SPK'
                    );
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
     *
     * Sumber: MySQL prod.
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
     * Mengambil detail pekerjaan berdasarkan ID_JOB_SLIP.
     *
     * Sumber: MySQL prod.
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
     * Validasi dan data sumber:
     * MySQL prod.
     *
     * Penyimpanan operasional:
     * Oracle prod_new.
     *
     * Jika job slip lama tidak ada di Oracle, job slip dibuat
     * terlebih dahulu berdasarkan data sumber MySQL.
     */
    public function storeMarshallingCic(
        array $data,
        string $username
    ): array {
        $readDb = $this->readDb();
        $db = $this->writeDb();

        /*
         * =========================================================
         * 1. NORMALISASI DAN VALIDASI INPUT
         * =========================================================
         */

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
            $idJobSlip === null
            || !is_numeric($idJobSlip)
            || (int) $idJobSlip <= 0
        ) {
            throw new RuntimeException(
                'ID job slip tidak valid.'
            );
        }

        $idJobSlip = (int) $idJobSlip;

        if ($noCont === '') {
            throw new RuntimeException(
                'Nomor kontainer wajib diisi.'
            );
        }

        if ($lokasiAkhir === '') {
            throw new RuntimeException(
                'Lokasi akhir wajib diisi.'
            );
        }

        if ($username === '') {
            throw new RuntimeException(
                'Username operator tidak ditemukan.'
            );
        }

        /*
         * Mengikuti logika legacy:
         * NM_BLOCK = lima karakter pertama lokasi akhir.
         * NM_TIER  = karakter terakhir lokasi akhir.
         */
        $nmBlock = strtoupper(substr($lokasiAkhir, 0, 5));
        $nmTier = strtoupper(substr($lokasiAkhir, -1));

        if ($nmBlock === '' || $nmTier === '') {
            throw new RuntimeException(
                'Format lokasi akhir tidak valid.'
            );
        }

        /*
         * =========================================================
         * 2. VALIDASI SUMBER DI MYSQL PROD
         * =========================================================
         */

        // 2a. Cari job slip sumber berdasarkan ID dan kontainer.
        $jobSlip = $readDb->table('t_job_slip')
            ->where('ID_JOB_SLIP', $idJobSlip)
            ->where('NO_CONT', $noCont)
            ->first();

        if (!$jobSlip) {
            throw new RuntimeException(
                "Job slip {$idJobSlip} untuk kontainer {$noCont} " .
                'tidak ditemukan di MySQL prod.'
            );
        }

        // 2b. Validasi jenis pekerjaan CIC.
        $jenisDiizinkan = [
            'BEHANDLE 1',
            'BEHANDLE 2',
            'EX BEHANDLE 1',
            'EX BEHANDLE 2',
        ];

        $jenisSumber = strtoupper(
            trim((string) $jobSlip->JENIS)
        );

        if (!in_array($jenisSumber, $jenisDiizinkan, true)) {
            throw new RuntimeException(
                'Jenis pekerjaan pada job slip tidak sesuai untuk Marshalling CIC.'
            );
        }

        // 2c. Validasi status sumber.
        if (
            strtoupper(trim((string) $jobSlip->STATUS)) !== 'WAITING'
            || (string) $jobSlip->KD_STATUS !== '20'
        ) {
            throw new RuntimeException(
                'Job slip bukan berstatus WAITING atau KD_STATUS bukan 20.'
            );
        }

        // 2d. Cari data kontainer sumber.
        $container = $readDb->table('t_cocostscont')
            ->where('NO_CONT', $noCont)
            ->orderByDesc('ID')
            ->first();

        if (!$container) {
            throw new RuntimeException(
                "Kontainer {$noCont} tidak ditemukan di MySQL prod."
            );
        }

        // 2e. Cari SPK sumber.
        $noSpk = trim((string) ($jobSlip->NO_SPK ?? ''));

        if ($noSpk === '') {
            throw new RuntimeException(
                'Nomor SPK pada job slip sumber kosong.'
            );
        }

        $spk = $readDb->table('t_spk')
            ->where('NO_SPK', $noSpk)
            ->orderByDesc('ID')
            ->first();

        if (!$spk) {
            throw new RuntimeException(
                "SPK {$noSpk} tidak ditemukan di MySQL prod."
            );
        }

        // 2f. Validasi relasi SPK dan kontainer di MySQL.
        $spkCont = $readDb->table('t_spk_cont')
            ->where('ID', $spk->ID)
            ->where('NO_CONT', $noCont)
            ->first();

        if (!$spkCont) {
            throw new RuntimeException(
                "Kontainer {$noCont} tidak terdaftar pada SPK " .
                "{$noSpk} di MySQL prod."
            );
        }

        // 2g. Status 900 tidak boleh diproses.
        if ((string) $spkCont->STATUS_CONT === '900') {
            throw new RuntimeException(
                "Kontainer {$noCont} memiliki STATUS_CONT 900 " .
                'dan tidak dapat diproses.'
            );
        }

        // 2h. Ambil detail pekerjaan dari form.
        $jenisPekerjaan1 = $data['jenispekerjaan1'] ?? null;
        $jenisPekerjaan2 = $data['jenispekerjaan2'] ?? null;
        $jenisPekerjaan3 = $data['jenispekerjaan3'] ?? null;

        if (
            $jenisPekerjaan1 === null
            || trim((string) $jenisPekerjaan1) === ''
        ) {
            throw new RuntimeException(
                'Jenis pekerjaan 1 wajib dipilih.'
            );
        }

        /*
         * 2i. Cari laporan behandle di MySQL.
         *
         * Laporan opsional. Jika tidak ditemukan di MySQL,
         * proses utama tetap dapat berjalan.
         */
        $report = $readDb->table('report_behandle')
            ->where('NO_CONT', $noCont)
            ->where('REQ_NO_DOK', $jobSlip->NO_DOK)
            ->where('RB1_NO_SPK', $noSpk)
            ->orderByDesc('ID')
            ->first();

        /*
         * =========================================================
         * 3. MULAI TRANSAKSI ORACLE PROD_NEW
         * =========================================================
         */

        $transactionStarted = false;

        try {
            $db->beginTransaction();
            $transactionStarted = true;

            /*
             * 3a. Cari SPK Oracle berdasarkan nomor SPK.
             *
             * ID SPK MySQL tidak boleh langsung digunakan sebagai
             * ID SPK Oracle.
             */
            $spkOracle = $db->table('T_SPK')
                ->where('NO_SPK', $noSpk)
                ->first();

            if (!$spkOracle) {
                throw new RuntimeException(
                    "SPK {$noSpk} valid di MySQL prod, tetapi tidak " .
                    'ditemukan di Oracle T_SPK. Data induk SPK perlu ' .
                    'disinkronkan terlebih dahulu agar foreign key Oracle valid.'
                );
            }

            $idSpkOracle = $spkOracle->ID;

            /*
             * 3b. Cari job slip lama di Oracle.
             *
             * Pertama gunakan ID sumber dan nomor kontainer.
             * Jika ID MySQL berbeda dengan ID Oracle, cari berdasarkan
             * nomor SPK dan kontainer.
             */
            $jobSlipOracle = $db->table('T_JOB_SLIP')
                ->where('ID_JOB_SLIP', $idJobSlip)
                ->where('NO_CONT', $noCont)
                ->first();

            if (!$jobSlipOracle) {
                $jobSlipOracle = $db->table('T_JOB_SLIP')
                    ->where('NO_SPK', $noSpk)
                    ->where('NO_CONT', $noCont)
                    ->where('STATUS', 'WAITING')
                    ->where('KD_STATUS', '20')
                    ->orderByDesc('ID_JOB_SLIP')
                    ->first();
            }

            /*
             * 3c. Jika job slip lama belum ada di Oracle,
             * buat berdasarkan data sumber MySQL.
             *
             * ID yang dibuat harus berasal dari sequence Oracle.
             */
            $jobSlipOracleBaruDibuat = false;

            if (!$jobSlipOracle) {
                $idJobSlipOracle = $this->getNextJobSlipId();

                $dataJobSlipAwal = [
                    'ID_JOB_SLIP' => $idJobSlipOracle,
                    'NO_SPK' => $noSpk,
                    'TGL_SPK' => $jobSlip->TGL_SPK,
                    'JNS_DOK' => $jobSlip->JNS_DOK,
                    'NO_DOK' => $jobSlip->NO_DOK,
                    'NO_CONT' => $noCont,
                    'NO_GATEPASS' => $jobSlip->NO_GATEPASS,
                    'TGL_GATEPASS' => $jobSlip->TGL_GATEPASS,
                    'LOKASI_AWAL' => $jobSlip->LOKASI_AWAL ?? null,
                    'LOKASI_AKHIR' => $jobSlip->LOKASI_AKHIR ?? null,
                    'TIER_AWAL' => $jobSlip->TIER_AWAL ?? null,
                    'TIER_AKHIR' => $jobSlip->TIER_AKHIR ?? null,
                    'JENIS' => $jobSlip->JENIS,
                    'STATUS' => 'WAITING',
                    'KD_STATUS' => '20',
                    'NOTE' => $jobSlip->NOTE ?? null,
                    'WK_STATUS' => now(),
                    'OPERATOR' => $username,
                ];

                $db->table('T_JOB_SLIP')->insert($dataJobSlipAwal);

                $jobSlipOracle = $db->table('T_JOB_SLIP')
                    ->where('ID_JOB_SLIP', $idJobSlipOracle)
                    ->first();

                if (!$jobSlipOracle) {
                    throw new RuntimeException(
                        'Job slip sumber sudah dicoba disimpan ke Oracle, ' .
                        'tetapi tidak dapat dibaca kembali.'
                    );
                }

                $jobSlipOracleBaruDibuat = true;
            }

            /*
             * Inilah ID yang harus dipakai untuk operasi Oracle
             * selanjutnya. Jangan memakai ID MySQL secara langsung.
             */
            $idJobSlipOracle = $jobSlipOracle->ID_JOB_SLIP;

            /*
             * 3d. Cari relasi SPK-kontainer di Oracle.
             */
            $spkContOracle = $db->table('T_SPK_CONT')
                ->where('ID', $idSpkOracle)
                ->where('NO_CONT', $noCont)
                ->first();

            if (!$spkContOracle) {
                throw new RuntimeException(
                    "SPK {$noSpk} dan kontainer {$noCont} valid di MySQL, " .
                    'tetapi relasinya belum tersedia di Oracle T_SPK_CONT. ' .
                    'Transaksi dibatalkan agar data operasional tidak parsial.'
                );
            }

            /*
             * =====================================================
             * 4. INSERT DETAIL PEKERJAAN KE ORACLE
             * =====================================================
             *
             * ID_JOB_SLIP menggunakan ID Oracle.
             */
            $db->table('T_JOB_DETAIL')->insert([
                'ID_JOB_SLIP' => $idJobSlipOracle,
                'NO_SPK' => $noSpk,
                'NO_CONT' => $noCont,
                'JENIS' => 'CIC',
                'TYPE_CONT' => $container->KD_CONT_TIPE ?? null,

                'ID_PEKERJAAN' => $jenisPekerjaan1,
                'ID_ALAT' => $data['alat1'] ?? null,
                'OPERATOR' => $data['operator1'] ?? null,

                'ID_PEKERJAAN_2' => $jenisPekerjaan2,
                'ID_TRUCK' => $data['truck1'] ?? null,
                'OPERATOR_2' => $data['operator2'] ?? null,

                'ID_PEKERJAAN_3' => $jenisPekerjaan3,
                'ID_ALAT_3' => $data['alat3'] ?? null,
                'OPERATOR_3' => $data['operator3'] ?? null,

                'ID_USER' => $username,
                'WK_STATUS' => now(),
            ]);

            /*
             * =====================================================
             * 5. UPDATE JOB SLIP LAMA DI ORACLE
             * =====================================================
             */
            $updatedJobSlip = $db->table('T_JOB_SLIP')
                ->where('ID_JOB_SLIP', $idJobSlipOracle)
                ->where('NO_CONT', $noCont)
                ->update([
                    'STATUS' => 'DONE',
                    'STATUS_JOB' => 'Y',
                    'STATUS_FUMIGASI' => $fumigasi,
                    'NOTE' => $note,
                    'KD_STATUS' => '50',
                    'OPERATOR' => $username,
                    'WK_STATUS' => now(),
                ]);

            if ($updatedJobSlip < 1) {
                /*
                 * Jika job slip baru dibuat pada langkah 3c,
                 * update tetap seharusnya mengenai satu baris.
                 */
                throw new RuntimeException(
                    "Job slip Oracle {$idJobSlipOracle} tidak berhasil diperbarui."
                );
            }

            /*
             * =====================================================
             * 6. UPDATE LOKASI DAN STATUS KONTAINER
             * =====================================================
             */
            $updatedSpkCont = $db->table('T_SPK_CONT')
                ->where('ID', $idSpkOracle)
                ->where('NO_CONT', $noCont)
                ->update([
                    'LOKASI' => $nmBlock,
                    'TIER' => $nmTier,
                    'STATUS_CONT' => '460',
                ]);

            if ($updatedSpkCont < 1) {
                throw new RuntimeException(
                    "Lokasi/status kontainer {$noCont} tidak berhasil " .
                    'diperbarui pada Oracle T_SPK_CONT.'
                );
            }

            /*
             * =====================================================
             * 7. GENERATE ID JOB SLIP BARU
             * =====================================================
             *
             * ID baru harus berbeda dari job slip lama.
             */
            $newJobSlipId = $this->getNextJobSlipId();

            /*
             * =====================================================
             * 8. INSERT JOB SLIP BARU KE ORACLE
             * =====================================================
             */
            $db->table('T_JOB_SLIP')->insert([
                'ID_JOB_SLIP' => $newJobSlipId,
                'NO_SPK' => $noSpk,
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
             * =====================================================
             * 9. INSERT LOG PERGERAKAN KONTAINER
             * =====================================================
             *
             * Kolom mengikuti implementasi service sebelumnya.
             * Jika tabel Oracle memiliki kolom wajib tambahan,
             * sesuaikan berdasarkan struktur aktual.
             */
            $db->table('LOG_PERGERAKAN_KONTAINER')->insert([
                'NO_CONT' => $noCont,
                'NO_SPK' => $noSpk,
                'ACTIVITY' => 'MARSHALLING_CIC',
            ]);

            /*
             * =====================================================
             * 10. INSERT T_OPERATION_NEW
             * =====================================================
             *
             * ID_SPK wajib memakai ID Oracle T_SPK.
             * Ini memperbaiki masalah FK_T_OPERATION_NEW_SPK.
             */
            $db->table('T_OPERATION_NEW')->insert([
                'ID_SPK' => $idSpkOracle,
                'NO_CONT' => $noCont,
                'KD_STATUS' => '460',
                'WK_STATUS' => now(),
                'OPERATOR' => $username,
            ]);

            /*
             * =====================================================
             * 11. UPDATE REPORT_BEHANDLE JIKA ADA
             * =====================================================
             *
             * Keberadaan laporan diperiksa di MySQL.
             * Jika laporan sumber tidak ditemukan, proses ini dilewati.
             */
            if ($report) {
                $jobUntukLaporan = $job !== ''
                    ? $job
                    : $jenisSumber;

                if ($jobUntukLaporan === 'BEHANDLE 1') {
                    $reportUpdate = [
                        'PB1_LOKASI_CIC' => $nmBlock . '0' . $nmTier,
                        'PB1_MARSHALLING_B1' => now(),
                    ];
                } else {
                    $reportUpdate = [
                        'PB2_MARSHALLING_B2' => now(),
                        'PB2_LOKASI_CIC' => $nmBlock . '0' . $nmTier,
                    ];
                }

                $updatedReport = $db->table('REPORT_BEHANDLE')
                    ->where('NO_CONT', $noCont)
                    ->where('REQ_NO_DOK', $jobSlip->NO_DOK)
                    ->where('RB1_NO_SPK', $noSpk)
                    ->update($reportUpdate);

                if ($updatedReport < 1) {
                    throw new RuntimeException(
                        "Laporan behandle kontainer {$noCont} dan SPK {$noSpk} " .
                        'ada di MySQL, tetapi tidak berhasil diperbarui di Oracle.'
                    );
                }
            }

            /*
             * =====================================================
             * 12. COMMIT
             * =====================================================
             */
            $db->commit();
            $transactionStarted = false;

            $result = [
                'id_job_slip_mysql' => $idJobSlip,
                'id_job_slip_oracle' => $idJobSlipOracle,
                'new_job_slip_id' => $newJobSlipId,
                'job_slip_oracle_dibuat' => $jobSlipOracleBaruDibuat,
                'no_cont' => $noCont,
                'no_spk' => $noSpk,
                'id_spk_mysql' => $spk->ID,
                'id_spk_oracle' => $idSpkOracle,
                'lokasi' => $nmBlock,
                'tier' => $nmTier,
                'database_sumber' => 'prod',
                'database_tujuan' => 'prod_new',
            ];

            $this->logInfo(
                'marshalling-cic',
                'Transaksi Marshalling CIC berhasil disimpan ke Oracle.',
                $result + [
                    'username' => $username,
                ]
            );

            return $result;
        } catch (Throwable $e) {
            /*
             * Batalkan semua perubahan Oracle jika terjadi kegagalan.
             */
            if ($transactionStarted) {
                try {
                    $db->rollBack();
                } catch (Throwable $rollbackException) {
                    $this->logError(
                        'marshalling-cic',
                        'Rollback transaksi Oracle gagal.',
                        [
                            'no_cont' => $noCont,
                            'username' => $username,
                            'rollback_error' => $rollbackException->getMessage(),
                        ]
                    );
                }
            }

            $this->logError(
                'marshalling-cic',
                'Transaksi Marshalling CIC gagal dan di-rollback.',
                [
                    'id_job_slip' => $idJobSlip,
                    'no_cont' => $noCont,
                    'no_spk' => $jobSlip->NO_SPK ?? null,
                    'username' => $username,
                    'exception' => get_class($e),
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            );

            throw $e;
        }
    }
}