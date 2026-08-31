<?php

namespace App\Livewire\Operation;

use Livewire\Component;

class Hold extends Component
{
    public string $searchCont = '';

    public array $containers = [];

    public $selectedContainer = null;

    public array $holds = [];

    public string $warna = '';

    public ?string $message = null;

    public ?string $messageType = null;


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
        | RESET HASIL SEARCH
        |--------------------------------------------------------------------------
        */

        $this->containers = [];

        $this->selectedContainer = null;

        $this->message = null;

        $this->messageType = null;


        /*
        |--------------------------------------------------------------------------
        | TEMPORARY
        |--------------------------------------------------------------------------
        |
        | Database HOLD belum kita hubungkan.
        | Ini hanya untuk memastikan template bisa berjalan.
        |
        */

        $this->messageType = 'danger';

        $this->message =
            "NO CONTAINER : {$keyword} BELUM TERHUBUNG KE DATABASE HOLD";
    }


    public function selectContainer($containerId): void
    {
        /*
        |--------------------------------------------------------------------------
        | TEMPORARY
        |--------------------------------------------------------------------------
        |
        | Logic pemilihan container akan kita isi setelah
        | mapping DB diagram + Controller PHP lama selesai.
        |
        */

        $this->selectedContainer = null;
    }


    public function hold(): void
    {
        /*
        |--------------------------------------------------------------------------
        | TEMPORARY
        |--------------------------------------------------------------------------
        */

        $this->messageType = 'danger';

        $this->message =
            'Proses HOLD belum dihubungkan ke database.';
    }


    public function release($holdId = null): void
    {
        /*
        |--------------------------------------------------------------------------
        | TEMPORARY
        |--------------------------------------------------------------------------
        */

        $this->messageType = 'danger';

        $this->message =
            'Proses RELEASE belum dihubungkan ke database.';
    }


    public function resetSearch(): void
    {
        $this->searchCont = '';

        $this->containers = [];

        $this->selectedContainer = null;

        $this->holds = [];

        $this->warna = '';

        $this->message = null;

        $this->messageType = null;
    }


    public function render()
    {
        return view(
            'livewire.operation.hold'
        );
    }
}