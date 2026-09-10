<?php

namespace App\Http\Controllers\Operation;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class PickupController extends Controller
{

    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'no_spk' => ['required', 'string', 'max:50'],
        ]);

        try {
            $rows = DB::connection('prod')->selectOne(
                "SELECT
                    ts.NO_SPK AS no_spk,
                    tsc.NO_CONT AS no_container,
                    tsc.UKR_CONT AS ukuran,
                    tsc.STATUS_CONT AS status_cont
                FROM t_spk ts
                INNER JOIN t_spk_cont tsc ON ts.ID = tsc.ID
                WHERE ts.NO_SPK = ?
                    AND tsc.STATUS_CONT IN (100)
                    AND ts.WK_REQ >= '2026-01-01'
                ORDER BY tsc.NO_CONT",
                [$validated['no_spk']]
            );

            $trucks = DB::connection('prod')->select("SELECT * FROM reff_truck");

            if (blank($rows)) {
                return response()->json([
                    'message' => 'Data SPK tidak ditemukan. Pastikan nomor SPK sudah benar.',
                ], 404);
            }

            $view = view('livewire.partials.pickup.form', compact('rows', 'trucks'))->render();

            return response()->json([
                'data' => $view,
            ]);
        } catch (Throwable) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat mencari data SPK.',
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'no_spk' => ['required', 'string', 'max:50'],
            'no_container' => ['required', 'string', 'max:20'],
            'no_truck' => ['nullable', 'string', 'max:30'],
        ]);

        try {
            $columns = Schema::connection('prod')->getColumnListing('t_spk_cont');
            $truckColumn = $this->firstAvailableColumn($columns, ['NO_TRUCK', 'NOMER_TRUCK', 'NO_POL', 'NOPOL']);
            $setSql = 'tsc.STATUS_CONT = 100';
            $bindings = [];

            if ($truckColumn) {
                $setSql = "tsc.$truckColumn = NULLIF(?, 'NONE'), $setSql";
                $bindings[] = $validated['no_truck'] ?? 'NONE';
            }

            $bindings[] = $validated['no_spk'];
            $bindings[] = $validated['no_container'];

            $updated = DB::connection('prod')->update(
                "UPDATE t_spk_cont tsc
                INNER JOIN t_spk ts ON ts.ID = tsc.ID
                SET $setSql
                WHERE ts.NO_SPK = ?
                    AND tsc.NO_CONT = ?",
                $bindings
            );

            if ($updated === 0) {
                return response()->json([
                    'message' => 'Data pickup tidak ditemukan atau sudah diproses.',
                ], 404);
            }

            return response()->json([
                'message' => 'Data pickup berhasil dikirim.',
            ]);
        } catch (Throwable) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengirim data pickup.',
            ], 500);
        }
    }

    private function firstAvailableColumn(array $columns, array $candidates): ?string
    {
        $columns = array_map('strtoupper', $columns);

        foreach ($candidates as $candidate) {
            if (in_array($candidate, $columns, true)) {
                return $candidate;
            }
        }

        return null;
    }
}
