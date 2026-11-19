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
        return $this->normalizeRows(DB::connection('prod_new')->select(
            "SELECT NO_CONT, SUHU_CUST, SUHU_TERMINAL
             FROM (
                SELECT
                    B.NO_CONT,
                    D.TEMP_CUST AS SUHU_CUST,
                    D.TEMP_TERMINAL AS SUHU_TERMINAL,
                    ROW_NUMBER() OVER (
                        PARTITION BY B.NO_CONT
                        ORDER BY A.ID DESC, C.ID DESC
                    ) AS RN
                FROM T_SPK A
                INNER JOIN T_SPK_CONT B
                    ON A.ID = B.ID
                INNER JOIN T_REQUEST C
                    ON A.NO_DOK = C.NO_DOK
                    AND A.TGL_DOK = C.TGL_DOK
                INNER JOIN T_REQUEST_CONT D
                    ON C.ID = D.ID
                    AND B.NO_CONT = D.NO_CONT
                WHERE D.TEMP_CUST IS NOT NULL
                    AND UPPER(B.NO_CONT) LIKE ?
                    AND B.STATUS_CONT <> '900'
             ) REEFER
             WHERE RN = 1
             ORDER BY NO_CONT",
            ['%' . strtoupper(trim($keyword)) . '%']
        ));
    }

    /**
     * Memeriksa aktivitas Plug yang belum Unplug.
     *
     * Mengacu pada legacy:
     * M_operation->checked($check)
     */
    public function checked(string $noCont): array
    {
        return $this->normalizeRows(DB::connection('prod_new')->select(
            "SELECT *
            FROM T_OP_REEFER
            WHERE NO_CONT = ?
                AND WAKTU IS NOT NULL
                AND FL_UNPLUG = 'N'
            ORDER BY ID DESC
            FETCH FIRST 1 ROWS ONLY",
            [$noCont]
        ));
    }

    /**
     * Mengambil temperatur monitor terakhir.
     *
     * Mengacu pada legacy:
     * M_operation->temp_akhir($check)
     */
    public function tempAkhir(string $noCont, ?int $activePlugId = null): array
    {
        if ($activePlugId === null) {
            return [];
        }

        return $this->normalizeRows(DB::connection('prod_new')->select(
            "SELECT *
            FROM T_OP_REEFER
            WHERE NO_CONT = ?
                AND TEMPERATURE_MONITOR IS NOT NULL
                AND FL_MONITOR = 'Y'
                AND ID >= ?
            ORDER BY ID DESC
            FETCH FIRST 1 ROWS ONLY",
            [$noCont, $activePlugId]
        ));
    }

    /**
     * Mengambil data SPK dan container terbaru.
     *
     * Mengacu pada query $SQL_SPK_CONT
     * di legacy set_plug().
     */
    public function getSpkContainer(string $noCont, ?string $noSpk = null): ?object
    {
        $sql = "SELECT
                A.NO_CONT,
                A.STATUS_CONT,
                A.ID AS ID_SPK_CONT,
                B.NO_SPK,
                B.NO_DOK,
                B.TGL_DOK
            FROM T_SPK_CONT A
            INNER JOIN T_SPK B
                ON A.ID = B.ID
            INNER JOIN T_REQUEST C
                ON B.NO_DOK = C.NO_DOK
                AND B.TGL_DOK = C.TGL_DOK
            INNER JOIN T_REQUEST_CONT D
                ON C.ID = D.ID
                AND A.NO_CONT = D.NO_CONT
            WHERE A.NO_CONT = ?
                AND A.STATUS_CONT <> '900'
                AND D.TEMP_CUST IS NOT NULL";
        $bindings = [$noCont];

        if ($noSpk !== null && trim($noSpk) !== '') {
            $sql .= ' AND B.NO_SPK = ?';
            $bindings[] = trim($noSpk);
        }

        $sql .= ' ORDER BY A.ID DESC FETCH FIRST 1 ROWS ONLY';

        return $this->normalizeRow(
            DB::connection('prod_new')->selectOne($sql, $bindings)
        );
    }

    /**
     * Menentukan kondisi container.
     *
     * 1 = Belum Plug
     * 2 = Sedang Plug
     * 0 = Kondisi lainnya
     */
    public function determineCondition(string $noCont, ?array $checked = null): int
    {
        $checked ??= $this->checked($noCont);

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

    private function normalizeRows(array $rows): array
    {
        return array_map(
            fn ($row) => $this->normalizeRow($row),
            $rows
        );
    }

    private function normalizeRow(?object $row): ?object
    {
        if ($row === null) {
            return null;
        }

        $normalized = new \stdClass;
        foreach ((array) $row as $column => $value) {
            $normalized->{strtoupper($column)} = $value;
        }

        return $normalized;
    }
}
