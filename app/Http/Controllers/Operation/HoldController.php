<?php

namespace App\Http\Controllers\Operation;

use App\Http\Controllers\Controller;
use App\Http\Traits\LogTraits;
use App\Services\HoldService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Throwable;

class HoldController extends Controller
{
    use LogTraits;

    public function __construct(
        protected HoldService $holdService
    ) {
    }

    /**
     * Search container.
     *
     * 1. Cari container belum HOLD.
     * 2. Jika tidak ditemukan, cari container yang sudah HOLD.
     * 3. Jika tidak ditemukan juga, kembalikan 404.
     *
     * Seluruh pencarian dilakukan pada koneksi prod.
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
            $data = $this->holdService->searchHold($keyword);

            if ($data->isNotEmpty()) {
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

            $heldData = $this->holdService->searchRelease($keyword);

            if ($heldData->isNotEmpty()) {
                $item = $heldData->first();

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

            return response()->json([
                'success' => false,
                'status' => 0,
                'keyword' => $keyword,
                'message' => "WARNING ! NO CONT : {$keyword} NOT FOUND",
                'html' => '',
            ], 404);
        } catch (Throwable $e) {
            $this->logError('hold', 'Gagal mencari container HOLD', [
                'error' => $e->getMessage(),
                'keyword' => $keyword,
            ]);

            return response()->json([
                'success' => false,
                'status' => 500,
                'message' => 'Terjadi kesalahan saat mencari NO CONTAINER.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Daftar container yang sedang HOLD.
     *
     * GET /hold/data
     */
    public function indexData(Request $request): JsonResponse
    {
        try {
            $perPage = (int) $request->input('per_page', 10);
            $perPage = min(max($perPage, 1), 50);

            $data = $this->holdService->getHeldContainers($perPage);

            $html = View::make(
                'livewire.partials.hold.table',
                [
                    'data' => $data,
                ]
            )->render();

            return response()->json([
                'success' => true,
                'count' => $data->total(),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
                'html' => $html,
            ]);
        } catch (Throwable $e) {
            $this->logError('hold', 'Gagal mengambil daftar container HOLD', [
                'error' => $e->getMessage(),
                'per_page' => $request->input('per_page', 10),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data container HOLD.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * HOLD container.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id' => [
                'required',
                'integer',
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

        $id = $validated['id'];
        $noCont = strtoupper(trim($validated['nomercont']));
        $warna = strtoupper($validated['warna']);

        try {
            $this->holdService->hold(
                $id,
                $noCont,
                $warna
            );

            $this->logInfo('hold', 'Container berhasil di-HOLD', [
                'id' => $id,
                'no_container' => $noCont,
                'warna' => $warna,
            ]);

            return response()->json([
                'success' => true,
                'status' => 'success',
                'message' => 'Container berhasil diproses HOLD.',
                'id' => $id,
                'no_container' => $noCont,
                'warna' => $warna,
            ]);
        } catch (Throwable $e) {
            $this->logError('hold', 'Gagal memproses HOLD container', [
                'error' => $e->getMessage(),
                'id' => $id,
                'no_container' => $noCont,
                'warna' => $warna,
            ]);

            $errorCode = (int) $e->getCode();
            $httpCode = in_array($errorCode, [404, 422], true)
                ? $errorCode
                : 500;

            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => $e->getMessage()
                    ?: 'Container gagal diproses HOLD.',
                'error' => $e->getMessage(),
            ], $httpCode);
        }
    }

    /**
     * RELEASE container.
     */
    public function release(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id' => [
                'required',
                'integer',
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

        $id = $validated['id'];
        $noCont = strtoupper(trim($validated['nomercont']));
        $noSpk = isset($validated['nospk'])
            ? strtoupper(trim($validated['nospk']))
            : null;

        try {
            $this->holdService->release(
                $id,
                $noCont,
                $noSpk
            );

            $this->logInfo('hold', 'Container berhasil di-RELEASE', [
                'id' => $id,
                'no_container' => $noCont,
                'no_spk' => $noSpk,
            ]);

            return response()->json([
                'success' => true,
                'status' => 'success',
                'message' => 'Container berhasil diproses RELEASE.',
                'id' => $id,
                'no_container' => $noCont,
                'no_spk' => $noSpk,
            ]);
        } catch (Throwable $e) {
            $this->logError('hold', 'Gagal memproses RELEASE container', [
                'error' => $e->getMessage(),
                'id' => $id,
                'no_container' => $noCont,
                'no_spk' => $noSpk,
            ]);

            $errorCode = (int) $e->getCode();
            $httpCode = in_array($errorCode, [404, 422], true)
                ? $errorCode
                : 500;

            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => $e->getMessage()
                    ?: 'Container gagal diproses RELEASE.',
                'error' => $e->getMessage(),
            ], $httpCode);
        }
    }
}