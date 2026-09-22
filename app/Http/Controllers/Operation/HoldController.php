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
             * ---------------------------------------------------------
             * 1. CARI CONTAINER YANG BELUM HOLD
             * ---------------------------------------------------------
             *
             * Legacy:
             * search_holdd()
             *
             * Kondisi:
             * FL_HOLD = N
             */

            $data = $this->holdService->searchHold(
                $keyword
            );


            if ($data->isNotEmpty()) {

                /*
                 * Search langsung mendapatkan data.
                 *
                 * Tidak perlu lagi menampilkan daftar
                 * dan meminta user klik container.
                 */

                $item = $data->first();


                /*
                 * Langsung render FORM HOLD.
                 */

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
             * ---------------------------------------------------------
             * 2. CONTAINER TIDAK DITEMUKAN DI SEARCH HOLD
             * ---------------------------------------------------------
             *
             * Cek apakah container sudah dalam kondisi HOLD.
             *
             * Legacy:
             * search_realease()
             */

            $hold = $this->holdService->searchRelease(
                $keyword
            );


            if ($hold->isNotEmpty()) {

                /*
                 * Ambil data pertama.
                 */

                $item = $hold->first();


                /*
                 * Langsung render FORM RELEASE.
                 */

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
             * ---------------------------------------------------------
             * 3. CONTAINER TIDAK DITEMUKAN
             * ---------------------------------------------------------
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
     * Digunakan oleh:
     * GET /hold/data
     */
    public function indexData(): JsonResponse
    {
        try {

            $data = $this->holdService
                ->getHeldContainers();


            $html = View::make(
                'livewire.partials.hold.table',
                [
                    'data' => $data,
                ]
            )->render();


            return response()->json([
                'success' => true,
                'count' => $data->count(),
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