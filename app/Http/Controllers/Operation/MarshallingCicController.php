<?php

namespace App\Http\Controllers\Operation;

use App\Http\Controllers\Controller;
use App\Http\Traits\LogTraits;
use App\Services\MarshallingCicService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class MarshallingCicController extends Controller
{
    use LogTraits;

    protected MarshallingCicService $marshallingCicService;

    public function __construct(MarshallingCicService $marshallingCicService)
    {
        $this->marshallingCicService = $marshallingCicService;
    }

    public function indexData(Request $request): JsonResponse
    {
        try {
            $perPage = max(
                1,
                min((int) $request->input('per_page', 10), 50)
            );

            $jobs = $this->marshallingCicService->getAllJobs($perPage);

            $view = view(
                'livewire.partials.marshallingcic.table',
                compact('jobs')
            )->render();

            return response()->json([
                'success' => true,
                'data' => $view,
                'pagination' => [
                    'current_page' => $jobs->currentPage(),
                    'last_page' => $jobs->lastPage(),
                    'per_page' => $jobs->perPage(),
                    'total' => $jobs->total(),
                    'from' => $jobs->firstItem(),
                    'to' => $jobs->lastItem(),
                ],
            ]);
        } catch (Throwable $e) {
            $this->logError(
                'marshalling_cic',
                'Gagal mengambil data monitoring Marshalling CIC.',
                [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            );

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data Marshalling CIC.',
            ], 500);
        }
    }

    public function search(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'no_cont' => ['required', 'string', 'max:20'],
            ]);

            $keyword = strtoupper(trim($validated['no_cont']));

            if ($keyword === '') {
                throw new Exception('Nomor container wajib diisi.', 422);
            }

            if (checkHoldP2($keyword)) {
                throw new Exception(
                    'Container on Hold P2. Harap release container terlebih dahulu.',
                    422
                );
            }

            $jobs = $this->marshallingCicService->search($keyword);

            if ($jobs->isEmpty()) {
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
                'success' => true,
                'data' => $view,
            ]);
        } catch (Throwable $e) {
            $statusCode = (int) $e->getCode();

            if ($statusCode < 400 || $statusCode > 599) {
                $statusCode = 500;
            }

            $this->logError(
                'marshalling_cic',
                'Pencarian Marshalling CIC gagal.',
                [
                    'no_cont' => $request->input('no_cont'),
                    'error' => $e->getMessage(),
                ]
            );

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
                    ?: 'Terjadi kesalahan saat mencari container.',
            ], $statusCode);
        }
    }

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

            $jobActivities = $this->marshallingCicService->getJobActivities();
            $equipments = $this->marshallingCicService->getEquipments();
            $trucks = $this->marshallingCicService->getTrucks();
            $operators = $this->marshallingCicService->getOperators();

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
                'success' => true,
                'data' => $view,
            ]);
        } catch (Throwable $e) {
            $statusCode = (int) $e->getCode();

            if ($statusCode < 400 || $statusCode > 599) {
                $statusCode = 500;
            }

            $this->logError(
                'marshalling_cic',
                'Gagal mengambil detail Marshalling CIC.',
                [
                    'id_job_slip' => $request->input('id_job_slip'),
                    'error' => $e->getMessage(),
                ]
            );

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
                    ?: 'Terjadi kesalahan saat mengambil detail.',
            ], $statusCode);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'idJobSlip' => ['required', 'integer'],
                'spk' => ['required', 'string'],
                'nocont' => ['required', 'string', 'max:20'],
                'ukrcont' => ['nullable', 'string', 'max:20'],
                'lokaw' => ['nullable', 'string', 'max:50'],
                'lokak' => ['required', 'string', 'max:50'],
                'job' => ['required', 'string', 'max:100'],
                'note' => ['nullable', 'string'],
                'fumigasi' => ['nullable', 'string', 'max:10'],
                'respon' => ['nullable', 'string', 'max:255'],
                'jenispekerjaan1' => ['nullable', 'string', 'max:50'],
                'alat1' => ['nullable', 'string', 'max:50'],
                'operator1' => ['nullable', 'string', 'max:100'],
                'jenispekerjaan2' => ['nullable', 'string', 'max:50'],
                'truck1' => ['nullable', 'string', 'max:50'],
                'operator2' => ['nullable', 'string', 'max:100'],
                'jenispekerjaan3' => ['nullable', 'string', 'max:50'],
                'alat3' => ['nullable', 'string', 'max:50'],
                'operator3' => ['nullable', 'string', 'max:100'],
            ]);

            if ((string) $validated['spk'] !== '460') {
                return response()->json([
                    'success' => false,
                    'message' => 'Container ini belum dilakukan job slip untuk stacking CIC.',
                ], 422);
            }

            $username = session('USERLOGIN');

            if (!$username && auth()->check()) {
                $username = auth()->user()->username
                    ?? auth()->user()->name
                    ?? null;
            }

            if (!$username) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session login tidak ditemukan. Silakan login kembali.',
                ], 401);
            }

            $result = $this->marshallingCicService->storeMarshallingCic(
                $validated,
                (string) $username
            );

            $this->logInfo(
                'marshalling_cic',
                'Marshalling CIC berhasil disimpan.',
                [
                    'id_job_slip' => $validated['idJobSlip'],
                    'no_cont' => $validated['nocont'],
                    'username' => $username,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Marshalling CIC berhasil disimpan.',
                'data' => $result,
            ]);
        } catch (Throwable $e) {
            $statusCode = (int) $e->getCode();

            if ($statusCode < 400 || $statusCode > 599) {
                $statusCode = 500;
            }

            $this->logError(
                'marshalling_cic',
                'Marshalling CIC gagal disimpan.',
                [
                    'id_job_slip' => $request->input('idJobSlip'),
                    'no_cont' => $request->input('nocont'),
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            );

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
                    ?: 'Terjadi kesalahan saat menyimpan Marshalling CIC.',
            ], $statusCode);
        }
    }
}
