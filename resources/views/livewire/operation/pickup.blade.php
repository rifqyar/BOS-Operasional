<div class="bg-[#f8f9ff] text-[#0b1c30] antialiased min-h-screen">

    {{-- ============================================================
        PICKUP PAGE
        Template utama PortOps Central
    ============================================================= --}}

    {{-- SIDEBAR --}}
    <nav
        class="hidden md:flex flex-col h-screen overflow-y-auto
               fixed left-0 top-0
               w-[260px]
               border-r border-[#c3c6d7]/20
               bg-[#213145]
               z-40"
    >

        {{-- BRAND --}}
        <div class="p-6 flex flex-col gap-2 border-b border-[#c3c6d7]/10">

            <h1 class="text-base font-bold text-[#dbe1ff]">
                PortOps Central
            </h1>

            <p class="text-[13px] text-[#bec6e0]">
                Terminal A-101
            </p>

        </div>


        {{-- NAVIGATION --}}
        <ul class="flex flex-col py-4 flex-1">

            {{-- Dashboard --}}
            <li>
                <a
                    href="#"
                    class="flex items-center gap-4
                           px-6 py-3
                           text-[#bec6e0]/70
                           hover:text-[#bec6e0]
                           hover:bg-[#d3e4fe]/10
                           border-l-4 border-transparent
                           transition-colors"
                >

                    <span class="material-symbols-outlined">
                        dashboard
                    </span>

                    <span class="text-xs font-semibold tracking-wider">
                        Dashboard
                    </span>

                </a>
            </li>


            {{-- Operation --}}
            <li class="mt-2">

                <div
                    class="px-6 py-2
                           text-[10px]
                           font-semibold
                           uppercase
                           tracking-widest
                           text-[#bec6e0]/50"
                >
                    Operations
                </div>

            </li>


            {{-- PICKUP ACTIVE --}}
            <li>

                <a
                    href="{{ route('operation.pickup') }}"
                    class="flex items-center gap-4
                           px-6 py-3
                           bg-[#2563eb]/10
                           border-l-4
                           border-[#004ac6]
                           text-[#dbe1ff]
                           font-bold"
                >

                    <span
                        class="material-symbols-outlined"
                        style="font-variation-settings: 'FILL' 1;"
                    >
                        local_shipping
                    </span>

                    <span class="text-xs font-semibold tracking-wider">
                        PICKUP
                    </span>

                </a>

            </li>


            {{-- Behandle In --}}
            <li>

                <a
                    href="#"
                    class="flex items-center gap-4
                           px-6 py-3
                           text-[#bec6e0]/70
                           hover:text-[#bec6e0]
                           hover:bg-[#d3e4fe]/10
                           border-l-4 border-transparent
                           transition-colors"
                >

                    <span class="material-symbols-outlined">
                        move_to_inbox
                    </span>

                    <span class="text-xs font-semibold tracking-wider">
                        BEHANDLE IN
                    </span>

                </a>

            </li>


            {{-- Hold --}}
            <li>

                <a
                    href="#"
                    class="flex items-center gap-4
                           px-6 py-3
                           text-[#bec6e0]/70
                           hover:text-[#bec6e0]
                           hover:bg-[#d3e4fe]/10
                           border-l-4 border-transparent
                           transition-colors"
                >

                    <span class="material-symbols-outlined">
                        front_hand
                    </span>

                    <span class="text-xs font-semibold tracking-wider">
                        HOLD
                    </span>

                </a>

            </li>


            {{-- Marshalling --}}
            <li>

                <a
                    href="#"
                    class="flex items-center gap-4
                           px-6 py-3
                           text-[#bec6e0]/70
                           hover:text-[#bec6e0]
                           hover:bg-[#d3e4fe]/10
                           border-l-4 border-transparent
                           transition-colors"
                >

                    <span class="material-symbols-outlined">
                        warehouse
                    </span>

                    <span class="text-xs font-semibold tracking-wider">
                        MARSHALLING
                    </span>

                </a>

            </li>


            {{-- Inspection --}}
            <li>

                <a
                    href="#"
                    class="flex items-center gap-4
                           px-6 py-3
                           text-[#bec6e0]/70
                           hover:text-[#bec6e0]
                           hover:bg-[#d3e4fe]/10
                           border-l-4 border-transparent
                           transition-colors"
                >

                    <span class="material-symbols-outlined">
                        fact_check
                    </span>

                    <span class="text-xs font-semibold tracking-wider">
                        INSPECTION
                    </span>

                </a>

            </li>


            {{-- Spacer --}}
            <li class="mt-auto">

                <a
                    href="#"
                    class="flex items-center gap-4
                           px-6 py-3
                           text-[#bec6e0]/70
                           hover:text-[#bec6e0]
                           hover:bg-[#d3e4fe]/10
                           border-l-4 border-transparent
                           transition-colors"
                >

                    <span class="material-symbols-outlined">
                        logout
                    </span>

                    <span class="text-xs font-semibold tracking-wider">
                        Logout
                    </span>

                </a>

            </li>

        </ul>

    </nav>


    {{-- ============================================================
        MAIN CONTENT
    ============================================================= --}}

    <div class="md:ml-[260px] min-h-screen flex flex-col">


        {{-- TOP BAR --}}
        <header
            class="flex justify-between items-center
                   w-full
                   px-6
                   h-10
                   bg-[#f8f9ff]
                   border-b border-[#c3c6d7]/30
                   sticky top-0
                   z-30"
        >

            {{-- SEARCH / BRAND --}}
            <div class="flex items-center gap-6">

                <span
                    class="text-base font-black text-[#0b1c30] md:hidden"
                >
                    PortOps Central
                </span>


                <div class="relative hidden sm:block">

                    <span
                        class="material-symbols-outlined
                               absolute left-2
                               top-1/2
                               -translate-y-1/2
                               text-[#434655]
                               text-[18px]"
                    >
                        search
                    </span>

                    <input
                        type="text"
                        placeholder="Search operations..."
                        class="pl-8 pr-4 py-1
                               text-[13px]
                               bg-[#eff4ff]
                               border border-[#c3c6d7]/50
                               rounded
                               focus:border-[#004ac6]
                               focus:ring-1
                               focus:ring-[#004ac6]
                               outline-none
                               w-64"
                    >

                </div>

            </div>


            {{-- ACTIONS --}}
            <div class="flex items-center gap-4">

                <button
                    type="button"
                    class="p-1
                           text-[#434655]
                           hover:bg-[#eff4ff]
                           rounded"
                >

                    <span class="material-symbols-outlined text-[20px]">
                        notifications
                    </span>

                </button>


                <button
                    type="button"
                    class="p-1
                           text-[#434655]
                           hover:bg-[#eff4ff]
                           rounded hidden sm:block"
                >

                    <span class="material-symbols-outlined text-[20px]">
                        terminal
                    </span>

                </button>


                <button
                    type="button"
                    class="p-1
                           text-[#434655]
                           hover:bg-[#eff4ff]
                           rounded hidden sm:block"
                >

                    <span class="material-symbols-outlined text-[20px]">
                        help_outline
                    </span>

                </button>


                <div class="h-6 w-px bg-[#c3c6d7]/30"></div>


                <div
                    class="w-8 h-8
                           rounded-full
                           bg-[#2563eb]
                           flex items-center justify-center
                           border border-[#c3c6d7]/20"
                >

                    <span
                        class="material-symbols-outlined
                               text-white
                               text-[18px]"
                    >
                        person
                    </span>

                </div>

            </div>

        </header>


        {{-- ========================================================
            CANVAS
        ========================================================= --}}

        <main
            class="flex-1
                   overflow-y-auto
                   p-6
                   bg-[#f8f9ff]"
        >


            {{-- PAGE HEADER --}}
            <div class="mb-6">

                <div class="flex items-center gap-2 mb-2">

                    <span
                        class="material-symbols-outlined
                               text-[#004ac6]
                               text-[22px]"
                    >
                        local_shipping
                    </span>

                    <h2
                        class="text-2xl
                               font-semibold
                               tracking-tight
                               text-[#0b1c30]"
                    >
                        PICK UP
                    </h2>

                </div>

                <p class="text-sm text-[#434655]">
                    Search No SPK untuk melihat container terkait.
                </p>

            </div>


            {{-- ====================================================
                SEARCH CARD
            ===================================================== --}}

            <div
                class="bg-white
                       border border-[#c3c6d7]/30
                       rounded-xl
                       shadow-sm
                       p-6
                       mb-6"
            >

                <div class="flex items-center gap-3 mb-5">

                    <div
                        class="w-10 h-10
                               rounded-lg
                               bg-[#d3e4fe]
                               flex items-center justify-center
                               text-[#004ac6]"
                    >

                        <span class="material-symbols-outlined">
                            search
                        </span>

                    </div>


                    <div>

                        <h3
                            class="text-base
                                   font-semibold
                                   text-[#0b1c30]"
                        >
                            Search SPK
                        </h3>

                        <p class="text-xs text-[#434655]">
                            Masukkan nomor SPK untuk mencari data container.
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

                        <div class="w-full sm:max-w-md">

                            <label
                                for="searchSpk"
                                class="block
                                       mb-2
                                       text-xs
                                       font-semibold
                                       uppercase
                                       tracking-wider
                                       text-[#434655]"
                            >
                                No SPK
                            </label>


                            <div class="relative">

                                <span
                                    class="material-symbols-outlined
                                           absolute
                                           left-3
                                           top-1/2
                                           -translate-y-1/2
                                           text-[#737686]
                                           text-[20px]"
                                >
                                    confirmation_number
                                </span>


                                <input
                                    id="searchSpk"
                                    type="text"
                                    wire:model="searchSpk"
                                    autofocus
                                    autocomplete="off"
                                    placeholder="SEARCH NO SPK"
                                    class="w-full
                                           rounded-lg
                                           border
                                           border-[#c3c6d7]
                                           bg-white
                                           pl-10
                                           pr-4
                                           py-2.5
                                           text-sm
                                           text-[#0b1c30]
                                           placeholder:text-[#737686]
                                           focus:border-[#004ac6]
                                           focus:outline-none
                                           focus:ring-2
                                           focus:ring-[#b4c5ff]"
                                >

                            </div>


                            @error('searchSpk')

                                <p class="mt-2 text-xs text-[#ba1a1a]">
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
                                   px-5
                                   py-2.5
                                   text-sm
                                   font-semibold
                                   text-white
                                   hover:bg-[#003ea8]
                                   focus:outline-none
                                   focus:ring-2
                                   focus:ring-[#b4c5ff]
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
                                class="material-symbols-outlined text-[19px]"
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

                    </div>

                </form>

            </div>


            {{-- ====================================================
                MESSAGE
            ===================================================== --}}

            @if($pickupMessage)

                <div
                    class="mb-6
                           rounded-lg
                           border
                           px-4
                           py-3
                           flex
                           items-start
                           gap-3

                           @if($pickupMessageType === 'success')
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

                        @if($pickupMessageType === 'success')
                            check_circle
                        @else
                            error
                        @endif

                    </span>


                    <div>

                        <p class="text-sm font-semibold">
                            {{ $pickupMessage }}
                        </p>

                    </div>

                </div>

            @endif


            {{-- ====================================================
                RESULT
            ===================================================== --}}

            @if($spk)

                <div
                    class="bg-white
                           border border-[#c3c6d7]/30
                           rounded-xl
                           shadow-sm
                           overflow-hidden"
                >


                    {{-- RESULT HEADER --}}
                    <div
                        class="px-6
                               py-5
                               border-b border-[#c3c6d7]/30
                               flex
                               flex-col
                               gap-4
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
                                    description
                                </span>

                                <h3
                                    class="text-base
                                           font-semibold
                                           text-[#0b1c30]"
                                >
                                    SPK Information
                                </h3>

                            </div>


                            <p class="mt-1 text-xs text-[#737686]">
                                Container yang terdaftar pada SPK.
                            </p>

                        </div>


                        {{-- SPK NUMBER --}}
                        <div
                            class="rounded-lg
                                   bg-[#eff4ff]
                                   border border-[#c3c6d7]/30
                                   px-4
                                   py-3"
                        >

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
                                class="mt-1
                                       text-sm
                                       font-bold
                                       text-[#004ac6]"
                            >
                                {{ $spk->no_spk }}
                            </p>

                        </div>

                    </div>


                    {{-- SUMMARY --}}
                    <div
                        class="grid
                               grid-cols-1
                               sm:grid-cols-3
                               border-b
                               border-[#c3c6d7]/30"
                    >

                        <div class="p-5">

                            <p
                                class="text-[10px]
                                       uppercase
                                       tracking-widest
                                       font-semibold
                                       text-[#737686]"
                            >
                                Total Container
                            </p>

                            <p
                                class="mt-1
                                       text-xl
                                       font-semibold
                                       text-[#0b1c30]"
                            >
                                {{ $containers->count() }}
                            </p>

                        </div>


                        <div
                            class="p-5
                                   border-t
                                   sm:border-t-0
                                   sm:border-l
                                   border-[#c3c6d7]/30"
                        >

                            <p
                                class="text-[10px]
                                       uppercase
                                       tracking-widest
                                       font-semibold
                                       text-[#737686]"
                            >
                                SPK Status
                            </p>

                            <p
                                class="mt-1
                                       text-sm
                                       font-semibold
                                       text-[#0b1c30]"
                            >
                                {{ $spk->status ?? '-' }}
                            </p>

                        </div>


                        <div
                            class="p-5
                                   border-t
                                   sm:border-t-0
                                   sm:border-l
                                   border-[#c3c6d7]/30"
                        >

                            <p
                                class="text-[10px]
                                       uppercase
                                       tracking-widest
                                       font-semibold
                                       text-[#737686]"
                            >
                                Document
                            </p>

                            <p
                                class="mt-1
                                       text-sm
                                       font-semibold
                                       text-[#0b1c30]"
                            >
                                {{ $spk->no_dok ?? '-' }}
                            </p>

                        </div>

                    </div>


                    {{-- =================================================
                        CONTAINER TABLE
                    ================================================== --}}

                    <div class="overflow-x-auto">

                        <table class="min-w-full">

                            <thead class="bg-[#eff4ff]">

                                <tr>

                                    <th
                                        class="px-6
                                               py-3
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
                                        class="px-6
                                               py-3
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
                                        class="px-6
                                               py-3
                                               text-left
                                               text-[10px]
                                               font-semibold
                                               uppercase
                                               tracking-widest
                                               text-[#737686]"
                                    >
                                        Ukuran
                                    </th>


                                    <th
                                        class="px-6
                                               py-3
                                               text-left
                                               text-[10px]
                                               font-semibold
                                               uppercase
                                               tracking-widest
                                               text-[#737686]"
                                    >
                                        Type
                                    </th>


                                    <th
                                        class="px-6
                                               py-3
                                               text-left
                                               text-[10px]
                                               font-semibold
                                               uppercase
                                               tracking-widest
                                               text-[#737686]"
                                    >
                                        Status
                                    </th>


                                    <th
                                        class="px-6
                                               py-3
                                               text-left
                                               text-[10px]
                                               font-semibold
                                               uppercase
                                               tracking-widest
                                               text-[#737686]"
                                    >
                                        Send NPCT1
                                    </th>


                                    {{-- BARU --}}
                                    <th
                                        class="px-6
                                               py-3
                                               text-left
                                               text-[10px]
                                               font-semibold
                                               uppercase
                                               tracking-widest
                                               text-[#737686]"
                                    >
                                        Current Process
                                    </th>


                                    {{-- BARU --}}
                                    <th
                                        class="px-6
                                               py-3
                                               text-left
                                               text-[10px]
                                               font-semibold
                                               uppercase
                                               tracking-widest
                                               text-[#737686]"
                                    >
                                        Operation Status
                                    </th>

                                </tr>

                            </thead>


                            <tbody
                                class="divide-y
                                       divide-[#c3c6d7]/20
                                       bg-white"
                            >

@forelse($containers as $index => $item)

    @php
        $operation = $operations->get($item->container_id);
        $pickup = $operation?->pickup;
    @endphp

    {{-- MAIN CONTAINER ROW --}}
    <tr
        class="hover:bg-[#eff4ff]/50
               transition-colors"
    >

        {{-- NUMBER --}}
        <td
            class="px-6
                   py-4
                   text-xs
                   font-medium
                   text-[#737686]"
        >
            {{ $index + 1 }}
        </td>


        {{-- CONTAINER --}}
        <td
            class="px-6
                   py-4
                   whitespace-nowrap"
        >

            <div class="flex items-center gap-3">

                <div
                    class="w-8
                           h-8
                           rounded-lg
                           bg-[#d3e4fe]
                           flex
                           items-center
                           justify-center
                           text-[#004ac6]"
                >

                    <span
                        class="material-symbols-outlined text-[18px]"
                    >
                        inventory_2
                    </span>

                </div>

                <span
                    class="text-sm
                           font-semibold
                           text-[#0b1c30]"
                >
                    {{ $item->container?->no_cont ?? '-' }}
                </span>

            </div>

        </td>


        {{-- UKURAN --}}
        <td
            class="px-6
                   py-4
                   whitespace-nowrap
                   text-sm
                   text-[#434655]"
        >
            {{ $item->container?->type?->size ?? '-' }}
        </td>


        {{-- TYPE --}}
        <td
            class="px-6
                   py-4
                   whitespace-nowrap
                   text-sm
                   text-[#434655]"
        >
            {{ $item->container?->type?->name ?? '-' }}
        </td>


        {{-- SPK CONTAINER STATUS --}}
        <td class="px-6 py-4">

            @if($item->status)

                <span
                    class="inline-flex
                           items-center
                           rounded-full
                           bg-[#eff4ff]
                           border
                           border-[#c3c6d7]/40
                           px-3
                           py-1
                           text-[10px]
                           font-semibold
                           uppercase
                           tracking-wider
                           text-[#434655]"
                >
                    {{ $item->status }}
                </span>

            @else

                <span class="text-xs text-[#737686]">
                    -
                </span>

            @endif

        </td>


        {{-- SEND NPCT1 --}}
        <td class="px-6 py-4">

            @if(
                $item->fl_send_npct1 === '1' ||
                $item->fl_send_npct1 === 'Y'
            )

                <span
                    class="inline-flex
                           items-center
                           gap-1.5
                           rounded-full
                           bg-[#6ffbbe]/20
                           border
                           border-[#4edea3]/40
                           px-3
                           py-1
                           text-[10px]
                           font-semibold
                           text-[#005236]"
                >

                    <span
                        class="material-symbols-outlined text-[14px]"
                    >
                        check_circle
                    </span>

                    SENT

                </span>

            @else

                <span
                    class="inline-flex
                           items-center
                           gap-1.5
                           rounded-full
                           bg-[#d3e4fe]/60
                           border
                           border-[#c3c6d7]/40
                           px-3
                           py-1
                           text-[10px]
                           font-semibold
                           text-[#565e74]"
                >

                    <span
                        class="material-symbols-outlined text-[14px]"
                    >
                        schedule
                    </span>

                    NOT SENT

                </span>

            @endif

        </td>


        {{-- CURRENT PROCESS --}}
        <td class="px-6 py-4">

            @if($operation?->current_process)

                <span
                    class="inline-flex
                           items-center
                           rounded-full
                           bg-[#d3e4fe]
                           border
                           border-[#b4c5ff]
                           px-3
                           py-1
                           text-[10px]
                           font-semibold
                           uppercase
                           tracking-wider
                           text-[#004ac6]"
                >
                    {{ str_replace('_', ' ', $operation->current_process) }}
                </span>

            @else

                <span class="text-xs text-[#737686]">
                    NO OPERATION
                </span>

            @endif

        </td>


        {{-- OPERATION STATUS --}}
        <td class="px-6 py-4">

            @if($operation?->status)

                <span
                    class="inline-flex
                           items-center
                           rounded-full
                           bg-[#eff4ff]
                           border
                           border-[#c3c6d7]/40
                           px-3
                           py-1
                           text-[10px]
                           font-semibold
                           uppercase
                           tracking-wider
                           text-[#434655]"
                >
                    {{ $operation->status }}
                </span>

            @else

                <span class="text-xs text-[#737686]">
                    -
                </span>

            @endif

        </td>

    </tr>


    {{-- =========================================================
        PICKUP DETAIL
    ========================================================== --}}

    @if($operation)

        <tr class="bg-[#f8f9ff]">

            <td
                colspan="8"
                class="px-6 py-4"
            >

                <div
                    class="rounded-lg
                           border
                           border-[#c3c6d7]/30
                           bg-white
                           p-4"
                >

                    <div
                        class="flex
                               items-center
                               gap-2
                               mb-4"
                    >

                        <span
                            class="material-symbols-outlined
                                   text-[#004ac6]
                                   text-[20px]"
                        >
                            local_shipping
                        </span>

                        <div>

                            <h4
                                class="text-sm
                                       font-semibold
                                       text-[#0b1c30]"
                            >
                                Pickup Information
                            </h4>

                            <p
                                class="text-[11px]
                                       text-[#737686]"
                            >
                                Detail pickup untuk operation
                                #{{ $operation->id }}
                            </p>

                        </div>

                    </div>


                    <div
                        class="grid
                               grid-cols-1
                               sm:grid-cols-3
                               gap-4"
                    >

                        {{-- PICKUP STATUS --}}
                        <div
                            class="rounded-lg
                                   border
                                   border-[#c3c6d7]/30
                                   bg-[#f8f9ff]
                                   p-4"
                        >

                            <p
                                class="text-[10px]
                                       font-semibold
                                       uppercase
                                       tracking-widest
                                       text-[#737686]"
                            >
                                Pickup Status
                            </p>

                            <p
                                class="mt-2
                                       text-sm
                                       font-semibold
                                       text-[#0b1c30]"
                            >
                                {{ $pickup?->status ?? '-' }}
                            </p>

                        </div>


                        {{-- PICKUP AT --}}
                        <div
                            class="rounded-lg
                                   border
                                   border-[#c3c6d7]/30
                                   bg-[#f8f9ff]
                                   p-4"
                        >

                            <p
                                class="text-[10px]
                                       font-semibold
                                       uppercase
                                       tracking-widest
                                       text-[#737686]"
                            >
                                Pickup At
                            </p>

                            <p
                                class="mt-2
                                       text-sm
                                       font-semibold
                                       text-[#0b1c30]"
                            >

                                @if($pickup?->pickup_at)

                                    {{ $pickup->pickup_at->format('d-m-Y H:i:s') }}

                                @else

                                    -

                                @endif

                            </p>

                        </div>


                        {{-- TRUCK --}}
                        <div
                            class="rounded-lg
                                   border
                                   border-[#c3c6d7]/30
                                   bg-[#f8f9ff]
                                   p-4"
                        >

                            <p
                                class="text-[10px]
                                       font-semibold
                                       uppercase
                                       tracking-widest
                                       text-[#737686]"
                            >
                                Truck ID
                            </p>

                            <p
                                class="mt-2
                                       text-sm
                                       font-semibold
                                       text-[#0b1c30]"
                            >
                                {{ $pickup?->truck?->id ?? '-' }}
                            </p>

                        </div>

                    </div>

                </div>

            </td>

        </tr>

    @endif

@empty

    <tr>

        <td
            colspan="8"
            class="px-6
                   py-12
                   text-center"
        >

            <span
                class="material-symbols-outlined
                       text-[#737686]
                       text-[36px]"
            >
                inventory_2
            </span>

            <p
                class="mt-2
                       text-sm
                       font-medium
                       text-[#434655]"
            >
                Tidak ada container.
            </p>

        </td>

    </tr>

@endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            @endif


            {{-- FOOTER --}}
            <div
                class="mt-6
                       border-t
                       border-[#c3c6d7]/30
                       pt-5
                       text-center"
            >

                <p class="text-xs text-[#737686]">
                    PICK UP · PortOps Central
                </p>

            </div>


        </main>

    </div>

</div>


{{-- ================================================================
    MATERIAL SYMBOLS
================================================================= --}}