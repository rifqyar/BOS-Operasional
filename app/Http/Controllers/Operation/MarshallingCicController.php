<?php

namespace App\Http\Controllers\Operation;

use App\Http\Controllers\Controller;
use App\Http\Traits\LogTraits;
use App\Services\MarshallingCicService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use RuntimeException;
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

            if (checkHoldP2($keyword, 'prod_new')) {
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
            // 1. VALIDASI INPUT
            $validated = $request->validate([
                'idJobSlip' => [
                    'bail',
                    'required',
                    'integer',
                    'min:1',
                ],

                'nocont' => [
                    'bail',
                    'required',
                    'string',
                    'max:20',
                ],

                'ukrcont' => [
                    'nullable',
                    'string',
                    'max:20',
                ],

                'lokaw' => [
                    'nullable',
                    'string',
                    'max:50',
                ],

                'lokak' => [
                    'bail',
                    'required',
                    'string',
                    'max:50',
                ],

                'job' => [
                    'bail',
                    'required',
                    'string',
                    'max:100',
                ],

                'note' => [
                    'nullable',
                    'string',
                    'max:500',
                ],

                'fumigasi' => [
                    'nullable',
                    'string',
                    'max:10',
                ],

                'respon' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                /*
                 * PEKERJAAN 1
                 * Jika jenis pekerjaan = 0 (Tidak Ada Aktivitas),
                 * alat dan operator boleh kosong.
                 * Jika memilih pekerjaan lain, keduanya wajib diisi.
                 */
                'jenispekerjaan1' => [
                    'nullable',
                    'string',
                    'max:50',
                ],

                'alat1' => [
                    'nullable',
                    'string',
                    'max:50',
                    Rule::requiredIf(
                        fn () => $request->filled('jenispekerjaan1')
                            && (string) $request->input('jenispekerjaan1') !== '0'
                    ),
                ],

                'operator1' => [
                    'nullable',
                    'string',
                    'max:100',
                    Rule::requiredIf(
                        fn () => $request->filled('jenispekerjaan1')
                            && (string) $request->input('jenispekerjaan1') !== '0'
                    ),
                ],

                /*
                 * PEKERJAAN 2
                 * Jika jenis pekerjaan = 0 (Tidak Ada Aktivitas),
                 * truk dan operator boleh kosong.
                 * Jika memilih pekerjaan lain, keduanya wajib diisi.
                 */
                'jenispekerjaan2' => [
                    'nullable',
                    'string',
                    'max:50',
                ],

                'truck1' => [
                    'nullable',
                    'string',
                    'max:50',
                    Rule::requiredIf(
                        fn () => $request->filled('jenispekerjaan2')
                            && (string) $request->input('jenispekerjaan2') !== '0'
                    ),
                ],

                'operator2' => [
                    'nullable',
                    'string',
                    'max:100',
                    Rule::requiredIf(
                        fn () => $request->filled('jenispekerjaan2')
                            && (string) $request->input('jenispekerjaan2') !== '0'
                    ),
                ],

                /*
                 * PEKERJAAN 3
                 * Jika jenis pekerjaan = 0 (Tidak Ada Aktivitas),
                 * alat dan operator boleh kosong.
                 * Jika memilih pekerjaan lain, keduanya wajib diisi.
                 */
                'jenispekerjaan3' => [
                    'nullable',
                    'string',
                    'max:50',
                ],

                'alat3' => [
                    'nullable',
                    'string',
                    'max:50',
                    Rule::requiredIf(
                        fn () => $request->filled('jenispekerjaan3')
                            && (string) $request->input('jenispekerjaan3') !== '0'
                    ),
                ],

                'operator3' => [
                    'nullable',
                    'string',
                    'max:100',
                    Rule::requiredIf(
                        fn () => $request->filled('jenispekerjaan3')
                            && (string) $request->input('jenispekerjaan3') !== '0'
                    ),
                ],
            ], [
                'idJobSlip.required' => 'ID Job Slip wajib tersedia.',
                'nocont.required' => 'Nomor kontainer wajib tersedia.',
                'lokak.required' => 'Lokasi akhir wajib diisi.',
                'job.required' => 'Pekerjaan wajib tersedia.',

                'alat1.required' => 'Alat untuk Jenis Pekerjaan 1 wajib dipilih.',
                'operator1.required' => 'Operator untuk Jenis Pekerjaan 1 wajib diisi.',

                'truck1.required' => 'Truk untuk Jenis Pekerjaan 2 wajib dipilih.',
                'operator2.required' => 'Operator untuk Jenis Pekerjaan 2 wajib diisi.',

                'alat3.required' => 'Alat untuk Jenis Pekerjaan 3 wajib dipilih.',
                'operator3.required' => 'Operator untuk Jenis Pekerjaan 3 wajib diisi.',
            ]);

            // 2. VALIDASI SESSION LOGIN
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
                    'status' => 'error',
                    'message' => 'Session login tidak ditemukan.',
                    'data' => null,
                    'errors' => [
                        'session' => [
                            'Username tidak ditemukan. Silakan login kembali.',
                        ],
                    ],
                ], 401);
            }

            // 3. PROSES PENYIMPANAN
            $result = $this->marshallingCicService->storeMarshallingCic(
                $validated,
                (string) $username
            );

            // 4. LOG BERHASIL
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
                'status' => 'success',
                'message' => 'Marshalling CIC berhasil disimpan.',
                'data' => $result,
            ], 200);

        } catch (ValidationException $e) {
            // 5. ERROR VALIDASI
            $this->logError(
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
                'status' => 'error',
                'message' => 'Data Marshalling CIC belum lengkap atau tidak valid.',
                'errors' => $e->errors(),
                'data' => null,
            ], 422);

        } catch (RuntimeException $e) {
            $this->logError(
                'marshalling-cic',
                'Validasi bisnis Marshalling CIC gagal.',
                [
                    'id_job_slip' => $request->input('idJobSlip'),
                    'no_cont' => $request->input('nocont'),
                    'username' => session('USERLOGIN'),
                    'error' => $e->getMessage(),
                ]
            );

            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => $e->getMessage(),
                'error' => $e->getMessage(),
                'data' => null,
            ], 422);
        } catch (Throwable $e) {
            // 6. LOG DETAIL ERROR UNTUK DEBUGGING
            $this->logError(
                'marshalling-cic',
                'Marshalling CIC gagal disimpan.',
                [
                    'id_job_slip' => $request->input('idJobSlip'),
                    'no_cont' => $request->input('nocont'),
                    'username' => session('USERLOGIN'),
                    'exception' => get_class($e),
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'code' => $e->getCode(),
                    'trace' => $e->getTraceAsString(),
                ]
            );

            // 7. ERROR LAIN: JANGAN TAMPILKAN DETAIL DATABASE KE PENGGUNA
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan Marshalling CIC. ' .
                    'Silakan periksa data dan coba kembali.',
                'data' => null,
            ], 500);
        }
    }
}
