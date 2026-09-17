<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class RealisasiServices
{

    public function searchReal(string $keyword)
    {
        $data = DB::connection('prod')->select(
            "SELECT
                A.*,
                B.NO_SPK,
                C.TIPE_CONT,
                E.ID_JOB_SLIP
            FROM t_spk_cont A
            INNER JOIN t_spk B
                ON A.ID = B.ID
            INNER JOIN t_request_cont C
                ON A.NO_CONT = C.NO_CONT
            INNER JOIN t_op_behandlein D
                ON A.NO_CONT = D.NO_CONT
            INNER JOIN t_job_slip E
                ON D.NO_SPK = E.NO_SPK
            WHERE A.NO_CONT LIKE UPPER(?)
              AND A.STATUS_CONT IN ('460')
            ORDER BY A.ID DESC
            LIMIT 1",
            [
                '%' . $keyword . '%'
            ]
        );

        return $data;
    }


    public function getStatus(string $no_cont)
    {
        $data = DB::connection('prod')->selectOne(
            "SELECT *
             FROM t_op_inspection
             WHERE NO_CONT = ?
               AND STATUS = 'WAITING'",
            [
                $no_cont
            ]
        );

        return $data;
    }


    public function getAlat()
    {
        $data = DB::connection('prod')->select(
            "SELECT *
             FROM t_reff_alat"
        );

        return $data;
    }

    public function getOperator()
    {
        $data = DB::connection('prod')->select(
            "SELECT *
             FROM reff_user
             WHERE KD_GROUP LIKE '%OPR%'"
        );

        return $data;
    }


    public function getCondition()
    {
        $data = DB::connection('prod')->select(
            "SELECT
                ID,
                KONDISI
             FROM reff_kondisi"
        );

        return $data;
    }


    public function getJoin(
        string $no_cont,
        string $no_dok,
        $tgl_dok
    ) {
        $data = DB::connection('prod')->selectOne(
            "SELECT
                A.LNSW_NOAJU
             FROM t_ppk_hdr A
             JOIN t_ppk_cont B
                ON A.ID_IJIN = B.ID_IJIN
             WHERE A.LNSW_KD_RESPON = '005'
               AND B.NO_CONT = ?
               AND A.NO_RESPON = ?
               AND A.TG_RESPON = ?",
            [
                $no_cont,
                $no_dok,
                $tgl_dok,
            ]
        );

        return $data;
    }
}