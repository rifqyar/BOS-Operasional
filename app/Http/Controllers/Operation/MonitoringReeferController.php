<?php

namespace App\Http\Controllers\Operation;

use App\Http\Controllers\Controller;
use App\Http\Traits\LogTraits;
use App\Services\MonitoringReeferServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class MonitoringReeferController extends Controller
{
    use LogTraits;

    private string $logChannel = 'monitoring-reefer';

    public function __construct(
        protected MonitoringReeferServices $service
    ) {
    }

    /** Pencarian awal daftar container, setara list_reefer() legacy. */
    public function search(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'no_cont' => ['required', 'string', 'max:50'],
        ]);

        if ($validator->fails()) {
            $this->logError(
                $this->logChannel,
                'Validasi pencarian Monitoring Reefer gagal.',
                [
                    'action' => 'search',
                    'no_cont' => $request->input('no_cont'),
                    'errors' => $validator->errors()->toArray(),
                ]
            );

            return response()->json([
                'status'  => 'error',
                'success' => false,
                'kode'    => 422,
                'message' => 'Data pencarian tidak valid.',
                'errors'  => $validator->errors(),
                'data'    => null,
            ], 422);
        }

        $keyword = strtoupper(trim($validator->validated()['no_cont']));

        try {
            $rows = $this->service->searchReefer($keyword);

            if (empty($rows)) {
                $this->logError(
                    $this->logChannel,
                    'Container tidak ditemukan pada pencarian Monitoring Reefer.',
                    [
                        'action' => 'search',
                        'no_cont' => $keyword,
                    ]
                );

                return response()->json([
                    'status'  => 'error',
                    'success' => false,
                    'kode'    => 404,
                    'message' => "NO CONT : {$keyword} TIDAK DITEMUKAN.",
                    'data'    => null,
                ], 404);
            }

            $html = view(
                'livewire.partials.monitoringreefer.table',
                [
                    'data' => $rows,
                ]
            )->render();

            $this->logInfo(
                $this->logChannel,
                'Pencarian container Monitoring Reefer berhasil.',
                [
                    'action'  => 'search',
                    'no_cont' => $keyword,
                    'status'  => true,
                ]
            );

            return response()->json([
                'status'  => 'success',
                'success' => true,
                'kode'    => 200,
                'message' => 'Container berhasil ditemukan.',
                'data'    => $html,
            ]);
        } catch (Throwable $e) {
            $this->logError(
                $this->logChannel,
                'Search Monitoring Reefer gagal.',
                [
                    'action'    => 'search',
                    'no_cont'   => $keyword,
                    'exception' => get_class($e),
                    'error'     => $e->getMessage(),
                ]
            );

            return response()->json([
                'status'  => 'error',
                'success' => false,
                'kode'    => 500,
                'message' => 'Terjadi kesalahan saat mencari data Monitoring Reefer.',
                'error'   => $e->getMessage(),
                'data'    => null,
            ], 500);
        }
    }

    /** Mengambil detail container aktif untuk form monitoring legacy. */
    public function detail(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'no_cont' => ['required', 'string', 'max:50'],
        ]);

        if ($validator->fails()) {
            $this->logError(
                $this->logChannel,
                'Validasi detail Monitoring Reefer gagal.',
                [
                    'action' => 'detail',
                    'no_cont' => $request->input('no_cont'),
                    'errors' => $validator->errors()->toArray(),
                ]
            );

            return response()->json([
                'status' => 'error',
                'success' => false,
                'kode' => 422,
                'message' => 'Nomor container tidak valid.',
                'errors' => $validator->errors(),
                'data' => null,
            ], 422);
        }

        $keyword = strtoupper(trim($validator->validated()['no_cont']));

        try {
            $monitoring = $this->service->getMonitoring($keyword);

            if (!$monitoring) {
                $this->logError(
                    $this->logChannel,
                    'Container tidak aktif untuk Monitoring Reefer.',
                    [
                        'action' => 'detail',
                        'no_cont' => $keyword,
                    ]
                );

                return response()->json([
                    'status' => 'error',
                    'success' => false,
                    'kode' => 404,
                    'message' => "NO CONT : {$keyword} SUDAH UNPLUGIN / TIDAK AKTIF MONITORING.",
                    'data' => null,
                ], 404);
            }

            $temprev = $this->service->getLastTemperature($keyword);
            $html = view(
                'livewire.partials.monitoringreefer.form',
                [
                    'nilai' => $monitoring,
                    'temprev' => $temprev,
                ]
            )->render();

            $this->logInfo(
                $this->logChannel,
                'Detail Monitoring Reefer berhasil diambil.',
                [
                    'action' => 'detail',
                    'no_cont' => $keyword,
                ]
            );

            return response()->json([
                'status' => 'success',
                'success' => true,
                'kode' => 200,
                'message' => 'Detail Monitoring Reefer berhasil diambil.',
                'data' => $html,
            ]);
        } catch (Throwable $e) {
            $this->logError(
                $this->logChannel,
                'Gagal mengambil detail Monitoring Reefer.',
                [
                    'action' => 'detail',
                    'no_cont' => $keyword,
                    'exception' => get_class($e),
                    'error' => $e->getMessage(),
                ]
            );

            return response()->json([
                'status' => 'error',
                'success' => false,
                'kode' => 500,
                'message' => 'Terjadi kesalahan saat mengambil detail Monitoring Reefer.',
                'error' => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    /**
     * Simpan hasil monitoring reefer.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nomerkon'    => ['required', 'string', 'max:50'],
            'temperature' => ['required', 'numeric'],
            'note'        => ['nullable', 'string', 'max:1000'],
            'w_plugin'    => ['nullable'],
        ]);

        if ($validator->fails()) {
            $this->logError(
                $this->logChannel,
                'Validasi penyimpanan Monitoring Reefer gagal.',
                [
                    'action' => 'store',
                    'no_cont' => $request->input('nomerkon'),
                    'errors' => $validator->errors()->toArray(),
                ]
            );

            return response()->json([
                'status'  => 'error',
                'success' => false,
                'kode'    => 422,
                'message' => 'Data monitoring tidak valid.',
                'errors'  => $validator->errors(),
                'data'    => null,
            ], 422);
        }

        $validated = $validator->validated();

        $noCont = strtoupper(trim($validated['nomerkon']));
        $user = $request->user();
        $username = trim((string) (
            data_get($user, 'USERLOGIN')
            ?? data_get($user, 'userlogin')
            ?? data_get($user, 'username')
            ?? data_get($user, 'user_name')
            ?? $request->session()->get('USERLOGIN')
            ?? $request->session()->get('userlogin')
            ?? ''
        ));

        if ($username === '') {
            $this->logError(
                $this->logChannel,
                'Penyimpanan Monitoring Reefer ditolak karena session pengguna tidak ditemukan.',
                [
                    'action' => 'store',
                    'no_cont' => $noCont,
                ]
            );

            return response()->json([
                'status'  => 'error',
                'success' => false,
                'kode'    => 401,
                'message' => 'Session pengguna tidak ditemukan. Silakan login kembali.',
                'error'   => 'Session pengguna tidak ditemukan.',
                'data'    => null,
            ], 401);
        }

        try {
            $result = $this->service->storeMonitoring(
                [
                    'no_cont'     => $noCont,
                    'temperature' => $validated['temperature'],
                    'w_plugin'    => $validated['w_plugin'] ?? null,
                    'note'        => $validated['note'] ?? null,
                ],
                $username
            );

            $this->logInfo(
                $this->logChannel,
                'Monitoring Reefer berhasil disimpan.',
                [
                    'action'      => 'store',
                    'no_cont'     => $noCont,
                    'temperature' => $validated['temperature'],
                    'operator'    => $username,
                    'status'      => true,
                    'data'        => $result,
                ]
            );

            return response()->json([
                'status'  => 'success',
                'success' => true,
                'kode'    => 200,
                'message' => "NO CONT : {$noCont} MONITORING BERHASIL.",
                'data'    => $result,
            ]);
        } catch (\InvalidArgumentException $e) {
            $this->logError(
                $this->logChannel,
                'Monitoring Reefer ditolak oleh validasi bisnis.',
                [
                    'action'  => 'store',
                    'no_cont' => $noCont,
                    'status'  => false,
                    'reason'  => $e->getMessage(),
                ]
            );

            return response()->json([
                'status'  => 'error',
                'success' => false,
                'kode'    => 422,
                'message' => $e->getMessage(),
                'error'   => $e->getMessage(),
                'data'    => null,
            ], 422);
        } catch (Throwable $e) {
            $this->logError(
                $this->logChannel,
                'Monitoring Reefer gagal disimpan.',
                [
                    'action'      => 'store',
                    'no_cont'     => $noCont,
                    'temperature' => $validated['temperature'],
                    'operator'    => $username,
                    'exception'   => get_class($e),
                    'error'       => $e->getMessage(),
                ]
            );

            return response()->json([
                'status'  => 'error',
                'success' => false,
                'kode'    => 500,
                'message' => 'Terjadi kesalahan saat menyimpan Monitoring Reefer.',
                'error'   => $e->getMessage(),
                'data'    => null,
            ], 500);
        }
    }
}
