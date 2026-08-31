@props(['message' => null, 'messageType' => null])

<div class="min-h-screen bg-[#f8f9ff] text-[#0b1c30] antialiased">

    {{-- ============================================================
        SIDEBAR
    ============================================================= --}}

    <aside
        class="fixed left-0 top-0 z-40 hidden h-screen w-[260px]
               flex-col overflow-y-auto
               border-r border-[#c3c6d7]/20
               bg-[#213145] md:flex"
    >

        {{-- BRAND --}}

        <div
            class="border-b border-[#c3c6d7]/10 p-6"
        >

            <h1 class="text-base font-bold text-[#dbe1ff]">
                PortOps Central
            </h1>

            <p class="text-[13px] text-[#bec6e0]">
                Terminal A-101
            </p>

        </div>


        {{-- NAVIGATION --}}

        <nav class="flex flex-1 flex-col py-4">

            {{-- DASHBOARD --}}

            <a
                href="{{ route('home') }}"
                class="flex items-center gap-4
                       border-l-4 border-transparent
                       px-6 py-3
                       text-[#bec6e0]/70
                       hover:bg-[#d3e4fe]/10"
            >

                <span class="material-symbols-outlined">
                    dashboard
                </span>

                <span
                    class="text-xs
                           font-semibold
                           tracking-wider"
                >
                    Dashboard
                </span>

            </a>


            {{-- SECTION --}}

            <div
                class="px-6 pb-2 pt-4
                       text-[10px]
                       font-semibold
                       uppercase
                       tracking-widest
                       text-[#bec6e0]/50"
            >
                Operations
            </div>


            {{-- PICKUP --}}

            <a
                href="{{ route('operation.pickup') }}"
                class="flex items-center gap-4
                       border-l-4 border-transparent
                       px-6 py-3
                       text-[#bec6e0]/70
                       hover:bg-[#d3e4fe]/10"
            >

                <span class="material-symbols-outlined">
                    local_shipping
                </span>

                <span
                    class="text-xs
                           font-semibold
                           tracking-wider"
                >
                    PICKUP
                </span>

            </a>


            {{-- BEHANDLE IN --}}

            <a
                href="{{ route('operation.behandle-in') }}"
                class="flex items-center gap-4
                       border-l-4 border-transparent
                       px-6 py-3
                       text-[#bec6e0]/70
                       hover:bg-[#d3e4fe]/10"
            >

                <span class="material-symbols-outlined">
                    move_to_inbox
                </span>

                <span
                    class="text-xs
                           font-semibold
                           tracking-wider"
                >
                    BEHANDLE IN
                </span>

            </a>


            {{-- HOLD --}}

            <a
                href="{{ route('operation.hold') }}"
                class="flex items-center gap-4
                       border-l-4 border-transparent
                       px-6 py-3
                       text-[#bec6e0]/70
                       hover:bg-[#d3e4fe]/10"
            >

                <span class="material-symbols-outlined">
                    front_hand
                </span>

                <span
                    class="text-xs
                           font-semibold
                           tracking-wider"
                >
                    HOLD
                </span>

            </a>


            {{-- MARSHALLING CIC --}}

            <a
                href="{{ route('operation.marshallingcic') }}"
                class="flex items-center gap-4
                       border-l-4 border-transparent
                       px-6 py-3
                       text-[#bec6e0]/70
                       hover:bg-[#d3e4fe]/10"
            >

                <span class="material-symbols-outlined">
                    warehouse
                </span>

                <span
                    class="text-xs
                           font-semibold
                           tracking-wider"
                >
                    MARSHALLING CIC
                </span>

            </a>


            {{-- MARSHALLING YARD --}}

            <a
                href="{{ route('operation.marshalling-yard') }}"
                class="flex items-center gap-4
                       border-l-4 border-transparent
                       px-6 py-3
                       text-[#bec6e0]/70
                       hover:bg-[#d3e4fe]/10"
            >

                <span class="material-symbols-outlined">
                    location_on
                </span>

                <span
                    class="text-xs
                           font-semibold
                           tracking-wider"
                >
                    MARSHALLING YARD
                </span>

            </a>


            {{-- INSPECTION --}}

            <a
                href="{{ route('operation.inspection') }}"
                class="flex items-center gap-4
                       border-l-4 border-transparent
                       px-6 py-3
                       text-[#bec6e0]/70
                       hover:bg-[#d3e4fe]/10"
            >

                <span class="material-symbols-outlined">
                    fact_check
                </span>

                <span
                    class="text-xs
                           font-semibold
                           tracking-wider"
                >
                    INSPECTION
                </span>

            </a>


            {{-- PLUG REEFER --}}

            <a
                href="{{ route('operation.plug-reefer') }}"
                class="flex items-center gap-4
                       border-l-4 border-transparent
                       px-6 py-3
                       text-[#bec6e0]/70
                       hover:bg-[#d3e4fe]/10"
            >

                <span class="material-symbols-outlined">
                    ac_unit
                </span>

                <span
                    class="text-xs
                           font-semibold
                           tracking-wider"
                >
                    PLUG REEFER
                </span>

            </a>


            {{-- MONITORING REEFER --}}

            <a
                href="{{ route('operation.monitoring-reefer') }}"
                class="flex items-center gap-4
                       border-l-4 border-transparent
                       px-6 py-3
                       text-[#bec6e0]/70
                       hover:bg-[#d3e4fe]/10"
            >

                <span class="material-symbols-outlined">
                    thermostat
                </span>

                <span
                    class="text-xs
                           font-semibold
                           tracking-wider"
                >
                    MONITORING REEFER
                </span>

            </a>


            {{-- DELIVERY --}}

            <a
                href="{{ route('operation.delivery') }}"
                class="flex items-center gap-4
                       border-l-4 border-transparent
                       px-6 py-3
                       text-[#bec6e0]/70
                       hover:bg-[#d3e4fe]/10"
            >

                <span class="material-symbols-outlined">
                    local_shipping
                </span>

                <span
                    class="text-xs
                           font-semibold
                           tracking-wider"
                >
                    DELIVERY
                </span>

            </a>


            {{-- INSPECTION OUT --}}

            <a
                href="{{ route('operation.inspection-out') }}"
                class="flex items-center gap-4
                       border-l-4 border-transparent
                       px-6 py-3
                       text-[#bec6e0]/70
                       hover:bg-[#d3e4fe]/10"
            >

                <span class="material-symbols-outlined">
                    fact_check
                </span>

                <span
                    class="text-xs
                           font-semibold
                           tracking-wider"
                >
                    INSPECTION OUT
                </span>

            </a>


            {{-- =====================================================
                ACTIVE ON CHASSIS
            ====================================================== --}}

            <a
                href="{{ route('operation.on-chassis') }}"
                class="flex items-center gap-4
                       border-l-4
                       border-[#004ac6]
                       bg-[#2563eb]/10
                       px-6 py-3
                       font-bold
                       text-[#dbe1ff]"
            >

                <span
                    class="material-symbols-outlined"
                    style="font-variation-settings:'FILL' 1;"
                >
                    directions_car
                </span>

                <span
                    class="text-xs
                           font-semibold
                           tracking-wider"
                >
                    ON CHASSIS
                </span>

            </a>


            {{-- COPY YARD --}}

            <a
                href="{{ route('operation.copy-yard') }}"
                class="flex items-center gap-4
                       border-l-4 border-transparent
                       px-6 py-3
                       text-[#bec6e0]/70
                       hover:bg-[#d3e4fe]/10"
            >

                <span class="material-symbols-outlined">
                    content_copy
                </span>

                <span
                    class="text-xs
                           font-semibold
                           tracking-wider"
                >
                    COPY YARD
                </span>

            </a>


            {{-- LOGOUT --}}

            <div class="mt-auto">

                <a
                    href="#"
                    class="flex items-center gap-4
                           border-l-4 border-transparent
                           px-6 py-3
                           text-[#bec6e0]/70
                           hover:bg-[#d3e4fe]/10"
                >

                    <span class="material-symbols-outlined">
                        logout
                    </span>

                    <span class="text-xs font-semibold tracking-wider">
                        Logout
                    </span>

                </a>

            </div>

        </nav>

    </aside>



    {{-- ============================================================
        MAIN
    ============================================================= --}}

    <div class="min-h-screen md:ml-[260px]">


        {{-- ========================================================
            HEADER
        ========================================================= --}}

        <header
            class="sticky top-0 z-30
                   flex h-14 items-center
                   justify-between
                   border-b
                   border-[#c3c6d7]/30
                   bg-[#f8f9ff]
                   px-4 sm:px-6"
        >

            <h1
                class="text-base
                       font-black
                       text-[#0b1c30]
                       md:hidden"
            >
                PortOps Central
            </h1>


            <div class="hidden sm:block">

                <div class="relative">

                    <span
                        class="material-symbols-outlined
                               absolute left-2 top-1/2
                               -translate-y-1/2
                               text-[18px]
                               text-[#737686]"
                    >
                        search
                    </span>


                    <input
                        type="text"
                        placeholder="Search operations..."
                        class="w-64 rounded
                               border
                               border-[#c3c6d7]/50
                               bg-[#eff4ff]
                               py-1 pl-8 pr-4
                               text-[13px]
                               outline-none
                               focus:border-[#004ac6]"
                    >

                </div>

            </div>


            <div class="flex items-center gap-3">

                <span
                    class="material-symbols-outlined
                           text-[#434655]"
                >
                    notifications
                </span>

                <div
                    class="h-6 w-px
                           bg-[#c3c6d7]/30"
                ></div>

                <div
                    class="flex h-8 w-8
                           items-center
                           justify-center
                           rounded-full
                           bg-[#2563eb]"
                >

                    <span
                        class="material-symbols-outlined
                               text-white"
                    >
                        person
                    </span>

                </div>

            </div>

        </header>



        {{-- ========================================================
            CONTENT
        ========================================================= --}}

        <main
            class="min-h-[calc(100vh-56px)]
                   p-4 sm:p-6"
        >


            {{-- ====================================================
                TITLE
            ===================================================== --}}

            <div class="mb-6">

                <div class="flex items-center gap-2">

                    <span
                        class="material-symbols-outlined
                               text-[24px]
                               text-[#004ac6]"
                    >
                        directions_car
                    </span>


                    <h1
                        class="text-2xl
                               font-semibold
                               tracking-tight"
                    >
                        ON CHASSIS
                    </h1>

                </div>


                <p
                    class="mt-2 text-sm
                           text-[#434655]"
                >
                    Proses container untuk ditempatkan pada chassis.
                </p>

            </div>



            {{-- ====================================================
                SEARCH CARD
            ===================================================== --}}

            <div
                class="mb-6 rounded-xl
                       border
                       border-[#c3c6d7]/30
                       bg-white
                       p-5
                       shadow-sm sm:p-6"
            >

                <div
                    class="mb-5 flex
                           items-center gap-3"
                >

                    <div
                        class="flex h-10 w-10
                               items-center
                               justify-center
                               rounded-lg
                               bg-[#d3e4fe]
                               text-[#004ac6]"
                    >

                        <span class="material-symbols-outlined">
                            search
                        </span>

                    </div>


                    <div>

                        <h2
                            class="text-base
                                   font-semibold"
                        >
                            Search No Container
                        </h2>


                        <p
                            class="text-xs
                                   text-[#737686]"
                        >
                            Masukkan nomor container untuk mencari data.
                        </p>

                    </div>

                </div>


                <form wire:submit="search">

                    <div
                        class="flex flex-col
                               gap-3
                               sm:flex-row
                               sm:items-end"
                    >

                        <div
                            class="w-full
                                   sm:max-w-md"
                        >

                            <label
                                for="searchCont"
                                class="mb-2 block
                                       text-xs
                                       font-semibold
                                       uppercase
                                       tracking-wider
                                       text-[#434655]"
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
                                class="w-full rounded-lg
                                       border
                                       border-[#c3c6d7]
                                       bg-white
                                       px-4 py-2.5
                                       text-sm
                                       outline-none
                                       focus:border-[#004ac6]
                                       focus:ring-2
                                       focus:ring-[#b4c5ff]"
                            >


                            @error('searchCont')

                                <p
                                    class="mt-2 text-xs
                                           text-[#ba1a1a]"
                                >
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>


                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="inline-flex
                                   items-center
                                   justify-center
                                   gap-2
                                   rounded-lg
                                   bg-[#004ac6]
                                   px-5 py-2.5
                                   text-sm
                                   font-semibold
                                   text-white
                                   hover:bg-[#003ea8]
                                   disabled:opacity-50"
                        >

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
                            || $hasSearched
                        )

                            <button
                                type="button"
                                wire:click="resetSearch"
                                class="inline-flex
                                       items-center
                                       justify-center
                                       gap-2
                                       rounded-lg
                                       border
                                       border-[#c3c6d7]
                                       bg-white
                                       px-5 py-2.5
                                       text-sm
                                       font-semibold
                                       text-[#434655]
                                       hover:bg-[#eff4ff]"
                            >

                                <span
                                    class="material-symbols-outlined
                                           text-[18px]"
                                >
                                    refresh
                                </span>

                                RESET

                            </button>

                        @endif

                    </div>

                </form>

            </div>



            {{-- ====================================================
                MESSAGE
            ===================================================== --}}

            @isset($message)
            @if($message)

                <div
                    class="mb-6 rounded-lg
                           border px-4 py-3

                           @if($messageType === 'danger')
                               border-[#ba1a1a]/30
                               bg-[#ffdad6]
                               text-[#93000a]
                           @elseif($messageType === 'success')
                               border-[#146c2e]/30
                               bg-[#d9f7df]
                               text-[#146c2e]
                           @else
                               border-[#004ac6]/20
                               bg-[#d3e4fe]
                               text-[#003ea8]
                           @endif"
                >

                    <div class="flex items-center gap-3">

                        <span class="material-symbols-outlined">

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
                                   font-semibold"
                        >
                            {{ $message }}
                        </p>

                    </div>

                </div>

            @endif
            @endisset



            {{-- ====================================================
                DATA TABLE
            ===================================================== --}}

            <div
                class="overflow-hidden
                       rounded-xl
                       border
                       border-[#c3c6d7]/30
                       bg-white
                       shadow-sm"
            >

                {{-- HEADER TABLE --}}

                <div
                    class="border-b
                           border-[#c3c6d7]/30
                           px-5 py-4
                           sm:px-6"
                >

                    <div
                        class="flex items-center
                               justify-between"
                    >

                        <div>

                            <h2
                                class="text-base
                                       font-semibold"
                            >
                                Data Container
                            </h2>


                            <p
                                class="mt-1 text-xs
                                       text-[#737686]"
                            >
                                Container yang tersedia untuk proses ON CHASSIS.
                            </p>

                        </div>


                        <div
                            class="rounded-lg
                                   bg-[#eff4ff]
                                   px-4 py-2
                                   text-center"
                        >

                            <p
                                class="text-[10px]
                                       font-semibold
                                       uppercase
                                       tracking-wider
                                       text-[#737686]"
                            >
                                Total
                            </p>


                            <p
                                class="text-lg
                                       font-bold
                                       text-[#004ac6]"
                            >
                                {{ count($operations ?? []) }}
                            </p>

                        </div>

                    </div>

                </div>



                {{-- =================================================
                    TABLE
                ================================================== --}}

                <div class="overflow-x-auto">

                    <table
                        class="min-w-full
                               text-left"
                    >

                        <thead
                            class="bg-[#eff4ff]"
                        >

                            <tr>

                                <th
                                    class="whitespace-nowrap
                                           px-5 py-3
                                           text-[10px]
                                           font-bold
                                           uppercase
                                           tracking-wider
                                           text-[#434655]"
                                >
                                    No Kontainer
                                </th>


                                <th
                                    class="whitespace-nowrap
                                           px-5 py-3
                                           text-[10px]
                                           font-bold
                                           uppercase
                                           tracking-wider
                                           text-[#434655]"
                                >
                                    Ukuran
                                </th>


                                <th
                                    class="whitespace-nowrap
                                           px-5 py-3
                                           text-[10px]
                                           font-bold
                                           uppercase
                                           tracking-wider
                                           text-[#434655]"
                                >
                                    No Truck
                                </th>


                                <th
                                    class="whitespace-nowrap
                                           px-5 py-3
                                           text-[10px]
                                           font-bold
                                           uppercase
                                           tracking-wider
                                           text-[#434655]"
                                >
                                    Lokasi
                                </th>


                                <th
                                    class="whitespace-nowrap
                                           px-5 py-3
                                           text-[10px]
                                           font-bold
                                           uppercase
                                           tracking-wider
                                           text-[#434655]"
                                >
                                    Proses
                                </th>

                            </tr>

                        </thead>



                        <tbody
                            class="divide-y
                                   divide-[#c3c6d7]/20"
                        >

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


                                <tr
                                    class="hover:bg-[#f8f9ff]"
                                >

                                    {{-- NO CONTAINER --}}

                                    <td
                                        class="whitespace-nowrap
                                               px-5 py-4"
                                    >

                                        <input
                                            type="text"
                                            readonly
                                            value="{{ $noContainer }}"
                                            class="w-full
                                                   min-w-[160px]
                                                   rounded-lg
                                                   border
                                                   border-[#c3c6d7]
                                                   bg-[#eff4ff]
                                                   px-3 py-2
                                                   text-sm
                                                   font-semibold
                                                   text-[#0b1c30]"
                                        >

                                    </td>


                                    {{-- UKURAN --}}

                                    <td
                                        class="whitespace-nowrap
                                               px-5 py-4"
                                    >

                                        <input
                                            type="text"
                                            readonly
                                            value="{{ $ukuran }}"
                                            class="w-full
                                                   min-w-[100px]
                                                   rounded-lg
                                                   border
                                                   border-[#c3c6d7]
                                                   bg-[#eff4ff]
                                                   px-3 py-2
                                                   text-sm
                                                   font-semibold
                                                   text-[#0b1c30]"
                                        >

                                    </td>


                                    {{-- NO TRUCK --}}

                                    <td
                                        class="whitespace-nowrap
                                               px-5 py-4"
                                    >

                                        <input
                                            type="text"
                                            readonly
                                            value="{{ $noTruck }}"
                                            class="w-full
                                                   min-w-[140px]
                                                   rounded-lg
                                                   border
                                                   border-[#c3c6d7]
                                                   bg-[#eff4ff]
                                                   px-3 py-2
                                                   text-sm
                                                   font-semibold
                                                   text-[#0b1c30]"
                                        >

                                    </td>


                                    {{-- LOKASI --}}

                                    <td
                                        class="whitespace-nowrap
                                               px-5 py-4"
                                    >

                                        <input
                                            type="text"
                                            readonly
                                            value="{{ $lokasi }}"
                                            class="w-full
                                                   min-w-[130px]
                                                   rounded-lg
                                                   border
                                                   border-[#c3c6d7]
                                                   bg-[#eff4ff]
                                                   px-3 py-2
                                                   text-sm
                                                   font-semibold
                                                   text-[#0b1c30]"
                                        >

                                    </td>


                                    {{-- PROSES --}}

                                    <td
                                        class="whitespace-nowrap
                                               px-5 py-4"
                                    >

                                        <button
                                            type="button"
                                            wire:click="onChassis({{ $operation->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="onChassis({{ $operation->id }})"
                                            class="inline-flex
                                                   items-center
                                                   justify-center
                                                   gap-2
                                                   rounded-lg
                                                   bg-[#004ac6]
                                                   px-5 py-2.5
                                                   text-sm
                                                   font-semibold
                                                   text-white
                                                   hover:bg-[#003ea8]
                                                   disabled:opacity-50"
                                        >

                                            <span
                                                class="material-symbols-outlined
                                                       text-[18px]"
                                            >
                                                directions_car
                                            </span>


                                            <span
                                                wire:loading.remove
                                                wire:target="onChassis({{ $operation->id }})"
                                            >
                                                ON CHASSES
                                            </span>


                                            <span
                                                wire:loading
                                                wire:target="onChassis({{ $operation->id }})"
                                            >
                                                PROCESS...
                                            </span>

                                        </button>

                                    </td>

                                </tr>

                            @empty

                                {{-- =================================================
                                    EMPTY TABLE
                                ================================================== --}}

                                <tr>

                                    <td
                                        colspan="5"
                                        class="px-5 py-14
                                               text-center"
                                    >

                                        <div
                                            class="mx-auto
                                                   flex h-14 w-14
                                                   items-center
                                                   justify-center
                                                   rounded-full
                                                   bg-[#eff4ff]
                                                   text-[#004ac6]"
                                        >

                                            <span
                                                class="material-symbols-outlined
                                                       text-[28px]"
                                            >
                                                directions_car
                                            </span>

                                        </div>


                                        <p
                                            class="mt-4 text-sm
                                                   font-semibold
                                                   text-[#434655]"
                                        >
                                            Belum ada data container
                                        </p>


                                        <p
                                            class="mt-1 text-xs
                                                   text-[#737686]"
                                        >
                                            Data container akan muncul
                                            setelah pencarian dilakukan.
                                        </p>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>



            {{-- ====================================================
                SELECTED OPERATION
            ===================================================== --}}

            @if($selectedOperation)

                <div
                    class="mt-6 overflow-hidden
                           rounded-xl
                           border
                           border-[#c3c6d7]/30
                           bg-white
                           shadow-sm"
                >

                    <div
                        class="border-b
                               border-[#c3c6d7]/30
                               px-5 py-4
                               sm:px-6"
                    >

                        <div
                            class="flex items-center
                                   gap-3"
                        >

                            <span
                                class="material-symbols-outlined
                                       text-[#004ac6]"
                            >
                                directions_car
                            </span>


                            <div>

                                <h2
                                    class="text-base
                                           font-semibold"
                                >
                                    Container Terpilih
                                </h2>


                                <p
                                    class="mt-1 text-xs
                                           text-[#737686]"
                                >
                                    Detail operation yang dipilih.
                                </p>

                            </div>

                        </div>

                    </div>


                    <div
                        class="grid grid-cols-1
                               gap-5 p-5
                               sm:grid-cols-2
                               sm:p-6"
                    >

                        <div>

                            <label
                                class="mb-2 block
                                       text-xs
                                       font-semibold
                                       uppercase
                                       tracking-wider
                                       text-[#434655]"
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
                                class="w-full rounded-lg
                                       border
                                       border-[#c3c6d7]
                                       bg-[#eff4ff]
                                       px-4 py-2.5
                                       text-sm
                                       font-semibold"
                            >

                        </div>


                        <div>

                            <label
                                class="mb-2 block
                                       text-xs
                                       font-semibold
                                       uppercase
                                       tracking-wider
                                       text-[#434655]"
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
                                class="w-full rounded-lg
                                       border
                                       border-[#c3c6d7]
                                       bg-[#eff4ff]
                                       px-4 py-2.5
                                       text-sm
                                       font-semibold"
                            >

                        </div>


                        <div>

                            <label
                                class="mb-2 block
                                       text-xs
                                       font-semibold
                                       uppercase
                                       tracking-wider
                                       text-[#434655]"
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
                                class="w-full rounded-lg
                                       border
                                       border-[#c3c6d7]
                                       bg-[#eff4ff]
                                       px-4 py-2.5
                                       text-sm
                                       font-semibold"
                            >

                        </div>


                        <div>

                            <label
                                class="mb-2 block
                                       text-xs
                                       font-semibold
                                       uppercase
                                       tracking-wider
                                       text-[#434655]"
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
                                class="w-full rounded-lg
                                       border
                                       border-[#c3c6d7]
                                       bg-[#eff4ff]
                                       px-4 py-2.5
                                       text-sm
                                       font-semibold"
                            >

                        </div>

                    </div>

                </div>

            @endif



            {{-- ====================================================
                FOOTER
            ===================================================== --}}

            <footer
                class="mt-6 border-t
                       border-[#c3c6d7]/30
                       pt-5 text-center"
            >

                <p class="text-xs text-[#737686]">
                    ON CHASSIS · PortOps Central
                </p>

            </footer>

        </main>

    </div>

</div>