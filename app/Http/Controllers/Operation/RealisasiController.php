<?php

namespace App\Http\Controllers\Operation;

use App\Http\Controllers\Controller;
use App\Services\RealisasiServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class RealisasiController extends Controller
{
    public function __construct(
        protected RealisasiServices $realisasiServices
    ) {
    }

    /**
     * Search Container
     *
     * Legacy:
     * search_realis()
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'no_cont' => [
                    'required',
                    'string',
                    'max:20',
                ],
            ]);

            $noCont = trim($validated['no_cont']);

            $rows = $this->realisasiServices
                ->searchReal($noCont);

            /*
             * Legacy:
             * if (COUNT($re) == 0)
             */
            if (count($rows) === 0) {
                return response()->json([
                    'status' => 0,
                    'kode' => 1,
                    'nilai' => $noCont,
                    'message' => "Container {$noCont} tidak ditemukan.",
                ], 404);
            }

            $html = view(
                'livewire.partials.realisasi.table',
                [
                    'rows' => $rows,
                ]
            )->render();

            return response()->json([
                'status' => 2,
                'data' => $html,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage()
                    ?: 'Gagal mengambil data container.',
            ], 500);
        }
    }

    /**
     * Detail Pemeriksaan
     *
     * Legacy:
     * search_realisasi()
     */
    public function detail(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'no_cont' => [
                    'required',
                    'string',
                    'max:20',
                ],
            ]);

            $noCont = trim($validated['no_cont']);

            /*
             * Legacy:
             * $re = $this->M_operation->searchreal($keyword);
             */
            $rows = $this->realisasiServices
                ->searchReal($noCont);

            if (count($rows) === 0) {
                return response()->json([
                    'status' => 0,
                    'message' => "Container {$noCont} tidak ditemukan.",
                ], 404);
            }

            /*
             * searchreal() menggunakan LIMIT 1,
             * sehingga row pertama adalah data yang dipakai.
             */
            $row = $rows[0];

            /*
             * Legacy:
             * $start = $this->M_operation->statusnya($keyword);
             */
            $realisasi = $this->realisasiServices
                ->getStatus($noCont);

            /*
             * Legacy:
             *
             * START_INSP == NULL
             *      kond = 0
             *
             * FINISH_INSP == NULL
             *      kond = 1
             *
             * ELSE
             *      kond = 2
             */
            if (!$realisasi) {
                $kond = 0;
            } elseif ($realisasi->START_INSP === null) {
                $kond = 0;
            } elseif ($realisasi->FINISH_INSP === null) {
                $kond = 1;
            } else {
                $kond = 2;
            }

            /*
             * Legacy:
             * JOIN hanya ketika FINISH_INSP belum ada.
             */
            $join = null;

            if ($kond === 1 && $realisasi) {
                $noDok = $realisasi->NO_DOK ?? null;
                $tglDok = $realisasi->TGL_DOK ?? null;

                if ($noDok && $tglDok) {
                    $join = $this->realisasiServices->getJoin(
                        $noCont,
                        $noDok,
                        $tglDok
                    );
                }
            }

            /*
             * Dropdown legacy.
             */
            $equipments = $this->realisasiServices
                ->getAlat();

            $operators = $this->realisasiServices
                ->getOperator();

            $conditions = $this->realisasiServices
                ->getCondition();

            /*
             * Render Blade.
             */
            $html = view(
                'livewire.partials.realisasi.form',
                [
                    'row' => $row,
                    'realisasi' => $realisasi,
                    'kond' => $kond,
                    'join' => $join,
                    'equipments' => $equipments,
                    'operators' => $operators,
                    'conditions' => $conditions,
                ]
            )->render();

            return response()->json([
                'status' => 1,
                'kond' => $kond,
                'data' => $html,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage()
                    ?: 'Gagal mengambil detail pemeriksaan.',
            ], 500);
        }
    }

    /**
     * Store
     *
     * Untuk fase development:
     * READ ONLY.
     *
     * Tidak menjalankan set_pemeriksaan()
     * dari legacy.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'nomerkon' => [
                    'required',
                    'string',
                    'max:20',
                ],

                'noseal' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                'idJobSlip' => [
                    'nullable',
                ],

                'nospk' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                'tipecont' => [
                    'nullable',
                    'string',
                    'max:50',
                ],

                'alat' => [
                    'nullable',
                ],

                'operator' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                'join' => [
                    'nullable',
                ],
            ]);

            return response()->json([
                'status' => 1,
                'message' => 'Data berhasil divalidasi.',
                'data' => $validated,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage()
                    ?: 'Data gagal diproses.',
            ], 422);
        }
    }
}