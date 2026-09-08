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
                           hover:bg-slate-50
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

                        <span class="material-symbols-outlined text-[22px] text-blue-600">
                            fact_check
                        </span>

                        <h1 class="truncate text-lg font-black tracking-tight sm:text-xl">
                            INSPECTION OUT
                        </h1>

                    </div>

                    <p class="mt-0.5 truncate text-xs text-slate-500 dark:text-slate-400">
                        Pemeriksaan container sebelum gate out
                    </p>

                </div>

            </div>


            <span
                class="hidden shrink-0 rounded-full bg-blue-50 px-3 py-1.5
                       text-[10px] font-bold uppercase tracking-wider text-blue-700
                       sm:inline-flex
                       dark:bg-blue-950/50 dark:text-blue-300"
            >
                INSPECTION OUT
            </span>

        </header>



        {{-- ============================================================
            SEARCH
        ============================================================= --}}

        <section
            class="mb-5 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm
                   sm:p-5
                   dark:border-slate-800 dark:bg-slate-900"
        >

            <div class="mb-4 flex items-center gap-3">

                <div
                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl
                           bg-blue-50 text-blue-600
                           dark:bg-blue-950/50 dark:text-blue-400"
                >
                    <span class="material-symbols-outlined">
                        search
                    </span>
                </div>

                <div class="min-w-0">

                    <h2 class="text-sm font-bold sm:text-base">
                        Search No Container
                    </h2>

                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Masukkan nomor container untuk melakukan inspection out.
                    </p>

                </div>

            </div>


            <form wire:submit="search">

                <div class="flex flex-col gap-3 sm:flex-row sm:items-end">

                    <div class="w-full">

                        <label
                            for="searchCont"
                            class="mb-2 block text-[11px] font-bold uppercase
                                   tracking-wider text-slate-600
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
                            class="h-12 w-full rounded-xl border border-slate-300
                                   bg-white px-4 text-sm font-semibold uppercase
                                   outline-none transition
                                   focus:border-blue-600 focus:ring-2 focus:ring-blue-100
                                   dark:border-slate-700 dark:bg-slate-950
                                   dark:text-white dark:focus:border-blue-500
                                   dark:focus:ring-blue-950"
                        >

                        @error('searchCont')

                            <p class="mt-2 text-xs font-medium text-red-600 dark:text-red-400">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="inline-flex h-12 w-full shrink-0 items-center justify-center
                               gap-2 rounded-xl bg-blue-600 px-5 text-sm font-bold text-white
                               transition hover:bg-blue-700
                               disabled:cursor-not-allowed disabled:opacity-50
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


                    @if(
                        $searchCont !== ''
                        || !empty($operations)
                        || $selectedOperation
                    )

                        <button
                            type="button"
                            wire:click="resetSearch"
                            class="inline-flex h-12 w-full shrink-0 items-center justify-center
                                   gap-2 rounded-xl border border-slate-300 bg-white px-5
                                   text-sm font-bold text-slate-700 transition
                                   hover:bg-slate-50
                                   dark:border-slate-700 dark:bg-slate-950
                                   dark:text-slate-200 dark:hover:bg-slate-800
                                   sm:w-auto"
                        >

                            <span class="material-symbols-outlined text-[20px]">
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
                        border-red-200 bg-red-50 text-red-800
                        dark:border-red-900/50 dark:bg-red-950/40 dark:text-red-300
                    @elseif($messageType === 'success')
                        border-green-200 bg-green-50 text-green-800
                        dark:border-green-900/50 dark:bg-green-950/40 dark:text-green-300
                    @else
                        border-blue-200 bg-blue-50 text-blue-800
                        dark:border-blue-900/50 dark:bg-blue-950/40 dark:text-blue-300
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

                        <p class="text-sm font-semibold leading-5">
                            {{ $message }}
                        </p>

                    </div>

                </div>

            @endif

        @endisset



        {{-- ============================================================
            SELECT CONTAINER
        ============================================================= --}}

        @if(
            !empty($operations)
            && !$selectedOperation
        )

            <section
                class="mb-5 overflow-hidden rounded-2xl border border-slate-200
                       bg-white shadow-sm
                       dark:border-slate-800 dark:bg-slate-900"
            >

                <div
                    class="border-b border-slate-200 px-4 py-4
                           dark:border-slate-800 sm:px-5"
                >

                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center
                                   rounded-xl bg-blue-50 text-blue-600
                                   dark:bg-blue-950/50 dark:text-blue-400"
                        >
                            <span class="material-symbols-outlined">
                                inventory_2
                            </span>
                        </div>

                        <div>

                            <h2 class="text-sm font-bold sm:text-base">
                                Pilih Container
                            </h2>

                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                Pilih container yang akan diperiksa.
                            </p>

                        </div>

                    </div>

                </div>


                <div class="grid gap-3 p-4 sm:p-5">

                    @foreach($operations as $operation)

                        <button
                            type="button"
                            wire:click="selectOperation({{ $operation->id }})"
                            class="flex min-h-[68px] w-full items-center justify-between
                                   gap-4 rounded-xl border border-slate-200 bg-white
                                   px-4 py-3 text-left transition
                                   hover:border-blue-400 hover:bg-blue-50
                                   dark:border-slate-700 dark:bg-slate-950
                                   dark:hover:border-blue-700 dark:hover:bg-blue-950/30"
                        >

                            <div class="min-w-0">

                                <p
                                    class="truncate text-base font-black tracking-wide
                                           text-slate-900 dark:text-white"
                                >
                                    {{
                                        $operation
                                            ->container
                                            ?->no_cont
                                        ?? '-'
                                    }}
                                </p>

                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
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
                                class="flex h-9 w-9 shrink-0 items-center justify-center
                                       rounded-full bg-blue-50 text-blue-600
                                       dark:bg-blue-950/50 dark:text-blue-400"
                            >
                                <span class="material-symbols-outlined text-[20px]">
                                    chevron_right
                                </span>
                            </span>

                        </button>

                    @endforeach

                </div>

            </section>

        @endif



        {{-- ============================================================
            SELECTED CONTAINER / INSPECTION OUT
        ============================================================= --}}

        @if($selectedOperation)

            <section
                class="overflow-hidden rounded-2xl border border-slate-200
                       bg-white shadow-sm
                       dark:border-slate-800 dark:bg-slate-900"
            >

                {{-- ----------------------------------------------------
                    CONTAINER HEADER
                ----------------------------------------------------- --}}

                <div
                    class="border-b border-slate-200 p-4
                           dark:border-slate-800 sm:p-5"
                >

                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                        <div class="min-w-0">

                            <div class="mb-2 flex items-center gap-2">

                                <span
                                    class="material-symbols-outlined
                                           text-[21px] text-blue-600
                                           dark:text-blue-400"
                                >
                                    fact_check
                                </span>

                                <span
                                    class="text-[11px] font-bold uppercase tracking-wider
                                           text-slate-500 dark:text-slate-400"
                                >
                                    Container Inspection
                                </span>

                            </div>


                            <h2
                                class="break-all text-2xl font-black tracking-tight
                                       text-slate-900 dark:text-white sm:text-3xl"
                            >
                                {{
                                    $selectedOperation
                                        ->container
                                        ?->no_cont
                                    ?? '-'
                                }}
                            </h2>


                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                No Container
                            </p>

                        </div>


                        <div class="flex items-center gap-2">

                            <span
                                class="inline-flex items-center gap-1.5 rounded-full
                                       bg-blue-50 px-3 py-1.5 text-[10px] font-bold
                                       uppercase tracking-wider text-blue-700
                                       dark:bg-blue-950/50 dark:text-blue-300"
                            >

                                <span
                                    class="h-1.5 w-1.5 rounded-full bg-blue-600
                                           dark:bg-blue-400"
                                ></span>

                                INSPECTION OUT

                            </span>

                        </div>

                    </div>

                </div>



                {{-- ----------------------------------------------------
                    FORM
                ----------------------------------------------------- --}}

                <div class="p-4 sm:p-5">

                    <form wire:submit="submitInspectionOut">

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                            {{-- NO CONTAINER --}}

                            <div>

                                <label
                                    class="mb-2 block text-[11px] font-bold uppercase
                                           tracking-wider text-slate-600
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
                                    class="h-12 w-full rounded-xl border border-slate-200
                                           bg-slate-100 px-4 text-sm font-black uppercase
                                           text-slate-800 outline-none
                                           dark:border-slate-700 dark:bg-slate-800
                                           dark:text-white"
                                >

                            </div>



                            {{-- UKURAN --}}

                            <div>

                                <label
                                    class="mb-2 block text-[11px] font-bold uppercase
                                           tracking-wider text-slate-600
                                           dark:text-slate-300"
                                >
                                    Ukuran
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
                                    class="h-12 w-full rounded-xl border border-slate-200
                                           bg-slate-100 px-4 text-sm font-bold uppercase
                                           text-slate-800 outline-none
                                           dark:border-slate-700 dark:bg-slate-800
                                           dark:text-white"
                                >

                            </div>



                            {{-- KONDISI SEAL --}}

                            <div class="md:col-span-2">

                                <label
                                    class="mb-2 block text-[11px] font-bold uppercase
                                           tracking-wider text-slate-600
                                           dark:text-slate-300"
                                >
                                    Kondisi Seal
                                </label>


                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

                                    <label
                                        class="flex min-h-12 cursor-pointer items-center gap-3
                                               rounded-xl border border-slate-200 bg-white
                                               px-4 transition
                                               hover:border-blue-400 hover:bg-blue-50
                                               has-[:checked]:border-blue-600
                                               has-[:checked]:bg-blue-50
                                               dark:border-slate-700 dark:bg-slate-950
                                               dark:hover:border-blue-700
                                               dark:hover:bg-blue-950/30
                                               dark:has-[:checked]:border-blue-500
                                               dark:has-[:checked]:bg-blue-950/30"
                                    >

                                        <input
                                            type="radio"
                                            wire:model="sealCondition"
                                            value="ada"
                                            class="h-5 w-5 accent-blue-600"
                                        >

                                        <div>

                                            <p class="text-sm font-bold">
                                                ADA
                                            </p>

                                            <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                                Seal tersedia
                                            </p>

                                        </div>

                                    </label>


                                    <label
                                        class="flex min-h-12 cursor-pointer items-center gap-3
                                               rounded-xl border border-slate-200 bg-white
                                               px-4 transition
                                               hover:border-blue-400 hover:bg-blue-50
                                               has-[:checked]:border-blue-600
                                               has-[:checked]:bg-blue-50
                                               dark:border-slate-700 dark:bg-slate-950
                                               dark:hover:border-blue-700
                                               dark:hover:bg-blue-950/30
                                               dark:has-[:checked]:border-blue-500
                                               dark:has-[:checked]:bg-blue-950/30"
                                    >

                                        <input
                                            type="radio"
                                            wire:model="sealCondition"
                                            value="tidak ada"
                                            class="h-5 w-5 accent-blue-600"
                                        >

                                        <div>

                                            <p class="text-sm font-bold">
                                                TIDAK ADA
                                            </p>

                                            <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                                Seal tidak tersedia
                                            </p>

                                        </div>

                                    </label>

                                </div>


                                @error('sealCondition')

                                    <p class="mt-2 text-xs font-medium text-red-600 dark:text-red-400">
                                        {{ $message }}
                                    </p>

                                @enderror

                            </div>



                            {{-- NO SEAL --}}

                            <div class="md:col-span-2">

                                <label
                                    for="sealNo"
                                    class="mb-2 block text-[11px] font-bold uppercase
                                           tracking-wider text-slate-600
                                           dark:text-slate-300"
                                >
                                    No Seal
                                </label>

                                <input
                                    id="sealNo"
                                    type="text"
                                    wire:model="sealNo"
                                    placeholder="MASUKKAN NO SEAL"
                                    autocomplete="off"
                                    class="h-12 w-full rounded-xl border border-slate-300
                                           bg-white px-4 text-sm font-semibold uppercase
                                           outline-none transition
                                           focus:border-blue-600 focus:ring-2
                                           focus:ring-blue-100
                                           dark:border-slate-700 dark:bg-slate-950
                                           dark:text-white dark:focus:border-blue-500
                                           dark:focus:ring-blue-950"
                                >

                                @error('sealNo')

                                    <p class="mt-2 text-xs font-medium text-red-600 dark:text-red-400">
                                        {{ $message }}
                                    </p>

                                @enderror

                            </div>



                            {{-- KONDISI CONTAINER --}}

                            <div class="md:col-span-2">

                                <label
                                    for="containerCondition"
                                    class="mb-2 block text-[11px] font-bold uppercase
                                           tracking-wider text-slate-600
                                           dark:text-slate-300"
                                >
                                    Kondisi Container
                                </label>

                                <select
                                    id="containerCondition"
                                    wire:model="containerCondition"
                                    class="h-12 w-full rounded-xl border border-slate-300
                                           bg-white px-4 text-sm font-semibold outline-none
                                           transition
                                           focus:border-blue-600 focus:ring-2
                                           focus:ring-blue-100
                                           dark:border-slate-700 dark:bg-slate-950
                                           dark:text-white dark:focus:border-blue-500
                                           dark:focus:ring-blue-950"
                                >

                                    <option value="">
                                        PILIH KONDISI CONTAINER
                                    </option>

                                    @foreach($conditions as $condition)

                                        <option value="{{ $condition['id'] }}">
                                            {{ $condition['name'] }}
                                        </option>

                                    @endforeach

                                </select>


                                @error('containerCondition')

                                    <p class="mt-2 text-xs font-medium text-red-600 dark:text-red-400">
                                        {{ $message }}
                                    </p>

                                @enderror

                            </div>

                        </div>



                        {{-- ------------------------------------------------
                            ACTION
                        ------------------------------------------------- --}}

                        <div
                            class="mt-6 grid grid-cols-1 gap-3 border-t
                                   border-slate-200 pt-5
                                   dark:border-slate-800 sm:grid-cols-2"
                        >

                            <button
                                type="submit"
                                wire:loading.attr="disabled"
                                class="inline-flex h-12 items-center justify-center gap-2
                                       rounded-xl bg-blue-600 px-5 text-sm font-black
                                       text-white transition
                                       hover:bg-blue-700
                                       disabled:cursor-not-allowed disabled:opacity-50"
                            >

                                <span
                                    class="material-symbols-outlined text-[21px]"
                                    wire:loading.remove
                                    wire:target="submitInspectionOut"
                                >
                                    fact_check
                                </span>

                                <span
                                    wire:loading.remove
                                    wire:target="submitInspectionOut"
                                >
                                    INSPECTION OUT
                                </span>

                                <span
                                    wire:loading
                                    wire:target="submitInspectionOut"
                                >
                                    PROCESSING...
                                </span>

                            </button>


                            <button
                                type="button"
                                wire:click="resetSearch"
                                class="inline-flex h-12 items-center justify-center gap-2
                                       rounded-xl border border-slate-300 bg-white px-5
                                       text-sm font-bold text-slate-700 transition
                                       hover:bg-slate-50
                                       dark:border-slate-700 dark:bg-slate-950
                                       dark:text-slate-200 dark:hover:bg-slate-800"
                            >

                                <span class="material-symbols-outlined text-[20px]">
                                    refresh
                                </span>

                                RESET

                            </button>

                        </div>

                    </form>

                </div>

            </section>

        @endif



        {{-- ============================================================
            EMPTY RESULT
        ============================================================= --}}

        @if(
            $hasSearched
            && empty($operations)
            && !$selectedOperation
        )

            <section
                class="mt-5 overflow-hidden rounded-2xl border border-slate-200
                       bg-white shadow-sm
                       dark:border-slate-800 dark:bg-slate-900"
            >

                <div class="px-5 py-14 text-center">

                    <div
                        class="mx-auto flex h-16 w-16 items-center justify-center
                               rounded-full bg-slate-100 text-slate-500
                               dark:bg-slate-800 dark:text-slate-400"
                    >

                        <span class="material-symbols-outlined text-[30px]">
                            search_off
                        </span>

                    </div>


                    <p class="mt-4 text-sm font-bold text-slate-700 dark:text-slate-200">
                        Data container tidak ditemukan
                    </p>

                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        Silakan periksa kembali nomor container.
                    </p>

                </div>

            </section>

        @endif



        {{-- ============================================================
            FOOTER
        ============================================================= --}}

        <footer
            class="mt-6 border-t border-slate-200 py-5 text-center
                   dark:border-slate-800"
        >

            <p class="text-[11px] text-slate-500 dark:text-slate-500">
                INSPECTION OUT · PortOps Central
            </p>

        </footer>

    </div>

</div>
