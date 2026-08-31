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


        <nav class="flex flex-1 flex-col py-4">

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

                <span class="text-xs font-semibold tracking-wider">
                    Dashboard
                </span>

            </a>


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


            @php

                $menusBefore = [

                    [
                        'route' => 'operation.pickup',
                        'label' => 'PICKUP',
                        'icon' => 'local_shipping',
                    ],

                    [
                        'route' => 'operation.behandle-in',
                        'label' => 'BEHANDLE IN',
                        'icon' => 'move_to_inbox',
                    ],

                    [
                        'route' => 'operation.hold',
                        'label' => 'HOLD',
                        'icon' => 'front_hand',
                    ],

                    [
                        'route' => 'operation.marshallingcic',
                        'label' => 'MARSHALLING CIC',
                        'icon' => 'warehouse',
                    ],

                    [
                        'route' => 'operation.marshalling-yard',
                        'label' => 'MARSHALLING YARD',
                        'icon' => 'location_on',
                    ],

                    [
                        'route' => 'operation.inspection',
                        'label' => 'INSPECTION',
                        'icon' => 'fact_check',
                    ],

                ];

            @endphp


            @foreach($menusBefore as $menu)

                <a
                    href="{{ route($menu['route']) }}"
                    class="flex items-center gap-4
                           border-l-4 border-transparent
                           px-6 py-3
                           text-[#bec6e0]/70
                           hover:bg-[#d3e4fe]/10"
                >

                    <span class="material-symbols-outlined">
                        {{ $menu['icon'] }}
                    </span>

                    <span
                        class="text-xs
                               font-semibold
                               tracking-wider"
                    >
                        {{ $menu['label'] }}
                    </span>

                </a>

            @endforeach


            {{-- ACTIVE PLUG REEFER --}}

            <a
                href="{{ route('operation.plug-reefer') }}"
                class="flex items-center gap-4
                       border-l-4
                       border-[#004ac6]
                       bg-[#2563eb]/10
                       px-6 py-3
                       font-bold text-[#dbe1ff]"
            >

                <span
                    class="material-symbols-outlined"
                    style="font-variation-settings:'FILL' 1;"
                >
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


            @php

                $menusAfter = [

                    [
                        'route' => 'operation.monitoring-reefer',
                        'label' => 'MONITORING REEFER',
                        'icon' => 'thermostat',
                    ],

                    [
                        'route' => 'operation.delivery',
                        'label' => 'DELIVERY',
                        'icon' => 'local_shipping',
                    ],

                    [
                        'route' => 'operation.inspection-out',
                        'label' => 'INSPECTION OUT',
                        'icon' => 'fact_check',
                    ],

                    [
                        'route' => 'operation.on-chassis',
                        'label' => 'ON CHASSIS',
                        'icon' => 'directions_car',
                    ],

                    [
                        'route' => 'operation.copy-yard',
                        'label' => 'COPY YARD',
                        'icon' => 'content_copy',
                    ],

                ];

            @endphp


            @foreach($menusAfter as $menu)

                <a
                    href="{{ route($menu['route']) }}"
                    class="flex items-center gap-4
                           border-l-4 border-transparent
                           px-6 py-3
                           text-[#bec6e0]/70
                           hover:bg-[#d3e4fe]/10"
                >

                    <span class="material-symbols-outlined">
                        {{ $menu['icon'] }}
                    </span>

                    <span
                        class="text-xs
                               font-semibold
                               tracking-wider"
                    >
                        {{ $menu['label'] }}
                    </span>

                </a>

            @endforeach


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


        {{-- HEADER --}}

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



        {{-- ============================================================
            CONTENT
        ============================================================= --}}

        <main
            class="min-h-[calc(100vh-56px)]
                   p-4 sm:p-6"
        >


            {{-- TITLE --}}

            <div class="mb-6">

                <div
                    class="flex items-center gap-2"
                >

                    <span
                        class="material-symbols-outlined
                               text-[24px]
                               text-[#004ac6]"
                    >
                        ac_unit
                    </span>


                    <h1
                        class="text-2xl
                               font-semibold
                               tracking-tight"
                    >
                        PLUG REEFER
                    </h1>

                </div>


                <p
                    class="mt-2 text-sm
                           text-[#434655]"
                >
                    Plugin dan unplugin container reefer.
                </p>

            </div>



            {{-- ====================================================
                SEARCH
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
                            Masukkan nomor container reefer.
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
                                   gap-2 rounded-lg
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
                            || !empty($operations)
                            || $selectedOperation
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

                    <div
                        class="flex items-center gap-3"
                    >

                        <span
                            class="material-symbols-outlined"
                        >

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
                MULTIPLE RESULT
            ===================================================== --}}

            @if(
                !empty($operations)
                && !$selectedOperation
            )

                <div
                    class="mb-6 overflow-hidden
                           rounded-xl
                           border
                           border-[#c3c6d7]/30
                           bg-white
                           shadow-sm"
                >

                    <div
                        class="border-b
                               border-[#c3c6d7]/30
                               px-5 py-4"
                    >

                        <h2
                            class="text-base
                                   font-semibold"
                        >
                            Pilih Container Reefer
                        </h2>

                    </div>


                    <div
                        class="grid gap-3 p-5"
                    >

                        @foreach($operations as $operation)

                            <button
                                type="button"
                                wire:click="selectOperation({{ $operation->id }})"
                                class="flex w-full
                                       items-center
                                       justify-between
                                       rounded-lg
                                       border
                                       border-[#c3c6d7]/40
                                       bg-white
                                       px-4 py-3
                                       text-left
                                       transition
                                       hover:border-[#004ac6]
                                       hover:bg-[#eff4ff]"
                            >

                                <div>

                                    <p
                                        class="text-sm
                                               font-bold"
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
                                               text-[#737686]"
                                    >
                                        SPK:
                                        {{
                                            $operation
                                                ->spk
                                                ?->no_spk
                                            ?? '-'
                                        }}
                                    </p>

                                </div>


                                <span
                                    class="material-symbols-outlined
                                           text-[#004ac6]"
                                >
                                    chevron_right
                                </span>

                            </button>

                        @endforeach

                    </div>

                </div>

            @endif



            {{-- ====================================================
                SELECTED CONTAINER
            ===================================================== --}}

            @if($selectedOperation)

                <div
                    class="overflow-hidden
                           rounded-xl
                           border
                           border-[#c3c6d7]/30
                           bg-white
                           shadow-sm"
                >

                    {{-- HEADER --}}

                    <div
                        class="border-b
                               border-[#c3c6d7]/30
                               px-5 py-5
                               sm:px-6"
                    >

                        <div
                            class="flex
                                   flex-col gap-3
                                   sm:flex-row
                                   sm:items-center
                                   sm:justify-between"
                        >

                            <div>

                                <div
                                    class="flex
                                           items-center gap-2"
                                >

                                    <span
                                        class="material-symbols-outlined
                                               text-[#004ac6]"
                                    >
                                        ac_unit
                                    </span>


                                    <h2
                                        class="text-base
                                               font-semibold"
                                    >
                                        Data Reefer
                                    </h2>

                                </div>


                                <p
                                    class="mt-1 text-xs
                                           text-[#737686]"
                                >

                                    Container:

                                    <span
                                        class="font-bold
                                               text-[#0b1c30]"
                                    >
                                        {{
                                            $selectedOperation
                                                ->container
                                                ?->no_cont
                                            ?? '-'
                                        }}
                                    </span>

                                </p>

                            </div>


                            @if($condition === 1)

                                <span
                                    class="inline-flex
                                           w-fit
                                           rounded-full
                                           bg-[#d3e4fe]
                                           px-3 py-1
                                           text-[10px]
                                           font-semibold
                                           uppercase
                                           tracking-wider
                                           text-[#004ac6]"
                                >
                                    SIAP PLUGIN
                                </span>

                            @else

                                <span
                                    class="inline-flex
                                           w-fit
                                           rounded-full
                                           bg-[#d9f7df]
                                           px-3 py-1
                                           text-[10px]
                                           font-semibold
                                           uppercase
                                           tracking-wider
                                           text-[#146c2e]"
                                >
                                    SUDAH PLUGIN
                                </span>

                            @endif

                        </div>

                    </div>



                    {{-- BODY --}}

                    <div class="p-5 sm:p-6">


                        {{-- ====================================================
                            CONDITION 1
                            MULAI PLUGIN
                        ===================================================== --}}

                        @if($condition === 1)

                            <div
                                class="grid grid-cols-1
                                       gap-5
                                       md:grid-cols-2"
                            >

                                {{-- CONTAINER --}}

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



                                {{-- TEMP DEFAULT --}}

                                <div>

                                    <label
                                        class="mb-2 block
                                               text-xs
                                               font-semibold
                                               uppercase
                                               tracking-wider
                                               text-[#434655]"
                                    >
                                        Temperature Default
                                    </label>


                                    <input
                                        type="text"
                                        readonly
                                        value="{{
                                            $selectedOperation
                                                ->container
                                                ?->suhu_cust
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



                                {{-- TEMP TPS --}}

                                <div>

                                    <label
                                        class="mb-2 block
                                               text-xs
                                               font-semibold
                                               uppercase
                                               tracking-wider
                                               text-[#434655]"
                                    >
                                        Temperature TPS
                                    </label>


                                    <input
                                        type="text"
                                        readonly
                                        value="{{
                                            $selectedOperation
                                                ->container
                                                ?->suhu_terminal
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



                                {{-- TEMP SAAT INI --}}

                                <div>

                                    <label
                                        for="temperature"
                                        class="mb-2 block
                                               text-xs
                                               font-semibold
                                               uppercase
                                               tracking-wider
                                               text-[#434655]"
                                    >
                                        Temperature Saat Ini
                                    </label>


                                    <input
                                        id="temperature"
                                        type="text"
                                        wire:model="temperature"
                                        placeholder="Masukkan temperature"
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


                                    @error('temperature')

                                        <p
                                            class="mt-2 text-xs
                                                   text-[#ba1a1a]"
                                        >
                                            {{ $message }}
                                        </p>

                                    @enderror

                                </div>



                                {{-- NOTE --}}

                                <div
                                    class="md:col-span-2"
                                >

                                    <label
                                        for="note"
                                        class="mb-2 block
                                               text-xs
                                               font-semibold
                                               uppercase
                                               tracking-wider
                                               text-[#434655]"
                                    >
                                        Note
                                    </label>


                                    <textarea
                                        id="note"
                                        wire:model="note"
                                        rows="5"
                                        placeholder="Masukkan catatan..."
                                        class="w-full rounded-lg
                                               border
                                               border-[#c3c6d7]
                                               bg-white
                                               px-4 py-3
                                               text-sm
                                               outline-none
                                               focus:border-[#004ac6]
                                               focus:ring-2
                                               focus:ring-[#b4c5ff]"
                                    ></textarea>

                                </div>

                            </div>



                            {{-- ACTION --}}

                            <div
                                class="mt-6
                                       flex flex-col
                                       gap-3
                                       border-t
                                       border-[#c3c6d7]/30
                                       pt-5
                                       sm:flex-row"
                            >

                                <button
                                    type="button"
                                    wire:click="startPlugin"
                                    wire:loading.attr="disabled"
                                    class="inline-flex
                                           items-center
                                           justify-center
                                           gap-2 rounded-lg
                                           bg-[#004ac6]
                                           px-6 py-3
                                           text-sm
                                           font-semibold
                                           text-white
                                           hover:bg-[#003ea8]
                                           disabled:opacity-50"
                                >

                                    <span
                                        class="material-symbols-outlined
                                               text-[19px]"
                                    >
                                        power
                                    </span>

                                    MULAI PLUGIN

                                </button>


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
                                           px-6 py-3
                                           text-sm
                                           font-semibold
                                           text-[#434655]
                                           hover:bg-[#eff4ff]"
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


                        {{-- ====================================================
                            CONDITION 2
                            UNPLUGIN
                        ===================================================== --}}

                        @elseif($condition === 2)

                            <div
                                class="grid grid-cols-1
                                       gap-5
                                       md:grid-cols-2"
                            >

                                {{-- CONTAINER --}}

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



                                {{-- TEMP SEBELUMNYA --}}

                                <div>

                                    <label
                                        class="mb-2 block
                                               text-xs
                                               font-semibold
                                               uppercase
                                               tracking-wider
                                               text-[#434655]"
                                    >
                                        Temperature Sebelumnya
                                    </label>


                                    <input
                                        type="text"
                                        readonly
                                        value="{{
                                            $monitoring
                                                ?->temperature
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



                                {{-- TEMP TERAKHIR --}}

                                <div>

                                    <label
                                        for="temperature"
                                        class="mb-2 block
                                               text-xs
                                               font-semibold
                                               uppercase
                                               tracking-wider
                                               text-[#434655]"
                                    >
                                        Temperature Terakhir
                                    </label>


                                    <input
                                        id="temperature"
                                        type="text"
                                        wire:model="temperature"
                                        placeholder="Masukkan temperature"
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


                                    @error('temperature')

                                        <p
                                            class="mt-2 text-xs
                                                   text-[#ba1a1a]"
                                        >
                                            {{ $message }}
                                        </p>

                                    @enderror

                                </div>



                                {{-- NOTE --}}

                                <div
                                    class="md:col-span-2"
                                >

                                    <label
                                        for="note"
                                        class="mb-2 block
                                               text-xs
                                               font-semibold
                                               uppercase
                                               tracking-wider
                                               text-[#434655]"
                                    >
                                        Note
                                    </label>


                                    <textarea
                                        id="note"
                                        wire:model="note"
                                        rows="5"
                                        placeholder="Masukkan catatan..."
                                        class="w-full rounded-lg
                                               border
                                               border-[#c3c6d7]
                                               bg-white
                                               px-4 py-3
                                               text-sm
                                               outline-none
                                               focus:border-[#004ac6]
                                               focus:ring-2
                                               focus:ring-[#b4c5ff]"
                                    ></textarea>

                                </div>

                            </div>



                            {{-- ACTION --}}

                            <div
                                class="mt-6
                                       flex flex-col
                                       gap-3
                                       border-t
                                       border-[#c3c6d7]/30
                                       pt-5
                                       sm:flex-row"
                            >

                                <button
                                    type="button"
                                    wire:click="unplugReefer"
                                    wire:loading.attr="disabled"
                                    class="inline-flex
                                           items-center
                                           justify-center
                                           gap-2 rounded-lg
                                           bg-[#004ac6]
                                           px-6 py-3
                                           text-sm
                                           font-semibold
                                           text-white
                                           hover:bg-[#003ea8]
                                           disabled:opacity-50"
                                >

                                    <span
                                        class="material-symbols-outlined
                                               text-[19px]"
                                    >
                                        power_off
                                    </span>

                                    UNPLUGIN REEFER

                                </button>


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
                                           px-6 py-3
                                           text-sm
                                           font-semibold
                                           text-[#434655]
                                           hover:bg-[#eff4ff]"
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

                        @endif

                    </div>

                </div>

            @endif



            {{-- ====================================================
                EMPTY TABLE
            ===================================================== --}}

            @if(
                $hasSearched
                && empty($operations)
                && !$selectedOperation
            )

                <div
                    class="overflow-hidden
                           rounded-xl
                           border
                           border-[#c3c6d7]/30
                           bg-white
                           shadow-sm"
                >

                    <div
                        class="border-b
                               border-[#c3c6d7]/30
                               px-5 py-4"
                    >

                        <h2
                            class="text-base
                                   font-semibold"
                        >
                            Data Reefer
                        </h2>

                    </div>


                    <div
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
                                ac_unit
                            </span>

                        </div>


                        <p
                            class="mt-4 text-sm
                                   font-semibold
                                   text-[#434655]"
                        >
                            Data container reefer tidak ditemukan
                        </p>


                        <p
                            class="mt-1 text-xs
                                   text-[#737686]"
                        >
                            Silakan periksa nomor container yang dimasukkan.
                        </p>

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
                    PLUG REEFER · PortOps Central
                </p>

            </footer>

        </main>

    </div>

</div>