<?php

namespace App\Livewire\Operation;

use App\Models\SpkContainer;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Hold extends Component
{
    use WithPagination;

    public string $searchCont = '';

    public $container = null;

    public bool $showForm = false;

    public string $warnaHold = '';

    public ?string $holdMessage = null;

    public ?string $holdMessageType = null;

    public int $perPage = 10;

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
                'container',
            ])
            ->where('fl_hold', 'N')
            ->where('status', '!=', '900')
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
                'Data kontainer tidak ditemukan.'
            );

            return;
        }

        $this->holdMessage = null;
        $this->holdMessageType = null;
    }

    public function openResult(): void
    {
        if (! $this->container) {
            return;
        }

        $this->warnaHold = '';
        $this->showForm = true;

        $this->holdMessage = null;
        $this->holdMessageType = null;
    }

    public function send(): void
    {
        if (! $this->container) {
            $this->setMessage(
                'danger',
                'Data kontainer belum dipilih.'
            );

            return;
        }

        $this->validate([
            'warnaHold' => [
                'required',
                'in:N,M,T',
            ],
        ]);

        $spkContainerId = $this->container->id;

        try {
            DB::transaction(function () use ($spkContainerId) {
                $spkContainer = SpkContainer::query()
                    ->whereKey($spkContainerId)
                    ->lockForUpdate()
                    ->first();

                if (! $spkContainer) {
                    throw new \RuntimeException(
                        'Data SPK Container tidak ditemukan.'
                    );
                }

                if ($spkContainer->fl_hold !== 'N') {
                    throw new \RuntimeException(
                        'Container sudah dalam status HOLD.'
                    );
                }

                if ($spkContainer->status === '900') {
                    throw new \RuntimeException(
                        'Container tidak dapat di-HOLD.'
                    );
                }

                $spkContainer->update([
                    'fl_hold' => 'Y',
                    'fl_warna_hold' => $this->warnaHold,
                ]);
            });

            $this->showForm = false;

            $this->resetPage();

            $this->setMessage(
                'success',
                'Container berhasil di-HOLD.'
            );
        } catch (\Throwable $e) {
            report($e);

            $this->setMessage(
                'danger',
                $e->getMessage()
            );
        }
    }

    public function release(int $id): void
    {
        try {
            DB::transaction(function () use ($id) {
                $spkContainer = SpkContainer::query()
                    ->whereKey($id)
                    ->lockForUpdate()
                    ->first();

                if (! $spkContainer) {
                    throw new \RuntimeException(
                        'Data SPK Container tidak ditemukan.'
                    );
                }

                if ($spkContainer->fl_hold !== 'Y') {
                    throw new \RuntimeException(
                        'Container tidak dalam status HOLD.'
                    );
                }

                if ($spkContainer->status === '900') {
                    throw new \RuntimeException(
                        'Container tidak dapat di-RELEASE.'
                    );
                }

                $spkContainer->update([
                    'fl_hold' => 'N',
                    'fl_warna_hold' => 'N',
                ]);
            });

            $this->resetPage();

            $this->setMessage(
                'success',
                'Container berhasil di-RELEASE.'
            );
        } catch (\Throwable $e) {
            report($e);

            $this->setMessage(
                'danger',
                $e->getMessage()
            );
        }
    }

    private function resetResult(): void
    {
        $this->container = null;
        $this->warnaHold = '';
        $this->showForm = false;
        $this->holdMessage = null;
        $this->holdMessageType = null;
    }

    public function resetSearch(): void
    {
        $this->searchCont = '';

        $this->resetResult();
    }

    private function setMessage(string $type, string $message): void
    {
        $this->holdMessageType = $type;
        $this->holdMessage = $message;
    }

    public function render()
    {
        $heldContainers = SpkContainer::query()
            ->with([
                'spk',
                'container',
            ])
            ->where('fl_hold', 'Y')
            ->where('status', '!=', '900')
            ->latest('id')
            ->paginate($this->perPage);

        return view(
            'livewire.operation.hold',
            [
                'heldContainers' => $heldContainers,
            ]
        );
    }
}