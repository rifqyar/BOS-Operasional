<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class HoldService
{
    /**
     * Legacy:
     * search_holdd($keyword)
     *
     * Mencari container yang belum HOLD.
     */
    public function searchHold(string $keyword): Collection
    {
        return collect(
            DB::connection('prod')->select(
                "SELECT
                    d.ID,
                    d.NO_SPK,
                    c.NO_CONT,
                    d.TGL_DOK,
                    d.NO_DOK,
                    a.NAMA AS JNS_DOK,
                    CASE
                        WHEN c.FL_HOLD = 'Y' THEN 'HOLD'
                    END AS KETERANGAN
                FROM t_spk d
                JOIN t_spk_cont c
                    ON c.ID = d.ID
                LEFT JOIN reff_kode_dok_bc a
                    ON a.ID = d.JNS_DOK
                WHERE c.NO_CONT LIKE ?
                  AND c.FL_HOLD = 'N'
                  AND c.STATUS_CONT != 900",
                ['%' . $keyword . '%']
            )
        );
    }

    /**
     * Legacy:
     * search_realease($keyword)
     *
     * Mencari container yang sudah HOLD.
     */
    public function searchRelease(string $keyword): Collection
    {
        return collect(
            DB::connection('prod')->select(
                "SELECT
                    d.ID,
                    d.NO_SPK,
                    c.NO_CONT,
                    d.TGL_DOK,
                    d.NO_DOK,
                    a.NAMA AS JNS_DOK,
                    CASE
                        WHEN c.FL_HOLD = 'Y' THEN 'HOLD'
                    END AS KETERANGAN,
                    CASE
                        WHEN c.FL_WARNA_HOLD = 'N' THEN 'PUTIH'
                        WHEN c.FL_WARNA_HOLD = 'M' THEN 'MERAH'
                        WHEN c.FL_WARNA_HOLD = 'T' THEN 'TIMAH'
                    END AS WARNA
                FROM t_spk d
                JOIN t_spk_cont c
                    ON c.ID = d.ID
                LEFT JOIN reff_kode_dok_bc a
                    ON a.ID = d.JNS_DOK
                WHERE c.NO_CONT LIKE ?
                  AND c.FL_HOLD = 'Y'
                  AND c.STATUS_CONT != 900",
                ['%' . $keyword . '%']
            )
        );
    }

    /**
     * Legacy:
     * realese()
     *
     * Mengambil seluruh container yang sedang HOLD.
     */
    public function getHeldContainers(): Collection
    {
        return collect(
            DB::connection('prod')->select(
                "SELECT
                    d.ID,
                    d.NO_SPK,
                    c.NO_CONT,
                    d.TGL_DOK,
                    d.NO_DOK,
                    a.NAMA AS JNS_DOK,
                    CASE
                        WHEN c.FL_HOLD = 'Y' THEN 'HOLD'
                    END AS KETERANGAN,
                    CASE
                        WHEN c.FL_WARNA_HOLD = 'N' THEN 'PUTIH'
                        WHEN c.FL_WARNA_HOLD = 'M' THEN 'MERAH'
                        WHEN c.FL_WARNA_HOLD = 'T' THEN 'TIMAH'
                    END AS WARNA
                FROM t_spk d
                JOIN t_spk_cont c
                    ON c.ID = d.ID
                LEFT JOIN reff_kode_dok_bc a
                    ON a.ID = d.JNS_DOK
                WHERE c.FL_HOLD = 'Y'
                  AND c.STATUS_CONT != 900"
            )
        );
    }

    /**
     * Development phase:
     * belum melakukan UPDATE.
     *
     * Legacy sebenarnya:
     *
     * UPDATE t_spk_cont
     * SET FL_HOLD='Y',
     *     FL_WARNA_HOLD='$WARNA'
     * WHERE ID='$ID'
     * AND NO_CONT='$NO_CONT'
     */
    public function hold(
        string|int $id,
        string $noCont,
        string $warna
    ): void {
        // Read-only development phase.
    }

    /**
     * Development phase:
     * belum melakukan UPDATE.
     *
     * Legacy sebenarnya:
     *
     * UPDATE t_spk_cont
     * SET FL_HOLD='N',
     *     FL_WARNA_HOLD='N'
     * WHERE ID='$ID'
     * AND NO_CONT='$NO_CONT'
     */
    public function release(
        string|int $id,
        string $noCont,
        ?string $noSpk = null
    ): void {
        // Read-only development phase.
    }
}