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
     * Data dibaca dari database legacy (prod).
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
     * Validasi/read  : prod (legacy MySQL)
     * Update         : prod_new (Oracle)
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

        $prod = DB::connection('prod');
        $prodNew = DB::connection('prod_new');

        try {
            /*
             * Validasi ulang dari legacy DB.
             * Jangan mengandalkan data dari hasil pencarian saja.
             */
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
             * Mulai transaksi pada masing-masing koneksi.
             * Transaksi MySQL dan Oracle berjalan terpisah.
             */
            $prod->beginTransaction();
            $prodNew->beginTransaction();

            /*
             * 1. UPDATE t_operation (Oracle)
             * Mengikuti key legacy: NO_CONT + NO_SPK.
             */
            $operationUpdated = $prodNew
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
             * 2. UPDATE t_spk_cont (Oracle)
             * Status container menjadi 870.
             * Status 900 tidak boleh diubah.
             */
            $spkContUpdated = $prodNew
                ->table('t_spk_cont')
                ->where('NO_CONT', $noCont)
                ->whereIn('STATUS_CONT', ['450', '800', '850', '870'])
                ->where('STATUS_CONT', '<>', '900')
                ->update([
                    'STATUS_CONT' => '870',
                ]);

            if ($spkContUpdated === 0) {
                throw new Exception(
                    'Status container tidak memenuhi syarat atau gagal diperbarui.',
                    422
                );
            }

            /*
             * 3. UPDATE t_op_delivery (Oracle)
             * Mengikuti field yang di-update oleh legacy set_inspect().
             */
            $deliveryUpdated = $prodNew
                ->table('t_op_delivery')
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

            /*
             * Commit kedua koneksi.
             */
            $prod->commit();
            $prodNew->commit();

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
                ]
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Inspection Out berhasil diproses.',
                'cont' => [
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
                if ($prod->transactionLevel() > 0) {
                    $prod->rollBack();
                }
            } catch (Throwable) {
                // Pertahankan exception utama.
            }

            try {
                if ($prodNew->transactionLevel() > 0) {
                    $prodNew->rollBack();
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
                ]
            );

            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
                    ?: 'Terjadi kesalahan saat memproses Inspection Out.',
                'error' => $th->getMessage(),
            ], $errorCode);
        }
    }
}