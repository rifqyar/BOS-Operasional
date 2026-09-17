<div
    class="mx-auto mb-5 max-w-7xl"
    data-panel
    data-panel-name="marshallingyard"

    data-data-url="{{ route('marshallingyard.data') }}"
    data-search-url="{{ route('marshallingyard.search') }}"
    data-detail-url="{{ route('marshallingyard.detail') }}"
    data-store-url="{{ route('marshallingyard.store') }}"

    data-csrf-token="{{ csrf_token() }}"
>
    {{-- HEADER --}}
    <div class="mb-4 flex items-center justify-between gap-3">
        <a
            href="{{ route('dashboard') }}"
            wire:navigate
            data-back-menu
            class="inline-flex min-h-11 items-center gap-2 rounded-xl
                   bg-white px-4 py-2.5 text-sm font-semibold
                   text-slate-700 shadow-sm ring-1 ring-slate-200
                   transition hover:bg-slate-50
                   dark:bg-slate-900 dark:text-slate-200
                   dark:ring-slate-800"
        >
            <flux:icon.arrow-left class="h-4 w-4" />
            <span>Menu Handheld</span>
        </a>

        <span
            class="rounded-xl bg-sky-100 px-3 py-2 text-xs font-bold
                   text-sky-700 dark:bg-sky-950 dark:text-sky-300"
        >
            Marshalling Yard
        </span>
    </div>

    {{-- SEARCH --}}
    <div
        class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200
               dark:bg-slate-900 dark:ring-slate-800"
    >
        <div class="mb-4">
            <h2 class="text-base font-bold text-slate-900 dark:text-white">
                Search Container
            </h2>

            <p class="mt-1 text-xs leading-5 text-slate-500 dark:text-slate-400">
                Cari No Container untuk menampilkan Job Slip Marshalling Yard
                yang dapat diproses.
            </p>
        </div>

        <form
            data-search-form
            class="flex flex-col gap-3 sm:flex-row"
        >
            <div class="min-w-0 flex-1">
                <label
                    for="marshalling-yard-search"
                    class="mb-1.5 block text-xs font-medium
                           text-slate-600 dark:text-slate-300"
                >
                    No Container
                </label>

                <input
                    id="marshalling-yard-search"
                    name="no_cont"
                    type="text"
                    autocomplete="off"
                    inputmode="text"
                    placeholder="SEARCH NO. CONTAINER"
                    class="min-h-12 w-full rounded-xl border
                           border-slate-300 bg-white px-3 text-sm
                           uppercase outline-none
                           focus:border-sky-500
                           focus:ring-2 focus:ring-sky-500/20
                           dark:border-slate-700 dark:bg-slate-950"
                >
            </div>

            <div class="flex gap-2 sm:items-end">
                <button
                    type="submit"
                    data-search-button
                    class="min-h-12 w-full rounded-xl bg-sky-600
                           px-5 py-2.5 text-sm font-bold text-white
                           transition hover:bg-sky-700
                           disabled:cursor-not-allowed
                           disabled:opacity-60 sm:w-auto"
                >
                    Search
                </button>

                <button
                    type="button"
                    data-reset-button
                    class="min-h-12 w-full rounded-xl bg-slate-100
                           px-5 py-2.5 text-sm font-semibold
                           text-slate-700 transition hover:bg-slate-200
                           dark:bg-slate-800 dark:text-slate-200
                           dark:hover:bg-slate-700 sm:w-auto"
                >
                    Reset
                </button>
            </div>
        </form>
    </div>

    {{-- MONITORING --}}
    <div
        class="mt-5 overflow-hidden rounded-2xl bg-white shadow-sm
               ring-1 ring-slate-200 dark:bg-slate-900
               dark:ring-slate-800"
    >
        <div
            class="border-b border-slate-200 px-4 py-4
                   dark:border-slate-800 sm:px-5"
        >
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">
                        Daftar Marshalling Yard
                    </h2>

                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        Pilih pekerjaan yang akan diproses.
                    </p>
                </div>

                <span
                    data-data-count
                    class="rounded-full bg-slate-100 px-3 py-1
                           text-xs font-semibold text-slate-600
                           dark:bg-slate-800 dark:text-slate-300"
                >
                    -
                </span>
            </div>
        </div>

        <div
            data-table
            class="overflow-x-auto"
        >
            <div class="px-4 py-10 text-center text-sm text-slate-500">
                Memuat data Marshalling Yard...
            </div>
        </div>
    </div>

    {{-- DETAIL --}}
    <div
        data-detail
        class="mt-5 hidden"
    ></div>
</div>