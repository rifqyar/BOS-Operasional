<div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-slate-950">

    {{-- HEADER --}}
    <div class="border-b border-slate-200 bg-slate-50 px-4 py-4 dark:border-white/10 dark:bg-white/5">

        <div class="flex items-center justify-between gap-3">

            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                    Operation
                </p>

                <h2 class="mt-1 text-base font-bold text-slate-950 dark:text-white">
                    ON CHASSIS
                </h2>
            </div>

            <span class="rounded-md bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300">
                READY
            </span>

        </div>

    </div>

    {{-- FORM --}}
    <form
        data-store-form
        class="space-y-5 p-4">

        {{-- NO SPK --}}
        <div>
            <label
                for="onchassis-no-spk"
                class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                No SPK
            </label>

            <input
                id="onchassis-no-spk"
                type="text"
                name="nomerspk"
                value="{{ $container->NO_SPK ?? '' }}"
                readonly
                class="mt-2 h-11 w-full rounded-md border border-slate-200 bg-slate-100 px-3 text-sm font-medium text-slate-700 dark:border-white/10 dark:bg-white/5 dark:text-slate-200">
        </div>

        {{-- NO CONTAINER --}}
        <div>
            <label
                for="onchassis-no-cont"
                class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                No Container
            </label>

            <input
                id="onchassis-no-cont"
                type="text"
                name="nomercont"
                value="{{ $container->NO_CONT ?? '' }}"
                readonly
                class="mt-2 h-11 w-full rounded-md border border-slate-200 bg-slate-100 px-3 text-sm font-bold text-slate-800 dark:border-white/10 dark:bg-white/5 dark:text-white">
        </div>

        {{-- UKURAN --}}
        <div>
            <label
                for="onchassis-ukuran"
                class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                Ukuran
            </label>

            <input
                id="onchassis-ukuran"
                type="text"
                name="ukuran"
                value="{{ $container->UKR_CONT ?? '' }}"
                readonly
                class="mt-2 h-11 w-full rounded-md border border-slate-200 bg-slate-100 px-3 text-sm font-medium text-slate-700 dark:border-white/10 dark:bg-white/5 dark:text-slate-200">
        </div>

        {{-- NO TRUCK --}}
        <div>
            <label
                for="onchassis-no-truck"
                class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                No Truck
            </label>

            <input
                id="onchassis-no-truck"
                type="text"
                name="notruck"
                value="{{ $container->NO_TRUCK ?? '' }}"
                readonly
                class="mt-2 h-11 w-full rounded-md border border-slate-200 bg-slate-100 px-3 text-sm font-medium text-slate-700 dark:border-white/10 dark:bg-white/5 dark:text-slate-200">
        </div>

        {{-- LOKASI --}}
        <div>
            <label
                for="onchassis-lokasi"
                class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                Lokasi
            </label>

            @php
                $lokasi = $container->LOKASI ?? '';
                $tier = $container->TIER ?? '';

                if (strtoupper(substr($lokasi, 0, 3)) === 'CIC') {
                    $lokasiTampil = $lokasi;
                } else {
                    $lokasiTampil = $lokasi . '0' . $tier;
                }
            @endphp

            <input
                id="onchassis-lokasi"
                type="text"
                name="lokasi"
                value="{{ $lokasiTampil }}"
                readonly
                class="mt-2 h-11 w-full rounded-md border border-slate-200 bg-slate-100 px-3 text-sm font-bold text-slate-700 dark:border-white/10 dark:bg-white/5 dark:text-slate-200">
        </div>

        {{-- ACTION --}}
        <div class="border-t border-slate-200 pt-4 dark:border-white/10">

            <button
                type="submit"
                data-store-button
                class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-md bg-sky-700 px-5 py-2 text-sm font-bold text-white transition hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-500 disabled:cursor-not-allowed disabled:opacity-70">

                ON CHASSIS
            </button>

        </div>

    </form>

</div>