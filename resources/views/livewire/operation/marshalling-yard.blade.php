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
            class="flex flex-col gap-1
                   border-b border-[#c3c6d7]/10
                   p-6"
        >

            <h1 class="text-base font-bold text-[#dbe1ff]">
                PortOps Central
            </h1>

            <p class="text-[13px] text-[#bec6e0]">
                Terminal A-101
            </p>

        </div>


        {{-- MENU --}}

        <nav class="flex flex-1 flex-col py-4">

            {{-- DASHBOARD --}}

            <a
                href="{{ route('home') }}"
                class="flex items-center gap-4
                       border-l-4 border-transparent
                       px-6 py-3
                       text-[#bec6e0]/70
                       transition
                       hover:bg-[#d3e4fe]/10
                       hover:text-[#bec6e0]"
            >

                <span class="material-symbols-outlined">
                    dashboard
                </span>

                <span class="text-xs font-semibold tracking-wider">
                    Dashboard
                </span>

            </a>


            {{-- OPERATIONS TITLE --}}

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

                <span class="text-xs font-semibold tracking-wider">
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

                <span class="text-xs font-semibold tracking-wider">
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

                <span class="text-xs font-semibold tracking-wider">
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

                <span class="text-xs font-semibold tracking-wider">
                    MARSHALLING CIC
                </span>

            </a>


            {{-- MARSHALLING YARD ACTIVE --}}

            <a
                href="{{ route('operation.marshalling-yard') }}"
                class="flex items-center gap-4
                       border-l-4 border-[#004ac6]
                       bg-[#2563eb]/10
                       px-6 py-3
                       font-bold text-[#dbe1ff]"
            >

                <span
                    class="material-symbols-outlined"
                    style="font-variation-settings:'FILL' 1;"
                >
                    location_on
                </span>

                <span class="text-xs font-semibold tracking-wider">
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

                <span class="text-xs font-semibold tracking-wider">
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

                <span class="text-xs font-semibold tracking-wider">
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

                <span class="text-xs font-semibold tracking-wider">
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

                <span class="text-xs font-semibold tracking-wider">
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

                <span class="text-xs font-semibold tracking-wider">
                    INSPECTION OUT
                </span>

            </a>


            {{-- ON CHASSIS --}}

            <a
                href="{{ route('operation.on-chassis') }}"
                class="flex items-center gap-4
                       border-l-4 border-transparent
                       px-6 py-3
                       text-[#bec6e0]/70
                       hover:bg-[#d3e4fe]/10"
            >

                <span class="material-symbols-outlined">
                    directions_car
                </span>

                <span class="text-xs font-semibold tracking-wider">
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

                <span class="text-xs font-semibold tracking-wider">
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


        {{-- TOP BAR --}}

        <header
            class="sticky top-0 z-30
                   flex h-14 items-center
                   justify-between
                   border-b border-[#c3c6d7]/30
                   bg-[#f8f9ff]
                   px-4 sm:px-6"
        >

            <div class="flex items-center gap-4">

                <span
                    class="text-base font-black
                           text-[#0b1c30] md:hidden"
                >
                    PortOps Central
                </span>


                <div class="relative hidden sm:block">

                    <span
                        class="material-symbols-outlined
                               absolute left-2 top-1/2
                               -translate-y-1/2
                               text-[18px]
                               text-[#434655]"
                    >
                        search
                    </span>


                    <input
                        type="text"
                        placeholder="Search operations..."
                        class="w-64 rounded
                               border border-[#c3c6d7]/50
                               bg-[#eff4ff]
                               py-1 pl-8 pr-4
                               text-[13px]
                               outline-none
                               focus:border-[#004ac6]"
                    >

                </div>

            </div>


            <div class="flex items-center gap-3">

                <button
                    type="button"
                    class="rounded p-1 text-[#434655]
                           hover:bg-[#eff4ff]"
                >

                    <span class="material-symbols-outlined text-[20px]">
                        notifications
                    </span>

                </button>


                <div class="h-6 w-px bg-[#c3c6d7]/30"></div>


                <div
                    class="flex h-8 w-8
                           items-center
                           justify-center
                           rounded-full
                           bg-[#2563eb]"
                >

                    <span
                        class="material-symbols-outlined
                               text-[18px] text-white"
                    >
                        person
                    </span>

                </div>

            </div>

        </header>



        {{-- CONTENT --}}

        <main class="min-h-[calc(100vh-56px)] p-4 sm:p-6">


            {{-- PAGE HEADER --}}

            <div class="mb-6">

                <div class="mb-2 flex items-center gap-2">

                    <span
                        class="material-symbols-outlined
                               text-[23px]
                               text-[#004ac6]"
                    >
                        location_on
                    </span>


                    <h1
                        class="text-2xl font-semibold
                               tracking-tight
                               text-[#0b1c30]"
                    >
                        MARSHALLING YARD
                    </h1>

                </div>


                <p class="text-sm text-[#434655]">
                    Search No Container untuk melihat data job dan proses Marshalling Yard.
                </p>

            </div>



            {{-- SEARCH CARD --}}

            <div
                class="mb-6 rounded-xl
                       border border-[#c3c6d7]/30
                       bg-white p-5
                       shadow-sm sm:p-6"
            >

                <div class="mb-5 flex items-center gap-3">

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
                            class="text-base font-semibold
                                   text-[#0b1c30]"
                        >
                            Search Container
                        </h2>


                        <p class="text-xs text-[#737686]">
                            Masukkan nomor container untuk mencari job.
                        </p>

                    </div>

                </div>


                <form wire:submit="search">

                    <div
                        class="flex flex-col gap-3
                               sm:flex-row sm:items-end"
                    >

                        <div class="w-full sm:max-w-md">

                            <label
                                for="searchCont"
                                class="mb-2 block
                                       text-xs font-semibold
                                       uppercase
                                       tracking-wider
                                       text-[#434655]"
                            >
                                No Container
                            </label>


                            <div class="relative">

                                <span
                                    class="material-symbols-outlined
                                           absolute left-3 top-1/2
                                           -translate-y-1/2
                                           text-[20px]
                                           text-[#737686]"
                                >
                                    inventory_2
                                </span>


                                <input
                                    id="searchCont"
                                    type="text"
                                    wire:model="searchCont"
                                    autofocus
                                    autocomplete="off"
                                    placeholder="SEARCH NO CONTAINER"
                                    class="w-full rounded-lg
                                           border
                                           border-[#c3c6d7]
                                           bg-white
                                           py-2.5 pl-10 pr-4
                                           text-sm
                                           text-[#0b1c30]
                                           placeholder:text-[#737686]
                                           focus:border-[#004ac6]
                                           focus:outline-none
                                           focus:ring-2
                                           focus:ring-[#b4c5ff]"
                                >

                            </div>


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
                                   gap-2 rounded-lg
                                   bg-[#004ac6]
                                   px-5 py-2.5
                                   text-sm font-semibold
                                   text-white
                                   hover:bg-[#003ea8]
                                   disabled:cursor-not-allowed
                                   disabled:opacity-50"
                        >

                            <span
                                wire:loading.remove
                                wire:target="search"
                                class="material-symbols-outlined
                                       text-[19px]"
                            >
                                search
                            </span>


                            <span
                                wire:loading
                                wire:target="search"
                                class="material-symbols-outlined
                                       animate-spin
                                       text-[19px]"
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
                        )

                            <button
                                type="button"
                                wire:click="resetSearch"
                                class="inline-flex
                                       items-center
                                       justify-center
                                       gap-2 rounded-lg
                                       border
                                       border-[#c3c6d7]
                                       bg-white
                                       px-5 py-2.5
                                       text-sm font-semibold
                                       text-[#434655]
                                       hover:bg-[#eff4ff]"
                            >

                                <span
                                    class="material-symbols-outlined
                                           text-[18px]"
                                >
                                    refresh
                                </span>

                                REFRESH

                            </button>

                        @endif

                    </div>

                </form>

            </div>



            {{-- MESSAGE --}}

            @isset($message)
            @if($message)

                <div
                    class="mb-6 flex items-start
                           gap-3 rounded-lg border
                           px-4 py-3

                           @if($messageType === 'success')
                               border-[#4edea3]/40
                               bg-[#6ffbbe]/10
                               text-[#005236]
                           @else
                               border-[#ba1a1a]/30
                               bg-[#ffdad6]/50
                               text-[#93000a]
                           @endif"
                >

                    <span
                        class="material-symbols-outlined
                               text-[20px]"
                    >

                        @if($messageType === 'success')
                            check_circle
                        @else
                            error
                        @endif

                    </span>


                    <p class="text-sm font-semibold">
                        {{ $message }}
                    </p>

                </div>

            @endif
            @endisset



            {{-- ====================================================
                TABLE
            ===================================================== --}}

            <div
                class="overflow-hidden
                       rounded-xl
                       border border-[#c3c6d7]/30
                       bg-white shadow-sm"
            >

                {{-- TABLE HEADER --}}

                <div
                    class="flex flex-col gap-4
                           border-b
                           border-[#c3c6d7]/30
                           px-5 py-5
                           sm:px-6
                           md:flex-row
                           md:items-center
                           md:justify-between"
                >

                    <div>

                        <div class="flex items-center gap-2">

                            <span
                                class="material-symbols-outlined
                                       text-[#004ac6]"
                            >
                                location_on
                            </span>


                            <h2
                                class="text-base font-semibold
                                       text-[#0b1c30]"
                            >
                                Data Marshalling Yard
                            </h2>

                        </div>


                        <p
                            class="mt-1 text-xs
                                   text-[#737686]"
                        >
                            Job slip dan lokasi container.
                        </p>

                    </div>


                    <div
                        class="rounded-lg
                               border
                               border-[#c3c6d7]/30
                               bg-[#eff4ff]
                               px-4 py-3"
                    >

                        <p
                            class="text-[10px]
                                   font-semibold
                                   uppercase
                                   tracking-widest
                                   text-[#737686]"
                        >
                            Total
                        </p>


                        <p
                            class="mt-1 text-sm
                                   font-bold
                                   text-[#004ac6]"
                        >
                            {{ is_countable($operations) ? count($operations) : 0 }}
                        </p>

                    </div>

                </div>



                {{-- TABLE RESPONSIVE --}}

                <div class="overflow-x-auto">

                    <table class="min-w-[1100px] w-full">

                        <thead class="bg-[#eff4ff]">

                            <tr>

                                <th
                                    class="px-5 py-3
                                           text-left
                                           text-[10px]
                                           font-semibold
                                           uppercase
                                           tracking-widest
                                           text-[#737686]"
                                >
                                    NO
                                </th>


                                <th
                                    class="px-5 py-3
                                           text-left
                                           text-[10px]
                                           font-semibold
                                           uppercase
                                           tracking-widest
                                           text-[#737686]"
                                >
                                    ID JOB
                                </th>


                                <th
                                    class="px-5 py-3
                                           text-left
                                           text-[10px]
                                           font-semibold
                                           uppercase
                                           tracking-widest
                                           text-[#737686]"
                                >
                                    NO CONTAINER
                                </th>


                                <th
                                    class="px-5 py-3
                                           text-left
                                           text-[10px]
                                           font-semibold
                                           uppercase
                                           tracking-widest
                                           text-[#737686]"
                                >
                                    UKURAN
                                </th>


                                <th
                                    class="px-5 py-3
                                           text-left
                                           text-[10px]
                                           font-semibold
                                           uppercase
                                           tracking-widest
                                           text-[#737686]"
                                >
                                    LOKASI AWAL
                                </th>


                                <th
                                    class="px-5 py-3
                                           text-left
                                           text-[10px]
                                           font-semibold
                                           uppercase
                                           tracking-widest
                                           text-[#737686]"
                                >
                                    LOKASI AKHIR
                                </th>


                                <th
                                    class="px-5 py-3
                                           text-left
                                           text-[10px]
                                           font-semibold
                                           uppercase
                                           tracking-widest
                                           text-[#737686]"
                                >
                                    JOB
                                </th>


                                <th
                                    class="px-5 py-3
                                           text-left
                                           text-[10px]
                                           font-semibold
                                           uppercase
                                           tracking-widest
                                           text-[#737686]"
                                >
                                    RESPON
                                </th>


                                <th
                                    class="px-5 py-3
                                           text-left
                                           text-[10px]
                                           font-semibold
                                           uppercase
                                           tracking-widest
                                           text-[#737686]"
                                >
                                    PROSES
                                </th>

                            </tr>

                        </thead>



                        <tbody
                            class="divide-y
                                   divide-[#c3c6d7]/20
                                   bg-white"
                        >

                            @forelse($operations as $index => $operation)

                                @php

                                    $marshalling =
                                        $operation->marshalling;

                                    $jobSlip =
                                        $marshalling?->jobSlip;

                                    $locationFrom =
                                        $marshalling?->locationFrom;

                                    $locationTo =
                                        $marshalling?->locationTo;

                                    $locationAwal =
                                        $locationFrom?->location_code
                                        ?? '-';

                                    $locationAkhir =
                                        $locationTo?->location_code
                                        ?? '-';

                                    $respon =
                                        $marshalling?->response
                                        ?? $marshalling?->respon
                                        ?? 'NO RESPON';

                                    if (
                                        $respon === null ||
                                        $respon === '' ||
                                        strtoupper($respon) === 'NULL'
                                    ) {
                                        $respon = 'NO RESPON';
                                    }

                                @endphp


                                <tr
                                    wire:key="marshalling-yard-{{ $operation->id }}"
                                    class="transition-colors
                                           hover:bg-[#eff4ff]/50"
                                >

                                    {{-- NO --}}

                                    <td
                                        class="whitespace-nowrap
                                               px-5 py-4
                                               text-sm
                                               text-[#434655]"
                                    >
                                        {{ $index + 1 }}
                                    </td>


                                    {{-- ID JOB --}}

                                    <td
                                        class="whitespace-nowrap
                                               px-5 py-4
                                               text-sm
                                               font-semibold
                                               text-[#0b1c30]"
                                    >
                                        {{ $jobSlip?->id ?? '-' }}
                                    </td>


                                    {{-- CONTAINER --}}

                                    <td
                                        class="whitespace-nowrap
                                               px-5 py-4
                                               text-sm
                                               font-bold
                                               text-[#0b1c30]"
                                    >
                                        {{ $operation->container?->no_cont ?? '-' }}
                                    </td>


                                    {{-- SIZE --}}

                                    <td
                                        class="whitespace-nowrap
                                               px-5 py-4
                                               text-sm
                                               text-[#434655]"
                                    >
                                        {{ $operation->container?->type?->size ?? '-' }}
                                    </td>


                                    {{-- LOKASI AWAL --}}

                                    <td
                                        class="whitespace-nowrap
                                               px-5 py-4
                                               text-sm
                                               text-[#434655]"
                                    >
                                        {{ $locationAwal }}
                                    </td>


                                    {{-- LOKASI AKHIR --}}

                                    <td
                                        class="whitespace-nowrap
                                               px-5 py-4
                                               text-sm
                                               text-[#434655]"
                                    >
                                        {{ $locationAkhir }}
                                    </td>


                                    {{-- JOB --}}

                                    <td
                                        class="whitespace-nowrap
                                               px-5 py-4
                                               text-sm
                                               text-[#434655]"
                                    >
                                        {{ $jobSlip?->job_type ?? '-' }}
                                    </td>


                                    {{-- RESPON --}}

                                    <td
                                        class="whitespace-nowrap
                                               px-5 py-4"
                                    >

                                        @if($respon === 'NO RESPON')

                                            <span
                                                class="inline-flex
                                                       rounded-full
                                                       bg-[#ffdad6]
                                                       px-3 py-1
                                                       text-[10px]
                                                       font-semibold
                                                       uppercase
                                                       tracking-wider
                                                       text-[#93000a]"
                                            >
                                                NO RESPON
                                            </span>

                                        @else

                                            <span
                                                class="inline-flex
                                                       rounded-full
                                                       bg-[#6ffbbe]/20
                                                       px-3 py-1
                                                       text-[10px]
                                                       font-semibold
                                                       text-[#005236]"
                                            >
                                                {{ $respon }}
                                            </span>

                                        @endif

                                    </td>


                                    {{-- PROSES --}}

                                    <td
                                        class="whitespace-nowrap
                                               px-5 py-4"
                                    >

                                        <button
                                            type="button"
                                            wire:click="selectOperation({{ $operation->id }})"
                                            class="inline-flex
                                                   items-center
                                                   gap-2 rounded-lg
                                                   bg-[#004ac6]
                                                   px-4 py-2
                                                   text-xs
                                                   font-semibold
                                                   text-white
                                                   hover:bg-[#003ea8]"
                                        >

                                            <span
                                                class="material-symbols-outlined
                                                       text-[17px]"
                                            >
                                                location_on
                                            </span>

                                            MARSHALLING

                                        </button>

                                    </td>

                                </tr>


                            @empty

                                <tr>

                                    <td
                                        colspan="9"
                                        class="px-6 py-14
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
                                                location_on
                                            </span>

                                        </div>


                                        <p
                                            class="mt-4 text-sm
                                                   font-semibold
                                                   text-[#434655]"
                                        >
                                            Belum ada data Marshalling Yard
                                        </p>


                                        <p
                                            class="mt-1 text-xs
                                                   text-[#737686]"
                                        >
                                            Masukkan nomor container
                                            kemudian tekan SEARCH.
                                        </p>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>



            {{-- ====================================================
                SELECTED DETAIL
            ===================================================== --}}

            @if($selectedOperation)

                <div
                    class="mt-6 overflow-hidden
                           rounded-xl
                           border
                           border-[#c3c6d7]/30
                           bg-white shadow-sm"
                >

                    <div
                        class="border-b
                               border-[#c3c6d7]/30
                               px-5 py-5 sm:px-6"
                    >

                        <div class="flex items-center gap-2">

                            <span
                                class="material-symbols-outlined
                                       text-[#004ac6]"
                            >
                                description
                            </span>


                            <h2
                                class="text-base
                                       font-semibold
                                       text-[#0b1c30]"
                            >
                                Marshalling Detail
                            </h2>

                        </div>

                    </div>


                    <div
                        class="grid grid-cols-1
                               gap-5 p-5
                               sm:grid-cols-2
                               lg:grid-cols-4 sm:p-6"
                    >

                        <div>

                            <p
                                class="text-[10px]
                                       font-semibold
                                       uppercase
                                       tracking-widest
                                       text-[#737686]"
                            >
                                No SPK
                            </p>


                            <p
                                class="mt-1 text-sm
                                       font-semibold
                                       text-[#0b1c30]"
                            >
                                {{ $selectedOperation->spk?->no_spk ?? '-' }}
                            </p>

                        </div>


                        <div>

                            <p
                                class="text-[10px]
                                       font-semibold
                                       uppercase
                                       tracking-widest
                                       text-[#737686]"
                            >
                                No Container
                            </p>


                            <p
                                class="mt-1 text-sm
                                       font-semibold
                                       text-[#0b1c30]"
                            >
                                {{ $selectedOperation->container?->no_cont ?? '-' }}
                            </p>

                        </div>


                        <div>

                            <p
                                class="text-[10px]
                                       font-semibold
                                       uppercase
                                       tracking-widest
                                       text-[#737686]"
                            >
                                Job Slip
                            </p>


                            <p
                                class="mt-1 text-sm
                                       font-semibold
                                       text-[#0b1c30]"
                            >
                                {{ $selectedMarshalling?->jobSlip?->no_job ?? '-' }}
                            </p>

                        </div>


                        <div>

                            <p
                                class="text-[10px]
                                       font-semibold
                                       uppercase
                                       tracking-widest
                                       text-[#737686]"
                            >
                                Status
                            </p>


                            <p
                                class="mt-1 text-sm
                                       font-semibold
                                       text-[#0b1c30]"
                            >
                                {{ $selectedMarshalling?->status ?? '-' }}
                            </p>

                        </div>


                        <div>

                            <p
                                class="text-[10px]
                                       font-semibold
                                       uppercase
                                       tracking-widest
                                       text-[#737686]"
                            >
                                Location From
                            </p>


                            <p
                                class="mt-1 text-sm
                                       font-semibold
                                       text-[#0b1c30]"
                            >
                                {{
                                    $selectedMarshalling
                                        ?->locationFrom
                                        ?->location_code
                                    ?? '-'
                                }}
                            </p>

                        </div>


                        <div>

                            <p
                                class="text-[10px]
                                       font-semibold
                                       uppercase
                                       tracking-widest
                                       text-[#737686]"
                            >
                                Location To
                            </p>


                            <p
                                class="mt-1 text-sm
                                       font-semibold
                                       text-[#0b1c30]"
                            >
                                {{
                                    $selectedMarshalling
                                        ?->locationTo
                                        ?->location_code
                                    ?? '-'
                                }}
                            </p>

                        </div>


                        <div>

                            <p
                                class="text-[10px]
                                       font-semibold
                                       uppercase
                                       tracking-widest
                                       text-[#737686]"
                            >
                                Marshalling Type
                            </p>


                            <p
                                class="mt-1 text-sm
                                       font-semibold
                                       text-[#0b1c30]"
                            >
                                {{
                                    $selectedMarshalling
                                        ?->marshalling_type
                                    ?? '-'
                                }}
                            </p>

                        </div>


                        <div>

                            <p
                                class="text-[10px]
                                       font-semibold
                                       uppercase
                                       tracking-widest
                                       text-[#737686]"
                            >
                                Container Size
                            </p>


                            <p
                                class="mt-1 text-sm
                                       font-semibold
                                       text-[#0b1c30]"
                            >
                                {{
                                    $selectedOperation
                                        ->container
                                        ?->type
                                        ?->size
                                    ?? '-'
                                }}
                            </p>

                        </div>

                    </div>

                </div>

            @endif



            {{-- FOOTER --}}

            <footer
                class="mt-6 border-t
                       border-[#c3c6d7]/30
                       pt-5 text-center"
            >

                <p class="text-xs text-[#737686]">
                    MARSHALLING YARD · PortOps Central
                </p>

            </footer>

        </main>

    </div>

</div>