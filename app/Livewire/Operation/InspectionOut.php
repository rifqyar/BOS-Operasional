<?php

namespace App\Livewire\Operation;

use App\Models\Operation;
use Livewire\Component;

class InspectionOut extends Component
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

    public $inspectionOut = null;


    /*
    |--------------------------------------------------------------------------
    | FORM
    |--------------------------------------------------------------------------
    */

    public string $sealCondition = 'ada';

    public string $sealNo = '';

    public string $containerCondition = '';


    /*
    |--------------------------------------------------------------------------
    | STATE
    |--------------------------------------------------------------------------
    */

    public bool $hasSearched = false;

    public int $status = 0;


    /*
    |--------------------------------------------------------------------------
    | CONDITION OPTIONS
    |--------------------------------------------------------------------------
    |
    | Source lama mengambil data kondisi dari database.
    |
    | Untuk sementara opsi dibuat berdasarkan kebutuhan UI.
    | Jika nanti ada model master kondisi, bagian ini kita
    | ganti dengan query master tersebut.
    |
    */

    public array $conditions = [
        [
            'id' => 'BAIK',
            'name' => 'BAIK',
        ],
        [
            'id' => 'RUSAK',
            'name' => 'RUSAK',
        ],
        [
            'id' => 'PENYOK',
            'name' => 'PENYOK',
        ],
        [
            'id' => 'BERKARAT',
            'name' => 'BERKARAT',
        ],
    ];


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
        | RESET
        |--------------------------------------------------------------------------
        */

        $this->operations = [];

        $this->selectedOperation = null;

        $this->inspectionOut = null;

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
                'inspectionOut',
                'inspectionOutLogs',
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
                    'inspectionOut',
                    'inspectionOutLogs',
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
        | EXISTING INSPECTION OUT
        |--------------------------------------------------------------------------
        */

        $this->inspectionOut =
            $this->selectedOperation->inspectionOut;


        /*
        |--------------------------------------------------------------------------
        | SET DEFAULT FORM
        |--------------------------------------------------------------------------
        */

        $this->sealNo =
            $this->inspectionOut?->seal_no
            ?? $this->selectedOperation
                ->container
                ?->seal_no
            ?? '';


        $this->sealCondition =
            $this->inspectionOut?->seal_condition
            ?? 'ada';


        $this->containerCondition =
            $this->inspectionOut?->container_condition
            ?? '';


        /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        |
        | Source lama:
        |
        | status == 1
        | = daftar container
        |
        | status == 2
        | = form inspection out
        |
        */

        $this->status = 2;


        /*
        |--------------------------------------------------------------------------
        | MESSAGE
        |--------------------------------------------------------------------------
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
            . " FOUND";
    }


    /*
    |--------------------------------------------------------------------------
    | INSPECTION OUT
    |--------------------------------------------------------------------------
    */

    public function submitInspectionOut(): void
    {
        if (!$this->selectedOperation) {

            $this->messageType = 'danger';

            $this->message =
                'Silakan pilih container terlebih dahulu.';

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        $this->validate(
            [
                'sealCondition' => [
                    'required',
                    'in:ada,tidak ada',
                ],

                'sealNo' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                'containerCondition' => [
                    'required',
                    'string',
                    'max:100',
                ],
            ],
            [
                'sealCondition.required' =>
                    'Kondisi seal wajib dipilih.',

                'sealNo.max' =>
                    'No Seal maksimal 100 karakter.',

                'containerCondition.required' =>
                    'Kondisi container wajib dipilih.',
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | VALIDASI SEAL
        |--------------------------------------------------------------------------
        */

        if (
            $this->sealCondition === 'ada'
            && trim($this->sealNo) === ''
        ) {

            $this->addError(
                'sealNo',
                'No Seal wajib diisi jika seal ada.'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | DATABASE
        |--------------------------------------------------------------------------
        |
        | BELUM INSERT.
        |
        | Nama field OperationInspectionOut belum diberikan.
        | Jangan menebak field karena DB baru menggunakan
        | struktur yang berbeda dari CI lama.
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
            . " SUCCESS INSPECTION OUT";
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

        $this->inspectionOut = null;

        $this->sealCondition = 'ada';

        $this->sealNo = '';

        $this->containerCondition = '';

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
            'livewire.operation.inspection-out'
        );
    }
}