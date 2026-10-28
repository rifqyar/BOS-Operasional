<?php

namespace App\Services;

use App\Http\Traits\LogTraits;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Throwable;

class MarshallingYardServices
{
    use LogTraits;

    /**
     * Mengambil daftar Job Slip Marshalling Yard.
     */
    public function getAllJobsYard(
        int $perPage = 10
    ): LengthAwarePaginator {
        return $this->baseQueryYard()
            ->orderByDesc('A.ID_JOB_SLIP')
            ->paginate($perPage);
    }

    /**
     * Mencari data berdasarkan nomor kontainer.
     */
    public function searchYard(
        string $keyword,
        int $perPage = 10
    ): LengthAwarePaginator {
        return $this->baseQueryYard()
            ->where('A.NO_CONT', 'like', '%' . strtoupper(trim($keyword)) . '%')
            ->orderByDesc('A.ID_JOB_SLIP')
            ->paginate($perPage);
    }

    /**
     * Query daftar Marshalling Yard.
     *
     * Pembacaan menggunakan koneksi prod.
     */
    private function baseQueryYard()
    {
        return DB::connection('prod')
            ->table('t_job_slip as A')
            ->join('t_spk as B', 'A.NO_SPK', '=', 'B.NO_SPK')
            ->join('t_gatepass as C', 'A.NO_GATEPASS', '=', 'C.ID')
            ->select([
                'A.ID_JOB_SLIP',
                'A.NO_CONT',
                'C.UKR_CONT',
                'A.LOKASI_AWAL',
                'A.LOKASI_AKHIR',
                'A.TIER_AWAL',
                'A.TIER_AKHIR',
                'A.JENIS',
            ])
            ->where('A.STATUS', 'WAITING')
            ->where('A.KD_STATUS', '20')
            ->where('A.LOKASI_AKHIR', 'like', '1A%')
            ->where('C.FL_ACTIVE', 'Y')
            ->whereIn('A.JENIS', [
                'EX BEHANDLE 1',
                'EX BEHANDLE 2',
            ])
            ->groupBy([
                'A.ID_JOB_SLIP',
                'A.NO_CONT',
                'C.UKR_CONT',
                'A.LOKASI_AWAL',
                'A.LOKASI_AKHIR',
                'A.TIER_AWAL',
                'A.TIER_AKHIR',
                'A.JENIS',
            ]);
    }

    /**
     * Mengambil detail Job Slip untuk form.
     */
    public function getDetailYard(
        string|int $idJobSlip
    ): ?object {
        $sql = "
            SELECT DISTINCT
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
                WHERE LNSW_KD_RESPON = ?
            ) E
                ON D.NO_DOK = E.NO_RESPON
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
            GROUP BY A.NO_CONT
            ORDER BY A.ID_JOB_SLIP DESC
        ";

