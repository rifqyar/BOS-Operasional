<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class HoldService
{
    /**
     * Query dasar menggunakan pola HoldService lama.
     * Sumber data: MySQL prod.
     */
    private function baseQuery()
    {
        return DB::connection('prod')
            ->table('t_spk as d')
            ->join('t_spk_cont as c', 'c.ID', '=', 'd.ID')
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
                'c.STATUS_CONT',
                'c.FL_HOLD',
                'c.FL_WARNA_HOLD',
            ])
            ->selectRaw("
                CASE
                    WHEN c.FL_HOLD = 'Y' THEN 'HOLD'
                    ELSE ''
                END AS KETERANGAN
            ")
            ->selectRaw("
                CASE c.FL_WARNA_HOLD
                    WHEN 'N' THEN 'PUTIH'
                    WHEN 'M' THEN 'MERAH'
                    WHEN 'T' THEN 'TIMAH'
                    ELSE '-'
                END AS WARNA
            ");
    }

    /**
     * Mencari container yang tersedia untuk Hold.
     */
    public function searchAvailable(string $noContainer): Collection
    {
        return $this->baseQuery()
            ->where('c.NO_CONT', 'like', '%' . trim($noContainer) . '%')
            ->where('c.STATUS_CONT', '<>', 900)
            ->where(function ($query) {
                $query->whereNull('c.FL_HOLD')
                    ->orWhere('c.FL_HOLD', '<>', 'Y');
            })
            ->orderBy('d.NO_SPK')
            ->limit(50)
            ->get();
    }

    /**
     * Mengambil detail container.
     */
    public function getDetail(int $id): ?object
    {
        return $this->baseQuery()
            ->where('c.ID', $id)
            ->where('c.STATUS_CONT', '<>', 900)
            ->first();
    }

    /**
     * Mengambil daftar container Hold.
     * Pagination mengikuti versi lama: 10 data per halaman.
     */
    public function getHeldContainers(): LengthAwarePaginator
    {
        return $this->baseQuery()
            ->where('c.FL_HOLD', 'Y')
            ->where('c.STATUS_CONT', '<>', 900)
            ->orderBy('d.ID', 'desc')
            ->paginate(10);
    }

    /**
     * Memvalidasi container sebelum Hold.
     */
    public function validateAvailable(int $id): object
    {
        $item = $this->getDetail($id);

        if (!$item) {
            throw new RuntimeException(
                'Data container tidak ditemukan.'
            );
        }

        if (($item->FL_HOLD ?? 'N') === 'Y') {
            throw new RuntimeException(
                'Container sudah dalam status Hold.'
            );
        }

        return $item;
    }

    /**
     * Menyimpan Hold ke MySQL dan Oracle.
     */
    public function hold(int $id, string $warna, string $user): object
    {
        $warna = strtoupper(trim($warna));

        if (!in_array($warna, ['N', 'M', 'T'], true)) {
            throw ValidationException::withMessages([
                'warna' => 'Warna Hold harus Putih, Merah, atau Timah.',
            ]);
        }

        $item = $this->validateAvailable($id);
        $now = now();

        // Update status Hold pada MySQL.
        $updated = DB::connection('prod')
            ->table('t_spk_cont')
            ->where('ID', $item->ID)
            ->where('NO_CONT', $item->NO_CONT)
            ->where(function ($query) {
                $query->whereNull('FL_HOLD')
                    ->orWhere('FL_HOLD', '<>', 'Y');
            })
            ->update([
                'FL_HOLD' => 'Y',
                'FL_WARNA_HOLD' => $warna,
            ]);

        if ($updated < 1) {
            throw new RuntimeException(
                'Container gagal di-Hold atau sudah berstatus Hold.'
            );
        }

        // Insert catatan Hold ke Oracle.
        DB::connection('prod_new')
            ->table('T_OP_HOLD')
            ->insert([
                'NO_SPK' => $item->NO_SPK,
                'W_HOLD' => $now,
                'USER_LOGIN' => $user,
                'OPERATOR' => $user,
            ]);

        // Update data operasional pada Oracle.
        DB::connection('prod_new')
            ->table('T_OPERATION')
            ->where('NO_SPK', $item->NO_SPK)
            ->where('NO_CONT', $item->NO_CONT)
            ->update([
                'WK_HOLD' => $now,
            ]);

        return $item;
    }

    /**
     * Release container pada MySQL.
     */
    public function release(int $id): object
    {
        $item = $this->baseQuery()
            ->where('c.ID', $id)
            ->where('c.FL_HOLD', 'Y')
            ->where('c.STATUS_CONT', '<>', 900)
            ->first();

        if (!$item) {
            throw new RuntimeException(
                'Container tidak ditemukan atau sudah Release.'
            );
        }

        $updated = DB::connection('prod')
            ->table('t_spk_cont')
            ->where('ID', $item->ID)
            ->where('NO_CONT', $item->NO_CONT)
            ->where('FL_HOLD', 'Y')
            ->update([
                'FL_HOLD' => 'N',
                'FL_WARNA_HOLD' => 'N',
            ]);

        if ($updated < 1) {
            throw new RuntimeException(
                'Container gagal di-Release atau sudah tidak berstatus Hold.'
            );
        }

        return $item;
    }
}