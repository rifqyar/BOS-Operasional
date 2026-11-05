<?php
namespace App\Http\Controllers\Operation;

use App\Http\Controllers\Controller;
use App\Http\Traits\LogTraits;
use App\Services\MarshallingYardServices;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
     * Mengambil daftar Job Slip Marshalling Yard.
     */
    public function indexData(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'page' => ['nullable', 'integer', 'min:1'],
                'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
            ]);

            $perPage = (int) ($validated['per_page'] ?? 10);

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
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            $this->logError(
                'marshalling-yard',
                'Gagal mengambil daftar Marshalling Yard.',
                ['error' => $e->getMessage()]
            );

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data Marshalling Yard.',
            ], 500);
        }
    }

    /**
     * Mencari data berdasarkan nomor kontainer.
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'no_cont' => ['required', 'string', 'max:20'],
                'page' => ['nullable', 'integer', 'min:1'],
                'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
            ]);

            $keyword = strtoupper(trim($validated['no_cont']));

            if ($keyword === '') {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor kontainer wajib diisi.',
                ], 422);
            }

            $perPage = (int) ($validated['per_page'] ?? 10);

            $rows = $this->marshallingYardServices->searchYard(
                $keyword,
                $perPage
            );

            $view = view(
                'livewire.partials.marshallingyard.table',
                compact('rows')
            )->render();

            $pagination = [
                'current_page' => $rows->currentPage(),
                'last_page' => $rows->lastPage(),
                'per_page' => $rows->perPage(),
                'total' => $rows->total(),
                'from' => $rows->firstItem(),
                'to' => $rows->lastItem(),
            ];

            if ($rows->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'data' => $view,
                    'message' => 'Data Marshalling Yard tidak ditemukan.',
                    'pagination' => $pagination,
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $view,
                'pagination' => $pagination,
            ]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            $this->logError(
                'marshalling-yard',
                'Gagal mencari data Marshalling Yard.',
                [
                    'no_cont' => $request->input('no_cont'),
                    'error' => $e->getMessage(),
                ]
            );

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencari data Marshalling Yard.',
            ], 500);
        }
    }

    /**
     * Mengambil detail untuk form proses Marshalling Yard.
     */
    public function detail(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'id_job_slip' => ['required', 'integer', 'min:1'],
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
        } catch (Exception $e) {
            $this->logError(
                'marshalling-yard',
                'Gagal mengambil detail Marshalling Yard.',
                [
                    'id_job_slip' => $request->input('id_job_slip'),
                    'error' => $e->getMessage(),
                ]
            );

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil detail Marshalling Yard.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Menyimpan proses Marshalling Yard.
     *
     * Status container diperiksa dari tabel referensi pada service.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'idJobSlip' => ['required', 'integer', 'min:1'],
            'nocont' => ['required', 'string', 'max:20'],
            'ukrcont' => ['nullable', 'string', 'max:10'],
            'lokaw' => ['nullable', 'string', 'max:50'],
            'lokak' => ['required', 'string', 'max:50'],
            'job' => ['required', 'string', 'max:50'],
            'respon' => ['nullable', 'string', 'max:50'],
            'note' => ['nullable', 'string', 'max:500'],

            /*
             * Field aktivitas mengikuti form yang sudah ada.
             */
            'jenispekerjaan1' => ['nullable', 'integer'],
            'alat1' => ['nullable', 'string', 'max:50'],
            'operator1' => ['nullable', 'string', 'max:50'],

            'jenispekerjaan2' => ['nullable', 'integer'],
            'truck1' => ['nullable', 'string', 'max:50'],
            'operator2' => ['nullable', 'string', 'max:50'],

            'jenispekerjaan3' => ['nullable', 'integer'],
            'alat3' => ['nullable', 'string', 'max:50'],
            'operator3' => ['nullable', 'string', 'max:50'],
        ]);

        try {
            $username = (string) (Auth::user()?->username
                ?? Auth::user()?->user_name
                ?? session('USERLOGIN', ''));
            $result = $this->marshallingYardServices->storeMarshallingYard($validated, $username);

            return response()->json([
                'status' => 'success',
                'message' => $result['message'],
                'data' => $result['data'],
            ]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            $statusCode = (int) $e->getCode();
            $statusCode = in_array($statusCode, [404, 409, 422], true) ? $statusCode : 500;

            return response()->json([
                'status' => $statusCode === 422 ? 'info' : 'error',
                'message' => $statusCode === 500
                    ? 'Terjadi kesalahan saat menyimpan Marshalling Yard.'
                    : $e->getMessage(),
                'data' => null,
            ], $statusCode);
        }
    }
}
