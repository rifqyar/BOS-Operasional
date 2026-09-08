<div class="min-h-screen bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-white">

    <div class="mx-auto w-full max-w-6xl px-4 py-4 sm:px-6 sm:py-6">

        {{-- ============================================================
            HEADER
        ============================================================= --}}

        <header class="mb-5 flex items-center justify-between">

            <div class="flex items-center gap-3">

                <a
                    href="{{ route('home') }}"
                    class="flex h-10 w-10 items-center justify-center rounded-xl
                           border border-slate-200 bg-white text-slate-600
                           shadow-sm transition hover:bg-slate-100
                           dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300"
                    title="Kembali"
                >
                    <span class="material-symbols-outlined">
                        arrow_back
                    </span>
                </a>

                <div>
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400">
                        PortOps Central
                    </p>

                    <h1 class="text-base font-bold tracking-tight sm:text-lg">
                        Monitoring Reefer
                    </h1>
                </div>

            </div>


            <div
                class="flex items-center gap-2 rounded-full
                       bg-blue-50 px-3 py-1.5
                       text-xs font-bold text-blue-700
                       dark:bg-blue-950/40 dark:text-blue-300"
            >
                <span class="material-symbols-outlined text-[17px]">
                    thermostat
                </span>

                REEFER
            </div>

        </header>



        {{-- ============================================================
            TITLE
        ============================================================= --}}

        <div class="mb-5">

            <div class="flex items-center gap-2">

                <span
                    class="material-symbols-outlined
                           text-[25px] text-blue-600
                           dark:text-blue-400"
                >
                    thermostat
                </span>

                <h2
                    class="text-xl font-black tracking-tight
                           sm:text-2xl"
                >
                    MONITORING REEFER
                </h2>

            </div>

            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Monitoring temperatur container reefer.
            </p>

        </div>



        {{-- ============================================================
            SEARCH
        ============================================================= --}}

        <section
            class="mb-5 rounded-2xl border border-slate-200
                   bg-white p-4 shadow-sm
                   dark:border-slate-800 dark:bg-slate-900
                   sm:p-5"
        >

            <div class="mb-4 flex items-center gap-3">

                <div
                    class="flex h-10 w-10 shrink-0 items-center
                           justify-center rounded-xl
                           bg-blue-50 text-blue-600
                           dark:bg-blue-950/40 dark:text-blue-400"
                >
                    <span class="material-symbols-outlined">
                        search
                    </span>
                </div>

                <div>
                    <h3 class="text-sm font-bold sm:text-base">
                        Cari Container
                    </h3>

                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Masukkan nomor container reefer.
                    </p>
                </div>

            </div>


            <form wire:submit="search">

                <div class="flex flex-col gap-3 sm:flex-row">

                    <div class="w-full">

                        <label
                            for="searchCont"
                            class="mb-1.5 block text-xs font-bold
                                   uppercase tracking-wider
                                   text-slate-600 dark:text-slate-400"
                        >
                            No Container
                        </label>

                        <input
                            id="searchCont"
                            type="text"
                            wire:model="searchCont"
                            autofocus
                            autocomplete="off"
                            placeholder="Contoh: HLCU9988776"
                            class="h-12 w-full rounded-xl
                                   border border-slate-300
                                   bg-white px-4
                                   text-base font-semibold
                                   uppercase outline-none
                                   placeholder:font-normal
                                   placeholder:text-slate-400
                                   focus:border-blue-600
                                   focus:ring-2 focus:ring-blue-100
                                   dark:border-slate-700
                                   dark:bg-slate-950
                                   dark:placeholder:text-slate-600
                                   dark:focus:border-blue-500
                                   dark:focus:ring-blue-950"
                        >

                        @error('searchCont')
                            <p class="mt-2 text-xs font-medium text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    <div class="flex gap-2 sm:items-end">

                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="inline-flex h-12 flex-1 items-center
                                   justify-center gap-2 rounded-xl
                                   bg-blue-600 px-5 text-sm font-bold
                                   text-white transition
                                   hover:bg-blue-700
                                   disabled:cursor-not-allowed
                                   disabled:opacity-50
                                   sm:flex-none"
                        >

                            <span
                                wire:loading.remove
                                wire:target="search"
                            >
                                <span class="material-symbols-outlined text-[19px]">
                                    search
                                </span>
                            </span>

                            <span
                                wire:loading
                                wire:target="search"
                                class="material-symbols-outlined animate-spin text-[19px]"
                            >
                                progress_activity
                            </span>

                            <span
                                wire:loading.remove
                                wire:target="search"
                            >
                                SEARCH
                            </span>

                            <span
                                wire:loading
                                wire:target="search"
                            >
                                SEARCHING...
                            </span>

                        </button>


                        @if(
                            $searchCont !== ''
                            || !empty($operations)
                            || $selectedOperation
                        )

                            <button
                                type="button"
                                wire:click="resetSearch"
                                class="inline-flex h-12 w-12 shrink-0
                                       items-center justify-center
                                       rounded-xl border
                                       border-slate-300
                                       bg-white text-slate-600
                                       transition hover:bg-slate-100
                                       dark:border-slate-700
                                       dark:bg-slate-900
                                       dark:text-slate-300"
                                title="Reset"
                            >
                                <span class="material-symbols-outlined">
                                    refresh
                                </span>
                            </button>

                        @endif

                    </div>

                </div>

            </form>

        </section>



        {{-- ============================================================
            MESSAGE
        ============================================================= --}}

        @isset($message)

            @if($message)

                <div
                    class="mb-5 rounded-xl border px-4 py-3

                    @if($messageType === 'danger')
                        border-red-200 bg-red-50 text-red-700
                        dark:border-red-900/50 dark:bg-red-950/30 dark:text-red-300
                    @elseif($messageType === 'success')
                        border-green-200 bg-green-50 text-green-700
                        dark:border-green-900/50 dark:bg-green-950/30 dark:text-green-300
                    @else
                        border-blue-200 bg-blue-50 text-blue-700
                        dark:border-blue-900/50 dark:bg-blue-950/30 dark:text-blue-300
                    @endif"
                >

                    <div class="flex items-start gap-3">

                        <span class="material-symbols-outlined shrink-0">
                            @if($messageType === 'danger')
                                error
                            @elseif($messageType === 'success')
                                check_circle
                            @else
                                info
                            @endif
                        </span>

                        <p class="text-sm font-semibold">
                            {{ $message }}
                        </p>

                    </div>

                </div>

            @endif

        @endisset



        {{-- ============================================================
            MULTIPLE CONTAINER
        ============================================================= --}}

        @if(
            !empty($operations)
            && !$selectedOperation
        )

            <section
                class="mb-5 overflow-hidden rounded-2xl
                       border border-slate-200 bg-white
                       shadow-sm
                       dark:border-slate-800 dark:bg-slate-900"
            >

                <div
                    class="border-b border-slate-200 px-4 py-4
                           dark:border-slate-800"
                >

                    <div class="flex items-center gap-2">

                        <span
                            class="material-symbols-outlined
                                   text-blue-600 dark:text-blue-400"
                        >
                            inventory_2
                        </span>

                        <div>

                            <h3 class="text-sm font-bold sm:text-base">
                                Pilih Container Reefer
                            </h3>

                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                Ditemukan {{ count($operations) }} container.
                            </p>

                        </div>

                    </div>

                </div>


                <div class="grid gap-3 p-4 sm:grid-cols-2">

                    @foreach($operations as $operation)

                        <button
                            type="button"
                            wire:click="selectOperation({{ $operation->id }})"
                            class="group flex min-h-[76px] w-full
                                   items-center justify-between
                                   rounded-xl border
                                   border-slate-200
                                   bg-white px-4 py-3
                                   text-left transition
                                   hover:border-blue-500
                                   hover:bg-blue-50
                                   active:scale-[0.99]
                                   dark:border-slate-700
                                   dark:bg-slate-950
                                   dark:hover:border-blue-500
                                   dark:hover:bg-blue-950/30"
                        >

                            <div class="min-w-0">

                                <p
                                    class="truncate text-base font-black
                                           tracking-wide
                                           text-slate-900
                                           dark:text-white"
                                >
                                    {{
                                        $operation
                                            ->container
                                            ?->no_cont
                                        ?? '-'
                                    }}
                                </p>

                                <p
                                    class="mt-1 text-xs
                                           text-slate-500
                                           dark:text-slate-400"
                                >
                                    SPK:
                                    <span class="font-semibold">
                                        {{
                                            $operation
                                                ->spk
                                                ?->no_spk
                                            ?? '-'
                                        }}
                                    </span>
                                </p>

                            </div>


                            <span
                                class="material-symbols-outlined shrink-0
                                       text-blue-600
                                       transition group-hover:translate-x-1
                                       dark:text-blue-400"
                            >
                                chevron_right
                            </span>

                        </button>

                    @endforeach

                </div>

            </section>

        @endif



        {{-- ============================================================
            SELECTED CONTAINER
        ============================================================= --}}

        @if($selectedOperation)

            <section
                class="mb-5 overflow-hidden rounded-2xl
                       border border-slate-200 bg-white
                       shadow-sm
                       dark:border-slate-800 dark:bg-slate-900"
            >

                {{-- CONTAINER HEADER --}}

                <div
                    class="border-b border-slate-200 px-4 py-4
                           dark:border-slate-800 sm:px-5"
                >

                    <div
                        class="flex flex-col gap-3
                               sm:flex-row sm:items-center
                               sm:justify-between"
                    >

                        <div class="min-w-0">

                            <div class="mb-2 flex items-center gap-2">

                                <span
                                    class="material-symbols-outlined
                                           text-blue-600
                                           dark:text-blue-400"
                                >
                                    thermostat
                                </span>

                                <span
                                    class="text-xs font-bold uppercase
                                           tracking-wider
                                           text-slate-500
                                           dark:text-slate-400"
                                >
                                    Monitoring Reefer
                                </span>

                            </div>


                            <p
                                class="break-all text-2xl font-black
                                       tracking-tight
                                       text-slate-950
                                       dark:text-white
                                       sm:text-3xl"
                            >
                                {{
                                    $selectedOperation
                                        ->container
                                        ?->no_cont
                                    ?? '-'
                                }}
                            </p>


                            <p
                                class="mt-1 text-xs
                                       text-slate-500
                                       dark:text-slate-400"
                            >
                                SPK:
                                <span class="font-semibold">
                                    {{
                                        $selectedOperation
                                            ->spk
                                            ?->no_spk
                                        ?? '-'
                                    }}
                                </span>
                            </p>

                        </div>


                        @if($condition === 1)

                            <span
                                class="inline-flex w-fit items-center gap-1.5
                                       rounded-full bg-green-100
                                       px-3 py-1.5 text-[10px]
                                       font-black uppercase tracking-wider
                                       text-green-700
                                       dark:bg-green-950/40
                                       dark:text-green-300"
                            >
                                <span
                                    class="h-2 w-2 rounded-full bg-green-500"
                                ></span>

                                AKTIF
                            </span>

                        @else

                            <span
                                class="inline-flex w-fit items-center gap-1.5
                                       rounded-full bg-red-100
                                       px-3 py-1.5 text-[10px]
                                       font-black uppercase tracking-wider
                                       text-red-700
                                       dark:bg-red-950/40
                                       dark:text-red-300"
                            >
                                <span
                                    class="h-2 w-2 rounded-full bg-red-500"
                                ></span>

                                UNPLUGIN
                            </span>

                        @endif

                    </div>

                </div>



                <div class="p-4 sm:p-5">


                    {{-- ====================================================
                        SUDAH UNPLUGIN
                    ===================================================== --}}

                    @if($condition !== 1)

                        <div
                            class="rounded-xl border border-red-200
                                   bg-red-50 p-4
                                   dark:border-red-900/50
                                   dark:bg-red-950/30"
                        >

                            <div class="flex items-start gap-3">

                                <div
                                    class="flex h-10 w-10 shrink-0
                                           items-center justify-center
                                           rounded-full bg-red-100
                                           text-red-600
                                           dark:bg-red-950
                                           dark:text-red-400"
                                >
                                    <span class="material-symbols-outlined">
                                        warning
                                    </span>
                                </div>


                                <div>

                                    <p
                                        class="text-sm font-bold
                                               text-red-700
                                               dark:text-red-300"
                                    >
                                        Container Sudah Unplugin
                                    </p>

                                    <p
                                        class="mt-1 text-xs leading-5
                                               text-red-600
                                               dark:text-red-400"
                                    >
                                        Monitoring tidak dapat dilakukan
                                        karena container sudah UNPLUGIN.
                                    </p>

                                </div>

                            </div>

                        </div>


                    {{-- ====================================================
                        MONITORING FORM
                    ===================================================== --}}

                    @else

                        <div class="space-y-5">


                            {{-- TEMPERATURE INFO --}}

                            <div
                                class="grid grid-cols-1 gap-4
                                       sm:grid-cols-2"
                            >

                                {{-- TEMPERATURE SEBELUMNYA --}}

                                <div>

                                    <label
                                        class="mb-1.5 block text-xs
                                               font-bold uppercase
                                               tracking-wider
                                               text-slate-600
                                               dark:text-slate-400"
                                    >
                                        Temperature Sebelumnya
                                    </label>

                                    <div
                                        class="flex h-14 items-center
                                               rounded-xl border
                                               border-slate-200
                                               bg-slate-50 px-4
                                               dark:border-slate-700
                                               dark:bg-slate-950"
                                    >

                                        <span
                                            class="material-symbols-outlined
                                                   mr-3 text-slate-400"
                                        >
                                            history
                                        </span>

                                        <span
                                            class="text-lg font-bold
                                                   text-slate-700
                                                   dark:text-slate-200"
                                        >
                                            {{
                                                $lastMonitoring
                                                    ?->temperature
                                                ?? '-'
                                            }}
                                        </span>

                                    </div>

                                </div>



                                {{-- TEMPERATURE SAAT INI --}}

                                <div>

                                    <label
                                        for="temperature"
                                        class="mb-1.5 block text-xs
                                               font-bold uppercase
                                               tracking-wider
                                               text-slate-600
                                               dark:text-slate-400"
                                    >
                                        Temperature Saat Ini
                                    </label>

                                    <div class="relative">

                                        <span
                                            class="material-symbols-outlined
                                                   absolute left-4 top-1/2
                                                   -translate-y-1/2
                                                   text-blue-600
                                                   dark:text-blue-400"
                                        >
                                            thermostat
                                        </span>

                                        <input
                                            id="temperature"
                                            type="text"
                                            wire:model="temperature"
                                            inputmode="decimal"
                                            placeholder="Masukkan temperature"
                                            class="h-14 w-full rounded-xl
                                                   border
                                                   border-slate-300
                                                   bg-white
                                                   pl-12 pr-4
                                                   text-lg font-bold
                                                   outline-none
                                                   focus:border-blue-600
                                                   focus:ring-2
                                                   focus:ring-blue-100
                                                   dark:border-slate-700
                                                   dark:bg-slate-950
                                                   dark:focus:border-blue-500
                                                   dark:focus:ring-blue-950"
                                        >

                                    </div>

                                    @error('temperature')

                                        <p
                                            class="mt-2 text-xs font-medium
                                                   text-red-600
                                                   dark:text-red-400"
                                        >
                                            {{ $message }}
                                        </p>

                                    @enderror

                                </div>

                            </div>



                            {{-- NOTE --}}

                            <div>

                                <label
                                    for="note"
                                    class="mb-1.5 block text-xs
                                           font-bold uppercase
                                           tracking-wider
                                           text-slate-600
                                           dark:text-slate-400"
                                >
                                    Catatan
                                </label>

                                <textarea
                                    id="note"
                                    wire:model="note"
                                    rows="4"
                                    placeholder="Masukkan catatan monitoring..."
                                    class="w-full rounded-xl
                                           border border-slate-300
                                           bg-white px-4 py-3
                                           text-sm outline-none
                                           placeholder:text-slate-400
                                           focus:border-blue-600
                                           focus:ring-2
                                           focus:ring-blue-100
                                           dark:border-slate-700
                                           dark:bg-slate-950
                                           dark:placeholder:text-slate-600
                                           dark:focus:border-blue-500
                                           dark:focus:ring-blue-950"
                                ></textarea>

                                @error('note')

                                    <p
                                        class="mt-2 text-xs font-medium
                                               text-red-600
                                               dark:text-red-400"
                                    >
                                        {{ $message }}
                                    </p>

                                @enderror

                            </div>



                            {{-- ACTION --}}

                            <div
                                class="flex flex-col gap-3
                                       border-t border-slate-200
                                       pt-5
                                       dark:border-slate-800
                                       sm:flex-row"
                            >

                                <button
                                    type="button"
                                    wire:click="monitoring"
                                    wire:loading.attr="disabled"
                                    class="inline-flex h-12 w-full
                                           items-center justify-center
                                           gap-2 rounded-xl
                                           bg-blue-600 px-6
                                           text-sm font-bold text-white
                                           transition
                                           hover:bg-blue-700
                                           active:scale-[0.99]
                                           disabled:cursor-not-allowed
                                           disabled:opacity-50
                                           sm:w-auto"
                                >

                                    <span
                                        wire:loading.remove
                                        wire:target="monitoring"
                                        class="material-symbols-outlined
                                               text-[20px]"
                                    >
                                        thermostat
                                    </span>

                                    <span
                                        wire:loading
                                        wire:target="monitoring"
                                        class="material-symbols-outlined
                                               animate-spin text-[20px]"
                                    >
                                        progress_activity
                                    </span>

                                    <span
                                        wire:loading.remove
                                        wire:target="monitoring"
                                    >
                                        MONITORING
                                    </span>

                                    <span
                                        wire:loading
                                        wire:target="monitoring"
                                    >
                                        MENYIMPAN...
                                    </span>

                                </button>


                                <button
                                    type="button"
                                    wire:click="resetSearch"
                                    class="inline-flex h-12 w-full
                                           items-center justify-center
                                           gap-2 rounded-xl
                                           border border-slate-300
                                           bg-white px-6
                                           text-sm font-bold
                                           text-slate-700
                                           transition
                                           hover:bg-slate-100
                                           dark:border-slate-700
                                           dark:bg-slate-900
                                           dark:text-slate-200
                                           dark:hover:bg-slate-800
                                           sm:w-auto"
                                >

                                    <span
                                        class="material-symbols-outlined
                                               text-[19px]"
                                    >
                                        refresh
                                    </span>

                                    RESET

                                </button>

                            </div>

                        </div>

                    @endif

                </div>

            </section>



            {{-- ============================================================
                MONITORING HISTORY
            ============================================================= --}}

            <section
                class="overflow-hidden rounded-2xl
                       border border-slate-200 bg-white
                       shadow-sm
                       dark:border-slate-800 dark:bg-slate-900"
            >

                <div
                    class="border-b border-slate-200 px-4 py-4
                           dark:border-slate-800 sm:px-5"
                >

                    <div
                        class="flex items-center
                               justify-between gap-3"
                    >

                        <div class="min-w-0">

                            <div class="flex items-center gap-2">

                                <span
                                    class="material-symbols-outlined
                                           text-blue-600
                                           dark:text-blue-400"
                                >
                                    history
                                </span>

                                <h3 class="text-sm font-bold sm:text-base">
                                    Riwayat Monitoring
                                </h3>

                            </div>

                            <p
                                class="mt-1 text-xs
                                       text-slate-500
                                       dark:text-slate-400"
                            >
                                Data monitoring container.
                            </p>

                        </div>


                        <div
                            class="shrink-0 rounded-xl
                                   bg-blue-50 px-3 py-2
                                   text-center
                                   dark:bg-blue-950/40"
                        >

                            <p
                                class="text-[9px] font-bold uppercase
                                       tracking-wider
                                       text-slate-500
                                       dark:text-slate-400"
                            >
                                Total
                            </p>

                            <p
                                class="text-lg font-black
                                       text-blue-600
                                       dark:text-blue-400"
                            >
                                {{ count($monitoringLogs) }}
                            </p>

                        </div>

                    </div>

                </div>



                {{-- ========================================================
                    MOBILE HISTORY CARD
                ========================================================= --}}

                <div class="space-y-3 p-4 sm:hidden">

                    @forelse(
                        $monitoringLogs
                        as $index => $log
                    )

                        <div
                            class="rounded-xl border
                                   border-slate-200
                                   bg-slate-50 p-4
                                   dark:border-slate-700
                                   dark:bg-slate-950"
                        >

                            <div
                                class="mb-3 flex items-center
                                       justify-between"
                            >

                                <span
                                    class="text-xs font-bold
                                           text-slate-500
                                           dark:text-slate-400"
                                >
                                    #{{ $index + 1 }}
                                </span>

                                <span
                                    class="inline-flex items-center gap-1
                                           rounded-full
                                           bg-blue-100 px-3 py-1
                                           text-sm font-black
                                           text-blue-700
                                           dark:bg-blue-950/50
                                           dark:text-blue-300"
                                >

                                    <span
                                        class="material-symbols-outlined
                                               text-[16px]"
                                    >
                                        thermostat
                                    </span>

                                    {{ $log->temperature ?? '-' }}

                                </span>

                            </div>


                            <div class="mb-3">

                                <p
                                    class="mb-1 text-[10px] font-bold
                                           uppercase tracking-wider
                                           text-slate-400"
                                >
                                    Catatan
                                </p>

                                <p
                                    class="text-sm leading-5
                                           text-slate-700
                                           dark:text-slate-300"
                                >
                                    {{ $log->note ?? '-' }}
                                </p>

                            </div>


                            <div
                                class="flex items-center gap-2
                                       border-t border-slate-200
                                       pt-3 text-xs
                                       text-slate-500
                                       dark:border-slate-700
                                       dark:text-slate-400"
                            >

                                <span
                                    class="material-symbols-outlined
                                           text-[16px]"
                                >
                                    schedule
                                </span>

                                {{
                                    optional(
                                        $log->created_at
                                    )->format('d-m-Y H:i')
                                    ?? '-'
                                }}

                            </div>

                        </div>

                    @empty

                        <div class="py-10 text-center">

                            <div
                                class="mx-auto flex h-12 w-12
                                       items-center justify-center
                                       rounded-full bg-blue-50
                                       text-blue-600
                                       dark:bg-blue-950/40
                                       dark:text-blue-400"
                            >
                                <span class="material-symbols-outlined">
                                    thermostat
                                </span>
                            </div>

                            <p
                                class="mt-3 text-sm font-semibold
                                       text-slate-700
                                       dark:text-slate-300"
                            >
                                Belum ada data monitoring
                            </p>

                            <p
                                class="mt-1 text-xs
                                       text-slate-500
                                       dark:text-slate-400"
                            >
                                Riwayat monitoring akan muncul di sini.
                            </p>

                        </div>

                    @endforelse

                </div>



                {{-- ========================================================
                    DESKTOP HISTORY TABLE
                ========================================================= --}}

                <div class="hidden overflow-x-auto sm:block">

                    <table class="min-w-full text-left">

                        <thead
                            class="bg-slate-50
                                   dark:bg-slate-950"
                        >

                            <tr>

                                <th
                                    class="whitespace-nowrap px-5 py-3
                                           text-[10px] font-bold
                                           uppercase tracking-wider
                                           text-slate-500
                                           dark:text-slate-400"
                                >
                                    No
                                </th>

                                <th
                                    class="whitespace-nowrap px-5 py-3
                                           text-[10px] font-bold
                                           uppercase tracking-wider
                                           text-slate-500
                                           dark:text-slate-400"
                                >
                                    Temperature
                                </th>

                                <th
                                    class="px-5 py-3
                                           text-[10px] font-bold
                                           uppercase tracking-wider
                                           text-slate-500
                                           dark:text-slate-400"
                                >
                                    Catatan
                                </th>

                                <th
                                    class="whitespace-nowrap px-5 py-3
                                           text-[10px] font-bold
                                           uppercase tracking-wider
                                           text-slate-500
                                           dark:text-slate-400"
                                >
                                    Waktu
                                </th>

                            </tr>

                        </thead>


                        <tbody
                            class="divide-y divide-slate-100
                                   dark:divide-slate-800"
                        >

                            @forelse(
                                $monitoringLogs
                                as $index => $log
                            )

                                <tr
                                    class="transition
                                           hover:bg-slate-50
                                           dark:hover:bg-slate-950"
                                >

                                    <td
                                        class="whitespace-nowrap px-5 py-4
                                               text-sm
                                               text-slate-700
                                               dark:text-slate-300"
                                    >
                                        {{ $index + 1 }}
                                    </td>


                                    <td
                                        class="whitespace-nowrap px-5 py-4"
                                    >

                                        <span
                                            class="inline-flex items-center
                                                   gap-1.5 rounded-full
                                                   bg-blue-100 px-3 py-1
                                                   text-xs font-black
                                                   text-blue-700
                                                   dark:bg-blue-950/50
                                                   dark:text-blue-300"
                                        >

                                            <span
                                                class="material-symbols-outlined
                                                       text-[15px]"
                                            >
                                                thermostat
                                            </span>

                                            {{ $log->temperature ?? '-' }}

                                        </span>

                                    </td>


                                    <td
                                        class="min-w-[250px] px-5 py-4
                                               text-sm
                                               text-slate-600
                                               dark:text-slate-300"
                                    >
                                        {{ $log->note ?? '-' }}
                                    </td>


                                    <td
                                        class="whitespace-nowrap px-5 py-4
                                               text-xs
                                               text-slate-500
                                               dark:text-slate-400"
                                    >

                                        <div class="flex items-center gap-1.5">

                                            <span
                                                class="material-symbols-outlined
                                                       text-[15px]"
                                            >
                                                schedule
                                            </span>

                                            {{
                                                optional(
                                                    $log->created_at
                                                )->format('d-m-Y H:i')
                                                ?? '-'
                                            }}

                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="4"
                                        class="px-5 py-12 text-center"
                                    >

                                        <div
                                            class="mx-auto flex h-12 w-12
                                                   items-center justify-center
                                                   rounded-full bg-blue-50
                                                   text-blue-600
                                                   dark:bg-blue-950/40
                                                   dark:text-blue-400"
                                        >

                                            <span
                                                class="material-symbols-outlined"
                                            >
                                                thermostat
                                            </span>

                                        </div>

                                        <p
                                            class="mt-3 text-sm font-semibold
                                                   text-slate-700
                                                   dark:text-slate-300"
                                        >
                                            Belum ada data monitoring
                                        </p>

                                        <p
                                            class="mt-1 text-xs
                                                   text-slate-500
                                                   dark:text-slate-400"
                                        >
                                            Riwayat monitoring akan muncul
                                            di sini.
                                        </p>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </section>

        @endif



        {{-- ============================================================
            EMPTY SEARCH
        ============================================================= --}}

        @if(
            $hasSearched
            && empty($operations)
            && !$selectedOperation
        )

            <section
                class="overflow-hidden rounded-2xl
                       border border-slate-200 bg-white
                       shadow-sm
                       dark:border-slate-800 dark:bg-slate-900"
            >

                <div class="px-5 py-14 text-center">

                    <div
                        class="mx-auto flex h-14 w-14
                               items-center justify-center
                               rounded-full bg-slate-100
                               text-slate-500
                               dark:bg-slate-800
                               dark:text-slate-400"
                    >

                        <span
                            class="material-symbols-outlined text-[28px]"
                        >
                            search_off
                        </span>

                    </div>

                    <p
                        class="mt-4 text-sm font-bold
                               text-slate-700
                               dark:text-slate-300"
                    >
                        Data container tidak ditemukan
                    </p>

                    <p
                        class="mt-1 text-xs
                               text-slate-500
                               dark:text-slate-400"
                    >
                        Silakan periksa kembali nomor container.
                    </p>

                </div>

            </section>

        @endif



        {{-- ============================================================
            FOOTER
        ============================================================= --}}

        <footer
            class="mt-6 border-t border-slate-200
                   py-5 text-center
                   dark:border-slate-800"
        >

            <p
                class="text-[11px] font-medium
                       text-slate-400 dark:text-slate-500"
            >
                MONITORING REEFER · PortOps Central
            </p>

        </footer>

    </div>

</div>
