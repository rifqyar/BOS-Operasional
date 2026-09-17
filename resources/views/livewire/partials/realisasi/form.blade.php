@php
    $equipments = $equipments ?? [];
    $operators = $operators ?? [];
    $conditions = $conditions ?? [];

    $realisasi = $realisasi ?? null;
    $join = $join ?? null;
    $kond = $kond ?? 0;

    $noCont = $row->NO_CONT ?? '';
    $noSpk = $row->NO_SPK ?? '';
    $idJobSlip = $row->ID_JOB_SLIP ?? '';

    $ukuran = $row->UKR_CONT ?? '-';
    $tipeCont = $row->TIPE_CONT ?? '';
@endphp

<div
    data-realisasi-form
    class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900"
>

    <div class="flex items-center justify-between border-b border-slate-200 px-4 py-4 dark:border-slate-700">

        <div>

            <h3 class="text-base font-bold text-slate-900 dark:text-white">
                Pemeriksaan Behandle
            </h3>

            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                {{ $noCont }}
            </p>

        </div>

        <button
            type="button"
            data-close-detail
            class="rounded-lg p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800"
        >
            ✕
        </button>

    </div>


    {{-- ================================================================ --}}
    {{-- BELUM MULAI --}}
    {{-- ================================================================ --}}

    @if ($kond === 0)

        <form
            data-send-form
            class="space-y-5 p-4"
        >

            <input
                type="hidden"
                name="nomerkon"
                value="{{ $noCont }}"
            >

            <input
                type="hidden"
                name="nospk"
                value="{{ $noSpk }}"
            >

            <input
                type="hidden"
                name="idJobSlip"
                value="{{ $idJobSlip }}"
            >

            <input
                type="hidden"
                name="join"
                value="0"
            >

            <div>

                <label class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                    NO CONTAINER
                </label>

                <input
                    type="text"
                    value="{{ $noCont }}"
                    readonly
                    class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm font-semibold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                >

            </div>

            <div>

                <label class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                    NO SPK
                </label>

                <input
                    type="text"
                    value="{{ $noSpk }}"
                    readonly
                    class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                >

            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                <div>

                    <label class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                        UKURAN
                    </label>

                    <input
                        type="text"
                        value="{{ $ukuran }}"
                        readonly
                        class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                    >

                </div>

                <div>

                    <label class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                        TIPE
                    </label>

                    <input
                        type="text"
                        value="{{ $tipeCont }}"
                        readonly
                        class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                    >

                </div>

            </div>

            <button
                type="submit"
                class="w-full rounded-lg bg-blue-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-blue-700 disabled:opacity-60"
            >
                MULAI PERIKSA
            </button>

            <div
                data-form-message
                class="hidden rounded-lg px-3 py-2 text-sm"
            ></div>

        </form>


    {{-- ================================================================ --}}
    {{-- SEDANG BERJALAN --}}
    {{-- ================================================================ --}}

    @elseif ($kond === 1)

        <form
            data-send-form
            class="space-y-5 p-4"
        >

            <input
                type="hidden"
                name="nomerkon"
                value="{{ $noCont }}"
            >

            <input
                type="hidden"
                name="nospk"
                value="{{ $noSpk }}"
            >

            <input
                type="hidden"
                name="idJobSlip"
                value="{{ $idJobSlip }}"
            >

            <input
                type="hidden"
                name="join"
                value="{{ $join ? 1 : 0 }}"
            >


            {{-- NO CONTAINER --}}

            <div>

                <label class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                    NO CONTAINER
                </label>

                <input
                    type="text"
                    value="{{ $noCont }}"
                    readonly
                    class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm font-semibold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                >

            </div>


            {{-- NO SEAL --}}

            <div>

                <label
                    for="noseal"
                    class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                >
                    NO SEAL
                </label>

                <input
                    type="text"
                    id="noseal"
                    name="noseal"
                    value="{{ $realisasi->NO_SEAL ?? '' }}"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                >

            </div>


            {{-- TYPE CONTAINER --}}

            <div>

                <label
                    for="tipecont"
                    class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                >
                    TYPE CONTAINER
                </label>

                <select
                    id="tipecont"
                    name="tipecont"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                >

                    <option value="">
                        --- Pilih Type ---
                    </option>

                    <option
                        value="DRY"
                        @selected($tipeCont === 'DRY')
                    >
                        DRY
                    </option>

                    <option
                        value="HQ"
                        @selected($tipeCont === 'HQ')
                    >
                        HQ
                    </option>

                    <option
                        value="OVD"
                        @selected($tipeCont === 'OVD')
                    >
                        OVD
                    </option>

                    <option
                        value="TNK"
                        @selected($tipeCont === 'TNK')
                    >
                        TNK
                    </option>

                    <option
                        value="OT"
                        @selected($tipeCont === 'OT')
                    >
                        OT
                    </option>

                    <option
                        value="RFR"
                        @selected($tipeCont === 'RFR')
                    >
                        RFR
                    </option>

                </select>

            </div>


            {{-- ALAT --}}

            <div>

                <label
                    for="alat"
                    class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                >
                    DATA ALAT
                </label>

                <select
                    id="alat"
                    name="alat"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                >

                    <option value="">
                        --- Pilih Alat ---
                    </option>

                    @foreach ($equipments as $equipment)

                        <option value="{{ $equipment->ID ?? '' }}">
                            {{ $equipment->NM_ALAT ?? '' }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- OPERATOR --}}

            <div>

                <label
                    for="operator"
                    class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                >
                    OPERATOR
                </label>

                <select
                    id="operator"
                    name="operator"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                >

                    <option value="">
                        --- Pilih Operator ---
                    </option>

                    @foreach ($operators as $operator)

                        <option value="{{ $operator->ID ?? '' }}">
                            {{ $operator->NAMA ?? '' }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- JOIN --}}

            @if ($join)

                <div class="rounded-lg bg-amber-50 p-4 text-sm text-amber-900 dark:bg-amber-950/30 dark:text-amber-300">

                    Dokumen ini adalah dokumen join inspection
                    dengan No Aju

                    <strong>
                        {{ $join->LNSW_NOAJU ?? '-' }}
                    </strong>.

                </div>

            @endif


            <button
                type="submit"
                class="w-full rounded-lg bg-blue-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-blue-700 disabled:opacity-60"
            >
                SELESAI PEMERIKSAAN
            </button>

            <div
                data-form-message
                class="hidden rounded-lg px-3 py-2 text-sm"
            ></div>

        </form>


    {{-- ================================================================ --}}
    {{-- SELESAI --}}
    {{-- ================================================================ --}}

    @else

        <div class="p-4">

            <div class="rounded-lg bg-green-50 p-4 text-sm font-semibold text-green-700 dark:bg-green-950/30 dark:text-green-300">

                No Container
                <strong>
                    {{ $noCont }}
                </strong>
                sudah selesai pemeriksaan.

            </div>

        </div>

    @endif

</div>