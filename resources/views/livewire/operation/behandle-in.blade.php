@props(['message' => null, 'messageType' => null, 'containers' => [], 'searchCont' => ''])

<div class="bg-[#f8f9ff] text-[#0b1c30] antialiased min-h-screen">


    {{-- ============================================================
        BEHANDLE IN PAGE
        Template utama PortOps Central
    ============================================================= --}}


    {{-- ============================================================
        SIDEBAR
    ============================================================= --}}

    <nav
        class="hidden md:flex
               flex-col
               h-screen
               overflow-y-auto
               fixed
               left-0
               top-0
               w-[260px]
               border-r
               border-[#c3c6d7]/20
               bg-[#213145]
               z-40"
    >


        {{-- BRAND --}}

        <div
            class="p-6
                   flex
                   flex-col
                   gap-2
                   border-b
                   border-[#c3c6d7]/10"
        >

            <h1 class="text-base font-bold text-[#dbe1ff]">
                PortOps Central
            </h1>


            <p class="text-[13px] text-[#bec6e0]">
                Terminal A-101
            </p>

        </div>



        {{-- NAVIGATION --}}

        <ul class="flex flex-col py-4 flex-1">


            {{-- DASHBOARD --}}

            <li>

                <a
                    href="{{ route('home') }}"
                    class="flex
                           items-center
                           gap-4
                           px-6
                           py-3
                           text-[#bec6e0]/70
                           hover:text-[#bec6e0]
                           hover:bg-[#d3e4fe]/10
                           border-l-4
                           border-transparent
                           transition-colors"
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

            </li>



            {{-- OPERATIONS HEADER --}}

            <li class="mt-2">

                <div
                    class="px-6
                           py-2
                           text-[10px]
                           font-semibold
                           uppercase
                           tracking-widest
                           text-[#bec6e0]/50"
                >
                    Operations
                </div>

            </li>



            {{-- PICKUP --}}

            <li>

                <a
                    href="{{ route('operation.pickup') }}"
                    class="flex
                           items-center
                           gap-4
                           px-6
                           py-3
                           text-[#bec6e0]/70
                           hover:text-[#bec6e0]
                           hover:bg-[#d3e4fe]/10
                           border-l-4
                           border-transparent
                           transition-colors"
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

            </li>



            {{-- BEHANDLE IN ACTIVE --}}

            <li>

                <a
                    href="{{ route('operation.behandle-in') }}"
                    class="flex
                           items-center
                           gap-4
                           px-6
                           py-3
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

            </li>



            {{-- HOLD --}}

            <li>

                <a
                    href="#"
                    class="flex
                           items-center
                           gap-4
                           px-6
                           py-3
                           text-[#bec6e0]/70
                           hover:text-[#bec6e0]
                           hover:bg-[#d3e4fe]/10
                           border-l-4
                           border-transparent
                           transition-colors"
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

            </li>



            {{-- MARSHALLING --}}

            <li>

                <a
                    href="#"
                    class="flex
                           items-center
                           gap-4
                           px-6
                           py-3
                           text-[#bec6e0]/70
                           hover:text-[#bec6e0]
                           hover:bg-[#d3e4fe]/10
                           border-l-4
                           border-transparent
                           transition-colors"
                >

                    <span class="material-symbols-outlined">
                        warehouse
                    </span>


                    <span
                        class="text-xs
                               font-semibold
                               tracking-wider"
                    >
                        MARSHALLING
                    </span>

                </a>

            </li>



            {{-- INSPECTION --}}

            <li>

                <a
                    href="#"
                    class="flex
                           items-center
                           gap-4
                           px-6
                           py-3
                           text-[#bec6e0]/70
                           hover:text-[#bec6e0]
                           hover:bg-[#d3e4fe]/10
                           border-l-4
                           border-transparent
                           transition-colors"
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

            </li>



            {{-- REEFER --}}

            <li>

                <a
                    href="#"
                    class="flex
                           items-center
                           gap-4
                           px-6
                           py-3
                           text-[#bec6e0]/70
                           hover:text-[#bec6e0]
                           hover:bg-[#d3e4fe]/10
                           border-l-4
                           border-transparent
                           transition-colors"
                >

                    <span class="material-symbols-outlined">
                        ac_unit
                    </span>


                    <span
                        class="text-xs
                               font-semibold
                               tracking-wider"
                    >
                        REEFER
                    </span>

                </a>

            </li>



            {{-- DELIVERY --}}

            <li>

                <a
                    href="#"
                    class="flex
                           items-center
                           gap-4
                           px-6
                           py-3
                           text-[#bec6e0]/70
                           hover:text-[#bec6e0]
                           hover:bg-[#d3e4fe]/10
                           border-l-4
                           border-transparent
                           transition-colors"
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

            </li>



            {{-- SPACER --}}

            <li class="mt-auto">

                <a
                    href="#"
                    class="flex
                           items-center
                           gap-4
                           px-6
                           py-3
                           text-[#bec6e0]/70
                           hover:text-[#bec6e0]
                           hover:bg-[#d3e4fe]/10
                           border-l-4
                           border-transparent
                           transition-colors"
                >

                    <span class="material-symbols-outlined">
                        logout
                    </span>


                    <span
                        class="text-xs
                               font-semibold
                               tracking-wider"
                    >
                        Logout
                    </span>

                </a>

            </li>

        </ul>

    </nav>



    {{-- ============================================================
        MAIN CONTENT
    ============================================================= --}}

    <div
        class="md:ml-[260px]
               min-h-screen
               flex
               flex-col"
    >


        {{-- ========================================================
            TOP BAR
        ========================================================= --}}

        <header
            class="flex
                   justify-between
                   items-center
                   w-full
                   px-6
                   h-10
                   bg-[#f8f9ff]
                   border-b
                   border-[#c3c6d7]/30
                   sticky
                   top-0
                   z-30"
        >


            {{-- SEARCH / BRAND --}}

            <div class="flex items-center gap-6">

                <span
                    class="text-base
                           font-black
                           text-[#0b1c30]
                           md:hidden"
                >
                    PortOps Central
                </span>


                <div class="relative hidden sm:block">

                    <span
                        class="material-symbols-outlined
                               absolute
                               left-2
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
                        class="pl-8
                               pr-4
                               py-1
                               text-[13px]
                               bg-[#eff4ff]
                               border
                               border-[#c3c6d7]/50
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

                    <span
                        class="material-symbols-outlined
                               text-[20px]"
                    >
                        notifications
                    </span>

                </button>


                <button
                    type="button"
                    class="p-1
                           text-[#434655]
                           hover:bg-[#eff4ff]
                           rounded
                           hidden sm:block"
                >

                    <span
                        class="material-symbols-outlined
                               text-[20px]"
                    >
                        terminal
                    </span>

                </button>


                <button
                    type="button"
                    class="p-1
                           text-[#434655]
                           hover:bg-[#eff4ff]
                           rounded
                           hidden sm:block"
                >

                    <span
                        class="material-symbols-outlined
                               text-[20px]"
                    >
                        help_outline
                    </span>

                </button>


                <div
                    class="h-6
                           w-px
                           bg-[#c3c6d7]/30"
                ></div>


                <div
                    class="w-8
                           h-8
                           rounded-full
                           bg-[#2563eb]
                           flex
                           items-center
                           justify-center
                           border
                           border-[#c3c6d7]/20"
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
                   p-4
                   sm:p-6
                   bg-[#f8f9ff]"
        >


            {{-- ====================================================
                PAGE HEADER
            ===================================================== --}}

            <div class="mb-6">

                <div
                    class="flex
                           items-center
                           gap-2
                           mb-2"
                >

                    <span
                        class="material-symbols-outlined
                               text-[#004ac6]
                               text-[22px]"
                    >
                        move_to_inbox
                    </span>


                    <h2
                        class="text-2xl
                               font-semibold
                               tracking-tight
                               text-[#0b1c30]"
                    >
                        BEHANDLE IN
                    </h2>

                </div>


                <p class="text-sm text-[#434655]">
                    Search No Container untuk melihat data Behandle In.
                </p>

            </div>



            {{-- ====================================================
                SEARCH CARD
            ===================================================== --}}

            <div
                class="bg-white
                       border
                       border-[#c3c6d7]/30
                       rounded-xl
                       shadow-sm
                       p-5
                       sm:p-6
                       mb-6"
            >


                {{-- SEARCH CARD HEADER --}}

                <div
                    class="flex
                           items-center
                           gap-3
                           mb-5"
                >

                    <div
                        class="w-10
                               h-10
                               rounded-lg
                               bg-[#d3e4fe]
                               flex
                               items-center
                               justify-center
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
                            Search Container
                        </h3>


                        <p
                            class="text-xs
                                   text-[#434655]"
                        >
                            Masukkan nomor container untuk mencari data.
                        </p>

                    </div>

                </div>



                {{-- SEARCH FORM --}}

                <form wire:submit="search">

                    <div
                        class="flex
                               flex-col
                               gap-3
                               sm:flex-row
                               sm:items-end"
                    >


                        {{-- INPUT --}}

                        <div
                            class="w-full
                                   sm:max-w-md"
                        >

                            <label
                                for="searchCont"
                                class="block
                                       mb-2
                                       text-xs
                                       font-semibold
                                       uppercase
                                       tracking-wider
                                       text-[#434655]"
                            >
                                No Container
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
                                    inventory_2
                                </span>


                                <input
                                    id="searchCont"
                                    type="text"
                                    wire:model="searchCont"
                                    autofocus
                                    autocomplete="off"
                                    placeholder="SEARCH NO CONT"
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


                            @error('searchCont')

                                <p
                                    class="mt-2
                                           text-xs
                                           text-[#ba1a1a]"
                                >
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>



                        {{-- SEARCH BUTTON --}}

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
                                class="material-symbols-outlined
                                       text-[19px]"
                            >
                                search
                            </span>


                            <span
                                wire:loading
                                wire:target="search"
                                class="material-symbols-outlined
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



                        {{-- RESET BUTTON --}}

                        @if($searchCont || count($containers) > 0)

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
                                       px-5
                                       py-2.5
                                       text-sm
                                       font-semibold
                                       text-[#434655]
                                       hover:bg-[#eff4ff]
                                       focus:outline-none
                                       focus:ring-2
                                       focus:ring-[#b4c5ff]"
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
                    class="mb-6
                           rounded-lg
                           border
                           px-4
                           py-3
                           flex
                           items-start
                           gap-3

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


                    <div>

                        <p class="text-sm font-semibold">
                            {{ $message }}
                        </p>

                    </div>

                </div>

            @endif
            @endisset



            {{-- ====================================================
                RESULT
            ===================================================== --}}

            @if(count($containers) > 0)

                <div
                    class="bg-white
                           border
                           border-[#c3c6d7]/30
                           rounded-xl
                           shadow-sm
                           overflow-hidden"
                >


                    {{-- RESULT HEADER --}}

                    <div
                        class="px-5
                               sm:px-6
                               py-5
                               border-b
                               border-[#c3c6d7]/30
                               flex
                               flex-col
                               gap-4
                               md:flex-row
                               md:items-center
                               md:justify-between"
                    >

                        <div>

                            <div
                                class="flex
                                       items-center
                                       gap-2"
                            >

                                <span
                                    class="material-symbols-outlined
                                           text-[#004ac6]"
                                >
                                    inventory_2
                                </span>


                                <h3
                                    class="text-base
                                           font-semibold
                                           text-[#0b1c30]"
                                >
                                    Container Information
                                </h3>

                            </div>


                            <p
                                class="mt-1
                                       text-xs
                                       text-[#737686]"
                            >
                                Data container hasil pencarian.
                            </p>

                        </div>


                        {{-- TOTAL --}}

                        <div
                            class="rounded-lg
                                   bg-[#eff4ff]
                                   border
                                   border-[#c3c6d7]/30
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
                                Total Container
                            </p>


                            <p
                                class="mt-1
                                       text-sm
                                       font-bold
                                       text-[#004ac6]"
                            >
                                {{ count($containers) }}
                            </p>

                        </div>

                    </div>



                    {{-- =================================================
                        CONTAINER TABLE
                    ================================================== --}}

                    <div class="overflow-x-auto">

                        <table class="min-w-full">


                            {{-- TABLE HEADER --}}

                            <thead class="bg-[#eff4ff]">

                                <tr>

                                    <th
                                        class="px-5
                                               sm:px-6
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
                                        class="px-5
                                               sm:px-6
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
                                        class="px-5
                                               sm:px-6
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
                                        class="px-5
                                               sm:px-6
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
                                        class="px-5
                                               sm:px-6
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

                                </tr>

                            </thead>



                            {{-- TABLE BODY --}}

                            <tbody
                                class="divide-y
                                       divide-[#c3c6d7]/20
                                       bg-white"
                            >

                                @foreach($containers as $index => $item)

                                    <tr
                                        class="hover:bg-[#eff4ff]/50
                                               transition-colors"
                                    >


                                        {{-- NUMBER --}}

                                        <td
                                            class="px-5
                                                   sm:px-6
                                                   py-4
                                                   text-xs
                                                   font-medium
                                                   text-[#737686]"
                                        >
                                            {{ $index + 1 }}
                                        </td>



                                        {{-- CONTAINER --}}

                                        <td
                                            class="px-5
                                                   sm:px-6
                                                   py-4
                                                   whitespace-nowrap"
                                        >

                                            <div
                                                class="flex
                                                       items-center
                                                       gap-3"
                                            >

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
                                                        class="material-symbols-outlined
                                                               text-[18px]"
                                                    >
                                                        inventory_2
                                                    </span>

                                                </div>


                                                <span
                                                    class="text-sm
                                                           font-semibold
                                                           text-[#0b1c30]"
                                                >
                                                    {{ $item->no_cont ?? '-' }}
                                                </span>

                                            </div>

                                        </td>



                                        {{-- UKURAN --}}

                                        <td
                                            class="px-5
                                                   sm:px-6
                                                   py-4
                                                   whitespace-nowrap
                                                   text-sm
                                                   text-[#434655]"
                                        >
                                            {{ $item->type?->size ?? '-' }}
                                        </td>



                                        {{-- TYPE --}}

                                        <td
                                            class="px-5
                                                   sm:px-6
                                                   py-4
                                                   whitespace-nowrap
                                                   text-sm
                                                   text-[#434655]"
                                        >
                                            {{ $item->type?->name ?? '-' }}
                                        </td>



                                        {{-- STATUS --}}

                                        <td
                                            class="px-5
                                                   sm:px-6
                                                   py-4"
                                        >

                                            <span
                                                class="inline-flex
                                                       items-center
                                                       gap-1.5
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

                                                <span
                                                    class="material-symbols-outlined
                                                           text-[14px]"
                                                >
                                                    info
                                                </span>

                                                FOUND

                                            </span>

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

            @endif



            {{-- ====================================================
                EMPTY RESULT
            ===================================================== --}}

            @if(
                $searchCont &&
                $messageType === 'danger' &&
                count($containers) === 0
            )

                <div
                    class="bg-white
                           border
                           border-[#c3c6d7]/30
                           rounded-xl
                           shadow-sm
                           px-6
                           py-12
                           text-center"
                >

                    <div
                        class="mx-auto
                               w-12
                               h-12
                               rounded-full
                               bg-[#ffdad6]
                               flex
                               items-center
                               justify-center
                               text-[#ba1a1a]"
                    >

                        <span
                            class="material-symbols-outlined"
                        >
                            search_off
                        </span>

                    </div>


                    <p
                        class="mt-4
                               text-sm
                               font-semibold
                               text-[#434655]"
                    >
                        Container tidak ditemukan.
                    </p>


                    <p
                        class="mt-1
                               text-xs
                               text-[#737686]"
                    >
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
                class="fixed
                       inset-0
                       z-50
                       flex
                       items-center
                       justify-center
                       bg-[#0b1c30]/20
                       backdrop-blur-sm"
            >

                <div
                    class="rounded-xl
                           border
                           border-[#c3c6d7]/30
                           bg-white
                           px-6
                           py-5
                           text-center
                           shadow-xl"
                >

                    <span
                        class="material-symbols-outlined
                               animate-spin
                               text-[#004ac6]
                               text-[32px]"
                    >
                        progress_activity
                    </span>


                    <p
                        class="mt-2
                               text-sm
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

            <div
                class="mt-6
                       border-t
                       border-[#c3c6d7]/30
                       pt-5
                       text-center"
            >

                <p class="text-xs text-[#737686]">
                    BEHANDLE IN · PortOps Central
                </p>

            </div>


        </main>

    </div>

</div>