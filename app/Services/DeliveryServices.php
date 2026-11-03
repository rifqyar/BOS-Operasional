<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class DeliveryServices
{
    /**
     * Pencarian container berdasarkan gatepass dan status container.
     * Seluruh validasi/read menggunakan database legacy prod.
     */
    public function searchContainer(string $keyword): array
    {
        return DB::connection('prod')->select(
            "SELECT A.*
             FROM t_gatepass A
             INNER JOIN t_spk_cont B
                ON A.NO_CONT = B.NO_CONT
             WHERE A.NO_CONT LIKE ?
               AND A.JNS_KEGIATAN = '3'
               AND B.STATUS_CONT != 900",
            ['%' . $keyword . '%']
        );
    }

    public function getGatepass(string $noCont): array
    {
        return DB::connection('prod')->select(
            "SELECT *
             FROM t_gatepass
             WHERE NO_CONT = ?
               AND JNS_KEGIATAN = '3'",
            [$noCont]
        );
    }

    public function getDelivery(string $noCont): array
    {
        return DB::connection('prod')->select(
            "SELECT *
             FROM t_op_delivery
             WHERE NO_CONT = ?
               AND WK_GATEOUT IS NULL",
            [$noCont]
        );
    }

    public function getCondition(): array
    {
        return DB::connection('prod')->select(
            "SELECT ID, KONDISI
             FROM reff_kondisi"
        );
    }

    /**
     * Menentukan form yang ditampilkan:
     * status 1 = Truck In
     * status 2 = Gate Out
     */
    public function getDetail(string $noCont): array
    {
        $noCont = strtoupper(trim($noCont));

        $gatepass = $this->getGatepass($noCont);
        $delivery = $this->getDelivery($noCont);

        if (empty($gatepass)) {
            return [
                'status' => 0,
                'kode' => 1,
                'no_cont' => $noCont,
                'message' => 'NO CONT NOT FOUND',
            ];
        }

        if (empty($delivery)) {
            return [
                'status' => 1,
                'no_cont' => $noCont,
                'ukuran' => $gatepass[0]->UKR_CONT ?? null,
                'delivery' => [],
                'condition' => $this->getCondition(),
            ];
        }

        $deliveryRow = $delivery[0];

        $readyForGateOut =
            !empty($deliveryRow->WK_TRUCKIN)
            && !empty($deliveryRow->WK_CHASSIS)
            && !empty($deliveryRow->WK_INSPECT)
            && empty($deliveryRow->WK_GATEOUT);

        if ($readyForGateOut) {
            return [
                'status' => 2,
                'no_cont' => $noCont,
                'delivery' => $delivery,
                'condition' => $this->getCondition(),
            ];
        }

        return [
            'status' => 0,
            'kode' => 1,
            'no_cont' => $noCont,
            'message' => 'Container belum memenuhi kondisi Delivery.',
        ];
    }
}