<?php

namespace App\Http\Controllers\Operation;

use App\Http\Controllers\Controller;
use App\Services\DeliveryServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function __construct(
        protected DeliveryServices $deliveryService
    ) {
    }

    /**
     * Search Delivery.
     *
     * Search berhasil:
     * langsung tampilkan form.
     *
     * Search gagal:
     * return alert message.
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search_cont2' => ['required', 'string', 'max:50'],
        ]);

        $noCont = trim($validated['search_cont2']);

        /**
         * Pastikan container memang masuk
         * kriteria search Delivery legacy.
         */
        $searchResult = $this->deliveryService->searchContainer($noCont);

        if (count($searchResult) === 0) {
            return response()->json([
                'status' => 0,
                'kode' => 1,
                'message' => "NO CONT : {$noCont} NOT FOUND",
            ]);
        }

        /**
         * Container ditemukan.
         * Langsung tentukan form Delivery.
         */
        $result = $this->deliveryService->getDetail($noCont);

        if ($result['status'] === 0) {
            return response()->json([
                'status' => 0,
                'kode' => 1,
                'message' => $result['message'] ?? "NO CONT : {$noCont} NOT FOUND",
            ]);
        }

        $html = view(
            'livewire.partials.delivery.form',
            [
                'result' => $result,
            ]
        )->render();

        return response()->json([
            'status' => $result['status'],
            'data' => $html,
        ]);
    }

    /**
     * Store Delivery.
     *
     * Development saat ini READ ONLY.
     * Belum melakukan INSERT / UPDATE.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nomercont' => ['required', 'string', 'max:50'],
            'gate' => ['nullable', 'string', 'max:50'],
            'nomertruck' => ['nullable', 'string', 'max:50'],
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Delivery masih dalam mode read-only. Proses penyimpanan belum diaktifkan.',
        ], 422);
    }
}