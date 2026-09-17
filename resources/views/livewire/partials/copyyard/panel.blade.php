<div
    class="mx-auto mb-5 w-full max-w-4xl"
    data-panel
    data-panel-name="copyyard"
    data-search-url="{{ route('copyyard.search') }}"
    data-store-url="{{ route('copyyard.store') }}"
    data-csrf-token="{{ csrf_token() }}">

    {{-- ============================================================
         HEADER
    ============================================================= --}}

    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

        <a
            href="{{ route('dashboard') }}"
            wire:navigate
            data-back-menu
            class="inline-flex min-h-10 w-fit items-center gap-2 rounded-md border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-sky-500 dark:border-white/10 dark:text-slate-200 dark:hover:bg-white/10">

            <flux:icon.arrow-left class="size-4" />

            Menu Handheld
        </a>

        <span
            class="w-fit rounded-md bg-sky-100 px-3 py-1.5 text-xs font-bold uppercase tracking-wide text-sky-700 dark:bg-sky-400/10 dark:text-sky-300">

            Copy Yard

        </span>

    </div>


    {{-- ============================================================
         SEARCH
    ============================================================= --}}

    <form
        data-search-form
        class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-slate-950">

        <label
            for="copyyard-search"
            class="text-sm font-semibold text-slate-700 dark:text-slate-200">

            No Container

        </label>

        <div class="mt-2 flex flex-col gap-3 sm:flex-row">

            <div class="relative flex-1">

                <flux:icon.magnifying-glass
                    class="pointer-events-none absolute left-3 top-1/2 size-5 -translate-y-1/2 text-slate-400" />

                <input
                    id="copyyard-search"
                    name="search_cont"
                    type="text"
                    autocomplete="off"
                    autofocus
                    required
                    placeholder="SEARCH NO CONTAINER"
                    class="h-12 w-full rounded-md border border-slate-200 bg-white pl-10 pr-3 text-sm font-medium text-slate-950 outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 dark:border-white/10 dark:bg-slate-900 dark:text-white dark:placeholder:text-slate-500">

            </div>

            <button
                type="submit"
                data-search-button
                class="inline-flex h-12 items-center justify-center gap-2 rounded-md bg-sky-700 px-6 text-sm font-bold text-white transition hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-500 disabled:cursor-not-allowed disabled:opacity-70">

                <flux:icon.magnifying-glass class="size-4" />

                SEARCH

            </button>

        </div>

    </form>


    {{-- ============================================================
         CONTENT
    ============================================================= --}}

    <div
        data-content
        class="mt-5">
    </div>

</div>