<div class="mx-auto mb-5 max-w-3xl"
    wire:ignore
    data-panel
    data-panel-name="behandlein"
    data-search-url="{{ Route::has('behandlein.search') ? route('behandlein.search') : '' }}"
    data-store-url="{{ Route::has('behandlein.store') ? route('behandlein.store') : '' }}"
    data-csrf-token="{{ csrf_token() }}">
    <div class="mb-4 flex items-center justify-between gap-3">
        <a href="{{ route('dashboard') }}" wire:navigate data-back-menu
            class="inline-flex min-h-10 items-center gap-2 rounded-md border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-sky-500 dark:border-white/10 dark:text-slate-200 dark:hover:bg-white/10">
            <flux:icon.arrow-left class="size-4" />
            Menu Handheld
        </a>
        <span class="rounded-sm bg-sky-100 px-2 py-1 text-xs font-semibold uppercase tracking-wide text-sky-700 dark:bg-sky-400/10 dark:text-sky-200">Behandle In</span>
    </div>

    <form data-search-form
        class="rounded-lg border border-slate-200 bg-slate-50 p-4 dark:border-white/10 dark:bg-white/5">
        <label for="panel-no-container" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Nomor Container</label>
        <div class="mt-2 grid gap-3 sm:grid-cols-[1fr_auto]">
            <div class="relative">
                <flux:icon.magnifying-glass class="pointer-events-none absolute left-3 top-1/2 size-5 -translate-y-1/2 text-slate-400" />
                <input id="panel-no-container" name="no_container" type="text" autocomplete="off"
                    placeholder="SEARCH NO. CONTAINER"
                    class="h-12 w-full rounded-md border border-slate-200 bg-white pl-10 pr-3 text-sm font-medium text-slate-950 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 dark:border-white/10 dark:bg-slate-950 dark:text-white">
            </div>
            <button type="submit" data-search-button
                class="inline-flex h-12 items-center justify-center gap-2 rounded-md bg-sky-700 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 focus:ring-offset-slate-50 disabled:cursor-not-allowed disabled:opacity-70 dark:focus:ring-offset-slate-900">
                <flux:icon.magnifying-glass class="size-4" />
                Search
            </button>
        </div>
    </form>

    <div class="mt-4 hidden overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-slate-950" data-result>
        <div class="border-b border-slate-200 bg-slate-50 px-4 py-3 dark:border-white/10 dark:bg-white/5">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">NO CONTAINER</p>
            <p class="mt-1 text-base font-semibold text-slate-950 dark:text-white" data-result-container>-</p>
        </div>

        <div data-rows></div>
    </div>
</div>
