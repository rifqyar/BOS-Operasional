<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class MarshallingYardServices
{
    /**
     * Ambil seluruh job Marshalling Yard
     * dengan pagination.
     */
    public function getAllJobsYard(
        int $perPage = 10
    ): LengthAwarePaginator {
        $perPage = max(1, min($perPage, 50));

        $query = DB::connection('prod')
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
            ->where('A.LOKASI_AKHIR', 'LIKE', '1A%')
            ->where('C.FL_ACTIVE', 'Y')
            ->whereIn('A.JENIS', [
                'EX BEHANDLE 1',
                'EX BEHANDLE 2',
            ])
            ->groupBy(
                'A.NO_CONT',
                'A.ID_JOB_SLIP',
                'C.UKR_CONT',
                'A.LOKASI_AWAL',
                'A.LOKASI_AKHIR',
                'A.TIER_AWAL',
                'A.TIER_AKHIR',
                'A.JENIS'
            )
            ->orderBy(
                'A.ID_JOB_SLIP',
                'DESC'
            );

        return $query->paginate($perPage);
    }


    /**
     * Search Marshalling Yard berdasarkan
     * nomor container.
     */
    public function searchYard(
        string $keyword,
        int $perPage = 10
    ): LengthAwarePaginator {
        $perPage = max(1, min($perPage, 50));

        $query = DB::connection('prod')
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
            ->where(
                'A.NO_CONT',
                'LIKE',
                '%' . $keyword . '%'
            )
            ->where('A.STATUS', 'WAITING')
            ->where('A.KD_STATUS', '20')
            ->where('A.LOKASI_AKHIR', 'LIKE', '1A%')
            ->where('C.FL_ACTIVE', 'Y')
            ->whereIn('A.JENIS', [
                'EX BEHANDLE 1',
                'EX BEHANDLE 2',
            ])
            ->groupBy(
                'A.NO_CONT',
                'A.ID_JOB_SLIP',
                'C.UKR_CONT',
                'A.LOKASI_AWAL',
                'A.LOKASI_AKHIR',
                'A.TIER_AWAL',
                'A.TIER_AKHIR',
                'A.JENIS'
            )
            ->orderBy(
                'A.ID_JOB_SLIP',
                'DESC'
            );

        return $query->paginate($perPage);
    }


    /**
     * Detail Job Marshalling Yard.
     */
    public function getDetailYard(
        string|int $idJobSlip
    ): ?object {
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
                    WHERE LNSW_KD_RESPON = ?
                ) E
                    ON D.NO_DOK = E.NO_RESPON
                    AND D.TGL_DOK = E.TG_RESPON
                WHERE A.STATUS = ?
                    AND A.KD_STATUS = ?
                    AND C.STATUS_CONT != ?
                    AND D.FL_ACTIVE = ?
                    AND A.JENIS IN (?, ?, ?, ?)
                    AND A.ID_JOB_SLIP = ?
                GROUP BY A.NO_CONT
                ORDER BY A.ID_JOB_SLIP DESC",
            [
                '005',
                'WAITING',
                '20',
                '900',
                'Y',
                'BEHANDLE 1',
                'BEHANDLE 2',
                'EX BEHANDLE 1',
                'EX BEHANDLE 2',
                $idJobSlip,
            ]
        );
    }


    /**
     * Master Job Activity.
     */
    public function getJobActivity(): array
    {
        return DB::connection('prod')->select(
            "SELECT *
             FROM m_job_activity"
        );
    }


    /**
     * Master Equipment.
     */
    public function getEquipment(): array
    {
        return DB::connection('prod')->select(
            "SELECT *
             FROM t_reff_alat"
        );
    }


    /**
     * Master Operator.
     */
    public function getOperator(): array
    {
        return DB::connection('prod')->select(
            "SELECT *
             FROM reff_user
             WHERE KD_GROUP LIKE ?",
            ['%OPR%']
        );
    }


    /**
     * Master Truck.
     */
    public function getTruck(): array
    {
        return DB::connection('prod')->select(
            "SELECT *
             FROM reff_truck
             ORDER BY NO_TRUCK ASC"
        );
    }
}