<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class MarshallingCicService
{

    public function getAllJobs()
    {
        return DB::connection('prod')->select(
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
            WHERE NOT EXISTS (
                SELECT 1
                FROM t_atensi_p2
                WHERE NO_CONT = C.NO_CONT
                AND NO_SPK = B.NO_SPK
            )
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
            ORDER BY A.ID_JOB_SLIP DESC"
        );
    }


    public function search(string $keyword)
    {
        return DB::connection('prod')->select(
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
            ['%' . $keyword . '%']
        );
    }


    public function getDetail(int $idJobSlip)
    {
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
            [$idJobSlip]
        );
    }

    public function getJobActivities()
    {
        return DB::connection('prod')->select(
            "SELECT * FROM m_job_activity"
        );
    }

    public function getEquipments()
    {
        return DB::connection('prod')->select(
            "SELECT * FROM t_reff_alat"
        );
    }

    public function getTrucks()
    {
        return DB::connection('prod')->select(
            "SELECT * FROM reff_truck"
        );
    }

    public function getOperators()
    {
        return DB::connection('prod')->select(
            "SELECT *
             FROM reff_user
             WHERE KD_GROUP LIKE '%OPR%'"
        );
    }
}