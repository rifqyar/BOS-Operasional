<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class HoldService
{
    protected $db;

    public function __construct()
    {
        $this->db = DB::connection('prod');
    }

    public function searchHold(string $keyword): Collection
    {
        $keyword = strtoupper(trim($keyword));

        return $this->db
            ->table('t_spk as d')
            ->join('t_spk_cont as c', 'c.ID', '=', 'd.ID')
            ->leftJoin(
                'reff_kode_dok_bc as a',
                'a.ID',
                '=',
                'd.JNS_DOK'
            )
            ->where('c.NO_CONT', 'like', "%{$keyword}%")
            ->where('c.FL_HOLD', 'N')
            ->where('c.STATUS_CONT', '!=', '900')
            ->select([
                'd.ID',
                'd.NO_SPK',
                'd.TGL_DOK',
                'd.NO_DOK',
                'd.JNS_DOK',
                'a.NAMA as JNS_DOK_NAMA',
                'c.NO_CONT',
                'c.STATUS_CONT',
                'c.FL_HOLD',
                'c.FL_WARNA_HOLD',
            ])
            ->orderBy('c.NO_CONT')
            ->get();
    }

    public function searchRelease(string $keyword): Collection
    {
        $keyword = strtoupper(trim($keyword));

        return $this->db
            ->table('t_spk as d')
            ->join('t_spk_cont as c', 'c.ID', '=', 'd.ID')
            ->leftJoin(
                'reff_kode_dok_bc as a',
                'a.ID',
                '=',
                'd.JNS_DOK'
            )
            ->where('c.NO_CONT', 'like', "%{$keyword}%")
            ->where('c.FL_HOLD', 'Y')
            ->where('c.STATUS_CONT', '!=', '900')
            ->select([
                'd.ID',
                'd.NO_SPK',
                'd.TGL_DOK',
                'd.NO_DOK',
                'd.JNS_DOK',
                'a.NAMA as JNS_DOK_NAMA',
                'c.NO_CONT',
                'c.STATUS_CONT',
                'c.FL_HOLD',
                'c.FL_WARNA_HOLD',
            ])
            ->orderBy('c.NO_CONT')
            ->get();
    }

    public function getHeldContainers(): Collection
    {
        return $this->db
            ->table('t_spk as d')
            ->join('t_spk_cont as c', 'c.ID', '=', 'd.ID')
            ->leftJoin(
                'reff_kode_dok_bc as a',
                'a.ID',
                '=',
                'd.JNS_DOK'
            )
            ->where('c.FL_HOLD', 'Y')
            ->where('c.STATUS_CONT', '!=', '900')
            ->select([
                'd.ID',
                'd.NO_SPK',
                'd.TGL_DOK',
                'd.NO_DOK',
                'd.JNS_DOK',
                'a.NAMA as JNS_DOK_NAMA',
                'c.NO_CONT',
                'c.STATUS_CONT',
                'c.FL_HOLD',
                'c.FL_WARNA_HOLD',
            ])
            ->orderByDesc('c.ID')
            ->get();
    }

    public function search(string $keyword): array
    {
        $keyword = strtoupper(trim($keyword));

        $hold = $this->searchHold($keyword);

        if ($hold->isNotEmpty()) {
            return [
                'status' => 2,
                'keyword' => $keyword,
                'data' => $hold,
            ];
        }

        $release = $this->searchRelease($keyword);

        if ($release->isNotEmpty()) {
            return [
                'status' => 3,
                'keyword' => $keyword,
                'data' => $release,
            ];
        }

        return [
            'status' => 0,
            'keyword' => $keyword,
            'data' => collect(),
        ];
    }

    public function hold(
        int|string $id,
        string $noCont,
        string $warna
    ): void {
        $noCont = strtoupper(trim($noCont));
        $warna = strtoupper(trim($warna));

        if (!in_array($warna, ['N', 'M', 'T'], true)) {
            throw new RuntimeException('Kode warna HOLD tidak valid.');
        }

        $this->db->transaction(function () use (
            $id,
            $noCont,
            $warna
        ) {
            $container = $this->db
                ->table('t_spk_cont')
                ->where('ID', $id)
                ->where('NO_CONT', $noCont)
                ->first();

            if (!$container) {
                throw new RuntimeException(
                    'Data container tidak ditemukan.'
                );
            }

            if ((string) $container->STATUS_CONT === '900') {
                throw new RuntimeException(
                    'Container dengan status 900 tidak dapat di-HOLD.'
                );
            }

            if ((string) $container->FL_HOLD === 'Y') {
                throw new RuntimeException(
                    'Container sudah dalam kondisi HOLD.'
                );
            }

            $updated = $this->db
                ->table('t_spk_cont')
                ->where('ID', $id)
                ->where('NO_CONT', $noCont)
                ->update([
                    'FL_HOLD' => 'Y',
                    'FL_WARNA_HOLD' => $warna,
                ]);

            if ($updated === 0) {
                throw new RuntimeException(
                    'Container gagal di-HOLD.'
                );
            }
        });
    }

    public function release(
        int|string $id,
        string $noCont,
        ?string $noSpk = null
    ): void {
        $noCont = strtoupper(trim($noCont));
        $noSpk = $noSpk !== null
            ? strtoupper(trim($noSpk))
            : null;

        $this->db->transaction(function () use (
            $id,
            $noCont,
            $noSpk
        ) {
            $container = $this->db
                ->table('t_spk_cont')
                ->where('ID', $id)
                ->where('NO_CONT', $noCont)
                ->first();

            if (!$container) {
                throw new RuntimeException(
                    'Data container tidak ditemukan.'
                );
            }

            if ((string) $container->STATUS_CONT === '900') {
                throw new RuntimeException(
                    'Container dengan status 900 tidak dapat di-release.'
                );
            }

            if ((string) $container->FL_HOLD !== 'Y') {
                throw new RuntimeException(
                    'Container tidak sedang HOLD.'
                );
            }

            $updated = $this->db
                ->table('t_spk_cont')
                ->where('ID', $id)
                ->where('NO_CONT', $noCont)
                ->update([
                    'FL_HOLD' => 'N',
                    'FL_WARNA_HOLD' => 'N',
                ]);

            if ($updated === 0) {
                throw new RuntimeException(
                    'Container gagal di-RELEASE.'
                );
            }

            if ($noSpk !== null && $noSpk !== '') {
                $this->db
                    ->table('t_atensi_p2')
                    ->where('NO_SPK', $noSpk)
                    ->where('NO_CONT', $noCont)
                    ->update([
                        'STATUS_HOLD' => 'N',
                        'RELEASE_AT' => now(),
                    ]);
            }
        });
    }
}