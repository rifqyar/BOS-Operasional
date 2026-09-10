<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class BehandleInServices
{
    public function getDataContainer(string $no_cont)
    {
        $data = DB::connection('prod')->selectOne(
            "SELECT
                    b.*,
	                a.NO_SPK,
                    a.NO_DOK,
                    a.TGL_DOK,
                    E.ID as ID_REQUEST,
                    E.UKR_CONT,
                    E.TIPE_CONT,
                    E.STATUS_BILLING,
                    E.FL_PERBAIKI
                from
                    t_spk a
                join t_spk_cont b on
                    a.ID = b.ID
                left join (
                    select
                        d.ID,
                        c.NO_CONT,
                        d.NO_DOK,
                        d.TGL_DOK,
                        c.UKR_CONT,
                        c.TIPE_CONT,
                        c.STATUS_BILLING,
                        c.FL_PERBAIKI
                    from
                        t_request_cont c
                    inner join t_request d on
                        c.ID = d.ID ) E on
                    E.NO_CONT = b.NO_CONT
                    and E.NO_DOK = a.NO_DOK
                    and E.TGL_DOK = a.TGL_DOK
                where
                	b.NO_CONT like ?
                    and b.STATUS_CONT = ?
                    and E.FL_PERBAIKI = ?",
            ["%" . $no_cont . "%", '200', 'N']
        );
        return $data;
    }

    public function getJobActivity()
    {
        $data = DB::connection('prod')->select("SELECT * FROM m_job_activity");
        return $data;
    }

    public function getAlat()
    {
        $data = DB::connection('prod')->select("SELECT * FROM t_reff_alat");
        return $data;
    }

    public function getOperator()
    {
        $data = DB::connection('prod')->select("SELECT * FROM reff_user WHERE KD_GROUP LIKE '%OPR%'");
        return $data;
    }

    public function getTruck(string $id_flat)
    {
        $data = DB::connection('prod')->select("SELECT NO_TRUCK from reff_truck where NOT NO_TRUCK = ?", [$id_flat]);
        return $data;
    }

    public function getCondition()
    {
        $data = DB::connection('prod')->select("SELECT ID,KONDISI from reff_kondisi");
        return $data;
    }
}
