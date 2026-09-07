<div class="min-h-screen bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-white">
    <div class="mx-auto w-full max-w-5xl px-4 py-6 sm:px-6 lg:px-8">

        {{-- HEADER --}}
        <div class="mb-6 flex items-center justify-between gap-4">
            <div>
                <a
                    href="{{ route('dashboard') }}"
                    wire:navigate
                    class="inline-flex items-center gap-2 text-sm font-medium text-slate-600 transition hover:text-sky-600 dark:text-slate-300 dark:hover:text-sky-400"
                >
                    <flux:icon.arrow-left class="size-4" />
                    Menu Handheld
                </a>
            </div>

            <div class="rounded-lg bg-sky-100 px-4 py-2 text-sm font-semibold text-sky-700 dark:bg-sky-500/15 dark:text-sky-400">
                HOLD
            </div>
        </div>


        {{-- SEARCH --}}
        <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-950">
            <div class="flex flex-col gap-3 sm:flex-row">
                <div class="flex-1">
                    <flux:input
                        wire:model="searchCont"
                        wire:keydown.enter="search"
                        placeholder="SEARCH NO CONT"
                        autocomplete="off"
                    />
                </div>

                <div class="flex gap-2">
                    <flux:button
                        wire:click="search"
                        variant="primary"
                        icon="magnifying-glass"
                    >
                        SEARCH
                    </flux:button>

                    @if($searchCont !== '' || $container)
                        <flux:button
                            wire:click="resetSearch"
                            variant="ghost"
                        >
                            RESET
                        </flux:button>
                    @endif
                </div>
            </div>
        </div>


        {{-- MESSAGE --}}
        @if($holdMessage)
            <div class="mt-4 rounded-lg border px-4 py-3 text-sm
                @if($holdMessageType === 'success')
                    border-emerald-200 bg-emerald-50 text-emerald-700
                    dark:border-emerald-900/50 dark:bg-emerald-950/30 dark:text-emerald-400
                @else
                    border-red-200 bg-red-50 text-red-700
                    dark:border-red-900/50 dark:bg-red-950/30 dark:text-red-400
                @endif
            ">
                {{ $holdMessage }}
            </div>
        @endif


        {{-- SEARCH RESULT --}}
        @if($container && !$showForm)
            <div class="mt-4 rounded-lg border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-950">

                <div class="mb-3 text-sm font-semibold text-slate-700 dark:text-slate-200">
                    Hasil Pencarian
                </div>

                <button
                    type="button"
                    wire:click="openResult"
                    class="group w-full rounded-lg border border-slate-200 bg-slate-50 p-4 text-left transition hover:border-sky-400 hover:bg-sky-50 dark:border-slate-800 dark:bg-slate-900 dark:hover:border-sky-500 dark:hover:bg-sky-500/10"
                >
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                        <div>
                            <div class="text-base font-bold text-slate-900 dark:text-white">
                                {{ $container->container->no_cont ?? '-' }}
                            </div>

                            <div class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                                NO SPK:
                                <span class="font-medium text-slate-900 dark:text-slate-200">
                                    {{ $container->spk->no_spk ?? '-' }}
                                </span>
                            </div>
                        </div>

                        <div class="inline-flex items-center justify-center rounded-lg bg-sky-600 px-4 py-2 text-sm font-semibold text-white transition group-hover:bg-sky-700">
                            BUKA DATA
                        </div>

                    </div>
                </button>
            </div>
        @endif


        {{-- FORM HOLD --}}
        @if($showForm && $container)
            <div class="mt-4 rounded-lg border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-950">

                <div class="mb-5 border-b border-slate-200 pb-4 dark:border-slate-800">
                    <h2 class="text-base font-semibold text-slate-900 dark:text-white">
                        Data HOLD
                    </h2>

                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Silakan tentukan jenis segel HOLD.
                    </p>
                </div>


                {{-- NO SPK --}}
                <div class="mb-4">
                    <flux:label>NO SPK</flux:label>

                    <flux:input
                        value="{{ $container->spk->no_spk ?? '-' }}"
                        readonly
                    />
                </div>


                {{-- NO CONTAINER --}}
                <div class="mb-4">
                    <flux:label>NO CONTAINER</flux:label>

                    <flux:input
                        value="{{ $container->container->no_cont ?? '-' }}"
                        readonly
                    />
                </div>


                {{-- NO DOKUMEN --}}
                <div class="mb-4">
                    <flux:label>NO DOKUMEN</flux:label>

                    <flux:input
                        value="{{ $container->spk->no_dok ?? '-' }}"
                        readonly
                    />
                </div>


                {{-- TANGGAL DOKUMEN --}}
                <div class="mb-4">
                    <flux:label>TANGGAL DOKUMEN</flux:label>

                    <flux:input
                        value="{{ $container->spk->tgl_dok ?? '-' }}"
                        readonly
                    />
                </div>


                {{-- JENIS DOKUMEN --}}
                <div class="mb-4">
                    <flux:label>JENIS DOKUMEN</flux:label>

                    <flux:input
                        value="-"
                        readonly
                    />

                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        Relasi jenis dokumen belum digunakan pada model SPK saat ini.
                    </p>
                </div>


                {{-- KETERANGAN --}}
                <div class="mb-4">
                    <flux:label>KETERANGAN</flux:label>

                    <flux:input
                        value="HOLD"
                        readonly
                    />
                </div>


                {{-- JENIS SEGEL --}}
                <div class="mb-5">
                    <flux:label>JENIS SEGEL</flux:label>

                    <div class="mt-2 grid grid-cols-1 gap-3 sm:grid-cols-3">

                        {{-- PUTIH --}}
                        <label class="cursor-pointer">
                            <input
                                type="radio"
                                wire:model="warnaHold"
                                value="N"
                                class="peer sr-only"
                            >

                            <div class="rounded-lg border border-slate-300 bg-white px-4 py-3 text-center text-sm font-semibold text-slate-700 transition peer-checked:border-sky-500 peer-checked:bg-sky-50 peer-checked:text-sky-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:peer-checked:border-sky-500 dark:peer-checked:bg-sky-500/10 dark:peer-checked:text-sky-400">
                                PUTIH
                            </div>
                        </label>


                        {{-- MERAH --}}
                        <label class="cursor-pointer">
                            <input
                                type="radio"
                                wire:model="warnaHold"
                                value="M"
                                class="peer sr-only"
                            >

                            <div class="rounded-lg border border-slate-300 bg-white px-4 py-3 text-center text-sm font-semibold text-slate-700 transition peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:text-red-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:peer-checked:border-red-500 dark:peer-checked:bg-red-500/10 dark:peer-checked:text-red-400">
                                MERAH
                            </div>
                        </label>


                        {{-- TIMAH --}}
                        <label class="cursor-pointer">
                            <input
                                type="radio"
                                wire:model="warnaHold"
                                value="T"
                                class="peer sr-only"
                            >

                            <div class="rounded-lg border border-slate-300 bg-white px-4 py-3 text-center text-sm font-semibold text-slate-700 transition peer-checked:border-amber-500 peer-checked:bg-amber-50 peer-checked:text-amber-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:peer-checked:border-amber-500 dark:peer-checked:bg-amber-500/10 dark:peer-checked:text-amber-400">
                                TIMAH
                            </div>
                        </label>

                    </div>

                    @error('warnaHold')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">
                            {{ $message }}
                        </p>
                    @enderror
                </div>


                {{-- BUTTON --}}
                <div class="flex flex-col gap-2 sm:flex-row">
                    <flux:button
                        wire:click="send"
                        wire:loading.attr="disabled"
                        variant="primary"
                        class="w-full sm:w-auto"
                    >
                        <span wire:loading.remove wire:target="send">
                            SIMPAN
                        </span>

                        <span wire:loading wire:target="send">
                            MENYIMPAN...
                        </span>
                    </flux:button>

                    <flux:button
                        wire:click="resetResult"
                        variant="ghost"
                        class="w-full sm:w-auto"
                    >
                        RESET
                    </flux:button>
                </div>

            </div>
        @endif


        {{-- DATA HOLD --}}
        <div class="mt-6 rounded-lg border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-950">

            <div class="flex items-center justify-between gap-3 border-b border-slate-200 px-4 py-4 dark:border-slate-800">
                <div>
                    <h2 class="text-base font-semibold text-slate-900 dark:text-white">
                        DATA HOLD
                    </h2>

                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        Menampilkan container yang sedang HOLD.
                    </p>
                </div>

                <div class="rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-700 dark:bg-sky-500/15 dark:text-sky-400">
                    {{ $heldContainers->total() }}
                </div>
            </div>


            {{-- TABLE --}}
            <div class="overflow-x-auto">
                <table class="min-w-[1050px] w-full text-left text-sm">

                    <thead class="bg-slate-100 text-xs uppercase text-slate-600 dark:bg-slate-900 dark:text-slate-400">
                        <tr>
                            <th class="whitespace-nowrap px-4 py-3 font-semibold">
                                NO SPK
                            </th>

                            <th class="whitespace-nowrap px-4 py-3 font-semibold">
                                NO CONTAINER
                            </th>

                            <th class="whitespace-nowrap px-4 py-3 font-semibold">
                                NO DOKUMEN
                            </th>

                            <th class="whitespace-nowrap px-4 py-3 font-semibold">
                                TANGGAL DOKUMEN
                            </th>

                            <th class="whitespace-nowrap px-4 py-3 font-semibold">
                                JENIS DOKUMEN
                            </th>

                            <th class="whitespace-nowrap px-4 py-3 font-semibold">
                                KETERANGAN
                            </th>

                            <th class="whitespace-nowrap px-4 py-3 font-semibold">
                                JENIS SEGEL
                            </th>

                            <th class="whitespace-nowrap px-4 py-3 text-center font-semibold">
                                ACTION
                            </th>
                        </tr>
                    </thead>


                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">

                        @forelse($heldContainers as $row)
                            <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-900/70">

                                {{-- NO SPK --}}
                                <td class="whitespace-nowrap px-4 py-3 font-medium text-slate-900 dark:text-white">
                                    {{ $row->spk->no_spk ?? '-' }}
                                </td>


                                {{-- NO CONTAINER --}}
                                <td class="whitespace-nowrap px-4 py-3 font-semibold text-slate-900 dark:text-white">
                                    {{ $row->container->no_cont ?? '-' }}
                                </td>


                                {{-- NO DOKUMEN --}}
                                <td class="whitespace-nowrap px-4 py-3 text-slate-600 dark:text-slate-300">
                                    {{ $row->spk->no_dok ?? '-' }}
                                </td>


                                {{-- TANGGAL DOKUMEN --}}
                                <td class="whitespace-nowrap px-4 py-3 text-slate-600 dark:text-slate-300">
                                    {{ $row->spk->tgl_dok ?? '-' }}
                                </td>


                                {{-- JENIS DOKUMEN --}}
                                <td class="whitespace-nowrap px-4 py-3 text-slate-600 dark:text-slate-300">
                                    -
                                </td>


                                {{-- KETERANGAN --}}
                                <td class="whitespace-nowrap px-4 py-3">
                                    <span class="inline-flex rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700 dark:bg-red-500/15 dark:text-red-400">
                                        HOLD
                                    </span>
                                </td>


                                {{-- JENIS SEGEL --}}
                                <td class="whitespace-nowrap px-4 py-3">
                                    @php
                                        $warna = $row->fl_warna_hold ?? 'N';

                                        $warnaLabel = match ($warna) {
                                            'M' => 'MERAH',
                                            'T' => 'TIMAH',
                                            default => 'PUTIH',
                                        };
                                    @endphp

                                    <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                        {{ $warnaLabel }}
                                    </span>
                                </td>


                                {{-- ACTION --}}
                                <td class="px-4 py-3 text-center">
                                    <flux:button
                                        wire:click="release({{ $row->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="release({{ $row->id }})"
                                        variant="danger"
                                        size="sm"
                                    >
                                        RELEASE
                                    </flux:button>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td
                                    colspan="8"
                                    class="px-4 py-10 text-center text-sm text-slate-500 dark:text-slate-400"
                                >
                                    Belum ada container HOLD.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>
                </table>
            </div>


            {{-- PAGINATION --}}
            @if($heldContainers->hasPages())
                <div class="border-t border-slate-200 px-4 py-3 dark:border-slate-800">
                    {{ $heldContainers->links() }}
                </div>
            @endif

        </div>

    </div>
</div>