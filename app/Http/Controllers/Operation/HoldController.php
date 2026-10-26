<?php

namespace App\Http\Controllers\Operation;

use App\Http\Controllers\Controller;
use App\Http\Traits\LogTraits;
use App\Services\HoldService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class HoldController extends Controller
{
    use LogTraits;

    public function __construct(
        protected HoldService $services
    ) {
    }

    /**
     * Mencari container untuk Hold atau menampilkan detail Release.
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search_cont' => ['required', 'string', 'max:50'],
        ]);

        try {
            $search = trim($validated['search_cont']);

            $items = $this->services->searchAvailable($search);

            if ($items->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Container tidak ditemukan atau tidak tersedia untuk Hold.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data container ditemukan.',
                'data' => $items,
            ]);
        } catch (Throwable $e) {
            Log::error('Hold search gagal', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencari container.',
            ], 500);
        }
    }

    /**
     * Mengambil detail container berdasarkan ID.
     */
    public function detail(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id' => ['required', 'integer', 'min:1'],
        ]);

        try {
            $item = $this->services->getDetail(
                (int) $validated['id']
            );

            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Detail container tidak ditemukan.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $item,
            ]);
        } catch (Throwable $e) {
            Log::error('Hold detail gagal', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail container.',
            ], 500);
        }
    }

    /**
     * Mengambil daftar container yang sedang Hold.
     */
    public function indexData(): JsonResponse
    {
        try {
            $data = $this->services->getHeldContainers();

            $html = view(
                'livewire.partials.hold.table',
                ['data' => $data]
            )->render();

            return response()->json([
                'success' => true,
                'message' => 'Data Hold berhasil diambil.',
                'html' => $html,
                'data' => $data,
            ]);
        } catch (Throwable $e) {
            Log::error('Hold indexData gagal', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil daftar container Hold.',
            ], 500);
        }
    }

    /**
     * Menyimpan Hold.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id' => ['required', 'integer', 'min:1'],
            'warna' => ['required', 'in:N,M,T'],
        ]);

        $prod = DB::connection('prod');
        $prodNew = DB::connection('prod_new');

        $prod->beginTransaction();
        $prodNew->beginTransaction();

        try {
            $authUser = Auth::user();

            $user = $authUser?->username
                ?? $authUser?->user_name
                ?? $authUser?->name
                ?? 'SYSTEM';

            $item = $this->services->hold(
                (int) $validated['id'],
                $validated['warna'],
                $user
            );

            $prod->commit();
            $prodNew->commit();

            $this->logInfo(
                'hold',
                'Berhasil melakukan Hold container',
                [
                    'id' => $item->ID,
                    'no_spk' => $item->NO_SPK,
                    'no_cont' => $item->NO_CONT,
                    'warna' => $validated['warna'],
                    'user' => $user,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => "Container {$item->NO_CONT} berhasil di-Hold.",
            ]);
        } catch (Throwable $e) {
            $this->rollbackConnections();

            if ($e instanceof ValidationException) {
                throw $e;
            }

            Log::error('Hold store gagal', [
                'message' => $e->getMessage(),
                'id' => $validated['id'],
            ]);

            return response()->json([
                'success' => false,
                'message' => $e instanceof RuntimeException
                    ? $e->getMessage()
                    : 'Terjadi kesalahan saat melakukan Hold.',
            ], 500);
        }
    }

    /**
     * Melepas status Hold.
     */
    public function release(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id' => ['required', 'integer', 'min:1'],
        ]);

        $prod = DB::connection('prod');
        $prodNew = DB::connection('prod_new');

        $prod->beginTransaction();
        $prodNew->beginTransaction();

        try {
            $item = $this->services->release(
                (int) $validated['id']
            );

            $prod->commit();
            $prodNew->commit();

            $this->logInfo(
                'hold',
                'Berhasil melakukan Release container',
                [
                    'id' => $item->ID,
                    'no_spk' => $item->NO_SPK,
                    'no_cont' => $item->NO_CONT,
                    'user' => Auth::user()?->username
                        ?? Auth::user()?->name
                        ?? 'SYSTEM',
                ]
            );

            return response()->json([
                'success' => true,
                'message' => "Container {$item->NO_CONT} berhasil di-Release.",
            ]);
        } catch (Throwable $e) {
            $this->rollbackConnections();

            Log::error('Hold release gagal', [
                'message' => $e->getMessage(),
                'id' => $validated['id'],
            ]);

            return response()->json([
                'success' => false,
                'message' => $e instanceof RuntimeException
                    ? $e->getMessage()
                    : 'Terjadi kesalahan saat melakukan Release.',
            ], 500);
        }
    }

    /**
     * Rollback koneksi yang masih memiliki transaksi aktif.
     */
    private function rollbackConnections(): void
    {
        foreach (['prod', 'prod_new'] as $connectionName) {
            try {
                $connection = DB::connection($connectionName);

                if ($connection->transactionLevel() > 0) {
                    $connection->rollBack();
                }
            } catch (Throwable $e) {
                Log::error(
                    "Rollback koneksi {$connectionName} gagal",
                    ['message' => $e->getMessage()]
                );
            }
        }
    }
}