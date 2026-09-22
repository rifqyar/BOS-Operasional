<div class="mb-5 w-full"
    wire:ignore
    data-panel
    data-panel-name="hold"
    data-search-url="{{ Route::has('hold.search') ? route('hold.search') : '' }}"
    data-detail-url="{{ Route::has('hold.detail') ? route('hold.detail') : '' }}"
    data-store-url="{{ Route::has('hold.store') ? route('hold.store') : '' }}"
    data-release-url="{{ Route::has('hold.release') ? route('hold.release') : '' }}"
    data-data-url="{{ Route::has('hold.data') ? route('hold.data') : '' }}"
    data-csrf-token="{{ csrf_token() }}">

    {{-- ============================================================
         HEADER + SEARCH + SEARCH RESULT
         Acuan visual tetap Behandle In
    ============================================================= --}}

    <div class="mx-auto max-w-3xl">

        {{-- HEADER --}}

        <div class="mb-4 flex items-center justify-between gap-3">

            <a href="{{ route('dashboard') }}"
                wire:navigate
                data-back-menu
                class="inline-flex min-h-10 items-center gap-2 rounded-md border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-sky-500 dark:border-white/10 dark:text-slate-200 dark:hover:bg-white/10">

                <flux:icon.arrow-left class="size-4" />

                Menu Handheld

            </a>

            <span
                class="rounded-sm bg-sky-100 px-2 py-1 text-xs font-semibold uppercase tracking-wide text-sky-700 dark:bg-sky-400/10 dark:text-sky-200">

                Hold

            </span>

        </div>


        {{-- SEARCH --}}

        <form
            data-search-form
            class="rounded-lg border border-slate-200 bg-slate-50 p-4 dark:border-white/10 dark:bg-white/5">

            <label
                for="hold-no-container"
                class="text-sm font-semibold text-slate-700 dark:text-slate-200">

                Nomor Container

            </label>

            <div class="mt-2 grid gap-3 sm:grid-cols-[1fr_auto]">

                <div class="relative">

                    <flux:icon.magnifying-glass
                        class="pointer-events-none absolute left-3 top-1/2 size-5 -translate-y-1/2 text-slate-400" />

                    <input
                        id="hold-no-container"
                        name="search_cont"
                        type="text"
                        autocomplete="off"
                        placeholder="SEARCH NO. CONTAINER"
                        class="h-12 w-full rounded-md border border-slate-200 bg-white pl-10 pr-3 text-sm font-medium text-slate-950 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 dark:border-white/10 dark:bg-slate-950 dark:text-white">

                </div>

                <button
                    type="submit"
                    data-search-button
                    class="inline-flex h-12 items-center justify-center gap-2 rounded-md bg-sky-700 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 focus:ring-offset-slate-50 disabled:cursor-not-allowed disabled:opacity-70 dark:focus:ring-offset-slate-900">

                    <flux:icon.magnifying-glass class="size-4" />

                    Search

                </button>

            </div>

        </form>


        {{-- MESSAGE --}}

        <div
            data-message
            class="mt-4 hidden">
        </div>


        {{-- SEARCH RESULT --}}

        <div
            data-result
            class="mt-4 hidden overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-slate-950">

            <div
                class="border-b border-slate-200 bg-slate-50 px-4 py-3 dark:border-white/10 dark:bg-white/5">

                <div class="flex items-center justify-between gap-3">

                    <div class="min-w-0">

                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Container
                        </p>

                        <p
                            data-result-container
                            class="mt-1 truncate text-base font-semibold text-slate-950 dark:text-white">
                            -
                        </p>

                    </div>

                    <span
                        data-result-status
                        class="hidden shrink-0 rounded-sm px-2 py-1 text-xs font-semibold uppercase tracking-wide">
                    </span>

                </div>

            </div>


            <div data-rows></div>

        </div>

    </div>


    {{-- ============================================================
         CURRENT HOLD
         Dibuat lebih lebar di desktop
    ============================================================= --}}

    <div class="mx-auto mt-5 w-full max-w-7xl px-0 sm:px-2 lg:px-4">

        <div
            data-hold-list
            class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-slate-950">

            {{-- HEADER TABLE --}}

            <div
                class="border-b border-slate-200 bg-slate-50 px-4 py-3 dark:border-white/10 dark:bg-white/5">

                <div class="flex items-center justify-between gap-3">

                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Data Hold
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-950 dark:text-white">
                            Container Sedang HOLD
                        </p>

                    </div>

                    <span
                        class="shrink-0 rounded-sm bg-amber-100 px-2 py-1 text-xs font-semibold uppercase tracking-wide text-amber-700 dark:bg-amber-400/10 dark:text-amber-300">

                        HOLD

                    </span>

                </div>

            </div>


            {{-- TABLE --}}

            <div data-hold-rows>

                <div class="px-4 py-6 text-center">

                    <div class="inline-flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">

                        <svg
                            class="size-4 animate-spin"
                            viewBox="0 0 24 24"
                            fill="none">

                            <circle
                                class="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                stroke-width="4">
                            </circle>

                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                            </path>

                        </svg>

                        Memuat data HOLD...

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>