<?php

namespace App\Livewire\Operation;

use App\Models\BehandleReport;
use App\Models\Equipment;
use App\Models\JobDetail;
use App\Models\JobSlip;
use App\Models\Operation;
use App\Models\OperationMarshalling;
use App\Models\OperationMarshallingLog;
use App\Models\SpkContainer;
use App\Models\SystemUser;
use App\Models\Truck;
use App\Models\YardLocation;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;

class MarshallingCic extends Component
{
    use WithPagination;

    public string $searchCont = '';

    public ?JobSlip $jobSlip = null;

    public $spkContainer = null;

    public $container = null;

    public $gatepass = null;

    public ?int $locationToId = null;

    public string $note = '';

    public string $respon = '';

    public bool $fumigasi = false;

    public int $jenisPekerjaan1 = 0;

    public ?int $alat1 = null;

    public ?int $operator1 = null;

    public int $jenisPekerjaan2 = 0;

    public ?int $truck1 = null;

    public ?int $operator2 = null;

    public int $jenisPekerjaan3 = 0;

    public ?int $alat3 = null;

    public ?int $operator3 = null;

    public $locations = [];

    public $trucks = [];

    public $equipments = [];

    public $operators = [];

    public ?string $message = null;

    public ?string $messageType = null;

    public bool $showForm = false;

    public function updatedSearchCont(): void
    {
        $this->resetPage();
    }

    public function selectJob(int $jobSlipId): void
    {
        $this->resetForm();

        $jobSlip = JobSlip::query()
            ->with([
                'spkContainer.spk',
                'spkContainer.container.type',
                'gatepass',
                'locationFrom',
                'locationTo',
            ])
            ->where('id', $jobSlipId)
            ->where('status', 'WAITING')
            ->whereIn('job_type', [
                'BEHANDLE 1',
                'BEHANDLE 2',
            ])
            ->whereHas('spkContainer', function ($query) {
                $query->where('status', '!=', '900');
            })
            ->whereHas('gatepass', function ($query) {
                $query->where('status', 'WAITING');
            })
            ->whereHas('locationTo', function ($query) {
                $query->where('location_code', 'like', 'CIC%');
            })
            ->first();

        if (!$jobSlip) {
            $this->messageType = 'danger';
            $this->message = 'Job Slip sudah tidak tersedia untuk diproses.';

            return;
        }

        $this->jobSlip = $jobSlip;
        $this->spkContainer = $jobSlip->spkContainer;
        $this->container = $this->spkContainer?->container;
        $this->gatepass = $jobSlip->gatepass;

        $this->locationToId = $jobSlip->location_to_id;

        $this->respon = (string) (
            $this->gatepass?->jenis_kegiatan ?? ''
        );

        $this->loadMasterData();

        $this->showForm = true;
        $this->message = null;
        $this->messageType = null;
    }

    public function cancelProcess(): void
    {
        $this->resetForm();
        $this->showForm = false;
        $this->message = null;
        $this->messageType = null;
    }

