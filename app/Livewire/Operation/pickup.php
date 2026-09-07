<?php

namespace App\Livewire\Operation;

use App\Models\Operation;
use App\Models\OperationPickup;
use App\Models\Spk;
use App\Models\Truck;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Pickup extends Component
{
    private const PROCESS = 'PICKUP';

    private const STATUS = 'SENT';

    public string $searchSpk = '';

    public $spk = null;

    public $containers = [];

    public $operations = [];

    public $trucks = [];

    public array $selectedTrucks = [];

    public ?string $pickupMessage = null;

    public ?string $pickupMessageType = null;

    public function search(): void
    {
        $this->validate([
            'searchSpk' => [
                'required',
                'string',
                'max: 100',
            ],
        ]);

        $keyword = strtoupper(trim($this->searchSpk));

        $this->resetResult();

        $this->spk = Spk::query()
            ->where('no_spk', 'like', "%{$keyword}%")
            ->first();

        if (! $this->spk) {
            $this->setMessage(
                'danger',
                "NO SPK : {$keyword} NOT FOUND"
            );

            return;
        }

        $containers = $this->spk
            ->containers()
            ->with('container.type')
            ->get();

        $this->operations = $this->spk
            ->operations()
            ->with([
                'container.type',
                'pickup.truck',
            ])
            ->get()
            ->keyBy('container_id');

        $this->containers = $containers
            ->filter(function ($spkContainer) {
                $operation = $this->operations->get(
                    $spkContainer->container_id
                );

                if (! $operation) {
                    return true;
                }

                return strtoupper(
                    trim((string) $operation->current_process)
                ) === self::PROCESS;
            })
            ->values();

        if ($this->containers->isEmpty()) {
            $this->setMessage(
                'danger',
                "NO SPK : {$this->spk->no_spk} TIDAK MEMILIKI CONTAINER YANG SESUAI PROSES PICKUP"
            );

            return;
        }

        $this->trucks = Truck::query()
            ->orderBy('id')
            ->get();

        foreach ($this->containers as $spkContainer) {
            $operation = $this->operations->get(
                $spkContainer->container_id
            );

            if ($operation?->pickup?->truck_id) {
                $this->selectedTrucks[
                    $spkContainer->container_id
                ] = $operation->pickup->truck_id;
            }
        }

        $this->setMessage(
            'success',
            "NO SPK : {$this->spk->no_spk} FOUND"
        );
    }

    public function send(int $containerId): void
    {
        $this->validate([
            "selectedTrucks.{$containerId}" => [
                'required',
                'integer',
                'exists:trucks,id',
            ],
        ], [
            "selectedTrucks.{$containerId}.required" =>
                'Silakan pilih truck terlebih dahulu.',

            "selectedTrucks.{$containerId}.exists" =>
                'Truck yang dipilih tidak ditemukan.',
        ]);

        if (! $this->spk) {
            $this->setMessage(
                'danger',
                'Silakan search SPK terlebih dahulu.'
            );

            return;
        }

        $truckId = (int) $this->selectedTrucks[$containerId];

        $spkContainer = $this->spk
            ->containers()
            ->where('container_id', $containerId)
            ->first();

        if (! $spkContainer) {
            $this->setMessage(
                'danger',
                'Container tidak terdaftar pada SPK tersebut.'
            );

            return;
        }

        $operation = Operation::query()
            ->where('spk_id', $this->spk->id)
            ->where('container_id', $containerId)
            ->first();

        if (
            $operation &&
            filled($operation->current_process) &&
            strtoupper(
                trim((string) $operation->current_process)
            ) !== self::PROCESS
        ) {
            $this->setMessage(
                'danger',
                'Container sudah berada pada proses ' .
                strtoupper($operation->current_process) .
                '.'
            );

            $this->search();

            return;
        }

        try {
            DB::transaction(function () use (
                $containerId,
                $truckId,
                $operation
            ) {
                if (! $operation) {
                    $operation = Operation::create([
                        'id' => $this->nextOperationId(),
                        'spk_id' => $this->spk->id,
                        'container_id' => $containerId,
                        'current_process' => self::PROCESS,
                        'status' => self::STATUS,
                        'started_at' => now(),
                    ]);
                } else {
                    $operation->update([
                        'current_process' => self::PROCESS,
                        'status' => self::STATUS,
                        'started_at' => $operation->started_at ?? now(),
                    ]);
                }

                OperationPickup::create([
                    'id' => $this->nextPickupId(),
                    'operation_id' => $operation->id,
                    'truck_id' => $truckId,
                    'status' => self::STATUS,
                    'pickup_at' => now(),
                ]);
            });

            $this->setMessage(
                'success',
                'Data Pickup berhasil dikirim.'
            );

            $this->search();

        } catch (\Throwable $e) {
            report($e);

            throw $e;
        }
    }

    private function nextOperationId(): int
    {
        return ((int) Operation::query()->max('id')) + 1;
    }

    private function nextPickupId(): int
    {
        return ((int) OperationPickup::query()->max('id')) + 1;
    }

    public function resetSearch(): void
    {
        $this->searchSpk = '';

        $this->resetResult();
    }

    private function resetResult(): void
    {
        $this->spk = null;
        $this->containers = [];
        $this->operations = [];
        $this->trucks = [];
        $this->selectedTrucks = [];
        $this->pickupMessage = null;
        $this->pickupMessageType = null;
    }

    private function setMessage(
        string $type,
        string $message
    ): void {
        $this->pickupMessageType = $type;
        $this->pickupMessage = $message;
    }

    public function render()
    {
        return view('livewire.operation.pickup');
    }
}