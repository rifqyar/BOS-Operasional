<div
    id="hold-panel"
    class="w-full"
    data-search-url="{{ route('hold.search') }}"
    data-data-url="{{ route('hold.data') }}"
    data-store-url="{{ route('hold.store') }}"
    data-release-url="{{ route('hold.release') }}"
>
    <div class="mx-auto w-full max-w-7xl space-y-4">

        <div class="rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">

            <div class="flex items-center gap-3">

                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">

                    <flux:icon
                        name="pause-circle"
                        class="size-6"
                    />

                </div>

                <div class="min-w-0">

                    <h1 class="text-base font-semibold text-zinc-900 dark:text-white sm:text-lg">
                        HOLD CONTAINER
                    </h1>

                    <p class="text-xs text-zinc-500 dark:text-zinc-400 sm:text-sm">
                        Search container untuk proses HOLD dan RELEASE.
                    </p>

                </div>

            </div>

        </div>

        <div class="rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">

            <form
                id="hold-search-form"
                class="flex flex-col gap-3 sm:flex-row"
            >

                <div class="relative flex-1">

                    <flux:icon
                        name="magnifying-glass"
                        class="absolute left-3 top-1/2 size-5 -translate-y-1/2 text-zinc-400"
                    />

                    <input
                        id="hold-search-input"
                        name="search_cont"
                        type="text"
                        autocomplete="off"
                        placeholder="Masukkan nomor container..."
                        class="w-full rounded-xl border border-zinc-300 bg-white py-3 pl-10 pr-4 text-sm uppercase outline-none transition focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white"
                    >

                </div>

                <button
                    id="hold-search-button"
                    type="submit"
                    class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-zinc-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-zinc-800 disabled:cursor-not-allowed disabled:opacity-60 dark:bg-white dark:text-zinc-900"
                >

                    <flux:icon
                        name="magnifying-glass"
                        class="size-4"
                    />

                    <span>Cari</span>

                </button>

            </form>

            <div
                id="hold-message"
                class="mt-3 hidden rounded-xl px-4 py-3 text-sm"
            ></div>

        </div>

        <div
            id="hold-form-container"
            class="w-full"
        >

            <div class="flex items-center justify-center rounded-2xl border border-dashed border-zinc-300 bg-white p-10 dark:border-zinc-700 dark:bg-zinc-900">

                <div class="text-center">

                    <flux:icon
                        name="arrow-path"
                        class="mx-auto size-6 animate-spin text-zinc-400"
                    />

                    <p class="mt-3 text-sm text-zinc-500">
                        Memuat data container HOLD...
                    </p>

                </div>

            </div>

        </div>

    </div>
</div>