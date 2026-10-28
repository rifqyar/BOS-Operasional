<?php

namespace App\Http\Controllers\Operation;

use App\Http\Controllers\Controller;
use App\Http\Traits\LogTraits;
use App\Services\RealisasiServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class RealisasiController extends Controller
{
    use LogTraits;

    protected RealisasiServices $realisasiServices;

    public function __construct(RealisasiServices $realisasiServices)
    {
        $this->realisasiServices = $realisasiServices;
    }

    /**
     * Pencarian container.
     * Migrasi dari search_realis() pada legacy.
     */
    public function search(Request $request): JsonResponse
    {
        $noCont = null;

        try {
            $validated = $request->validate([
                'no_cont' => ['required', 'string', 'max:20'],
            ]);

            $noCont = trim($validated['no_cont']);

            $rows = $this->realisasiServices->searchReal($noCont);

            if (empty($rows)) {
                $this->logWarning(
                    'pemeriksaan-behandle',
                    'Container tidak ditemukan pada pencarian realisasi.',
                    ['no_cont' => $noCont]
                );

                return response()->json([
                    'status' => 0,
                    'kode' => 1,
                    'nilai' => $noCont,
                    'message' => "Container {$noCont} tidak ditemukan.",
                ], 404);
            }

            $html = view(
                'livewire.partials.realisasi.table',
                ['rows' => $rows]
            )->render();

            $this->logInfo(
                'pemeriksaan-behandle',
                'Pencarian container realisasi berhasil.',
                [
                    'no_cont' => $noCont,
                    'jumlah_data' => count($rows),
                ]
            );

            return response()->json([
                'status' => 2,
                'message' => 'Data container berhasil ditemukan.',
                'data' => $html,
            ]);
        } catch (ValidationException $e) {
            $this->logWarning(
                'pemeriksaan-behandle',
                'Validasi pencarian realisasi gagal.',
                [
                    'no_cont' => $request->input('no_cont'),
                    'errors' => $e->errors(),
                ]
            );

            return response()->json([
                'status' => 0,
                'message' => 'Nomor container tidak valid.',
                'errors' => $e->errors(),
            ], 422);
        } catch (Throwable $e) {
            report($e);

            $this->logError(
                'pemeriksaan-behandle',
                'Terjadi kesalahan saat mencari container realisasi.',
                [
                    'no_cont' => $noCont ?? $request->input('no_cont'),
                    'error' => $e->getMessage(),
                    'exception' => get_class($e),
                ]
            );

            return response()->json([
                'status' => 0,
                'message' => 'Gagal mengambil data container.',
            ], 500);
        }
    }

    /**
     * Detail pemeriksaan.
     * Migrasi dari search_realisasi() pada legacy.
     */
    public function detail(Request $request): JsonResponse
    {
        $noCont = null;

        try {
            $validated = $request->validate([
                'no_cont' => ['required', 'string', 'max:20'],
            ]);

            $noCont = trim($validated['no_cont']);

            $rows = $this->realisasiServices->searchReal($noCont);

            if (empty($rows)) {
                $this->logWarning(
                    'pemeriksaan-behandle',
                    'Container tidak ditemukan saat mengambil detail realisasi.',
                    ['no_cont' => $noCont]
                );

                return response()->json([
                    'status' => 0,
                    'message' => "Container {$noCont} tidak ditemukan.",
                ], 404);
            }

            $row = $rows[0];

            $realisasi = $this->realisasiServices->getStatus($noCont);

            /*
             * 0 = Belum mulai pemeriksaan.
             * 1 = Pemeriksaan sedang berlangsung.
             * 2 = Pemeriksaan selesai.
             */
            if (!$realisasi || $realisasi->START_INSP === null) {
                $kond = 0;
            } elseif ($realisasi->FINISH_INSP === null) {
                $kond = 1;
            } else {
                $kond = 2;
            }

            $join = null;

            if ($kond === 1 && $realisasi) {
                $noDok = $realisasi->NO_DOK ?? null;
                $tglDok = $realisasi->TGL_DOK ?? null;

                if ($noDok !== null && $tglDok !== null) {
                    $join = $this->realisasiServices->getJoin(
                        $noCont,
                        $noDok,
                        $tglDok
                    );
                }
            }

            $equipments = $this->realisasiServices->getAlat();
            $operators = $this->realisasiServices->getOperator();
            $conditions = $this->realisasiServices->getCondition();

            $html = view(
                'livewire.partials.realisasi.form',
                [
                    'row' => $row,
                    'realisasi' => $realisasi,
                    'kond' => $kond,
                    'join' => $join,
                    'equipments' => $equipments,
                    'operators' => $operators,
                    'conditions' => $conditions,
                ]
            )->render();

            $this->logInfo(
                'pemeriksaan-behandle',
                'Detail pemeriksaan berhasil diambil.',
                [
                    'no_cont' => $noCont,
                    'kondisi' => $kond,
                ]
            );

            return response()->json([
                'status' => 1,
                'message' => 'Detail pemeriksaan berhasil diambil.',
                'kond' => $kond,
                'data' => $html,
            ]);
        } catch (ValidationException $e) {
            $this->logWarning(
                'pemeriksaan-behandle',
                'Validasi detail pemeriksaan gagal.',
                [
                    'no_cont' => $request->input('no_cont'),
                    'errors' => $e->errors(),
                ]
            );

            return response()->json([
                'status' => 0,
                'message' => 'Nomor container tidak valid.',
                'errors' => $e->errors(),
            ], 422);
        } catch (Throwable $e) {
            report($e);

            $this->logError(
                'pemeriksaan-behandle',
                'Terjadi kesalahan saat mengambil detail pemeriksaan.',
                [
                    'no_cont' => $noCont ?? $request->input('no_cont'),
                    'error' => $e->getMessage(),
                    'exception' => get_class($e),
                ]
            );

            return response()->json([
                'status' => 0,
                'message' => 'Gagal mengambil detail pemeriksaan.',
            ], 500);
        }
    }

    /**
     * Menyimpan proses pemeriksaan.
     *
     * kode 1 = Pemeriksaan dimulai.
     * kode 2 = Pemeriksaan selesai.
     */
    public function store(Request $request): JsonResponse
    {
        $noCont = $request->input('nomerkon');

        try {
            $validated = $request->validate([
                'nomerkon' => ['required', 'string', 'max:20'],
                'noseal' => ['nullable', 'string', 'max:100'],
                'idJobSlip' => ['nullable'],
                'nospk' => ['nullable', 'string', 'max:100'],
                'tipecont' => ['nullable', 'string', 'max:50'],
                'alat' => ['nullable'],
                'operator' => ['nullable', 'string', 'max:100'],
                'join' => ['nullable'],
            ]);

            $noCont = trim($validated['nomerkon']);

            $user = $request->user();

            $operatorLogin =
                data_get($user, 'USERLOGIN')
                ?? data_get($user, 'userlogin')
                ?? data_get($user, 'username')
                ?? data_get($user, 'name')
                ?? $request->session()->get('USERLOGIN')
                ?? $request->session()->get('userlogin');

            if (
                !is_string($operatorLogin)
                || trim($operatorLogin) === ''
            ) {
                $this->logWarning(
                    'pemeriksaan-behandle',
                    'Proses pemeriksaan ditolak karena identitas operator tidak ditemukan.',
                    ['no_cont' => $noCont]
                );

                return response()->json([
                    'status' => 0,
                    'message' => 'Identitas operator tidak ditemukan. Silakan login kembali.',
                ], 401);
            }

            $operatorLogin = trim($operatorLogin);

            $result = $this->realisasiServices->storePemeriksaan(
                $validated,
                $operatorLogin
            );

            $this->logInfo(
                'pemeriksaan-behandle',
                'Permintaan pemeriksaan berhasil diproses.',
                [
                    'no_cont' => $noCont,
                    'operator' => $operatorLogin,
                    'kode' => $result['kode'] ?? null,
                ]
            );

            return response()->json([
                'status' => $result['status'] ?? 1,
                'kode' => $result['kode'] ?? 0,
                'message' => $result['message']
                    ?? 'Pemeriksaan berhasil diproses.',
                'data' => $result['data'] ?? null,
            ]);
        } catch (ValidationException $e) {
            $this->logWarning(
                'pemeriksaan-behandle',
                'Validasi data pemeriksaan gagal.',
                [
                    'no_cont' => $noCont,
                    'errors' => $e->errors(),
                ]
            );

            return response()->json([
                'status' => 0,
                'message' => 'Data pemeriksaan tidak valid.',
                'errors' => $e->errors(),
            ], 422);
        } catch (RuntimeException $e) {
            $this->logWarning(
                'pemeriksaan-behandle',
                'Proses pemeriksaan ditolak oleh validasi bisnis.',
                [
                    'no_cont' => $noCont,
                    'error' => $e->getMessage(),
                ]
            );

            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], 422);
        } catch (Throwable $e) {
            report($e);

            $this->logError(
                'pemeriksaan-behandle',
                'Terjadi kesalahan saat menyimpan pemeriksaan.',
                [
                    'no_cont' => $noCont,
                    'error' => $e->getMessage(),
                    'exception' => get_class($e),
                ]
            );

            return response()->json([
                'status' => 0,
                'message' => 'Terjadi kesalahan saat memproses pemeriksaan.',
            ], 500);
        }
    }
}