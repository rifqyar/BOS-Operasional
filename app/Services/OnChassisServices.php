<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class OnChassisServices
{
    /**
     * Search container yang belum On Chassis.
     * READ: MySQL legacy (prod).
     */
    public function searchContainer(string $keyword): array
    {
        return DB::connection('prod')->select(
            "SELECT *
             FROM t_op_delivery
             WHERE NO_CONT LIKE ?
               AND WK_CHASSIS IS NULL
             ORDER BY ID DESC",
            ['%' . $keyword . '%']
        );
    }

    /**
     * Mengambil satu container untuk ditampilkan pada form.
     */
    public function getContainer(string $keyword): ?object
    {
        $data = $this->searchContainer($keyword);

        return $data[0] ?? null;
    }

    /**
     * Daftar container yang sudah Truck In dan belum On Chassis.
     * READ: MySQL legacy (prod).
     */
    public function getAvailableContainers(): array
    {
        return DB::connection('prod')->select(
            "SELECT *
             FROM t_op_delivery
             WHERE WK_TRUCKIN IS NOT NULL
               AND WK_CHASSIS IS NULL
             ORDER BY ID DESC"
        );
    }

    /**
     * Validasi ulang data delivery sebelum proses.
     * READ: MySQL legacy (prod).
     *
     * Legacy update t_op_delivery berdasarkan NO_CONT,
     * sedangkan t_operation berdasarkan NO_CONT + NO_SPK.
     */
    public function getContainerForProcess(
        string $noContainer,
        string $noSpk
    ): ?object {
        return DB::connection('prod')->selectOne(
            "SELECT *
             FROM t_op_delivery
             WHERE NO_CONT = ?
               AND NO_SPK = ?
               AND WK_CHASSIS IS NULL
             ORDER BY ID DESC
             LIMIT 1",
            [$noContainer, $noSpk]
        );
    }
}