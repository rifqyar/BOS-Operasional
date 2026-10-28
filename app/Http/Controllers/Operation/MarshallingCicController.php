<?php

namespace App\Http\Controllers\Operation;

use App\Http\Controllers\Controller;
use App\Http\Traits\LogTraits;
use App\Services\MarshallingCicService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;

class MarshallingCicController extends Controller
{
    use LogTraits;

    protected MarshallingCicService $marshallingCicService;

    public function __construct(
        MarshallingCicService $marshallingCicService
    ) {
        $this->marshallingCicService = $marshallingCicService;
    }

    /**
     * Mengambil data monitoring Marshalling CIC.
     */
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

            $this->logInfo(
                'marshalling-cic',
                'Berhasil mengambil data monitoring Marshalling CIC.',
                [
                    'per_page' => $perPage,
                    'current_page' => $jobs->currentPage(),
                    'total' => $jobs->total(),
                ]
            );

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
                'marshalling-cic',
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

    /**
     * Mencari pekerjaan berdasarkan nomor kontainer.
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'no_cont' => ['required', 'string', 'max:20'],
            ]);

            $keyword = strtoupper(trim($validated['no_cont']));

            if ($keyword === '') {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor kontainer wajib diisi.',
                ], 422);
            }

            if (checkHoldP2($keyword)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Container on Hold P2. Harap release container terlebih dahulu.',
                ], 422);
            }

            $jobs = $this->marshallingCicService->search($keyword);

            if ($jobs->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data Marshalling CIC tidak ditemukan.',
                ], 404);
            }

            $view = view(
                'livewire.partials.marshallingcic.table',
                compact('jobs')
            )->render();

            $this->logInfo(
                'marshalling-cic',
                'Pencarian Marshalling CIC berhasil.',
                [
                    'no_cont' => $keyword,
                    'jumlah_data' => $jobs->count(),
                ]
            );

            return response()->json([
                'success' => true,
                'data' => $view,
            ]);
        } catch (ValidationException $e) {
            $this->logWarning(
                'marshalling-cic',
                'Validasi pencarian Marshalling CIC gagal.',
                [
                    'no_cont' => $request->input('no_cont'),
                    'errors' => $e->errors(),
                ]
            );

            return response()->json([
                'success' => false,
                'message' => 'Data pencarian tidak valid.',
                'errors' => $e->errors(),
            ], 422);
        } catch (Throwable $e) {
            $this->logError(
                'marshalling-cic',
                'Pencarian Marshalling CIC gagal.',
                [
                    'no_cont' => $request->input('no_cont'),
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            );

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencari kontainer.',
            ], 500);
        }
    }

    /**
     * Mengambil detail pekerjaan dan data master untuk form.
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
                return response()->json([
                    'success' => false,
                    'message' => 'Detail Job Marshalling CIC tidak ditemukan.',
                ], 404);
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

            $this->logInfo(
                'marshalling-cic',
                'Berhasil mengambil detail Marshalling CIC.',
                [
                    'id_job_slip' => $validated['id_job_slip'],
                    'no_cont' => $row->NO_CONT ?? null,
                ]
            );

            return response()->json([
                'success' => true,
                'data' => $view,
            ]);
        } catch (ValidationException $e) {
            $this->logWarning(
                'marshalling-cic',
                'Validasi detail Marshalling CIC gagal.',
                [
                    'id_job_slip' => $request->input('id_job_slip'),
                    'errors' => $e->errors(),
                ]
            );

            return response()->json([
                'success' => false,
                'message' => 'ID job slip tidak valid.',
                'errors' => $e->errors(),
            ], 422);
        } catch (Throwable $e) {
            $this->logError(
                'marshalling-cic',
                'Gagal mengambil detail Marshalling CIC.',
                [
                    'id_job_slip' => $request->input('id_job_slip'),
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            );

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil detail Marshalling CIC.',
            ], 500);
        }
    }

    /**
     * Menyimpan proses Marshalling CIC.
     */
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
                    'message' => 'Proses ini hanya berlaku untuk SPK 460.',
                ], 422);
            }

            $username = session('USERLOGIN');

            if (!$username && auth()->check()) {
                $user = auth()->user();

                $username = $user->username
                    ?? $user->name
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
                'marshalling-cic',
                'Marshalling CIC berhasil disimpan.',
                [
                    'id_job_slip' => $validated['idJobSlip'],
                    'no_cont' => $validated['nocont'],
                    'username' => $username,
                    'result' => $result,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Marshalling CIC berhasil disimpan.',
                'data' => $result,
            ]);
        } catch (ValidationException $e) {
            $this->logWarning(
                'marshalling-cic',
                'Validasi penyimpanan Marshalling CIC gagal.',
                [
                    'id_job_slip' => $request->input('idJobSlip'),
                    'no_cont' => $request->input('nocont'),
                    'errors' => $e->errors(),
                ]
            );

            return response()->json([
                'success' => false,
                'message' => 'Data Marshalling CIC tidak valid.',
                'errors' => $e->errors(),
            ], 422);
        } catch (Throwable $e) {
            $exceptionCode = (int) $e->getCode();

            $statusCode = in_array($exceptionCode, [400, 401, 403, 404, 409, 422], true)
                ? $exceptionCode
                : 500;

            $this->logError(
                'marshalling-cic',
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
                'message' => $statusCode === 500
                    ? 'Terjadi kesalahan saat menyimpan Marshalling CIC.'
                    : $e->getMessage(),
            ], $statusCode);
        }
    }
}