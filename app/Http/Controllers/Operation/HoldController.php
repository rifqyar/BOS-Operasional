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

    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search_cont' => [
                'required',
                'string',
                'max:50',
            ],
        ]);

        try {
            $keyword = strtoupper(
                trim($validated['search_cont'])
            );

            $result = $this->holdService->search($keyword);

            $statusLabel = match ($result['status']) {
                2 => 'SIAP HOLD',
                3 => 'SEDANG HOLD',
                default => 'TIDAK DITEMUKAN',
            };

            $html = '';

            if ($result['data']->isNotEmpty()) {
                $html = View::make(
                    'livewire.partials.hold.form',
                    [
                        'data' => $result['data'],
                        'status' => $result['status'],
                        'statusLabel' => $statusLabel,
                    ]
                )->render();
            }

            return response()->json([
                'success' => true,
                'status' => $result['status'],
                'keyword' => $result['keyword'],
                'count' => $result['data']->count(),
                'html' => $html,
            ]);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencari container.',
            ], 500);
        }
    }

    public function indexData(): JsonResponse
    {
        try {
            $data = $this->holdService->getHeldContainers();

            $html = View::make(
                'livewire.partials.hold.form',
                [
                    'data' => $data,
                    'status' => 3,
                    'statusLabel' => 'SEDANG HOLD',
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
                $validated['nomercont'],
                $validated['warna']
            );

            return response()->json([
                'success' => true,
                'message' => 'Container berhasil di-HOLD.',
            ]);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
                    ?: 'Container gagal di-HOLD.',
            ], 422);
        }
    }

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
                $validated['nomercont'],
                $validated['nospk'] ?? null
            );

            return response()->json([
                'success' => true,
                'message' => 'Container berhasil di-RELEASE.',
            ]);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
                    ?: 'Container gagal di-RELEASE.',
            ], 422);
        }
    }
}