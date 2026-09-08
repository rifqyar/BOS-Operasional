<div class="min-h-screen bg-slate-50 text-slate-900 antialiased dark:bg-slate-950 dark:text-white">

    <div class="mx-auto w-full max-w-6xl px-4 py-5 sm:px-6 sm:py-6 lg:px-8">

        {{-- ============================================================
            HEADER
        ============================================================= --}}

        <div class="mb-5 flex items-center justify-between gap-3">

            <a
                href="{{ route('dashboard') }}"
                wire:navigate
                class="inline-flex min-h-11 items-center gap-2 rounded-lg
                       px-3 text-sm font-semibold
                       text-slate-600
                       transition hover:bg-slate-200
                       dark:text-slate-300 dark:hover:bg-slate-800"
            >
                <span class="material-symbols-outlined text-[20px]">
                    arrow_back
                </span>

                <span>
                    Menu Handheld
                </span>
            </a>

            <span
                class="inline-flex min-h-8 items-center rounded-md
                       bg-blue-100 px-3 py-1
                       text-[11px] font-bold uppercase tracking-wider
                       text-blue-700
                       dark:bg-blue-950 dark:text-blue-300"
            >
                Plug Reefer
            </span>

        </div>



        {{-- ============================================================
            TITLE
        ============================================================= --}}

        <div class="mb-5">

            <div class="flex items-center gap-2">

                <span
                    class="material-symbols-outlined
                           text-[26px]
                           text-blue-600
                           dark:text-blue-400"
                >
                    ac_unit
                </span>

                <h1
                    class="text-xl font-bold tracking-tight
                           sm:text-2xl"
                >
                    PLUG REEFER
                </h1>

            </div>

            <p
                class="mt-1.5 text-sm
                       text-slate-500
                       dark:text-slate-400"
            >
                Plugin dan unplugin container reefer.
            </p>

        </div>



        {{-- ============================================================
            SEARCH
        ============================================================= --}}

        <div
            class="rounded-xl border
                   border-slate-200
                   bg-white
                   p-4 shadow-sm
                   dark:border-slate-800
                   dark:bg-slate-900
                   sm:p-5"
        >

            <div class="mb-4">

                <label
                    for="searchCont"
                    class="mb-2 block
                           text-xs font-bold uppercase
                           tracking-wider
                           text-slate-600
                           dark:text-slate-300"
                >
                    No Container
                </label>

                <div class="flex flex-col gap-3 sm:flex-row">

                    <div class="relative min-w-0 flex-1">

                        <span
                            class="material-symbols-outlined
                                   pointer-events-none
                                   absolute left-3 top-1/2
                                   -translate-y-1/2
                                   text-[20px]
                                   text-slate-400"
                        >
                            search
                        </span>

                        <input
                            id="searchCont"
                            type="text"
                            wire:model="searchCont"
                            wire:keydown.enter="search"
                            autofocus
                            autocomplete="off"
                            placeholder="Masukkan nomor container"
                            class="h-12 w-full rounded-lg
                                   border
                                   border-slate-300
                                   bg-white
                                   pl-11 pr-4
                                   text-sm
                                   font-medium
                                   uppercase
                                   outline-none
                                   transition
                                   focus:border-blue-600
                                   focus:ring-2
                                   focus:ring-blue-100
                                   dark:border-slate-700
                                   dark:bg-slate-950
                                   dark:text-white
                                   dark:focus:border-blue-500
                                   dark:focus:ring-blue-950"
                        />

                    </div>


                    <button
                        type="button"
                        wire:click="search"
                        wire:loading.attr="disabled"
                        wire:target="search"
                        class="inline-flex h-12 w-full
                               items-center justify-center gap-2
                               rounded-lg
                               bg-blue-600
                               px-5
                               text-sm font-bold
                               text-white
                               transition
                               hover:bg-blue-700
                               disabled:cursor-not-allowed
                               disabled:opacity-50
                               sm:w-auto"
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
                            class="inline-flex items-center gap-2"
                        >
                            <span
                                class="material-symbols-outlined
                                       animate-spin text-[18px]"
                            >
                                progress_activity
                            </span>

                            SEARCHING...
                        </span>

                    </button>

                </div>


                @error('searchCont')

                    <p
                        class="mt-2 flex items-center gap-1.5
                               text-xs font-semibold
                               text-red-600
                               dark:text-red-400"
                    >
                        <span class="material-symbols-outlined text-[16px]">
                            error
                        </span>

                        {{ $message }}
                    </p>

                @enderror

            </div>


            @if(
                $searchCont !== ''
                || !empty($operations)
                || $selectedOperation
            )

                <button
                    type="button"
                    wire:click="resetSearch"
                    class="inline-flex h-11 w-full
                           items-center justify-center gap-2
                           rounded-lg
                           border
                           border-slate-300
                           bg-white
                           px-4
                           text-sm font-semibold
                           text-slate-700
                           transition
                           hover:bg-slate-50
                           dark:border-slate-700
                           dark:bg-slate-900
                           dark:text-slate-200
                           dark:hover:bg-slate-800
                           sm:w-auto"
                >

                    <span class="material-symbols-outlined text-[18px]">
                        refresh
                    </span>

                    RESET

                </button>

            @endif

        </div>



        {{-- ============================================================
            MESSAGE
        ============================================================= --}}

        @isset($message)

            @if($message)

                <div
                    class="mt-4 rounded-lg border px-4 py-3
                           @if($messageType === 'danger')
                               border-red-200
                               bg-red-50
                               text-red-700
                               dark:border-red-900
                               dark:bg-red-950/40
                               dark:text-red-300
                           @elseif($messageType === 'success')
                               border-green-200
                               bg-green-50
                               text-green-700
                               dark:border-green-900
                               dark:bg-green-950/40
                               dark:text-green-300
                           @else
                               border-blue-200
                               bg-blue-50
                               text-blue-700
                               dark:border-blue-900
                               dark:bg-blue-950/40
                               dark:text-blue-300
                           @endif"
                >

                    <div class="flex items-start gap-3">

                        <span
                            class="material-symbols-outlined
                                   shrink-0 text-[20px]"
                        >
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
            MULTIPLE RESULT
        ============================================================= --}}

        @if(
            !empty($operations)
            && !$selectedOperation
        )

            <div
                class="mt-5 overflow-hidden
                       rounded-xl border
                       border-slate-200
                       bg-white shadow-sm
                       dark:border-slate-800
                       dark:bg-slate-900"
            >

                <div
                    class="border-b
                           border-slate-200
                           px-4 py-4
                           dark:border-slate-800
                           sm:px-5"
                >

                    <div class="flex items-center justify-between gap-3">

                        <div>

                            <h2 class="text-base font-bold">
                                Pilih Container Reefer
                            </h2>

                            <p
                                class="mt-1 text-xs
                                       text-slate-500
                                       dark:text-slate-400"
                            >
                                Pilih container yang akan diproses.
                            </p>

                        </div>

                        <span
                            class="inline-flex h-8 min-w-8
                                   items-center justify-center
                                   rounded-full
                                   bg-blue-100 px-2
                                   text-xs font-bold
                                   text-blue-700
                                   dark:bg-blue-950
                                   dark:text-blue-300"
                        >
                            {{ count($operations) }}
                        </span>

                    </div>

                </div>


                <div class="divide-y divide-slate-100 dark:divide-slate-800">

                    @foreach($operations as $operation)

                        <button
                            type="button"
                            wire:click="selectOperation({{ $operation->id }})"
                            class="flex min-h-[76px] w-full
                                   items-center justify-between
                                   gap-4 px-4 py-4
                                   text-left
                                   transition
                                   hover:bg-slate-50
                                   dark:hover:bg-slate-800/60
                                   sm:px-5"
                        >

                            <div class="min-w-0">

                                <p
                                    class="truncate text-base
                                           font-bold tracking-wide"
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
                                class="material-symbols-outlined
                                       shrink-0
                                       text-[24px]
                                       text-blue-600
                                       dark:text-blue-400"
                            >
                                chevron_right
                            </span>

                        </button>

                    @endforeach

                </div>

            </div>

        @endif



        {{-- ============================================================
            SELECTED CONTAINER
        ============================================================= --}}

        @if($selectedOperation)

            <div
                class="mt-5 overflow-hidden
                       rounded-xl border
                       border-slate-200
                       bg-white shadow-sm
                       dark:border-slate-800
                       dark:bg-slate-900"
            >

                {{-- ====================================================
                    CONTAINER HEADER
                ===================================================== --}}

                <div
                    class="border-b
                           border-slate-200
                           px-4 py-5
                           dark:border-slate-800
                           sm:px-5"
                >

                    <div
                        class="flex flex-col gap-4
                               sm:flex-row
                               sm:items-center
                               sm:justify-between"
                    >

                        <div class="min-w-0">

                            <div class="flex items-center gap-2">

                                <span
                                    class="material-symbols-outlined
                                           text-[22px]
                                           text-blue-600
                                           dark:text-blue-400"
                                >
                                    ac_unit
                                </span>

                                <p
                                    class="text-xs font-bold
                                           uppercase
                                           tracking-wider
                                           text-slate-500
                                           dark:text-slate-400"
                                >
                                    Container Reefer
                                </p>

                            </div>


                            <p
                                class="mt-2 break-all
                                       text-2xl font-black
                                       tracking-wide
                                       text-slate-950
                                       dark:text-white"
                            >
                                {{
                                    $selectedOperation
                                        ->container
                                        ?->no_cont
                                    ?? '-'
                                }}
                            </p>


                            <p
                                class="mt-1 text-sm
                                       text-slate-500
                                       dark:text-slate-400"
                            >
                                SPK:
                                <span class="font-bold">
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
                                class="inline-flex w-fit
                                       min-h-9
                                       items-center
                                       rounded-full
                                       bg-blue-100
                                       px-3
                                       text-xs font-bold
                                       uppercase
                                       tracking-wider
                                       text-blue-700
                                       dark:bg-blue-950
                                       dark:text-blue-300"
                            >
                                <span
                                    class="mr-2 h-2 w-2
                                           rounded-full
                                           bg-blue-600"
                                ></span>

                                SIAP PLUGIN
                            </span>

                        @else

                            <span
                                class="inline-flex w-fit
                                       min-h-9
                                       items-center
                                       rounded-full
                                       bg-green-100
                                       px-3
                                       text-xs font-bold
                                       uppercase
                                       tracking-wider
                                       text-green-700
                                       dark:bg-green-950
                                       dark:text-green-300"
                            >
                                <span
                                    class="mr-2 h-2 w-2
                                           rounded-full
                                           bg-green-600"
                                ></span>

                                SUDAH PLUGIN
                            </span>

                        @endif

                    </div>

                </div>



                {{-- ====================================================
                    BODY
                ===================================================== --}}

                <div class="p-4 sm:p-5">


                    {{-- ==================================================
                        CONDITION 1 — PLUGIN
                    =================================================== --}}

                    @if($condition === 1)

                        <div class="space-y-5">


                            {{-- REEFER INFO --}}

                            <div>

                                <div class="mb-3">

                                    <h3 class="text-sm font-bold">
                                        Data Reefer
                                    </h3>

                                    <p
                                        class="mt-1 text-xs
                                               text-slate-500
                                               dark:text-slate-400"
                                    >
                                        Informasi suhu container sebelum plugin.
                                    </p>

                                </div>


                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">


                                    {{-- CONTAINER --}}

                                    <div
                                        class="rounded-lg
                                               border
                                               border-slate-200
                                               bg-slate-50
                                               p-4
                                               dark:border-slate-800
                                               dark:bg-slate-950"
                                    >

                                        <p
                                            class="text-[10px]
                                                   font-bold
                                                   uppercase
                                                   tracking-wider
                                                   text-slate-500
                                                   dark:text-slate-400"
                                        >
                                            No Container
                                        </p>

                                        <p
                                            class="mt-1.5 text-base
                                                   font-bold
                                                   tracking-wide"
                                        >
                                            {{
                                                $selectedOperation
                                                    ->container
                                                    ?->no_cont
                                                ?? '-'
                                            }}
                                        </p>

                                    </div>



                                    {{-- TEMP DEFAULT --}}

                                    <div
                                        class="rounded-lg
                                               border
                                               border-slate-200
                                               bg-slate-50
                                               p-4
                                               dark:border-slate-800
                                               dark:bg-slate-950"
                                    >

                                        <p
                                            class="text-[10px]
                                                   font-bold
                                                   uppercase
                                                   tracking-wider
                                                   text-slate-500
                                                   dark:text-slate-400"
                                        >
                                            Temperature Default
                                        </p>

                                        <p
                                            class="mt-1.5 text-base
                                                   font-bold"
                                        >
                                            {{
                                                $selectedOperation
                                                    ->container
                                                    ?->suhu_cust
                                                ?? '-'
                                            }}
                                        </p>

                                    </div>



                                    {{-- TEMP TPS --}}

                                    <div
                                        class="rounded-lg
                                               border
                                               border-slate-200
                                               bg-slate-50
                                               p-4
                                               dark:border-slate-800
                                               dark:bg-slate-950"
                                    >

                                        <p
                                            class="text-[10px]
                                                   font-bold
                                                   uppercase
                                                   tracking-wider
                                                   text-slate-500
                                                   dark:text-slate-400"
                                        >
                                            Temperature TPS
                                        </p>

                                        <p
                                            class="mt-1.5 text-base
                                                   font-bold"
                                        >
                                            {{
                                                $selectedOperation
                                                    ->container
                                                    ?->suhu_terminal
                                                ?? '-'
                                            }}
                                        </p>

                                    </div>



                                    {{-- TEMP SAAT INI --}}

                                    <div
                                        class="rounded-lg
                                               border-2
                                               border-blue-200
                                               bg-blue-50
                                               p-4
                                               dark:border-blue-900
                                               dark:bg-blue-950/30"
                                    >

                                        <label
                                            for="temperature"
                                            class="block
                                                   text-[10px]
                                                   font-bold
                                                   uppercase
                                                   tracking-wider
                                                   text-blue-700
                                                   dark:text-blue-300"
                                        >
                                            Temperature Saat Ini
                                        </label>

                                        <input
                                            id="temperature"
                                            type="text"
                                            inputmode="decimal"
                                            wire:model="temperature"
                                            placeholder="Masukkan temperature"
                                            class="mt-2 h-12 w-full
                                                   rounded-lg
                                                   border
                                                   border-blue-200
                                                   bg-white
                                                   px-4
                                                   text-base
                                                   font-semibold
                                                   outline-none
                                                   focus:border-blue-600
                                                   focus:ring-2
                                                   focus:ring-blue-100
                                                   dark:border-blue-800
                                                   dark:bg-slate-950
                                                   dark:text-white
                                                   dark:focus:border-blue-500"
                                        />

                                        @error('temperature')

                                            <p
                                                class="mt-2 text-xs
                                                       font-semibold
                                                       text-red-600
                                                       dark:text-red-400"
                                            >
                                                {{ $message }}
                                            </p>

                                        @enderror

                                    </div>

                                </div>

                            </div>



                            {{-- NOTE --}}

                            <div>

                                <label
                                    for="note"
                                    class="mb-2 block
                                           text-xs
                                           font-bold
                                           uppercase
                                           tracking-wider
                                           text-slate-600
                                           dark:text-slate-300"
                                >
                                    Note
                                </label>

                                <textarea
                                    id="note"
                                    wire:model="note"
                                    rows="4"
                                    placeholder="Masukkan catatan jika diperlukan..."
                                    class="w-full rounded-lg
                                           border
                                           border-slate-300
                                           bg-white
                                           px-4 py-3
                                           text-sm
                                           outline-none
                                           transition
                                           focus:border-blue-600
                                           focus:ring-2
                                           focus:ring-blue-100
                                           dark:border-slate-700
                                           dark:bg-slate-950
                                           dark:text-white
                                           dark:focus:border-blue-500"
                                ></textarea>

                            </div>



                            {{-- ACTION --}}

                            <div
                                class="border-t
                                       border-slate-200
                                       pt-5
                                       dark:border-slate-800"
                            >

                                <button
                                    type="button"
                                    wire:click="startPlugin"
                                    wire:loading.attr="disabled"
                                    wire:target="startPlugin"
                                    class="inline-flex h-12 w-full
                                           items-center justify-center
                                           gap-2 rounded-lg
                                           bg-blue-600
                                           px-6
                                           text-sm font-bold
                                           text-white
                                           transition
                                           hover:bg-blue-700
                                           disabled:cursor-not-allowed
                                           disabled:opacity-50
                                           sm:w-auto"
                                >

                                    <span
                                        wire:loading.remove
                                        wire:target="startPlugin"
                                        class="material-symbols-outlined
                                               text-[20px]"
                                    >
                                        power
                                    </span>

                                    <span
                                        wire:loading.remove
                                        wire:target="startPlugin"
                                    >
                                        MULAI PLUGIN
                                    </span>


                                    <span
                                        wire:loading
                                        wire:target="startPlugin"
                                        class="inline-flex items-center gap-2"
                                    >

                                        <span
                                            class="material-symbols-outlined
                                                   animate-spin text-[19px]"
                                        >
                                            progress_activity
                                        </span>

                                        MEMPROSES...

                                    </span>

                                </button>

                            </div>

                        </div>



                    {{-- ==================================================
                        CONDITION 2 — UNPLUGIN
                    =================================================== --}}

                    @elseif($condition === 2)

                        <div class="space-y-5">


                            {{-- REEFER INFO --}}

                            <div>

                                <div class="mb-3">

                                    <h3 class="text-sm font-bold">
                                        Data Reefer
                                    </h3>

                                    <p
                                        class="mt-1 text-xs
                                               text-slate-500
                                               dark:text-slate-400"
                                    >
                                        Informasi suhu terakhir sebelum unplugin.
                                    </p>

                                </div>


                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">


                                    {{-- CONTAINER --}}

                                    <div
                                        class="rounded-lg
                                               border
                                               border-slate-200
                                               bg-slate-50
                                               p-4
                                               dark:border-slate-800
                                               dark:bg-slate-950"
                                    >

                                        <p
                                            class="text-[10px]
                                                   font-bold
                                                   uppercase
                                                   tracking-wider
                                                   text-slate-500
                                                   dark:text-slate-400"
                                        >
                                            No Container
                                        </p>

                                        <p
                                            class="mt-1.5 text-base
                                                   font-bold
                                                   tracking-wide"
                                        >
                                            {{
                                                $selectedOperation
                                                    ->container
                                                    ?->no_cont
                                                ?? '-'
                                            }}
                                        </p>

                                    </div>



                                    {{-- TEMP SEBELUMNYA --}}

                                    <div
                                        class="rounded-lg
                                               border
                                               border-slate-200
                                               bg-slate-50
                                               p-4
                                               dark:border-slate-800
                                               dark:bg-slate-950"
                                    >

                                        <p
                                            class="text-[10px]
                                                   font-bold
                                                   uppercase
                                                   tracking-wider
                                                   text-slate-500
                                                   dark:text-slate-400"
                                        >
                                            Temperature Sebelumnya
                                        </p>

                                        <p
                                            class="mt-1.5 text-base
                                                   font-bold"
                                        >
                                            {{
                                                $monitoring
                                                    ?->temperature
                                                ?? '-'
                                            }}
                                        </p>

                                    </div>



                                    {{-- TEMP TERAKHIR --}}

                                    <div
                                        class="rounded-lg
                                               border-2
                                               border-blue-200
                                               bg-blue-50
                                               p-4
                                               dark:border-blue-900
                                               dark:bg-blue-950/30
                                               sm:col-span-2"
                                    >

                                        <label
                                            for="temperature"
                                            class="block
                                                   text-[10px]
                                                   font-bold
                                                   uppercase
                                                   tracking-wider
                                                   text-blue-700
                                                   dark:text-blue-300"
                                        >
                                            Temperature Terakhir
                                        </label>

                                        <input
                                            id="temperature"
                                            type="text"
                                            inputmode="decimal"
                                            wire:model="temperature"
                                            placeholder="Masukkan temperature"
                                            class="mt-2 h-12 w-full
                                                   rounded-lg
                                                   border
                                                   border-blue-200
                                                   bg-white
                                                   px-4
                                                   text-base
                                                   font-semibold
                                                   outline-none
                                                   focus:border-blue-600
                                                   focus:ring-2
                                                   focus:ring-blue-100
                                                   dark:border-blue-800
                                                   dark:bg-slate-950
                                                   dark:text-white
                                                   dark:focus:border-blue-500"
                                        />

                                        @error('temperature')

                                            <p
                                                class="mt-2 text-xs
                                                       font-semibold
                                                       text-red-600
                                                       dark:text-red-400"
                                            >
                                                {{ $message }}
                                            </p>

                                        @enderror

                                    </div>

                                </div>

                            </div>



                            {{-- NOTE --}}

                            <div>

                                <label
                                    for="note"
                                    class="mb-2 block
                                           text-xs
                                           font-bold
                                           uppercase
                                           tracking-wider
                                           text-slate-600
                                           dark:text-slate-300"
                                >
                                    Note
                                </label>

                                <textarea
                                    id="note"
                                    wire:model="note"
                                    rows="4"
                                    placeholder="Masukkan catatan jika diperlukan..."
                                    class="w-full rounded-lg
                                           border
                                           border-slate-300
                                           bg-white
                                           px-4 py-3
                                           text-sm
                                           outline-none
                                           transition
                                           focus:border-blue-600
                                           focus:ring-2
                                           focus:ring-blue-100
                                           dark:border-slate-700
                                           dark:bg-slate-950
                                           dark:text-white
                                           dark:focus:border-blue-500"
                                ></textarea>

                            </div>



                            {{-- ACTION --}}

                            <div
                                class="border-t
                                       border-slate-200
                                       pt-5
                                       dark:border-slate-800"
                            >

                                <button
                                    type="button"
                                    wire:click="unplugReefer"
                                    wire:loading.attr="disabled"
                                    wire:target="unplugReefer"
                                    class="inline-flex h-12 w-full
                                           items-center justify-center
                                           gap-2 rounded-lg
                                           bg-blue-600
                                           px-6
                                           text-sm font-bold
                                           text-white
                                           transition
                                           hover:bg-blue-700
                                           disabled:cursor-not-allowed
                                           disabled:opacity-50
                                           sm:w-auto"
                                >

                                    <span
                                        wire:loading.remove
                                        wire:target="unplugReefer"
                                        class="material-symbols-outlined
                                               text-[20px]"
                                    >
                                        power_off
                                    </span>

                                    <span
                                        wire:loading.remove
                                        wire:target="unplugReefer"
                                    >
                                        UNPLUGIN REEFER
                                    </span>


                                    <span
                                        wire:loading
                                        wire:target="unplugReefer"
                                        class="inline-flex items-center gap-2"
                                    >

                                        <span
                                            class="material-symbols-outlined
                                                   animate-spin text-[19px]"
                                        >
                                            progress_activity
                                        </span>

                                        MEMPROSES...

                                    </span>

                                </button>

                            </div>

                        </div>

                    @endif

                </div>

            </div>

        @endif



        {{-- ============================================================
            EMPTY STATE
        ============================================================= --}}

        @if(
            $hasSearched
            && empty($operations)
            && !$selectedOperation
        )

            <div
                class="mt-5 overflow-hidden
                       rounded-xl border
                       border-slate-200
                       bg-white shadow-sm
                       dark:border-slate-800
                       dark:bg-slate-900"
            >

                <div
                    class="px-5 py-14
                           text-center sm:px-6"
                >

                    <div
                        class="mx-auto flex h-16 w-16
                               items-center justify-center
                               rounded-full
                               bg-blue-50
                               text-blue-600
                               dark:bg-blue-950
                               dark:text-blue-400"
                    >

                        <span
                            class="material-symbols-outlined text-[30px]"
                        >
                            ac_unit
                        </span>

                    </div>


                    <p
                        class="mt-4 text-sm font-bold
                               text-slate-700
                               dark:text-slate-200"
                    >
                        Data container reefer tidak ditemukan
                    </p>


                    <p
                        class="mx-auto mt-1 max-w-sm
                               text-xs leading-5
                               text-slate-500
                               dark:text-slate-400"
                    >
                        Silakan periksa kembali nomor container
                        yang dimasukkan kemudian lakukan pencarian ulang.
                    </p>

                </div>

            </div>

        @endif



        {{-- ============================================================
            FOOTER
        ============================================================= --}}

        <footer
            class="mt-6 border-t
                   border-slate-200
                   pt-5 text-center
                   dark:border-slate-800"
        >

            <p
                class="text-xs
                       text-slate-400
                       dark:text-slate-500"
            >
                PLUG REEFER · PortOps Central
            </p>

        </footer>

    </div>

</div>
