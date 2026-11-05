<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class HoldService
{
    /**
     * Legacy: search_holdd($keyword)
     *
     * Read-only query menggunakan koneksi prod.
     */
    public function searchHold(string $keyword)
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
     * Legacy: search_realease($keyword)
     *
     * Read-only query menggunakan koneksi prod.
     */
    public function searchRelease(string $keyword)
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
     * Legacy: realese()
     *
     * Daftar container HOLD dengan pagination.
     * Seluruh pembacaan menggunakan prod.
     */
    public function getHeldContainers(int $perPage = 10)
    {
        $perPage = min(max($perPage, 1), 50);

        return DB::connection('prod')
            ->table('t_spk as d')
            ->join(
                't_spk_cont as c',
                'c.ID',
                '=',
                'd.ID'
            )
            ->leftJoin(
                'reff_kode_dok_bc as a',
                'a.ID',
                '=',
                'd.JNS_DOK'
            )
            ->select([
                'd.ID',
                'd.NO_SPK',
                'c.NO_CONT',
                'd.TGL_DOK',
                'd.NO_DOK',
                'a.NAMA as JNS_DOK',
            ])
            ->selectRaw("
                CASE
                    WHEN c.FL_HOLD = 'Y' THEN 'HOLD'
                END AS KETERANGAN
            ")
            ->selectRaw("
                CASE
                    WHEN c.FL_WARNA_HOLD = 'N' THEN 'PUTIH'
                    WHEN c.FL_WARNA_HOLD = 'M' THEN 'MERAH'
                    WHEN c.FL_WARNA_HOLD = 'T' THEN 'TIMAH'
                END AS WARNA
            ")
            ->where('c.FL_HOLD', 'Y')
            ->where('c.STATUS_CONT', '!=', 900)
            ->orderByDesc('d.ID')
            ->paginate($perPage);
    }

    /**
     * HOLD container.
     *
     * Validasi dan update dilakukan pada prod (MySQL), mengikuti legacy.
     *
     * Legacy update:
     * t_spk_cont.FL_HOLD = 'Y'
     * t_spk_cont.FL_WARNA_HOLD = warna
     */
    public function hold(
        string|int $id,
        string $noCont,
        string $warna
    ): void {
        $noCont = strtoupper(trim($noCont));
        $warna = strtoupper(trim($warna));

        if (!in_array($warna, ['N', 'M', 'T'], true)) {
            throw new Exception('Warna HOLD tidak valid.', 422);
        }

        /*
         * Validasi/read dari MySQL prod.
         */
        $legacyRow = DB::connection('prod')
            ->table('t_spk_cont')
            ->select([
                'ID',
                'NO_CONT',
                'FL_HOLD',
                'STATUS_CONT',
            ])
            ->where('ID', $id)
            ->where('NO_CONT', $noCont)
            ->first();

        if (!$legacyRow) {
            throw new Exception(
                'Data container tidak ditemukan pada database legacy.',
                404
            );
        }

        if ((string) $legacyRow->FL_HOLD !== 'N') {
            throw new Exception(
                'Container sudah dalam status HOLD atau tidak dapat diproses.',
                422
            );
        }

        if ((int) $legacyRow->STATUS_CONT === 900) {
            throw new Exception(
                'Container dengan status 900 tidak dapat diproses HOLD.',
                422
            );
        }

        /*
         * Update ke MySQL prod, sesuai proses legacy.
         */
        $connection = DB::connection('prod');

        $connection->beginTransaction();

        try {
            $updated = $connection
                ->table('t_spk_cont')
                ->where('ID', $id)
                ->where('NO_CONT', $noCont)
                ->update([
                    'FL_HOLD' => 'Y',
                    'FL_WARNA_HOLD' => $warna,
                ]);

            if ($updated === 0) {
                throw new Exception(
                    'Update HOLD tidak berhasil pada database baru.',
                    500
                );
            }

            $connection->commit();
        } catch (Throwable $e) {
            if ($connection->transactionLevel() > 0) {
                $connection->rollBack();
            }

            throw $e;
        }
    }

    /**
     * RELEASE container.
     *
     * Validasi dan seluruh update dilakukan pada prod (MySQL),
     * mengikuti proses legacy.
     *
     * Legacy:
     * 1. Update t_spk_cont:
     *    FL_HOLD = 'N'
     *    FL_WARNA_HOLD = 'N'
     *
     * 2. Update t_atensi_p2:
     *    STATUS_HOLD = 'N'
     *    RELEASE_AT = waktu sekarang
     */
    public function release(
        string|int $id,
        string $noCont,
        ?string $noSpk = null
    ): void {
        $noCont = strtoupper(trim($noCont));

        /*
         * Validasi/read container dari prod.
         * Ambil NO_SPK dari legacy jika tidak dikirim frontend.
         */
        $legacyRow = DB::connection('prod')
            ->table('t_spk as d')
            ->join(
                't_spk_cont as c',
                'c.ID',
                '=',
                'd.ID'
            )
            ->select([
                'd.ID',
                'd.NO_SPK',
                'c.NO_CONT',
                'c.FL_HOLD',
                'c.STATUS_CONT',
            ])
            ->where('d.ID', $id)
            ->where('c.NO_CONT', $noCont)
            ->first();

        if (!$legacyRow) {
            throw new Exception(
                'Data container tidak ditemukan pada database legacy.',
                404
            );
        }

        if ((string) $legacyRow->FL_HOLD !== 'Y') {
            throw new Exception(
                'Container tidak sedang dalam status HOLD.',
                422
            );
        }

        if ((int) $legacyRow->STATUS_CONT === 900) {
            throw new Exception(
                'Container dengan status 900 tidak dapat diproses RELEASE.',
                422
            );
        }

        $noSpk = $noSpk ?: $legacyRow->NO_SPK;

        /*
         * Update ke MySQL prod, sesuai proses legacy.
         */
        $connection = DB::connection('prod');

        $connection->beginTransaction();

        try {
            /*
             * 1. Release container pada t_spk_cont.
             */
            $updated = $connection
                ->table('t_spk_cont')
                ->where('ID', $id)
                ->where('NO_CONT', $noCont)
                ->update([
                    'FL_HOLD' => 'N',
                    'FL_WARNA_HOLD' => 'N',
                ]);

            if ($updated === 0) {
                throw new Exception(
                    'Update RELEASE container tidak berhasil pada database baru.',
                    500
                );
            }

            /*
             * 2. Update atensi P2.
             *
             * Legacy tidak menggagalkan proses jika tidak ada
             * baris atensi yang cocok, jadi row count tidak
             * dijadikan syarat sukses.
             */
            $connection
                ->table('t_atensi_p2')
                ->where('NO_SPK', $noSpk)
                ->where('NO_CONT', $noCont)
                ->update([
                    'STATUS_HOLD' => 'N',
                    'RELEASE_AT' => now(),
                ]);

            $connection->commit();
        } catch (Throwable $e) {
            if ($connection->transactionLevel() > 0) {
                $connection->rollBack();
            }

            throw $e;
        }
    }
}
