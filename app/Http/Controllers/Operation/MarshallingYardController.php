<?php

namespace App\Http\Controllers\Operation;

use App\Http\Controllers\Controller;
use App\Http\Traits\LogTraits;
use App\Services\MarshallingYardServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;

class MarshallingYardController extends Controller
{
    use LogTraits;

    public function __construct(
        protected MarshallingYardServices $marshallingYardServices
    ) {
    }

    /**
     * Initial data Marshalling Yard.
     *
     * Default:
     * - 10 data per page
     * - maksimal 50 data per page
     */
    public function indexData(Request $request): JsonResponse
    {
        try {
            $perPage = (int) $request->input('per_page', 10);
            $perPage = max(1, min($perPage, 50));

            $rows = $this->marshallingYardServices
                ->getAllJobsYard($perPage);

            $view = view(
                'livewire.partials.marshallingyard.table',
                compact('rows')
            )->render();

            return response()->json([
                'success' => true,
                'data' => $view,
                'pagination' => [
                    'current_page' => $rows->currentPage(),
                    'last_page' => $rows->lastPage(),
                    'per_page' => $rows->perPage(),
                    'total' => $rows->total(),
                    'from' => $rows->firstItem(),
                    'to' => $rows->lastItem(),
                ],
            ]);
        } catch (Throwable $e) {
            $this->logInfo(
                'stack',
                'Gagal mengambil data Marshalling Yard.',
                [
                    'error' => $e->getMessage(),
                ]
            );

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data Marshalling Yard.',
            ], 500);
        }
    }

    /**
     * Search berdasarkan No Container.
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'no_cont' => [
                    'required',
                    'string',
                    'max:20',
                ],
                'page' => [
                    'nullable',
                    'integer',
                    'min:1',
                ],
                'per_page' => [
                    'nullable',
                    'integer',
                    'min:1',
                    'max:50',
                ],
            ]);

            $keyword = strtoupper(trim($validated['no_cont']));

            if ($keyword === '') {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor Container wajib diisi.',
                ], 422);
            }

            $perPage = (int) ($validated['per_page'] ?? 10);
            $perPage = max(1, min($perPage, 50));

            $rows = $this->marshallingYardServices
                ->searchYard($keyword, $perPage);

            $view = view(
                'livewire.partials.marshallingyard.table',
                compact('rows')
            )->render();

            if ($rows->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'data' => $view,
                    'message' => 'Data Marshalling Yard tidak ditemukan.',
                    'pagination' => [
                        'current_page' => $rows->currentPage(),
                        'last_page' => $rows->lastPage(),
                        'per_page' => $rows->perPage(),
                        'total' => $rows->total(),
                        'from' => $rows->firstItem(),
                        'to' => $rows->lastItem(),
                    ],
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $view,
                'pagination' => [
                    'current_page' => $rows->currentPage(),
                    'last_page' => $rows->lastPage(),
                    'per_page' => $rows->perPage(),
                    'total' => $rows->total(),
                    'from' => $rows->firstItem(),
                    'to' => $rows->lastItem(),
                ],
            ]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            $this->logInfo(
                'stack',
                'Gagal mencari Marshalling Yard.',
                [
                    'error' => $e->getMessage(),
                ]
            );

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencari Marshalling Yard.',
            ], 500);
        }
    }

    /**
     * Detail ketika tombol PROSES diklik.
     */
    public function detail(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'id_job_slip' => [
                    'required',
                    'integer',
                ],
            ]);

            $row = $this->marshallingYardServices
                ->getDetailYard($validated['id_job_slip']);

            if (!$row) {
                return response()->json([
                    'success' => false,
                    'message' => 'Detail Job Slip Marshalling Yard tidak ditemukan.',
                ], 404);
            }

            $references = [
                'job_activities' => $this->marshallingYardServices
                    ->getJobActivity(),

                'equipments' => $this->marshallingYardServices
                    ->getEquipment(),

                'operators' => $this->marshallingYardServices
                    ->getOperator(),

                'trucks' => $this->marshallingYardServices
                    ->getTruck(),
            ];

            $view = view(
                'livewire.partials.marshallingyard.form',
                [
                    'row' => $row,
                    'references' => $references,
                    'job_activities' => $references['job_activities'],
                    'equipments' => $references['equipments'],
                    'operators' => $references['operators'],
                    'trucks' => $references['trucks'],
                ]
            )->render();

            return response()->json([
                'success' => true,
                'data' => $view,
            ]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            $this->logInfo(
                'stack',
                'Gagal mengambil detail Marshalling Yard.',
                [
                    'id_job_slip' => $request->input('id_job_slip'),
                    'error' => $e->getMessage(),
                ]
            );

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil detail Marshalling Yard.',
            ], 500);
        }
    }

    /**
     * Menyimpan proses Marshalling Yard.
     */
    public function store(Request $request): JsonResponse
    {
        /*
         * Validasi di luar try-catch.
         * Laravel akan menangani kegagalan validasi sebagai HTTP 422.
         */
        $validated = $request->validate([
            'idJobSlip' => [
                'required',
                'integer',
            ],

            'spk' => [
                'required',
                'in:450',
            ],

            'nocont' => [
                'required',
                'string',
                'max:20',
            ],

            'ukrcont' => [
                'nullable',
                'string',
                'max:10',
            ],

            'lokaw' => [
                'nullable',
                'string',
                'max:50',
            ],

            'lokak' => [
                'required',
                'string',
                'max:50',
            ],

            'job' => [
                'required',
                'string',
                'max:50',
            ],

            'respon' => [
                'nullable',
                'string',
                'max:50',
            ],

            'note' => [
                'nullable',
                'string',
                'max:500',
            ],

            'fumigasi' => [
                'nullable',
            ],

            /*
             * Nama field mengikuti form legacy.
             */
            'jenispekerjaan1' => [
                'nullable',
                'integer',
            ],

            'alat1' => [
                'nullable',
                'string',
                'max:50',
            ],

            'operator1' => [
                'nullable',
                'string',
                'max:50',
            ],

            'jenispekerjaan2' => [
                'nullable',
                'integer',
            ],

            'truck1' => [
                'nullable',
                'string',
                'max:50',
            ],

            'operator2' => [
                'nullable',
                'string',
                'max:50',
            ],

            'jenispekerjaan3' => [
                'nullable',
                'integer',
            ],

            'alat3' => [
                'nullable',
                'string',
                'max:50',
            ],

            'operator3' => [
                'nullable',
                'string',
                'max:50',
            ],
        ]);

        try {
            $result = $this->marshallingYardServices
                ->storeMarshallingYard($validated);

            if (!$result['success']) {
                $statusCode = match ($result['status']) {
                    'not_found',
                    'spk_not_found',
                    'container_type_not_found' => 404,

                    'inspection_not_done',
                    'invalid_location' => 422,

                    default => 400,
                };

                return response()->json([
                    'success' => false,
                    'status' => $result['status'],
                    'message' => $result['message'],
                ], $statusCode);
            }

            return response()->json([
                'success' => true,
                'status' => 'success',
                'message' => $result['message'],
            ], 200);
        } catch (Throwable $e) {
            $this->logInfo(
                'stack',
                'Gagal menyimpan Marshalling Yard.',
                [
                    'id_job_slip' => $validated['idJobSlip'],
                    'nocont' => $validated['nocont'],
                    'error' => $e->getMessage(),
                ]
            );

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan Marshalling Yard.',
            ], 500);
        }
    }
}