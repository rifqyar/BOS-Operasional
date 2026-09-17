<?php

namespace App\Http\Controllers\Operation;

use App\Http\Controllers\Controller;
use App\Services\InspectionOutServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InspectionOutController extends Controller
{
    public function __construct(
        protected InspectionOutServices $inspectionOutService
    ) {
    }

    /**
     * Search Inspection Out.
     *
     * Search langsung menentukan apakah container
     * bisa masuk ke form Inspection Out.
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search_cont' => ['required', 'string', 'max:50'],
        ]);

        $noCont = trim($validated['search_cont']);

        $result = $this->inspectionOutService
            ->getInspectionData($noCont);

        /**
         * Tidak memenuhi kondisi.
         * Frontend akan menampilkan alert.
         */
        if (($result['status'] ?? 0) !== 2) {
            return response()->json([
                'status' => 0,
                'kode' => 1,
                'message' => $result['message']
                    ?? "WARNING ! NO CONT : {$noCont} NOT FOUND",
            ]);
        }

        /**
         * Langsung render form.
         */
        $html = view(
            'livewire.partials.inspectionout.form',
            [
                'result' => $result,
            ]
        )->render();

        return response()->json([
            'status' => 2,
            'data' => $html,
        ]);
    }

    /**
     * Store Inspection Out.
     *
     * Saat ini READ ONLY.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nomercont' => ['required', 'string', 'max:50'],
            'ukuran' => ['nullable', 'string', 'max:50'],
            'optradio' => ['required', 'in:ada,tidak ada'],
            'noseal' => ['nullable', 'string', 'max:100'],
            'kondisi' => ['nullable', 'string', 'max:50'],
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Inspection Out masih dalam mode read-only. Proses penyimpanan belum diaktifkan.',
        ], 422);
    }
}