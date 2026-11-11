<?php

namespace App\Http\Controllers\Operation;

use App\Http\Controllers\Controller;
use App\Http\Traits\LogTraits;
use App\Services\InspectionOutServices;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class InspectionOutController extends Controller
{
    use LogTraits;

    public function __construct(
        protected InspectionOutServices $inspectionOutService
    ) {
    }

    /**
     * Search Inspection Out.
     *
    * Data dibaca dari prod_new.
     * Jika container memenuhi kondisi, langsung tampilkan form.
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search_cont' => ['required', 'string', 'max:50'],
        ]);

        $noCont = strtoupper(trim($validated['search_cont']));

        try {
            $result = $this->inspectionOutService
                ->getInspectionData($noCont);

            if (($result['status'] ?? 0) !== 2) {
                return response()->json([
                    'status' => 0,
                    'kode' => 1,
                    'message' => $result['message']
                        ?? "WARNING ! NO CONT : {$noCont} NOT FOUND",
                ]);
            }

            $html = view(
                'livewire.partials.inspectionout.form',
                ['result' => $result]
            )->render();

            return response()->json([
                'status' => 2,
                'data' => $html,
            ]);
        } catch (Throwable $th) {
            $this->logError(
                'inspection-out',
                'Gagal mencari data Inspection Out',
                [
                    'no_container' => $noCont,
                    'error' => $th->getMessage(),
                ]
            );

            return response()->json([
                'status' => 0,
                'kode' => 1,
                'message' => 'Terjadi kesalahan saat mencari data Inspection Out.',
            ], 500);
        }
    }

    /**
     * Store Inspection Out.
     *
    * Validasi dan seluruh perubahan dilakukan pada prod_new.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nomercont' => ['required', 'string', 'max:50'],
            'ukuran' => ['nullable', 'string', 'max:50'],
            'optradio' => ['required', 'in:ada,tidak ada'],
            'noseal' => ['nullable', 'string', 'max:100'],
            'kondisi' => ['required', 'string', 'max:50'],
        ]);

        $noCont = strtoupper(trim($validated['nomercont']));

        $prod = DB::connection('prod_new');
        $transactionStarted = false;
        $stage = 'begin_transaction';

        try {
            $prod->beginTransaction();
            $transactionStarted = true;

            /*
             * Validasi ulang dari prod_new, di dalam transaksi agar
             * state yang diperiksa sama dengan state yang diubah.
             */
            $stage = 'validasi_inspection_out';
            $validation = $this->inspectionOutService
                ->validateInspectionOut($noCont, $validated['kondisi']);

            if (!$validation['valid']) {
                throw new Exception(
                    $validation['message'] ?? 'Container tidak memenuhi syarat Inspection Out.',
                    422
                );
            }

            $spkCont = $validation['spk_cont'];
            $spk = $validation['spk'];
            $delivery = $validation['delivery'];

            $noSpk = $spk->NO_SPK ?? null;

            if (blank($noSpk)) {
                throw new Exception(
                    'Nomor SPK tidak ditemukan untuk container tersebut.',
                    422
                );
            }

            $now = now();

            /*
             * Operator mengikuti session legacy USERLOGIN,
             * dengan fallback ke user Laravel yang sedang login.
             */
            $operator = session('USERLOGIN')
                ?? Auth::user()?->username
                ?? Auth::user()?->user_name
                ?? 'SYSTEM';

            /*
             * 1. UPDATE t_operation (prod_new)
             * Mengikuti key legacy: NO_CONT + NO_SPK.
             */
            $stage = 'update_t_operation';
            $operationUpdated = $prod
                ->table('t_operation')
                ->where('NO_CONT', $noCont)
                ->where('NO_SPK', $noSpk)
                ->update([
                    'WK_INSPECTOUT' => $now,
                ]);

            if ($operationUpdated === 0) {
                throw new Exception(
                    'Data t_operation tidak ditemukan atau gagal diperbarui.',
                    422
                );
            }

            /*
             * 2. UPDATE t_spk_cont (prod_new)
             * Status container menjadi 870.
             * Status 900 tidak boleh diubah.
             */
            $stage = 'update_t_spk_cont';
            $spkContUpdated = $prod
                ->table('t_spk_cont')
                ->where('ID', $spkCont->ID)
                ->where('NO_CONT', $noCont)
                ->whereIn('STATUS_CONT', ['450', '800', '850', '870'])
                ->where('STATUS_CONT', '<>', '900')
                ->update([
                    'STATUS_CONT' => '870',
                ]);

            if ($spkContUpdated === 0 && (string) $spkCont->STATUS_CONT !== '870') {
                throw new Exception(
                    'Status container tidak memenuhi syarat atau gagal diperbarui.',
                    422
                );
            }

            /*
             * 3. UPDATE t_op_delivery (prod_new)
             * Mengikuti field yang di-update oleh legacy set_inspect().
             */
            $stage = 'update_t_op_delivery';
            $deliveryUpdated = $prod
                ->table('t_op_delivery')
                ->where('ID', $delivery->ID)
                ->where('NO_CONT', $noCont)
                ->whereNull('WK_INSPECT')
                ->whereNull('WK_GATEOUT')
                ->update([
                    'KONDISI_CONT' => $validated['kondisi'],
                    'KONDISI_SEAL' => $validated['optradio'],
                    'OPERATOR_I' => $operator,
                    'NO_SEAL' => $validated['noseal'] ?? null,
                    'WK_INSPECT' => $now,
                ]);

            if ($deliveryUpdated === 0) {
                throw new Exception(
                    'Data delivery tidak ditemukan, sudah diproses, atau gagal diperbarui.',
                    422
                );
            }

            $stage = 'commit';
            $prod->commit();
            $transactionStarted = false;

            $this->logInfo(
                'inspection-out',
                'Inspection Out berhasil diproses',
                [
                    'no_container' => $noCont,
                    'no_spk' => $noSpk,
                    'operator' => $operator,
                    'kondisi' => $validated['kondisi'],
                    'kondisi_seal' => $validated['optradio'],
                    'no_seal' => $validated['noseal'] ?? null,
                    'stage' => 'selesai',
                ]
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Inspection Out berhasil diproses.',
                'data' => [
                    'no_cont' => $noCont,
                    'no_spk' => $noSpk,
                    'wk_inspect' => $now,
                ],
            ]);
        } catch (Throwable $th) {
            /*
             * Rollback hanya jika transaksi masih aktif.
             */
            try {
                if ($transactionStarted && $prod->transactionLevel() > 0) {
                    $prod->rollBack();
                }
            } catch (Throwable) {
                // Pertahankan exception utama.
            }

            $errorCode = in_array((int) $th->getCode(), [404, 422], true)
                ? (int) $th->getCode()
                : 500;

            $this->logError(
                'inspection-out',
                'Gagal memproses Inspection Out',
                [
                    'no_container' => $noCont,
                    'error' => $th->getMessage(),
                    'code' => $th->getCode(),
                    'stage' => $stage,
                ]
            );

            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
                    ?: 'Terjadi kesalahan saat memproses Inspection Out.',
                'data' => null,
            ], $errorCode);
        }
    }
}
