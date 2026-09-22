@if ($status === 1)

    <div class="p-4">

        <div class="mb-4">

            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                Hasil Pencarian
            </p>

            <h3 class="mt-1 text-lg font-bold text-slate-950 dark:text-white">
                Container Siap HOLD
            </h3>

        </div>


        <div class="grid gap-3 sm:grid-cols-2">

            <div class="rounded-lg bg-slate-50 p-3 dark:bg-white/5">

                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">
                    No. Container
                </p>

                <p class="mt-1 font-semibold text-slate-950 dark:text-white">
                    {{ $item->NO_CONT ?? '-' }}
                </p>

            </div>


            <div class="rounded-lg bg-slate-50 p-3 dark:bg-white/5">

                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">
                    No. SPK
                </p>

                <p class="mt-1 font-semibold text-slate-950 dark:text-white">
                    {{ $item->NO_SPK ?? '-' }}
                </p>

            </div>


            <div class="rounded-lg bg-slate-50 p-3 dark:bg-white/5">

                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">
                    No. Dokumen
                </p>

                <p class="mt-1 font-semibold text-slate-950 dark:text-white">
                    {{ $item->NO_DOK ?? '-' }}
                </p>

            </div>


            <div class="rounded-lg bg-slate-50 p-3 dark:bg-white/5">

                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">
                    Tanggal Dokumen
                </p>

                <p class="mt-1 font-semibold text-slate-950 dark:text-white">
                    {{ $item->TGL_DOK ?? '-' }}
                </p>

            </div>


            <div class="rounded-lg bg-slate-50 p-3 dark:bg-white/5 sm:col-span-2">

                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">
                    Jenis Dokumen
                </p>

                <p class="mt-1 font-semibold text-slate-950 dark:text-white">
                    {{ $item->JNS_DOK ?? '-' }}
                </p>

            </div>

        </div>


        <form
            action="{{ route('hold.store') }}"
            method="POST"
            data-hold-store-form
            class="mt-5"
        >

            @csrf


            <input
                type="hidden"
                name="id"
                value="{{ $item->ID ?? '' }}"
            >


            <input
                type="hidden"
                name="nomercont"
                value="{{ $item->NO_CONT ?? '' }}"
            >


            <div>

                <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                    Warna HOLD
                </label>


                <div class="mt-3 grid gap-3 sm:grid-cols-3">

                    <label class="cursor-pointer">

                        <input
                            type="radio"
                            name="warna"
                            value="N"
                            class="peer sr-only"
                        >

                        <div class="rounded-lg border border-slate-200 p-4 text-center transition peer-checked:border-sky-600 peer-checked:bg-sky-50 dark:border-white/10 dark:peer-checked:bg-sky-400/10">

                            <p class="font-semibold text-slate-900 dark:text-white">
                                PUTIH
                            </p>

                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                N
                            </p>

                        </div>

                    </label>


                    <label class="cursor-pointer">

                        <input
                            type="radio"
                            name="warna"
                            value="M"
                            class="peer sr-only"
                        >

                        <div class="rounded-lg border border-slate-200 p-4 text-center transition peer-checked:border-red-600 peer-checked:bg-red-50 dark:border-white/10 dark:peer-checked:bg-red-400/10">

                            <p class="font-semibold text-slate-900 dark:text-white">
                                MERAH
                            </p>

                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                M
                            </p>

                        </div>

                    </label>


                    <label class="cursor-pointer">

                        <input
                            type="radio"
                            name="warna"
                            value="T"
                            class="peer sr-only"
                        >

                        <div class="rounded-lg border border-slate-200 p-4 text-center transition peer-checked:border-slate-600 peer-checked:bg-slate-100 dark:border-white/10 dark:peer-checked:bg-white/10">

                            <p class="font-semibold text-slate-900 dark:text-white">
                                TIMAH
                            </p>

                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                T
                            </p>

                        </div>

                    </label>

                </div>

            </div>


            <button
                type="submit"
                data-hold-submit
                class="mt-5 inline-flex min-h-11 w-full items-center justify-center rounded-md bg-amber-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-amber-700 disabled:cursor-not-allowed disabled:opacity-70"
            >
                HOLD
            </button>

        </form>

    </div>


@elseif ($status === 3)

    <div class="p-4">

        <div class="mb-4">

            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                Hasil Pencarian
            </p>

            <h3 class="mt-1 text-lg font-bold text-slate-950 dark:text-white">
                Container Sedang HOLD
            </h3>

        </div>


        <div class="grid gap-3 sm:grid-cols-2">

            <div class="rounded-lg bg-slate-50 p-3 dark:bg-white/5">

                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">
                    No. Container
                </p>

                <p class="mt-1 font-semibold text-slate-950 dark:text-white">
                    {{ $item->NO_CONT ?? '-' }}
                </p>

            </div>


            <div class="rounded-lg bg-slate-50 p-3 dark:bg-white/5">

                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">
                    No. SPK
                </p>

                <p class="mt-1 font-semibold text-slate-950 dark:text-white">
                    {{ $item->NO_SPK ?? '-' }}
                </p>

            </div>


            <div class="rounded-lg bg-slate-50 p-3 dark:bg-white/5">

                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">
                    No. Dokumen
                </p>

                <p class="mt-1 font-semibold text-slate-950 dark:text-white">
                    {{ $item->NO_DOK ?? '-' }}
                </p>

            </div>


            <div class="rounded-lg bg-slate-50 p-3 dark:bg-white/5">

                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">
                    Tanggal Dokumen
                </p>

                <p class="mt-1 font-semibold text-slate-950 dark:text-white">
                    {{ $item->TGL_DOK ?? '-' }}
                </p>

            </div>


            <div class="rounded-lg bg-slate-50 p-3 dark:bg-white/5">

                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">
                    Jenis Dokumen
                </p>

                <p class="mt-1 font-semibold text-slate-950 dark:text-white">
                    {{ $item->JNS_DOK ?? '-' }}
                </p>

            </div>


            <div class="rounded-lg bg-amber-50 p-3 dark:bg-amber-400/10">

                <p class="text-xs font-medium text-amber-600 dark:text-amber-300">
                    Status
                </p>

                <p class="mt-1 font-semibold text-amber-700 dark:text-amber-200">
                    {{ $item->KETERANGAN ?? 'HOLD' }}
                </p>

            </div>


            <div class="rounded-lg bg-slate-50 p-3 dark:bg-white/5 sm:col-span-2">

                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">
                    Warna HOLD
                </p>

                <p class="mt-1 font-semibold text-slate-950 dark:text-white">
                    {{ $item->WARNA ?? '-' }}
                </p>

            </div>

        </div>


        <button
            type="button"
            data-release-row
            data-id="{{ $item->ID ?? '' }}"
            data-no-spk="{{ $item->NO_SPK ?? '' }}"
            data-no-cont="{{ $item->NO_CONT ?? '' }}"
            class="mt-5 inline-flex min-h-11 w-full items-center justify-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-70"
        >
            RELEASE
        </button>

    </div>

@endif