<?php

namespace App\Http\Controllers\Operation;

use App\Http\Controllers\Controller;
use App\Http\Traits\LogTraits;
use App\Services\OnChassisServices;
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
    * READ/VALIDASI: prod_new.
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
                    'status' => 'info',
                    'message' => "Container {$keyword} tidak ditemukan atau sudah On Chassis.",
                    'data' => null,
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
                'data' => null,
            ], 500);
        }
    }

    /**
     * Proses On Chassis.
     *
    * Validasi dan update dilakukan pada prod_new.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nomerspk' => ['nullable', 'string', 'max:100'],
            'nomercont' => ['required', 'string', 'max:50'],
            'ukuran' => ['nullable', 'string', 'max:50'],
            'notruck' => ['nullable', 'string', 'max:100'],
            'lokasi' => ['nullable', 'string', 'max:100'],
        ]);

        $noContainer = strtoupper(trim($validated['nomercont']));
        $noSpk = trim((string) ($validated['nomerspk'] ?? ''));
        $prod = DB::connection('prod_new');
        $transactionStarted = false;
        $stage = 'begin_transaction';

        try {
            $prod->beginTransaction();
            $transactionStarted = true;

            $stage = 'validasi_container';
            $validation = $this->onChassisService->getContainerForProcess(
                $noContainer,
                $noSpk !== '' ? $noSpk : null
            );
            $delivery = $validation['delivery'];
            $spkContainer = $validation['spk_container'];
            $spk = $validation['spk'];

            /*
             * 2. Ambil operator dan timestamp proses.
             */
            $user = (string) (Auth::user()?->username
                ?? Auth::user()?->user_name
                ?? session('USERLOGIN')
                ?? 'SYSTEM');

            $now = now();

            $stage = 'update_t_spk_cont';
            $statusUpdated = $prod->table('t_spk_cont')
                ->where('ID', $spkContainer->ID)
                ->where('NO_CONT', $noContainer)
                ->where('STATUS_CONT', '<>', '900')
                ->update([
                    'STATUS_CONT' => '850',
                ]);
            if ($statusUpdated === 0 && (string) $spkContainer->STATUS_CONT !== '850') {
                throw new \RuntimeException('Status container gagal diperbarui untuk proses On Chassis.', 409);
            }

            $stage = 'update_t_op_delivery';
            $deliveryUpdated = $prod->table('t_op_delivery')
                ->where('ID', $delivery->ID)
                ->where('NO_CONT', $noContainer)
                ->whereNull('WK_CHASSIS')
                ->update([
                    'OPERATOR_O' => $user,
                    'WK_CHASSIS' => $now,
                ]);
            if ($deliveryUpdated === 0) {
                throw new \RuntimeException('Data Delivery tidak ditemukan atau sudah diproses On Chassis.', 409);
            }

            $stage = 'update_t_operation';
            $operationUpdated = $prod->table('t_operation')
                ->where('NO_CONT', $noContainer)
                ->where('NO_SPK', $spk->NO_SPK)
                ->update([
                    'WK_CHASSIS' => $now,
                ]);
            if ($operationUpdated === 0) {
                throw new \RuntimeException('Data operasi gagal diperbarui untuk On Chassis.', 409);
            }

            $stage = 'commit';
            $prod->commit();
            $transactionStarted = false;

            $this->logInfo(self::LOG_CHANNEL, 'On Chassis berhasil diproses.', [
                'no_spk' => $spk->NO_SPK,
                'no_container' => $noContainer,
                'operator' => $user,
                'wk_chassis' => $now->toDateTimeString(),
                'stage' => 'selesai',
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'On Chassis berhasil diproses.',
                'data' => [
                    'no_spk' => $spk->NO_SPK,
                    'no_container' => $noContainer,
                    'operator' => $user,
                    'wk_chassis' => $now->toDateTimeString(),
                ],
            ]);
        } catch (Throwable $th) {
            try {
                if ($transactionStarted && $prod->transactionLevel() > 0) {
                    $prod->rollBack();
                }
            } catch (Throwable) {
                // Pertahankan exception utama agar tetap tercatat.
            }

            $errorCode = in_array((int) $th->getCode(), [404, 409, 422], true)
                ? (int) $th->getCode()
                : 500;

            $this->logError(self::LOG_CHANNEL, 'Gagal memproses On Chassis', [
                'error' => $th->getMessage(),
                'no_spk' => $noSpk,
                'no_container' => $noContainer,
                'notruck' => $validated['notruck'] ?? null,
                'lokasi' => $validated['lokasi'] ?? null,
                'stage' => $stage,
                'code' => $th->getCode(),
            ]);

            return response()->json([
                'status' => $errorCode === 500 ? 'error' : 'info',
                'message' => $th->getMessage()
                    ?: 'Terjadi kesalahan saat memproses On Chassis.',
                'data' => null,
            ], $errorCode);
        }
    }
}
