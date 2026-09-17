<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class PlugReeferServices
{

    public function searchReefer(string $keyword)
    {
        return DB::connection('prod')->select(
            "SELECT
                B.NO_CONT,
                D.TEMP_CUST AS SUHU_CUST,
                D.TEMP_TERMINAL AS SUHU_TERMINAL
            FROM t_spk A
            INNER JOIN t_spk_cont B
                ON A.ID = B.ID
            INNER JOIN t_request C
                ON A.NO_DOK = C.NO_DOK
            INNER JOIN t_request_cont D
                ON B.NO_CONT = D.NO_CONT
                AND C.ID = D.ID
            LEFT JOIN t_op_reefer E
                ON B.NO_CONT = E.NO_CONT
                AND A.NO_SPK = E.NO_SPK
            WHERE D.TEMP_CUST IS NOT NULL
              AND B.NO_CONT LIKE ?
              AND B.STATUS_CONT != '900'
            ORDER BY A.ID DESC
            LIMIT 1",
            [
                '%' . $keyword . '%'
            ]
        );
    }


    public function checked(string $noCont)
    {
        return DB::connection('prod')->select(
            "SELECT *
             FROM t_op_reefer
             WHERE NO_CONT = ?
               AND WAKTU IS NOT NULL
               AND FL_UNPLUG = 'N'
             ORDER BY ID ASC
             LIMIT 1",
            [
                $noCont
            ]
        );
    }


    public function tempAkhir(string $noCont)
    {
        return DB::connection('prod')->select(
            "SELECT *
             FROM t_op_reefer
             WHERE NO_CONT = ?
               AND TEMPERATURE_MONITOR IS NOT NULL
               AND FL_MONITOR = 'Y'
             ORDER BY ID DESC
             LIMIT 1",
            [
                $noCont
            ]
        );
    }


    public function getSpkContainer(string $noCont)
    {
        return DB::connection('prod')->selectOne(
            "SELECT
                A.*,
                B.*
             FROM t_spk_cont A
             INNER JOIN t_spk B
                ON A.ID = B.ID
             WHERE A.NO_CONT = ?
             ORDER BY A.ID DESC
             LIMIT 1",
            [
                $noCont
            ]
        );
    }


    public function determineCondition(string $noCont): int
    {
        $checked = $this->checked($noCont);

        if (empty($checked)) {
            return 1;
        }

        $start = $checked[0];

        if (
            !empty($start->WAKTU)
            && ($start->FL_PLUG ?? null) === 'Y'
            && ($start->FL_UNPLUG ?? null) === 'N'
        ) {
            return 2;
        }

        return 0;
    }
}