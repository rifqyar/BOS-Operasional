<?php

namespace App\Livewire\Operation;

use App\Models\Operation;
use Livewire\Component;

class MarshallingYard extends Component
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

    public $selectedMarshalling = null;


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

        $this->selectedMarshalling = null;

        $this->message = null;

        $this->messageType = null;
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


        /*
        |--------------------------------------------------------------------------
        | NORMALIZE
        |--------------------------------------------------------------------------
        */

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

        $this->selectedMarshalling = null;

        $this->message = null;

        $this->messageType = null;


        /*
        |--------------------------------------------------------------------------
        | SEARCH OPERATION
        |--------------------------------------------------------------------------
        |
        | Source lama:
        |
        | search berdasarkan No Container
        |
        | Pada DB baru:
        |
        | operations
        |   -> container
        |   -> marshalling
        |
        */

        $query = Operation::query()

            ->with([
                'spk',
                'container',
                'container.type',

                'marshalling',
                'marshalling.jobSlip',
                'marshalling.locationFrom',
                'marshalling.locationTo',
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
            | HANYA OPERATION MARSHALLING
            |--------------------------------------------------------------------------
            */

            ->where(
                'current_process',
                'MARSHALLING'
            )

            ->orderByDesc('id');


        /*
        |--------------------------------------------------------------------------
        | GET DATA
        |--------------------------------------------------------------------------
        */

        $this->operations = $query->get();


        /*
        |--------------------------------------------------------------------------
        | NOT FOUND
        |--------------------------------------------------------------------------
        */

        if ($this->operations->isEmpty()) {

            $this->messageType = 'danger';

            $this->message =
                "NO CONTAINER : {$keyword} NOT FOUND";

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | SELECT FIRST DATA
        |--------------------------------------------------------------------------
        */

        $this->selectedOperation =
            $this->operations->first();


        $this->selectedMarshalling =
            $this->selectedOperation?->marshalling;


        /*
        |--------------------------------------------------------------------------
        | SUCCESS
        |--------------------------------------------------------------------------
        */

        $this->messageType = 'success';

        $this->message =
            'NO CONTAINER : '
            . (
                $this->selectedOperation
                    ?->container
                    ?->no_cont
                ?? $keyword
            )
            . ' FOUND';
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

                    'marshalling',
                    'marshalling.jobSlip',
                    'marshalling.locationFrom',
                    'marshalling.locationTo',
                ])

                ->find($id);


        if (!$this->selectedOperation) {

            $this->selectedMarshalling = null;

            return;
        }


        $this->selectedMarshalling =
            $this->selectedOperation->marshalling;
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

        $this->selectedMarshalling = null;

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
            'livewire.operation.marshalling-yard'
        );
    }
}