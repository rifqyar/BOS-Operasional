<?php

namespace App\Livewire\Operation;

use App\Models\Operation;
use Livewire\Component;

class Inspection extends Component
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

    public $inspection = null;


    /*
    |--------------------------------------------------------------------------
    | FORM
    |--------------------------------------------------------------------------
    */

    public string $noSeal = '';

    public string $containerType = '';

    public string $alat = '';

    public string $operator = '';


    /*
    |--------------------------------------------------------------------------
    | STATE
    |--------------------------------------------------------------------------
    */

    public bool $inspectionStarted = false;


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
        $this->searchCont = '';

        $this->operations = [];

        $this->selectedOperation = null;

        $this->inspection = null;

        $this->noSeal = '';

        $this->containerType = '';

        $this->alat = '';

        $this->operator = '';

        $this->inspectionStarted = false;

        $this->message = null;

        $this->messageType = null;
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
        | RESET STATE
        |--------------------------------------------------------------------------
        */

        $this->operations = [];

        $this->selectedOperation = null;

        $this->inspection = null;

        $this->inspectionStarted = false;

        $this->message = null;

        $this->messageType = null;


        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        |
        | Source lama:
        | search_realis
        |
        | DB baru:
        | operations -> container
        |
        */

        $this->operations = Operation::query()

            ->with([
                'spk',
                'container',
                'container.type',
                'inspection',
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
            | INSPECTION PROCESS
            |--------------------------------------------------------------------------
            */

            ->where(
                'current_process',
                'INSPECTION'
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
        | FOUND
        |--------------------------------------------------------------------------
        */

        if ($this->operations->count() === 1) {

            $this->selectOperation(
                $this->operations->first()->id
            );

            $this->messageType = 'primary';

            $this->message =
                "NO CONT : {$keyword} MULAI PEMERIKSAAN";

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | MULTIPLE CONTAINER
        |--------------------------------------------------------------------------
        */

        $this->messageType = 'primary';

        $this->message =
            "Ditemukan {$this->operations->count()} container. Silakan pilih container.";
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
                    'inspection',
                ])

                ->find($id);


        if (!$this->selectedOperation) {

            $this->messageType = 'danger';

            $this->message =
                'Data operation tidak ditemukan.';

            return;
        }


        $this->inspection =
            $this->selectedOperation->inspection;


        /*
        |--------------------------------------------------------------------------
        | LOAD EXISTING DATA
        |--------------------------------------------------------------------------
        */

        $this->containerType =
            $this->selectedOperation
                ->container
                ?->type
                ?->name
            ?? '';


        /*
        |--------------------------------------------------------------------------
        | EXISTING INSPECTION
        |--------------------------------------------------------------------------
        */

        if ($this->inspection) {

            $this->noSeal =
                $this->inspection->no_seal
                ?? '';

            $this->alat =
                $this->inspection->alat_id
                ?? '';

            $this->operator =
                $this->inspection->operator_id
                ?? '';

            $this->inspectionStarted =
                true;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | START INSPECTION
    |--------------------------------------------------------------------------
    */

    public function startInspection(): void
    {
        if (!$this->selectedOperation) {

            $this->messageType = 'danger';

            $this->message =
                'Silakan pilih container terlebih dahulu.';

            return;
        }


        $this->inspectionStarted = true;


        $this->messageType = 'primary';

        $this->message =
            'NO CONT : '
            . (
                $this->selectedOperation
                    ->container
                    ?->no_cont
                ?? '-'
            )
            . ' MULAI PEMERIKSAAN';
    }


    /*
    |--------------------------------------------------------------------------
    | FINISH INSPECTION
    |--------------------------------------------------------------------------
    |
    | Untuk sementara tidak melakukan INSERT / UPDATE.
    |
    | Kita hanya menyelesaikan flow UI terlebih dahulu
    | karena struktur tabel inspection DB baru belum diberikan.
    |
    */

    public function finishInspection(): void
    {
        if (!$this->selectedOperation) {

            $this->messageType = 'danger';

            $this->message =
                'Container belum dipilih.';

            return;
        }


        $this->validate(
            [
                'noSeal' => [
                    'required',
                    'string',
                    'max:100',
                ],

                'containerType' => [
                    'required',
                    'string',
                    'max:50',
                ],
            ],
            [
                'noSeal.required' =>
                    'No Seal wajib diisi.',

                'containerType.required' =>
                    'Type Container wajib dipilih.',
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | IMPORTANT
        |--------------------------------------------------------------------------
        |
        | Belum melakukan database update.
        |
        | Setelah struktur OperationInspection diberikan,
        | bagian ini bisa kita sambungkan ke DB baru.
        |
        */

        $this->messageType = 'primary';

        $this->message =
            'NO CONT : '
            . (
                $this->selectedOperation
                    ->container
                    ?->no_cont
                ?? '-'
            )
            . ' SELESAI PEMERIKSAAN';
    }


    /*
    |--------------------------------------------------------------------------
    | RESET
    |--------------------------------------------------------------------------
    */

    public function resetSearch(): void
    {
        $this->searchCont = '';

        $this->operations = [];

        $this->selectedOperation = null;

        $this->inspection = null;

        $this->noSeal = '';

        $this->containerType = '';

        $this->alat = '';

        $this->operator = '';

        $this->inspectionStarted = false;

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
            'livewire.operation.inspection'
        );
    }
}