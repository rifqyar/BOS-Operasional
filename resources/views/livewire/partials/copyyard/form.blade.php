<div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-slate-950">

    {{-- ============================================================
         HEADER
    ============================================================= --}}

    <div class="border-b border-slate-200 bg-slate-50 px-4 py-4 dark:border-white/10 dark:bg-white/5">

        <div class="flex items-center justify-between gap-3">

            <div>

                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                    Operation
                </p>

                <h2 class="mt-1 text-base font-bold text-slate-950 dark:text-white">
                    COPYYARD
                </h2>

            </div>

            <span
                class="rounded-md bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300">

                READY

            </span>

        </div>

    </div>


    {{-- ============================================================
         FORM
    ============================================================= --}}

    <form
        data-store-form
        class="space-y-5 p-4">

        {{-- ========================================================
             NO CONTAINER
        ========================================================= --}}

        <div>

            <label
                for="copyyard-no-cont"
                class="text-sm font-semibold text-slate-700 dark:text-slate-200">

                No Container

            </label>

            <input
                id="copyyard-no-cont"
                type="text"
                name="nomerkon"
                value="{{ $container->NO_CONT ?? '' }}"
                readonly
                class="mt-2 h-11 w-full rounded-md border border-slate-200 bg-slate-100 px-3 text-sm font-bold text-slate-800 dark:border-white/10 dark:bg-white/5 dark:text-white">

        </div>


        {{-- ========================================================
             UKURAN
        ========================================================= --}}

        <div>

            <label
                for="copyyard-ukuran"
                class="text-sm font-semibold text-slate-700 dark:text-slate-200">

                Ukuran Cont

            </label>

            <input
                id="copyyard-ukuran"
                type="text"
                name="ukurankon"
                value="{{ $container->UKR_CONT ?? '' }}"
                readonly
                class="mt-2 h-11 w-full rounded-md border border-slate-200 bg-slate-100 px-3 text-sm font-medium text-slate-700 dark:border-white/10 dark:bg-white/5 dark:text-slate-200">

        </div>


        {{-- ========================================================
             JENIS DOKUMEN
        ========================================================= --}}

        <div>

            <label
                for="copyyard-jns-dok"
                class="text-sm font-semibold text-slate-700 dark:text-slate-200">

                Jenis Dokumen

            </label>

            <input
                id="copyyard-jns-dok"
                type="text"
                name="jns_dok"
                value="{{ $container->NAMA ?? '' }}"
                readonly
                class="mt-2 h-11 w-full rounded-md border border-slate-200 bg-slate-100 px-3 text-sm font-medium text-slate-700 dark:border-white/10 dark:bg-white/5 dark:text-slate-200">

        </div>


        {{-- ========================================================
             STATUS CONTAINER
        ========================================================= --}}

        <div>

            <label
                for="copyyard-status"
                class="text-sm font-semibold text-slate-700 dark:text-slate-200">

                Status Cont

            </label>

            <input
                id="copyyard-status"
                type="text"
                name="status_cont"
                value="{{ $container->KETERANGAN ?? '' }}"
                readonly
                class="mt-2 h-11 w-full rounded-md border border-slate-200 bg-slate-100 px-3 text-sm font-medium text-slate-700 dark:border-white/10 dark:bg-white/5 dark:text-slate-200">

        </div>


        {{-- ========================================================
             LOKASI LAMA
        ========================================================= --}}

        <div>

            <label
                for="copyyard-old-location"
                class="text-sm font-semibold text-slate-700 dark:text-slate-200">

                Lokasi

            </label>

            @php
                $lokasi = strtoupper(trim($container->LOKASI ?? ''));
                $tier = trim($container->TIER ?? '');

                if ($lokasi === 'SAMPAH') {
                    $lokasiTampil = 'SAMPAH';
                } elseif ($lokasi !== '') {
                    $lokasiTampil = $lokasi . '0' . $tier;
                } else {
                    $lokasiTampil = '-';
                }
            @endphp

            <input
                id="copyyard-old-location"
                type="text"
                name="nolok"
                value="{{ $lokasiTampil }}"
                readonly
                class="mt-2 h-11 w-full rounded-md border border-slate-200 bg-slate-100 px-3 text-sm font-bold text-slate-700 dark:border-white/10 dark:bg-white/5 dark:text-slate-200">

        </div>


        {{-- ========================================================
             ACTION BLOK
             Legacy hanya ditampilkan untuk user group SPA
        ========================================================= --}}

        @if ($usernya === 'SPA')

            <div>

                <label
                    for="copyyard-action-block"
                    class="text-sm font-semibold text-slate-700 dark:text-slate-200">

                    Action Blok

                </label>

                <select
                    id="copyyard-action-block"
                    name="mySelect"
                    data-action-block
                    class="mt-2 h-11 w-full rounded-md border border-slate-200 bg-white px-3 text-sm font-medium text-slate-800 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 dark:border-white/10 dark:bg-slate-900 dark:text-white">

                    <option value="satu">
                        BLOK LAPANGAN
                    </option>

                    <option value="dua">
                        BLOK SAMPAH
                    </option>

                </select>

            </div>

        @else

            <input
                type="hidden"
                name="mySelect"
                value="satu">

        @endif


        {{-- ========================================================
             LOKASI BARU
        ========================================================= --}}

        <div>

            <label
                for="copyyard-new-location"
                class="text-sm font-semibold text-slate-700 dark:text-slate-200">

                Lokasi Baru

            </label>

            <input
                id="copyyard-new-location"
                type="text"
                name="lokbar"
                data-new-location
                required
                placeholder="Contoh: 1A010101"
                class="mt-2 h-11 w-full rounded-md border border-slate-200 bg-white px-3 text-sm font-medium uppercase text-slate-950 outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 dark:border-white/10 dark:bg-slate-900 dark:text-white dark:placeholder:text-slate-500">

        </div>


        {{-- ========================================================
             BUTTON
        ========================================================= --}}

        <div class="border-t border-slate-200 pt-4 dark:border-white/10">

            <button
                type="submit"
                data-store-button
                class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-md bg-sky-700 px-5 py-2 text-sm font-bold text-white transition hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-500 disabled:cursor-not-allowed disabled:opacity-70">

                COPY YARD

            </button>

        </div>

    </form>

</div>