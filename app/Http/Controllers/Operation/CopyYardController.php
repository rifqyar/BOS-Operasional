<?php

namespace App\Http\Controllers\Operation;

use App\Http\Controllers\Controller;
use App\Services\CopyYardServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CopyYardController extends Controller
{
    public function __construct(
        protected CopyYardServices $copyYardService
    ) {
    }

    /**
     * Search container.
     *
     * Flow:
     *
     * Search
     *   ↓
     * Container ditemukan?
     *   ↓ YES
     * Form Copy Yard
     *
     *   ↓ NO
     * Warning
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

        $container = $this->copyYardService
            ->getContainer($keyword);

        if (!$container) {
            return response()->json([
                'status' => 0,
                'kode' => 1,
                'NOCONT' => $keyword,
                'message' => "KONTAINER : {$keyword} NOT FOUND",
            ]);
        }

        /**
         * Legacy:
         * $data['usernya'] = session('KD_GROUP');
         *
         * Digunakan untuk menentukan apakah
         * ACTION BLOK ditampilkan.
         */
        $usernya = session('KD_GROUP');

        $html = view(
            'livewire.partials.copyyard.form',
            [
                'container' => $container,
                'usernya' => $usernya,
            ]
        )->render();

        return response()->json([
            'status' => 1,
            'data' => $html,
        ]);
    }

    /**
     * Copy Yard.
     *
     * Saat ini masih READ ONLY.
     *
     * Legacy set_copy() melakukan banyak UPDATE,
     * tetapi pada fase development sekarang belum
     * kita aktifkan persistence.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nomerkon' => [
                'required',
                'string',
                'max:50',
            ],

            'ukurankon' => [
                'nullable',
                'string',
                'max:50',
            ],

            'jns_dok' => [
                'nullable',
                'string',
                'max:100',
            ],

            'status_cont' => [
                'nullable',
                'string',
                'max:100',
            ],

            'nolok' => [
                'nullable',
                'string',
                'max:100',
            ],

            'mySelect' => [
                'nullable',
                'string',
                'in:satu,dua',
            ],

            'lokbar' => [
                'nullable',
                'string',
                'max:100',
            ],
        ]);

        return response()->json([
            'success' => false,
            'message' =>
                'Copy Yard masih dalam mode read-only. ' .
                'Proses pemindahan container belum diaktifkan.',
        ], 422);
    }
}