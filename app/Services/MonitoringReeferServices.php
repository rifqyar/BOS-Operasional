<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class MonitoringReeferServices
{

    public function getMonitoring(string $keyword)
    {
        return DB::connection('prod')->selectOne(
            "SELECT *
             FROM t_op_reefer
             WHERE WAKTU IS NOT NULL
               AND NO_CONT LIKE ?
               AND WAKTU_END IS NULL
             ORDER BY ID ASC
             LIMIT 1",
            [
                '%' . $keyword . '%',
            ]
        );
    }


    public function getLastTemperature(string $keyword)
    {
        return DB::connection('prod')->selectOne(
            "SELECT TEMPERATURE_MONITOR
             FROM t_op_reefer
             WHERE WAKTU_MONITOR IS NOT NULL
               AND NO_CONT LIKE ?
               AND WAKTU_END IS NULL
               AND FL_MONITOR = 'Y'
             ORDER BY ID DESC
             LIMIT 1",
            [
                '%' . $keyword . '%',
            ]
        );
    }
}