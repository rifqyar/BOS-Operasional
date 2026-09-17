<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class OnChassisServices
{

    public function searchContainer(string $keyword)
    {
        return DB::connection('prod')->select(
            "SELECT *
             FROM t_op_delivery
             WHERE NO_CONT LIKE ?
               AND WK_CHASSIS IS NULL
             ORDER BY ID DESC
             LIMIT 1",
            ["%" . $keyword . "%"]
        );
    }


    public function getContainer(string $keyword)
    {
        $data = $this->searchContainer($keyword);

        if (count($data) === 0) {
            return null;
        }

        return $data[0];
    }
}