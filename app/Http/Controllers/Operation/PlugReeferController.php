<?php

namespace App\Http\Controllers\Operation;

use App\Http\Controllers\Controller;
use App\Http\Traits\LogTraits;
use App\Services\PlugReeferServices;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class PlugReeferController extends Controller
{
    use LogTraits;

    public function __construct(
        protected PlugReeferServices $services
    ) {}

    /**
     * SEARCH CONTAINER
     *
     * Mengacu pada legacy:
     * search_reefer()
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'no_cont' => ['required', 'string', 'max:30'],
        ]);

        $noCont = strtoupper(trim($validated['no_cont']));

        try {
            $rows = $this->services->searchReefer($noCont);

            if (empty($rows)) {
                $this->logError(
                    'plugreefer',
                    'Data container Plug Reefer tidak ditemukan',
                    [
                        'no_container' => $noCont,
                    ]
                );

                return response()->json([
                    'message' => 'Data container tidak ditemukan atau tidak memenuhi syarat Plug Reefer.',
                ], 404);
            }

            $view = view(
                'livewire.partials.plugreefer.table',
                [
                    'rows' => $rows,
                ]
            )->render();

            $this->logInfo(
                'plugreefer',
                'Pencarian container Plug Reefer berhasil',
                [
                    'no_container' => $noCont,
                    'jumlah_data' => count($rows),
                ]
            );

            return response()->json([
                'data' => $view,
            ]);
        } catch (Throwable $e) {
            $this->logError(
                'plugreefer',
                'Gagal mencari data container Plug Reefer',
                [
                    'no_container' => $noCont,
                    'error' => $e->getMessage(),
                ]
            );

            return response()->json([
                'message' => 'Terjadi kesalahan saat mencari data Plug Reefer.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * DETAIL CONTAINER
     *
     * Mengacu pada legacy:
     * serch_reefer()
     */
    public function detail(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'no_cont' => ['required', 'string', 'max:30'],
        ]);

        $noCont = strtoupper(trim($validated['no_cont']));

        try {
            $rows = $this->services->searchReefer($noCont);

            if (empty($rows)) {
                $this->logError(
                    'plugreefer',
                    'Container tidak ditemukan saat mengambil detail Plug Reefer',
                    [
                        'no_container' => $noCont,
                    ]
                );

                return response()->json([
                    'message' => 'Data container tidak ditemukan.',
                ], 404);
            }

            $row = $rows[0];

            $checked = $this->services->checked($noCont);

            $tempAkhir = $this->services->tempAkhir($noCont);

            $spk = $this->services->getSpkContainer($noCont);

            /*
             * Condition 1:
             * Belum ada aktivitas Plug aktif.
             *
             * Condition 2:
             * Container sedang Plug.
             *
             * Condition 0:
             * Kondisi lainnya, tidak dapat diproses.
             */
            $condition = 0;

            if (empty($checked)) {
                $condition = 1;
            } else {
                $activePlug = $checked[0];

                if (
                    !empty($activePlug->WAKTU)
                    && ($activePlug->FL_PLUG ?? null) === 'Y'
                    && ($activePlug->FL_UNPLUG ?? null) === 'N'
                ) {
                    $condition = 2;
                }
            }

            $lastTemperature = $tempAkhir[0]->TEMPERATURE_MONITOR
                ?? '';

            $view = view(
                'livewire.partials.plugreefer.form',
                [
                    'row' => $row,
                    'spk' => $spk,
                    'checked' => $checked,
                    'tempAkhir' => $tempAkhir,
                    'lastTemperature' => $lastTemperature,
                    'condition' => $condition,
                ]
            )->render();

            $this->logInfo(
                'plugreefer',
                'Detail container Plug Reefer berhasil diambil',
                [
                    'no_container' => $noCont,
                    'condition' => $condition,
                ]
            );

            return response()->json([
                'data' => $view,
            ]);
        } catch (Throwable $e) {
            $this->logError(
                'plugreefer',
                'Gagal mengambil detail container Plug Reefer',
                [
                    'no_container' => $noCont,
                    'error' => $e->getMessage(),
                ]
            );

            return response()->json([
                'message' => 'Terjadi kesalahan saat mengambil detail Plug Reefer.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * STORE PLUG / UNPLUG
     *
     * Mengacu pada legacy:
     * set_plug()
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nomerkon' => ['required', 'string', 'max:30'],
            'temperature' => ['required', 'numeric'],
            'note' => ['nullable', 'string'],
            'action' => ['required', 'in:plugin,unplugin'],
        ]);

        $noCont = strtoupper(trim($validated['nomerkon']));
        $temperature = $validated['temperature'];
        $note = $validated['note'] ?? null;
        $action = $validated['action'];

        $connection = DB::connection('prod');

        try {
            $connection->beginTransaction();

            /*
             * Pastikan container masih memenuhi syarat
             * berdasarkan data legacy.
             */
            $rows = $this->services->searchReefer($noCont);

            if (empty($rows)) {
                throw new Exception(
                    'Data container tidak ditemukan atau tidak memenuhi syarat Plug Reefer.',
                    404
                );
            }

            /*
             * Ambil SPK dan container terbaru.
             */
            $spk = $this->services->getSpkContainer($noCont);

            if (!$spk || empty($spk->NO_CONT)) {
                throw new Exception(
                    'Data SPK dan container tidak ditemukan.',
                    404
                );
            }

            /*
             * Periksa kembali kondisi aktivitas agar
             * action dari browser tidak menjadi satu-satunya
             * penentu operasi database.
             */
            $checked = $this->services->checked($noCont);

            $operator = Auth::user()?->username
                ?? Auth::user()?->user_name
                ?? 'SYSTEM';

            $now = now();

            /*
             * ACTION PLUGIN
             *
             * Legacy:
             * Jika tidak ada aktivitas aktif,
             * INSERT data baru ke t_op_reefer.
             */
            if ($action === 'plugin') {
                if (!empty($checked)) {
                    throw new Exception(
                        'Container sudah memiliki aktivitas Plug aktif. Muat ulang detail container.',
                        409
                    );
                }

                $connection->table('t_op_reefer')->insert([
                    'NO_SPK' => $spk->NO_SPK,
                    'NO_CONT' => $noCont,
                    'WAKTU' => $now,
                    'WAKTU_MONITOR' => $now,
                    'TEMPERATURE_AWAL' => $temperature,
                    'TEMPERATURE_MONITOR' => $temperature,
                    'FL_PLUG' => 'Y',
                    'FL_MONITOR' => 'Y',
                    'NOTE' => $note,
                    'OPERATOR_START' => $operator,
                    'OPERATOR_MONITOR' => $operator,
                    'WK_REKAM' => $now,
                ]);

                $connection->commit();

                $this->logInfo(
                    'plugreefer',
                    'Container berhasil di-Plugin',
                    [
                        'no_spk' => $spk->NO_SPK,
                        'no_container' => $noCont,
                        'temperature' => $temperature,
                        'operator' => $operator,
                    ]
                );

                return response()->json([
                    'status' => 'success',
                    'message' => 'Container berhasil di-Plugin.',
                    'cont' => $spk,
                ]);
            }

            /*
             * ACTION UNPLUGIN
             *
             * Legacy:
             * Jika aktivitas Plug ditemukan,
             * UPDATE data aktivitas tersebut.
             */
            if ($action === 'unplugin') {
                if (empty($checked)) {
                    throw new Exception(
                        'Container tidak memiliki aktivitas Plug aktif.',
                        409
                    );
                }

                $activePlug = $checked[0];

                if (
                    empty($activePlug->WAKTU)
                    || ($activePlug->FL_PLUG ?? null) !== 'Y'
                    || ($activePlug->FL_UNPLUG ?? null) !== 'N'
                ) {
                    throw new Exception(
                        'Kondisi container tidak memenuhi syarat Unplug Reefer.',
                        409
                    );
                }

                /*
                 * Pertahankan kriteria legacy:
                 * NO_CONT dan NO_SPK,
                 * urut ID ascending,
                 * hanya satu baris.
                 */
                $updated = $connection
                    ->table('t_op_reefer')
                    ->where('NO_CONT', $noCont)
                    ->where('NO_SPK', $spk->NO_SPK)
                    ->whereNotNull('WAKTU')
                    ->where('FL_UNPLUG', 'N')
                    ->orderBy('ID', 'ASC')
                    ->limit(1)
                    ->update([
                        'WAKTU_END' => $now,
                        'FL_UNPLUG' => 'Y',
                        'NOTE' => $note,
                        'TEMPERATURE_AKHIR' => $temperature,
                        'OPERATOR_END' => $operator,
                    ]);

                if ($updated === 0) {
                    throw new Exception(
                        'Data aktivitas Plug tidak berhasil diperbarui.',
                        409
                    );
                }

                $connection->commit();

                $this->logInfo(
                    'plugreefer',
                    'Container berhasil di-Unplug',
                    [
                        'no_spk' => $spk->NO_SPK,
                        'no_container' => $noCont,
                        'temperature' => $temperature,
                        'operator' => $operator,
                    ]
                );

                return response()->json([
                    'status' => 'success',
                    'message' => 'Container berhasil di-Unplug.',
                    'cont' => $spk,
                ]);
            }

            throw new Exception('Action Plug Reefer tidak valid.', 422);
        } catch (Throwable $e) {
            if ($connection->transactionLevel() > 0) {
                $connection->rollBack();
            }

            $statusCode = in_array(
                (int) $e->getCode(),
                [404, 409, 422],
                true
            ) ? (int) $e->getCode() : 500;

            $this->logError(
                'plugreefer',
                'Gagal memproses Plug/Unplug Reefer',
                [
                    'no_container' => $noCont,
                    'action' => $action,
                    'temperature' => $temperature,
                    'error' => $e->getMessage(),
                ]
            );

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
                    ?: 'Terjadi kesalahan saat memproses Plug Reefer.',
                'error' => $e->getMessage(),
            ], $statusCode);
        }
    }
}