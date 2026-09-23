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
     * Initial data Marshalling Yard.
     *
     * Default:
     * - 10 data per page
     * - maksimal 50 data per page
     */
    public function indexData(
        Request $request
    ): JsonResponse {
        try {

            $perPage = (int) $request->input(
                'per_page',
                10
            );

            $perPage = max(
                1,
                min($perPage, 50)
            );


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
                    'current_page' =>
                        $rows->currentPage(),

                    'last_page' =>
                        $rows->lastPage(),

                    'per_page' =>
                        $rows->perPage(),

                    'total' =>
                        $rows->total(),

                    'from' =>
                        $rows->firstItem(),

                    'to' =>
                        $rows->lastItem(),
                ],
            ]);

        } catch (Throwable $e) {

            report($e);


            $status = (int) $e->getCode();

            if (
                $status < 400 ||
                $status > 599
            ) {
                $status = 500;
            }


            return response()->json([
                'success' => false,

                'message' =>
                    $e->getMessage()
                    ?: 'Terjadi kesalahan saat mengambil data Marshalling Yard.',
            ], $status);
        }
    }


    /**
     * Search berdasarkan No Container.
     */
    public function search(
        Request $request
    ): JsonResponse {
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


            $keyword = strtoupper(
                trim($validated['no_cont'])
            );


            if ($keyword === '') {

                return response()->json([
                    'success' => false,
                    'message' =>
                        'Nomor Container wajib diisi.',
                ], 422);
            }


            $perPage = (int) (
                $validated['per_page']
                ?? 10
            );

            $perPage = max(
                1,
                min($perPage, 50)
            );


            $rows = $this->marshallingYardServices
                ->searchYard(
                    $keyword,
                    $perPage
                );


            if ($rows->isEmpty()) {

                $view = view(
                    'livewire.partials.marshallingyard.table',
                    [
                        'rows' => $rows,
                    ]
                )->render();


                return response()->json([
                    'success' => false,
                    'data' => $view,
                    'message' =>
                        'Data Marshalling Yard tidak ditemukan.',
                ], 404);
            }


            $view = view(
                'livewire.partials.marshallingyard.table',
                compact('rows')
            )->render();


            return response()->json([
                'success' => true,

                'data' => $view,

                'pagination' => [
                    'current_page' =>
                        $rows->currentPage(),

                    'last_page' =>
                        $rows->lastPage(),

                    'per_page' =>
                        $rows->perPage(),

                    'total' =>
                        $rows->total(),

                    'from' =>
                        $rows->firstItem(),

                    'to' =>
                        $rows->lastItem(),
                ],
            ]);

        } catch (Throwable $e) {

            report($e);


            $status = (int) $e->getCode();

            if (
                $status < 400 ||
                $status > 599
            ) {
                $status = 500;
            }


            return response()->json([
                'success' => false,

                'message' =>
                    $e->getMessage()
                    ?: 'Terjadi kesalahan saat mencari Marshalling Yard.',
            ], $status);
        }
    }


    /**
     * Detail ketika tombol PROSES diklik.
     */
    public function detail(
        Request $request
    ): JsonResponse {
        try {

            $validated = $request->validate([
                'id_job_slip' => [
                    'required',
                    'integer',
                ],
            ]);


            $row = $this->marshallingYardServices
                ->getDetailYard(
                    $validated['id_job_slip']
                );


            if (!$row) {

                return response()->json([
                    'message' =>
                        'Detail Job Slip Marshalling Yard tidak ditemukan.',
                ], 404);
            }


            $references = [
                'job_activities' =>
                    $this->marshallingYardServices
                        ->getJobActivity(),

                'equipments' =>
                    $this->marshallingYardServices
                        ->getEquipment(),

                'operators' =>
                    $this->marshallingYardServices
                        ->getOperator(),

                'trucks' =>
                    $this->marshallingYardServices
                        ->getTruck(),
            ];


            $view = view(
                'livewire.partials.marshallingyard.form',
                [
                    'row' =>
                        $row,

                    'references' =>
                        $references,

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
                'success' => true,
                'data' => $view,
            ]);

        } catch (Throwable $e) {

            report($e);


            $status = (int) $e->getCode();

            if (
                $status < 400 ||
                $status > 599
            ) {
                $status = 500;
            }


            return response()->json([
                'success' => false,

                'message' =>
                    $e->getMessage()
                    ?: 'Terjadi kesalahan saat mengambil detail Marshalling Yard.',
            ], $status);
        }
    }


    /**
     * Development phase:
     * validasi saja.
     *
     * Belum melakukan persistence.
     */
    public function store(
        Request $request
    ): JsonResponse {
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
                'success' => true,

                'message' =>
                    'Data Marshalling Yard berhasil divalidasi.',

                'data' =>
                    $validated,
            ]);

        } catch (Throwable $e) {

            report($e);


            return response()->json([
                'success' => false,

                'message' =>
                    $e->getMessage()
                    ?: 'Terjadi kesalahan saat memproses Marshalling Yard.',
            ], 500);
        }
    }
}