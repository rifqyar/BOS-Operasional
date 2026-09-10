<?php

namespace App\Http\Controllers\Operation;

use App\CheckHoldP2Class;
use App\Http\Controllers\Controller;
use App\Services\BehandleInServices;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class BehandleController extends Controller
{
    public function __construct(protected BehandleInServices $behandleInServices)
    {}
    public function search(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'no_cont' => ['required', 'string', 'max:11'],
            ]);

            $checkHoldP2 = checkHoldP2($request->no_cont);
            if ($checkHoldP2) {
                throw new Exception("Container on Hold P2, Harap release Container terlebih dahulu", 500);
            }

            $rows = $this->behandleInServices->getDataContainer($validated['no_cont']);
            if(empty($rows)) {
                throw new Exception("Data Container tidak ditemukan. Pastikan nomor container sudah benar.", 404);
            }

            $trucks = $this->behandleInServices->getTruck((string) ($rows->ID_FLAT ?? ''));
            $references = [
                'job_activities' => $this->behandleInServices->getJobActivity(),
                'equipments' => $this->behandleInServices->getAlat(),
                'operators' => $this->behandleInServices->getOperator(),
                'trucks' => $trucks,
                'container_conditions' => $this->behandleInServices->getCondition(),
            ];

            $job_activities = $references['job_activities'];
            $equipments = $references['equipments'];
            $operators = $references['operators'];
            $container_conditions = $references['container_conditions'];

            $view = view('livewire.partials.behandlein.form', compact(
                'rows',
                'job_activities',
                'equipments',
                'operators',
                'trucks',
                'container_conditions',
                'references'
            ))->render();

            return response()->json([
                'data' => $view,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'message' => $e->getMessage() ?? 'Terjadi kesalahan saat mencari data Container.',
                'details' => $e,
            ], $e->getCode() ?: 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'nomerkon' => ['required', 'string', 'max:20'],
                'nospk' => ['required', 'string', 'max:50'],
                'isocode' => ['nullable', 'string', 'max:10'],
                'optradio' => ['nullable', 'string'],
                'noseal' => ['nullable', 'string', 'max:50'],
                'kondisi' => ['nullable'],
                'trucknya' => ['nullable', 'string', 'max:30'],
                'nolok' => ['nullable', 'string', 'max:30'],
                'ukuran' => ['nullable', 'string', 'max:10'],
                'tipe' => ['nullable', 'string', 'max:10'],
                'jenisPekerjaan' => ['nullable'],
                'alat' => ['nullable'],
                'operator' => ['nullable', 'string'],
            ]);

            return response()->json([
                'message' => 'Data Behandle In berhasil disimpan.',
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'message' => $e->getMessage() ?? 'Terjadi kesalahan saat menyimpan data Behandle In.',
            ], 500);
        }
    }
}
