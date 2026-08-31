@props([
    'searchCont' => '',
    'operation' => null,
    'containers' => [],
    'message' => null,
    'messageType' => null,
])

<div class="min-h-screen bg-[#f8f9ff] text-[#0b1c30] antialiased">


    {{-- ============================================================
        SIDEBAR
    ============================================================= --}}

    <aside
        class="fixed left-0 top-0 z-40 hidden h-screen w-[260px]
               flex-col overflow-y-auto border-r
               border-[#c3c6d7]/20 bg-[#213145] md:flex"
    >

        {{-- BRAND --}}

        <div
            class="flex flex-col gap-1 border-b
                   border-[#c3c6d7]/10 p-6"
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

            <a
                href="{{ route('home') }}"
                class="flex items-center gap-4 border-l-4
                       border-transparent px-6 py-3
                       text-[#bec6e0]/70
                       transition hover:bg-[#d3e4fe]/10
                       hover:text-[#bec6e0]"
            >

                <span class="material-symbols-outlined">
                    dashboard
                </span>

                <span class="text-xs font-semibold tracking-wider">
                    Dashboard
                </span>

            </a>


            <div
                class="px-6 pb-2 pt-4 text-[10px]
                       font-semibold uppercase
                       tracking-widest text-[#bec6e0]/50"
            >
                Operations
            </div>


            {{-- PICKUP --}}

            <a
                href="{{ route('operation.pickup') }}"
                class="flex items-center gap-4 border-l-4
                       border-transparent px-6 py-3
                       text-[#bec6e0]/70
                       transition hover:bg-[#d3e4fe]/10
                       hover:text-[#bec6e0]"
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
                class="flex items-center gap-4 border-l-4
                       border-transparent px-6 py-3
                       text-[#bec6e0]/70
                       transition hover:bg-[#d3e4fe]/10
                       hover:text-[#bec6e0]"
            >

                <span class="material-symbols-outlined">
                    move_to_inbox
                </span>

                <span class="text-xs font-semibold tracking-wider">
                    BEHANDLE IN
                </span>

            </a>


            {{-- HOLD ACTIVE --}}

            <a
                href="{{ route('operation.hold') }}"
                class="flex items-center gap-4
                       border-l-4 border-[#004ac6]
                       bg-[#2563eb]/10 px-6 py-3
                       font-bold text-[#dbe1ff]"
            >

                <span
                    class="material-symbols-outlined"
                    style="font-variation-settings: 'FILL' 1;"
                >
                    front_hand
                </span>

                <span class="text-xs font-semibold tracking-wider">
                    HOLD
                </span>

            </a>


            {{-- MARSHALLING --}}

            <a
                href="#"
                class="flex items-center gap-4 border-l-4
                       border-transparent px-6 py-3
                       text-[#bec6e0]/70
                       transition hover:bg-[#d3e4fe]/10
                       hover:text-[#bec6e0]"
            >

                <span class="material-symbols-outlined">
                    warehouse
                </span>

                <span class="text-xs font-semibold tracking-wider">
                    MARSHALLING
                </span>

            </a>


            {{-- INSPECTION --}}

            <a
                href="#"
                class="flex items-center gap-4 border-l-4
                       border-transparent px-6 py-3
                       text-[#bec6e0]/70
                       transition hover:bg-[#d3e4fe]/10
                       hover:text-[#bec6e0]"
            >

                <span class="material-symbols-outlined">
                    fact_check
                </span>

                <span class="text-xs font-semibold tracking-wider">
                    INSPECTION
                </span>

            </a>


            {{-- REEFER --}}

            <a
                href="#"
                class="flex items-center gap-4 border-l-4
                       border-transparent px-6 py-3
                       text-[#bec6e0]/70
                       transition hover:bg-[#d3e4fe]/10
                       hover:text-[#bec6e0]"
            >

                <span class="material-symbols-outlined">
                    ac_unit
                </span>

                <span class="text-xs font-semibold tracking-wider">
                    REEFER
                </span>

            </a>


            {{-- DELIVERY --}}

            <a
                href="#"
                class="flex items-center gap-4 border-l-4
                       border-transparent px-6 py-3
                       text-[#bec6e0]/70
                       transition hover:bg-[#d3e4fe]/10
                       hover:text-[#bec6e0]"
            >

                <span class="material-symbols-outlined">
                    local_shipping
                </span>

                <span class="text-xs font-semibold tracking-wider">
                    DELIVERY
                </span>

            </a>


            {{-- LOGOUT --}}

            <div class="mt-auto">

                <a
                    href="#"
                    class="flex items-center gap-4 border-l-4
                           border-transparent px-6 py-3
                           text-[#bec6e0]/70
                           transition hover:bg-[#d3e4fe]/10
                           hover:text-[#bec6e0]"
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
            TOP BAR
        ========================================================= --}}

        <header
            class="sticky top-0 z-30 flex h-14
                   items-center justify-between
                   border-b border-[#c3c6d7]/30
                   bg-[#f8f9ff] px-4 sm:px-6"
        >

            <div class="flex items-center gap-4">

                {{-- MOBILE BRAND --}}

                <span
                    class="text-base font-black text-[#0b1c30] md:hidden"
                >
                    PortOps Central
                </span>


                {{-- TOP SEARCH --}}

                <div class="relative hidden sm:block">

                    <span
                        class="material-symbols-outlined
                               absolute left-2 top-1/2
                               -translate-y-1/2
                               text-[18px] text-[#434655]"
                    >
                        search
                    </span>

                    <input
                        type="text"
                        placeholder="Search operations..."
                        class="w-64 rounded border
                               border-[#c3c6d7]/50
                               bg-[#eff4ff]
                               py-1 pl-8 pr-4
                               text-[13px]
                               outline-none
                               focus:border-[#004ac6]
                               focus:ring-1
                               focus:ring-[#004ac6]"
                    >

                </div>

            </div>


            <div class="flex items-center gap-3 sm:gap-4">

                <button
                    type="button"
                    class="rounded p-1 text-[#434655]
                           hover:bg-[#eff4ff]"
                >

                    <span class="material-symbols-outlined text-[20px]">
                        notifications
                    </span>

                </button>


                <button
                    type="button"
                    class="hidden rounded p-1
                           text-[#434655]
                           hover:bg-[#eff4ff] sm:block"
                >

                    <span class="material-symbols-outlined text-[20px]">
                        terminal
                    </span>

                </button>


                <button
                    type="button"
                    class="hidden rounded p-1
                           text-[#434655]
                           hover:bg-[#eff4ff] sm:block"
                >

                    <span class="material-symbols-outlined text-[20px]">
                        help_outline
                    </span>

                </button>


                <div class="h-6 w-px bg-[#c3c6d7]/30"></div>


                <div
                    class="flex h-8 w-8 items-center
                           justify-center rounded-full
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



        {{-- ========================================================
            CONTENT
        ========================================================= --}}

        <main class="min-h-[calc(100vh-56px)] p-4 sm:p-6">


            {{-- ====================================================
                PAGE HEADER
            ===================================================== --}}

            <div class="mb-6">

                <div class="mb-2 flex items-center gap-2">

                    <span
                        class="material-symbols-outlined
                               text-[23px] text-[#004ac6]"
                    >
                        front_hand
                    </span>


                    <h1
                        class="text-2xl font-semibold
                               tracking-tight text-[#0b1c30]"
                    >
                        HOLD
                    </h1>

                </div>


                <p class="text-sm text-[#434655]">
                    Search No Container untuk proses dan melihat informasi Hold.
                </p>

            </div>



            {{-- ====================================================
                SEARCH CARD
            ===================================================== --}}

            <div
                class="mb-6 rounded-xl border
                       border-[#c3c6d7]/30
                       bg-white p-5 shadow-sm sm:p-6"
            >

                <div class="mb-5 flex items-center gap-3">

                    <div
                        class="flex h-10 w-10
                               items-center justify-center
                               rounded-lg bg-[#d3e4fe]
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
                            Masukkan nomor container.
                        </p>

                    </div>

                </div>


                <form wire:submit="search">

                    <div
                        class="flex flex-col gap-3
                               sm:flex-row
                               sm:items-end"
                    >

                        <div class="w-full sm:max-w-md">

                            <label
                                for="searchCont"
                                class="mb-2 block
                                       text-xs font-semibold
                                       uppercase tracking-wider
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
                                    autocomplete="off"
                                    autofocus
                                    placeholder="SEARCH NO CONT"
                                    class="w-full rounded-lg
                                           border border-[#c3c6d7]
                                           bg-white py-2.5
                                           pl-10 pr-4 text-sm
                                           text-[#0b1c30]
                                           placeholder:text-[#737686]
                                           focus:border-[#004ac6]
                                           focus:outline-none
                                           focus:ring-2
                                           focus:ring-[#b4c5ff]"
                                >

                            </div>


                            @error('searchCont')

                                <p class="mt-2 text-xs text-[#ba1a1a]">
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>


                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center
                                   justify-center gap-2
                                   rounded-lg
                                   bg-[#004ac6]
                                   px-5 py-2.5
                                   text-sm font-semibold
                                   text-white
                                   transition
                                   hover:bg-[#003ea8]
                                   disabled:cursor-not-allowed
                                   disabled:opacity-50"
                        >

                            <span
                                wire:loading.remove
                                wire:target="search"
                                class="material-symbols-outlined text-[19px]"
                            >
                                search
                            </span>


                            <span
                                wire:loading
                                wire:target="search"
                                class="material-symbols-outlined
                                       animate-spin text-[19px]"
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


                       @if(($searchCont ?? '') !== '' || !empty($containers))

                            <button
                                type="button"
                                wire:click="resetSearch"
                                class="inline-flex items-center
                                       justify-center gap-2
                                       rounded-lg
                                       border border-[#c3c6d7]
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
                    class="mb-6 flex items-start
                           gap-3 rounded-lg border px-4 py-3

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

                    <span class="material-symbols-outlined text-[20px]">

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
                MULTIPLE CONTAINER RESULT
            ===================================================== --}}

            @if(!empty($containers))

                <div
                    class="mb-6 overflow-hidden
                           rounded-xl border
                           border-[#c3c6d7]/30
                           bg-white shadow-sm"
                >

                    {{-- HEADER --}}

                    <div
                        class="border-b border-[#c3c6d7]/30
                               px-5 py-5 sm:px-6"
                    >

                        <div class="flex items-center gap-2">

                            <span
                                class="material-symbols-outlined
                                       text-[#004ac6]"
                            >
                                inventory_2
                            </span>


                            <h2
                                class="text-base font-semibold
                                       text-[#0b1c30]"
                            >
                                Search Result
                            </h2>

                        </div>


                        <p class="mt-1 text-xs text-[#737686]">
                            Pilih container untuk melihat informasi detail.
                        </p>

                    </div>


                    {{-- CONTAINER LIST --}}

                    <div
                        class="grid grid-cols-1
                               gap-3 p-5
                               sm:grid-cols-2
                               lg:grid-cols-3 sm:p-6"
                    >

                        @foreach($containers as $container)

                            <button
                                type="button"
                                class="group rounded-xl
                                       border
                                       border-[#c3c6d7]/40
                                       bg-[#f8f9ff]
                                       p-4 text-left
                                       transition
                                       hover:border-[#004ac6]
                                       hover:bg-[#eff4ff]
                                       hover:shadow-sm"
                            >

                                <div
                                    class="flex items-center
                                           justify-between gap-3"
                                >

                                    <div
                                        class="flex h-10 w-10
                                               shrink-0
                                               items-center
                                               justify-center
                                               rounded-lg
                                               bg-[#d3e4fe]
                                               text-[#004ac6]"
                                    >

                                        <span
                                            class="material-symbols-outlined"
                                        >
                                            inventory_2
                                        </span>

                                    </div>


                                    <span
                                        class="material-symbols-outlined
                                               text-[#737686]
                                               transition
                                               group-hover:text-[#004ac6]"
                                    >
                                        chevron_right
                                    </span>

                                </div>


                                <p
                                    class="mt-4 text-sm
                                           font-bold
                                           text-[#0b1c30]"
                                >
                                    {{ $container->no_cont ?? '-' }}
                                </p>


                                <p
                                    class="mt-1 text-xs
                                           text-[#737686]"
                                >
                                    {{ $container->type?->name ?? 'Container' }}
                                </p>

                            </button>

                        @endforeach

                    </div>

                </div>

            @endif



            {{-- ====================================================
                CONTAINER DETAIL
            ===================================================== --}}

            @if($selectedContainer)

                <div
                    class="mb-6 overflow-hidden
                           rounded-xl border
                           border-[#c3c6d7]/30
                           bg-white shadow-sm"
                >

                    <div
                        class="border-b border-[#c3c6d7]/30
                               px-5 py-5 sm:px-6"
                    >

                        <div class="flex items-center gap-2">

                            <span
                                class="material-symbols-outlined
                                       text-[#004ac6]"
                            >
                                description
                            </span>


                            <div>

                                <h2
                                    class="text-base font-semibold
                                           text-[#0b1c30]"
                                >
                                    Container / Document Information
                                </h2>


                                <p class="text-xs text-[#737686]">
                                    Informasi container yang akan diproses.
                                </p>

                            </div>

                        </div>

                    </div>


                    <div
                        class="grid grid-cols-1
                               gap-5 p-5
                               sm:grid-cols-2
                               lg:grid-cols-3 sm:p-6"
                    >

                        {{-- NO SPK --}}

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
                                {{ $selectedContainer->operation?->spk?->no_spk ?? '-' }}
                            </p>

                        </div>


                        {{-- CONTAINER --}}

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
                                {{ $selectedContainer->no_cont ?? '-' }}
                            </p>

                        </div>


                        {{-- DOCUMENT --}}

                        <div>

                            <p
                                class="text-[10px]
                                       font-semibold
                                       uppercase
                                       tracking-widest
                                       text-[#737686]"
                            >
                                No Dokumen
                            </p>


                            <p
                                class="mt-1 text-sm
                                       font-semibold
                                       text-[#0b1c30]"
                            >
                                {{ $selectedContainer->no_document ?? '-' }}
                            </p>

                        </div>


                        {{-- DATE --}}

                        <div>

                            <p
                                class="text-[10px]
                                       font-semibold
                                       uppercase
                                       tracking-widest
                                       text-[#737686]"
                            >
                                Tanggal Dokumen
                            </p>


                            <p
                                class="mt-1 text-sm
                                       font-semibold
                                       text-[#0b1c30]"
                            >
                                {{ $selectedContainer->document_date?->format('d-m-Y') ?? '-' }}
                            </p>

                        </div>


                        {{-- DOCUMENT TYPE --}}

                        <div>

                            <p
                                class="text-[10px]
                                       font-semibold
                                       uppercase
                                       tracking-widest
                                       text-[#737686]"
                            >
                                Jenis Dokumen
                            </p>


                            <p
                                class="mt-1 text-sm
                                       font-semibold
                                       text-[#0b1c30]"
                            >
                                {{ $selectedContainer->document_type ?? '-' }}
                            </p>

                        </div>


                        {{-- DESCRIPTION --}}

                        <div class="sm:col-span-2">

                            <p
                                class="text-[10px]
                                       font-semibold
                                       uppercase
                                       tracking-widest
                                       text-[#737686]"
                            >
                                Keterangan
                            </p>


                            <p
                                class="mt-1 text-sm
                                       font-semibold
                                       text-[#0b1c30]"
                            >
                                {{ $selectedContainer->description ?? '-' }}
                            </p>

                        </div>

                    </div>



                    {{-- HOLD ACTION --}}

                    <div
                        class="border-t
                               border-[#c3c6d7]/30
                               bg-[#f8f9ff]
                               p-5 sm:p-6"
                    >

                        <div
                            class="flex flex-col gap-5
                                   lg:flex-row
                                   lg:items-end
                                   lg:justify-between"
                        >

                            <div class="w-full lg:max-w-md">

                                <label
                                    for="warna"
                                    class="mb-2 block
                                           text-xs font-semibold
                                           uppercase
                                           tracking-wider
                                           text-[#434655]"
                                >
                                    Jenis / Warna Segel
                                </label>


                                <select
                                    id="warna"
                                    wire:model="warna"
                                    class="w-full rounded-lg
                                           border
                                           border-[#c3c6d7]
                                           bg-white
                                           px-4 py-2.5
                                           text-sm
                                           text-[#0b1c30]
                                           outline-none
                                           focus:border-[#004ac6]
                                           focus:ring-2
                                           focus:ring-[#b4c5ff]"
                                >

                                    <option value="">
                                        Pilih Warna Segel
                                    </option>

                                    <option value="N">
                                        Putih
                                    </option>

                                    <option value="M">
                                        Merah
                                    </option>

                                    <option value="T">
                                        Timah
                                    </option>

                                </select>

                            </div>


                            <button
                                type="button"
                                class="inline-flex
                                       w-full
                                       items-center
                                       justify-center
                                       gap-2
                                       rounded-lg
                                       bg-[#004ac6]
                                       px-6 py-2.5
                                       text-sm
                                       font-semibold
                                       text-white
                                       transition
                                       hover:bg-[#003ea8]
                                       lg:w-auto"
                            >

                                <span class="material-symbols-outlined text-[19px]">
                                    front_hand
                                </span>

                                HOLD

                            </button>

                        </div>

                    </div>

                </div>

            @endif



            {{-- ====================================================
                CURRENT HOLD
            ===================================================== --}}

                            <div
                    class="overflow-hidden rounded-xl
                           border border-[#c3c6d7]/30
                           bg-white shadow-sm"
                >

                    {{-- HEADER --}}

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
                                    front_hand
                                </span>


                                <h2
                                    class="text-base font-semibold
                                           text-[#0b1c30]"
                                >
                                    Current Hold
                                </h2>

                            </div>


                            <p class="mt-1 text-xs text-[#737686]">
                                Data container yang sedang atau pernah diproses Hold.
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
                                Total Record
                            </p>


                            <p
                                class="mt-1 text-sm
                                       font-bold
                                       text-[#004ac6]"
                            >
                                {{ count($holds ?? []) }}
                            </p>

                        </div>

                    </div>



                    {{-- TABLE --}}

                    <div class="overflow-x-auto">

                        <table class="min-w-full">

                            <thead class="bg-[#eff4ff]">

                                <tr>

                                    <th
                                        class="whitespace-nowrap
                                               px-5 py-3
                                               text-left
                                               text-[10px]
                                               font-semibold
                                               uppercase
                                               tracking-widest
                                               text-[#737686]"
                                    >
                                        No
                                    </th>


                                    <th
                                        class="whitespace-nowrap
                                               px-5 py-3
                                               text-left
                                               text-[10px]
                                               font-semibold
                                               uppercase
                                               tracking-widest
                                               text-[#737686]"
                                    >
                                        No SPK
                                    </th>


                                    <th
                                        class="whitespace-nowrap
                                               px-5 py-3
                                               text-left
                                               text-[10px]
                                               font-semibold
                                               uppercase
                                               tracking-widest
                                               text-[#737686]"
                                    >
                                        No Container
                                    </th>


                                    <th
                                        class="whitespace-nowrap
                                               px-5 py-3
                                               text-left
                                               text-[10px]
                                               font-semibold
                                               uppercase
                                               tracking-widest
                                               text-[#737686]"
                                    >
                                        No Dokumen
                                    </th>


                                    <th
                                        class="whitespace-nowrap
                                               px-5 py-3
                                               text-left
                                               text-[10px]
                                               font-semibold
                                               uppercase
                                               tracking-widest
                                               text-[#737686]"
                                    >
                                        Tanggal
                                    </th>


                                    <th
                                        class="whitespace-nowrap
                                               px-5 py-3
                                               text-left
                                               text-[10px]
                                               font-semibold
                                               uppercase
                                               tracking-widest
                                               text-[#737686]"
                                    >
                                        Jenis Dokumen
                                    </th>


                                    <th
                                        class="whitespace-nowrap
                                               px-5 py-3
                                               text-left
                                               text-[10px]
                                               font-semibold
                                               uppercase
                                               tracking-widest
                                               text-[#737686]"
                                    >
                                        Keterangan
                                    </th>


                                    <th
                                        class="whitespace-nowrap
                                               px-5 py-3
                                               text-left
                                               text-[10px]
                                               font-semibold
                                               uppercase
                                               tracking-widest
                                               text-[#737686]"
                                    >
                                        Action
                                    </th>

                                </tr>

                            </thead>


                            <tbody
                                class="divide-y
                                       divide-[#c3c6d7]/20
                                       bg-white"
                            >

                                @forelse(($holds ?? []) as $index => $hold)

                                    <tr
                                        class="transition-colors
                                               hover:bg-[#eff4ff]/50"
                                    >

                                        {{-- NO --}}

                                        <td
                                            class="whitespace-nowrap
                                                   px-5 py-4
                                                   text-xs
                                                   text-[#737686]"
                                        >
                                            {{ $index + 1 }}
                                        </td>


                                        {{-- SPK --}}

                                        <td
                                            class="whitespace-nowrap
                                                   px-5 py-4
                                                   text-sm
                                                   font-medium
                                                   text-[#0b1c30]"
                                        >
                                            {{ $hold->operation?->spk?->no_spk ?? '-' }}
                                        </td>


                                        {{-- CONTAINER --}}

                                        <td
                                            class="whitespace-nowrap
                                                   px-5 py-4
                                                   text-sm
                                                   font-semibold
                                                   text-[#0b1c30]"
                                        >
                                            {{ $hold->operation?->container?->no_cont ?? '-' }}
                                        </td>


                                        {{-- DOCUMENT --}}

                                        <td
                                            class="whitespace-nowrap
                                                   px-5 py-4
                                                   text-sm
                                                   text-[#434655]"
                                        >
                                            -
                                        </td>


                                        {{-- DATE --}}

                                        <td
                                            class="whitespace-nowrap
                                                   px-5 py-4
                                                   text-sm
                                                   text-[#434655]"
                                        >
                                            {{ $hold->action_at?->format('d-m-Y') ?? '-' }}
                                        </td>


                                        {{-- DOCUMENT TYPE --}}

                                        <td
                                            class="whitespace-nowrap
                                                   px-5 py-4
                                                   text-sm
                                                   text-[#434655]"
                                        >
                                            -
                                        </td>


                                        {{-- REASON --}}

                                        <td
                                            class="min-w-[220px]
                                                   px-5 py-4
                                                   text-sm
                                                   text-[#434655]"
                                        >
                                            {{ $hold->reason ?? '-' }}
                                        </td>


                                        {{-- ACTION --}}

                                        <td
                                            class="whitespace-nowrap
                                                   px-5 py-4"
                                        >

                                            @if(strtoupper($hold->action ?? '') === 'HOLD')

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
                                                    HOLD
                                                </span>

                                            @elseif(strtoupper($hold->action ?? '') === 'RELEASE')

                                                <button
                                                    type="button"
                                                    class="inline-flex
                                                           items-center
                                                           gap-1
                                                           rounded-lg
                                                           bg-[#004ac6]
                                                           px-3 py-1.5
                                                           text-xs
                                                           font-semibold
                                                           text-white
                                                           hover:bg-[#003ea8]"
                                                >

                                                    <span
                                                        class="material-symbols-outlined
                                                               text-[16px]"
                                                    >
                                                        lock_open
                                                    </span>

                                                    RELEASE

                                                </button>

                                            @else

                                                <span
                                                    class="inline-flex
                                                           rounded-full
                                                           bg-[#eff4ff]
                                                           px-3 py-1
                                                           text-[10px]
                                                           font-semibold
                                                           uppercase
                                                           text-[#434655]"
                                                >
                                                    {{ $hold->action ?? '-' }}
                                                </span>

                                            @endif

                                        </td>

                                    </tr>

                                @empty

                                    <tr>
                                        <td
                                            colspan="8"
                                            class="px-6 py-12 text-center"
                                        >
                                            <div
                                                class="mx-auto flex h-14 w-14
                                                       items-center justify-center
                                                       rounded-full
                                                       bg-[#eff4ff]
                                                       text-[#004ac6]"
                                            >
                                                <span class="material-symbols-outlined text-[28px]">
                                                    inventory_2
                                                </span>
                                            </div>

                                            <p
                                                class="mt-4 text-sm
                                                       font-semibold
                                                       text-[#434655]"
                                            >
                                                Belum ada data HOLD
                                            </p>

                                            <p class="mt-1 text-xs text-[#737686]">
                                                Data container yang diproses HOLD
                                                akan ditampilkan di sini.
                                            </p>
                                        </td>
                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>




            {{-- ====================================================
                NO RESULT
            ===================================================== --}}

           @if(
    ($searchCont ?? '') !== '' &&
    $messageType === 'danger' &&
    empty($containers) &&
    empty($holds)
)

                <div
                    class="rounded-xl border
                           border-[#c3c6d7]/30
                           bg-white px-6 py-12
                           text-center shadow-sm"
                >

                    <div
                        class="mx-auto flex h-14 w-14
                               items-center justify-center
                               rounded-full
                               bg-[#ffdad6]
                               text-[#ba1a1a]"
                    >

                        <span class="material-symbols-outlined text-[28px]">
                            search_off
                        </span>

                    </div>


                    <h3
                        class="mt-4 text-sm
                               font-semibold
                               text-[#434655]"
                    >
                        Container tidak ditemukan
                    </h3>


                    <p class="mt-1 text-xs text-[#737686]">
                        Silakan periksa kembali nomor container.
                    </p>

                </div>

            @endif



            {{-- ====================================================
                LOADING
            ===================================================== --}}

            <div
                wire:loading
                wire:target="search"
                class="fixed inset-0 z-50
                       flex items-center
                       justify-center
                       bg-[#0b1c30]/20
                       backdrop-blur-sm"
            >

                <div
                    class="rounded-xl border
                           border-[#c3c6d7]/30
                           bg-white px-6 py-5
                           text-center shadow-xl"
                >

                    <span
                        class="material-symbols-outlined
                               animate-spin
                               text-[32px]
                               text-[#004ac6]"
                    >
                        progress_activity
                    </span>


                    <p
                        class="mt-2 text-sm
                               font-semibold
                               text-[#434655]"
                    >
                        Searching...
                    </p>

                </div>

            </div>



            {{-- ====================================================
                FOOTER
            ===================================================== --}}

            <footer
                class="mt-6 border-t
                       border-[#c3c6d7]/30
                       pt-5 text-center"
            >

                <p class="text-xs text-[#737686]">
                    HOLD · PortOps Central
                </p>

            </footer>


        </main>

    </div>

</div>