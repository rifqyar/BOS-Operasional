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
     *
     * Pagination:
     * - default 10 data
     * - minimum 1
     * - maximum 50
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


            $jobs =
                $this->marshallingCicService
                    ->getAllJobs($perPage);


            $view = view(
                'livewire.partials.marshallingcic.table',
                compact('jobs')
            )->render();


            return response()->json([

                'success' => true,

                'data' => $view,

                /*
                 * Pagination metadata
                 */
                'pagination' => [

                    'current_page' =>
                        $jobs->currentPage(),

                    'last_page' =>
                        $jobs->lastPage(),

                    'per_page' =>
                        $jobs->perPage(),

                    'total' =>
                        $jobs->total(),

                    'from' =>
                        $jobs->firstItem(),

                    'to' =>
                        $jobs->lastItem(),

                ],
            ]);

        } catch (Throwable $e) {

            report($e);


            $statusCode =
                (int) $e->getCode();


            if (
                $statusCode < 400 ||
                $statusCode > 599
            ) {
                $statusCode = 500;
            }


            return response()->json([

                'success' => false,

                'message' =>
                    $e->getMessage()
                    ?: 'Terjadi kesalahan saat mengambil data Marshalling CIC.',

            ], $statusCode);
        }
    }


    /**
     * Search container.
     *
     * Search tetap menggunakan hasil biasa,
     * tidak dipagination.
     */
    public function search(
        Request $request
    ): JsonResponse {

        try {

            $validated =
                $request->validate([

                    'no_cont' => [
                        'required',
                        'string',
                        'max:20',
                    ],

                ]);


            $keyword =
                strtoupper(
                    trim(
                        $validated['no_cont']
                    )
                );


            if ($keyword === '') {

                throw new Exception(
                    'Nomor Container wajib diisi.',
                    422
                );
            }


            /*
             * Check Hold P2
             */
            $checkHoldP2 =
                checkHoldP2(
                    $keyword
                );


            if ($checkHoldP2) {

                throw new Exception(
                    'Container on Hold P2, Harap release Container terlebih dahulu',
                    500
                );
            }


            /*
             * Search
             */
            $jobs =
                $this->marshallingCicService
                    ->search($keyword);


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

                'success' => true,

                'data' => $view,

            ]);

        } catch (Throwable $e) {

            report($e);


            $statusCode =
                (int) $e->getCode();


            if (
                $statusCode < 400 ||
                $statusCode > 599
            ) {
                $statusCode = 500;
            }


            return response()->json([

                'success' => false,

                'message' =>
                    $e->getMessage()
                    ?: 'Terjadi kesalahan saat mencari Container.',

            ], $statusCode);
        }
    }


    /**
     * Detail job.
     */
    public function detail(
        Request $request
    ): JsonResponse {

        try {

            $validated =
                $request->validate([

                    'id_job_slip' => [
                        'required',
                        'integer',
                    ],

                ]);


            $row =
                $this->marshallingCicService
                    ->getDetail(
                        (int) $validated['id_job_slip']
                    );


            if (!$row) {

                throw new Exception(
                    'Detail Job Marshalling CIC tidak ditemukan.',
                    404
                );
            }


            /*
             * Master data untuk form detail.
             */
            $jobActivities =
                $this->marshallingCicService
                    ->getJobActivities();


            $equipments =
                $this->marshallingCicService
                    ->getEquipments();


            $trucks =
                $this->marshallingCicService
                    ->getTrucks();


            $operators =
                $this->marshallingCicService
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

                'success' => true,

                'data' => $view,

            ]);

        } catch (Throwable $e) {

            report($e);


            $statusCode =
                (int) $e->getCode();


            if (
                $statusCode < 400 ||
                $statusCode > 599
            ) {
                $statusCode = 500;
            }


            return response()->json([

                'success' => false,

                'message' =>
                    $e->getMessage()
                    ?: 'Terjadi kesalahan saat mengambil detail.',

            ], $statusCode);
        }
    }


    /**
     * Read-only phase.
     */
    public function store(
        Request $request
    ): JsonResponse {

        return response()->json([

            'success' => true,

            'message' =>
                'Marshalling CIC saat ini dalam mode read-only. Data tidak disimpan.',

        ]);
    }
}