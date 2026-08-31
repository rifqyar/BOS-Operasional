<?php

namespace App\Livewire\Operation;

use App\Models\Operation;
use Livewire\Component;

class MonitoringReefer extends Component
{
    /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

    public string $searchCont = '';


    /*
    |--------------------------------------------------------------------------
    | DATA
    |--------------------------------------------------------------------------
    */

    public $operations = [];

    public $selectedOperation = null;

    public $reefer = null;

    public $monitoringLogs = [];

    public $lastMonitoring = null;


    /*
    |--------------------------------------------------------------------------
    | FORM
    |--------------------------------------------------------------------------
    */

    public string $temperature = '';

    public string $note = '';


    /*
    |--------------------------------------------------------------------------
    | STATE
    |--------------------------------------------------------------------------
    */

    public bool $hasSearched = false;

    public int $condition = 0;


    /*
    |--------------------------------------------------------------------------
    | MESSAGE
    |--------------------------------------------------------------------------
    */

    public ?string $message = null;

    public ?string $messageType = null;


    /*
    |--------------------------------------------------------------------------
    | MOUNT
    |--------------------------------------------------------------------------
    */

    public function mount(): void
    {
        $this->resetState();
    }


    /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

    public function search(): void
    {
        $this->validate(
            [
                'searchCont' => [
                    'required',
                    'string',
                    'max:100',
                ],
            ],
            [
                'searchCont.required' =>
                    'No Container wajib diisi.',
            ]
        );


        $keyword = strtoupper(
            trim($this->searchCont)
        );


        /*
        |--------------------------------------------------------------------------
        | RESET DATA
        |--------------------------------------------------------------------------
        */

        $this->operations = [];

        $this->selectedOperation = null;

        $this->reefer = null;

        $this->monitoringLogs = [];

        $this->lastMonitoring = null;

        $this->temperature = '';

        $this->note = '';

        $this->condition = 0;

        $this->hasSearched = true;

        $this->message = null;

        $this->messageType = null;


        /*
        |--------------------------------------------------------------------------
        | SEARCH OPERATION
        |--------------------------------------------------------------------------
        */

        $this->operations = Operation::query()

            ->with([
                'spk',
                'container',
                'container.type',
                'reefer',
                'reeferMonitoringLogs',
            ])

            ->whereHas(
                'container',
                function ($query) use ($keyword) {

                    $query->where(
                        'no_cont',
                        'like',
                        '%' . $keyword . '%'
                    );
                }
            )

            ->where(
                'current_process',
                'REEFER'
            )

            ->orderByDesc('id')

            ->get();


        /*
        |--------------------------------------------------------------------------
        | NOT FOUND
        |--------------------------------------------------------------------------
        */

        if ($this->operations->isEmpty()) {

            $this->messageType = 'danger';

            $this->message =
                "WARNING ! NO CONT : {$keyword} NOT FOUND";

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | MULTIPLE RESULT
        |--------------------------------------------------------------------------
        */

        if ($this->operations->count() > 1) {

            $this->messageType = 'primary';

            $this->message =
                'Ditemukan '
                . $this->operations->count()
                . ' container. Silakan pilih container.';

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | SINGLE RESULT
        |--------------------------------------------------------------------------
        */

        $this->selectOperation(
            $this->operations->first()->id
        );
    }


    /*
    |--------------------------------------------------------------------------
    | SELECT CONTAINER
    |--------------------------------------------------------------------------
    */

    public function selectOperation(int $id): void
    {
        $this->selectedOperation =
            Operation::query()

                ->with([
                    'spk',
                    'container',
                    'container.type',
                    'reefer',
                    'reeferMonitoringLogs',
                ])

                ->find($id);


        if (!$this->selectedOperation) {

            $this->condition = 0;

            $this->messageType = 'danger';

            $this->message =
                'Data operation tidak ditemukan.';

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | REEFER
        |--------------------------------------------------------------------------
        */

        $this->reefer =
            $this->selectedOperation->reefer;


        /*
        |--------------------------------------------------------------------------
        | MONITORING LOG
        |--------------------------------------------------------------------------
        */

        $this->monitoringLogs =
            $this->selectedOperation
                ->reeferMonitoringLogs
                ?? [];


        /*
        |--------------------------------------------------------------------------
        | LAST MONITORING
        |--------------------------------------------------------------------------
        */

        $this->lastMonitoring =
            collect($this->monitoringLogs)
                ->sortByDesc('id')
                ->first();


        /*
        |--------------------------------------------------------------------------
        | CONDITION
        |--------------------------------------------------------------------------
        |
        | 1 = masih dapat monitoring
        | 0 = tidak dapat monitoring
        |
        | Source lama:
        | $kond == 1 -> tampil form monitoring
        | selain itu  -> sudah unplugin
        |
        */

        if ($this->reefer) {

            $this->condition = 1;

        } else {

            $this->condition = 0;
        }


        /*
        |--------------------------------------------------------------------------
        | LAST TEMPERATURE
        |--------------------------------------------------------------------------
        */

        if ($this->lastMonitoring) {

            $this->temperature =
                $this->lastMonitoring->temperature
                ?? '';
        }


        $this->messageType = 'success';

        $this->message =
            "NO CONT : "
            . (
                $this->selectedOperation
                    ->container
                    ?->no_cont
                ?? '-'
            )
            . " SIAP MONITORING";
    }


    /*
    |--------------------------------------------------------------------------
    | MONITORING
    |--------------------------------------------------------------------------
    */

    public function monitoring(): void
    {
        if (!$this->selectedOperation) {

            $this->messageType = 'danger';

            $this->message =
                'Silakan pilih container terlebih dahulu.';

            return;
        }


        if ($this->condition !== 1) {

            $this->messageType = 'danger';

            $this->message =
                'Container sudah UNPLUGIN atau tidak aktif.';

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        $this->validate(
            [
                'temperature' => [
                    'required',
                    'string',
                    'max:50',
                ],

                'note' => [
                    'nullable',
                    'string',
                    'max:1000',
                ],
            ],
            [
                'temperature.required' =>
                    'Temperature saat ini wajib diisi.',
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | DATABASE
        |--------------------------------------------------------------------------
        |
        | INSERT sengaja belum dilakukan.
        |
        | Kita membutuhkan model:
        |
        | OperationReeferMonitoringLog
        |
        | beserta field DB barunya.
        |
        */


        $this->messageType = 'success';

        $this->message =
            "NO CONT : "
            . (
                $this->selectedOperation
                    ->container
                    ?->no_cont
                ?? '-'
            )
            . " MONITORING BERHASIL";


        /*
        |--------------------------------------------------------------------------
        | REFRESH DATA
        |--------------------------------------------------------------------------
        */

        $this->selectedOperation->refresh();

        $this->monitoringLogs =
            $this->selectedOperation
                ->reeferMonitoringLogs
                ?? [];

        $this->lastMonitoring =
            collect($this->monitoringLogs)
                ->sortByDesc('id')
                ->first();
    }


    /*
    |--------------------------------------------------------------------------
    | RESET
    |--------------------------------------------------------------------------
    */

    public function resetSearch(): void
    {
        $this->resetState();
    }


    /*
    |--------------------------------------------------------------------------
    | RESET STATE
    |--------------------------------------------------------------------------
    */

    protected function resetState(): void
    {
        $this->searchCont = '';

        $this->operations = [];

        $this->selectedOperation = null;

        $this->reefer = null;

        $this->monitoringLogs = [];

        $this->lastMonitoring = null;

        $this->temperature = '';

        $this->note = '';

        $this->condition = 0;

        $this->hasSearched = false;

        $this->message = null;

        $this->messageType = null;
    }


    /*
    |--------------------------------------------------------------------------
    | RENDER
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        return view(
            'livewire.operation.monitoring-reefer'
        );
    }
}