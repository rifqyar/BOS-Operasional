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

    /**
     * Search container aktif untuk monitoring reefer.
     */
    public function search(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'no_cont' => ['required', 'string', 'max:50'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'kode'    => 422,
                'message' => 'Data pencarian tidak valid.',
                'data'    => $validator->errors(),
            ], 422);
        }

        $keyword = strtoupper(trim($validator->validated()['no_cont']));

        try {
            $monitoring = $this->service->getMonitoring($keyword);

            if (!$monitoring) {
                return response()->json([
                    'status'  => false,
                    'kode'    => 404,
                    'message' => "NO CONT : {$keyword} TIDAK DITEMUKAN / SUDAH UNPLUGIN.",
                    'data'    => null,
                ], 404);
            }

            $temprev = $this->service->getLastTemperature($keyword);

            $html = view(
                'livewire.partials.monitoringreefer.form',
                [
                    'nilai'   => $monitoring,
                    'temprev' => $temprev,
                ]
            )->render();

            $this->logInfo(
                $this->logChannel,
                'Search Monitoring Reefer berhasil.',
                [
                    'action'  => 'search',
                    'no_cont' => $keyword,
                    'status'  => true,
                ]
            );

            return response()->json([
                'status'  => true,
                'kode'    => 200,
                'message' => 'Data monitoring reefer berhasil ditemukan.',
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
                'status'  => false,
                'kode'    => 500,
                'message' => 'Terjadi kesalahan saat mencari data Monitoring Reefer.',
                'data'    => null,
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
            return response()->json([
                'status'  => false,
                'kode'    => 422,
                'message' => 'Data monitoring tidak valid.',
                'data'    => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $noCont = strtoupper(trim($validated['nomerkon']));
        $username = (string) $request->session()->get('USERLOGIN', '');

        if ($username === '') {
            return response()->json([
                'status'  => false,
                'kode'    => 401,
                'message' => 'Session pengguna tidak ditemukan. Silakan login kembali.',
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
                'status'  => true,
                'kode'    => 200,
                'message' => "NO CONT : {$noCont} MONITORING BERHASIL.",
                'data'    => $result,
            ]);
        } catch (\InvalidArgumentException $e) {
            $this->logInfo(
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
                'status'  => false,
                'kode'    => 422,
                'message' => $e->getMessage(),
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
                'status'  => false,
                'kode'    => 500,
                'message' => 'Terjadi kesalahan saat menyimpan Monitoring Reefer.',
                'data'    => null,
            ], 500);
        }
    }
}