<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class MarshallingCicService
{
    /**
     * Get all Marshalling CIC jobs with pagination.
     *
     * Default: 10 data per halaman.
     */
    public function getAllJobs(
        int $perPage = 10
    ): LengthAwarePaginator {
        $perPage = max(
            1,
            min($perPage, 50)
        );

        $query = DB::connection('prod')
            ->table('t_job_slip as A')

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
                DB::connection('prod')
                    ->table('t_ppk_hdr')
                    ->where(
                        'LNSW_KD_RESPON',
                        '005'
                    ),
                'E',
                function ($join) {
                    $join
                        ->on(
                            'D.NO_DOK',
                            '=',
                            'E.NO_RESPON'
                        )
                        ->on(
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

            ->whereNotExists(function ($query) {
                $query
                    ->select(DB::raw('1'))
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

            ->where(
                'A.STATUS',
                'WAITING'
            )

            ->where(
                'A.KD_STATUS',
                '20'
            )

            ->where(
                'A.LOKASI_AKHIR',
                'LIKE',
                'CIC%'
            )

            ->where(
                'C.STATUS_CONT',
                '!=',
                900
            )

            ->where(
                'D.FL_ACTIVE',
                'Y'
            )

            ->whereIn(
                'A.JENIS',
                [
                    'BEHANDLE 1',
                    'BEHANDLE 2',
                    'EX BEHANDLE 1',
                    'EX BEHANDLE 2',
                ]
            )

            ->distinct()

            ->orderBy(
                'A.ID_JOB_SLIP',
                'DESC'
            );

        return $query->paginate(
            $perPage
        );
    }


    /**
     * Search Marshalling CIC by container.
     *
     * Search tetap menggunakan Collection/result biasa.
     */
    public function search(string $keyword)
    {
        return DB::connection('prod')->select(
            "SELECT DISTINCT
                A.ID_JOB_SLIP,
                A.NO_CONT,
                C.UKR_CONT,
                A.LOKASI_AWAL,
                A.LOKASI_AKHIt,
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
            WHERE A.NO_CONT LIKE ?
            AND A.STATUS = 'WAITING'
            AND A.KD_STATUS = '20'
            AND A.LOKASI_AKHIR LIKE 'CIC%'
            AND C.STATUS_CONT != 900
            AND D.FL_ACTIVE = 'Y'
            AND A.JENIS IN (
                'BEHANDLE 1',
                'BEHANDLE 2'
            )
            ORDER BY A.ID_JOB_SLIP DESC",
            [
                '%' . $keyword . '%'
            ]
        );
    }


    /**
     * Get detail Marshalling CIC job.
     */
    public function getDetail(
        int $idJobSlip
    ) {
        return DB::connection('prod')->selectOne(
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
            [
                $idJobSlip
            ]
        );
    }


    /**
     * Get job activities.
     */
    public function getJobActivities()
    {
        return DB::connection('prod')->select(
            "SELECT *
             FROM m_job_activity"
        );
    }


    /**
     * Get equipments.
     */
    public function getEquipments()
    {
        return DB::connection('prod')->select(
            "SELECT *
             FROM t_reff_alat"
        );
    }


    /**
     * Get trucks.
     */
    public function getTrucks()
    {
        return DB::connection('prod')->select(
            "SELECT *
             FROM reff_truck"
        );
    }


    /**
     * Get operators.
     */
    public function getOperators()
    {
        return DB::connection('prod')->select(
            "SELECT *
             FROM reff_user
             WHERE KD_GROUP LIKE '%OPR%'"
        );
    }
}