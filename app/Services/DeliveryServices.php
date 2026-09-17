<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class DeliveryServices
{

    public function searchContainer(string $keyword)
    {
        return DB::connection('prod')->select(
            "SELECT A.*
             FROM t_gatepass A
             INNER JOIN t_spk_cont B
                ON A.NO_CONT = B.NO_CONT
             WHERE A.NO_CONT LIKE ?
               AND A.JNS_KEGIATAN = '3'
               AND B.STATUS_CONT != 900",
            ["%" . $keyword . "%"]
        );
    }


    public function getGatepass(string $no_cont)
    {
        return DB::connection('prod')->select(
            "SELECT *
             FROM t_gatepass
             WHERE NO_CONT LIKE ?
               AND JNS_KEGIATAN = '3'",
            ["%" . $no_cont . "%"]
        );
    }


    public function getDelivery(string $no_cont)
    {
        return DB::connection('prod')->select(
            "SELECT *
             FROM t_op_delivery
             WHERE NO_CONT LIKE ?
               AND WK_GATEOUT IS NULL",
            ["%" . $no_cont . "%"]
        );
    }


    public function getCondition()
    {
        return DB::connection('prod')->select(
            "SELECT ID, KONDISI
             FROM reff_kondisi"
        );
    }


    public function getDetail(string $no_cont): array
    {
        $gatepass = $this->getGatepass($no_cont);

        $delivery = $this->getDelivery($no_cont);

 
        if (count($gatepass) === 0) {
            return [
                'status' => 0,
                'kode' => 1,
                'no_cont' => $no_cont,
                'message' => 'NO CONT NOT FOUND',
            ];
        }


        if (count($delivery) === 0) {
            return [
                'status' => 1,
                'no_cont' => $no_cont,
                'ukuran' => $gatepass[0]->UKR_CONT ?? null,
                'delivery' => [],
                'condition' => $this->getCondition(),
            ];
        }

        $deliveryRow = $delivery[0];

        $readyForGateOut =
            $deliveryRow->WK_TRUCKIN !== null
            && $deliveryRow->WK_CHASSIS !== null
            && $deliveryRow->WK_INSPECT !== null
            && $deliveryRow->WK_GATEOUT === null;

        if ($readyForGateOut) {
            return [
                'status' => 2,
                'no_cont' => $no_cont,
                'delivery' => $delivery,
                'condition' => $this->getCondition(),
            ];
        }

        return [
            'status' => 0,
            'kode' => 1,
            'no_cont' => $no_cont,
            'message' => 'NO CONT NOT FOUND',
        ];
    }
}