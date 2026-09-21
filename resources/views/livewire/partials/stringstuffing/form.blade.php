<div
    class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-slate-950">

    <div
        class="border-b border-slate-200 bg-slate-50 px-4 py-4 dark:border-white/10 dark:bg-white/5">

        <div class="flex items-center justify-between gap-3">

            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                    Operation
                </p>

                <h2 class="mt-1 text-base font-bold text-slate-950 dark:text-white">
                    STRIPPING & STUFFING
                </h2>
            </div>

            @if ($container->WK_START_STRIPSTUF ?? null)
                <span class="rounded-md bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 dark:bg-amber-400/10 dark:text-amber-300">
                    RUNNING
                </span>
            @else
                <span class="rounded-md bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300">
                    READY
                </span>
            @endif

        </div>

    </div>


    <form
        data-store-form
        class="space-y-5 p-4">

        <div>
            <label
                for="stringstuffing-no-cont"
                class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                No Cont Baru
            </label>

            <input
                id="stringstuffing-no-cont"
                type="text"
                name="nomerkon"
                value="{{ $container->NO_CONT ?? '' }}"
                readonly
                class="mt-2 h-11 w-full rounded-md border border-slate-200 bg-slate-100 px-3 text-sm font-bold text-slate-800 dark:border-white/10 dark:bg-white/5 dark:text-white">
        </div>


        <div>
            <label
                for="stringstuffing-no-dok"
                class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                No Dokumen Baru
            </label>

            <input
                id="stringstuffing-no-dok"
                type="text"
                name="no_dok"
                value="{{ $container->NO_DOK ?? '' }}"
                readonly
                class="mt-2 h-11 w-full rounded-md border border-slate-200 bg-slate-100 px-3 text-sm font-medium text-slate-700 dark:border-white/10 dark:bg-white/5 dark:text-slate-200">
        </div>


        <div>
            <label
                for="stringstuffing-no-cont-lama"
                class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                No Cont Lama
            </label>

            <input
                id="stringstuffing-no-cont-lama"
                type="text"
                name="no_cont_lama"
                value="{{ $container->NO_CONT_LAMA ?? '' }}"
                readonly
                class="mt-2 h-11 w-full rounded-md border border-slate-200 bg-slate-100 px-3 text-sm font-medium text-slate-700 dark:border-white/10 dark:bg-white/5 dark:text-slate-200">
        </div>


        <div>
            <label
                for="stringstuffing-no-dok-lama"
                class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                No Dokumen Lama
            </label>

            <input
                id="stringstuffing-no-dok-lama"
                type="text"
                name="no_dok_lama"
                value="{{ $container->NO_DOK_LAMA ?? '' }}"
                readonly
                class="mt-2 h-11 w-full rounded-md border border-slate-200 bg-slate-100 px-3 text-sm font-medium text-slate-700 dark:border-white/10 dark:bg-white/5 dark:text-slate-200">
        </div>


        @if ($container->WK_START_STRIPSTUF ?? null)

            <div>
                <label
                    for="stringstuffing-start"
                    class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                    Start Strip Stuff
                </label>

                <input
                    id="stringstuffing-start"
                    type="text"
                    name="op_start"
                    value="{{ $container->WK_START_STRIPSTUF }}"
                    readonly
                    class="mt-2 h-11 w-full rounded-md border border-slate-200 bg-slate-100 px-3 text-sm font-medium text-slate-700 dark:border-white/10 dark:bg-white/5 dark:text-slate-200">
            </div>

        @endif


        <div class="border-t border-slate-200 pt-4 dark:border-white/10">

            <button
                type="submit"
                data-store-button
                class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-md bg-sky-700 px-5 py-2 text-sm font-bold text-white transition hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-500 disabled:cursor-not-allowed disabled:opacity-70">

                @if ($container->WK_START_STRIPSTUF ?? null)
                    END STRIPPING & STUFFING
                @else
                    MULAI STRIPPING & STUFFING
                @endif

            </button>

        </div>

    </form>

</div>