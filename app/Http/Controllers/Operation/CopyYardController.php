<?php

namespace App\Http\Controllers\Operation;

use App\Http\Controllers\Controller;
use App\Http\Traits\LogTraits;
use App\Services\CopyYardServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                    'status' => 'info',
                    'message' => 'Container tidak ditemukan atau status tidak memenuhi syarat.',
                    'data' => null,
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
                'data' => null,
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nomerkon' => ['required', 'string', 'max:30'],
            'status_cont' => ['required', 'string', 'max:100'],
            'mySelect' => ['required', 'in:satu,dua'],
            'lokbar' => ['nullable', 'string', 'max:30'],
        ]);

        $noContainer = strtoupper(trim($validated['nomerkon']));
        $action = $validated['mySelect'];

        // Untuk action dua, destination tidak diambil dari request.
        $destination = $action === 'dua'
            ? 'SAMPAH'
            : strtoupper(trim($validated['lokbar'] ?? ''));

        $prod = DB::connection('prod');
        $transactionStarted = false;
        $stage = 'begin_transaction';
        try {
            $prod->beginTransaction();
            $transactionStarted = true;

            $stage = 'validasi_container';
            $container = $this->copyYardServices->getContainerForUpdate($noContainer);

            if (!$container) {
                throw new \RuntimeException('Container tidak ditemukan atau status sudah berubah.', 404);
            }

            $stage = 'validasi_status';
            $statusReference = $this->copyYardServices->getStatusByDescription($validated['status_cont']);
            if (!$statusReference || (string) $statusReference->ID !== (string) $container->STATUS_CONT) {
                throw new \RuntimeException('Status container berubah. Silakan cari ulang container.', 409);
            }

            if ($action === 'satu' && $destination === '') {
                throw new \RuntimeException('Lokasi tujuan wajib diisi.', 422);
            }

            $stage = 'validasi_lokasi_dan_transisi';
            $moveData = $this->copyYardServices->buildMoveData(
                $container,
                $action,
                $destination
            );

            $operator = (string) (session('USERLOGIN')
                ?? Auth::user()?->username
                ?? Auth::user()?->user_name
                ?? 'SYSTEM');
            if ($action !== 'dua') {
                $moveData['operator'] = $operator;
                if (!empty($moveData['job_slip'])) {
                    $moveData['job_slip']['operator'] = $operator;
                }
            }

            $stage = 'update_copy_yard';
            $this->copyYardServices->moveContainer($moveData);

            $stage = 'commit';
            $prod->commit();
            $transactionStarted = false;

            $this->logInfo($this->channel, 'Proses CopyYard berhasil.', [
                'no_cont' => $noContainer,
                'action' => $action,
                'destination' => $destination,
                'status_text' => $statusReference->KETERANGAN,
                'status_before' => $container->STATUS_CONT,
                'status_after' => $moveData['status_cont'],
                'operator' => $operator ?? null,
                'stage' => 'selesai',
            ]);

            return response()->json([
                'status' => 'success',
                'message' => $action === 'dua'
                    ? 'Container berhasil dipindahkan ke blok sampah.'
                    : 'Perpindahan container berhasil disimpan.',
                'data' => [
                    'no_cont' => $noContainer,
                    'status_cont' => $moveData['status_cont'],
                ],
            ]);
        } catch (Throwable $e) {
            try {
                if ($transactionStarted && $prod->transactionLevel() > 0) {
                    $prod->rollBack();
                }
            } catch (Throwable) {
                // Pertahankan exception utama untuk logging.
            }

            $code = (int) $e->getCode();
            $httpCode = in_array($code, [4, 5, 7, 8, 9, 404, 409, 422], true)
                ? (in_array($code, [404, 409, 422], true) ? $code : 422)
                : 500;

            $this->logError($this->channel, 'Proses CopyYard gagal.', [
                'no_cont' => $noContainer,
                'action' => $action,
                'destination' => $destination,
                'error' => $e->getMessage(),
                'code' => $code,
                'stage' => $stage,
            ]);

            return response()->json([
                'status' => $httpCode === 500 ? 'error' : 'info',
                'message' => $e->getMessage() ?: 'Proses CopyYard gagal.',
                'data' => null,
            ], $httpCode);
        }
    }
}
