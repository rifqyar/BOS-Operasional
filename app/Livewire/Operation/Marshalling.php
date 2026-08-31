<?php

namespace App\Livewire\Operation;

use App\Models\Operation;
use Livewire\Component;

class Marshalling extends Component
{
    /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

    public string $searchCont = '';


    /*
    |--------------------------------------------------------------------------
    | RESULT
    |--------------------------------------------------------------------------
    */

    public $operation = null;

    public $marshalling = null;

    public array $containers = [];


    /*
    |--------------------------------------------------------------------------
    | MESSAGE
    |--------------------------------------------------------------------------
    */

    public ?string $message = null;

    public ?string $messageType = null;


    /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

    public function search(): void
    {
        $this->validate([
            'searchCont' => [
                'required',
                'string',
                'max:100',
            ],
        ], [
            'searchCont.required' => 'No Container wajib diisi.',
        ]);


        $keyword = strtoupper(
            trim($this->searchCont)
        );


        /*
        |--------------------------------------------------------------------------
        | RESET RESULT
        |--------------------------------------------------------------------------
        */

        $this->operation = null;

        $this->marshalling = null;

        $this->containers = [];

        $this->message = null;

        $this->messageType = null;


        /*
        |--------------------------------------------------------------------------
        | SEARCH OPERATION
        |--------------------------------------------------------------------------
        |
        | DB BARU:
        |
        | operations
        |     -> container
        |     -> spk
        |     -> marshalling
        |
        */

        $this->operation = Operation::query()

            ->with([
                'spk',
                'container.type',
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

            ->where(
                'current_process',
                'MARSHALLING'
            )

            ->orderByDesc('id')

            ->first();


        /*
        |--------------------------------------------------------------------------
        | NOT FOUND
        |--------------------------------------------------------------------------
        */

        if (!$this->operation) {

            $this->messageType = 'danger';

            $this->message =
                "NO CONTAINER : {$keyword} NOT FOUND";

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | MARSHALLING
        |--------------------------------------------------------------------------
        */

        $this->marshalling =
            $this->operation->marshalling;


        /*
        |--------------------------------------------------------------------------
        | CONTAINER RESULT
        |--------------------------------------------------------------------------
        |
        | View kita sebelumnya memakai $containers.
        | Tetap kita isi agar tidak perlu mengubah template
        | yang sudah dibuat.
        |
        */

        $this->containers = [
            $this->operation,
        ];


        /*
        |--------------------------------------------------------------------------
        | FOUND
        |--------------------------------------------------------------------------
        */

        $this->messageType = 'success';

        $this->message =
            "NO CONTAINER : {$this->operation->container?->no_cont} FOUND";
    }


    /*
    |--------------------------------------------------------------------------
    | RESET SEARCH
    |--------------------------------------------------------------------------
    */

    public function resetSearch(): void
    {
        $this->searchCont = '';

        $this->operation = null;

        $this->marshalling = null;

        $this->containers = [];

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
            'livewire.operation.marshalling'
        );
    }
}