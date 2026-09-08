@props(['message' => null, 'messageType' => null])

<div class="min-h-screen bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-white">

```
<div class="mx-auto w-full max-w-6xl px-3 py-4 sm:px-6 sm:py-6 lg:px-8">

    {{-- ================================================================
        HEADER
    ================================================================= --}}

    <div class="mb-4 flex items-center justify-between gap-2 sm:mb-5">

        <a
            href="{{ route('dashboard') }}"
            wire:navigate
            class="inline-flex min-h-11 items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-700 shadow-sm transition active:scale-[0.98] hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-sky-500 dark:border-white/10 dark:bg-white/5 dark:text-slate-200 dark:hover:bg-white/10"
        >

            <flux:icon.arrow-left class="size-4 shrink-0" />

            <span class="hidden sm:inline">
                Menu Handheld
            </span>

            <span class="sm:hidden">
                Kembali
            </span>

        </a>


        <span
            class="inline-flex min-h-10 items-center rounded-lg bg-sky-100 px-3 text-xs font-bold uppercase tracking-wide text-sky-700 dark:bg-sky-400/10 dark:text-sky-300"
        >
            Inspection
        </span>

    </div>


    {{-- ================================================================
        SEARCH
    ================================================================= --}}

    <div
        class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm sm:p-4 dark:border-white/10 dark:bg-white/5"
    >

        <label
            for="searchCont"
            class="block text-sm font-bold text-slate-700 dark:text-slate-200"
        >
            No Container
        </label>


        <div class="mt-2 flex flex-col gap-2 sm:flex-row sm:gap-3">

            {{-- INPUT --}}

            <div class="relative min-w-0 flex-1">

                <flux:icon.magnifying-glass
                    class="pointer-events-none absolute left-3 top-1/2 size-5 -translate-y-1/2 text-slate-400"
                />

                <input
                    id="searchCont"
                    type="text"
                    wire:model="searchCont"
                    wire:keydown.enter="search"
                    autofocus
                    autocomplete="off"
                    placeholder="Masukkan nomor container"
                    class="h-12 w-full rounded-lg border border-slate-200 bg-white pl-10 pr-3 text-base font-medium text-slate-950 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 dark:border-white/10 dark:bg-slate-950 dark:text-white"
                >

            </div>


            {{-- SEARCH --}}

            <button
                type="button"
                wire:click="search"
                wire:loading.attr="disabled"
                wire:target="search"
                class="inline-flex h-12 w-full shrink-0 items-center justify-center gap-2 rounded-lg bg-sky-700 px-5 text-sm font-bold text-white shadow-sm transition active:scale-[0.98] hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-70 sm:w-auto dark:focus:ring-offset-slate-950"
            >

                <span
                    wire:loading.remove
                    wire:target="search"
                >
                    <flux:icon.magnifying-glass class="size-4" />
                </span>

                <span
                    wire:loading
                    wire:target="search"
                    class="size-4 animate-spin rounded-full border-2 border-white/40 border-t-white"
                ></span>

                <span
                    wire:loading.remove
                    wire:target="search"
                >
                    Search
                </span>

                <span
                    wire:loading
                    wire:target="search"
                >
                    Searching...
                </span>

            </button>


            {{-- RESET --}}

            @if(
                $searchCont !== ''
                || !empty($operations)
                || $selectedOperation
            )

                <button
                    type="button"
                    wire:click="resetSearch"
                    class="inline-flex h-12 w-full shrink-0 items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-5 text-sm font-bold text-slate-700 transition active:scale-[0.98] hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-sky-500 sm:w-auto dark:border-white/10 dark:bg-slate-950 dark:text-slate-200 dark:hover:bg-white/10"
                >

                    <flux:icon.arrow-path class="size-4" />

                    Reset

                </button>

            @endif

        </div>


        @error('searchCont')

            <p class="mt-2 text-sm font-medium text-red-600 dark:text-red-400">
                {{ $message }}
            </p>

        @enderror

    </div>


    {{-- ================================================================
        MESSAGE
    ================================================================= --}}

    @if($message ?? null)

        <div
            class="mt-3 rounded-xl border px-4 py-3 sm:mt-4
            @if($messageType === 'danger')
                border-red-200 bg-red-50 text-red-700 dark:border-red-400/20 dark:bg-red-400/10 dark:text-red-300
            @elseif($messageType === 'success')
                border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-400/20 dark:bg-emerald-400/10 dark:text-emerald-300
            @else
                border-sky-200 bg-sky-50 text-sky-700 dark:border-sky-400/20 dark:bg-sky-400/10 dark:text-sky-300
            @endif"
        >

            <div class="flex items-start gap-3">

                <span class="mt-0.5 shrink-0">

                    @if($messageType === 'danger')

                        <flux:icon.exclamation-circle class="size-5" />

                    @elseif($messageType === 'success')

                        <flux:icon.check-circle class="size-5" />

                    @else

                        <flux:icon.information-circle class="size-5" />

                    @endif

                </span>

                <p class="text-sm font-semibold">
                    {{ $message ?? '' }}
                </p>

            </div>

        </div>

    @endif


    {{-- ================================================================
        MULTIPLE CONTAINER
    ================================================================= --}}

    @if(
        !empty($operations)
        && !$selectedOperation
    )

        <div
            class="mt-4 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm sm:mt-5 dark:border-white/10 dark:bg-slate-950"
        >

            {{-- HEADER --}}

            <div
                class="border-b border-slate-200 bg-slate-50 px-4 py-4 dark:border-white/10 dark:bg-white/5"
            >

                <div class="flex items-center justify-between gap-3">

                    <div>

                        <p
                            class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400"
                        >
                            Hasil Pencarian
                        </p>

                        <p
                            class="mt-1 text-base font-bold text-slate-900 dark:text-white"
                        >
                            Pilih Container
                        </p>

                    </div>

                    <span
                        class="shrink-0 rounded-md bg-sky-100 px-2.5 py-1 text-xs font-bold text-sky-700 dark:bg-sky-400/10 dark:text-sky-300"
                    >
                        {{ count($operations) }} Data
                    </span>

                </div>

            </div>


            {{-- CONTAINER LIST --}}

            <div class="divide-y divide-slate-200 dark:divide-white/10">

                @foreach($operations as $operation)

                    <button
                        type="button"
                        wire:key="inspection-operation-{{ $operation->id }}"
                        wire:click="selectOperation({{ $operation->id }})"
                        class="flex min-h-[76px] w-full items-center justify-between gap-4 px-4 py-4 text-left transition active:bg-sky-50 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-sky-500 dark:hover:bg-white/5"
                    >

                        <div class="min-w-0">

                            <p
                                class="truncate text-base font-black tracking-wide text-slate-900 dark:text-white"
                            >
                                {{ $operation->container?->no_cont ?? '-' }}
                            </p>

                            <p
                                class="mt-1 text-xs font-medium text-slate-500 dark:text-slate-400"
                            >
                                SPK:
                                <span class="font-bold">
                                    {{ $operation->spk?->no_spk ?? '-' }}
                                </span>
                            </p>

                        </div>


                        <span
                            class="flex size-9 shrink-0 items-center justify-center rounded-full bg-sky-100 text-sky-700 dark:bg-sky-400/10 dark:text-sky-300"
                        >
                            <flux:icon.chevron-right class="size-5" />
                        </span>

                    </button>

                @endforeach

            </div>

        </div>

    @endif


    {{-- ================================================================
        INSPECTION FORM
    ================================================================= --}}

    @if($selectedOperation)

        <div
            class="mt-4 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm sm:mt-5 dark:border-white/10 dark:bg-slate-950"
        >

            {{-- ========================================================
                FORM HEADER
            ========================================================= --}}

            <div
                class="border-b border-slate-200 bg-slate-50 px-4 py-4 sm:px-5 dark:border-white/10 dark:bg-white/5"
            >

                <div class="flex items-start justify-between gap-3">

                    <div class="min-w-0">

                        <p
                            class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400"
                        >
                            Data Pemeriksaan
                        </p>

                        <p
                            class="mt-1 truncate text-xl font-black tracking-tight text-slate-950 dark:text-white"
                        >
                            {{
                                $selectedOperation
                                    ->container
                                    ?->no_cont
                                ?? '-'
                            }}
                        </p>

                        <p
                            class="mt-1 text-xs font-medium text-slate-500 dark:text-slate-400"
                        >
                            SPK:
                            <span class="font-bold text-slate-700 dark:text-slate-200">
                                {{
                                    $selectedOperation
                                        ->spk
                                        ?->no_spk
                                    ?? '-'
                                }}
                            </span>
                        </p>

                    </div>


                    {{-- STATUS --}}

                    <span
                        class="shrink-0 rounded-md px-2.5 py-1.5 text-[10px] font-black uppercase tracking-wide
                        @if($inspection?->status === 'DONE')
                            bg-emerald-100 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300
                        @elseif($inspection?->status === 'WAITING')
                            bg-amber-100 text-amber-700 dark:bg-amber-400/10 dark:text-amber-300
                        @else
                            bg-sky-100 text-sky-700 dark:bg-sky-400/10 dark:text-sky-300
                        @endif"
                    >

                        @if($inspection?->status === 'DONE')

                            SELESAI

                        @elseif($inspection?->status === 'WAITING')

                            BERJALAN

                        @else

                            SIAP

                        @endif

                    </span>

                </div>

            </div>


            {{-- ========================================================
                FORM BODY
            ========================================================= --}}

            <div class="p-3 sm:p-5">


                {{-- ====================================================
                    CONTAINER INFORMATION
                ===================================================== --}}

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">


                    {{-- NO CONTAINER --}}

                    <div>

                        <label
                            class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400"
                        >
                            No Container
                        </label>

                        <div
                            class="flex min-h-12 items-center rounded-lg border border-slate-200 bg-slate-100 px-3 text-base font-black tracking-wide text-slate-800 dark:border-white/10 dark:bg-white/5 dark:text-white"
                        >
                            <span class="truncate">
                                {{
                                    $selectedOperation
                                        ->container
                                        ?->no_cont
                                    ?? '-'
                                }}
                            </span>
                        </div>

                    </div>


                    {{-- NO SPK --}}

                    <div>

                        <label
                            class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400"
                        >
                            No SPK
                        </label>

                        <div
                            class="flex min-h-12 items-center rounded-lg border border-slate-200 bg-slate-100 px-3 text-sm font-bold text-slate-800 dark:border-white/10 dark:bg-white/5 dark:text-white"
                        >
                            <span class="truncate">
                                {{
                                    $selectedOperation
                                        ->spk
                                        ?->no_spk
                                    ?? '-'
                                }}
                            </span>
                        </div>

                    </div>


                    {{-- TYPE CONTAINER --}}

                    <div>

                        <label
                            class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400"
                        >
                            Type Container
                        </label>

                        <div
                            class="flex min-h-12 items-center rounded-lg border border-slate-200 bg-slate-100 px-3 text-sm font-bold text-slate-800 dark:border-white/10 dark:bg-white/5 dark:text-white"
                        >
                            {{
                                $selectedOperation
                                    ->container
                                    ?->type
                                    ?->name
                                ?? '-'
                            }}
                        </div>

                    </div>


                    {{-- STATUS CONTAINER --}}

                    <div>

                        <label
                            class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400"
                        >
                            Status Container
                        </label>

                        <div
                            class="flex min-h-12 items-center rounded-lg border border-slate-200 bg-slate-100 px-3 text-sm font-bold text-slate-800 dark:border-white/10 dark:bg-white/5 dark:text-white"
                        >

                            {{
                                $inspection?->status === 'DONE'
                                    ? '500'
                                    : 'READY'
                            }}

                        </div>

                    </div>


                    {{-- JENIS KEGIATAN --}}

                    <div class="sm:col-span-2">

                        <label
                            class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400"
                        >
                            Jenis Kegiatan
                        </label>

                        <div
                            class="flex min-h-12 items-center rounded-lg border border-slate-200 bg-slate-100 px-3 text-sm font-bold text-slate-800 dark:border-white/10 dark:bg-white/5 dark:text-white"
                        >

                            {{
                                $jobSlip?->gatepass?->jenis_kegiatan === '1'
                                    ? 'BEHANDLE 1'
                                    : (
                                        $jobSlip?->gatepass?->jenis_kegiatan === '2'
                                            ? 'BEHANDLE 2'
                                            : '-'
                                    )
                            }}

                        </div>

                    </div>

                </div>


                {{-- ====================================================
                    INPUT PEMERIKSAAN
                ===================================================== --}}

                <div
                    class="mt-5 border-t border-slate-200 pt-5 dark:border-white/10"
                >

                    <p
                        class="mb-4 text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400"
                    >
                        Data Pemeriksaan
                    </p>


                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">


                        {{-- NO SEAL --}}

                        <div>

                            <label
                                for="noSeal"
                                class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400"
                            >
                                No Seal
                            </label>

                            <input
                                id="noSeal"
                                type="text"
                                wire:model="noSeal"
                                @disabled(!$inspectionStarted)
                                autocomplete="off"
                                placeholder="Masukkan No Seal"
                                class="h-12 w-full rounded-lg border border-slate-200 bg-white px-3 text-base font-medium text-slate-950 outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 disabled:bg-slate-100 disabled:text-slate-500 dark:border-white/10 dark:bg-slate-950 dark:text-white dark:disabled:bg-white/5"
                            >

                            @error('noSeal')

                                <p class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400">
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>


                        {{-- ALAT --}}

                        <div>

                            <label
                                for="alat"
                                class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400"
                            >
                                Alat
                            </label>

                            <select
                                id="alat"
                                wire:model="alat"
                                @disabled($inspection?->status === 'DONE')
                                class="h-12 w-full rounded-lg border border-slate-200 bg-white px-3 text-base font-medium text-slate-700 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 disabled:bg-slate-100 dark:border-white/10 dark:bg-slate-950 dark:text-slate-200 dark:disabled:bg-white/5"
                            >

                                <option value="">
                                    Pilih Alat
                                </option>

                                @foreach($equipments as $equipment)

                                    <option value="{{ $equipment->id }}">
                                        {{ $equipment->code }}
                                        -
                                        {{ $equipment->name }}
                                    </option>

                                @endforeach

                            </select>

                            @error('alat')

                                <p class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400">
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>


                        {{-- OPERATOR --}}

                        <div class="sm:col-span-2">

                            <label
                                for="operator"
                                class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400"
                            >
                                Operator
                            </label>

                            <select
                                id="operator"
                                wire:model="operator"
                                @disabled($inspection?->status === 'DONE')
                                class="h-12 w-full rounded-lg border border-slate-200 bg-white px-3 text-base font-medium text-slate-700 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 disabled:bg-slate-100 dark:border-white/10 dark:bg-slate-950 dark:text-slate-200 dark:disabled:bg-white/5"
                            >

                                <option value="">
                                    Pilih Operator
                                </option>

                                @foreach($operators as $user)

                                    <option value="{{ $user->id }}">
                                        {{ $user->username }}
                                        -
                                        {{ $user->name }}
                                    </option>

                                @endforeach

                            </select>

                            @error('operator')

                                <p class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400">
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>

                    </div>

                </div>


                {{-- ====================================================
                    INSPECTION TIMESTAMP
                ===================================================== --}}

                @if($inspection)

                    <div
                        class="mt-5 grid grid-cols-1 gap-3 border-t border-slate-200 pt-5 sm:grid-cols-2 dark:border-white/10"
                    >

                        {{-- START --}}

                        <div
                            class="rounded-lg border border-slate-200 bg-slate-50 p-4 dark:border-white/10 dark:bg-white/5"
                        >

                            <p
                                class="text-[10px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400"
                            >
                                Mulai Pemeriksaan
                            </p>

                            <p
                                class="mt-1.5 text-sm font-bold text-slate-800 dark:text-white"
                            >
                                {{
                                    $inspection->started_at
                                        ? $inspection
                                            ->started_at
                                            ->format('d-m-Y H:i:s')
                                        : '-'
                                }}
                            </p>

                        </div>


                        {{-- FINISH --}}

                        <div
                            class="rounded-lg border border-slate-200 bg-slate-50 p-4 dark:border-white/10 dark:bg-white/5"
                        >

                            <p
                                class="text-[10px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400"
                            >
                                Selesai Pemeriksaan
                            </p>

                            <p
                                class="mt-1.5 text-sm font-bold text-slate-800 dark:text-white"
                            >
                                {{
                                    $inspection->finished_at
                                        ? $inspection
                                            ->finished_at
                                            ->format('d-m-Y H:i:s')
                                        : '-'
                                }}
                            </p>

                        </div>

                    </div>

                @endif


                {{-- ====================================================
                    ACTION
                ===================================================== --}}

                <div
                    class="mt-5 flex flex-col gap-2 border-t border-slate-200 pt-5 sm:flex-row dark:border-white/10"
                >

                    {{-- MULAI --}}

                    @if(!$inspection)

                        <button
                            type="button"
                            wire:click="startInspection"
                            wire:loading.attr="disabled"
                            wire:target="startInspection"
                            class="inline-flex h-12 w-full items-center justify-center gap-2 rounded-lg bg-sky-700 px-5 text-sm font-black text-white shadow-sm transition active:scale-[0.98] hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto dark:focus:ring-offset-slate-950"
                        >

                            <span
                                wire:loading.remove
                                wire:target="startInspection"
                            >
                                MULAI PEMERIKSAAN
                            </span>

                            <span
                                wire:loading
                                wire:target="startInspection"
                            >
                                MEMULAI...
                            </span>

                        </button>


                    {{-- SELESAI --}}

                    @elseif($inspection->status === 'WAITING')

                        <button
                            type="button"
                            wire:click="finishInspection"
                            wire:loading.attr="disabled"
                            wire:target="finishInspection"
                            class="inline-flex h-12 w-full items-center justify-center gap-2 rounded-lg bg-sky-700 px-5 text-sm font-black text-white shadow-sm transition active:scale-[0.98] hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto dark:focus:ring-offset-slate-950"
                        >

                            <span
                                wire:loading.remove
                                wire:target="finishInspection"
                            >
                                SELESAI PEMERIKSAAN
                            </span>

                            <span
                                wire:loading
                                wire:target="finishInspection"
                            >
                                MENYELESAIKAN...
                            </span>

                        </button>


                    {{-- DONE --}}

                    @else

                        <div
                            class="inline-flex h-12 w-full items-center justify-center gap-2 rounded-lg bg-emerald-50 px-5 text-sm font-black text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300 sm:w-auto"
                        >

                            <flux:icon.check-circle class="size-5" />

                            PEMERIKSAAN SELESAI

                        </div>


                        {{-- LANJUT BEHANDLE 2 --}}

                        @if(
                            $jobSlip?->gatepass?->jenis_kegiatan === '1'
                            && !$showBehandle2Form
                        )

                            <button
                                type="button"
                                wire:click="openBehandle2Form"
                                class="inline-flex h-12 w-full items-center justify-center gap-2 rounded-lg border border-sky-600 bg-white px-5 text-sm font-bold text-sky-700 transition active:scale-[0.98] hover:bg-sky-50 focus:outline-none focus:ring-2 focus:ring-sky-500 sm:w-auto dark:border-sky-400/50 dark:bg-slate-950 dark:text-sky-300 dark:hover:bg-white/5"
                            >

                                <flux:icon.arrow-right class="size-4" />

                                LANJUT BEHANDLE 2

                            </button>

                        @endif

                    @endif


                    {{-- RESET --}}

                    <button
                        type="button"
                        wire:click="resetSearch"
                        class="inline-flex h-12 w-full items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-5 text-sm font-bold text-slate-700 transition active:scale-[0.98] hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-sky-500 sm:w-auto dark:border-white/10 dark:bg-slate-950 dark:text-slate-200 dark:hover:bg-white/10"
                    >

                        <flux:icon.arrow-path class="size-4" />

                        Reset

                    </button>

                </div>


                {{-- ====================================================
                    BEHANDLE 2 FORM
                ===================================================== --}}

                @if($showBehandle2Form)

                    <div
                        class="mt-5 rounded-xl border border-sky-200 bg-sky-50 p-4 dark:border-sky-400/20 dark:bg-sky-400/10"
                    >

                        {{-- HEADER --}}

                        <div class="mb-4">

                            <p
                                class="text-xs font-bold uppercase tracking-wide text-sky-700 dark:text-sky-300"
                            >
                                Lanjutan Proses
                            </p>

                            <h3
                                class="mt-1 text-base font-black text-slate-900 dark:text-white"
                            >
                                Gatepass & Job Slip Behandle 2
                            </h3>

                        </div>


                        {{-- FORM --}}

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">


                            {{-- NO DOK --}}

                            <div>

                                <label
                                    for="b2NoDok"
                                    class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-600 dark:text-slate-300"
                                >
                                    No Dokumen
                                </label>

                                <input
                                    id="b2NoDok"
                                    type="text"
                                    wire:model="b2NoDok"
                                    autocomplete="off"
                                    placeholder="Masukkan No Dokumen"
                                    class="h-12 w-full rounded-lg border border-slate-200 bg-white px-3 text-base font-medium text-slate-900 outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 dark:border-white/10 dark:bg-slate-950 dark:text-white"
                                >

                                @error('b2NoDok')

                                    <p class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400">
                                        {{ $message }}
                                    </p>

                                @enderror

                            </div>


                            {{-- JENIS DOK --}}

                            <div>

                                <label
                                    for="b2JnsDok"
                                    class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-600 dark:text-slate-300"
                                >
                                    Jenis Dokumen
                                </label>

                                <input
                                    id="b2JnsDok"
                                    type="text"
                                    wire:model="b2JnsDok"
                                    autocomplete="off"
                                    placeholder="Masukkan Jenis Dokumen"
                                    class="h-12 w-full rounded-lg border border-slate-200 bg-white px-3 text-base font-medium text-slate-900 outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 dark:border-white/10 dark:bg-slate-950 dark:text-white"
                                >

                                @error('b2JnsDok')

                                    <p class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400">
                                        {{ $message }}
                                    </p>

                                @enderror

                            </div>


                            {{-- TGL DOK --}}

                            <div>

                                <label
                                    for="b2TglDok"
                                    class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-600 dark:text-slate-300"
                                >
                                    Tanggal Dokumen
                                </label>

                                <input
                                    id="b2TglDok"
                                    type="date"
                                    wire:model="b2TglDok"
                                    class="h-12 w-full rounded-lg border border-slate-200 bg-white px-3 text-base font-medium text-slate-900 outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 dark:border-white/10 dark:bg-slate-950 dark:text-white"
                                >

                                @error('b2TglDok')

                                    <p class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400">
                                        {{ $message }}
                                    </p>

                                @enderror

                            </div>

                        </div>


                        {{-- ACTION BEHANDLE 2 --}}

                        <div
                            class="mt-4 flex flex-col gap-2 sm:flex-row"
                        >

                            <button
                                type="button"
                                wire:click="createBehandle2"
                                wire:loading.attr="disabled"
                                wire:target="createBehandle2"
                                class="inline-flex h-12 w-full items-center justify-center rounded-lg bg-sky-700 px-5 text-sm font-black text-white shadow-sm transition active:scale-[0.98] hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-500 disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto"
                            >

                                <span
                                    wire:loading.remove
                                    wire:target="createBehandle2"
                                >
                                    SIMPAN
                                </span>

                                <span
                                    wire:loading
                                    wire:target="createBehandle2"
                                >
                                    MENYIMPAN...
                                </span>

                            </button>


                            <button
                                type="button"
                                wire:click="cancelBehandle2Form"
                                class="inline-flex h-12 w-full items-center justify-center rounded-lg border border-slate-200 bg-white px-5 text-sm font-bold text-slate-700 transition active:scale-[0.98] hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-sky-500 sm:w-auto dark:border-white/10 dark:bg-slate-950 dark:text-slate-200 dark:hover:bg-white/10"
                            >
                                BATAL
                            </button>

                        </div>

                    </div>

                @endif

            </div>

        </div>

    @endif


    {{-- ================================================================
        EMPTY STATE
    ================================================================= --}}

    @if(empty($operations) && !$selectedOperation)

        <div
            class="mt-4 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm sm:mt-5 dark:border-white/10 dark:bg-slate-950"
        >

            <div
                class="border-b border-slate-200 bg-slate-50 px-4 py-4 dark:border-white/10 dark:bg-white/5"
            >

                <p
                    class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400"
                >
                    Inspection
                </p>

                <p
                    class="mt-1 text-base font-black text-slate-900 dark:text-white"
                >
                    Data Pemeriksaan
                </p>

            </div>


            <div class="px-5 py-12 text-center sm:py-14">

                <div
                    class="mx-auto flex size-14 items-center justify-center rounded-full bg-sky-100 text-sky-700 dark:bg-sky-400/10 dark:text-sky-300"
                >

                    <flux:icon.clipboard-document-check class="size-7" />

                </div>


                <p
                    class="mt-4 text-sm font-bold text-slate-700 dark:text-slate-200"
                >
                    Belum ada data pemeriksaan
                </p>

                <p
                    class="mx-auto mt-1 max-w-sm text-xs leading-5 text-slate-500 dark:text-slate-400"
                >
                    Masukkan nomor container pada kolom pencarian
                    untuk mencari data pemeriksaan.
                </p>

            </div>

        </div>

    @endif


    {{-- ================================================================
        FOOTER
    ================================================================= --}}

    <footer
        class="mt-6 border-t border-slate-200 pt-4 text-center dark:border-white/10"
    >

        <p class="text-[11px] font-medium text-slate-400 dark:text-slate-500">
            INSPECTION · PortOps Central
        </p>

    </footer>

</div>
```

</div>
