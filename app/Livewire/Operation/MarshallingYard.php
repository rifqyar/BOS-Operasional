<?php

namespace App\Livewire\Operation;

use App\Models\BehandleReport;
use App\Models\JobDetail;
use App\Models\JobSlip;
use App\Models\Operation;
use App\Models\OperationInspection;
use App\Models\OperationMarshalling;
use App\Models\OperationMarshallingLog;
use App\Models\YardLocation;
use App\Models\Equipment;
use App\Models\Truck;
use App\Models\SystemUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;

class MarshallingYard extends Component
{
    use WithPagination;

    public string $searchCont = '';

    public bool $showForm = false;

    public ?JobSlip $jobSlip = null;

    public ?int $locationToId = null;

    public string $note = '';

    public bool $fumigasi = false;

    public string $respon = '';

    public ?int $jenisPekerjaan1 = 7;
    public ?int $alat1 = null;
    public ?int $operator1 = null;

    public ?int $jenisPekerjaan2 = 8;
    public ?int $truck1 = null;
    public ?int $operator2 = null;

    public ?int $jenisPekerjaan3 = 10;
    public ?int $alat3 = null;
    public ?int $operator3 = null;

    public string $message = '';
    public string $messageType = 'success';

    protected $queryString = [
        'searchCont' => [
            'except' => '',
        ],
    ];

    public function updatingSearchCont(): void
    {
        $this->resetPage();
    }

    public function selectJob(int $jobSlipId): void
    {
        $this->resetValidation();
        $this->clearMessage();

        $jobSlip = JobSlip::query()
            ->with([
                'spkContainer.container.type',
                'spkContainer.spk',
                'locationFrom',
                'locationTo',
                'gatepass',
            ])
            ->whereKey($jobSlipId)
            ->where('status', 'WAITING')
            ->whereIn('job_type', [
                'BEHANDLE 1',
                'BEHANDLE 2',
            ])
            ->first();

        if (!$jobSlip) {
            $this->setMessage(
                'Job Slip tidak ditemukan atau sudah tidak dapat diproses.',
                'error'
            );

            return;
        }

        $this->jobSlip = $jobSlip;

        $this->locationToId = $jobSlip->location_to_id;

        $this->note = (string) ($jobSlip->note ?? '');

        $this->respon = '';

        $this->fumigasi = strtoupper(
            (string) ($jobSlip->status_fumigasi ?? '')
        ) === 'Y';

        $this->showForm = true;
    }

    public function cancelProcess(): void
    {
        $this->resetForm();

        $this->clearMessage();
    }

    public function save(): void
    {
        $this->resetValidation();
        $this->clearMessage();

        if (!$this->jobSlip) {
            $this->addError(
                'save',
                'Job Slip belum dipilih.'
            );

            return;
        }

        $this->validate([
            'locationToId' => [
                'required',
                'integer',
                'exists:yard_locations,id',
            ],

            'jenisPekerjaan1' => [
                'required',
                'integer',
                'in:0,7,9',
            ],

            'jenisPekerjaan2' => [
                'required',
                'integer',
                'in:0,8',
            ],

            'jenisPekerjaan3' => [
                'required',
                'integer',
                'in:0,10',
            ],

            'alat1' => [
                'nullable',
                'integer',
                'exists:equipments,id',
            ],

            'operator1' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],

            'truck1' => [
                'nullable',
                'integer',
                'exists:trucks,id',
            ],

            'operator2' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],

            'alat3' => [
                'nullable',
                'integer',
                'exists:equipments,id',
            ],

            'operator3' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],

