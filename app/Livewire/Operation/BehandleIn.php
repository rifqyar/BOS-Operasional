<?php

namespace App\Livewire\Operation;

use App\Models\Container;
use Livewire\Component;

class BehandleIn extends Component
{
    public string $searchCont = '';

    public $containers = [];

    public ?string $message = null;

    public ?string $messageType = null;


    /**
     * SEARCH CONTAINER
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

                'searchCont.max' =>
                    'No Container maksimal 100 karakter.',
            ]
        );


        $keyword = strtoupper(
            trim($this->searchCont)
        );


        /*
        |--------------------------------------------------------------------------
        | RESET HASIL SEBELUMNYA
        |--------------------------------------------------------------------------
        */

        $this->containers = [];

        $this->message = null;

        $this->messageType = null;


        /*
        |--------------------------------------------------------------------------
        | SEARCH CONTAINER
        |--------------------------------------------------------------------------
        */

        $this->containers = Container::query()

            ->with([
                'type',
            ])

            ->where(
                'no_cont',
                'like',
                '%' . $keyword . '%'
            )

            ->orderBy(
                'no_cont'
            )

            ->get();


        /*
        |--------------------------------------------------------------------------
        | NOT FOUND
        |--------------------------------------------------------------------------
        */

        if ($this->containers->isEmpty()) {

            $this->messageType = 'danger';

            $this->message =
                "NO CONTAINER : {$keyword} NOT FOUND";

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | FOUND
        |--------------------------------------------------------------------------
        */

        $this->messageType = 'success';

        $this->message =
            "NO CONTAINER : {$keyword} FOUND";
    }


    /**
     * RESET SEARCH
     */
    public function resetSearch(): void
    {
        $this->searchCont = '';

        $this->containers = [];

        $this->message = null;

        $this->messageType = null;
    }


    /**
     * RENDER
     */
    public function render()
    {
        return view(
            'livewire.operation.behandle-in'
        );
    }
}