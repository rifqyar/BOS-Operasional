<?php

namespace App\Livewire\Operation;

use App\Models\Operation;
use Livewire\Component;

class MarshallingCic extends Component
{
    public string $searchCont = '';

    public $operation = null;

    public $marshalling = null;

    public array $marshallings = [];

    public $containers = [];

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


        $this->operation = null;
        $this->marshalling = null;
        $this->marshallings = [];
        $this->containers = [];
        $this->message = null;
        $this->messageType = null;


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


        if (!$this->operation) {

            $this->messageType = 'danger';

            $this->message =
                "NO CONTAINER : {$keyword} NOT FOUND";

            return;
        }


        $this->marshalling =
            $this->operation->marshalling;

        $this->marshallings = $this->marshalling 
            ? [$this->marshalling] 
            : [];

        $this->containers = [
            $this->operation
        ];


        $this->messageType = 'success';

        $this->message =
            "NO CONTAINER : {$this->operation->container?->no_cont} FOUND";
    }


    public function resetSearch(): void
    {
        $this->searchCont = '';

        $this->operation = null;

        $this->marshalling = null;

        $this->marshallings = [];

        $this->containers = [];

        $this->message = null;

        $this->messageType = null;
    }


    public function render()
    {
        return view(
            'livewire.operation.marshalling-cic'
        );
    }
}