            'note' => [
                'nullable',
                'string',
                'max:500',
            ],
        ]);

        if (
            $this->jenisPekerjaan1 === 0
            && $this->jenisPekerjaan2 === 0
            && $this->jenisPekerjaan3 === 0
        ) {
            $this->addError(
                'save',
                'Minimal satu aktivitas harus dipilih.'
            );

            return;
        }

        if ($this->jenisPekerjaan1 !== 0) {
            $this->validate([
                'alat1' => [
                    'required',
                    'integer',
                    'exists:equipments,id',
                ],

                'operator1' => [
                    'required',
                    'integer',
                    'exists:users,id',
                ],
            ]);
        }

        if ($this->jenisPekerjaan2 !== 0) {
            $this->validate([
                'truck1' => [
                    'required',
                    'integer',
                    'exists:trucks,id',
                ],

                'operator2' => [
                    'required',
                    'integer',
                    'exists:users,id',
                ],
            ]);
        }

        if ($this->jenisPekerjaan3 !== 0) {
            $this->validate([
                'alat3' => [
                    'required',
                    'integer',
                    'exists:equipments,id',
                ],

                'operator3' => [
                    'required',
                    'integer',
                    'exists:users,id',
                ],
            ]);
        }

        try {
            DB::transaction(function (): void {

                $jobSlip = JobSlip::query()
                    ->with([
                        'spkContainer.container.type',
                        'spkContainer.spk',
                        'locationFrom',
                        'locationTo',
                        'gatepass',
                    ])
                    ->lockForUpdate()
                    ->find($this->jobSlip->id);

                if (!$jobSlip) {
                    throw ValidationException::withMessages([
                        'save' => 'Job Slip tidak ditemukan.',
                    ]);
                }

                if ($jobSlip->status !== 'WAITING') {
                    throw ValidationException::withMessages([
                        'save' => 'Job Slip sudah tidak berstatus WAITING.',
                    ]);
                }

                if (!in_array(
                    $jobSlip->job_type,
                    [
                        'BEHANDLE 1',
                        'BEHANDLE 2',
                    ],
                    true
                )) {
                    throw ValidationException::withMessages([
                        'save' => 'Job Slip bukan pekerjaan BEHANDLE.',
                    ]);
                }

                $spkContainer = $jobSlip->spkContainer;

                if (!$spkContainer) {
                    throw ValidationException::withMessages([
                        'save' => 'SPK Container tidak ditemukan.',
                    ]);
                }

                $container = $spkContainer->container;

                if (!$container) {
                    throw ValidationException::withMessages([
                        'save' => 'Container tidak ditemukan.',
                    ]);
                }

                $operation = Operation::query()
                    ->where(
                        'spk_id',
                        $spkContainer->spk_id
                    )
                    ->where(
                        'container_id',
                        $container->id
                    )
                    ->latest('id')
                    ->lockForUpdate()
                    ->first();

                if (!$operation) {
                    throw ValidationException::withMessages([
                        'save' => 'Operation tidak ditemukan.',
                    ]);
                }

                $inspection = OperationInspection::query()
                    ->where(
                        'operation_id',
                        $operation->id
                    )
                    ->where(function ($query) use ($jobSlip) {
                        $query
                            ->where(
                                'job_slip_id',
                                $jobSlip->id
                            )
                            ->orWhereNull('job_slip_id');
                    })
                    ->latest('id')
                    ->first();

                if (
                    !$inspection
                    || strtoupper((string) $inspection->status) !== 'DONE'
                ) {
                    throw ValidationException::withMessages([
                        'save' => 'Inspection terakhir belum berstatus DONE.',
                    ]);
                }

                $locationTo = YardLocation::query()
                    ->lockForUpdate()
                    ->find($this->locationToId);

                if (!$locationTo) {
                    throw ValidationException::withMessages([
                        'locationToId' => 'Lokasi Yard tidak ditemukan.',
                    ]);
                }

                if (!(bool) $locationTo->is_active) {
                    throw ValidationException::withMessages([
                        'locationToId' => 'Lokasi Yard tidak aktif.',
                    ]);
                }

                if ((bool) $locationTo->is_occupied) {
                    throw ValidationException::withMessages([
                        'locationToId' => 'Lokasi Yard sudah terisi.',
                    ]);
                }

                $duplicate = OperationMarshalling::query()
                    ->where(
                        'job_slip_id',
                        $jobSlip->id
                    )
                    ->where(
                        'marshalling_type',
                        'YARD'
                    )
                    ->whereIn('status', [
                        'PROCESS',
                        'DONE',
                    ])
                    ->exists();

                if ($duplicate) {
                    throw ValidationException::withMessages([
                        'save' => 'Job Slip ini sudah pernah diproses Marshalling Yard.',
                    ]);
                }

                $before = [
                    'job_slip' => $jobSlip->toArray(),
                    'container' => $container->toArray(),
                    'location' => $locationTo->toArray(),
                ];

                $jobDetail = JobDetail::create([
                    'job_slip_id' => $jobSlip->id,

                    'job_activity_code' =>
                        $this->jenisPekerjaan1,

                    'equipment_id' =>
                        $this->jenisPekerjaan1 !== 0
                            ? $this->alat1
                            : null,

                    'operator_id' =>
                        $this->jenisPekerjaan1 !== 0
                            ? $this->operator1
                            : null,

                    'job_activity_code_2' =>
                        $this->jenisPekerjaan2,

                    'truck_id' =>
                        $this->jenisPekerjaan2 !== 0
                            ? $this->truck1
                            : null,

                    'operator_id_2' =>
                        $this->jenisPekerjaan2 !== 0
                            ? $this->operator2
                            : null,

                    'job_activity_code_3' =>
                        $this->jenisPekerjaan3,

                    'equipment_id_3' =>
                        $this->jenisPekerjaan3 !== 0
                            ? $this->alat3
                            : null,

                    'operator_id_3' =>
                        $this->jenisPekerjaan3 !== 0
                            ? $this->operator3
                            : null,

                    'status' => 'DONE',
                ]);

                $marshalling = OperationMarshalling::create([
                    'operation_id' => $operation->id,
                    'job_slip_id' => $jobSlip->id,
                    'marshalling_type' => 'YARD',
                    'location_from_id' => $jobSlip->location_from_id,
                    'location_to_id' => $locationTo->id,
                    'status' => 'DONE',
                    'started_at' => now(),
                    'finished_at' => now(),
                ]);

                $jobSlip->update([
                    'status' => 'DONE',
                    'note' => $this->note ?: $jobSlip->note,
                    'status_fumigasi' => $this->fumigasi ? 'Y' : 'N',
                ]);

                $container->update([
                    'current_location_id' => $locationTo->id,
                ]);

                $locationTo->update([
                    'is_occupied' => 1,
                ]);

                $this->updateBehandleReport(
                    $jobSlip,
                    $container,
                    $locationTo
                );

                $nextJobSlip = $this->createNextJobSlip(
                    $jobSlip,
                    $locationTo
                );

                $after = [
                    'job_slip' => $jobSlip->fresh()->toArray(),
                    'container' => $container->fresh()->toArray(),
                    'location' => $locationTo->fresh()->toArray(),
                    'job_detail_id' => $jobDetail->id,
                    'next_job_slip_id' => $nextJobSlip->id,
                ];

                OperationMarshallingLog::create([
                    'operation_id' => $operation->id,
                    'marshalling_id' => $marshalling->id,
                    'action' => 'MARSHALLING_YARD',
                    'status' => 'DONE',
                    'user_id' => auth()->id(),
                    'note' => $this->note ?: null,
                    'data_before' => $before,
                    'data_after' => $after,
                    'ip_address' => request()->ip(),
                ]);
            });
        } catch (ValidationException $e) {

            foreach ($e->errors() as $field => $messages) {

                foreach ($messages as $message) {
                    $this->addError($field, $message);
                }
            }

            return;

        } catch (\Throwable $e) {

            report($e);

            $this->setMessage(
                'Marshalling Yard gagal disimpan.',
                'error'
            );

            return;
        }

        $this->setMessage(
            'Marshalling Yard berhasil disimpan.',
            'success'
        );

        $this->resetForm();
    }

    protected function createNextJobSlip(
        JobSlip $jobSlip,
        YardLocation $locationTo
    ): JobSlip {
        return JobSlip::create([
            'spk_container_id' => $jobSlip->spk_container_id,
            'no_job' => 'MAR-YARD-' . $jobSlip->id . '-' . now()->format('YmdHisv'),
            'job_type' => 'STACKING YARD',
            'status' => 'WAITING',
            'location_from_id' => $locationTo->id,
            'location_to_id' => null,
            'gatepass_id' => $jobSlip->gatepass_id,
            'note' => $jobSlip->note,
            'status_fumigasi' => $jobSlip->status_fumigasi,
        ]);
    }

    protected function updateBehandleReport(
        JobSlip $jobSlip,
        $container,
        YardLocation $locationTo
    ): void {

        $report = BehandleReport::query()
            ->where(
                'no_cont',
                $container->no_cont
            )
            ->latest('id')
            ->first();

        if (!$report) {
            return;
        }

        if ($jobSlip->job_type === 'BEHANDLE 1') {

            $report->update([
                'pb1_marshalling_b1' => now(),
                'pb1_lokasi_cic' => $locationTo->location_code,
            ]);

            return;
        }

        if ($jobSlip->job_type === 'BEHANDLE 2') {

            $report->update([
                'pb2_marshalling_b2' => now(),
                'pb2_lokasi_cic' => $locationTo->location_code,
            ]);
        }
    }

    protected function resetForm(): void
    {
        $this->showForm = false;

        $this->jobSlip = null;

        $this->locationToId = null;

        $this->note = '';

        $this->fumigasi = false;

        $this->respon = '';

        $this->jenisPekerjaan1 = 7;
        $this->alat1 = null;
        $this->operator1 = null;

        $this->jenisPekerjaan2 = 8;
        $this->truck1 = null;
        $this->operator2 = null;

        $this->jenisPekerjaan3 = 10;
        $this->alat3 = null;
        $this->operator3 = null;

        $this->resetValidation();
    }

    protected function setMessage(
        string $message,
        string $type
    ): void {
        $this->message = $message;
        $this->messageType = $type;
    }

    protected function clearMessage(): void
    {
        $this->message = '';
        $this->messageType = 'success';
    }

    public function render()
    {
        $jobs = JobSlip::query()
            ->with([
                'spkContainer.container.type',
                'locationFrom',
                'locationTo',
                'gatepass',
            ])
            ->where('status', 'WAITING')
            ->whereIn('job_type', [
                'BEHANDLE 1',
                'BEHANDLE 2',
            ])
            ->when(
                trim($this->searchCont) !== '',
                function ($query) {
                    $search = trim($this->searchCont);

                    $query->whereHas(
                        'spkContainer.container',
                        function ($containerQuery) use ($search) {
                            $containerQuery->where(
                                'no_cont',
                                'like',
                                '%' . $search . '%'
                            );
                        }
                    );
                }
            )
            ->latest('id')
            ->paginate(10);

        $locations = YardLocation::query()
            ->where('is_active', 1)
            ->where('is_occupied', 0)
            ->orderBy('location_code')
            ->get();

        $equipments = Equipment::query()
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        $trucks = Truck::query()
            ->where('is_active', 1)
            ->orderBy('id')
            ->get();

        $operators = SystemUser::query()
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        $container = $this->jobSlip?->spkContainer?->container;

        return view('livewire.operation.marshalling-yard', [
            'jobs' => $jobs,
            'locations' => $locations,
            'equipments' => $equipments,
            'trucks' => $trucks,
            'operators' => $operators,
            'container' => $container,
        ]);
    }
}