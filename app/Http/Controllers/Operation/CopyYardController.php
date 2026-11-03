<?php

namespace App\Http\Controllers\Operation;

use App\Http\Controllers\Controller;
use App\Http\Traits\LogTraits;
use App\Services\CopyYardServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class CopyYardController extends Controller
{
    use LogTraits;

    private CopyYardServices $copyYardServices;

    private string $channel = 'copyyard';

    public function __construct(CopyYardServices $copyYardServices)
    {
        $this->copyYardServices = $copyYardServices;
    }

    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'search_cont' => ['required', 'string', 'max:30'],
        ]);

        $keyword = strtoupper(trim($request->input('search_cont')));

        try {
            $container = $this->copyYardServices->getContainer($keyword);

            if (!$container) {
                return response()->json([
                    'status' => 0,
                    'kode' => 2,
                    'message' => 'Container tidak ditemukan atau status tidak memenuhi syarat.',
                ], 404);
            }

            $userGroup = session('KD_GROUP');

            $html = view('livewire.partials.copyyard.form', [
                'container' => $container,
                'usernya' => $userGroup,
            ])->render();

            $this->logInfo($this->channel, 'Search container CopyYard berhasil.', [
                'keyword' => $keyword,
                'no_cont' => $container->NO_CONT,
            ]);

            return response()->json([
                'status' => 1,
                'message' => 'Data container ditemukan.',
                'data' => $html,
            ]);
        } catch (Throwable $e) {
            $this->logError($this->channel, 'Search container CopyYard gagal.', [
                'keyword' => $keyword,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 0,
                'message' => 'Terjadi kesalahan saat mencari container.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nomerkon' => ['required', 'string', 'max:30'],
            'mySelect' => ['required', 'in:satu,dua'],
            'lokbar' => ['nullable', 'string', 'max:30'],
        ]);

        $noContainer = strtoupper(trim($validated['nomerkon']));
        $action = $validated['mySelect'];

        // Untuk action dua, destination tidak diambil dari request.
        $destination = $action === 'dua'
            ? 'SAMPAH'
            : strtoupper(trim($validated['lokbar'] ?? ''));

        if ($action === 'satu' && $destination === '') {
            return response()->json([
                'status' => 'error',
                'message' => 'Lokasi tujuan wajib diisi.',
            ], 422);
        }

        try {
            DB::connection('prod_new')->beginTransaction();

            // Re-read seluruh data penting dari database legacy.
            $container = $this->copyYardServices->getContainerForUpdate($noContainer);

            if (!$container) {
                DB::connection('prod_new')->rollBack();

                return response()->json([
                    'status' => 'error',
                    'message' => 'Container tidak ditemukan atau status sudah berubah.',
                ], 404);
            }

            $moveData = $this->copyYardServices->buildMoveData(
                $container,
                $action,
                $destination
            );

            // Seluruh write dilakukan ke Oracle prod_new.
            $this->copyYardServices->moveContainer($moveData);

            DB::connection('prod_new')->commit();

            $this->logInfo($this->channel, 'Proses CopyYard berhasil.', [
                'no_cont' => $noContainer,
                'action' => $action,
                'destination' => $destination,
                'status_before' => $container->STATUS_CONT,
                'status_after' => $moveData['status_cont'],
            ]);

            return response()->json([
                'status' => 'success',
                'message' => $action === 'dua'
                    ? 'Container berhasil dipindahkan ke blok sampah.'
                    : 'Perpindahan container berhasil disimpan.',
                'cont' => $noContainer,
            ]);
        } catch (Throwable $e) {
            if (DB::connection('prod_new')->transactionLevel() > 0) {
                DB::connection('prod_new')->rollBack();
            }

            $code = (int) $e->getCode();
            $httpCode = in_array($code, [4, 5, 7, 8], true) ? 422 : 500;

            $this->logError($this->channel, 'Proses CopyYard gagal.', [
                'no_cont' => $noContainer,
                'action' => $action,
                'destination' => $destination,
                'error' => $e->getMessage(),
                'code' => $code,
            ]);

            return response()->json([
                'status' => 'error',
                'kode' => $code,
                'message' => $e->getMessage() ?: 'Proses CopyYard gagal.',
            ], $httpCode);
        }
    }
}