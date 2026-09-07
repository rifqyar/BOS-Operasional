@props(['message' => null, 'messageType' => null])

<div class="min-h-screen bg-[#f8f9ff] text-[#0b1c30] antialiased">

    {{-- SIDEBAR --}}
    <aside
        class="fixed left-0 top-0 z-40 hidden h-screen w-[260px]
               flex-col overflow-y-auto border-r border-[#c3c6d7]/20
               bg-[#213145] md:flex"
    >

        <div class="border-b border-[#c3c6d7]/10 p-6">
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
                class="flex items-center gap-4 border-l-4 border-transparent
                       px-6 py-3 text-[#bec6e0]/70 hover:bg-[#d3e4fe]/10"
            >
                <span class="material-symbols-outlined">
                    dashboard
                </span>

                <span class="text-xs font-semibold tracking-wider">
                    Dashboard
                </span>
            </a>


            <div
                class="px-6 pb-2 pt-4 text-[10px] font-semibold
                       uppercase tracking-widest text-[#bec6e0]/50"
            >
                Operations
            </div>


            @php
                $menus = [
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
                ];
            @endphp


            @foreach($menus as $menu)

                <a
                    href="{{ route($menu['route']) }}"
                    class="flex items-center gap-4 border-l-4 border-transparent
                           px-6 py-3 text-[#bec6e0]/70 hover:bg-[#d3e4fe]/10"
                >
                    <span class="material-symbols-outlined">
                        {{ $menu['icon'] }}
                    </span>

                    <span class="text-xs font-semibold tracking-wider">
                        {{ $menu['label'] }}
                    </span>
                </a>

            @endforeach


            {{-- ACTIVE --}}

            <a
                href="{{ route('operation.inspection') }}"
                class="flex items-center gap-4 border-l-4 border-[#004ac6]
                       bg-[#2563eb]/10 px-6 py-3 font-bold text-[#dbe1ff]"
            >
                <span
                    class="material-symbols-outlined"
                    style="font-variation-settings:'FILL' 1;"
                >
                    fact_check
                </span>

                <span class="text-xs font-semibold tracking-wider">
                    PEMERIKSAAN BEHANDLE
                </span>
            </a>


            @php
                $remainingMenus = [
                    [
                        'route' => 'operation.plug-reefer',
                        'label' => 'PLUG REEFER',
                        'icon' => 'ac_unit',
                    ],
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


            @foreach($remainingMenus as $menu)

                <a
                    href="{{ route($menu['route']) }}"
                    class="flex items-center gap-4 border-l-4 border-transparent
                           px-6 py-3 text-[#bec6e0]/70 hover:bg-[#d3e4fe]/10"
                >
                    <span class="material-symbols-outlined">
                        {{ $menu['icon'] }}
                    </span>

                    <span class="text-xs font-semibold tracking-wider">
                        {{ $menu['label'] }}
                    </span>
                </a>

            @endforeach


            <div class="mt-auto">

                <a
                    href="#"
                    class="flex items-center gap-4 border-l-4 border-transparent
                           px-6 py-3 text-[#bec6e0]/70 hover:bg-[#d3e4fe]/10"
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


    {{-- MAIN --}}

    <div class="min-h-screen md:ml-[260px]">

        {{-- HEADER --}}

        <header
            class="sticky top-0 z-30 flex h-14 items-center justify-between
                   border-b border-[#c3c6d7]/30 bg-[#f8f9ff] px-4 sm:px-6"
        >

            <h1
                class="text-base font-black text-[#0b1c30] md:hidden"
            >
                PortOps Central
            </h1>


            <div class="hidden sm:block">

                <div class="relative">

                    <span
                        class="material-symbols-outlined absolute left-2 top-1/2
                               -translate-y-1/2 text-[18px] text-[#737686]"
                    >
                        search
                    </span>

                    <input
                        type="text"
                        placeholder="Search operations..."
                        class="w-64 rounded border border-[#c3c6d7]/50
                               bg-[#eff4ff] py-1 pl-8 pr-4 text-[13px]
                               outline-none focus:border-[#004ac6]"
                    >

                </div>

            </div>


            <div class="flex items-center gap-3">

                <span
                    class="material-symbols-outlined text-[#434655]"
                >
                    notifications
                </span>

                <div class="h-6 w-px bg-[#c3c6d7]/30"></div>

                <div
                    class="flex h-8 w-8 items-center justify-center
                           rounded-full bg-[#2563eb]"
                >
                    <span
                        class="material-symbols-outlined text-white"
                    >
                        person
                    </span>
                </div>

            </div>

        </header>


        {{-- CONTENT --}}

        <main
            class="min-h-[calc(100vh-56px)] p-4 sm:p-6"
        >

            {{-- TITLE --}}

            <div class="mb-6">

                <div class="flex items-center gap-2">

                    <span
                        class="material-symbols-outlined text-[24px] text-[#004ac6]"
                    >
                        fact_check
                    </span>

                    <h1
                        class="text-2xl font-semibold tracking-tight"
                    >
                        PEMERIKSAAN BEHANDLE
                    </h1>

                </div>

                <p class="mt-2 text-sm text-[#434655]">
                    Pemeriksaan container Behandle.
                </p>

            </div>


            {{-- SEARCH --}}

            <div
                class="mb-6 rounded-xl border border-[#c3c6d7]/30
                       bg-white p-5 shadow-sm sm:p-6"
            >

                <div class="mb-5 flex items-center gap-3">

                    <div
                        class="flex h-10 w-10 items-center justify-center
                               rounded-lg bg-[#d3e4fe] text-[#004ac6]"
                    >
                        <span class="material-symbols-outlined">
                            search
                        </span>
                    </div>

                    <div>

                        <h2 class="text-base font-semibold">
                            Search No Container
                        </h2>

                        <p class="text-xs text-[#737686]">
                            Masukkan nomor container untuk pemeriksaan.
                        </p>

                    </div>

                </div>


                <form wire:submit="search">

                    <div
                        class="flex flex-col gap-3 sm:flex-row sm:items-end"
                    >

                        <div class="w-full sm:max-w-md">

                            <label
                                for="searchCont"
                                class="mb-2 block text-xs font-semibold
                                       uppercase tracking-wider text-[#434655]"
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
                                class="w-full rounded-lg border border-[#c3c6d7]
                                       bg-white px-4 py-2.5 text-sm outline-none
                                       focus:border-[#004ac6]
                                       focus:ring-2 focus:ring-[#b4c5ff]"
                            >

                            @error('searchCont')
                                <p class="mt-2 text-xs text-[#ba1a1a]">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="search"
                            class="inline-flex items-center justify-center
                                   gap-2 rounded-lg bg-[#004ac6] px-5 py-2.5
                                   text-sm font-semibold text-white
                                   hover:bg-[#003ea8] disabled:opacity-50"
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
                        )

                            <button
                                type="button"
                                wire:click="resetSearch"
                                class="inline-flex items-center justify-center
                                       gap-2 rounded-lg border border-[#c3c6d7]
                                       bg-white px-5 py-2.5 text-sm
                                       font-semibold text-[#434655]
                                       hover:bg-[#eff4ff]"
                            >

                                <span
                                    class="material-symbols-outlined text-[18px]"
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

            @if($message ?? null)

                <div
                    class="mb-6 rounded-lg border px-4 py-3
                        @if($messageType === 'danger')
                            border-[#ba1a1a]/30 bg-[#ffdad6] text-[#93000a]
                        @elseif($messageType === 'success')
                            border-[#137333]/30 bg-[#e6f4ea] text-[#137333]
                        @else
                            border-[#004ac6]/20 bg-[#d3e4fe] text-[#003ea8]
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

                        <p class="text-sm font-semibold">
                            {{ $message ?? '' }}
                        </p>

                    </div>

                </div>

            @endif


            {{-- MULTIPLE CONTAINER --}}

            @if(
                !empty($operations)
                && !$selectedOperation
            )

                <div
                    class="mb-6 overflow-hidden rounded-xl border
                           border-[#c3c6d7]/30 bg-white shadow-sm"
                >

                    <div
                        class="border-b border-[#c3c6d7]/30 px-5 py-4"
                    >
                        <h2 class="text-base font-semibold">
                            Pilih Container
                        </h2>
                    </div>


                    <div class="grid gap-3 p-5">

                        @foreach($operations as $operation)

                            <button
                                type="button"
                                wire:click="selectOperation({{ $operation->id }})"
                                class="flex w-full items-center justify-between
                                       rounded-lg border border-[#c3c6d7]/40
                                       bg-white px-4 py-3 text-left transition
                                       hover:border-[#004ac6]
                                       hover:bg-[#eff4ff]"
                            >

                                <div>

                                    <p class="text-sm font-bold">
                                        {{ $operation->container?->no_cont ?? '-' }}
                                    </p>

                                    <p class="mt-1 text-xs text-[#737686]">
                                        SPK:
                                        {{ $operation->spk?->no_spk ?? '-' }}
                                    </p>

                                </div>

                                <span
                                    class="material-symbols-outlined text-[#004ac6]"
                                >
                                    chevron_right
                                </span>

                            </button>

                        @endforeach

                    </div>

                </div>

            @endif


            {{-- INSPECTION FORM --}}

            @if($selectedOperation)

                <div
                    class="overflow-hidden rounded-xl border
                           border-[#c3c6d7]/30 bg-white shadow-sm"
                >

                    {{-- FORM HEADER --}}

                    <div
                        class="border-b border-[#c3c6d7]/30
                               px-5 py-5 sm:px-6"
                    >

                        <div
                            class="flex flex-col gap-3
                                   sm:flex-row sm:items-center
                                   sm:justify-between"
                        >

                            <div>

                                <div class="flex items-center gap-2">

                                    <span
                                        class="material-symbols-outlined
                                               text-[#004ac6]"
                                    >
                                        fact_check
                                    </span>

                                    <h2 class="text-base font-semibold">
                                        Data Pemeriksaan
                                    </h2>

                                </div>

                                <p class="mt-1 text-xs text-[#737686]">
                                    Container:

                                    <span
                                        class="font-bold text-[#0b1c30]"
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


                            <span
                                class="inline-flex w-fit rounded-full
                                       px-3 py-1 text-[10px] font-semibold
                                       uppercase tracking-wider
                                       @if($inspection?->status === 'DONE')
                                           bg-[#e6f4ea] text-[#137333]
                                       @elseif($inspection?->status === 'WAITING')
                                           bg-[#fff4ce] text-[#8a5a00]
                                       @else
                                           bg-[#d3e4fe] text-[#004ac6]
                                       @endif"
                            >

                                @if($inspection?->status === 'DONE')
                                    PEMERIKSAAN SELESAI
                                @elseif($inspection?->status === 'WAITING')
                                    PEMERIKSAAN BERJALAN
                                @else
                                    SIAP DIPERIKSA
                                @endif

                            </span>

                        </div>

                    </div>


                    {{-- FORM BODY --}}

                    <div class="p-5 sm:p-6">

                        <div
                            class="grid grid-cols-1 gap-5 md:grid-cols-2"
                        >

                            {{-- NO CONTAINER --}}

                            <div>

                                <label
                                    class="mb-2 block text-xs font-semibold
                                           uppercase tracking-wider
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
                                    class="w-full rounded-lg border
                                           border-[#c3c6d7]
                                           bg-[#eff4ff] px-4 py-2.5
                                           text-sm font-semibold"
                                >

                            </div>


                            {{-- NO SPK --}}

                            <div>

                                <label
                                    class="mb-2 block text-xs font-semibold
                                           uppercase tracking-wider
                                           text-[#434655]"
                                >
                                    No SPK
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
                                    class="w-full rounded-lg border
                                           border-[#c3c6d7]
                                           bg-[#eff4ff] px-4 py-2.5
                                           text-sm font-semibold"
                                >

                            </div>


                            {{-- TYPE CONTAINER --}}

                            <div>

                                <label
                                    class="mb-2 block text-xs font-semibold
                                           uppercase tracking-wider
                                           text-[#434655]"
                                >
                                    Type Container
                                </label>

                                <input
                                    type="text"
                                    readonly
                                    value="{{
                                        $selectedOperation
                                            ->container
                                            ?->type
                                            ?->name
                                        ?? '-'
                                    }}"
                                    class="w-full rounded-lg border
                                           border-[#c3c6d7]
                                           bg-[#eff4ff] px-4 py-2.5
                                           text-sm font-semibold"
                                >

                            </div>


                            {{-- STATUS --}}

                            <div>

                                <label
                                    class="mb-2 block text-xs font-semibold
                                           uppercase tracking-wider
                                           text-[#434655]"
                                >
                                    Status Container
                                </label>

                                <input
                                    type="text"
                                    readonly
                                    value="{{
                                        $inspection?->status === 'DONE'
                                            ? '500'
                                            : '460'
                                    }}"
                                    class="w-full rounded-lg border
                                           border-[#c3c6d7]
                                           bg-[#eff4ff] px-4 py-2.5
                                           text-sm font-semibold"
                                >

                            </div>


                            {{-- JENIS KEGIATAN --}}

                            <div>

                                <label
                                    class="mb-2 block text-xs font-semibold
                                           uppercase tracking-wider
                                           text-[#434655]"
                                >
                                    Jenis Kegiatan
                                </label>

                                <input
                                    type="text"
                                    readonly
                                    value="{{
                                        $jobSlip?->gatepass?->jenis_kegiatan === '1'
                                            ? 'BEHANDLE 1'
                                            : (
                                                $jobSlip?->gatepass?->jenis_kegiatan === '2'
                                                    ? 'BEHANDLE 2'
                                                    : '-'
                                            )
                                    }}"
                                    class="w-full rounded-lg border
                                           border-[#c3c6d7]
                                           bg-[#eff4ff] px-4 py-2.5
                                           text-sm font-semibold"
                                >

                            </div>


                            {{-- NO SEAL --}}

                            <div>

                                <label
                                    for="noSeal"
                                    class="mb-2 block text-xs font-semibold
                                           uppercase tracking-wider
                                           text-[#434655]"
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
                                    class="w-full rounded-lg border
                                           border-[#c3c6d7] bg-white
                                           px-4 py-2.5 text-sm outline-none
                                           focus:border-[#004ac6]
                                           focus:ring-2
                                           focus:ring-[#b4c5ff]
                                           disabled:bg-[#eff4ff]
                                           disabled:text-[#737686]"
                                >

                                @error('noSeal')
                                    <p class="mt-2 text-xs text-[#ba1a1a]">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>


                            {{-- ALAT --}}

                            <div>

                                <label
                                    for="alat"
                                    class="mb-2 block text-xs font-semibold
                                           uppercase tracking-wider
                                           text-[#434655]"
                                >
                                    Alat
                                </label>

                                <select
                                    id="alat"
                                    wire:model="alat"
                                    @disabled($inspection?->status === 'DONE')
                                    class="w-full rounded-lg border
                                           border-[#c3c6d7] bg-white
                                           px-4 py-2.5 text-sm outline-none
                                           focus:border-[#004ac6]
                                           focus:ring-2
                                           focus:ring-[#b4c5ff]
                                           disabled:bg-[#eff4ff]"
                                >

                                    <option value="">
                                        -- Pilih Alat --
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
                                    <p class="mt-2 text-xs text-[#ba1a1a]">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>


                            {{-- OPERATOR --}}

                            <div>

                                <label
                                    for="operator"
                                    class="mb-2 block text-xs font-semibold
                                           uppercase tracking-wider
                                           text-[#434655]"
                                >
                                    Operator
                                </label>

                                <select
                                    id="operator"
                                    wire:model="operator"
                                    @disabled($inspection?->status === 'DONE')
                                    class="w-full rounded-lg border
                                           border-[#c3c6d7] bg-white
                                           px-4 py-2.5 text-sm outline-none
                                           focus:border-[#004ac6]
                                           focus:ring-2
                                           focus:ring-[#b4c5ff]
                                           disabled:bg-[#eff4ff]"
                                >

                                    <option value="">
                                        -- Pilih Operator --
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
                                    <p class="mt-2 text-xs text-[#ba1a1a]">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>

                        </div>


                        {{-- INSPECTION INFO --}}

                        @if($inspection)

                            <div
                                class="mt-6 grid grid-cols-1 gap-4
                                       border-t border-[#c3c6d7]/30
                                       pt-5 sm:grid-cols-2"
                            >

                                <div
                                    class="rounded-lg bg-[#eff4ff] p-4"
                                >

                                    <p
                                        class="text-[10px] font-semibold
                                               uppercase tracking-wider
                                               text-[#737686]"
                                    >
                                        Mulai Pemeriksaan
                                    </p>

                                    <p class="mt-1 text-sm font-semibold">
                                        {{
                                            $inspection->started_at
                                                ? $inspection
                                                    ->started_at
                                                    ->format('d-m-Y H:i:s')
                                                : '-'
                                        }}
                                    </p>

                                </div>


                                <div
                                    class="rounded-lg bg-[#eff4ff] p-4"
                                >

                                    <p
                                        class="text-[10px] font-semibold
                                               uppercase tracking-wider
                                               text-[#737686]"
                                    >
                                        Selesai Pemeriksaan
                                    </p>

                                    <p class="mt-1 text-sm font-semibold">
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


                        {{-- ACTION --}}

                        <div
                            class="mt-6 flex flex-col gap-3
                                   border-t border-[#c3c6d7]/30
                                   pt-5 sm:flex-row"
                        >

                            @if(!$inspection)

                                <button
                                    type="button"
                                    wire:click="startInspection"
                                    wire:loading.attr="disabled"
                                    wire:target="startInspection"
                                    class="inline-flex items-center
                                           justify-center gap-2 rounded-lg
                                           bg-[#004ac6] px-6 py-3 text-sm
                                           font-semibold text-white
                                           hover:bg-[#003ea8]
                                           disabled:opacity-50"
                                >

                                    <span
                                        wire:loading.remove
                                        wire:target="startInspection"
                                        class="material-symbols-outlined
                                               text-[19px]"
                                    >
                                        play_arrow
                                    </span>

                                    <span
                                        wire:loading
                                        wire:target="startInspection"
                                    >
                                        MEMULAI...
                                    </span>

                                    <span
                                        wire:loading.remove
                                        wire:target="startInspection"
                                    >
                                        MULAI PEMERIKSAAN
                                    </span>

                                </button>


                            @elseif($inspection->status === 'WAITING')

                                <button
                                    type="button"
                                    wire:click="finishInspection"
                                    wire:loading.attr="disabled"
                                    wire:target="finishInspection"
                                    class="inline-flex items-center
                                           justify-center gap-2 rounded-lg
                                           bg-[#004ac6] px-6 py-3 text-sm
                                           font-semibold text-white
                                           hover:bg-[#003ea8]
                                           disabled:opacity-50"
                                >

                                    <span
                                        wire:loading.remove
                                        wire:target="finishInspection"
                                        class="material-symbols-outlined
                                               text-[19px]"
                                    >
                                        check_circle
                                    </span>

                                    <span
                                        wire:loading
                                        wire:target="finishInspection"
                                    >
                                        MENYELESAIKAN...
                                    </span>

                                    <span
                                        wire:loading.remove
                                        wire:target="finishInspection"
                                    >
                                        SELESAI PEMERIKSAAN
                                    </span>

                                </button>


                            @else

                                <div
                                    class="inline-flex items-center gap-2
                                           rounded-lg bg-[#e6f4ea]
                                           px-6 py-3 text-sm font-semibold
                                           text-[#137333]"
                                >

                                    <span
                                        class="material-symbols-outlined
                                               text-[19px]"
                                    >
                                        check_circle
                                    </span>

                                    PEMERIKSAAN SELESAI

                                </div>

                            @endif


                            <button
                                type="button"
                                wire:click="resetSearch"
                                class="inline-flex items-center
                                       justify-center gap-2 rounded-lg
                                       border border-[#c3c6d7] bg-white
                                       px-6 py-3 text-sm font-semibold
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

                    </div>

                </div>

            @endif


            {{-- EMPTY STATE --}}

            @if(empty($operations) && !$selectedOperation)

                <div
                    class="mt-6 overflow-hidden rounded-xl border
                           border-[#c3c6d7]/30 bg-white shadow-sm"
                >

                    <div
                        class="border-b border-[#c3c6d7]/30 px-5 py-4"
                    >

                        <h2 class="text-base font-semibold">
                            Data Pemeriksaan
                        </h2>

                    </div>


                    <div class="px-6 py-14 text-center">

                        <div
                            class="mx-auto flex h-14 w-14 items-center
                                   justify-center rounded-full
                                   bg-[#eff4ff] text-[#004ac6]"
                        >

                            <span
                                class="material-symbols-outlined text-[28px]"
                            >
                                fact_check
                            </span>

                        </div>

                        <p
                            class="mt-4 text-sm font-semibold text-[#434655]"
                        >
                            Belum ada data pemeriksaan
                        </p>

                        <p class="mt-1 text-xs text-[#737686]">
                            Masukkan nomor container untuk mencari data.
                        </p>

                    </div>

                </div>

            @endif


            {{-- FOOTER --}}

            <footer
                class="mt-6 border-t border-[#c3c6d7]/30
                       pt-5 text-center"
            >
                <p class="text-xs text-[#737686]">
                    PEMERIKSAAN BEHANDLE · PortOps Central
                </p>
            </footer>

        </main>

    </div>

</div>