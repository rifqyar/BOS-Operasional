<?php

namespace App\Http\Controllers\Operation;

use App\Http\Controllers\Controller;
use App\Services\MarshallingCicService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class MarshallingCicController extends Controller
{
    public function __construct(
        protected MarshallingCicService $marshallingCicService
    ) {
    }

    /**
     * Load monitoring table awal.
     */
    public function indexData(): JsonResponse
    {
        try {
            $jobs = $this->marshallingCicService->getAllJobs();

            $view = view(
                'livewire.partials.marshallingcic.table',
                compact('jobs')
            )->render();

            return response()->json([
                'data' => $view,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'message' => $e->getMessage()
                    ?: 'Terjadi kesalahan saat mengambil data Marshalling CIC.',
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * Search container.
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'no_cont' => ['required', 'string', 'max:20'],
            ]);

            $keyword = strtoupper(trim($validated['no_cont']));

            if ($keyword === '') {
                throw new Exception(
                    'Nomor Container wajib diisi.',
                    422
                );
            }

            $checkHoldP2 = checkHoldP2($keyword);

            if ($checkHoldP2) {
                throw new Exception(
                    'Container on Hold P2, Harap release Container terlebih dahulu',
                    500
                );
            }

            $jobs = $this->marshallingCicService->search($keyword);

            if (empty($jobs)) {
                throw new Exception(
                    'Data Marshalling CIC tidak ditemukan.',
                    404
                );
            }

            $view = view(
                'livewire.partials.marshallingcic.table',
                compact('jobs')
            )->render();

            return response()->json([
                'data' => $view,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'message' => $e->getMessage()
                    ?: 'Terjadi kesalahan saat mencari Container.',
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * Detail job.
     */
    public function detail(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'id_job_slip' => ['required', 'integer'],
            ]);

            $row = $this->marshallingCicService->getDetail(
                (int) $validated['id_job_slip']
            );

            if (!$row) {
                throw new Exception(
                    'Detail Job Marshalling CIC tidak ditemukan.',
                    404
                );
            }

            $jobActivities = $this->marshallingCicService
                ->getJobActivities();

            $equipments = $this->marshallingCicService
                ->getEquipments();

            $trucks = $this->marshallingCicService
                ->getTrucks();

            $operators = $this->marshallingCicService
                ->getOperators();

            $view = view(
                'livewire.partials.marshallingcic.form',
                compact(
                    'row',
                    'jobActivities',
                    'equipments',
                    'trucks',
                    'operators'
                )
            )->render();

            return response()->json([
                'data' => $view,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'message' => $e->getMessage()
                    ?: 'Terjadi kesalahan saat mengambil detail.',
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * Read-only phase.
     */
    public function store(Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'Marshalling CIC saat ini dalam mode read-only. Data tidak disimpan.',
        ]);
    }
}