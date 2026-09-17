<?php

namespace App\Http\Controllers\Operation;

use App\Http\Controllers\Controller;
use App\Services\MonitoringReeferServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MonitoringReeferController extends Controller
{
    public function __construct(
        protected MonitoringReeferServices $service
    ) {
    }


    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'no_cont' => [
                'required',
                'string',
                'max:50',
            ],
        ]);

        $keyword = trim($validated['no_cont']);

        $monitoring = $this->service->getMonitoring($keyword);

        if (!$monitoring) {
            return response()->json([
                'status' => false,
                'message' => "NO CONT : {$keyword} SUDAH UNPLUGIN / TIDAK AKTIF MONITORING",
            ], 404);
        }

        $temprev = $this->service->getLastTemperature($keyword);

        $html = view(
            'livewire.partials.monitoringreefer.form',
            [
                'nilai' => $monitoring,
                'temprev' => $temprev,
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
            'temperature' => [
                'required',
                'string',
                'max:50',
            ],
            'note' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'w_plugin' => [
                'nullable',
            ],
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Data monitoring berhasil divalidasi. Penyimpanan belum diaktifkan pada fase development.',
        ]);
    }
}