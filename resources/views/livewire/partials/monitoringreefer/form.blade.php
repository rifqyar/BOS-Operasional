<div
    class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-slate-950">

    {{-- Header --}}
    <div
        class="border-b border-slate-200 bg-slate-50 px-4 py-4 dark:border-white/10 dark:bg-white/5">

        <div class="flex items-center justify-between gap-3">

            <div>
                <p
                    class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                    Operation
                </p>

                <h2
                    class="mt-1 text-base font-bold text-slate-950 dark:text-white">
                    MONITORING REEFER
                </h2>
            </div>

            <span
                class="rounded-md bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300">
                ACTIVE
            </span>

        </div>

    </div>


    {{-- Form --}}
    <form
        data-store-form
        class="space-y-5 p-4">


        {{-- No Container --}}
        <div>

            <label
                for="monitoringreefer-no-cont"
                class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                No Container
            </label>

            <input
                id="monitoringreefer-no-cont"
                type="text"
                name="nomerkon"
                value="{{ $nilai->NO_CONT ?? '' }}"
                readonly
                class="mt-2 h-11 w-full rounded-md border border-slate-200 bg-slate-100 px-3 text-sm font-bold text-slate-800 dark:border-white/10 dark:bg-white/5 dark:text-white">

        </div>


        {{-- Temperature Sebelumnya --}}
        <div>

            <label
                for="monitoringreefer-temp-before"
                class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                Temperature Sebelumnya
            </label>

            <input
                id="monitoringreefer-temp-before"
                type="text"
                value="{{ $temprev->TEMPERATURE_MONITOR ?? '-' }}"
                readonly
                class="mt-2 h-11 w-full rounded-md border border-slate-200 bg-slate-100 px-3 text-sm font-medium text-slate-700 dark:border-white/10 dark:bg-white/5 dark:text-slate-200">

        </div>


        {{-- Temperature Saat Ini --}}
        <div>

            <label
                for="monitoringreefer-temperature"
                class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                Temperature Saat Ini
            </label>

            <input
                id="monitoringreefer-temperature"
                type="text"
                name="temperature"
                required
                autocomplete="off"
                placeholder="Masukkan temperature"
                class="mt-2 h-11 w-full rounded-md border border-slate-200 bg-white px-3 text-sm font-medium text-slate-950 outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 dark:border-white/10 dark:bg-slate-900 dark:text-white dark:placeholder:text-slate-500">

        </div>


        {{-- Note --}}
        <div>

            <label
                for="monitoringreefer-note"
                class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                Note
            </label>

            <textarea
                id="monitoringreefer-note"
                name="note"
                rows="4"
                placeholder="Masukkan catatan jika diperlukan"
                class="mt-2 w-full resize-none rounded-md border border-slate-200 bg-white px-3 py-3 text-sm font-medium text-slate-950 outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 dark:border-white/10 dark:bg-slate-900 dark:text-white dark:placeholder:text-slate-500"></textarea>

        </div>


        {{-- Hidden Waktu Plugin --}}
        <input
            type="hidden"
            name="w_plugin"
            value="{{ $nilai->WAKTU ?? '' }}">


        {{-- Action --}}
        <div
            class="border-t border-slate-200 pt-4 dark:border-white/10">

            <button
                type="submit"
                data-store-button
                class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-md bg-sky-700 px-5 py-2 text-sm font-bold text-white transition hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-500 disabled:cursor-not-allowed disabled:opacity-70">

                MONITORING

            </button>

        </div>

    </form>

</div>