<?php

namespace App\Services;

use App\Http\Traits\LogTraits;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class RealisasiServices
{
    use LogTraits;

    /**
     * Koneksi untuk membaca data referensi.
     */
    private string $readConnection = 'prod_new';

    /**
     * Koneksi tujuan untuk insert catatan/detail yang bersifat append-only.
     */
    private string $insertConnection = 'prod_new';

    /**
     * Mencari container yang memenuhi kriteria realisasi.
     */
    public function searchReal(string $keyword)
    {
        $keyword = trim($keyword);

        try {
            $data = DB::connection($this->readConnection)->select(
                "SELECT
                    A.*,
                    B.NO_SPK,
                    C.TIPE_CONT,
                    E.ID_JOB_SLIP
                 FROM t_spk_cont A
                 INNER JOIN t_spk B
                    ON A.ID = B.ID
                                 LEFT JOIN t_request_cont C
                    ON A.NO_CONT = C.NO_CONT
                                 LEFT JOIN t_op_behandlein D
                    ON A.NO_CONT = D.NO_CONT
                                 LEFT JOIN t_job_slip E
                    ON D.NO_SPK = E.NO_SPK
                 WHERE A.NO_CONT LIKE UPPER(?)
                   AND A.STATUS_CONT IN ('460')
                 ORDER BY A.ID DESC
                 FETCH FIRST 1 ROWS ONLY",
                ['%'.strtoupper($keyword).'%']
            );

            $this->logInfo(
                'pemeriksaan-behandle',
                'Pencarian container realisasi selesai.',
                [
                    'keyword' => $keyword,
                    'jumlah_data' => count($data),
                ]
            );

            return $this->normalizeRows($data);
        } catch (Throwable $e) {
            $this->logError(
                'pemeriksaan-behandle',
                'Gagal mencari container realisasi.',
                [
                    'keyword' => $keyword,
                    'error' => $e->getMessage(),
                ]
            );

            throw $e;
        }
    }

    private function normalizeRows(array $rows): array
    {
        return array_map(
            fn ($row) => $this->normalizeRow($row),
            $rows
        );
    }

    private function normalizeRow(?object $row): ?object
    {
        if ($row === null) {
            return null;
        }

        $normalized = new \stdClass;
        foreach ((array) $row as $column => $value) {
            $normalized->{strtoupper($column)} = $value;
        }

        return $normalized;
    }

    /**
     * Mengambil data pemeriksaan berstatus WAITING.
     *
     * Status WAITING dibaca dari prod_new.
     */
    public function getStatus(string $no_cont)
    {
        return $this->normalizeRow(DB::connection($this->readConnection)->selectOne(
            "SELECT *
             FROM t_op_inspection
             WHERE NO_CONT = ?
               AND STATUS = 'WAITING'
             ORDER BY ID DESC
             FETCH FIRST 1 ROWS ONLY",
            [$no_cont]
        ));
    }

    /**
     * Mengambil daftar alat pemeriksaan.
     */
    public function getAlat()
    {
        return $this->normalizeRows(DB::connection($this->readConnection)->select(
            'SELECT * FROM t_reff_alat'
        ));
    }

    /**
     * Mengambil daftar operator.
     */
    public function getOperator()
    {
        return $this->normalizeRows(DB::connection($this->readConnection)->select(
            "SELECT *
             FROM reff_user
             WHERE KD_GROUP LIKE '%OPR%'"
        ));
    }

    /**
     * Mengambil daftar kondisi.
     */
    public function getCondition()
    {
        return $this->normalizeRows(DB::connection($this->readConnection)->select(
            'SELECT ID, KONDISI FROM reff_kondisi'
        ));
    }

    /**
     * Memeriksa informasi dokumen untuk proses JOIN.
     */
    public function getJoin(
        string $no_cont,
        string $no_dok,
        $tgl_dok
    ) {
        return $this->normalizeRow(DB::connection($this->readConnection)->selectOne(
            "SELECT A.LNSW_NOAJU
             FROM t_ppk_hdr A
             JOIN t_ppk_cont B
                ON A.ID_IJIN = B.ID_IJIN
             WHERE A.LNSW_KD_RESPON = '005'
               AND B.NO_CONT = ?
               AND A.NO_RESPON = ?
               AND A.TG_RESPON = ?",
            [$no_cont, $no_dok, $tgl_dok]
        ));
    }

    /**
     * Mengambil data SPK dan container.
     */
    private function getSpkContainer(string $noCont)
    {
        return $this->normalizeRow(DB::connection($this->readConnection)->selectOne(
            "SELECT
                A.*,
                B.NO_SPK AS SPK_NO_SPK,
                B.KD_STATUS AS SPK_KD_STATUS
             FROM t_spk_cont A
             INNER JOIN t_spk B
                ON A.ID = B.ID
             WHERE A.NO_CONT = ?
               AND A.STATUS_CONT = '460'
             ORDER BY A.ID DESC
             FETCH FIRST 1 ROWS ONLY",
            [$noCont]
        ));
    }

    /**
     * Mengambil data job slip berdasarkan container dan SPK.
     */
    private function getJobSlip(
        string $noCont,
        string $noSpk
    ) {
        return $this->normalizeRow(DB::connection($this->readConnection)->selectOne(
            "SELECT *
             FROM t_job_slip
             WHERE NO_CONT = ?
               AND NO_SPK = ?
               AND STATUS = 'WAITING'
               AND KD_STATUS = '20'
             ORDER BY ID_JOB_SLIP DESC
             FETCH FIRST 1 ROWS ONLY",
            [$noCont, $noSpk]
        ));
    }

    /**
     * Mengambil data gatepass untuk container dan dokumen.
     */
    private function getGatepass(
        string $noCont,
        string $noDok
    ) {
        return $this->normalizeRow(DB::connection($this->readConnection)->selectOne(
            "SELECT *
             FROM t_gatepass
             WHERE NO_CONT = ?
               AND NO_DOK = ?
               AND JNS_KEGIATAN IN ('1', '2')
               AND FL_ACTIVE = 'Y'
             ORDER BY ID DESC
             FETCH FIRST 1 ROWS ONLY",
            [$noCont, $noDok]
        ));
    }

    /**
     * Proses utama pemeriksaan.
     *
     * kode 1 = Mulai pemeriksaan.
     * kode 2 = Selesai pemeriksaan.
     *
     * JOIN belum diproses karena proses buatb2join()
     * dari legacy belum dimigrasikan.
     */
    public function storePemeriksaan(
        array $input,
        string $operatorLogin
    ): array {
        $noCont = trim($input['nomerkon'] ?? '');

        if ($noCont === '') {
            throw new RuntimeException(
                'Nomor container wajib diisi.'
            );
        }

        if (trim($operatorLogin) === '') {
            throw new RuntimeException(
                'Identitas operator tidak ditemukan.'
            );
        }

        $join = (string) ($input['join'] ?? '0');

        if ($join === '1') {
            $this->logWarning(
                'pemeriksaan-behandle',
                'Proses JOIN ditolak karena migrasi belum selesai.',
                ['no_cont' => $noCont]
            );

            throw new RuntimeException(
                'Proses JOIN belum tersedia. Migrasi buatb2join() harus diselesaikan terlebih dahulu.'
            );
        }

        $this->logInfo(
            'pemeriksaan-behandle',
            'Memulai proses realisasi pemeriksaan.',
            [
                'no_cont' => $noCont,
                'operator' => $operatorLogin,
            ]
        );

        /*
         * Data referensi dibaca dari koneksi prod_new.
         */
        $container = $this->getSpkContainer($noCont);

        if (! $container) {
            $this->logWarning(
                'pemeriksaan-behandle',
                'Container tidak ditemukan atau statusnya tidak memenuhi syarat.',
                ['no_cont' => $noCont]
            );

            throw new RuntimeException(
                'Container tidak ditemukan atau status container bukan 460.'
            );
        }

        $noSpk = $container->SPK_NO_SPK
            ?? $container->NO_SPK
            ?? null;

        if (! $noSpk) {
            throw new RuntimeException(
                'Nomor SPK tidak ditemukan untuk container tersebut.'
            );
        }

        /*
         * Jika form mengirimkan nomor SPK, validasi terhadap database.
         */
        if (
            ! empty($input['nospk'])
            && trim($input['nospk']) !== (string) $noSpk
        ) {
            $this->logWarning(
                'pemeriksaan-behandle',
                'Nomor SPK dari form tidak sesuai dengan data container.',
                [
                    'no_cont' => $noCont,
                    'nospk_form' => $input['nospk'],
                    'nospk_database' => $noSpk,
                ]
            );

            throw new RuntimeException(
                'Nomor SPK tidak sesuai dengan data container.'
            );
        }

        $jobSlip = $this->getJobSlip(
            $noCont,
            (string) $noSpk
        );

        if (! $jobSlip) {
            $this->logWarning(
                'pemeriksaan-behandle',
                'Job slip tidak ditemukan.',
                [
                    'no_cont' => $noCont,
                    'no_spk' => $noSpk,
                ]
            );

            throw new RuntimeException(
                'Job slip tidak ditemukan untuk container tersebut.'
            );
        }

        $noDok = $jobSlip->NO_DOK ?? null;

        if (! $noDok) {
            throw new RuntimeException(
                'Nomor dokumen tidak ditemukan pada job slip.'
            );
        }

        $gatepass = $this->getGatepass(
            $noCont,
            (string) $noDok
        );

        if (! $gatepass) {
            $this->logWarning(
                'pemeriksaan-behandle',
                'Data gatepass tidak ditemukan.',
                [
                    'no_cont' => $noCont,
                    'no_dok' => $noDok,
                ]
            );

            throw new RuntimeException(
                'Data gatepass tidak ditemukan untuk container tersebut.'
            );
        }

        /*
         * Status pemeriksaan adalah state operasional legacy, jadi selalu
         * dibaca dan ditulis pada prod_new.
         */
        $inspection = DB::connection($this->readConnection)
            ->selectOne(
                "SELECT *
                 FROM t_op_inspection
                 WHERE NO_CONT = ?
                   AND STATUS = 'WAITING'
                 ORDER BY ID DESC
                 FETCH FIRST 1 ROWS ONLY",
                [$noCont]
            );

        /*
         * Belum ada pemeriksaan WAITING:
         * jalankan proses mulai pemeriksaan.
         */
        if (! $inspection) {
            return $this->mulaiPemeriksaan(
                $input,
                $container,
                $jobSlip,
                $gatepass,
                $operatorLogin
            );
        }

        /*
         * Sudah ada pemeriksaan WAITING:
         * nomor seal wajib diisi untuk menyelesaikan pemeriksaan.
         */
        $noSeal = trim((string) ($input['noseal'] ?? ''));

        if ($noSeal === '') {
            throw new RuntimeException(
                'Nomor seal wajib diisi untuk menyelesaikan pemeriksaan.'
            );
        }

        return $this->selesaiPemeriksaan(
            $input,
            $container,
            $jobSlip,
            $gatepass,
            $inspection,
            $operatorLogin
        );
    }

    /**
     * Memulai pemeriksaan container.
     */
    private function mulaiPemeriksaan(
        array $input,
        object $container,
        object $jobSlip,
        object $gatepass,
        string $operatorLogin
    ): array {
        $noCont = trim($input['nomerkon']);

        $noSpk = $container->SPK_NO_SPK
            ?? $container->NO_SPK
            ?? null;

        $now = now();

        $prod = DB::connection($this->readConnection);
        $insertDb = DB::connection($this->insertConnection);

        try {
            $prod->beginTransaction();
            if ($this->insertConnection !== $this->readConnection) {
                $insertDb->beginTransaction();
            }

            /*
             * Periksa ulang di dalam transaksi untuk mengurangi
             * risiko dua proses mulai berjalan bersamaan.
             */
            $waiting = $this->normalizeRow($prod->table('t_op_inspection')
                ->where('NO_CONT', $noCont)
                ->where('STATUS', 'WAITING')
                ->lockForUpdate()
                ->first());

            if ($waiting) {
                throw new RuntimeException(
                    'Pemeriksaan container sudah dimulai.'
                );
            }

            $lokasi = ($gatepass->LOKASI ?? '')
                .'0'
                .($gatepass->TIER ?? '');

            $jnsKegiatan = $gatepass->JNS_KEGIATAN ?? null;
            $noDok = $gatepass->NO_DOK ?? $jobSlip->NO_DOK ?? null;
            $jnsDok = $gatepass->JNS_DOK ?? null;
            $tglDok = $gatepass->TGL_DOK ?? null;

            /*
             * t_op_inspection adalah state proses yang nanti diperbarui
             * saat selesai, sehingga validasi awal/selesai membaca
             * record dari koneksi yang sama.
             */
            $prod->table('t_op_inspection')->insert([
                'NO_CONT' => $noCont,
                'OPERATOR_START' => $operatorLogin,
                'LOKASI' => $lokasi,
                'JNS_KEGIATAN' => $jnsKegiatan,
                'NO_DOK' => $noDok,
                'JNS_DOK' => $jnsDok,
                'TGL_DOK' => $tglDok,
                'NO_SPK' => $noSpk,
                'START_INSP' => $now,
                'STATUS' => 'WAITING',
            ]);

            /*
             * Catatan pergerakan container.
             */
            $insertDb->table('log_pergerakan_kontainer')->insert([
                'NO_CONT' => $noCont,
                'NO_SPK' => $noSpk,
                'ACTIVITY' => 'START_PERIKSA',
            ]);

            /*
             * Memperbarui waktu mulai pada tabel operation.
             */
            $prod->table('t_operation')
                ->where('NO_CONT', $noCont)
                ->where('NO_SPK', $noSpk)
                ->update([
                    'WK_START' => $now,
                ]);

            /*
             * Pembaruan laporan behandle.
             */
            $reportColumn = (string) $jnsKegiatan === '1'
                ? 'PB1_START_PERIKSA'
                : 'PB2_START_PERIKSA';

            $this->updateReportBehandle(
                $prod,
                $noCont,
                (string) $noSpk,
                $reportColumn,
                $now
            );

            $prod->commit();
            if ($this->insertConnection !== $this->readConnection) {
                $insertDb->commit();
            }

            $this->logInfo(
                'pemeriksaan-behandle',
                'Pemeriksaan container berhasil dimulai.',
                [
                    'no_cont' => $noCont,
                    'no_spk' => $noSpk,
                    'operator' => $operatorLogin,
                ]
            );

            return [
                'status' => 1,
                'kode' => 1,
                'message' => 'Pemeriksaan container berhasil dimulai.',
                'data' => [
                    'no_cont' => $noCont,
                    'no_spk' => $noSpk,
                ],
            ];
        } catch (Throwable $e) {
            $this->rollbackTransactions(
                $this->insertConnection === $this->readConnection
                    ? [$prod]
                    : [$prod, $insertDb],
                $noCont,
                $operatorLogin
            );

            $this->logError(
                'pemeriksaan-behandle',
                'Gagal memulai pemeriksaan container.',
                [
                    'no_cont' => $noCont,
                    'no_spk' => $noSpk,
                    'operator' => $operatorLogin,
                    'error' => $e->getMessage(),
                ]
            );

            throw $e;
        }
    }

    /**
     * Menyelesaikan pemeriksaan container.
     */
    private function selesaiPemeriksaan(
        array $input,
        object $container,
        object $jobSlip,
        object $gatepass,
        object $inspection,
        string $operatorLogin
    ): array {
        $noCont = trim($input['nomerkon']);
        $noSeal = trim((string) ($input['noseal'] ?? ''));

        $noSpk = $container->SPK_NO_SPK
            ?? $container->NO_SPK
            ?? null;

        $idAlat = $input['alat'] ?? null;
        $operatorDetail = trim((string) ($input['operator'] ?? ''));
        if ($operatorDetail === '') {
            $operatorDetail = $operatorLogin;
        }

        $tipeCont = $input['tipecont']
            ?? $container->TIPE_CONT
            ?? null;

        $now = now();

        $prod = DB::connection($this->readConnection);
        $insertDb = DB::connection($this->insertConnection);

        try {
            $prod->beginTransaction();
            if ($this->insertConnection !== $this->readConnection) {
                $insertDb->beginTransaction();
            }

            /*
             * Kunci data pemeriksaan yang sedang berlangsung.
             */
            $waiting = $this->normalizeRow($prod->table('t_op_inspection')
                ->where('NO_CONT', $noCont)
                ->where('NO_SPK', $noSpk)
                ->where('STATUS', 'WAITING')
                ->lockForUpdate()
                ->first());

            if (! $waiting) {
                throw new RuntimeException(
                    'Pemeriksaan aktif tidak ditemukan atau sudah selesai.'
                );
            }

            /*
             * Menyelesaikan pemeriksaan.
             */
            $prod->table('t_op_inspection')
                ->where('ID', $waiting->ID)
                ->update([
                    'NO_SEAL' => $noSeal,
                    'OPERATOR_FINISH' => $operatorLogin,
                    'STATUS' => 'DONE',
                    'FINISH_INSP' => $now,
                ]);

            /*
             * Catatan pergerakan container.
             */
            $insertDb->table('log_pergerakan_kontainer')->insert([
                'NO_CONT' => $noCont,
                'NO_SPK' => $noSpk,
                'ACTIVITY' => 'FINISH_PERIKSA',
            ]);

            /*
             * Memperbarui nomor seal dan waktu selesai.
             */
            $prod->table('t_operation')
                ->where('NO_CONT', $noCont)
                ->where('NO_SPK', $noSpk)
                ->update([
                    'NO_SEAL' => $noSeal,
                    'WK_FINISH' => $now,
                ]);

            /*
             * Memperbarui status container dari 460 menjadi 500.
             */
            $prod->table('t_spk_cont')
                ->where('NO_CONT', $noCont)
                ->where('ID', $container->ID)
                ->where('STATUS_CONT', '460')
                ->update([
                    'NO_SEAL' => $noSeal,
                    'STATUS_CONT' => '500',
                ]);

            $prod->table('t_spk')
                ->where('ID', $container->ID)
                ->where('NO_SPK', $noSpk)
                ->update([
                    'KD_STATUS' => '500',
                ]);

            /*
             * Memperbarui job slip menjadi MARSHALLING.
             */
            $jnsKegiatan = (string) (
                $gatepass->JNS_KEGIATAN ?? ''
            );

            $jenis = $jnsKegiatan === '1'
                ? 'EX BEHANDLE 1'
                : 'EX BEHANDLE 2';

            $updated = $prod->table('t_job_slip')
                ->where('ID_JOB_SLIP', $jobSlip->ID_JOB_SLIP)
                ->where('NO_CONT', $noCont)
                ->where('NO_SPK', $noSpk)
                ->where('STATUS', 'WAITING')
                ->update([
                    'JNS_JOB_SLIP' => 'MARSHALLING',
                    'JENIS' => $jenis,
                    'JNS_DOK' => $gatepass->JNS_DOK ?? null,
                    'KD_STATUS' => '20',
                ]);

            if ($updated === 0) {
                /*
                 * Update bisa mengembalikan 0 jika nilai tidak berubah.
                 * Pastikan record masih ada sebelum melanjutkan.
                 */
                $exists = $prod->table('t_job_slip')
                    ->where('ID_JOB_SLIP', $jobSlip->ID_JOB_SLIP)
                    ->where('NO_CONT', $noCont)
                    ->where('NO_SPK', $noSpk)
                    ->where('STATUS', 'WAITING')
                    ->exists();

                if (! $exists) {
                    throw new RuntimeException(
                        'Job slip tidak ditemukan saat pembaruan.'
                    );
                }
            }

            /*
             * Mencari job slip PICKUP untuk detail pemeriksaan.
             */
            $pickup = $this->normalizeRow($prod->table('t_job_slip')
                ->where('NO_CONT', $noCont)
                ->where('NO_SPK', $noSpk)
                ->where('JENIS', 'PICKUP')
                ->orderByDesc('ID_JOB_SLIP')
                ->first());

            if (! $pickup) {
                throw new RuntimeException(
                    'Job slip PICKUP tidak ditemukan.'
                );
            }

            /* Gunakan baris Pickup yang sama untuk foreign key detail. */
            $pickupForInsert = $this->normalizeRow($insertDb->table('t_job_slip')
                ->where('NO_CONT', $noCont)
                ->where('NO_SPK', $noSpk)
                ->where('JENIS', 'PICKUP')
                ->orderByDesc('ID_JOB_SLIP')
                ->first());

            if (! $pickupForInsert) {
                throw new RuntimeException(
                    'Job slip PICKUP tidak ditemukan untuk detail pemeriksaan.'
                );
            }

            /*
             * Membuat detail job slip pemeriksaan.
             */
            $insertDb->table('t_job_detail')->insert([
                'ID_JOB_SLIP' => $pickupForInsert->ID_JOB_SLIP,
                'JENIS' => 'PEMERIKSAAN',
                'NO_SPK' => $noSpk,
                'NO_CONT' => $noCont,
                'TYPE_CONT' => $tipeCont,
                'ID_PEKERJAAN' => 4,
                'ID_ALAT' => $idAlat,
                'OPERATOR' => $operatorDetail,
            ]);

            /*
             * Memperbarui laporan behandle.
             */
            $this->updateReportBehandle(
                $prod,
                $noCont,
                (string) $noSpk,
                'PB2_FINISH_PERIKSA',
                $now
            );

            $this->updateReportBehandle(
                $prod,
                $noCont,
                (string) $noSpk,
                'PB2_NEW_NO_SEAL',
                $noSeal
            );

            $prod->commit();
            if ($this->insertConnection !== $this->readConnection) {
                $insertDb->commit();
            }

            $this->logInfo(
                'pemeriksaan-behandle',
                'Pemeriksaan container berhasil diselesaikan.',
                [
                    'no_cont' => $noCont,
                    'no_spk' => $noSpk,
                    'operator' => $operatorLogin,
                ]
            );

            return [
                'status' => 1,
                'kode' => 2,
                'message' => 'Pemeriksaan container berhasil diselesaikan.',
                'data' => [
                    'no_cont' => $noCont,
                    'no_spk' => $noSpk,
                    'no_seal' => $noSeal,
                ],
            ];
        } catch (Throwable $e) {
            $this->rollbackTransactions(
                $this->insertConnection === $this->readConnection
                    ? [$prod]
                    : [$prod, $insertDb],
                $noCont,
                $operatorLogin
            );

            $this->logError(
                'pemeriksaan-behandle',
                'Gagal menyelesaikan pemeriksaan container.',
                [
                    'no_cont' => $noCont,
                    'no_spk' => $noSpk,
                    'operator' => $operatorLogin,
                    'error' => $e->getMessage(),
                ]
            );

            throw $e;
        }
    }

    /**
     * Memperbarui kolom laporan behandle.
     */
    private function updateReportBehandle(
        $connection,
        string $noCont,
        string $noSpk,
        string $column,
        $value
    ): void {
        $allowedColumns = [
            'PB1_START_PERIKSA',
            'PB2_START_PERIKSA',
            'PB2_FINISH_PERIKSA',
            'PB2_NEW_NO_SEAL',
        ];

        if (! in_array($column, $allowedColumns, true)) {
            throw new RuntimeException(
                'Kolom laporan behandle tidak diizinkan.'
            );
        }

        $report = $this->normalizeRow($connection->table('report_behandle')
            ->where('NO_CONT', $noCont)
            ->where('RB1_NO_SPK', $noSpk)
            ->orderByDesc('ID')
            ->first());

        if (! $report) {
            return;
        }

        $connection->table('report_behandle')
            ->where('ID', $report->ID)
            ->where('NO_CONT', $noCont)
            ->where('RB1_NO_SPK', $noSpk)
            ->update([$column => $value]);
    }

    /**
     * Roll back setiap koneksi yang transaksinya masih terbuka.
     */
    private function rollbackTransactions(
        array $connections,
        string $noCont,
        string $operatorLogin
    ): void {
        foreach ($connections as $connection) {
            try {
                if ($connection->transactionLevel() > 0) {
                    $connection->rollBack();
                }
            } catch (Throwable $rollbackException) {
                $this->logError(
                    'pemeriksaan-behandle',
                    'Rollback transaksi pemeriksaan gagal.',
                    [
                        'no_cont' => $noCont,
                        'operator' => $operatorLogin,
                        'rollback_error' => $rollbackException->getMessage(),
                    ]
                );
            }
        }
    }
}
