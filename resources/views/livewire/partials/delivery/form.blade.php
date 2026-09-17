@if (($result['status'] ?? 0) === 1)

    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-slate-950">

        <div class="border-b border-slate-200 bg-slate-50 px-4 py-3 dark:border-white/10 dark:bg-white/5">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                Delivery
            </p>

            <p class="mt-1 text-base font-semibold text-slate-950 dark:text-white">
                TRUCK IN
            </p>
        </div>

        <form data-store-form class="space-y-5 p-4">

            <input
                type="hidden"
                name="nomercont"
                value="{{ $result['no_cont'] ?? '' }}">

            <div class="grid gap-4 sm:grid-cols-2">

                <div>
                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                        No Container
                    </label>

                    <input
                        type="text"
                        value="{{ $result['no_cont'] ?? '-' }}"
                        readonly
                        class="mt-2 h-11 w-full rounded-md border border-slate-200 bg-slate-100 px-3 text-sm font-medium text-slate-700 dark:border-white/10 dark:bg-white/5 dark:text-slate-200">
                </div>

                <div>
                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                        Ukuran
                    </label>

                    <input
                        type="text"
                        value="{{ $result['ukuran'] ?? '-' }}"
                        readonly
                        class="mt-2 h-11 w-full rounded-md border border-slate-200 bg-slate-100 px-3 text-sm font-medium text-slate-700 dark:border-white/10 dark:bg-white/5 dark:text-slate-200">
                </div>

            </div>

            <div>
                <label
                    for="delivery-no-truck"
                    class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                    No Truck
                </label>

                <input
                    id="delivery-no-truck"
                    type="text"
                    name="nomertruck"
                    autocomplete="off"
                    placeholder="MASUKKAN NO TRUCK"
                    class="mt-2 h-11 w-full rounded-md border border-slate-200 bg-white px-3 text-sm font-medium text-slate-950 outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 dark:border-white/10 dark:bg-slate-950 dark:text-white">
            </div>

            <div>
                <label
                    for="delivery-gate"
                    class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                    Gate
                </label>

                <select
                    id="delivery-gate"
                    name="gate"
                    class="mt-2 h-11 w-full rounded-md border border-slate-200 bg-white px-3 text-sm font-medium text-slate-950 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 dark:border-white/10 dark:bg-slate-950 dark:text-white">

                    <option value="">Pilih Gate</option>

                    @for ($i = 1; $i <= 6; $i++)
                        <option value="GATE {{ $i }}">
                            GATE {{ $i }}
                        </option>
                    @endfor

                </select>
            </div>

            <div class="flex justify-end">
                <button
                    type="submit"
                    data-store-button
                    class="inline-flex min-h-11 items-center justify-center gap-2 rounded-md bg-sky-700 px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-500 disabled:cursor-not-allowed disabled:opacity-70">

                    TRUCK IN
                </button>
            </div>

        </form>
    </div>


@elseif (($result['status'] ?? 0) === 2)

    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-slate-950">

        <div class="border-b border-slate-200 bg-slate-50 px-4 py-3 dark:border-white/10 dark:bg-white/5">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                Delivery
            </p>

            <p class="mt-1 text-base font-semibold text-slate-950 dark:text-white">
                GATE OUT
            </p>
        </div>

        <form data-store-form class="space-y-5 p-4">

            <input
                type="hidden"
                name="nomercont"
                value="{{ $result['no_cont'] ?? '' }}">

            <div>
                <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                    No Container
                </label>

                <input
                    type="text"
                    value="{{ $result['no_cont'] ?? '-' }}"
                    readonly
                    class="mt-2 h-11 w-full rounded-md border border-slate-200 bg-slate-100 px-3 text-sm font-medium text-slate-700 dark:border-white/10 dark:bg-white/5 dark:text-slate-200">
            </div>

            <div>
                <label
                    for="delivery-gate-out"
                    class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                    Gate
                </label>

                <select
                    id="delivery-gate-out"
                    name="gate"
                    class="mt-2 h-11 w-full rounded-md border border-slate-200 bg-white px-3 text-sm font-medium text-slate-950 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 dark:border-white/10 dark:bg-slate-950 dark:text-white">

                    <option value="">Pilih Gate</option>

                    @for ($i = 1; $i <= 4; $i++)
                        <option value="GATE {{ $i }}">
                            GATE {{ $i }}
                        </option>
                    @endfor

                    <option value="TIDAK BOLEH GATE OUT">
                        TIDAK BOLEH GATE OUT
                    </option>

                </select>
            </div>

            <div class="flex justify-end">
                <button
                    type="submit"
                    data-store-button
                    class="inline-flex min-h-11 items-center justify-center gap-2 rounded-md bg-sky-700 px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-500 disabled:cursor-not-allowed disabled:opacity-70">

                    GATE OUT
                </button>
            </div>

        </form>
    </div>


@else

    <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm font-medium text-amber-800 dark:border-amber-400/20 dark:bg-amber-400/10 dark:text-amber-200">
        {{ $result['message'] ?? 'NO CONT NOT FOUND' }}
    </div>

@endif