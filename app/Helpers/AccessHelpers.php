<?php

use Illuminate\Support\Facades\DB;

function checkHoldP2(string $keyword): bool
{
    $query = "SELECT
                    d.ID,
                    d.NO_SPK,
                    c.NO_CONT,
                    d.TGL_DOK,
                    d.NO_DOK,
                    a.NAMA as 'JNS_DOK',
                    (case
                        when c.FL_HOLD = 'Y' then 'HOLD'
                    end) as 'KETERANGAN'
                    from
                    t_spk d
                    join t_spk_cont c on
                    c.ID = d.ID
                    left join reff_kode_dok_bc a on
                    a.ID = d.JNS_DOK
                    where exists (select 1 from t_atensi_p2 where NO_CONT = c.NO_CONT and NO_SPK = d.NO_SPK)
                    and (c.NO_CONT like '%$keyword%' or d.NO_SPK like '%$keyword%' or d.NO_DOK like '%$keyword%')
                    and c.FL_HOLD = 'Y'
                    and c.STATUS_CONT != 900";
    $onHold = DB::connection('prod')->selectOne($query);
    return !empty($onHold);
}
