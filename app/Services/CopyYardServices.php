<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class CopyYardServices
{

    public function searchContainer(string $keyword)
    {
        return DB::connection('prod')->select(
            "SELECT
                A.*,
                C.NAMA,
                D.KETERANGAN
            FROM t_spk_cont A
            INNER JOIN t_spk B
                ON A.ID = B.ID
            INNER JOIN reff_kode_dok_bc C
                ON B.JNS_DOK = C.ID
            INNER JOIN reff_status_spk D
                ON A.STATUS_CONT = D.ID
            WHERE A.NO_CONT LIKE ?
              AND A.STATUS_CONT NOT IN ('000', '100', '200')
            ORDER BY A.ID DESC
            LIMIT 1",
            [
                "%" . strtoupper($keyword) . "%"
            ]
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