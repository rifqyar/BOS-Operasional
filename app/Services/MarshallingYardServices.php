<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class MarshallingYardServices
{

    public function getAllJobsYard(): array
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
                    A.JENIS
                FROM t_job_slip A
                INNER JOIN t_spk B
                    ON A.NO_SPK = B.NO_SPK
                INNER JOIN t_gatepass C
                    ON A.NO_GATEPASS = C.ID
                WHERE A.STATUS = ?
                    AND A.KD_STATUS = ?
                    AND A.LOKASI_AKHIR LIKE ?
                    AND C.FL_ACTIVE = ?
                    AND A.JENIS IN (?, ?)
                GROUP BY A.NO_CONT
                ORDER BY A.ID_JOB_SLIP DESC",
            [
                'WAITING',
                '20',
                '1A%',
                'Y',
                'EX BEHANDLE 1',
                'EX BEHANDLE 2',
            ]
        );
    }


    public function searchYard(string $keyword): array
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
                    A.JENIS
                FROM t_job_slip A
                INNER JOIN t_spk B
                    ON A.NO_SPK = B.NO_SPK
                INNER JOIN t_gatepass C
                    ON A.NO_GATEPASS = C.ID
                WHERE A.NO_CONT LIKE ?
                    AND A.STATUS = ?
                    AND A.KD_STATUS = ?
                    AND A.LOKASI_AKHIR LIKE ?
                    AND C.FL_ACTIVE = ?
                    AND A.JENIS IN (?, ?)
                GROUP BY A.NO_CONT
                ORDER BY A.ID_JOB_SLIP DESC",
            [
                '%' . $keyword . '%',
                'WAITING',
                '20',
                '1A%',
                'Y',
                'EX BEHANDLE 1',
                'EX BEHANDLE 2',
            ]
        );
    }


    public function getDetailYard(string|int $idJobSlip): ?object
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


    public function getJobActivity(): array
    {
        return DB::connection('prod')->select(
            "SELECT * FROM m_job_activity"
        );
    }


    public function getEquipment(): array
    {
        return DB::connection('prod')->select(
            "SELECT * FROM t_reff_alat"
        );
    }


    public function getOperator(): array
    {
        return DB::connection('prod')->select(
            "SELECT *
             FROM reff_user
             WHERE KD_GROUP LIKE ?",
            ['%OPR%']
        );
    }


    public function getTruck(): array
    {
        return DB::connection('prod')->select(
            "SELECT *
             FROM reff_truck
             ORDER BY NO_TRUCK ASC"
        );
    }



}