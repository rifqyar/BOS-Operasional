@php
    $row = $row ?? null;
    $spk = $spk ?? null;
    $checked = $checked ?? null;
    $tempAkhir = $tempAkhir ?? null;

    $condition = $condition ?? 0;

    $noCont = $row->NO_CONT ?? '';
    $suhuCust = $row->SUHU_CUST ?? '';
    $suhuTerminal = $row->SUHU_TERMINAL ?? '';

    $lastTemperature = $lastTemperature ?? '';
@endphp

<div
    data-plugreefer-form
    class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900"
>

    {{-- HEADER --}}

    <div class="border-b border-slate-200 px-4 py-4 dark:border-slate-700">

        <div class="flex items-center justify-between gap-3">

            <div>

                <h3 class="text-base font-bold text-slate-900 dark:text-white">
                    Plug Reefer
                </h3>

                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                    {{ $noCont }}
                </p>

            </div>

            <button
                type="button"
                data-close-detail
                class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800"
            >
                ✕
            </button>

        </div>

    </div>


    {{-- BODY --}}

    <div class="p-4">


        {{-- ============================================================ --}}
        {{-- CONDITION 1 : MULAI PLUGIN --}}
        {{-- ============================================================ --}}

        @if ($condition === 1)

            <form
                data-send-form
                class="space-y-5"
            >

                <input
                    type="hidden"
                    name="nomerkon"
                    value="{{ $noCont }}"
                >

                <input
                    type="hidden"
                    name="action"
                    value="plugin"
                >


                {{-- CONTAINER --}}

                <div>

                    <label class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                        NO CONTAINER
                    </label>

                    <input
                        type="text"
                        value="{{ $noCont }}"
                        readonly
                        class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                    >

                </div>


                {{-- TEMPERATURE DEFAULT --}}

                <div>

                    <label class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                        TEMPERATURE DEFAULT
                    </label>

                    <input
                        type="text"
                        value="{{ $suhuCust }}"
                        readonly
                        class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                    >

                </div>


                {{-- TEMPERATURE TPS --}}

                <div>

                    <label class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                        TEMPERATURE TPS
                    </label>

                    <input
                        type="text"
                        value="{{ $suhuTerminal }}"
                        readonly
                        class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                    >

                </div>


                {{-- TEMPERATURE SAAT INI --}}

                <div>

                    <label
                        for="plug-temperature"
                        class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                    >
                        TEMPERATURE SAAT INI
                    </label>

                    <input
                        type="text"
                        id="plug-temperature"
                        name="temperature"
                        inputmode="decimal"
                        autocomplete="off"
                        required
                        placeholder="Masukkan temperature"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-3 text-sm font-semibold text-slate-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                    >

                </div>


                {{-- NOTE --}}

                <div>

                    <label
                        for="plug-note"
                        class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                    >
                        NOTE
                    </label>

                    <textarea
                        id="plug-note"
                        name="note"
                        rows="5"
                        placeholder="Masukkan catatan jika diperlukan..."
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                    ></textarea>

                </div>


                {{-- ACTION --}}

                <div class="border-t border-slate-200 pt-5 dark:border-slate-700">

                    <button
                        type="submit"
                        class="inline-flex h-12 w-full items-center justify-center rounded-lg bg-blue-600 px-5 text-sm font-bold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto"
                    >
                        MULAI PLUGIN
                    </button>

                </div>

            </form>


        {{-- ============================================================ --}}
        {{-- CONDITION 2 : UNPLUGIN --}}
        {{-- ============================================================ --}}

        @elseif ($condition === 2)

            <form
                data-send-form
                class="space-y-5"
            >

                <input
                    type="hidden"
                    name="nomerkon"
                    value="{{ $noCont }}"
                >

                <input
                    type="hidden"
                    name="action"
                    value="unplugin"
                >


                {{-- CONTAINER --}}

                <div>

                    <label class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                        NO CONTAINER
                    </label>

                    <input
                        type="text"
                        value="{{ $noCont }}"
                        readonly
                        class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                    >

                </div>


                {{-- TEMPERATURE SEBELUMNYA --}}

                <div>

                    <label class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                        TEMPERATURE SEBELUMNYA
                    </label>

                    <input
                        type="text"
                        value="{{ $lastTemperature ?: '-' }}"
                        readonly
                        class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm font-semibold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                    >

                </div>


                {{-- TEMPERATURE TERAKHIR --}}

                <div>

                    <label
                        for="unplug-temperature"
                        class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                    >
                        TEMPERATURE TERAKHIR
                    </label>

                    <input
                        type="text"
                        id="unplug-temperature"
                        name="temperature"
                        inputmode="decimal"
                        autocomplete="off"
                        required
                        placeholder="Masukkan temperature"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-3 text-sm font-semibold text-slate-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                    >

                </div>


                {{-- NOTE --}}

                <div>

                    <label
                        for="unplug-note"
                        class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                    >
                        NOTE
                    </label>

                    <textarea
                        id="unplug-note"
                        name="note"
                        rows="5"
                        placeholder="Masukkan catatan jika diperlukan..."
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                    ></textarea>

                </div>


                {{-- ACTION --}}

                <div class="border-t border-slate-200 pt-5 dark:border-slate-700">

                    <button
                        type="submit"
                        class="inline-flex h-12 w-full items-center justify-center rounded-lg bg-blue-600 px-5 text-sm font-bold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto"
                    >
                        UNPLUGIN REEFER
                    </button>

                </div>

            </form>


        {{-- ============================================================ --}}
        {{-- CONDITION 0 --}}
        {{-- ============================================================ --}}

        @else

            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-4 text-sm font-semibold text-red-700 dark:border-red-900 dark:bg-red-950/30 dark:text-red-300">

                WARNING ! NO CONT :
                {{ $noCont }}
                tidak dalam kondisi yang dapat diproses.

            </div>

        @endif

    </div>

</div>