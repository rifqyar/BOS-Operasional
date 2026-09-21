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
     * Legacy:
     * search_hold_new()
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

        $keyword = strtoupper(trim($validated['search_cont']));

        try {
            /*
             * STATUS 2
             * Container ditemukan dan belum HOLD.
             */
            $data = $this->holdService->searchHold($keyword);

            if ($data->isNotEmpty()) {
                $html = View::make(
                    'livewire.partials.hold.form',
                    [
                        'status' => 2,
                        'data' => $data,
                        'keyword' => $keyword,
                    ]
                )->render();

                return response()->json([
                    'success' => true,
                    'status' => 2,
                    'keyword' => $keyword,
                    'count' => $data->count(),
                    'html' => $html,
                ]);
            }

            /*
             * STATUS 3
             * Tidak ditemukan container yang siap HOLD,
             * cek apakah container sudah HOLD.
             */
            $hold = $this->holdService->searchRelease($keyword);

            if ($hold->isNotEmpty()) {
                $html = View::make(
                    'livewire.partials.hold.form',
                    [
                        'status' => 3,
                        'data' => $hold,
                        'keyword' => $keyword,
                    ]
                )->render();

                return response()->json([
                    'success' => true,
                    'status' => 3,
                    'keyword' => $keyword,
                    'count' => $hold->count(),
                    'html' => $html,
                ]);
            }

            /*
             * STATUS 0
             * Tidak ditemukan.
             */
            return response()->json([
                'success' => false,
                'status' => 0,
                'keyword' => $keyword,
                'message' => "WARNING ! NO CONT : {$keyword} NOT FOUND",
                'html' => '',
            ], 404);

        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'status' => 500,
                'message' => 'Terjadi kesalahan saat mencari NO CONTAINER.',
            ], 500);
        }
    }

    /**
     * Detail container yang dipilih dari STATUS 2.
     *
     * Legacy:
     * search_hold()
     *
     * Dipanggil ketika user klik NO CONTAINER
     * dari daftar hasil pencarian.
     */
    public function detail(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'no_cont' => [
                'required',
                'string',
                'max:50',
            ],
        ]);

        $keyword = strtoupper(trim($validated['no_cont']));

        try {
            $data = $this->holdService->searchHold($keyword);

            if ($data->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => "NO CONT : {$keyword} NOT FOUND",
                ], 404);
            }

            /*
             * Legacy status 1 menggunakan satu data.
             *
             * Ambil record pertama.
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

        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil detail container.',
            ], 500);
        }
    }

    /**
     * Daftar container yang sedang HOLD.
     */
    public function indexData(): JsonResponse
    {
        try {
            $data = $this->holdService->getHeldContainers();

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
                'message' => 'Gagal mengambil data container HOLD.',
            ], 500);
        }
    }

    /**
     * Development phase.
     *
     * Tidak melakukan UPDATE.
     */
    public function store(Request $request): JsonResponse
    {
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
                strtoupper($validated['nomercont']),
                $validated['warna']
            );

            return response()->json([
                'success' => true,
                'message' => 'Validasi HOLD berhasil. Mode development belum melakukan update database.',
            ]);

        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Container gagal diproses HOLD.',
            ], 422);
        }
    }

    /**
     * Development phase.
     *
     * Tidak melakukan UPDATE.
     */
    public function release(Request $request): JsonResponse
    {
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
                strtoupper($validated['nomercont']),
                $validated['nospk'] ?? null
            );

            return response()->json([
                'success' => true,
                'message' => 'Validasi RELEASE berhasil. Mode development belum melakukan update database.',
            ]);

        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Container gagal diproses RELEASE.',
            ], 422);
        }
    }
}