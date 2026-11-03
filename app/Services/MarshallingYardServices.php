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
     *
     * Pembacaan menggunakan koneksi prod.
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
            ->where(
                'A.NO_CONT',
                'like',
                '%' . strtoupper(trim($keyword)) . '%'
            )
            ->orderByDesc('A.ID_JOB_SLIP')
            ->paginate($perPage);
    }

    /**
     * Query dasar daftar Marshalling Yard.
     *
     * Pembacaan menggunakan koneksi prod.
     */
    private function baseQueryYard()
    {
        return DB::connection('prod')
            ->table('t_job_slip as A')
            ->join(
                't_spk as B',
                'A.NO_SPK',
                '=',
                'B.NO_SPK'
            )
            ->join(
                't_gatepass as C',
                'A.NO_GATEPASS',
                '=',
                'C.ID'
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
     *
     * Seluruh data dibaca dari koneksi prod.
     */
    public function getDetailYard(
        string|int $idJobSlip
    ): ?object {
        try {
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
                GROUP BY
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
                ORDER BY A.ID_JOB_SLIP DESC
            ";
            

            return DB::connection('prod')->selectOne(
                $sql,
                ['005', $idJobSlip]
            );
        } catch (Throwable $e) {
            $this->logError(
                'marshalling-yard',
                'Gagal mengambil detail Job Slip Marshalling Yard.',
                [
                    'stage' => 'getDetailYard',
                    'id_job_slip' => $idJobSlip,
                    'exception' => get_class($e),
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            );

            throw $e;
        }
    }

    /**
     * Referensi aktivitas pekerjaan.
     */
    public function getJobActivity()
    {
        try {
            return DB::connection('prod')
                ->table('m_job_activity')
                ->get();
        } catch (Throwable $e) {
            $this->logError(
                'marshalling-yard',
                'Gagal mengambil referensi aktivitas pekerjaan.',
                [
                    'stage' => 'getJobActivity',
                    'exception' => get_class($e),
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            );

            throw $e;
        }
    }

    /**
     * Referensi alat.
     */
    public function getEquipment()
    {
        try {
            return DB::connection('prod')
                ->table('t_reff_alat')
                ->get();
        } catch (Throwable $e) {
            $this->logError(
                'marshalling-yard',
                'Gagal mengambil referensi alat.',
                [
                    'stage' => 'getEquipment',
                    'exception' => get_class($e),
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            );

            throw $e;
        }
    }

    /**
     * Referensi operator.
     */
    public function getOperator()
    {
        try {
            return DB::connection('prod')
                ->table('reff_user')
                ->where('KD_GROUP', 'like', '%OPR%')
                ->get();
        } catch (Throwable $e) {
            $this->logError(
                'marshalling-yard',
                'Gagal mengambil referensi operator.',
                [
                    'stage' => 'getOperator',
                    'exception' => get_class($e),
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            );

            throw $e;
        }
    }

    /**
     * Referensi truk.
     */
    public function getTruck()
    {
        try {
            return DB::connection('prod')
                ->table('reff_truck')
                ->orderBy('NO_TRUCK')
                ->get();
        } catch (Throwable $e) {
            $this->logError(
                'marshalling-yard',
                'Gagal mengambil referensi truk.',
                [
                    'stage' => 'getTruck',
                    'exception' => get_class($e),
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            );

            throw $e;
        }
    }

    /**
     * Menyimpan proses Marshalling Yard.
     *
     * Pembacaan data sumber : prod
     * Penulisan data        : prod_new
     *
     * Pencatatan stage digunakan untuk mengidentifikasi
     * bagian yang gagal ketika terjadi exception.
     */
    public function storeMarshallingYard(array $data): array
    {
        $readDb = DB::connection('prod');
        $writeDb = DB::connection('prod_new');

        $idJobSlip = $data['idJobSlip'] ?? null;
        $noCont = strtoupper(trim($data['nocont'] ?? ''));
        $lokasiAkhir = strtoupper(trim($data['lokak'] ?? ''));

        $stage = 'inisialisasi';

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
             * 1. Ambil Job Slip lama dari prod.
             */
            $stage = 'ambil_job_slip_prod';

            $jobSlip = $readDb->table('t_job_slip')
                ->where('ID_JOB_SLIP', $idJobSlip)
                ->where('NO_CONT', $noCont)
                ->where('STATUS', 'WAITING')
                ->where('KD_STATUS', '20')
                ->orderByDesc('ID_JOB_SLIP')
                ->first();

            if (!$jobSlip) {
                $this->logWarning(
                    'marshalling-yard',
                    'Job Slip tidak ditemukan atau sudah diproses.',
                    [
                        'stage' => $stage,
                        'id_job_slip' => $idJobSlip,
                        'no_cont' => $noCont,
                    ]
                );

                return [
                    'success' => false,
                    'status' => 'not_found',
                    'message' => 'Data Job Slip tidak ditemukan atau sudah diproses.',
                ];
            }

            /*
             * 2. Validasi SPK.
             */
            $stage = 'validasi_spk';

            if ((string) ($data['spk'] ?? '') !== '450') {
                return [
                    'success' => false,
                    'status' => 'invalid_spk',
                    'message' => 'Proses ini hanya berlaku untuk SPK 450.',
                ];
            }

            /*
             * 3. Validasi dan pemisahan lokasi.
             *
             * Aturan pemisahan lokasi dipertahankan dari kode
             * sebelumnya. Pastikan aturan panjang karakter ini
             * memang sesuai format lokasi operasional.
             */
            $stage = 'validasi_lokasi';

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
             * 4. Ambil status pemeriksaan kontainer dari prod.
             */
            $stage = 'ambil_pemeriksaan_kontainer';

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
                        'stage' => $stage,
                        'id_job_slip' => $idJobSlip,
                        'no_cont' => $noCont,
                        'no_spk' => $jobSlip->NO_SPK,
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
             * 5. Ambil tipe kontainer dari prod.
             */
            $stage = 'ambil_tipe_kontainer';

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
             * 6. Ambil data SPK dari prod.
             */
            $stage = 'ambil_spk_prod';

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
             * Identitas operator mengikuti implementasi sebelumnya.
             */
            $operatorLogin = session('USERLOGIN', 'admin2');

            /*
             * 7. Simpan seluruh perubahan dalam transaksi prod_new.
             */
            $stage = 'transaksi_prod_new';

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
                $operatorLogin,
                &$stage
            ): void {
                /*
                 * 7.1 Update Job Slip lama.
                 */
                $stage = 'update_job_slip_prod_new';

                $affectedJobSlip = $writeDb->table('t_job_slip')
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
                 * Tidak langsung menganggap affected rows = 0
                 * sebagai kegagalan karena driver database dapat
                 * mengembalikan 0 ketika nilainya tidak berubah.
                 *
                 * Kesesuaian ID antara prod dan prod_new tetap
                 * perlu dipastikan.
                 */

                /*
                 * 7.2 Insert detail pekerjaan.
                 */
                $stage = 'insert_job_detail';

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
                 * 7.3 Update lokasi dan status kontainer.
                 */
                $stage = 'update_spk_cont';

                $writeDb->table('t_spk_cont')
                    ->where('NO_CONT', $noCont)
                    ->where('ID', $spk->ID)
                    ->update([
                        'LOKASI' => $nmBlock,
                        'TIER' => $nmTier,
                        'STATUS_CONT' => '450',
                    ]);

                /*
                 * 7.4 Update Gatepass.
                 */
                $stage = 'update_gatepass';

                $writeDb->table('t_gatepass')
                    ->where('NO_CONT', $noCont)
                    ->where('ID', $jobSlip->NO_GATEPASS)
                    ->update([
                        'STATUS' => 'DONE',
                    ]);

                /*
                 * 7.5 Insert Job Slip baru.
                 */
                $stage = 'insert_job_slip_baru';

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
                 * 7.6 Catat pergerakan kontainer.
                 */
                $stage = 'insert_log_pergerakan_kontainer';

                $writeDb->table('log_pergerakan_kontainer')->insert([
                    'NO_CONT' => $noCont,
                    'NO_SPK' => $jobSlip->NO_SPK,
                    'ACTIVITY' => 'MARSHALLING_YARD',
                ]);

                /*
                 * 7.7 Ambil report dari prod.
                 *
                 * Pencarian membaca data sumber dari prod.
                 */
                $stage = 'ambil_report_behandle';

                $report = $readDb->table('report_behandle')
                    ->where('NO_CONT', $noCont)
                    ->where('REQ_NO_DOK', $jobSlip->NO_DOK)
                    ->where('RB1_NO_SPK', $jobSlip->NO_SPK)
                    ->orderByDesc('ID')
                    ->first();

                /*
                 * 7.8 Update report di prod_new jika ditemukan.
                 *
                 * Perlu dipastikan bahwa ID report di prod dan
                 * prod_new memang identik.
                 */
                if ($report) {
                    $stage = 'update_report_behandle';

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
                    'stage' => $stage,
                    'id_job_slip' => $idJobSlip,
                    'no_cont' => $noCont,
                    'lokasi_akhir' => $lokasiAkhir,
                    'exception' => get_class($e),
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'code' => $e->getCode(),
                ]
            );

            /*
             * Exception dilempar kembali agar Controller tetap
             * menangani kegagalan sesuai kontrak respons API.
             */
            throw $e;
        }
    }
}