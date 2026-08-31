<?php

namespace App\Livewire\Operation;

use App\Models\Operation;
use Livewire\Component;

class Delivery extends Component
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

    public $delivery = null;


    /*
    |--------------------------------------------------------------------------
    | FORM
    |--------------------------------------------------------------------------
    */

    public string $truckNo = '';

    public string $gate = 'GATE 1';


    /*
    |--------------------------------------------------------------------------
    | STATE
    |--------------------------------------------------------------------------
    */

    public bool $hasSearched = false;

    /*
     * Source lama:
     *
     * status == 5
     * status == 1 -> TRUCK IN
     * status == 2 -> GATE OUT
     */

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
        | RESET HASIL SEBELUMNYA
        |--------------------------------------------------------------------------
        */

        $this->operations = [];

        $this->selectedOperation = null;

        $this->delivery = null;

        $this->truckNo = '';

        $this->gate = 'GATE 1';

        $this->status = 0;

        $this->message = null;

        $this->messageType = null;

        $this->hasSearched = true;


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
                'delivery',
                'deliveryLogs',
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
                'DELIVERY'
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
                . ' data container. Silakan pilih container.';

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
                    'delivery',
                    'deliveryLogs',
                ])

                ->find($id);


        if (!$this->selectedOperation) {

            $this->status = 0;

            $this->messageType = 'danger';

            $this->message =
                'Data operation tidak ditemukan.';

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | DELIVERY
        |--------------------------------------------------------------------------
        */

        $this->delivery =
            $this->selectedOperation->delivery;


        /*
        |--------------------------------------------------------------------------
        | TENTUKAN STATUS
        |--------------------------------------------------------------------------
        |
        | Source lama:
        |
        | status == 1
        | = TRUCK IN
        |
        | status == 2
        | = GATE OUT
        |
        */

        if (!$this->delivery) {

            /*
             * Belum ada data delivery.
             * Anggap tahap pertama = TRUCK IN.
             */

            $this->status = 1;

        } elseif (
            isset($this->delivery->status)
            && (string) $this->delivery->status === 'GATE_IN'
        ) {

            /*
             * Sudah Truck In.
             * Selanjutnya Gate Out.
             */

            $this->status = 2;

        } elseif (
            isset($this->delivery->status)
            && (string) $this->delivery->status === 'TRUCK_IN'
        ) {

            $this->status = 2;

        } elseif (
            isset($this->delivery->status)
            && (string) $this->delivery->status === 'DONE'
        ) {

            $this->status = 0;

            $this->messageType = 'success';

            $this->message =
                'Container sudah selesai DELIVERY.';

        } else {

            /*
             * Default aman.
             */

            $this->status = 1;
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
            . " FOUND";
    }


    /*
    |--------------------------------------------------------------------------
    | TRUCK IN
    |--------------------------------------------------------------------------
    */

    public function truckIn(): void
    {
        if (!$this->selectedOperation) {

            $this->messageType = 'danger';

            $this->message =
                'Silakan pilih container terlebih dahulu.';

            return;
        }


        $this->validate(
            [
                'truckNo' => [
                    'required',
                    'string',
                    'max:50',
                ],

                'gate' => [
                    'required',
                    'string',
                    'max:50',
                ],
            ],
            [
                'truckNo.required' =>
                    'No Truck wajib diisi.',

                'gate.required' =>
                    'Gate wajib dipilih.',
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | INSERT
        |--------------------------------------------------------------------------
        |
        | Belum dilakukan karena struktur field
        | OperationDelivery belum diberikan.
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
            . " SUCCESS TRUCK IN";
    }


    /*
    |--------------------------------------------------------------------------
    | GATE OUT
    |--------------------------------------------------------------------------
    */

    public function gateOut(): void
    {
        if (!$this->selectedOperation) {

            $this->messageType = 'danger';

            $this->message =
                'Silakan pilih container terlebih dahulu.';

            return;
        }


        $this->validate(
            [
                'gate' => [
                    'required',
                    'string',
                    'max:50',
                ],
            ],
            [
                'gate.required' =>
                    'Gate wajib dipilih.',
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | CEK TIDAK BOLEH GATE OUT
        |--------------------------------------------------------------------------
        */

        if (
            strtoupper(trim($this->gate))
            === 'TIDAK BOLEH GATE OUT'
        ) {

            $this->messageType = 'danger';

            $this->message =
                "WARNING ! NO CONT : "
                . (
                    $this->selectedOperation
                        ->container
                        ?->no_cont
                    ?? '-'
                )
                . " TIDAK BOLEH GATE OUT";

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | INSERT
        |--------------------------------------------------------------------------
        |
        | Belum dilakukan sampai struktur model
        | OperationDelivery dikonfirmasi.
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
            . " SUCCESS GATE OUT";
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

        $this->delivery = null;

        $this->truckNo = '';

        $this->gate = 'GATE 1';

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
            'livewire.operation.delivery'
        );
    }
}