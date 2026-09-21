<?php

namespace App\Http\Controllers\Operation;

use App\Http\Controllers\Controller;
use App\Services\StringStuffingServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StringStuffingController extends Controller
{
    public function __construct(
        protected StringStuffingServices $service
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

        $keyword = trim($validated['search_cont']);

        $container = $this->service->getContainer($keyword);

        if (!$container) {
            return response()->json([
                'status' => false,
                'message' => "NO CONT : {$keyword} NOT FOUND",
            ], 404);
        }

        $html = view(
            'livewire.partials.stringstuffing.form',
            [
                'container' => $container,
            ]
        )->render();

        return response()->json([
            'status' => true,
            'data' => $html,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nomerkon' => [
                'required',
                'string',
                'max:50',
            ],
            'no_dok' => [
                'nullable',
                'string',
                'max:100',
            ],
            'no_cont_lama' => [
                'nullable',
                'string',
                'max:50',
            ],
            'no_dok_lama' => [
                'nullable',
                'string',
                'max:100',
            ],
            'op_start' => [
                'nullable',
            ],
        ]);

        return response()->json([
            'status' => false,
            'message' => 'String Stuffing masih dalam mode read-only. Proses Start/End belum diaktifkan.',
        ], 422);
    }
}