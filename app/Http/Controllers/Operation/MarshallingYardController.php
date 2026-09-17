<?php

namespace App\Http\Controllers\Operation;

use App\Http\Controllers\Controller;
use App\Services\MarshallingYardServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class MarshallingYardController extends Controller
{
    public function __construct(
        protected MarshallingYardServices $marshallingYardServices
    ) {
    }

    /**
     * Initial monitoring data.
     */
    public function indexData(): JsonResponse
    {
        try {
            $rows = $this->marshallingYardServices->getAllJobsYard();

            $view = view(
                'livewire.partials.marshallingyard.table',
                compact('rows')
            )->render();

            return response()->json([
                'data' => $view,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'message' => $e->getMessage()
                    ?: 'Terjadi kesalahan saat mengambil data Marshalling Yard.',
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
            ]);

            $rows = $this->marshallingYardServices->searchYard(
                trim($validated['no_cont'])
            );

            if (empty($rows)) {
                return response()->json([
                    'data' => view(
                        'livewire.partials.marshallingyard.table',
                        [
                            'rows' => [],
                        ]
                    )->render(),
                    'message' => 'Data Marshalling Yard tidak ditemukan.',
                ]);
            }

            $view = view(
                'livewire.partials.marshallingyard.table',
                compact('rows')
            )->render();

            return response()->json([
                'data' => $view,
            ]);
        } catch (Throwable $e) {
            $status = (int) $e->getCode();

            if ($status < 400 || $status > 599) {
                $status = 500;
            }

            return response()->json([
                'message' => $e->getMessage()
                    ?: 'Terjadi kesalahan saat mencari Marshalling Yard.',
            ], $status);
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

            $row = $this->marshallingYardServices->getDetailYard(
                $validated['id_job_slip']
            );

            if (!$row) {
                return response()->json([
                    'message' => 'Detail Job Slip Marshalling Yard tidak ditemukan.',
                ], 404);
            }

            $references = [
                'job_activities' =>
                    $this->marshallingYardServices->getJobActivity(),

                'equipments' =>
                    $this->marshallingYardServices->getEquipment(),

                'operators' =>
                    $this->marshallingYardServices->getOperator(),

                'trucks' =>
                    $this->marshallingYardServices->getTruck(),
            ];

            $view = view(
                'livewire.partials.marshallingyard.form',
                [
                    'row' => $row,
                    'references' => $references,

                    'job_activities' =>
                        $references['job_activities'],

                    'equipments' =>
                        $references['equipments'],

                    'operators' =>
                        $references['operators'],

                    'trucks' =>
                        $references['trucks'],
                ]
            )->render();

            return response()->json([
                'data' => $view,
            ]);
        } catch (Throwable $e) {
            $status = (int) $e->getCode();

            if ($status < 400 || $status > 599) {
                $status = 500;
            }

            return response()->json([
                'message' => $e->getMessage()
                    ?: 'Terjadi kesalahan saat mengambil detail Marshalling Yard.',
            ], $status);
        }
    }

    /**
     * Development phase:
     * validasi saja, belum melakukan persistence.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'idJobSlip' => [
                    'required',
                    'integer',
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

                'jenisPekerjaan1' => [
                    'nullable',
                ],

                'alat1' => [
                    'nullable',
                ],

                'operator1' => [
                    'nullable',
                ],

                'jenisPekerjaan2' => [
                    'nullable',
                ],

                'truck1' => [
                    'nullable',
                ],

                'operator2' => [
                    'nullable',
                ],

                'jenisPekerjaan3' => [
                    'nullable',
                ],

                'alat3' => [
                    'nullable',
                ],

                'operator3' => [
                    'nullable',
                ],
            ]);

            return response()->json([
                'message' => 'Data Marshalling Yard berhasil divalidasi.',
                'data' => $validated,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'message' => $e->getMessage()
                    ?: 'Terjadi kesalahan saat memproses Marshalling Yard.',
            ], 500);
        }
    }
}