<?php

namespace App\Livewire\Operation;

use App\Models\BehandleReport;
use App\Models\Equipment;
use App\Models\Gatepass;
use App\Models\JobDetail;
use App\Models\JobSlip;
use App\Models\Operation;
use App\Models\OperationInspection;
use App\Models\SystemUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Inspection extends Component
{
    public string $searchCont = '';

    public $operations = [];

    public ?Operation $selectedOperation = null;

    public ?OperationInspection $inspection = null;

    public ?JobSlip $jobSlip = null;

    public string $noSeal = '';

    public ?int $alat = null;

    public ?int $operator = null;

    public bool $inspectionStarted = false;

    public ?string $message = null;

    public ?string $messageType = null;

    // === TAMBAHAN: LANJUT BEHANDLE 2 ===
    public bool $showBehandle2Form = false;

    public string $b2NoDok = '';

    public string $b2JnsDok = '';

    public ?string $b2TglDok = null;

    public function mount(): void
    {
        $this->resetState();
    }

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

        $this->resetSelection();

        $this->operations = Operation::query()
            ->with(['spk', 'container.type', 'container.currentLocation'])
            ->whereHas('container', function ($query) use ($keyword) {
                $query->where('no_cont', 'like', '%' . $keyword . '%');
            })
            ->whereHas('spk', function ($query) {
                $query->whereHas('containers', function ($query) {
                    $query->where('status', 'READY');
                });
            })
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('operations')
                    ->groupBy('container_id');
            })
            ->orderByDesc('id')
            ->get();

        if ($this->operations->isEmpty()) {
            $this->messageType = 'danger';

            $this->message =
                "WARNING ! NO CONT : {$keyword} NOT FOUND";

            return;
        }

        if ($this->operations->count() === 1) {
            $this->selectOperation(
                $this->operations->first()->id
            );

            return;
        }

        $this->messageType = 'primary';

        $this->message =
            "Ditemukan {$this->operations->count()} data. "
            . 'Silakan pilih container.';
    }

    public function selectOperation(int $id): void
    {
        $this->selectedOperation = Operation::query()
            ->with([
                'spk',
                'container.type',
                'container.currentLocation',
            ])
            ->find($id);

        if (!$this->selectedOperation) {
            $this->messageType = 'danger';

            $this->message =
                'Data operation tidak ditemukan.';

            return;
        }

        $this->loadCurrentInspection();

        $this->message = null;

        $this->messageType = null;
    }

    public function startInspection(): void
    {
        if (!$this->selectedOperation) {
            $this->messageType = 'danger';

            $this->message =
                'Silakan pilih container terlebih dahulu.';

            return;
        }

        $this->validate(
            [
                'operator' => [
                    'required',
                    'integer',
                    'exists:users,id',
                ],
                'alat' => [
                    'required',
                    'integer',
                    'exists:equipments,id',
                ],
            ],
            [
                'operator.required' =>
                    'Operator wajib dipilih.',
                'alat.required' =>
                    'Alat wajib dipilih.',
            ]
        );

        try {
            DB::transaction(
                function () {
                    $operation = Operation::query()
                        ->with([
                            'spk',
                            'container',
                        ])
                        ->lockForUpdate()
                        ->find(
                            $this->selectedOperation->id
                        );

                    if (!$operation) {
                        throw ValidationException::withMessages(
                            [
                                'searchCont' =>
                                    'Data operation tidak ditemukan.',
                            ]
                        );
                    }

                    $container =
                        $operation->container;

                    if (!$container) {
                        throw ValidationException::withMessages(
                            [
                                'searchCont' =>
                                    'Container tidak ditemukan.',
                            ]
                        );
                    }

                    $spkContainer =
                        $operation->spk
                            ->containers()
                            ->where(
                                'container_id',
                                $container->id
                            )
                            ->orderByDesc('id')
                            ->lockForUpdate()
                            ->first();

                    if (!$spkContainer) {
                        throw ValidationException::withMessages(
                            [
                                'searchCont' =>
                                    'SPK Container tidak ditemukan.',
                            ]
                        );
                    }

                    if (
                        (string) $spkContainer->status
                        !== 'READY'
                    ) {
                        throw ValidationException::withMessages(
                            [
                                'searchCont' =>
                                    'Container tidak berada pada status READY.',
                            ]
                        );
                    }

                    $jobSlip = JobSlip::query()
                        ->with('gatepass')
                        ->where(
                            'spk_container_id',
                            $spkContainer->id
                        )
                        ->orderByDesc('id')
                        ->lockForUpdate()
                        ->first();

                    if (!$jobSlip) {
                        throw ValidationException::withMessages(
                            [
                                'searchCont' =>
                                    'Job Slip tidak ditemukan.',
                            ]
                        );
                    }

                    $jenisKegiatan =
                        (string) (
                            $jobSlip
                                ->gatepass
                                ?->jenis_kegiatan
                            ?? ''
                        );

                    if (
                        !in_array(
                            $jenisKegiatan,
                            ['1', '2'],
                            true
                        )
                    ) {
                        throw ValidationException::withMessages(
                            [
                                'searchCont' =>
                                    'Jenis kegiatan Gatepass tidak valid.',
                            ]
                        );
                    }

                    $existing =
                        OperationInspection::query()
                            ->where(
                                'operation_id',
                                $operation->id
                            )
                            ->where(
                                'status',
                                'WAITING'
                            )
                            ->latest('id')
                            ->lockForUpdate()
                            ->first();

                    if ($existing) {
                        throw ValidationException::withMessages(
                            [
                                'searchCont' =>
                                    'Pemeriksaan container ini sedang berjalan.',
                            ]
                        );
                    }

                    OperationInspection::create(
                        [
                            'operation_id' =>
                                $operation->id,

                            'job_slip_id' =>
                                $jobSlip->id,

                            'behandlein_id' =>
                                $operation
                                    ->behandlein
                                    ?->id,

                            'equipment_id' =>
                                $this->alat,

                            'operator_id' =>
                                $this->operator,

                            'no_seal' => null,

                            'container_type_id' =>
                                $container
                                    ->container_type_id,

                            'started_at' => now(),

                            'finished_at' => null,

                            'status' => 'WAITING',

                            'note' => null,
                        ]
                    );

                    $operation->update(
                        [
                            'started_at' => now(),
                        ]
                    );

                    $report = $this->findReport(
                        $container->no_cont,
                        $operation->spk_id
                    );

                    if ($report) {
                        if (
                            $jenisKegiatan === '1'
                        ) {
                            $report->update(
                                [
                                    'pb1_marshalling_b1' =>
                                        now(),
                                ]
                            );
                        } else {
                            $report->update(
                                [
                                    'pb2_marshalling_b2' =>
                                        now(),
                                ]
                            );
                        }
                    }
                }
            );

            $this->loadCurrentInspection();

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

        } catch (ValidationException $e) {
            $this->messageType = 'danger';

            $this->message =
                collect(
                    $e->errors()
                )->flatten()->first();

        } catch (\Throwable $e) {
            report($e);

            $this->messageType = 'danger';

            $this->message =
                'Pemeriksaan gagal dimulai.';
        }
    }

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
            ],
            [
                'noSeal.required' =>
                    'No Seal wajib diisi.',
            ]
        );

        try {
            DB::transaction(
                function () {
                    $operation = Operation::query()
                        ->with([
                            'spk',
                            'container',
                        ])
                        ->lockForUpdate()
                        ->find(
                            $this->selectedOperation->id
                        );

                    if (!$operation) {
                        throw ValidationException::withMessages(
                            [
                                'noSeal' =>
                                    'Data operation tidak ditemukan.',
                            ]
                        );
                    }

                    $container =
                        $operation->container;

                    if (!$container) {
                        throw ValidationException::withMessages(
                            [
                                'noSeal' =>
                                    'Container tidak ditemukan.',
                            ]
                        );
                    }

                    $inspection =
                        OperationInspection::query()
                            ->where(
                                'operation_id',
                                $operation->id
                            )
                            ->where(
                                'status',
                                'WAITING'
                            )
                            ->latest('id')
                            ->lockForUpdate()
                            ->first();

                    if (!$inspection) {
                        throw ValidationException::withMessages(
                            [
                                'noSeal' =>
                                    'Pemeriksaan belum dimulai atau sudah selesai.',
                            ]
                        );
                    }

                    $spkContainer =
                        $operation->spk
                            ->containers()
                            ->where(
                                'container_id',
                                $container->id
                            )
                            ->orderByDesc('id')
                            ->lockForUpdate()
                            ->first();

                    if (!$spkContainer) {
                        throw ValidationException::withMessages(
                            [
                                'noSeal' =>
                                    'SPK Container tidak ditemukan.',
                            ]
                        );
                    }

                    $jobSlip = JobSlip::query()
                        ->with('gatepass')
                        ->where(
                            'id',
                            $inspection->job_slip_id
                        )
                        ->lockForUpdate()
                        ->first();

                    if (!$jobSlip) {
                        throw ValidationException::withMessages(
                            [
                                'noSeal' =>
                                    'Job Slip pemeriksaan tidak ditemukan.',
                            ]
                        );
                    }

                    $jenisKegiatan =
                        (string) (
                            $jobSlip
                                ->gatepass
                                ?->jenis_kegiatan
                            ?? ''
                        );

                    if (
                        !in_array(
                            $jenisKegiatan,
                            ['1', '2'],
                            true
                        )
                    ) {
                        throw ValidationException::withMessages(
                            [
                                'noSeal' =>
                                    'Jenis kegiatan Gatepass tidak valid.',
                            ]
                        );
                    }

                    $seal = strtoupper(
                        trim($this->noSeal)
                    );

                    $inspection->update(
                        [
                            'no_seal' => $seal,

                            'operator_id' =>
                                $this->operator
                                ?: $inspection
                                    ->operator_id,

                            'equipment_id' =>
                                $this->alat
                                ?: $inspection
                                    ->equipment_id,

                            'finished_at' => now(),

                            'status' => 'DONE',
                        ]
                    );

                    $operation->update(
                        [
                            'finished_at' => now(),
                        ]
                    );

                    $container->update(
                        [
                            'no_seal' => $seal,
                        ]
                    );

                    $spkContainer->update(
                        [
                            'status' => '500',
                        ]
                    );

                    if ($operation->spk) {
                        $operation
                            ->spk
                            ->update(
                                [
                                    'status' => '500',
                                ]
                            );
                    }

                    $jobSlip->update(
                        [
                            'job_type' =>
                                $jenisKegiatan === '1'
                                    ? 'EX BEHANDLE 1'
                                    : 'EX BEHANDLE 2',

                            'status' => 'WAITING',
                        ]
                    );

                    JobDetail::create(
                        [
                            'job_slip_id' =>
                                $jobSlip->id,

                            'equipment_id' =>
                                $this->alat
                                ?: $inspection
                                    ->equipment_id,

                            'operator_id' =>
                                $this->operator
                                ?: $inspection
                                    ->operator_id,

                            'job_activity_code' =>
                                null,

                            'status' => 'DONE',

                            'job_activity_code_2' =>
                                null,

                            'truck_id' =>
                                null,

                            'operator_id_2' =>
                                null,

                            'job_activity_code_3' =>
                                null,

                            'equipment_id_3' =>
                                null,

                            'operator_id_3' =>
                                null,
                        ]
                    );

                    $report = $this->findReport(
                        $container->no_cont,
                        $operation->spk_id
                    );

                    if ($report) {
                        $report->update(
                            [
                                'no_seal' => $seal,
                            ]
                        );
                    }
                }
            );

            $this->loadCurrentInspection();

            $this->inspectionStarted = false;

            $this->messageType = 'success';

            $this->message =
                'NO CONT : '
                . (
                    $this->selectedOperation
                        ->container
                        ?->no_cont
                    ?? '-'
                )
                . ' SELESAI PEMERIKSAAN';

        } catch (ValidationException $e) {
            $this->messageType = 'danger';

            $this->message =
                collect(
                    $e->errors()
                )->flatten()->first();

        } catch (\Throwable $e) {
            report($e);

            $this->messageType = 'danger';

            $this->message =
                'Pemeriksaan gagal diselesaikan.';
        }
    }

    // === TAMBAHAN: LANJUT BEHANDLE 2 ===

    public function openBehandle2Form(): void
    {
        if (!$this->inspection || $this->inspection->status !== 'DONE') {
            return;
        }

        $jenisKegiatan = (string) ($this->jobSlip?->gatepass?->jenis_kegiatan ?? '');

        if ($jenisKegiatan !== '1') {
            $this->messageType = 'danger';
            $this->message = 'Fitur lanjut Behandle 2 hanya berlaku untuk Behandle 1.';

            return;
        }

        $this->showBehandle2Form = true;
    }

    public function cancelBehandle2Form(): void
    {
        $this->showBehandle2Form = false;

        $this->b2NoDok = '';
        $this->b2JnsDok = '';
        $this->b2TglDok = null;

        $this->resetErrorBag([
            'b2NoDok',
            'b2JnsDok',
            'b2TglDok',
        ]);
    }

    public function createBehandle2(): void
    {
        if (!$this->selectedOperation) {
            $this->messageType = 'danger';
            $this->message = 'Container belum dipilih.';

            return;
        }

        $this->validate(
            [
                'b2NoDok' => ['required', 'string', 'max:100'],
                'b2JnsDok' => ['required', 'string', 'max:50'],
                'b2TglDok' => ['required', 'date'],
            ],
            [
                'b2NoDok.required' => 'No Dokumen wajib diisi.',
                'b2JnsDok.required' => 'Jenis Dokumen wajib diisi.',
                'b2TglDok.required' => 'Tanggal Dokumen wajib diisi.',
            ]
        );

        try {
            DB::transaction(function () {
                $operation = Operation::query()
                    ->with(['spk', 'container'])
                    ->lockForUpdate()
                    ->find($this->selectedOperation->id);

                if (!$operation) {
                    throw ValidationException::withMessages([
                        'b2NoDok' => 'Data operation tidak ditemukan.',
                    ]);
                }

                $container = $operation->container;

                if (!$container) {
                    throw ValidationException::withMessages([
                        'b2NoDok' => 'Container tidak ditemukan.',
                    ]);
                }

                $spkContainer = $operation->spk
                    ->containers()
                    ->where('container_id', $container->id)
                    ->orderByDesc('id')
                    ->lockForUpdate()
                    ->first();

                if (!$spkContainer) {
                    throw ValidationException::withMessages([
                        'b2NoDok' => 'SPK Container tidak ditemukan.',
                    ]);
                }

                // TODO: sesuaikan nama kolom dengan migration tabel `gatepasses`
                $gatepass = Gatepass::create([
                    'no_cont' => $container->no_cont,
                    'no_dok' => strtoupper(trim($this->b2NoDok)),
                    'jns_dok' => strtoupper(trim($this->b2JnsDok)),
                    'tgl_dok' => $this->b2TglDok,
                    'jenis_kegiatan' => '2',
                    'status' => 'WAITING',
                    'fl_active' => 'Y',
                ]);

                // TODO: sesuaikan nama kolom dengan migration tabel `job_slips`
                JobSlip::create([
                    'spk_container_id' => $spkContainer->id,
                    'gatepass_id' => $gatepass->id,
                    'job_type' => 'BEHANDLE 2',
                    'status' => 'WAITING',
                ]);
            });

            $this->cancelBehandle2Form();

            $this->messageType = 'success';
            $this->message = 'Gatepass & Job Slip BEHANDLE 2 berhasil dibuat.';

        } catch (ValidationException $e) {
            $this->messageType = 'danger';

            $this->message = collect($e->errors())->flatten()->first();

        } catch (\Throwable $e) {
            report($e);

            $this->messageType = 'danger';
            $this->message = 'Gagal membuat Gatepass Behandle 2.';
        }
    }

    protected function loadCurrentInspection(): void
    {
        if (!$this->selectedOperation) {
            $this->inspection = null;

            $this->jobSlip = null;

            $this->inspectionStarted = false;

            return;
        }

        $this->inspection =
            OperationInspection::query()
                ->with([
                    'equipment',
                    'operator',
                    'jobSlip.gatepass',
                ])
                ->where(
                    'operation_id',
                    $this->selectedOperation->id
                )
                ->latest('id')
                ->first();

        $this->jobSlip =
            $this->inspection?->jobSlip;

        $this->inspectionStarted =
            $this->inspection?->status === 'WAITING';

        if ($this->inspection) {
            $this->noSeal =
                $this->inspection->no_seal ?? '';

            $this->alat =
                $this->inspection->equipment_id;

            $this->operator =
                $this->inspection->operator_id;
        }
    }

    protected function findReport(
        string $noCont,
        int $spkId
    ): ?BehandleReport {
        $noSpk =
            $this->selectedOperation
                ?->spk
                ?->no_spk;

        if (!$noSpk) {
            return null;
        }

        return BehandleReport::query()
            ->where(
                'no_cont',
                $noCont
            )
            ->where(
                'no_spk',
                $noSpk
            )
            ->latest('id')
            ->first();
    }

    public function resetSearch(): void
    {
        $this->resetState();
    }

    protected function resetSelection(): void
    {
        $this->searchCont = '';

        $this->operations = [];

        $this->selectedOperation = null;

        $this->inspection = null;

        $this->jobSlip = null;

        $this->noSeal = '';

        $this->alat = null;

        $this->operator = null;

        $this->inspectionStarted = false;

        $this->message = null;

        $this->messageType = null;

        $this->showBehandle2Form = false;

        $this->b2NoDok = '';
        $this->b2JnsDok = '';
        $this->b2TglDok = null;
    }

    protected function resetState(): void
    {
        $this->resetSelection();
    }

    public function render()
    {
        return view(
            'livewire.operation.inspection',
            [
                'equipments' =>
                    Equipment::query()
                        ->where(
                            'is_active',
                            true
                        )
                        ->orderBy('name')
                        ->get(),

                'operators' =>
                    SystemUser::query()
                        ->where(
                            'is_active',
                            true
                        )
                        ->orderBy('name')
                        ->get(),
            ]
        );
    }
}