    public function save(): void
    {
        $this->validate([
            'locationToId' => [
                'required',
                'integer',
            ],

            'note' => [
                'nullable',
                'string',
                'max:500',
            ],

            'fumigasi' => [
                'boolean',
            ],

            'jenisPekerjaan1' => [
                'required',
                'integer',
                'in:0,3',
            ],

            'alat1' => [
                'nullable',
                'integer',
                'exists:equipments,id',
            ],

            'operator1' => [
                'nullable',
                'integer',
            ],

            'jenisPekerjaan2' => [
                'required',
                'integer',
                'in:0,6',
            ],

            'truck1' => [
                'nullable',
                'integer',
                'exists:trucks,id',
            ],

            'operator2' => [
                'nullable',
                'integer',
            ],

            'jenisPekerjaan3' => [
                'required',
                'integer',
                'in:0,5',
            ],

            'alat3' => [
                'nullable',
                'integer',
                'exists:equipments,id',
            ],

            'operator3' => [
                'nullable',
                'integer',
            ],
        ]);

        $this->validateActivity(
            $this->jenisPekerjaan1,
            $this->alat1,
            $this->operator1,
            'alat1',
            'operator1',
            'Alat 1',
            'Operator 1'
        );

        $this->validateActivity(
            $this->jenisPekerjaan2,
            $this->truck1,
            $this->operator2,
            'truck1',
            'operator2',
            'Truck',
            'Operator 2'
        );

        $this->validateActivity(
            $this->jenisPekerjaan3,
            $this->alat3,
            $this->operator3,
            'alat3',
            'operator3',
            'Alat 3',
            'Operator 3'
        );

        if (!$this->jobSlip) {
            throw ValidationException::withMessages([
                'searchCont' => 'Job Slip belum dipilih.',
            ]);
        }

        $containerNo = $this->container?->no_cont ?? '-';

        DB::transaction(function () {
            $jobSlip = JobSlip::query()
                ->with([
                    'spkContainer.spk',
                    'spkContainer.container',
                    'gatepass',
                    'locationFrom',
                    'locationTo',
                ])
                ->lockForUpdate()
                ->find($this->jobSlip->id);

            if (!$jobSlip) {
                throw ValidationException::withMessages([
                    'searchCont' => 'Job Slip tidak ditemukan.',
                ]);
            }

            if ($jobSlip->status !== 'WAITING') {
                throw ValidationException::withMessages([
                    'searchCont' =>
                        'Job Slip sudah diproses sebelumnya.',
                ]);
            }

            if (!in_array(
                strtoupper((string) $jobSlip->job_type),
                ['BEHANDLE 1', 'BEHANDLE 2'],
                true
            )) {
                throw ValidationException::withMessages([
                    'searchCont' =>
                        'Job Slip bukan pekerjaan Marshalling CIC.',
                ]);
            }

            $alreadyProcessed = OperationMarshalling::query()
                ->where('job_slip_id', $jobSlip->id)
                ->where('marshalling_type', 'CIC')
                ->whereIn('status', ['PROCESS', 'DONE'])
                ->lockForUpdate()
                ->exists();

            if ($alreadyProcessed) {
                throw ValidationException::withMessages([
                    'searchCont' =>
                        'Job Slip ini sudah diproses Marshalling CIC.',
                ]);
            }

            $spkContainer = SpkContainer::query()
                ->with([
                    'spk',
                    'container',
                ])
                ->lockForUpdate()
                ->find($jobSlip->spk_container_id);

            if (!$spkContainer) {
                throw ValidationException::withMessages([
                    'searchCont' =>
                        'SPK Container tidak ditemukan.',
                ]);
            }

            if ((string) $spkContainer->status === '900') {
                throw ValidationException::withMessages([
                    'searchCont' =>
                        'Container sudah tidak dapat diproses.',
                ]);
            }

            $container = $spkContainer->container;

            if (!$container) {
                throw ValidationException::withMessages([
                    'searchCont' =>
                        'Container tidak ditemukan.',
                ]);
            }

            $gatepass = $jobSlip->gatepass;

            if (!$gatepass) {
                throw ValidationException::withMessages([
                    'searchCont' =>
                        'Gatepass tidak ditemukan.',
                ]);
            }

            if ($gatepass->status !== 'WAITING') {
                throw ValidationException::withMessages([
                    'searchCont' =>
                        'Gatepass sudah tidak WAITING.',
                ]);
            }

            $locationTo = YardLocation::query()
                ->lockForUpdate()
                ->find($this->locationToId);

            if (!$locationTo) {
                throw ValidationException::withMessages([
                    'locationToId' =>
                        'Lokasi CIC tidak ditemukan.',
                ]);
            }

            if (
                !str_starts_with(
                    strtoupper((string) $locationTo->location_code),
                    'CIC'
                )
            ) {
                throw ValidationException::withMessages([
                    'locationToId' =>
                        'Lokasi akhir harus merupakan lokasi CIC.',
                ]);
            }

            if ((int) $locationTo->is_occupied === 1) {
                throw ValidationException::withMessages([
                    'locationToId' =>
                        'Lokasi CIC sudah terisi.',
                ]);
            }

            $operation = Operation::query()
                ->where('spk_id', $spkContainer->spk_id)
                ->where('container_id', $container->id)
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            if (!$operation) {
                throw ValidationException::withMessages([
                    'searchCont' =>
                        'Operation container tidak ditemukan.',
                ]);
            }

            $now = now();

            $before = [
                'job_slip_id' => $jobSlip->id,
                'job_slip_status' => $jobSlip->status,
                'location_from_id' => $jobSlip->location_from_id,
                'location_to_id' => $jobSlip->location_to_id,
                'container_location_id' =>
                    $container->current_location_id,
            ];

            $operation->update([
                'current_process' => 'MARSHALLING_CIC',
                'status' => 'PROCESS',
                'updated_by' => auth()->id(),
                'started_at' =>
                    $operation->started_at ?: $now,
            ]);

            $marshalling = OperationMarshalling::create([
                'operation_id' => $operation->id,
                'job_slip_id' => $jobSlip->id,
                'marshalling_type' => 'CIC',
                'location_from_id' =>
                    $jobSlip->location_from_id,
                'location_to_id' =>
                    $locationTo->id,
                'status' => 'PROCESS',
                'started_at' => $now,
            ]);

            $jobDetail = JobDetail::create([
                'job_slip_id' => $jobSlip->id,

                'equipment_id' => $this->alat1,
                'operator_id' => $this->operator1,
                'job_activity_code' =>
                    $this->jenisPekerjaan1,

                'job_activity_code_2' =>
                    $this->jenisPekerjaan2,
                'truck_id' => $this->truck1,
                'operator_id_2' => $this->operator2,

                'job_activity_code_3' =>
                    $this->jenisPekerjaan3,
                'equipment_id_3' => $this->alat3,
                'operator_id_3' => $this->operator3,

                'status' => 'PROCESS',
            ]);

            $jobSlip->update([
                'status' => 'DONE',
                'note' => $this->note !== ''
                    ? $this->note
                    : null,
                'status_fumigasi' => $this->fumigasi
                    ? 'Y'
                    : 'N',
            ]);

            $container->update([
                'current_location_id' => $locationTo->id,
            ]);

            $locationTo->update([
                'is_occupied' => 1,
            ]);

            $newJobSlip = JobSlip::create([
                'spk_container_id' =>
                    $spkContainer->id,
                'gatepass_id' =>
                    $gatepass->id,
                'no_job' =>
                    'MAR-' .
                    $spkContainer->id .
                    '-' .
                    $now->format('YmdHis'),
                'job_type' => 'MARSHALLING',
                'status' => 'WAITING',
                'location_from_id' =>
                    $locationTo->id,
                'location_to_id' => null,
                'note' => $this->note !== ''
                    ? $this->note
                    : null,
                'status_fumigasi' => $this->fumigasi
                    ? 'Y'
                    : 'N',
            ]);

            $marshalling->update([
                'status' => 'DONE',
                'finished_at' => $now,
            ]);

            $report = BehandleReport::query()
                ->where('no_cont', $container->no_cont)
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            if ($report) {
                if (
                    strtoupper(
                        trim((string) $jobSlip->job_type)
                    ) === 'BEHANDLE 1'
                ) {
                    $report->update([
                        'pb1_marshalling_b1' => $now,
                        'pb1_lokasi_cic' =>
                            $locationTo->location_code,
                    ]);
                } else {
                    $report->update([
                        'pb2_marshalling_b2' => $now,
                        'pb2_lokasi_cic' =>
                            $locationTo->location_code,
                    ]);
                }
            }

            $dataAfter = [
                'job_slip_id' => $jobSlip->id,
                'new_job_slip_id' => $newJobSlip->id,
                'job_detail_id' => $jobDetail->id,
                'marshalling_id' => $marshalling->id,
                'location_to_id' => $locationTo->id,
                'container_location_id' =>
                    $container->current_location_id,
            ];

            OperationMarshallingLog::create([
                'operation_id' => $operation->id,
                'marshalling_id' => $marshalling->id,
                'action' => 'MARSHALLING_CIC',
                'status' => 'DONE',
                'user_id' => auth()->id(),
                'note' => $this->note !== ''
                    ? $this->note
                    : null,
                'data_before' => $before,
                'data_after' => $dataAfter,
                'ip_address' => request()->ip(),
                'created_at' => $now,
            ]);
        });

        $this->cancelProcess();

        $this->messageType = 'success';
        $this->message =
            "MARSHALLING CIC {$containerNo} BERHASIL DISIMPAN";
    }

