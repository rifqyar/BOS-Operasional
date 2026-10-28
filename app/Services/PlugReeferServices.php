<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class PlugReeferServices
{
    /**
     * Mencari container yang memenuhi syarat Plug Reefer.
     *
     * Mengacu pada legacy:
     * M_operation->searchreefer($keyword)
     */
    public function searchReefer(string $keyword): array
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
            ['%' . $keyword . '%']
        );
    }

    /**
     * Memeriksa aktivitas Plug yang belum Unplug.
     *
     * Mengacu pada legacy:
     * M_operation->checked($check)
     */
    public function checked(string $noCont): array
    {
        return DB::connection('prod')->select(
            "SELECT *
            FROM t_op_reefer
            WHERE NO_CONT = ?
                AND WAKTU IS NOT NULL
                AND FL_UNPLUG = 'N'
            ORDER BY ID ASC
            LIMIT 1",
            [$noCont]
        );
    }

    /**
     * Mengambil temperatur monitor terakhir.
     *
     * Mengacu pada legacy:
     * M_operation->temp_akhir($check)
     */
    public function tempAkhir(string $noCont): array
    {
        return DB::connection('prod')->select(
            "SELECT *
            FROM t_op_reefer
            WHERE NO_CONT = ?
                AND TEMPERATURE_MONITOR IS NOT NULL
                AND FL_MONITOR = 'Y'
            ORDER BY ID DESC
            LIMIT 1",
            [$noCont]
        );
    }

    /**
     * Mengambil data SPK dan container terbaru.
     *
     * Mengacu pada query $SQL_SPK_CONT
     * di legacy set_plug().
     */
    public function getSpkContainer(string $noCont): ?object
    {
        return DB::connection('prod')->selectOne(
            "SELECT A.*, B.*
            FROM t_spk_cont A
            INNER JOIN t_spk B
                ON A.ID = B.ID
            WHERE A.NO_CONT = ?
            ORDER BY A.ID DESC
            LIMIT 1",
            [$noCont]
        );
    }

    /**
     * Menentukan kondisi container.
     *
     * 1 = Belum Plug
     * 2 = Sedang Plug
     * 0 = Kondisi lainnya
     */
    public function determineCondition(string $noCont): int
    {
        $checked = $this->checked($noCont);

        if (empty($checked)) {
            return 1;
        }

        $activePlug = $checked[0];

        if (
            !empty($activePlug->WAKTU)
            && ($activePlug->FL_PLUG ?? null) === 'Y'
            && ($activePlug->FL_UNPLUG ?? null) === 'N'
        ) {
            return 2;
        }

        return 0;
    }
}