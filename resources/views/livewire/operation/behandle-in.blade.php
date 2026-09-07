<div class="min-h-screen bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-white">

    <div class="mx-auto w-full max-w-5xl px-4 py-6 sm:px-6 lg:px-8">

        {{-- HEADER --}}
        <div class="mb-5 flex items-center justify-between">

            <a
                href="{{ route('dashboard') }}"
                wire:navigate
                class="inline-flex min-h-10 items-center gap-2 rounded-md border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-100 dark:border-white/10 dark:bg-white/5 dark:text-slate-200"
            >
                <flux:icon.arrow-left class="size-4" />

                Menu Handheld
            </a>

            <span
                class="rounded-md bg-sky-100 px-3 py-2 text-xs font-bold uppercase text-sky-700 dark:bg-sky-400/10 dark:text-sky-300"
            >
                Behandle In
            </span>

        </div>


        {{-- SEARCH --}}
        <div
            class="rounded-lg border border-slate-200
                   bg-white p-4 shadow-sm
                   dark:border-white/10 dark:bg-slate-950"
        >

            <label
                for="search-cont"
                class="text-sm font-semibold"
            >
                Nomor Kontainer
            </label>

            <div class="mt-2 flex gap-3">

                <input
                    id="search-cont"
                    type="text"
                    wire:model="searchCont"
                    wire:keydown.enter="search"
                    autocomplete="off"
                    autofocus
                    placeholder="SEARCH NO CONT"
                    class="h-12 min-w-0 flex-1 rounded-md
                           border border-slate-200
                           bg-white px-3 text-sm font-medium
                           outline-none
                           focus:border-sky-500
                           focus:ring-2 focus:ring-sky-500/20
                           dark:border-white/10
                           dark:bg-slate-950"
                >

                <button
                    type="button"
                    wire:click="search"
                    wire:loading.attr="disabled"
                    wire:target="search"
                    class="inline-flex h-12 items-center
                           justify-center rounded-md
                           bg-sky-700 px-5 text-sm font-bold
                           text-white hover:bg-sky-800
                           disabled:opacity-60"
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

                @if ($searchCont !== '' || $container)

                    <button
                        type="button"
                        wire:click="resetSearch"
                        class="inline-flex h-12 items-center
                               justify-center rounded-md
                               border border-slate-200
                               bg-white px-5 text-sm font-bold
                               text-slate-700
                               hover:bg-slate-100
                               dark:border-white/10
                               dark:bg-white/5
                               dark:text-slate-200"
                    >
                        RESET
                    </button>

                @endif

            </div>

            @error('searchCont')

                <p class="mt-2 text-sm text-red-600">
                    {{ $message }}
                </p>

            @enderror

        </div>


        {{-- MESSAGE --}}
        @if ($behandleMessage)

            <div
                class="mt-4 rounded-lg border px-4 py-3
                {{ $behandleMessageType === 'success'
                    ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                    : 'border-red-200 bg-red-50 text-red-700' }}"
            >
                {{ $behandleMessage }}
            </div>

        @endif


        {{-- SEARCH RESULT --}}
        @if (
            $container &&
            $operation &&
            ! $showForm
        )

            <div
                class="mt-5 rounded-lg
                       border border-slate-200
                       bg-white p-4 shadow-sm
                       dark:border-white/10
                       dark:bg-slate-950"
            >

                <div
                    class="mb-3 text-xs font-semibold
                           uppercase tracking-wide
                           text-slate-500"
                >
                    Search Result
                </div>


                <button
                    type="button"
                    wire:click="openResult"
                    wire:loading.attr="disabled"
                    wire:target="openResult"
                    class="group flex w-full items-center
                           justify-between gap-4 rounded-lg
                           border border-slate-200
                           bg-slate-50 p-4 text-left
                           transition
                           hover:border-sky-500
                           hover:bg-sky-50
                           disabled:opacity-60
                           dark:border-white/10
                           dark:bg-white/5
                           dark:hover:border-sky-500"
                >

                    <div class="min-w-0">

                        <div
                            class="text-base font-bold
                                   text-slate-900
                                   dark:text-white"
                        >
                            {{ $container->container?->no_cont ?? '-' }}
                        </div>

                        <div
                            class="mt-1 text-sm
                                   text-slate-500
                                   dark:text-slate-400"
                        >
                            SPK :
                            {{ $container->spk?->no_spk ?? '-' }}
                        </div>

                        <div
                            class="mt-1 text-xs
                                   text-slate-500
                                   dark:text-slate-400"
                        >
                            ISO CODE :
                            {{ $container->container?->type?->iso_code ?? '-' }}
                        </div>

                    </div>


                    <div class="shrink-0">

                        <span
                            wire:loading.remove
                            wire:target="openResult"
                            class="inline-flex h-10
                                   items-center justify-center
                                   rounded-md bg-sky-700
                                   px-5 text-sm font-bold
                                   text-white
                                   group-hover:bg-sky-800"
                        >
                            BUKA DATA
                        </span>

                        <span
                            wire:loading
                            wire:target="openResult"
                            class="inline-flex h-10
                                   items-center justify-center
                                   rounded-md bg-slate-400
                                   px-5 text-sm font-bold
                                   text-white"
                        >
                            LOADING...
                        </span>

                    </div>

                </button>

            </div>

        @endif


        {{-- FORM --}}
        @if (
            $container &&
            $operation &&
            $showForm
        )

            <div
                class="mt-5 overflow-hidden
                       rounded-lg border
                       border-slate-200 bg-white
                       shadow-sm
                       dark:border-white/10
                       dark:bg-slate-950"
            >

                <div class="p-4 sm:p-5">


                    {{-- NO CONT --}}
                    <div class="mb-4">

                        <label
                            for="no-cont"
                            class="mb-1 block text-xs
                                   font-semibold uppercase
                                   text-slate-500"
                        >
                            NO CONT
                        </label>

                        <input
                            id="no-cont"
                            type="text"
                            value="{{ $container->container?->no_cont ?? '' }}"
                            readonly
                            class="h-11 w-full rounded-md
                                   border border-slate-200
                                   bg-slate-100 px-3
                                   text-sm font-semibold
                                   text-slate-700
                                   dark:border-white/10
                                   dark:bg-white/5
                                   dark:text-slate-200"
                        >

                    </div>


                    {{-- NO SPK --}}
                    <div class="mb-4">

                        <label
                            for="no-spk"
                            class="mb-1 block text-xs
                                   font-semibold uppercase
                                   text-slate-500"
                        >
                            NO SPK
                        </label>

                        <input
                            id="no-spk"
                            type="text"
                            value="{{ $container->spk?->no_spk ?? '' }}"
                            readonly
                            class="h-11 w-full rounded-md
                                   border border-slate-200
                                   bg-slate-100 px-3
                                   text-sm font-semibold
                                   text-slate-700
                                   dark:border-white/10
                                   dark:bg-white/5
                                   dark:text-slate-200"
                        >

                    </div>


                    {{-- ISO CODE --}}
                    <div class="mb-4">

                        <label
                            for="iso-code"
                            class="mb-1 block text-xs
                                   font-semibold uppercase
                                   text-slate-500"
                        >
                            ISO CODE
                        </label>

                        <input
                            id="iso-code"
                            type="text"
                            wire:model="isoCode"
                            maxlength="10"
                            required
                            class="h-11 w-full rounded-md
                                   border border-slate-200
                                   bg-white px-3 text-sm
                                   uppercase outline-none
                                   focus:border-sky-500
                                   focus:ring-2
                                   focus:ring-sky-500/20
                                   dark:border-white/10
                                   dark:bg-slate-950"
                        >

                        @error('isoCode')

                            <p class="mt-1 text-xs text-red-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- KONDISI SEAL --}}
                    <div class="mb-4">

                        <label
                            class="mb-2 block text-xs
                                   font-semibold uppercase
                                   text-slate-500"
                        >
                            KONDISI SEAL
                        </label>

                        <div class="flex gap-6">

                            <label
                                class="inline-flex items-center gap-2"
                            >

                                <input
                                    type="radio"
                                    wire:model.live="sealCondition"
                                    value="ADA"
                                >

                                <span class="text-sm">
                                    ADA
                                </span>

                            </label>


                            <label
                                class="inline-flex items-center gap-2"
                            >

                                <input
                                    type="radio"
                                    wire:model.live="sealCondition"
                                    value="TIDAK ADA"
                                >

                                <span class="text-sm">
                                    TIDAK ADA
                                </span>

                            </label>

                        </div>

                    </div>


                    {{-- NO SEAL --}}
                    <div class="mb-4">

                        <label
                            for="no-seal"
                            class="mb-1 block text-xs
                                   font-semibold uppercase
                                   text-slate-500"
                        >
                            NO SEAL
                        </label>

                        <input
                            id="no-seal"
                            type="text"
                            wire:model="noSeal"
                            @disabled($sealCondition === 'TIDAK ADA')
                            placeholder="NO SEAL"
                            class="h-11 w-full rounded-md
                                   border border-slate-200
                                   bg-white px-3 text-sm
                                   outline-none
                                   focus:border-sky-500
                                   focus:ring-2
                                   focus:ring-sky-500/20
                                   disabled:bg-slate-100
                                   dark:border-white/10
                                   dark:bg-slate-950
                                   dark:disabled:bg-white/5"
                        >

                        @error('noSeal')

                            <p class="mt-1 text-xs text-red-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- KONDISI KONTAINER --}}
                    <div class="mb-4">

                        <label
                            for="container-condition-id"
                            class="mb-1 block text-xs
                                   font-semibold uppercase
                                   text-slate-500"
                        >
                            KONDISI KONTAINER
                        </label>

                        <select
                            id="container-condition-id"
                            wire:model="containerConditionId"
                            required
                            class="h-11 w-full rounded-md
                                   border border-slate-200
                                   bg-white px-3 text-sm
                                   outline-none
                                   focus:border-sky-500
                                   focus:ring-2
                                   focus:ring-sky-500/20
                                   dark:border-white/10
                                   dark:bg-slate-950"
                        >

                            <option value="">
                                KONDISI KONTAINER
                            </option>

                            @foreach ($containerConditions as $condition)

                                <option
                                    value="{{ $condition->id }}"
                                >
                                    {{ $condition->name }}
                                </option>

                            @endforeach

                        </select>

                        @error('containerConditionId')

                            <p class="mt-1 text-xs text-red-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- TID --}}
                    <div class="mb-4">

                        <label
                            for="truck-id"
                            class="mb-1 block text-xs
                                   font-semibold uppercase
                                   text-slate-500"
                        >
                            TID
                        </label>

                        <select
                            id="truck-id"
                            wire:model="truckId"
                            required
                            class="h-11 w-full rounded-md
                                   border border-slate-200
                                   bg-white px-3 text-sm
                                   outline-none
                                   focus:border-sky-500
                                   focus:ring-2
                                   focus:ring-sky-500/20
                                   dark:border-white/10
                                   dark:bg-slate-950"
                        >

                            <option value="">
                                TID
                            </option>

                            @foreach ($trucks as $truck)

                                <option
                                    value="{{ $truck->id }}"
                                >
                                    {{ $truck->id }}

                                    @if (
                                        !empty($truck->no_truck) ||
                                        !empty($truck->no_plat)
                                    )

                                        -
                                        {{ $truck->no_truck }}

                                        @if (!empty($truck->no_plat))
                                            - {{ $truck->no_plat }}
                                        @endif

                                    @endif

                                </option>

                            @endforeach

                        </select>

                        @error('truckId')

                            <p class="mt-1 text-xs text-red-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- LOKASI --}}
                    <div class="mb-4">

                        <label
                            for="location-id"
                            class="mb-1 block text-xs
                                   font-semibold uppercase
                                   text-slate-500"
                        >
                            LOKASI
                        </label>

                        <select
                            id="location-id"
                            wire:model="locationId"
                            required
                            class="h-11 w-full rounded-md
                                   border border-slate-200
                                   bg-white px-3 text-sm
                                   outline-none
                                   focus:border-sky-500
                                   focus:ring-2
                                   focus:ring-sky-500/20
                                   dark:border-white/10
                                   dark:bg-slate-950"
                        >

                            <option value="">
                                LOKASI
                            </option>

                            @foreach ($locations as $location)

                                <option
                                    value="{{ $location->id }}"
                                >
                                    {{ $location->location_code }}
                                </option>

                            @endforeach

                        </select>

                        @error('locationId')

                            <p class="mt-1 text-xs text-red-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- STATUS KONTAINER --}}
                    <div class="mb-4">

                        <label
                            class="mb-2 block text-xs
                                   font-semibold uppercase
                                   text-slate-500"
                        >
                            STATUS KONTAINER
                        </label>

                        <div class="flex gap-6">

                            <label
                                class="inline-flex items-center gap-2"
                            >

                                <input
                                    type="radio"
                                    wire:model="loadStatus"
                                    value="F"
                                >

                                <span class="text-sm">
                                    FULL
                                </span>

                            </label>


                            <label
                                class="inline-flex items-center gap-2"
                            >

                                <input
                                    type="radio"
                                    wire:model="loadStatus"
                                    value="E"
                                >

                                <span class="text-sm">
                                    EMPTY
                                </span>

                            </label>

                        </div>

                        @error('loadStatus')

                            <p class="mt-1 text-xs text-red-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- UKURAN --}}
                    <div class="mb-4">

                        <label
                            for="container-size"
                            class="mb-1 block text-xs
                                   font-semibold uppercase
                                   text-slate-500"
                        >
                            UKURAN
                        </label>

                        <select
                            id="container-size"
                            wire:model="containerSize"
                            required
                            class="h-11 w-full rounded-md
                                   border border-slate-200
                                   bg-white px-3 text-sm
                                   outline-none
                                   focus:border-sky-500
                                   focus:ring-2
                                   focus:ring-sky-500/20
                                   dark:border-white/10
                                   dark:bg-slate-950"
                        >

                            <option value="20">
                                20
                            </option>

                            <option value="40">
                                40
                            </option>

                            <option value="45">
                                45
                            </option>

                        </select>

                        @error('containerSize')

                            <p class="mt-1 text-xs text-red-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- TYPE CONTAINER --}}
                    <div class="mb-4">

                        <label
                            for="container-type"
                            class="mb-1 block text-xs
                                   font-semibold uppercase
                                   text-slate-500"
                        >
                            TYPE CONTAINER
                        </label>

                        <select
                            id="container-type"
                            wire:model="containerTypeCode"
                            required
                            class="h-11 w-full rounded-md
                                   border border-slate-200
                                   bg-white px-3 text-sm
                                   outline-none
                                   focus:border-sky-500
                                   focus:ring-2
                                   focus:ring-sky-500/20
                                   dark:border-white/10
                                   dark:bg-slate-950"
                        >

                            <option value="DRY">
                                DRY
                            </option>

                            <option value="HQ">
                                HQ
                            </option>

                            <option value="OVD">
                                OVD
                            </option>

                            <option value="TNK">
                                TNK
                            </option>

                            <option value="OT">
                                OT
                            </option>

                            <option value="RFR">
                                RFR
                            </option>

                        </select>

                        @error('containerTypeCode')

                            <p class="mt-1 text-xs text-red-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- LABEL --}}
                    <div class="mb-6">

                        <label
                            for="label"
                            class="mb-1 block text-xs
                                   font-semibold uppercase
                                   text-slate-500"
                        >
                            LABEL
                        </label>

                        <select
                            id="label"
                            wire:model="label"
                            required
                            class="h-11 w-full rounded-md
                                   border border-slate-200
                                   bg-white px-3 text-sm
                                   outline-none
                                   focus:border-sky-500
                                   focus:ring-2
                                   focus:ring-sky-500/20
                                   dark:border-white/10
                                   dark:bg-slate-950"
                        >

                            <option value="NON DG">
                                NON DG
                            </option>

                            <option value="DG">
                                DG
                            </option>

                            <option value="DG NON LABEL">
                                DG NON LABEL
                            </option>

                        </select>

                        @error('label')

                            <p class="mt-1 text-xs text-red-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- INFORMASI ALAT --}}
                    <div
                        class="border-t
                               border-slate-200
                               pt-5
                               dark:border-white/10"
                    >

                        <h3
                            class="mb-4 text-base font-bold
                                   uppercase"
                        >
                            INFORMASI ALAT:
                        </h3>


                        {{-- JENIS PEKERJAAN --}}
                        <div class="mb-4">

                            <label
                                for="job-activity-code"
                                class="mb-1 block text-xs
                                       font-semibold uppercase
                                       text-slate-500"
                            >
                                Jenis Pekerjaan
                            </label>

                            <select
                                id="job-activity-code"
                                wire:model="jobActivityCode"
                                class="h-11 w-full rounded-md
                                       border border-slate-200
                                       bg-white px-3 text-sm
                                       outline-none
                                       focus:border-sky-500
                                       focus:ring-2
                                       focus:ring-sky-500/20
                                       dark:border-white/10
                                       dark:bg-slate-950"
                            >

                                <option value="BEHANDLE 1">
                                    LIFT OFF YARD - PRE
                                </option>

                                <option value="">
                                    Tidak Ada Kegiatan
                                </option>

                            </select>

                            @error('jobActivityCode')

                                <p class="mt-1 text-xs text-red-600">
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>


                        {{-- ALAT --}}
                        <div class="mb-4">

                            <label
                                for="equipment-id"
                                class="mb-1 block text-xs
                                       font-semibold uppercase
                                       text-slate-500"
                            >
                                Alat
                            </label>

                            <select
                                id="equipment-id"
                                wire:model="equipmentId"
                                required
                                class="h-11 w-full rounded-md
                                       border border-slate-200
                                       bg-white px-3 text-sm
                                       outline-none
                                       focus:border-sky-500
                                       focus:ring-2
                                       focus:ring-sky-500/20
                                       dark:border-white/10
                                       dark:bg-slate-950"
                            >

                                <option value="">
                                    ---Pilih Alat---
                                </option>

                                @foreach ($equipments as $equipment)

                                    <option
                                        value="{{ $equipment->id }}"
                                    >
                                        {{ $equipment->id }}

                                        @if (!empty($equipment->name))
                                            - {{ $equipment->name }}
                                        @endif

                                    </option>

                                @endforeach

                            </select>

                            @error('equipmentId')

                                <p class="mt-1 text-xs text-red-600">
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>


                        {{-- OPERATOR --}}
                        <div class="mb-4">

                            <label
                                for="operator-id"
                                class="mb-1 block text-xs
                                       font-semibold uppercase
                                       text-slate-500"
                            >
                                Operator
                            </label>

                            <select
                                id="operator-id"
                                wire:model="operatorId"
                                required
                                class="h-11 w-full rounded-md
                                       border border-slate-200
                                       bg-white px-3 text-sm
                                       outline-none
                                       focus:border-sky-500
                                       focus:ring-2
                                       focus:ring-sky-500/20
                                       dark:border-white/10
                                       dark:bg-slate-950"
                            >

                                <option value="">
                                    ---Pilih Operator---
                                </option>

                                @foreach ($operators as $operator)

                                    <option
                                        value="{{ $operator->id }}"
                                    >
                                        {{ $operator->name }}
                                    </option>

                                @endforeach

                            </select>

                            @error('operatorId')

                                <p class="mt-1 text-xs text-red-600">
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>


                        {{-- JOIN INSPECTION --}}
                        <div class="mb-4">

                            <label
                                class="inline-flex items-center gap-3"
                            >

                                <input
                                    type="checkbox"
                                    wire:model="joinInspection"
                                    class="size-4 rounded
                                           border-slate-300"
                                >

                                <span
                                    class="text-sm font-semibold"
                                >
                                    Join Inspection
                                </span>

                            </label>

                        </div>


                        {{-- NOTE --}}
                        <div class="mb-4">

                            <label
                                for="note"
                                class="mb-1 block text-xs
                                       font-semibold uppercase
                                       text-slate-500"
                            >
                                Note
                            </label>

                            <textarea
                                id="note"
                                wire:model="note"
                                rows="4"
                                placeholder="NOTE"
                                class="w-full rounded-md
                                       border border-slate-200
                                       bg-white px-3 py-2
                                       text-sm outline-none
                                       focus:border-sky-500
                                       focus:ring-2
                                       focus:ring-sky-500/20
                                       dark:border-white/10
                                       dark:bg-slate-950"
                            ></textarea>

                            @error('note')

                                <p class="mt-1 text-xs text-red-600">
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>

                    </div>


                    {{-- ACTION --}}
                    <div class="mt-5 flex flex-wrap gap-3">

                        <button
                            type="button"
                            wire:click="send"
                            wire:loading.attr="disabled"
                            wire:target="send"
                            class="inline-flex h-11
                                   items-center justify-center
                                   rounded-md bg-sky-700
                                   px-6 text-sm font-bold
                                   text-white
                                   hover:bg-sky-800
                                   disabled:opacity-60"
                        >

                            <span
                                wire:loading.remove
                                wire:target="send"
                            >
                                SIMPAN
                            </span>

                            <span
                                wire:loading
                                wire:target="send"
                            >
                                MENYIMPAN...
                            </span>

                        </button>


                        <button
                            type="button"
                            wire:click="resetSearch"
                            class="inline-flex h-11
                                   items-center justify-center
                                   rounded-md border
                                   border-slate-200
                                   bg-white px-6
                                   text-sm font-bold
                                   text-slate-700
                                   hover:bg-slate-100
                                   dark:border-white/10
                                   dark:bg-white/5
                                   dark:text-slate-200"
                        >
                            RESET
                        </button>

                    </div>

                </div>

            </div>

        @endif

    </div>

</div>