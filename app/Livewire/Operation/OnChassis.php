<?php

namespace App\Livewire\Operation;

use App\Models\Operation;
use Livewire\Component;

class OnChassis extends Component
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

    public $chassis = null;


    /*
    |--------------------------------------------------------------------------
    | STATE
    |--------------------------------------------------------------------------
    */

    public bool $hasSearched = false;

    public int $status = 0;


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
    | SEARCH CONTAINER
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
        | RESET RESULT
        |--------------------------------------------------------------------------
        */

        $this->operations = [];

        $this->selectedOperation = null;

        $this->chassis = null;

        $this->status = 0;

        $this->message = null;

        $this->messageType = null;

        $this->hasSearched = true;


        /*
        |--------------------------------------------------------------------------
        | SEARCH OPERATION
        |--------------------------------------------------------------------------
        |
        | Mengambil operation berdasarkan nomor container.
        |
        */

        $this->operations = Operation::query()
            ->with([
                'spk',
                'container',
                'container.type',
                'chassis',
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

        $this->messageType = 'success';

        $this->message =
            'Ditemukan '
            . $this->operations->count()
            . ' data container.';

        $this->status = 1;
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
                    'chassis',
                ])
                ->find($id);


        /*
        |--------------------------------------------------------------------------
        | OPERATION NOT FOUND
        |--------------------------------------------------------------------------
        */

        if (!$this->selectedOperation) {

            $this->messageType = 'danger';

            $this->message =
                'Data operation tidak ditemukan.';

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | GET CHASSIS
        |--------------------------------------------------------------------------
        */

        $this->chassis =
            $this->selectedOperation->chassis;


        /*
        |--------------------------------------------------------------------------
        | DETAIL MODE
        |--------------------------------------------------------------------------
        */

        $this->status = 2;


        $this->messageType = 'success';

        $this->message =
            "NO CONT : "
            . (
                $this->selectedOperation
                    ->container
                    ?->no_cont
                ?? '-'
            )
            . " SELECTED";
    }


    /*
    |--------------------------------------------------------------------------
    | ON CHASSIS
    |--------------------------------------------------------------------------
    */

    public function onChassis(): void
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDATE SELECTED OPERATION
        |--------------------------------------------------------------------------
        */

        if (!$this->selectedOperation) {

            $this->messageType = 'danger';

            $this->message =
                'Silakan pilih container terlebih dahulu.';

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | CONTAINER
        |--------------------------------------------------------------------------
        */

        $containerNumber =
            $this->selectedOperation
                ->container
                ?->no_cont
            ?? '-';


        /*
        |--------------------------------------------------------------------------
        | DATABASE ACTION
        |--------------------------------------------------------------------------
        |
        | Untuk sementara BELUM melakukan INSERT / UPDATE.
        |
        | Source CI lama:
        |
        | operation/chases
        |
        | Tetapi struktur tabel operation_chassis beserta
        | field yang digunakan untuk proses ON CHASSIS
        | belum diberikan secara lengkap.
        |
        | Jadi tidak dibuat asumsi terhadap nama kolom.
        |
        */


        $this->messageType = 'success';

        $this->message =
            "NO CONT : "
            . $containerNumber
            . " ON CHASSIS SUCCESS";
    }


    /*
    |--------------------------------------------------------------------------
    | RESET SEARCH
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

        $this->chassis = null;

        $this->status = 0;

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
            'livewire.operation.on-chassis'
        );
    }
}