<?php

namespace App\Http\Controllers\Operation;

use App\Http\Controllers\Controller;
use App\Http\Traits\LogTraits;
use App\Services\OnChassisServices;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class OnChassisController extends Controller
{
    use LogTraits;

    private const LOG_CHANNEL = 'on_chassis';

    public function __construct(
        protected OnChassisServices $onChassisService
    ) {
    }

    /**
     * Search container.
     *
     * READ/VALIDASI: MySQL prod.
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search_ch' => ['required', 'string', 'max:50'],
        ]);

        $keyword = strtoupper(trim($validated['search_ch']));

        try {
            $container = $this->onChassisService->getContainer($keyword);

            if (!$container) {
                $this->logInfo(self::LOG_CHANNEL, 'Container On Chassis tidak ditemukan', [
                    'search_keyword' => $keyword,
                ]);

                return response()->json([
                    'status' => 'error',
                    'message' => "Container {$keyword} tidak ditemukan atau sudah On Chassis.",
                    'error' => null,
                ], 404);
            }

            $html = view(
                'livewire.partials.onchassis.form',
                [
                    'container' => $container,
                ]
            )->render();

            $this->logInfo(self::LOG_CHANNEL, 'Search container On Chassis berhasil', [
                'search_keyword' => $keyword,
                'no_container' => $container->NO_CONT ?? null,
                'no_spk' => $container->NO_SPK ?? null,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Data container berhasil ditemukan.',
                'data' => $html,
            ]);
        } catch (Throwable $th) {
            $this->logError(self::LOG_CHANNEL, 'Gagal mencari container On Chassis', [
                'error' => $th->getMessage(),
                'search_keyword' => $keyword,
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mencari container.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Proses On Chassis.
     *
     * READ/VALIDASI: MySQL prod.
     * UPDATE: Oracle prod_new.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nomerspk' => ['required', 'string', 'max:100'],
            'nomercont' => ['required', 'string', 'max:50'],
            'ukuran' => ['nullable', 'string', 'max:50'],
            'notruck' => ['nullable', 'string', 'max:100'],
            'lokasi' => ['nullable', 'string', 'max:100'],
        ]);

        $noSpk = trim($validated['nomerspk']);
        $noContainer = strtoupper(trim($validated['nomercont']));

        $oracle = DB::connection('prod_new');

        try {
            /*
             * 1. Validasi ulang dari database legacy.
             * Tidak melakukan update ke prod.
             */
            $delivery = $this->onChassisService->getContainerForProcess(
                $noContainer,
                $noSpk
            );

            if (!$delivery) {
                throw new Exception(
                    'Container tidak ditemukan atau sudah diproses On Chassis.',
                    404
                );
            }

            /*
             * 2. Ambil operator dan timestamp proses.
             */
            $user = Auth::user()?->username
                ?? Auth::user()?->user_name
                ?? session('USERLOGIN')
                ?? 'SYSTEM';

            $now = now();

            /*
             * 3. Mulai transaksi Oracle.
             */
            $oracle->beginTransaction();

            /*
             * 4. Update status container di Oracle.
             * Legacy mengecualikan STATUS_CONT = 900.
             */
            $oracle->table('t_spk_cont')
                ->where('NO_CONT', $noContainer)
                ->whereNotIn('STATUS_CONT', ['900'])
                ->update([
                    'STATUS_CONT' => '850',
                ]);

            /*
             * 5. Update t_op_delivery di Oracle.
             * Mengikuti legacy: berdasarkan NO_CONT.
             */
            $oracle->table('t_op_delivery')
                ->where('NO_CONT', $noContainer)
                ->update([
                    'OPERATOR_O' => $user,
                    'WK_CHASSIS' => $now,
                ]);

            /*
             * 6. Update t_operation di Oracle.
             * Mengikuti legacy: berdasarkan NO_CONT + NO_SPK.
             */
            $oracle->table('t_operation')
                ->where('NO_CONT', $noContainer)
                ->where('NO_SPK', $noSpk)
                ->update([
                    'WK_CHASSIS' => $now,
                ]);

            /*
             * 7. Commit transaksi Oracle.
             */
            $oracle->commit();

            $this->logInfo(self::LOG_CHANNEL, 'Berhasil memproses On Chassis', [
                'no_spk' => $noSpk,
                'no_container' => $noContainer,
                'operator' => $user,
                'wk_chassis' => $now->toDateTimeString(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'On Chassis berhasil diproses.',
                'data' => [
                    'no_spk' => $noSpk,
                    'no_container' => $noContainer,
                    'operator' => $user,
                    'wk_chassis' => $now->toDateTimeString(),
                ],
            ]);
        } catch (Throwable $th) {
            /*
             * Rollback hanya jika transaksi Oracle sudah dimulai.
             */
            if ($oracle->transactionLevel() > 0) {
                $oracle->rollBack();
            }

            $errorCode = (int) $th->getCode() === 404 ? 404 : 500;

            $this->logError(self::LOG_CHANNEL, 'Gagal memproses On Chassis', [
                'error' => $th->getMessage(),
                'no_spk' => $noSpk,
                'no_container' => $noContainer,
                'notruck' => $validated['notruck'] ?? null,
                'lokasi' => $validated['lokasi'] ?? null,
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
                    ?: 'Terjadi kesalahan saat memproses On Chassis.',
                'error' => $th->getMessage(),
            ], $errorCode);
        }
    }
}