        return DB::connection('prod')->selectOne(
            $sql,
            ['005', $idJobSlip]
        );
    }

    /**
     * Referensi aktivitas pekerjaan.
     */
    public function getJobActivity()
    {
        return DB::connection('prod')
            ->table('m_job_activity')
            ->get();
    }

    /**
     * Referensi alat.
     */
    public function getEquipment()
    {
        return DB::connection('prod')
            ->table('t_reff_alat')
            ->get();
    }

    /**
     * Referensi operator.
     */
    public function getOperator()
    {
        return DB::connection('prod')
            ->table('reff_user')
            ->where('KD_GROUP', 'like', '%OPR%')
            ->get();
    }

    /**
     * Referensi truk.
     */
    public function getTruck()
    {
        return DB::connection('prod')
            ->table('reff_truck')
            ->orderBy('NO_TRUCK')
            ->get();
    }

    /**
     * Menyimpan proses Marshalling Yard.
     *
     * Catatan:
     * - Data sumber dibaca dari prod.
     * - Penulisan mengikuti implementasi Laravel saat ini, yaitu prod_new.
     * - Pastikan seluruh tabel tujuan memang tersedia di prod_new.
     */
    public function storeMarshallingYard(array $data): array
    {
        $readDb = DB::connection('prod');
        $writeDb = DB::connection('prod_new');

        $idJobSlip = $data['idJobSlip'];
        $noCont = strtoupper(trim($data['nocont']));
        $lokasiAkhir = strtoupper(trim($data['lokak']));

        try {
            $this->logInfo(
                'marshalling-yard',
                'Memulai penyimpanan Marshalling Yard.',
                [
                    'id_job_slip' => $idJobSlip,
                    'no_cont' => $noCont,
                    'lokasi_akhir' => $lokasiAkhir,
                ]
            );

            /*
             * 1. Ambil Job Slip lama.
             */
            $jobSlip = $readDb->table('t_job_slip')
                ->where('ID_JOB_SLIP', $idJobSlip)
                ->where('NO_CONT', $noCont)
                ->where('STATUS', 'WAITING')
                ->where('KD_STATUS', '20')
                ->orderByDesc('ID_JOB_SLIP')
                ->first();

            if (!$jobSlip) {
                return [
                    'success' => false,
                    'status' => 'not_found',
                    'message' => 'Data Job Slip tidak ditemukan atau sudah diproses.',
                ];
            }

            /*
             * 2. Validasi jenis pekerjaan.
             *
             * Marshalling Yard adalah jalur SPK 450.
             * Nilai spk dari form divalidasi juga oleh Controller.
             */
            if ((string) ($data['spk'] ?? '') !== '450') {
                return [
                    'success' => false,
                    'status' => 'invalid_spk',
                    'message' => 'Proses ini hanya berlaku untuk SPK 450.',
                ];
            }

            /*
             * 3. Pisahkan lokasi menjadi block dan tier.
             *
             * Legacy memakai variabel $cekJumlah yang tidak didefinisikan.
             * strlen() digunakan di sini sebagai pengganti sementara.
             * Aturan posisi karakter perlu dicocokkan dengan format lokasi
             * aktual yang digunakan di sistem operasional.
             */
            if ($lokasiAkhir === '') {
                return [
                    'success' => false,
                    'status' => 'invalid_location',
                    'message' => 'Lokasi akhir wajib diisi.',
                ];
            }

            $cekJumlah = strlen($lokasiAkhir);

            if ($cekJumlah > 8) {
                $nmBlock = strtoupper(substr($lokasiAkhir, 0, 8));
            } else {
                $nmBlock = strtoupper(substr($lokasiAkhir, 0, 7));
            }

            $nmTier = strtoupper(substr($lokasiAkhir, -1));

            if ($nmBlock === '' || $nmTier === '') {
                return [
                    'success' => false,
                    'status' => 'invalid_location',
                    'message' => 'Format lokasi akhir tidak valid.',
                ];
            }

            /*
             * 4. Pastikan pemeriksaan kontainer sudah DONE.
             */
            $pemeriksaan = $readDb->table('t_op_inspection')
                ->where('NO_CONT', $noCont)
                ->where('NO_SPK', $jobSlip->NO_SPK)
                ->orderByDesc('ID')
                ->first();

            if (!$pemeriksaan || $pemeriksaan->STATUS !== 'DONE') {
                $this->logWarning(
                    'marshalling-yard',
                    'Penyimpanan ditolak karena pemeriksaan belum DONE.',
                    [
                        'id_job_slip' => $idJobSlip,
                        'no_cont' => $noCont,
                        'status_pemeriksaan' => $pemeriksaan->STATUS ?? null,
                    ]
                );

                return [
                    'success' => false,
                    'status' => 'inspection_not_done',
                    'message' => 'Kontainer belum selesai diperiksa.',
                ];
            }

            /*
             * 5. Ambil tipe kontainer.
             */
            $tipeKontainer = $readDb->table('t_cocostscont')
                ->select('KD_CONT_TIPE')
                ->where('NO_CONT', $noCont)
                ->orderByDesc('ID')
                ->first();

            if (!$tipeKontainer) {
                return [
                    'success' => false,
                    'status' => 'container_type_not_found',
                    'message' => 'Tipe kontainer tidak ditemukan.',
                ];
            }

            /*
             * 6. Ambil SPK terbaru.
             */
            $spk = $readDb->table('t_spk')
                ->where('NO_SPK', $jobSlip->NO_SPK)
                ->orderByDesc('ID')
                ->first();

            if (!$spk) {
                return [
                    'success' => false,
                    'status' => 'spk_not_found',
                    'message' => 'Data SPK tidak ditemukan.',
                ];
            }

            $now = now()->format('Y-m-d H:i:s');

            /*
             * Pertahankan fallback sesuai implementasi sebelumnya.
             * Idealnya identitas operator berasal dari autentikasi aplikasi.
             */
            $operatorLogin = session('USERLOGIN', 'admin2');

            /*
             * 7. Simpan seluruh perubahan dalam satu transaksi prod_new.
             */
            $writeDb->transaction(function () use (
                $writeDb,
                $readDb,
                $data,
                $jobSlip,
                $spk,
                $tipeKontainer,
                $idJobSlip,
                $noCont,
                $nmBlock,
                $nmTier,
                $now,
                $operatorLogin
            ): void {
                /*
                 * Update Job Slip lama.
                 */
                $writeDb->table('t_job_slip')
                    ->where('ID_JOB_SLIP', $idJobSlip)
                    ->where('NO_CONT', $noCont)
                    ->update([
                        'STATUS' => 'DONE',
                        'STATUS_JOB' => 'Y',
                        'KD_STATUS' => '50',
                        'OPERATOR' => $operatorLogin,
                        'WK_STATUS' => $now,
                    ]);

                /*
                 * Insert detail pekerjaan.
                 * Kolom mengikuti mapping legacy.
                 */
                $writeDb->table('t_job_detail')->insert([
                    'ID_JOB_SLIP' => $idJobSlip,
                    'NO_SPK' => $jobSlip->NO_SPK,
                    'NO_CONT' => $noCont,
                    'JENIS' => 'EX BEHANDLE 1',
                    'TYPE_CONT' => $tipeKontainer->KD_CONT_TIPE,

                    'ID_PEKERJAAN' => $data['jenispekerjaan1'] ?? 0,
                    'ID_ALAT' => $data['alat1'] ?? null,
                    'OPERATOR' => $data['operator1'] ?? null,

                    'ID_PEKERJAAN_2' => $data['jenispekerjaan2'] ?? 0,
                    'ID_TRUCK' => $data['truck1'] ?? null,
                    'OPERATOR_2' => $data['operator2'] ?? null,

                    'ID_PEKERJAAN_3' => $data['jenispekerjaan3'] ?? 0,
                    'ID_ALAT_3' => $data['alat3'] ?? null,
                    'OPERATOR_3' => $data['operator3'] ?? null,

                    'ID_USER' => 'admin2',
                    'WK_STATUS' => $now,
                ]);

                /*
                 * Update lokasi dan status kontainer.
                 */
                $writeDb->table('t_spk_cont')
                    ->where('NO_CONT', $noCont)
                    ->where('ID', $spk->ID)
                    ->update([
                        'LOKASI' => $nmBlock,
                        'TIER' => $nmTier,
                        'STATUS_CONT' => '450',
                    ]);

                /*
                 * Update Gatepass.
                 */
                $writeDb->table('t_gatepass')
                    ->where('NO_CONT', $noCont)
                    ->where('ID', $jobSlip->NO_GATEPASS)
                    ->update([
                        'STATUS' => 'DONE',
                    ]);

                /*
                 * Buat Job Slip baru untuk proses berikutnya.
                 */
                $writeDb->table('t_job_slip')->insert([
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
                    'WK_STATUS' => $now,
                    'OPERATOR' => $operatorLogin,
                ]);

                /*
                 * Catat pergerakan kontainer.
                 */
                $writeDb->table('log_pergerakan_kontainer')->insert([
                    'NO_CONT' => $noCont,
                    'NO_SPK' => $jobSlip->NO_SPK,
                    'ACTIVITY' => 'MARSHALLING_YARD',
                ]);

                /*
                 * Perbarui report_behandle jika data laporan ditemukan.
                 * Pencarian report mengikuti data sumber prod.
                 */
                $report = $readDb->table('report_behandle')
                    ->where('NO_CONT', $noCont)
                    ->where('REQ_NO_DOK', $jobSlip->NO_DOK)
                    ->where('RB1_NO_SPK', $jobSlip->NO_SPK)
                    ->orderByDesc('ID')
                    ->first();

                if ($report) {
                    $lokasiAfter = $nmBlock . '0' . $nmTier;

                    if (($data['job'] ?? '') === 'BEHANDLE 1') {
                        $writeDb->table('report_behandle')
                            ->where('ID', $report->ID)
                            ->where('NO_CONT', $noCont)
                            ->where('REQ_NO_DOK', $jobSlip->NO_DOK)
                            ->update([
                                'PB1_MARSHALLING_B1' => $now,
                                'PB1_LOKASI_AFTER' => $lokasiAfter,
                            ]);
                    } else {
                        $writeDb->table('report_behandle')
                            ->where('ID', $report->ID)
                            ->where('NO_CONT', $noCont)
                            ->where('REQ_NO_DOK', $jobSlip->NO_DOK)
                            ->update([
                                'PB2_MARSHALLING_EX_B2' => $now,
                                'PB2_LOKASI_AFTER' => $lokasiAfter,
                            ]);
                    }
                }
            });

            $this->logInfo(
                'marshalling-yard',
                'Penyimpanan Marshalling Yard berhasil.',
                [
                    'id_job_slip' => $idJobSlip,
                    'no_cont' => $noCont,
                    'lokasi_akhir' => $lokasiAkhir,
                ]
            );

            return [
                'success' => true,
                'status' => 'success',
                'message' => 'Marshalling Yard berhasil disimpan.',
            ];
        } catch (Throwable $e) {
            $this->logError(
                'marshalling-yard',
                'Gagal menyimpan Marshalling Yard.',
                [
                    'id_job_slip' => $idJobSlip,
                    'no_cont' => $noCont,
                    'error' => $e->getMessage(),
                ]
            );

            throw $e;
        }
    }
}