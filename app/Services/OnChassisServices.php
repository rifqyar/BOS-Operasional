<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use RuntimeException;

class OnChassisServices
{
    /** Search only Truck In containers that have not been processed On Chassis. */
    public function searchContainer(string $keyword): array
    {
        return DB::connection('prod_new')->table('t_op_delivery')
            ->where('NO_CONT', 'like', '%' . strtoupper(trim($keyword)) . '%')
            ->whereNotNull('WK_TRUCKIN')
            ->whereNull('WK_CHASSIS')
            ->orderByDesc('ID')
            ->get()
            ->all();
    }

    /** Return an exact match or a sole partial match; never choose an arbitrary container. */
    public function getContainer(string $keyword): ?object
    {
        $rows = $this->searchContainer($keyword);
        $needle = strtoupper(trim($keyword));
        $exact = array_values(array_filter(
            $rows,
            fn ($row) => strtoupper(trim((string) ($row->NO_CONT ?? ''))) === $needle
        ));

        if (count($exact) === 1) {
            return $exact[0];
        }

        return count($exact) === 0 && count($rows) === 1 ? $rows[0] : null;
    }

    /** Available Truck In records for the legacy On Chassis list. */
    public function getAvailableContainers(): array
    {
        return DB::connection('prod_new')->table('t_op_delivery')
            ->whereNotNull('WK_TRUCKIN')
            ->whereNull('WK_CHASSIS')
            ->orderByDesc('ID')
            ->get()
            ->all();
    }

    /**
     * Revalidate and lock the delivery, SPK container, SPK, and operation
    * rows inside the caller's prod_new transaction.
     */
    public function getContainerForProcess(string $noContainer, ?string $noSpk = null): array
    {
        $db = DB::connection('prod_new');
        $noContainer = strtoupper(trim($noContainer));

        $delivery = $db->table('t_op_delivery')
            ->where('NO_CONT', $noContainer)
            ->whereNotNull('WK_TRUCKIN')
            ->whereNull('WK_CHASSIS')
            ->orderByDesc('ID')
            ->lockForUpdate()
            ->first();

        if (!$delivery) {
            throw new RuntimeException('Container tidak ditemukan atau sudah diproses On Chassis.', 404);
        }

        $deliverySpk = trim((string) ($delivery->NO_SPK ?? ''));
        if ($deliverySpk === '') {
            throw new RuntimeException('Nomor SPK pada data Delivery tidak tersedia.', 422);
        }
        if ($noSpk !== null && trim($noSpk) !== '' && trim($noSpk) !== $deliverySpk) {
            throw new RuntimeException('Nomor SPK tidak sesuai dengan data Delivery container.', 422);
        }

        $spk = $db->table('t_spk')
            ->where('NO_SPK', $deliverySpk)
            ->orderByDesc('ID')
            ->lockForUpdate()
            ->first();
        if (!$spk) {
            throw new RuntimeException('Data SPK container tidak ditemukan.', 404);
        }

        $spkContainer = $db->table('t_spk_cont')
            ->where('ID', $spk->ID)
            ->where('NO_CONT', $noContainer)
            ->where('STATUS_CONT', '<>', '900')
            ->lockForUpdate()
            ->first();
        if (!$spkContainer) {
            throw new RuntimeException('Status container tidak memenuhi syarat On Chassis.', 422);
        }

        $operation = $db->table('t_operation')
            ->where('NO_CONT', $noContainer)
            ->where('NO_SPK', $deliverySpk)
            ->lockForUpdate()
            ->first();
        if (!$operation) {
            throw new RuntimeException('Data operasi container dan SPK tidak ditemukan.', 404);
        }

        return [
            'delivery' => $delivery,
            'spk_container' => $spkContainer,
            'spk' => $spk,
            'operation' => $operation,
        ];
    }
}
