<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class StringStuffingServices
{
    public function getContainer(string $keyword)
    {
        return DB::connection('prod')->selectOne(
            "SELECT
                NO_CONT,
                NO_DOK,
                NO_CONT_LAMA,
                NO_DOK_LAMA,
                WK_START_STRIPSTUF
             FROM t_op_stripstuff
             WHERE NO_CONT LIKE ?
             ORDER BY ID DESC
             LIMIT 1",
            [
                '%' . $keyword . '%',
            ]
        );
    }
}