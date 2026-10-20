<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class PickupServices
{
    public function getTrucks()
    {
        $data = DB::connection('prod')->select("SELECT * FROM reff_truck");
        return $data;
    }

    public function getDetailCont(string $noContainer, string $noTruck)
    {
        $data = DB::connection('prod')->selectOne(
            "SELECT
                A.NO_DOK,
                A.NO_SPK,
                A.TGL_DOK,
                A.NO_CONT,
                ? as ID_FLAT,
                B.TAR,
                B.TIPE_CONT,
                B.KD_CONT_JENIS,
                B.VESSEL,
                B.VOY_IN,
                B.ISO_CODE,
                C.NO_PLAT,
                C.BERAT_TRUCK,
                B.BRUTO,
                B.FL_DG,
                B.FL_OOG,
                A.STATUS_CONT
            from ( select a.NO_DOK, a.TGL_DOK, a.NO_SPK, b.NO_CONT, b.ID_FLAT, b.ISO_CODE, b.STATUS_CONT from t_spk a join t_spk_cont b on a.ID = b.ID ) A
            join (
                select
                    c.NO_DOK,
                    c.TGL_DOK,
                    d.NO_CONT,
                    d.TAR,
                    d.TIPE_CONT,
                    d.KD_CONT_JENIS,
                    d.VESSEL,
                    d.VOY_IN,
                    d.ISO_CODE,
                    d.BRUTO,
                    d.FL_DG,
                    d.FL_OOG
                from
                    t_request c
                join t_request_cont d on
                    c.ID = d.ID) B on
                A.no_dok = B.no_dok
                and A.NO_CONT = B.NO_CONT
            left join reff_truck C on
                ? = C.NO_TRUCK
            where
                A.NO_CONT = ?
            order by
                A.TGL_DOK desc",
            [$noTruck, $noTruck, $noContainer]
        );

        return $data;
    }
}
