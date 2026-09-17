<?php

namespace App\Http\Controllers\Operation;

use App\Http\Controllers\Controller;
use App\Services\PlugReeferServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class PlugReeferController extends Controller
{
    public function __construct(
        protected PlugReeferServices $plugReeferServices
    ) {
    }

    /**
     * Search container.
     *
     * Legacy:
     * search_reefer()
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'no_cont' => [
                    'required',
                    'string',
                    'max:30',
                ],
            ]);

            $noCont = strtoupper(
                trim($validated['no_cont'])
            );

            $rows = $this->plugReeferServices
                ->searchReefer($noCont);

            if (empty($rows)) {
                return response()->json([
                    'status' => 0,
                    'kode' => 1,
                    'nilai' => $noCont,
                    'message' => "WARNING ! NO CONT : {$noCont} NOT FOUND",
                ], 404);
            }

            $html = view(
                'livewire.partials.plugreefer.table',
                [
                    'rows' => $rows,
                ]
            )->render();

            return response()->json([
                'status' => 2,
                'data' => $html,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage()
                    ?: 'Gagal mencari container reefer.',
            ], 500);
        }
    }

    /**
     * Detail container reefer.
     *
     * Legacy:
     * serch_reefer()
     */
    public function detail(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'no_cont' => [
                    'required',
                    'string',
                    'max:30',
                ],
            ]);

            $noCont = strtoupper(
                trim($validated['no_cont'])
            );

            $rows = $this->plugReeferServices
                ->searchReefer($noCont);

            if (empty($rows)) {
                return response()->json([
                    'status' => 0,
                    'message' => "WARNING ! NO CONT : {$noCont} NOT FOUND",
                ], 404);
            }

            $row = $rows[0];

            $checked = $this->plugReeferServices
                ->checked($noCont);

            $tempAkhir = $this->plugReeferServices
                ->tempAkhir($noCont);

            $spk = $this->plugReeferServices
                ->getSpkContainer($noCont);

            /*
             * Legacy:
             *
             * $start == NULL
             *      kond = 1
             *
             * WAKTU != NULL
             * FL_PLUG = Y
             * FL_UNPLUG = N
             *      kond = 2
             *
             * else
             *      kond = 0
             */
            if (empty($checked)) {

                $condition = 1;

            } else {

                $start = $checked[0];

                if (
                    !empty($start->WAKTU)
                    && ($start->FL_PLUG ?? null) === 'Y'
                    && ($start->FL_UNPLUG ?? null) === 'N'
                ) {
                    $condition = 2;
                } else {
                    $condition = 0;
                }
            }

            $lastTemperature = null;

            if (!empty($tempAkhir)) {
                $lastTemperature =
                    $tempAkhir[0]->TEMPERATURE_MONITOR
                    ?? null;
            }

            $html = view(
                'livewire.partials.plugreefer.form',
                [
                    'row' => $row,
                    'spk' => $spk,
                    'checked' => $checked[0] ?? null,
                    'tempAkhir' => $tempAkhir[0] ?? null,
                    'lastTemperature' => $lastTemperature,
                    'condition' => $condition,
                ]
            )->render();

            return response()->json([
                'status' => 1,
                'condition' => $condition,
                'data' => $html,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage()
                    ?: 'Gagal mengambil detail reefer.',
            ], 500);
        }
    }

    /**
     * Plugin / Unplugin.
     *
     * READ ONLY untuk fase development.
     *
     * Belum menjalankan set_plug() legacy.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'nomerkon' => [
                    'required',
                    'string',
                    'max:30',
                ],

                'temperature' => [
                    'required',
                    'string',
                    'max:50',
                ],

                'note' => [
                    'nullable',
                    'string',
                    'max:1000',
                ],

                'action' => [
                    'required',
                    'in:plugin,unplugin',
                ],
            ]);

            $noCont = strtoupper(
                trim($validated['nomerkon'])
            );

            /*
             * Pastikan container memang ada.
             */
            $rows = $this->plugReeferServices
                ->searchReefer($noCont);

            if (empty($rows)) {
                return response()->json([
                    'status' => 0,
                    'message' => "NO CONT : {$noCont} NOT FOUND",
                ], 404);
            }

            if ($validated['action'] === 'plugin') {

                return response()->json([
                    'status' => 1,
                    'message' => "NO CONT : {$noCont} PLUGIN KONTAINER",
                ]);
            }

            return response()->json([
                'status' => 1,
                'message' => "NO CONT : {$noCont} UNPLUGIN KONTAINER",
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage()
                    ?: 'Data gagal diproses.',
            ], 422);
        }
    }
}