<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Dashboard extends Component
{
    public function render()
    {
        $countWaitingPickupCont = $this->getPickupListContainer();
        $countWaitingInspectionCont = $this->getWaitingInspectionCont();
        $countOnInspectionCont = $this->getOnInspectionCont();
        $countInspectionDoneCont = $this->getInspectionDoneCont();
        return view('livewire.dashboard', compact('countWaitingPickupCont', 'countWaitingInspectionCont', 'countOnInspectionCont', 'countInspectionDoneCont'));
    }

    private function getPickupListContainer(){
        $dbConnection = DB::connection('prod');

        $query = "SELECT
                        COUNT(1) AS TOTAL_PICKUP
                    from t_spk ts
                    inner join t_spk_cont tsc on ts.ID = tsc.ID
                    inner join (
                        select
                            tr.NO_DOK,
                            trc.NO_CONT
                        from t_request tr
                        inner join t_request_cont trc on tr.ID = trc.ID
                    ) tr on ts.NO_DOK = tr.no_dok and tsc.NO_CONT = tr.no_cont
                    where tsc.STATUS_CONT in (000,100,300)
                        AND ts.WK_REQ >= '2026-01-01'";
        $data = $dbConnection->selectOne($query);
        return $data;
    }

    private function getWaitingInspectionCont(){
        $dbConnection = DB::connection('prod');

        $query = "SELECT
                        COUNT(1) AS TOTAL_WAITING
                    from t_spk ts
                    inner join t_spk_cont tsc on ts.ID = tsc.ID
                    inner join (
                        select
                            tr.NO_DOK,
                            trc.NO_CONT
                        from t_request tr
                        inner join t_request_cont trc on tr.ID = trc.ID
                    ) tr on ts.NO_DOK = tr.no_dok and tsc.NO_CONT = tr.no_cont
                    left join t_job_slip tjs on ts.NO_SPK = tjs.NO_SPK and tsc.NO_CONT = tjs.NO_CONT
                    where ts.WK_REQ >= '2026-01-01'
                        AND not exists (select ID from t_op_inspection toi where toi.NO_DOK = ts.NO_DOK and toi.NO_CONT = tsc.NO_CONT)
                        and ts.KD_STATUS = '400'
                        and tjs.JENIS = 'BEHANDLE 1' and tjs.KD_STATUS = '50'";
        $data = $dbConnection->selectOne($query);
        return $data;
    }

    private function getOnInspectionCont(){
        $dbConnection = DB::connection('prod');

        $query = "SELECT
                        COUNT(1) AS TOTAL_PROCESS
                    from t_spk ts
                    inner join t_spk_cont tsc on ts.ID = tsc.ID
                    inner join (
                        select
                            tr.NO_DOK,
                            trc.NO_CONT
                        from t_request tr
                        inner join t_request_cont trc on tr.ID = trc.ID
                    ) tr on ts.NO_DOK = tr.no_dok and tsc.NO_CONT = tr.no_cont
                    left join t_op_inspection tjs on ts.NO_SPK = tjs.NO_SPK and tsc.NO_CONT = tjs.NO_CONT and tr.NO_DOK = tjs.NO_DOK
                    where ts.WK_REQ >= '2026-01-01'
                        AND tjs.START_INSP is not null and tjs.FINISH_INSP is null";
        $data = $dbConnection->selectOne($query);
        return $data;
    }

    private function getInspectionDoneCont(){
        $dbConnection = DB::connection('prod');

        $query = "SELECT
                        COUNT(1) AS TOTAL_PROCESS_DONE
                    from t_spk ts
                    inner join t_spk_cont tsc on ts.ID = tsc.ID
                    inner join (
                        select
                            tr.NO_DOK,
                            trc.NO_CONT
                        from t_request tr
                        inner join t_request_cont trc on tr.ID = trc.ID
                    ) tr on ts.NO_DOK = tr.no_dok and tsc.NO_CONT = tr.no_cont
                    left join t_op_inspection tjs on ts.NO_SPK = tjs.NO_SPK and tsc.NO_CONT = tjs.NO_CONT and tr.NO_DOK = tjs.NO_DOK
                    where ts.WK_REQ >= '2026-01-01'
                        AND tjs.START_INSP is not null and tjs.FINISH_INSP is not null
                        AND ts.KD_STATUS < '900'
                        AND tsc.STATUS_CONT in (500,540,520)";
        $data = $dbConnection->selectOne($query);
        return $data;
    }
}
