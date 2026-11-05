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
     * Database lama MySQL.
     * Semua pencarian dan validasi bisnis dilakukan di sini.
     */
    protected function readDb()
    {
        return DB::connection('prod');
    }

    /**
     * Mengambil daftar job Marshalling CIC dari MySQL prod.
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
            ->leftJoin(
                'reff_status_spk as R',
                'C.STATUS_CONT',
                '=',
                'R.ID'
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
                'R.KETERANGAN as STATUS_SPK_TEXT',
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
     * Search job Marshalling CIC dari MySQL prod.
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
                D.RESPON,
                R.KETERANGAN AS STATUS_SPK_TEXT
            FROM t_job_slip A
            INNER JOIN t_spk B
                ON A.NO_SPK = B.NO_SPK
            INNER JOIN t_spk_cont C
                ON B.ID = C.ID
            INNER JOIN t_gatepass D
                ON A.NO_GATEPASS = D.ID
            LEFT JOIN reff_status_spk R
                ON C.STATUS_CONT = R.ID
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
     * Mengambil detail job slip dari MySQL prod.
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
                R.KETERANGAN AS STATUS_SPK_TEXT,
                E.LNSW_NOAJU
            FROM t_job_slip A
            INNER JOIN t_spk B
                ON A.NO_SPK = B.NO_SPK
            INNER JOIN t_spk_cont C
                ON B.ID = C.ID
            INNER JOIN t_gatepass D
                ON A.NO_GATEPASS = D.ID
            LEFT JOIN reff_status_spk R
                ON C.STATUS_CONT = R.ID
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
     * Referensi jenis pekerjaan dari MySQL prod.
     */
    public function getJobActivities()
    {
        return $this->readDb()->select(
            'SELECT * FROM m_job_activity'
        );
    }

    /**
     * Referensi alat dari MySQL prod.
     */
    public function getEquipments()
    {
        return $this->readDb()->select(
            'SELECT * FROM t_reff_alat'
        );
    }

    /**
     * Referensi truck dari MySQL prod.
     */
    public function getTrucks()
    {
        return $this->readDb()->select(
            'SELECT * FROM reff_truck'
        );
    }

    /**
     * Referensi operator dari MySQL prod.
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
     * Simpan proses Marshalling CIC beserta seluruh perubahan legacy di MySQL prod.
     */
    public function storeMarshallingCic(
        array $data,
        string $username
    ): array {
        $db = $this->readDb();
        $idJobSlip = (int) ($data['idJobSlip'] ?? 0);
        $noCont = strtoupper(trim((string) ($data['nocont'] ?? '')));
        $lokasiAkhir = strtoupper(trim((string) ($data['lokak'] ?? '')));
        $job = strtoupper(trim((string) ($data['job'] ?? '')));
        $note = $data['note'] ?? null;
        $fumigasi = $data['fumigasi'] ?? null;
        $transactionStarted = false;

        try {
            $db->beginTransaction();
            $transactionStarted = true;

            if ($idJobSlip <= 0 || $noCont === '' || $lokasiAkhir === '') {
                throw new RuntimeException('Data job, container, dan lokasi akhir wajib tersedia.');
            }

            if (trim($username) === '') {
                throw new RuntimeException('Username operator tidak ditemukan.');
            }

            $nmBlock = strtoupper(substr($lokasiAkhir, 0, 5));
            $nmTier = strtoupper(substr($lokasiAkhir, -1));

            if ($nmBlock === '' || $nmTier === '') {
                throw new RuntimeException('Format lokasi akhir tidak valid.');
            }

            $jobSlip = $db->table('t_job_slip')
                ->where('ID_JOB_SLIP', $idJobSlip)
                ->where('NO_CONT', $noCont)
                ->lockForUpdate()
                ->first();

            if (!$jobSlip) {
                throw new RuntimeException("Job slip {$idJobSlip} untuk kontainer {$noCont} tidak ditemukan.");
            }

            $jenisSumber = strtoupper(trim((string) $jobSlip->JENIS));
            $jenisDiizinkan = [
                'BEHANDLE 1',
                'BEHANDLE 2',
                'EX BEHANDLE 1',
                'EX BEHANDLE 2',
            ];

            if (!in_array($jenisSumber, $jenisDiizinkan, true)) {
                throw new RuntimeException('Jenis pekerjaan pada job slip tidak sesuai untuk Marshalling CIC.');
            }

            if (
                strtoupper(trim((string) $jobSlip->STATUS)) !== 'WAITING'
                || (string) $jobSlip->KD_STATUS !== '20'
            ) {
                throw new RuntimeException('Job slip bukan pekerjaan yang sedang menunggu proses Marshalling CIC.');
            }

            $noSpk = trim((string) ($jobSlip->NO_SPK ?? ''));
            if ($noSpk === '') {
                throw new RuntimeException('Nomor SPK pada job slip sumber kosong.');
            }

            $spk = $db->table('t_spk')
                ->where('NO_SPK', $noSpk)
                ->orderByDesc('ID')
                ->first();

            if (!$spk) {
                throw new RuntimeException("SPK {$noSpk} tidak ditemukan.");
            }

            $spkCont = $db->table('t_spk_cont')
                ->where('ID', $spk->ID)
                ->where('NO_CONT', $noCont)
                ->lockForUpdate()
                ->first();

            if (!$spkCont) {
                throw new RuntimeException("Kontainer {$noCont} tidak terdaftar pada SPK {$noSpk}.");
            }

            $statusSekarang = $db->table('reff_status_spk')
                ->where('ID', $spkCont->STATUS_CONT)
                ->value('KETERANGAN');
            $statusCic = $db->table('reff_status_spk')
                ->where('ID', '460')
                ->value('KETERANGAN');

            if (!$statusSekarang || !$statusCic) {
                throw new RuntimeException('Referensi deskripsi status SPK tidak ditemukan. Hubungi administrator.');
            }

            if (mb_strtoupper(trim((string) $statusSekarang)) !== mb_strtoupper(trim((string) $statusCic))) {
                throw new RuntimeException(
                    "Status SPK '{$statusSekarang}' belum sesuai untuk proses Marshalling CIC. " .
                    "Status yang diperlukan: '{$statusCic}'."
                );
            }

            if ((string) ($data['jenispekerjaan1'] ?? '') === '') {
                throw new RuntimeException('Jenis pekerjaan 1 wajib dipilih.');
            }

            $container = $db->table('t_cocostscont')
                ->where('NO_CONT', $noCont)
                ->orderByDesc('ID')
                ->first();

            if (!$container) {
                throw new RuntimeException("Data tipe kontainer {$noCont} tidak ditemukan.");
            }

            $now = now();
            $db->table('t_job_detail')->insert([
                'ID_JOB_SLIP' => $idJobSlip,
                'NO_SPK' => $noSpk,
                'NO_CONT' => $noCont,
                'JENIS' => 'CIC',
                'TYPE_CONT' => $container->KD_CONT_TIPE ?? null,
                'ID_PEKERJAAN' => $data['jenispekerjaan1'],
                'ID_ALAT' => $data['alat1'] ?? null,
                'OPERATOR' => $data['operator1'] ?? null,
                'ID_PEKERJAAN_2' => $data['jenispekerjaan2'] ?? null,
                'ID_TRUCK' => $data['truck1'] ?? null,
                'OPERATOR_2' => $data['operator2'] ?? null,
                'ID_PEKERJAAN_3' => $data['jenispekerjaan3'] ?? null,
                'ID_ALAT_3' => $data['alat3'] ?? null,
                'OPERATOR_3' => $data['operator3'] ?? null,
                'ID_USER' => 'admin2',
                'WK_STATUS' => $now,
            ]);

            $updatedJobSlip = $db->table('t_job_slip')
                ->where('ID_JOB_SLIP', $idJobSlip)
                ->where('NO_CONT', $noCont)
                ->where('STATUS', 'WAITING')
                ->where('KD_STATUS', '20')
                ->update([
                    'STATUS' => 'DONE',
                    'STATUS_JOB' => 'Y',
                    'STATUS_FUMIGASI' => $fumigasi,
                    'NOTE' => $note,
                    'KD_STATUS' => '50',
                    'OPERATOR' => $username,
                    'WK_STATUS' => $now,
                ]);

            if ($updatedJobSlip < 1) {
                throw new RuntimeException('Job slip tidak berhasil diperbarui atau sudah diproses.');
            }

            $db->table('t_spk_cont')
                ->where('ID', $spk->ID)
                ->where('NO_CONT', $noCont)
                ->update([
                    'LOKASI' => $nmBlock,
                    'TIER' => $nmTier,
                    'STATUS_CONT' => '460',
                ]);

            $newJobSlipId = $db->table('t_job_slip')->insertGetId([
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
                'WK_STATUS' => $now,
                'OPERATOR' => $username,
            ], 'ID_JOB_SLIP');

            $db->table('log_pergerakan_kontainer')->insert([
                'NO_CONT' => $noCont,
                'NO_SPK' => $noSpk,
                'ACTIVITY' => 'MARSHALLING_CIC',
            ]);

            $db->table('t_operation_new')->insert([
                'ID_SPK' => $spk->ID,
                'NO_CONT' => $noCont,
                'KD_STATUS' => '460',
                'WK_STATUS' => $now,
                'OPERATOR' => $username,
            ]);

            $report = $db->table('report_behandle')
                ->where('NO_CONT', $noCont)
                ->where('REQ_NO_DOK', $jobSlip->NO_DOK)
                ->where('RB1_NO_SPK', $noSpk)
                ->orderByDesc('ID')
                ->first();

            if ($report) {
                $jobUntukLaporan = $job !== '' ? $job : $jenisSumber;
                $reportUpdate = $jobUntukLaporan === 'BEHANDLE 1'
                    ? [
                        'PB1_LOKASI_CIC' => $nmBlock . '0' . $nmTier,
                        'PB1_MARSHALLING_B1' => $now,
                    ]
                    : [
                        'PB2_MARSHALLING_B2' => $now,
                        'PB2_LOKASI_CIC' => $nmBlock . '0' . $nmTier,
                    ];

                $updatedReport = $db->table('report_behandle')
                    ->where('ID', $report->ID)
                    ->where('NO_CONT', $noCont)
                    ->where('REQ_NO_DOK', $jobSlip->NO_DOK)
                    ->update($reportUpdate);

                if ($updatedReport < 1) {
                    throw new RuntimeException('Data report behandle tidak berhasil diperbarui.');
                }
            }

            $db->commit();
            $transactionStarted = false;

            $result = [
                'id_job_slip' => $idJobSlip,
                'new_job_slip_id' => $newJobSlipId,
                'no_cont' => $noCont,
                'no_spk' => $noSpk,
                'status_spk' => $statusSekarang,
                'lokasi' => $nmBlock,
                'tier' => $nmTier,
                'database' => 'prod',
            ];

            $this->logInfo(
                'marshalling-cic',
                'Marshalling CIC berhasil disimpan di prod.',
                $result + ['username' => $username]
            );

            return $result;
        } catch (Throwable $e) {
            if ($transactionStarted && $db->transactionLevel() > 0) {
                try {
                    $db->rollBack();
                } catch (Throwable $rollbackException) {
                    $this->logError(
                        'marshalling-cic',
                        'Rollback transaksi prod Marshalling CIC gagal.',
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
                'Marshalling CIC gagal disimpan dan transaksi prod di-rollback.',
                [
                    'id_job_slip' => $idJobSlip,
                    'no_cont' => $noCont,
                    'username' => $username,
                    'exception' => get_class($e),
                    'error' => $e->getMessage(),
                ]
            );

            throw $e;
        }
    }
}