    protected function validateActivity(
        int $activity,
        ?int $resource,
        ?int $operator,
        string $resourceField,
        string $operatorField,
        string $resourceLabel,
        string $operatorLabel
    ): void {
        if ($activity === 0) {
            return;
        }

        if (!$resource) {
            throw ValidationException::withMessages([
                $resourceField =>
                    "{$resourceLabel} wajib dipilih.",
            ]);
        }

        if (!$operator) {
            throw ValidationException::withMessages([
                $operatorField =>
                    "{$operatorLabel} wajib dipilih.",
            ]);
        }
    }

    protected function loadMasterData(): void
    {
        $this->locations = YardLocation::query()
            ->where('is_active', 1)
            ->where('location_code', 'like', 'CIC%')
            ->orderBy('location_code')
            ->get();

        $this->trucks = Truck::query()
            ->where('is_active', 1)
            ->orderBy('id')
            ->get();

        $this->equipments = Equipment::query()
            ->where('is_active', 1)
            ->orderBy('code')
            ->get();

        $this->operators = SystemUser::query()
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();
    }

    protected function resetForm(): void
    {
        $this->jobSlip = null;
        $this->spkContainer = null;
        $this->container = null;
        $this->gatepass = null;

        $this->locationToId = null;
        $this->note = '';
        $this->respon = '';
        $this->fumigasi = false;

        $this->jenisPekerjaan1 = 0;
        $this->alat1 = null;
        $this->operator1 = null;

        $this->jenisPekerjaan2 = 0;
        $this->truck1 = null;
        $this->operator2 = null;

        $this->jenisPekerjaan3 = 0;
        $this->alat3 = null;
        $this->operator3 = null;
    }

    public function render()
    {
        $keyword = strtoupper(trim($this->searchCont));

        $jobs = JobSlip::query()
            ->with([
                'spkContainer.container.type',
                'gatepass',
                'locationFrom',
                'locationTo',
            ])
            ->where('status', 'WAITING')
            ->whereIn('job_type', [
                'BEHANDLE 1',
                'BEHANDLE 2',
            ])
            ->whereHas('spkContainer', function ($query) {
                $query->where('status', '!=', '900');
            })
            ->whereHas('spkContainer.container', function ($query) use ($keyword) {
                if ($keyword !== '') {
                    $query->where(
                        'no_cont',
                        'like',
                        '%' . $keyword . '%'
                    );
                }
            })
            ->whereHas('gatepass', function ($query) {
                $query->where('status', 'WAITING');
            })
            ->whereHas('locationTo', function ($query) {
                $query->where(
                    'location_code',
                    'like',
                    'CIC%'
                );
            })
            ->orderByDesc('id')
            ->paginate(10);

        return view(
            'livewire.operation.marshalling-cic',
            compact('jobs')
        );
    }
}