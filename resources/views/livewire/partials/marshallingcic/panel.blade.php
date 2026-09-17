<div
    class="mx-auto mb-5 max-w-7xl"
    data-panel
    data-panel-name="marshallingcic"
    data-data-url="{{ route('marshallingcic.data') }}"
    data-search-url="{{ route('marshallingcic.search') }}"
    data-detail-url="{{ route('marshallingcic.detail') }}"
    data-csrf-token="{{ csrf_token() }}"
>
    <div class="mb-4 flex items-center justify-between gap-3">
        <a
            href="{{ route('dashboard') }}"
            wire:navigate
            data-back-menu
            class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5
                   text-sm font-semibold text-slate-700 shadow-sm ring-1
                   ring-slate-200 hover:bg-slate-50
                   dark:bg-slate-900 dark:text-slate-200 dark:ring-slate-800"
        >
            ← Menu Handheld
        </a>

        <span
            class="rounded-full bg-sky-100 px-3 py-1 text-xs font-bold
                   text-sky-700 dark:bg-sky-950 dark:text-sky-300"
        >
            Marshalling CIC
        </span>
    </div>

    {{-- SEARCH --}}
    <div
        class="mb-5 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200
               dark:bg-slate-900 dark:ring-slate-800"
    >
        <form data-search-form>
            <label
                for="marshalling-cic-search"
                class="mb-2 block text-sm font-semibold text-slate-800
                       dark:text-slate-100"
            >
                Nomor Container
            </label>

            <div class="flex flex-col gap-2 sm:flex-row">
                <input
                    id="marshalling-cic-search"
                    name="no_cont"
                    type="text"
                    autocomplete="off"
                    placeholder="SEARCH NO. CONTAINER"
                    class="min-h-11 w-full rounded-xl border border-slate-300
                           bg-white px-3 py-2.5 text-sm uppercase outline-none
                           focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20
                           dark:border-slate-700 dark:bg-slate-950"
                >

                <div class="flex gap-2">
                    <button
                        type="submit"
                        data-search-button
                        class="min-h-11 rounded-xl bg-sky-600 px-5 py-2.5
                               text-sm font-semibold text-white
                               hover:bg-sky-700 disabled:cursor-not-allowed
                               disabled:opacity-60"
                    >
                        Search
                    </button>

                    <button
                        type="button"
                        data-reset-button
                        class="min-h-11 rounded-xl bg-slate-100 px-5 py-2.5
                               text-sm font-semibold text-slate-700
                               hover:bg-slate-200
                               dark:bg-slate-800 dark:text-slate-200"
                    >
                        Reset
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- MONITORING TABLE --}}
    <div data-table-container>
        <div
            class="rounded-2xl bg-white p-6 text-center shadow-sm
                   ring-1 ring-slate-200 dark:bg-slate-900
                   dark:ring-slate-800"
        >
            <span class="text-sm text-slate-500">
                Memuat data Marshalling CIC...
            </span>
        </div>
    </div>

    {{-- DETAIL CONTAINER --}}
    <div
        data-detail-container
        class="mt-5 hidden"
    ></div>
</div>