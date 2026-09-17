<?php

namespace App\Http\Controllers\Operation;

use App\Http\Controllers\Controller;
use App\Services\OnChassisServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OnChassisController extends Controller
{
    public function __construct(
        protected OnChassisServices $onChassisService
    ) {
    }

    /**
     * Search container.
     *
     * Search langsung menampilkan form.
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search_ch' => ['required', 'string', 'max:50'],
        ]);

        $keyword = trim($validated['search_ch']);

        $container = $this->onChassisService
            ->getContainer($keyword);

        /**
         * Tidak ditemukan.
         */
        if (!$container) {
            return response()->json([
                'status' => 0,
                'kode' => 1,
                'NOCONT' => $keyword,
                'message' => "WARNING ! NO CONT : {$keyword} NOT FOUND",
            ]);
        }

        /**
         * Langsung render form.
         */
        $html = view(
            'livewire.partials.onchassis.form',
            [
                'container' => $container,
            ]
        )->render();

        return response()->json([
            'status' => 1,
            'data' => $html,
        ]);
    }

    /**
     * Store / proses On Chassis.
     *
     * Saat ini READ ONLY.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nomerspk' => ['nullable', 'string', 'max:100'],
            'nomercont' => ['required', 'string', 'max:50'],
            'ukuran' => ['nullable', 'string', 'max:50'],
            'notruck' => ['nullable', 'string', 'max:100'],
            'lokasi' => ['nullable', 'string', 'max:100'],
        ]);

        return response()->json([
            'success' => false,
            'message' => 'On Chassis masih dalam mode read-only. Proses penyimpanan belum diaktifkan.',
        ], 422);
    }
}