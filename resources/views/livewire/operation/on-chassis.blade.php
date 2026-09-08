<div class="min-h-screen bg-slate-50 text-slate-900 antialiased dark:bg-slate-950 dark:text-white">

    <div class="mx-auto w-full max-w-6xl px-4 py-4 sm:px-6 sm:py-6">

        {{-- ============================================================
            HEADER
        ============================================================= --}}

        <header class="mb-5 flex items-center justify-between gap-3">

            <div class="flex min-w-0 items-center gap-3">

                <a
                    href="{{ route('home') }}"
                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl
                           border border-slate-200 bg-white text-slate-700 shadow-sm
                           transition hover:bg-slate-50
                           dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200
                           dark:hover:bg-slate-800"
                    aria-label="Kembali ke dashboard"
                >

                    <span class="material-symbols-outlined">
                        arrow_back
                    </span>

                </a>


                <div class="min-w-0">

                    <div class="flex items-center gap-2">

                        <span
                            class="material-symbols-outlined
                                   text-[22px] text-blue-600
                                   dark:text-blue-400"
                        >
                            directions_car
                        </span>


                        <h1
                            class="truncate text-lg font-black
                                   tracking-tight sm:text-xl"
                        >
                            ON CHASSIS
                        </h1>

                    </div>


                    <p
                        class="mt-0.5 truncate text-xs
                               text-slate-500 dark:text-slate-400"
                    >
                        Proses container untuk ditempatkan pada chassis
                    </p>

                </div>

            </div>


            <span
                class="hidden shrink-0 rounded-full
                       bg-blue-50 px-3 py-1.5
                       text-[10px] font-bold
                       uppercase tracking-wider
                       text-blue-700
                       sm:inline-flex
                       dark:bg-blue-950/50
                       dark:text-blue-300"
            >
                ON CHASSIS
            </span>

        </header>



        {{-- ============================================================
            SEARCH
        ============================================================= --}}

        <section
            class="mb-5 rounded-2xl
                   border border-slate-200
                   bg-white p-4 shadow-sm
                   sm:p-5
                   dark:border-slate-800
                   dark:bg-slate-900"
        >

            <div class="mb-4 flex items-center gap-3">

                <div
                    class="flex h-11 w-11 shrink-0
                           items-center justify-center
                           rounded-xl
                           bg-blue-50 text-blue-600
                           dark:bg-blue-950/50
                           dark:text-blue-400"
                >

                    <span class="material-symbols-outlined">
                        search
                    </span>

                </div>


                <div class="min-w-0">

                    <h2 class="text-sm font-bold sm:text-base">
                        Search No Container
                    </h2>


                    <p
                        class="text-xs
                               text-slate-500
                               dark:text-slate-400"
                    >
                        Masukkan nomor container untuk mencari data.
                    </p>

                </div>

            </div>


            <form wire:submit="search">

                <div
                    class="flex flex-col gap-3
                           sm:flex-row sm:items-end"
                >

                    {{-- INPUT --}}

                    <div class="w-full">

                        <label
                            for="searchCont"
                            class="mb-2 block
                                   text-[11px]
                                   font-bold uppercase
                                   tracking-wider
                                   text-slate-600
                                   dark:text-slate-300"
                        >
                            No Container
                        </label>


                        <input
                            id="searchCont"
                            type="text"
                            wire:model="searchCont"
                            autofocus
                            autocomplete="off"
                            placeholder="SEARCH NO CONT"
                            class="h-12 w-full rounded-xl
                                   border border-slate-300
                                   bg-white px-4
                                   text-sm font-semibold
                                   uppercase outline-none
                                   transition
                                   focus:border-blue-600
                                   focus:ring-2
                                   focus:ring-blue-100
                                   dark:border-slate-700
                                   dark:bg-slate-950
                                   dark:text-white
                                   dark:focus:border-blue-500
                                   dark:focus:ring-blue-950"
                        >


                        @error('searchCont')

                            <p
                                class="mt-2 text-xs
                                       font-medium
                                       text-red-600
                                       dark:text-red-400"
                            >
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- SEARCH BUTTON --}}

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="inline-flex h-12 w-full
                               shrink-0 items-center
                               justify-center gap-2
                               rounded-xl bg-blue-600
                               px-5 text-sm font-bold
                               text-white transition
                               hover:bg-blue-700
                               disabled:cursor-not-allowed
                               disabled:opacity-50
                               sm:w-auto"
                    >

                        <span
                            class="material-symbols-outlined text-[20px]"
                            wire:loading.remove
                            wire:target="search"
                        >
                            search
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


                    {{-- RESET --}}

                    @if(
                        $searchCont !== ''
                        || $hasSearched
                    )

                        <button
                            type="button"
                            wire:click="resetSearch"
                            class="inline-flex h-12 w-full
                                   shrink-0 items-center
                                   justify-center gap-2
                                   rounded-xl border
                                   border-slate-300
                                   bg-white px-5
                                   text-sm font-bold
                                   text-slate-700 transition
                                   hover:bg-slate-50
                                   dark:border-slate-700
                                   dark:bg-slate-950
                                   dark:text-slate-200
                                   dark:hover:bg-slate-800
                                   sm:w-auto"
                        >

                            <span
                                class="material-symbols-outlined
                                       text-[20px]"
                            >
                                refresh
                            </span>

                            RESET

                        </button>

                    @endif

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
                        border-red-200
                        bg-red-50
                        text-red-800
                        dark:border-red-900/50
                        dark:bg-red-950/40
                        dark:text-red-300

                    @elseif($messageType === 'success')
                        border-green-200
                        bg-green-50
                        text-green-800
                        dark:border-green-900/50
                        dark:bg-green-950/40
                        dark:text-green-300

                    @else
                        border-blue-200
                        bg-blue-50
                        text-blue-800
                        dark:border-blue-900/50
                        dark:bg-blue-950/40
                        dark:text-blue-300
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


                        <p
                            class="text-sm
                                   font-semibold
                                   leading-5"
                        >
                            {{ $message }}
                        </p>

                    </div>

                </div>

            @endif

        @endisset



        {{-- ============================================================
            DATA CONTAINER
        ============================================================= --}}

        <section
            class="overflow-hidden rounded-2xl
                   border border-slate-200
                   bg-white shadow-sm
                   dark:border-slate-800
                   dark:bg-slate-900"
        >

            {{-- HEADER --}}

            <div
                class="border-b border-slate-200
                       p-4
                       dark:border-slate-800
                       sm:p-5"
            >

                <div class="flex items-center justify-between gap-3">

                    <div class="flex min-w-0 items-center gap-3">

                        <div
                            class="flex h-11 w-11 shrink-0
                                   items-center justify-center
                                   rounded-xl
                                   bg-blue-50
                                   text-blue-600
                                   dark:bg-blue-950/50
                                   dark:text-blue-400"
                        >

                            <span class="material-symbols-outlined">
                                inventory_2
                            </span>

                        </div>


                        <div class="min-w-0">

                            <h2
                                class="text-sm font-bold
                                       sm:text-base"
                            >
                                Data Container
                            </h2>


                            <p
                                class="mt-0.5 text-xs
                                       text-slate-500
                                       dark:text-slate-400"
                            >
                                Container tersedia untuk ON CHASSIS.
                            </p>

                        </div>

                    </div>


                    {{-- TOTAL --}}

                    <div
                        class="shrink-0 rounded-xl
                               bg-blue-50 px-3 py-2
                               text-center
                               dark:bg-blue-950/50"
                    >

                        <p
                            class="text-[9px]
                                   font-bold uppercase
                                   tracking-wider
                                   text-slate-500
                                   dark:text-slate-400"
                        >
                            Total
                        </p>


                        <p
                            class="text-lg font-black
                                   leading-5 text-blue-600
                                   dark:text-blue-400"
                        >
                            {{ count($operations ?? []) }}
                        </p>

                    </div>

                </div>

            </div>



            {{-- ========================================================
                MOBILE / DESKTOP CARDS
            ========================================================= --}}

            <div class="grid grid-cols-1 gap-3 p-4 md:grid-cols-2 sm:p-5">

                @forelse(
                    $operations ?? []
                    as $operation
                )

                    @php

                        $container =
                            $operation->container;

                        /*
                        |--------------------------------------------------------------------------
                        | NO CONTAINER
                        |--------------------------------------------------------------------------
                        */

                        $noContainer =
                            $container?->no_cont
                            ?? '-';


                        /*
                        |--------------------------------------------------------------------------
                        | UKURAN
                        |--------------------------------------------------------------------------
                        */

                        $ukuran =
                            $container?->ukr_cont
                            ?? $container?->ukuran
                            ?? $container?->size
                            ?? $container?->type?->name
                            ?? '-';


                        /*
                        |--------------------------------------------------------------------------
                        | NO TRUCK
                        |--------------------------------------------------------------------------
                        */

                        $noTruck =
                            $operation
                                ->chassis
                                ?->no_truck
                            ?? $operation
                                ->chassis
                                ?->truck_no
                            ?? $operation
                                ->no_truck
                            ?? $operation
                                ->truck_no
                            ?? '-';


                        /*
                        |--------------------------------------------------------------------------
                        | LOKASI
                        |--------------------------------------------------------------------------
                        */

                        $location =
                            $container
                                ?->currentLocation;


                        if ($location) {

                            if (
                                !empty(
                                    $location->location_code
                                )
                            ) {

                                $lokasi =
                                    $location
                                        ->location_code;

                            } else {

                                $lokasi =
                                    trim(
                                        ($location->block ?? '')
                                        .
                                        ($location->slot ?? '')
                                    );


                                if (
                                    !empty(
                                        $location->tier
                                    )
                                ) {

                                    $lokasi .=
                                        '0'
                                        .
                                        $location->tier;

                                }


                                if (
                                    $lokasi === ''
                                ) {

                                    $lokasi = '-';

                                }

                            }

                        } else {

                            $lokasi = '-';

                        }

                    @endphp


                    {{-- =================================================
                        CONTAINER CARD
                    ================================================== --}}

                    <article
                        wire:key="on-chassis-{{ $operation->id }}"
                        class="overflow-hidden rounded-2xl
                               border border-slate-200
                               bg-white transition
                               hover:border-blue-300
                               dark:border-slate-700
                               dark:bg-slate-950
                               dark:hover:border-blue-700"
                    >

                        {{-- CARD HEADER --}}

                        <div
                            class="flex items-start
                                   justify-between gap-3
                                   border-b
                                   border-slate-200
                                   p-4
                                   dark:border-slate-800"
                        >

                            <div class="min-w-0">

                                <p
                                    class="mb-1 text-[10px]
                                           font-bold uppercase
                                           tracking-wider
                                           text-slate-500
                                           dark:text-slate-400"
                                >
                                    No Container
                                </p>


                                <p
                                    class="break-all text-xl
                                           font-black tracking-tight
                                           text-slate-900
                                           dark:text-white"
                                >
                                    {{ $noContainer }}
                                </p>

                            </div>


                            <span
                                class="inline-flex shrink-0
                                       items-center gap-1.5
                                       rounded-full
                                       bg-blue-50 px-2.5 py-1.5
                                       text-[9px] font-bold
                                       uppercase tracking-wider
                                       text-blue-700
                                       dark:bg-blue-950/50
                                       dark:text-blue-300"
                            >

                                <span
                                    class="h-1.5 w-1.5 rounded-full
                                           bg-blue-600
                                           dark:bg-blue-400"
                                ></span>

                                READY

                            </span>

                        </div>



                        {{-- CARD DATA --}}

                        <div class="grid grid-cols-2 gap-3 p-4">

                            {{-- UKURAN --}}

                            <div
                                class="rounded-xl
                                       bg-slate-50 p-3
                                       dark:bg-slate-900"
                            >

                                <p
                                    class="text-[9px]
                                           font-bold uppercase
                                           tracking-wider
                                           text-slate-500
                                           dark:text-slate-400"
                                >
                                    Ukuran
                                </p>


                                <p
                                    class="mt-1 break-words
                                           text-sm font-bold
                                           text-slate-800
                                           dark:text-slate-100"
                                >
                                    {{ $ukuran }}
                                </p>

                            </div>


                            {{-- NO TRUCK --}}

                            <div
                                class="rounded-xl
                                       bg-slate-50 p-3
                                       dark:bg-slate-900"
                            >

                                <p
                                    class="text-[9px]
                                           font-bold uppercase
                                           tracking-wider
                                           text-slate-500
                                           dark:text-slate-400"
                                >
                                    No Truck
                                </p>


                                <p
                                    class="mt-1 break-words
                                           text-sm font-bold
                                           text-slate-800
                                           dark:text-slate-100"
                                >
                                    {{ $noTruck }}
                                </p>

                            </div>


                            {{-- LOKASI --}}

                            <div
                                class="col-span-2 rounded-xl
                                       bg-slate-50 p-3
                                       dark:bg-slate-900"
                            >

                                <p
                                    class="text-[9px]
                                           font-bold uppercase
                                           tracking-wider
                                           text-slate-500
                                           dark:text-slate-400"
                                >
                                    Lokasi
                                </p>


                                <div
                                    class="mt-1 flex items-center gap-2"
                                >

                                    <span
                                        class="material-symbols-outlined
                                               text-[18px]
                                               text-blue-600
                                               dark:text-blue-400"
                                    >
                                        location_on
                                    </span>


                                    <p
                                        class="break-words
                                               text-sm font-bold
                                               text-slate-800
                                               dark:text-slate-100"
                                    >
                                        {{ $lokasi }}
                                    </p>

                                </div>

                            </div>

                        </div>



                        {{-- ACTION --}}

                        <div
                            class="border-t
                                   border-slate-200
                                   p-4
                                   dark:border-slate-800"
                        >

                            <button
                                type="button"
                                wire:click="onChassis({{ $operation->id }})"
                                wire:loading.attr="disabled"
                                wire:target="onChassis({{ $operation->id }})"
                                class="inline-flex h-12 w-full
                                       items-center
                                       justify-center gap-2
                                       rounded-xl
                                       bg-blue-600
                                       px-5
                                       text-sm font-black
                                       text-white transition
                                       hover:bg-blue-700
                                       disabled:cursor-not-allowed
                                       disabled:opacity-50"
                            >

                                <span
                                    class="material-symbols-outlined
                                           text-[21px]"
                                    wire:loading.remove
                                    wire:target="onChassis({{ $operation->id }})"
                                >
                                    directions_car
                                </span>


                                <span
                                    wire:loading.remove
                                    wire:target="onChassis({{ $operation->id }})"
                                >
                                    ON CHASSIS
                                </span>


                                <span
                                    wire:loading
                                    wire:target="onChassis({{ $operation->id }})"
                                >
                                    PROCESSING...
                                </span>

                            </button>

                        </div>

                    </article>

                @empty

                    {{-- =================================================
                        EMPTY
                    ================================================== --}}

                    <div
                        class="col-span-1 py-14
                               text-center md:col-span-2"
                    >

                        <div
                            class="mx-auto flex h-16 w-16
                                   items-center justify-center
                                   rounded-full
                                   bg-slate-100
                                   text-slate-500
                                   dark:bg-slate-800
                                   dark:text-slate-400"
                        >

                            <span
                                class="material-symbols-outlined
                                       text-[30px]"
                            >
                                directions_car
                            </span>

                        </div>


                        <p
                            class="mt-4 text-sm font-bold
                                   text-slate-700
                                   dark:text-slate-200"
                        >
                            Belum ada data container
                        </p>


                        <p
                            class="mt-1 text-xs
                                   text-slate-500
                                   dark:text-slate-400"
                        >
                            Data container akan muncul
                            setelah pencarian dilakukan.
                        </p>

                    </div>

                @endforelse

            </div>

        </section>



        {{-- ============================================================
            SELECTED OPERATION
        ============================================================= --}}

        @if($selectedOperation)

            <section
                class="mt-5 overflow-hidden
                       rounded-2xl
                       border border-slate-200
                       bg-white shadow-sm
                       dark:border-slate-800
                       dark:bg-slate-900"
            >

                {{-- HEADER --}}

                <div
                    class="border-b
                           border-slate-200
                           p-4
                           dark:border-slate-800
                           sm:p-5"
                >

                    <div
                        class="flex flex-col gap-3
                               sm:flex-row
                               sm:items-center
                               sm:justify-between"
                    >

                        <div class="min-w-0">

                            <div class="mb-2 flex items-center gap-2">

                                <span
                                    class="material-symbols-outlined
                                           text-[21px]
                                           text-blue-600
                                           dark:text-blue-400"
                                >
                                    directions_car
                                </span>


                                <span
                                    class="text-[10px]
                                           font-bold uppercase
                                           tracking-wider
                                           text-slate-500
                                           dark:text-slate-400"
                                >
                                    Container Terpilih
                                </span>

                            </div>


                            <h2
                                class="break-all text-2xl
                                       font-black tracking-tight
                                       text-slate-900
                                       dark:text-white
                                       sm:text-3xl"
                            >
                                {{
                                    $selectedOperation
                                        ->container
                                        ?->no_cont
                                    ?? '-'
                                }}
                            </h2>

                        </div>


                        <span
                            class="inline-flex w-fit
                                   items-center gap-1.5
                                   rounded-full
                                   bg-blue-50
                                   px-3 py-1.5
                                   text-[10px] font-bold
                                   uppercase tracking-wider
                                   text-blue-700
                                   dark:bg-blue-950/50
                                   dark:text-blue-300"
                        >

                            <span
                                class="h-1.5 w-1.5
                                       rounded-full
                                       bg-blue-600
                                       dark:bg-blue-400"
                            ></span>

                            ON CHASSIS

                        </span>

                    </div>

                </div>



                {{-- DETAIL --}}

                <div
                    class="grid grid-cols-1 gap-4
                           p-4 sm:grid-cols-2
                           sm:p-5"
                >

                    {{-- NO CONTAINER --}}

                    <div>

                        <label
                            class="mb-2 block
                                   text-[11px]
                                   font-bold uppercase
                                   tracking-wider
                                   text-slate-600
                                   dark:text-slate-300"
                        >
                            No Container
                        </label>


                        <input
                            type="text"
                            readonly
                            value="{{
                                $selectedOperation
                                    ->container
                                    ?->no_cont
                                ?? '-'
                            }}"
                            class="h-12 w-full rounded-xl
                                   border border-slate-200
                                   bg-slate-100 px-4
                                   text-sm font-black
                                   uppercase text-slate-800
                                   outline-none
                                   dark:border-slate-700
                                   dark:bg-slate-800
                                   dark:text-white"
                        >

                    </div>



                    {{-- SPK --}}

                    <div>

                        <label
                            class="mb-2 block
                                   text-[11px]
                                   font-bold uppercase
                                   tracking-wider
                                   text-slate-600
                                   dark:text-slate-300"
                        >
                            SPK
                        </label>


                        <input
                            type="text"
                            readonly
                            value="{{
                                $selectedOperation
                                    ->spk
                                    ?->no_spk
                                ?? '-'
                            }}"
                            class="h-12 w-full rounded-xl
                                   border border-slate-200
                                   bg-slate-100 px-4
                                   text-sm font-bold
                                   text-slate-800
                                   outline-none
                                   dark:border-slate-700
                                   dark:bg-slate-800
                                   dark:text-white"
                        >

                    </div>



                    {{-- CURRENT PROCESS --}}

                    <div>

                        <label
                            class="mb-2 block
                                   text-[11px]
                                   font-bold uppercase
                                   tracking-wider
                                   text-slate-600
                                   dark:text-slate-300"
                        >
                            Current Process
                        </label>


                        <input
                            type="text"
                            readonly
                            value="{{
                                $selectedOperation
                                    ->current_process
                                ?? '-'
                            }}"
                            class="h-12 w-full rounded-xl
                                   border border-slate-200
                                   bg-slate-100 px-4
                                   text-sm font-bold
                                   uppercase text-slate-800
                                   outline-none
                                   dark:border-slate-700
                                   dark:bg-slate-800
                                   dark:text-white"
                        >

                    </div>



                    {{-- STATUS --}}

                    <div>

                        <label
                            class="mb-2 block
                                   text-[11px]
                                   font-bold uppercase
                                   tracking-wider
                                   text-slate-600
                                   dark:text-slate-300"
                        >
                            Status
                        </label>


                        <input
                            type="text"
                            readonly
                            value="{{
                                $selectedOperation
                                    ->status
                                ?? '-'
                            }}"
                            class="h-12 w-full rounded-xl
                                   border border-slate-200
                                   bg-slate-100 px-4
                                   text-sm font-bold
                                   uppercase text-slate-800
                                   outline-none
                                   dark:border-slate-700
                                   dark:bg-slate-800
                                   dark:text-white"
                        >

                    </div>

                </div>

            </section>

        @endif



        {{-- ============================================================
            FOOTER
        ============================================================= --}}

        <footer
            class="mt-6 border-t
                   border-slate-200
                   py-5 text-center
                   dark:border-slate-800"
        >

            <p
                class="text-[11px]
                       text-slate-500
                       dark:text-slate-500"
            >
                ON CHASSIS · PortOps Central
            </p>

        </footer>

    </div>

</div>
