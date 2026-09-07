<?php

namespace App\Livewire\Operation;

use App\Models\BehandleReport;
use App\Models\ContainerCondition;
use App\Models\ContainerRequest;
use App\Models\Equipment;
use App\Models\Gatepass;
use App\Models\JobDetail;
use App\Models\JobSlip;
use App\Models\Operation;
use App\Models\OperationBehandlein;
use App\Models\OperationBehandleinLog;
use App\Models\SpkContainer;
use App\Models\SystemUser;
use App\Models\Truck;
use App\Models\YardLocation;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class BehandleIn extends Component
{
    public string $searchCont = '';

    public $container = null;
    public $operation = null;
    public $behandlein = null;
    public $jobSlip = null;
    public $gatepass = null;
    public $containerRequest = null;

    public $trucks = [];
    public $equipments = [];
    public $operators = [];
    public $containerConditions = [];
    public $locations = [];

    public ?int $truckId = null;
    public ?int $equipmentId = null;
    public ?int $operatorId = null;
    public ?int $containerTypeId = null;
    public ?int $containerConditionId = null;
    public ?int $locationId = null;

    public ?string $isoCode = null;
    public ?string $noSeal = null;

    public ?string $containerSize = null;
    public ?string $containerTypeCode = null;

    public string $sealCondition = 'ADA';

    public string $loadStatus = 'F';

    public string $label = 'NON DG';

    public string $jobActivityCode = 'BEHANDLE 1';

    public bool $joinInspection = false;

    public ?string $note = null;

    public bool $showForm = false;

    public ?string $behandleMessage = null;
    public ?string $behandleMessageType = null;

    private const REQUIRED_PROCESS = 'PICKUP';

    private const PROCESS = 'BEHANDLE_IN';

    private const STATUS = 'PROCESS';

    private const JOB_TYPE = 'BEHANDLE_IN';

    private const JOB_ACTIVITY_CODE = 'BEHANDLE 1';

    public function search(): void
    {
        $this->resetResult();

        $keyword = trim($this->searchCont);

        if ($keyword === '') {
            $this->setMessage(
                'danger',
                'Nomor kontainer wajib diisi.'
            );

            return;
        }

        $this->container = SpkContainer::query()
            ->with([
                'spk',
                'container.type',
                'container.currentLocation',
            ])
            ->whereHas('container', function ($query) use ($keyword) {
                $query->where(
                    'no_cont',
                    'like',
                    '%' . strtoupper($keyword) . '%'
                );
            })
            ->latest('id')
            ->first();

        if (! $this->container) {
            $this->setMessage(
                'danger',
                'Nomor kontainer tidak ditemukan.'
            );

            return;
        }

        $this->operation = Operation::query()
            ->where(
                'container_id',
                $this->container->container_id
            )
            ->latest('id')
            ->first();

        if (! $this->operation) {
            $this->setMessage(
                'danger',
                'Data operation tidak ditemukan.'
            );

            return;
        }

        $this->showForm = false;

        $this->behandleMessage = null;
        $this->behandleMessageType = null;
    }

    public function openResult(): void
    {
        if (
            ! $this->container ||
            ! $this->operation
        ) {
            return;
        }

        $this->containerRequest = ContainerRequest::query()
            ->with('containerType')
            ->where(
                'spk_container_id',
                $this->container->id
            )
            ->latest('id')
            ->first();

        $this->jobSlip = JobSlip::query()
            ->with([
                'spkContainer',
                'locationFrom',
                'locationTo',
                'gatepass',
            ])
            ->where(
                'spk_container_id',
                $this->container->id
            )
            ->where(
                'job_type',
                self::JOB_TYPE
            )
            ->latest('id')
            ->first();

        $this->gatepass = Gatepass::query()
            ->with([
                'gate',
                'spk',
                'spkContainer',
            ])
            ->where(
                'spk_id',
                $this->container->spk_id
            )
            ->where(
                'spk_container_id',
                $this->container->id
            )
            ->latest('id')
            ->first();

        $this->behandlein =
            $this->operation->behandlein;

        $this->loadMasterData();

        $this->loadExistingData();

        $this->showForm = true;

        $this->behandleMessage = null;
        $this->behandleMessageType = null;
    }

    public function send(): void
    {
        if (
            ! $this->container ||
            ! $this->operation
        ) {
            $this->setMessage(
                'danger',
                'Data container belum dipilih.'
            );

            return;
        }

        $this->validate([
            'truckId' => [
                'required',
                'integer',
                'exists:trucks,id',
            ],

            'equipmentId' => [
                'required',
                'integer',
                'exists:equipments,id',
            ],

            'operatorId' => [
                'required',
                'integer',
            ],

            'containerTypeId' => [
                'required',
                'integer',
                'exists:container_types,id',
            ],

            'containerConditionId' => [
                'required',
                'integer',
                'exists:container_conditions,id',
            ],

            'isoCode' => [
                'required',
                'string',
                'max:10',
            ],

            'sealCondition' => [
                'required',
                'in:ADA,TIDAK ADA',
            ],

            'noSeal' => [
                'nullable',
                'string',
                'max:100',
            ],

            'loadStatus' => [
                'required',
                'in:F,E',
            ],

            'locationId' => [
                'required',
                'integer',
                'exists:yard_locations,id',
            ],

            'label' => [
                'required',
                'in:DG,NON DG,DG NON LABEL',
            ],

            'joinInspection' => [
                'boolean',
            ],

            'note' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        if (
            $this->sealCondition === 'ADA' &&
            blank($this->noSeal)
        ) {
            $this->setMessage(
                'danger',
                'No Seal wajib diisi jika kondisi seal ADA.'
            );

            return;
        }

        if (
            $this->sealCondition === 'TIDAK ADA'
        ) {
            $this->noSeal = null;
        }

        try {
            DB::transaction(function () {
                $now = now();

                $spkContainer = SpkContainer::query()
                    ->with([
                        'spk',
                        'container.type',
                        'container.currentLocation',
                    ])
                    ->whereKey($this->container->id)
                    ->lockForUpdate()
                    ->first();

                if (! $spkContainer) {
                    throw new \RuntimeException(
                        'SPK Container tidak ditemukan.'
                    );
                }

                $operation = Operation::query()
                    ->whereKey($this->operation->id)
                    ->lockForUpdate()
                    ->first();

                if (! $operation) {
                    throw new \RuntimeException(
                        'Operation tidak ditemukan.'
                    );
                }

                if (
                    $operation->current_process !==
                    self::REQUIRED_PROCESS
                ) {
                    throw new \RuntimeException(
                        'Container sudah tidak berada di proses PICKUP.'
                    );
                }

                $container = $spkContainer->container;

                if (! $container) {
                    throw new \RuntimeException(
                        'Container tidak ditemukan.'
                    );
                }

                $location = YardLocation::query()
                    ->whereKey($this->locationId)
                    ->lockForUpdate()
                    ->first();

                if (! $location) {
                    throw new \RuntimeException(
                        'Lokasi yard tidak ditemukan.'
                    );
                }

                if (! $location->is_active) {
                    throw new \RuntimeException(
                        'Lokasi yard tidak aktif.'
                    );
                }

                if ($location->is_occupied) {
                    throw new \RuntimeException(
                        'Lokasi yard sudah terisi.'
                    );
                }

                $existingBehandle = OperationBehandlein::query()
                    ->where(
                        'operation_id',
                        $operation->id
                    )
                    ->lockForUpdate()
                    ->first();

                if ($existingBehandle) {
                    throw new \RuntimeException(
                        'Container sudah memiliki data Behandle In.'
                    );
                }

                $jobSlip = JobSlip::query()
                    ->where(
                        'spk_container_id',
                        $spkContainer->id
                    )
                    ->where(
                        'job_type',
                        self::JOB_TYPE
                    )
                    ->lockForUpdate()
                    ->latest('id')
                    ->first();

                if (! $jobSlip) {
                    throw new \RuntimeException(
                        'Job Slip Behandle In tidak ditemukan.'
                    );
                }

                $gatepass = Gatepass::query()
                    ->where(
                        'spk_id',
                        $spkContainer->spk_id
                    )
                    ->where(
                        'spk_container_id',
                        $spkContainer->id
                    )
                    ->latest('id')
                    ->lockForUpdate()
                    ->first();

                $request = ContainerRequest::query()
                    ->where(
                        'spk_container_id',
                        $spkContainer->id
                    )
                    ->latest('id')
                    ->lockForUpdate()
                    ->first();

                $operationBefore =
                    $operation->toArray();

                $containerBefore =
                    $container->toArray();

                $jobSlipBefore =
                    $jobSlip->toArray();

                $behandlein = OperationBehandlein::create([
                    'operation_id' =>
                        $operation->id,

                    'job_slip_id' =>
                        $jobSlip->id,

                    'equipment_id' =>
                        $this->equipmentId,

                    'operator_id' =>
                        $this->operatorId,

                    'truck_id' =>
                        $this->truckId,

                    'no_seal' =>
                        $this->noSeal,

                    'container_type_id' =>
                        $this->containerTypeId,

                    'container_condition_id' =>
                        $this->containerConditionId,

                    'iso_code' =>
                        strtoupper(
                            trim($this->isoCode)
                        ),

                    'load_status' =>
                        $this->loadStatus,

                    'location_id' =>
                        $this->locationId,

                    'join_inspection' =>
                        $this->joinInspection
                            ? 1
                            : 0,

                    'label' =>
                        $this->label,

                    'started_at' =>
                        $now,

                    'status' =>
                        self::STATUS,

                    'note' =>
                        $this->note,
                ]);

                $operation->update([
                    'current_process' =>
                        self::PROCESS,

                    'status' =>
                        self::STATUS,

                    'started_at' =>
                        $operation->started_at
                            ?? $now,

                    'updated_at' =>
                        $now,
                ]);

                $container->update([
                    'container_type_id' =>
                        $this->containerTypeId,

                    'current_location_id' =>
                        $this->locationId,

                    'no_seal' =>
                        $this->noSeal,

                    'updated_at' =>
                        $now,
                ]);

                $location->update([
                    'is_occupied' =>
                        1,

                    'updated_at' =>
                        $now,
                ]);

                if ($request) {
                    $request->update([
                        'container_type_id' =>
                            $this->containerTypeId,

                        'container_size' =>
                            $this->containerSize,

                        'container_type_code' =>
                            $this->containerTypeCode,

                        'fl_perbaiki' =>
                            'Y',

                        'status_dg' =>
                            null,

                        'updated_at' =>
                            $now,
                    ]);
                }

                $jobSlip->update([
                    'gatepass_id' =>
                        $gatepass?->id,

                    'location_to_id' =>
                        $this->locationId,

                    'updated_at' =>
                        $now,
                ]);

                JobDetail::create([
                    'job_slip_id' =>
                        $jobSlip->id,

                    'equipment_id' =>
                        $this->equipmentId,

                    'operator_id' =>
                        $this->operatorId,

                    'job_activity_code' =>
                        $this->jobActivityCode
                        ?: self::JOB_ACTIVITY_CODE,

                    'status' =>
                        self::STATUS,
                ]);

                if ($gatepass) {
                    $gatepass->update([
                        'received_at' =>
                            $gatepass->received_at
                                ?? $now,

                        'updated_at' =>
                            $now,
                    ]);
                }

                BehandleReport::create([
                    'operation_behandlein_id' =>
                        $behandlein->id,

                    'gatepass_id' =>
                        $gatepass?->id,

                    'no_spk' =>
                        $spkContainer
                            ->spk
                            ?->no_spk,

                    'no_cont' =>
                        $container->no_cont,

                    'no_dok' =>
                        $spkContainer
                            ->spk
                            ?->no_dok,

                    'behandle_in' =>
                        $now,

                    'kondisi_cont' =>
                        $this->containerConditionId,

                    'no_seal' =>
                        $this->noSeal,

                    'iso_code' =>
                        strtoupper(
                            trim($this->isoCode)
                        ),

                    'lokasi' =>
                        $location->location_code,
                ]);

                OperationBehandleinLog::create([
                    'operation_id' =>
                        $operation->id,

                    'behandlein_id' =>
                        $behandlein->id,

                    'action' =>
                        'CREATE',

                    'status' =>
                        self::STATUS,

                    'user_id' =>
                        auth()->id(),

                    'note' =>
                        $this->note,

                    'data_before' => [
                        'operation' =>
                            $operationBefore,

                        'container' =>
                            $containerBefore,

                        'job_slip' =>
                            $jobSlipBefore,
                    ],

                    'data_after' => [
                        'behandlein' =>
                            $behandlein
                                ->fresh()
                                ->toArray(),

                        'operation' =>
                            $operation
                                ->fresh()
                                ->toArray(),

                        'container' =>
                            $container
                                ->fresh()
                                ->toArray(),

                        'job_slip' =>
                            $jobSlip
                                ->fresh()
                                ->toArray(),
                    ],

                    'ip_address' =>
                        request()->ip(),

                    'created_at' =>
                        $now,
                ]);
            });

            $this->reloadData();

            $this->setMessage(
                'success',
                'Data Behandle In berhasil dikirim.'
            );

        } catch (\Throwable $e) {
            report($e);

            $this->setMessage(
                'danger',
                $e->getMessage()
            );
        }
    }

    private function loadMasterData(): void
    {
        $this->trucks = Truck::query()
            ->orderBy('id')
            ->get();

        $this->equipments = Equipment::query()
            ->where('is_active', '1')
            ->orderBy('name')
            ->get();

        $this->operators = SystemUser::query()
            ->where('is_active', '1')
            ->orderBy('name')
            ->get();

        $this->containerConditions =
            ContainerCondition::query()
                ->where('is_active', '1')
                ->orderBy('name')
                ->get();

        $this->locations = YardLocation::query()
            ->where('is_active', '1')
            ->where('is_occupied', '0')
            ->orderBy('block')
            ->orderBy('slot')
            ->orderBy('tier')
            ->get();
    }

    private function loadExistingData(): void
    {
        $behandlein =
            $this->operation?->behandlein;

        if (! $behandlein) {
            $this->truckId =
                $this->operation
                    ?->pickup
                    ?->truck_id;

            $this->equipmentId = null;

            $this->operatorId =
                auth()->id();

            $this->containerTypeId =
                $this->container
                    ?->container
                    ?->container_type_id;

            $this->containerSize =
                $this->container
                    ?->container
                    ?->type
                    ?->size;

            $this->containerTypeCode =
                $this->container
                    ?->container
                    ?->type
                    ?->code;

            $this->containerConditionId =
                null;

            $this->isoCode =
                $this->container
                    ?->container
                    ?->type
                    ?->iso_code;

            $this->noSeal =
                $this->container
                    ?->container
                    ?->no_seal;

            $this->sealCondition =
                blank($this->noSeal)
                    ? 'TIDAK ADA'
                    : 'ADA';

            $this->loadStatus =
                'F';

            $this->locationId =
                $this->jobSlip
                    ?->location_to_id;

            $this->label =
                $this->getDefaultLabel();

            $this->jobActivityCode =
                self::JOB_ACTIVITY_CODE;

            $this->joinInspection =
                false;

            $this->note =
                null;

            return;
        }

        $this->behandlein =
            $behandlein;

        $this->truckId =
            $behandlein->truck_id;

        $this->equipmentId =
            $behandlein->equipment_id;

        $this->operatorId =
            $behandlein->operator_id;

        $this->containerTypeId =
            $behandlein->container_type_id
            ?? $this->container
                ?->container
                ?->container_type_id;

        $this->containerSize =
            $this->container
                ?->container
                ?->type
                ?->size;

        $this->containerTypeCode =
            $this->container
                ?->container
                ?->type
                ?->code;

        $this->containerConditionId =
            $behandlein
                ->container_condition_id;

        $this->isoCode =
            $behandlein->iso_code;

        $this->noSeal =
            $behandlein->no_seal;

        $this->sealCondition =
            blank($this->noSeal)
                ? 'TIDAK ADA'
                : 'ADA';

        $this->loadStatus =
            $behandlein->load_status
            ?? 'F';

        $this->locationId =
            $behandlein->location_id;

        $this->label =
            $behandlein->label
            ?? 'NON DG';

        $this->jobActivityCode =
            self::JOB_ACTIVITY_CODE;

        $this->joinInspection =
            (bool) $behandlein
                ->join_inspection;

        $this->note =
            $behandlein->note;
    }

    private function getDefaultLabel(): string
    {
        $container =
            $this->container?->container;

        if (! $container) {
            return 'NON DG';
        }

        if (
            $container->fl_dg === 'Y' ||
            ! blank($container->imo)
        ) {
            return 'DG';
        }

        return 'NON DG';
    }

    private function reloadData(): void
    {
        if (! $this->operation) {
            return;
        }

        $operationId =
            $this->operation->id;

        $this->operation = Operation::query()
            ->with([
                'spk',
                'container',
                'pickup.truck',
                'behandlein',
                'behandlein.truck',
                'behandlein.equipment',
                'behandlein.operator',
                'behandlein.containerType',
                'behandlein.containerCondition',
                'behandlein.location',
            ])
            ->find($operationId);

        $this->behandlein =
            $this->operation
                ?->behandlein;

        if ($this->container) {
            $this->jobSlip =
                JobSlip::query()
                    ->with([
                        'spkContainer',
                        'locationFrom',
                        'locationTo',
                        'gatepass',
                    ])
                    ->where(
                        'spk_container_id',
                        $this->container->id
                    )
                    ->where(
                        'job_type',
                        self::JOB_TYPE
                    )
                    ->latest('id')
                    ->first();

            $this->gatepass =
                Gatepass::query()
                    ->with([
                        'gate',
                        'spk',
                        'spkContainer',
                    ])
                    ->where(
                        'spk_container_id',
                        $this->container->id
                    )
                    ->latest('id')
                    ->first();

            $this->containerRequest =
                ContainerRequest::query()
                    ->with('containerType')
                    ->where(
                        'spk_container_id',
                        $this->container->id
                    )
                    ->latest('id')
                    ->first();
        }
    }

    private function resetResult(): void
    {
        $this->container = null;

        $this->operation = null;

        $this->behandlein = null;

        $this->jobSlip = null;

        $this->gatepass = null;

        $this->containerRequest = null;

        $this->trucks = [];

        $this->equipments = [];

        $this->operators = [];

        $this->containerConditions = [];

        $this->locations = [];

        $this->truckId = null;

        $this->equipmentId = null;

        $this->operatorId = null;

        $this->containerTypeId = null;

        $this->containerConditionId = null;

        $this->locationId = null;

        $this->isoCode = null;

        $this->noSeal = null;

        $this->containerSize = null;

        $this->containerTypeCode = null;

        $this->sealCondition = 'ADA';

        $this->loadStatus = 'F';

        $this->label = 'NON DG';

        $this->jobActivityCode =
            self::JOB_ACTIVITY_CODE;

        $this->joinInspection = false;

        $this->note = null;

        $this->showForm = false;

        $this->behandleMessage = null;

        $this->behandleMessageType = null;
    }

    public function resetSearch(): void
    {
        $this->searchCont = '';

        $this->resetResult();
    }

    private function setMessage(
        string $type,
        string $message
    ): void {
        $this->behandleMessageType =
            $type;

        $this->behandleMessage =
            $message;
    }

    public function render()
    {
        return view(
            'livewire.operation.behandle-in'
        );
    }
}