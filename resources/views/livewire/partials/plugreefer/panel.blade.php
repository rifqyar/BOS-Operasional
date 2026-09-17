<div
    class="mx-auto mb-5 max-w-3xl"
    data-panel
    data-panel-name="plugreefer"
    data-search-url="{{ route('plugreefer.search') }}"
    data-detail-url="{{ route('plugreefer.detail') }}"
    data-store-url="{{ route('plugreefer.store') }}"
    data-csrf-token="{{ csrf_token() }}"
>
    {{-- HEADER --}}
    <div class="mb-4 flex items-center justify-between gap-3">

        <a
            href="{{ route('dashboard') }}"
            wire:navigate
            data-back-menu
            class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800"
        >
            ← Menu Handheld
        </a>

        <span class="rounded-full bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 dark:bg-blue-950/40 dark:text-blue-300">
            Plug Reefer
        </span>

    </div>


    {{-- SEARCH --}}
    <form data-search-form>

        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-900">

            <div class="flex flex-col gap-3 sm:flex-row">

                <div class="flex-1">

                    <label
                        for="panel-no-container"
                        class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200"
                    >
                        No Container
                    </label>

                    <input
                        type="text"
                        id="panel-no-container"
                        name="no_cont"
                        maxlength="30"
                        autocomplete="off"
                        placeholder="SEARCH NO CONT"
                        required
                        autofocus
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm font-semibold uppercase text-slate-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                    >

                </div>

                <div class="flex items-end">

                    <button
                        type="submit"
                        data-search-button
                        class="inline-flex w-full items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto"
                    >
                        SEARCH
                    </button>

                </div>

            </div>

        </div>

    </form>


    {{-- RESULT --}}
    <div
        data-result
        class="mt-4 hidden"
    >

        <div
            data-table
            class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900"
        ></div>

        <div
            data-detail
            class="mt-5 hidden"
        ></div>

    </div>

</div>