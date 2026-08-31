<?php

namespace App\Livewire\Operation;

use App\Models\Operation;
use Livewire\Component;

class PlugReefer extends Component
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

    public $monitoring = null;


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

    public int $condition = 0;


    /*
    | 0 = belum ada data
    | 1 = siap PLUGIN
    | 2 = sudah PLUGIN / UNPLUGIN
    */

    public bool $hasSearched = false;


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
        | RESET HASIL
        |--------------------------------------------------------------------------
        */

        $this->operations = [];

        $this->selectedOperation = null;

        $this->reefer = null;

        $this->monitoring = null;

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
        |
        | PLUG REEFER bekerja berdasarkan operation.
        |
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

            /*
            |--------------------------------------------------------------------------
            | REEFER
            |--------------------------------------------------------------------------
            |
            | Container reefer biasanya masuk proses REEFER.
            |
            */

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
                "Ditemukan {$this->operations->count()} container. Silakan pilih container.";

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
    | SELECT OPERATION
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
        | REEFER DATA
        |--------------------------------------------------------------------------
        */

        $this->reefer =
            $this->selectedOperation->reefer;


        /*
        |--------------------------------------------------------------------------
        | MONITORING DATA
        |--------------------------------------------------------------------------
        */

        $this->monitoring =
            $this->selectedOperation
                ->reeferMonitoringLogs
                ?->sortByDesc('id')
                ?->first();


        /*
        |--------------------------------------------------------------------------
        | DETERMINE CONDITION
        |--------------------------------------------------------------------------
        |
        | Source lama memiliki:
        |
        | kond == 1
        | -> MULAI PLUGIN
        |
        | kond == 2
        | -> UNPLUGIN REEFER
        |
        | Kita tentukan berdasarkan data reefer / monitoring
        | yang tersedia di DB baru.
        |
        */

        if ($this->reefer || $this->monitoring) {

            $this->condition = 2;

        } else {

            $this->condition = 1;

        }


        /*
        |--------------------------------------------------------------------------
        | LOAD LAST TEMPERATURE
        |--------------------------------------------------------------------------
        */

        if ($this->monitoring) {

            $this->temperature =
                $this->monitoring->temperature
                ?? '';

        }


        $this->messageType = 'primary';

        $this->message =
            "NO CONT : "
            . (
                $this->selectedOperation
                    ->container
                    ?->no_cont
                ?? '-'
            )
            . (
                $this->condition === 1
                    ? ' SIAP PLUGIN REEFER'
                    : ' SUDAH PLUGIN REEFER'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | START PLUGIN
    |--------------------------------------------------------------------------
    */

    public function startPlugin(): void
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
                'Container tidak dalam kondisi siap plugin.';

            return;
        }


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
        | BELUM INSERT.
        |
        | Kita tidak mengarang field OperationReefer karena
        | struktur model tersebut belum diberikan.
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
            . " PLUGIN KONTAINER";


        /*
        |--------------------------------------------------------------------------
        | UI STATE
        |--------------------------------------------------------------------------
        */

        $this->condition = 2;
    }


    /*
    |--------------------------------------------------------------------------
    | UNPLUGIN REEFER
    |--------------------------------------------------------------------------
    */

    public function unplugReefer(): void
    {
        if (!$this->selectedOperation) {

            $this->messageType = 'danger';

            $this->message =
                'Container belum dipilih.';

            return;
        }


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
                    'Temperature terakhir wajib diisi.',
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | DATABASE
        |--------------------------------------------------------------------------
        |
        | BELUM UPDATE.
        |
        | Struktur field DB baru belum diberikan.
        |
        */


        $this->messageType = 'primary';

        $this->message =
            "NO CONT : "
            . (
                $this->selectedOperation
                    ->container
                    ?->no_cont
                ?? '-'
            )
            . " UNPLUGIN KONTAINER";
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

        $this->monitoring = null;

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
            'livewire.operation.plug-reefer'
        );
    }
}