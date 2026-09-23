<?php

namespace App\Http\Controllers\Operation;

use App\Http\Controllers\Controller;
use App\Services\HoldService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Throwable;

class HoldController extends Controller
{
    public function __construct(
        protected HoldService $holdService
    ) {
    }

    /**
     * Search NO CONTAINER.
     *
     * Alur:
     *
     * SEARCH
     *   ↓
     * searchHold()
     *   ↓
     * ditemukan
     *   ↓
     * langsung FORM HOLD
     *
     * Jika tidak ditemukan:
     *
     * searchRelease()
     *   ↓
     * ditemukan
     *   ↓
     * langsung FORM RELEASE
     *
     * Jika tidak ditemukan:
     *
     * NOT FOUND
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search_cont' => [
                'required',
                'string',
                'max:50',
            ],
        ]);

        $keyword = strtoupper(
            trim($validated['search_cont'])
        );

        try {

            /*
             * =========================================================
             * 1. CARI CONTAINER YANG BELUM HOLD
             * =========================================================
             */

            $data = $this->holdService->searchHold(
                $keyword
            );

            if ($data->isNotEmpty()) {

                /*
                 * Search langsung mendapatkan data.
                 *
                 * Tidak ada list → detail.
                 *
                 * Langsung FORM HOLD.
                 */
                $item = $data->first();

                $html = View::make(
                    'livewire.partials.hold.form',
                    [
                        'status' => 1,
                        'item' => $item,
                        'keyword' => $keyword,
                    ]
                )->render();

                return response()->json([
                    'success' => true,
                    'status' => 1,
                    'keyword' => $keyword,
                    'html' => $html,
                ]);
            }

            /*
             * =========================================================
             * 2. TIDAK DITEMUKAN DI SEARCH HOLD
             * =========================================================
             *
             * Cek apakah container sudah HOLD.
             */

            $hold = $this->holdService->searchRelease(
                $keyword
            );

            if ($hold->isNotEmpty()) {

                /*
                 * Langsung FORM RELEASE.
                 */
                $item = $hold->first();

                $html = View::make(
                    'livewire.partials.hold.form',
                    [
                        'status' => 3,
                        'item' => $item,
                        'keyword' => $keyword,
                    ]
                )->render();

                return response()->json([
                    'success' => true,
                    'status' => 3,
                    'keyword' => $keyword,
                    'html' => $html,
                ]);
            }

            /*
             * =========================================================
             * 3. TIDAK DITEMUKAN
             * =========================================================
             */

            return response()->json([
                'success' => false,
                'status' => 0,
                'keyword' => $keyword,
                'message' =>
                    "WARNING ! NO CONT : {$keyword} NOT FOUND",
                'html' => '',
            ], 404);

        } catch (Throwable $e) {

            report($e);

            return response()->json([
                'success' => false,
                'status' => 500,
                'message' =>
                    'Terjadi kesalahan saat mencari NO CONTAINER.',
            ], 500);
        }
    }

    /**
     * Daftar container yang sedang HOLD.
     *
     * GET /hold/data
     *
     * Pagination server-side.
     *
     * Contoh:
     *
     * /hold/data?page=1
     * /hold/data?page=2
     *
     * Default:
     * 10 data per halaman.
     */
    public function indexData(Request $request): JsonResponse
    {
        try {

            /*
             * Ambil jumlah data per halaman.
             */
            $perPage = (int) $request->input(
                'per_page',
                10
            );

            /*
             * Batasi:
             *
             * minimum = 1
             * maximum = 50
             */
            $perPage = min(
                max($perPage, 1),
                50
            );

            /*
             * Ambil data dengan pagination.
             */
            $data = $this->holdService
                ->getHeldContainers($perPage);

            /*
             * Render table.
             *
             * Paginator dikirim langsung
             * ke Blade.
             */
            $html = View::make(
                'livewire.partials.hold.table',
                [
                    'data' => $data,
                ]
            )->render();

            return response()->json([
                'success' => true,

                /*
                 * Total seluruh data HOLD.
                 */
                'count' => $data->total(),

                /*
                 * Informasi pagination.
                 */
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),

                'from' => $data->firstItem(),
                'to' => $data->lastItem(),

                'html' => $html,
            ]);

        } catch (Throwable $e) {

            report($e);

            return response()->json([
                'success' => false,
                'message' =>
                    'Gagal mengambil data container HOLD.',
            ], 500);
        }
    }

    /**
     * HOLD container.
     *
     * Development phase.
     *
     * Tidak melakukan UPDATE database.
     */
    public function store(
        Request $request
    ): JsonResponse {

        $validated = $request->validate([
            'id' => [
                'required',
            ],

            'nomercont' => [
                'required',
                'string',
                'max:50',
            ],

            'warna' => [
                'required',
                'in:N,M,T',
            ],
        ]);

        try {

            $this->holdService->hold(
                $validated['id'],
                strtoupper(
                    $validated['nomercont']
                ),
                $validated['warna']
            );

            return response()->json([
                'success' => true,
                'message' =>
                    'Validasi HOLD berhasil. ' .
                    'Mode development belum melakukan update database.',
            ]);

        } catch (Throwable $e) {

            report($e);

            return response()->json([
                'success' => false,
                'message' =>
                    'Container gagal diproses HOLD.',
            ], 422);
        }
    }

    /**
     * RELEASE container.
     *
     * Development phase.
     *
     * Tidak melakukan UPDATE database.
     */
    public function release(
        Request $request
    ): JsonResponse {

        $validated = $request->validate([
            'id' => [
                'required',
            ],

            'nomercont' => [
                'required',
                'string',
                'max:50',
            ],

            'nospk' => [
                'nullable',
                'string',
                'max:50',
            ],
        ]);

        try {

            $this->holdService->release(
                $validated['id'],
                strtoupper(
                    $validated['nomercont']
                ),
                $validated['nospk'] ?? null
            );

            return response()->json([
                'success' => true,
                'message' =>
                    'Validasi RELEASE berhasil. ' .
                    'Mode development belum melakukan update database.',
            ]);

        } catch (Throwable $e) {

            report($e);

            return response()->json([
                'success' => false,
                'message' =>
                    'Container gagal diproses RELEASE.',
            ], 422);
        }
    }
}