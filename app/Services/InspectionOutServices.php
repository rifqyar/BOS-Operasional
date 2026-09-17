<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class InspectionOutServices
{

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
            "SELECT
                ID,
                KONDISI
             FROM reff_kondisi"
        );
    }


    public function getInspectionData(string $no_cont): array
    {
        $gatepass = $this->getGatepass($no_cont);


        if (count($gatepass) === 0) {
            return [
                'status' => 0,
                'kode' => 1,
                'no_cont' => $no_cont,
                'message' => "WARNING ! NO CONT : {$no_cont} NOT FOUND",
            ];
        }

        $delivery = $this->getDelivery($no_cont);


        if (count($delivery) === 0) {
            return [
                'status' => 0,
                'kode' => 1,
                'no_cont' => $no_cont,
                'message' => "WARNING ! NO CONT : {$no_cont} NOT FOUND",
            ];
        }

        $deliveryRow = $delivery[0];


        $readyForInspectionOut =
            $deliveryRow->WK_TRUCKIN !== null
            && $deliveryRow->WK_CHASSIS !== null
            && $deliveryRow->WK_INSPECT === null;

        if (!$readyForInspectionOut) {
            return [
                'status' => 0,
                'kode' => 1,
                'no_cont' => $no_cont,
                'message' => "WARNING ! NO CONT : {$no_cont} NOT FOUND",
            ];
        }

        return [
            'status' => 2,
            'no_cont' => $deliveryRow->NO_CONT ?? $no_cont,
            'ukuran' => $deliveryRow->UKR_CONT ?? null,
            'no_seal' => $deliveryRow->NO_SEAL ?? null,
            'delivery' => $deliveryRow,
            'condition' => $this->getCondition(),
        ];
    }
}