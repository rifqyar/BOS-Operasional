<?php

namespace App\Livewire\Operation;

use App\Models\Spk;
use Livewire\Component;

class Pickup extends Component
{
    public string $searchSpk = '';

    public $spk = null;

    public $containers = [];

    public ?string $pickupMessage = null;

    public ?string $pickupMessageType = null;


    /**
     * SEARCH NO SPK
     */
    public function search(): void
    {
        $this->validate([
            'searchSpk' => [
                'required',
                'string',
                'max:100',
            ],
        ]);

        $keyword = strtoupper(trim($this->searchSpk));

        /*
        |--------------------------------------------------------------------------
        | RESET
        |--------------------------------------------------------------------------
        */

        $this->spk = null;
        $this->containers = [];

        $this->pickupMessage = null;
        $this->pickupMessageType = null;


        /*
        |--------------------------------------------------------------------------
        | SEARCH SPK
        |--------------------------------------------------------------------------
        */

        $this->spk = Spk::query()
            ->where('no_spk', 'like', '%' . $keyword . '%')
            ->first();


        /*
        |--------------------------------------------------------------------------
        | SPK NOT FOUND
        |--------------------------------------------------------------------------
        */

        if (!$this->spk) {

            $this->pickupMessageType = 'danger';

            $this->pickupMessage =
                "NO SPK : {$keyword} NOT FOUND";

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | GET CONTAINERS
        |--------------------------------------------------------------------------
        */

        $this->containers = $this->spk
            ->spkContainers()
            ->with([
                'container.type',
            ])
            ->get();


        /*
        |--------------------------------------------------------------------------
        | NO CONTAINER
        |--------------------------------------------------------------------------
        */

        if ($this->containers->isEmpty()) {

            $this->pickupMessageType = 'danger';

            $this->pickupMessage =
                "NO SPK : {$this->spk->no_spk} TIDAK MEMILIKI CONTAINER";

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | SUCCESS
        |--------------------------------------------------------------------------
        */

        $this->pickupMessageType = 'success';

        $this->pickupMessage =
            "NO SPK : {$this->spk->no_spk} FOUND";
    }


    public function render()
    {
        return view('livewire.operation.pickup');
    }
}