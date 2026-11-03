<?php

namespace App\Http\Controllers\Operation;

use App\Http\Controllers\Controller;
use App\Http\Traits\LogTraits;
use App\Services\DeliveryServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class DeliveryController extends Controller
{
    use LogTraits;

    protected string $logChannel = 'delivery';

    public function __construct(
        protected DeliveryServices $deliveryService
    ) {
    }

    /**
     * Search container Delivery.
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'search_cont2' => ['required', 'string', 'max:50'],
            ]);

            $noCont = strtoupper(trim($validated['search_cont2']));

            $searchResult = $this->deliveryService->searchContainer($noCont);

            if (empty($searchResult)) {
                $this->logWarning(
                    $this->logChannel,
                    'Container Delivery tidak ditemukan.',
                    ['no_cont' => $noCont]
                );

                return response()->json([
                    'status' => 0,
                    'kode' => 1,
                    'message' => "NO CONT : {$noCont} NOT FOUND",
                ], 404);
            }

            $result = $this->deliveryService->getDetail($noCont);

            if (($result['status'] ?? 0) === 0) {
                return response()->json([
                    'status' => 0,
                    'kode' => 1,
                    'message' => $result['message'] ?? "NO CONT : {$noCont} NOT FOUND",
                ], 404);
            }

            $html = view(
                'livewire.partials.delivery.form',
                ['result' => $result]
            )->render();

            $this->logInfo(
                $this->logChannel,
                'Search Delivery berhasil.',
                [
                    'no_cont' => $noCont,
                    'delivery_status' => $result['status'],
                ]
            );

            return response()->json([
                'status' => $result['status'],
                'data' => $html,
            ]);
        } catch (Throwable $e) {
            $this->logError(
                $this->logChannel,
                'Gagal melakukan search Delivery.',
                [
                    'search_cont2' => $request->input('search_cont2'),
                    'error' => $e->getMessage(),
                ]
            );

            return response()->json([
                'status' => 0,
                'kode' => 500,
                'message' => 'Terjadi kesalahan saat mencari container Delivery.',
            ], 500);
        }
    }

    /**
     * Store Delivery.
     *
     * Saat ini masih read-only karena proses write belum diaktifkan.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'nomercont' => ['required', 'string', 'max:50'],
                'gate' => ['nullable', 'string', 'max:50'],
                'nomertruck' => ['nullable', 'string', 'max:50'],
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Proses penyimpanan Delivery belum diaktifkan.',
            ], 422);
        } catch (Throwable $e) {
            $this->logError(
                $this->logChannel,
                'Gagal melakukan validasi store Delivery.',
                [
                    'no_cont' => $request->input('nomercont'),
                    'error' => $e->getMessage(),
                ]
            );

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memproses Delivery.',
            ], 500);
        }
    }
}