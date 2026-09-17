@if (($result['status'] ?? 0) === 2)

    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-slate-950">

        <div class="border-b border-slate-200 bg-slate-50 px-4 py-3 dark:border-white/10 dark:bg-white/5">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                Inspection Out
            </p>

            <p class="mt-1 text-base font-semibold text-slate-950 dark:text-white">
                PEMERIKSAAN CONTAINER
            </p>
        </div>

        <form
            data-store-form
            class="space-y-5 p-4">

            {{-- NO CONT --}}
            <div>
                <label
                    for="inspection-no-cont"
                    class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                    No Container
                </label>

                <input
                    id="inspection-no-cont"
                    type="text"
                    name="nomercont"
                    value="{{ $result['no_cont'] ?? '-' }}"
                    readonly
                    class="mt-2 h-11 w-full rounded-md border border-slate-200 bg-slate-100 px-3 text-sm font-medium text-slate-700 dark:border-white/10 dark:bg-white/5 dark:text-slate-200">
            </div>

            {{-- UKURAN --}}
            <div>
                <label
                    for="inspection-ukuran"
                    class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                    Ukuran
                </label>

                <input
                    id="inspection-ukuran"
                    type="text"
                    name="ukuran"
                    value="{{ $result['ukuran'] ?? '-' }}"
                    readonly
                    class="mt-2 h-11 w-full rounded-md border border-slate-200 bg-slate-100 px-3 text-sm font-medium text-slate-700 dark:border-white/10 dark:bg-white/5 dark:text-slate-200">
            </div>

            {{-- KONDISI SEAL --}}
            <div>
                <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                    Kondisi Seal
                </label>

                <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:gap-6">

                    <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700 dark:text-slate-200">
                        <input
                            type="radio"
                            name="optradio"
                            value="ada"
                            checked
                            class="size-4 border-slate-300 text-sky-700 focus:ring-sky-500">
                        ADA
                    </label>

                    <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700 dark:text-slate-200">
                        <input
                            type="radio"
                            name="optradio"
                            value="tidak ada"
                            class="size-4 border-slate-300 text-sky-700 focus:ring-sky-500">
                        TIDAK ADA
                    </label>

                </div>
            </div>

            {{-- NO SEAL --}}
            <div>
                <label
                    for="inspection-no-seal"
                    class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                    No Seal
                </label>

                <input
                    id="inspection-no-seal"
                    type="text"
                    name="noseal"
                    value="{{ $result['no_seal'] ?? '' }}"
                    autocomplete="off"
                    placeholder="MASUKKAN NO SEAL"
                    class="mt-2 h-11 w-full rounded-md border border-slate-200 bg-white px-3 text-sm font-medium text-slate-950 outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 dark:border-white/10 dark:bg-slate-950 dark:text-white">
            </div>

            {{-- KONDISI CONTAINER --}}
            <div>
                <label
                    for="inspection-condition"
                    class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                    Kondisi Container
                </label>

                <select
                    id="inspection-condition"
                    name="kondisi"
                    class="mt-2 h-11 w-full rounded-md border border-slate-200 bg-white px-3 text-sm font-medium text-slate-950 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 dark:border-white/10 dark:bg-slate-950 dark:text-white">

                    <option value="">
                        Pilih Kondisi Container
                    </option>

                    @foreach (($result['condition'] ?? []) as $condition)
                        <option value="{{ $condition->ID }}">
                            {{ $condition->KONDISI }}
                        </option>
                    @endforeach

                </select>
            </div>

            {{-- BUTTON --}}
            <div class="flex justify-end">
                <button
                    id="inspection-store-button"
                    type="submit"
                    data-store-button
                    class="inline-flex min-h-11 items-center justify-center gap-2 rounded-md bg-sky-700 px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-500 disabled:cursor-not-allowed disabled:opacity-70">

                    INSPECTION OUT
                </button>
            </div>

        </form>
    </div>

@else

    <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm font-medium text-amber-800 dark:border-amber-400/20 dark:bg-amber-400/10 dark:text-amber-200">
        {{ $result['message'] ?? 'NO CONT NOT FOUND' }}
    </div>

@endif