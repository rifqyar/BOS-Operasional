@if (($status ?? null) === 1)

    {{-- ========================================================= --}}
    {{-- FORM HOLD --}}
    {{-- ========================================================= --}}

    <div class="p-4">

        <div class="mb-4 rounded-md border border-amber-200 bg-amber-50 px-4 py-3 dark:border-amber-400/20 dark:bg-amber-400/10">

            <div class="flex items-start gap-3">

                <flux:icon.exclamation-triangle class="mt-0.5 size-5 shrink-0 text-amber-600 dark:text-amber-400" />

                <div>

                    <p class="text-sm font-semibold text-amber-800 dark:text-amber-300">
                        Container ditemukan
                    </p>

                    <p class="mt-1 text-xs text-amber-700 dark:text-amber-400">
                        Container ini belum dalam status HOLD.
                    </p>

                </div>

            </div>

        </div>


        <form
            data-hold-store-form
            class="space-y-4"
        >

            <input
                type="hidden"
                name="id"
                value="{{ $item->ID ?? '' }}"
            >


            {{-- NO SPK --}}
            <div>

                <label class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                    No SPK
                </label>

                <input
                    type="text"
                    name="nospk"
                    value="{{ $item->NO_SPK ?? '' }}"
                    readonly
                    class="mt-1 h-10 w-full rounded-md border border-slate-200 bg-slate-100 px-3 text-sm font-medium text-slate-700 dark:border-white/10 dark:bg-white/5 dark:text-slate-200"
                >

            </div>


            {{-- NO CONTAINER --}}
            <div>

                <label class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                    No Container
                </label>

                <input
                    type="text"
                    name="nomercont"
                    value="{{ $item->NO_CONT ?? '' }}"
                    readonly
                    class="mt-1 h-10 w-full rounded-md border border-slate-200 bg-slate-100 px-3 text-sm font-semibold text-slate-900 dark:border-white/10 dark:bg-white/5 dark:text-white"
                >

            </div>


            {{-- NO DOKUMEN --}}
            <div>

                <label class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                    No Dokumen
                </label>

                <input
                    type="text"
                    name="nodok"
                    value="{{ $item->NO_DOK ?? '' }}"
                    readonly
                    class="mt-1 h-10 w-full rounded-md border border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 dark:border-white/10 dark:bg-white/5 dark:text-slate-200"
                >

            </div>


            {{-- TANGGAL DOKUMEN --}}
            <div>

                <label class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                    Tanggal Dokumen
                </label>

                <input
                    type="text"
                    name="tgldok"
                    value="{{ $item->TGL_DOK ?? '' }}"
                    readonly
                    class="mt-1 h-10 w-full rounded-md border border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 dark:border-white/10 dark:bg-white/5 dark:text-slate-200"
                >

            </div>


            {{-- JENIS DOKUMEN --}}
            <div>

                <label class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                    Jenis Dokumen
                </label>

                <input
                    type="text"
                    name="jnsdok"
                    value="{{ $item->JNS_DOK ?? '' }}"
                    readonly
                    class="mt-1 h-10 w-full rounded-md border border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 dark:border-white/10 dark:bg-white/5 dark:text-slate-200"
                >

            </div>


            {{-- KETERANGAN --}}
            <div>

                <label class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                    Keterangan
                </label>

                <input
                    type="text"
                    name="keterangan"
                    value="{{ $item->KETERANGAN ?? '' }}"
                    readonly
                    class="mt-1 h-10 w-full rounded-md border border-slate-200 bg-slate-100 px-3 text-sm font-semibold text-amber-700 dark:border-white/10 dark:bg-white/5 dark:text-amber-300"
                >

            </div>


            {{-- WARNA HOLD --}}
            <div>

                <label
                    for="hold-warna"
                    class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400"
                >
                    Warna Hold
                </label>

                <select
                    id="hold-warna"
                    name="warna"
                    required
                    class="mt-1 h-10 w-full rounded-md border border-slate-200 bg-white px-3 text-sm font-medium text-slate-900 outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 dark:border-white/10 dark:bg-slate-950 dark:text-white"
                >

                    <option value="">
                        Pilih Warna
                    </option>

                    <option value="N">
                        PUTIH
                    </option>

                    <option value="M">
                        MERAH
                    </option>

                    <option value="T">
                        TIMAH
                    </option>

                </select>

            </div>


            {{-- BUTTON --}}
            <div class="pt-2">

                <button
                    type="submit"
                    data-hold-submit
                    class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-md bg-amber-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 disabled:cursor-not-allowed disabled:opacity-60"
                >

                    <flux:icon.lock-closed class="size-4" />

                    HOLD CONTAINER

                </button>

            </div>

        </form>

    </div>


@elseif (($status ?? null) === 3)

    {{-- ========================================================= --}}
    {{-- FORM RELEASE --}}
    {{-- ========================================================= --}}

    <div class="p-4">

        <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 dark:border-red-400/20 dark:bg-red-400/10">

            <div class="flex items-start gap-3">

                <flux:icon.lock-closed class="mt-0.5 size-5 shrink-0 text-red-600 dark:text-red-400" />

                <div>

                    <p class="text-sm font-semibold text-red-800 dark:text-red-300">
                        Container sedang HOLD
                    </p>

                    <p class="mt-1 text-xs text-red-700 dark:text-red-400">
                        Container ditemukan dalam daftar HOLD dan dapat diproses RELEASE.
                    </p>

                </div>

            </div>

        </div>


        <form
            data-release-form
            class="space-y-4"
        >

            <input
                type="hidden"
                name="id"
                value="{{ $item->ID ?? '' }}"
            >


            {{-- NO SPK --}}
            <div>

                <label class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                    No SPK
                </label>

                <input
                    type="text"
                    name="nospk"
                    value="{{ $item->NO_SPK ?? '' }}"
                    readonly
                    class="mt-1 h-10 w-full rounded-md border border-slate-200 bg-slate-100 px-3 text-sm font-medium text-slate-700 dark:border-white/10 dark:bg-white/5 dark:text-slate-200"
                >

            </div>


            {{-- NO CONTAINER --}}
            <div>

                <label class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                    No Container
                </label>

                <input
                    type="text"
                    name="nomercont"
                    value="{{ $item->NO_CONT ?? '' }}"
                    readonly
                    class="mt-1 h-10 w-full rounded-md border border-slate-200 bg-slate-100 px-3 text-sm font-semibold text-slate-900 dark:border-white/10 dark:bg-white/5 dark:text-white"
                >

            </div>


            {{-- NO DOKUMEN --}}
            <div>

                <label class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                    No Dokumen
                </label>

                <input
                    type="text"
                    name="nodok"
                    value="{{ $item->NO_DOK ?? '' }}"
                    readonly
                    class="mt-1 h-10 w-full rounded-md border border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 dark:border-white/10 dark:bg-white/5 dark:text-slate-200"
                >

            </div>


            {{-- TANGGAL DOKUMEN --}}
            <div>

                <label class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                    Tanggal Dokumen
                </label>

                <input
                    type="text"
                    name="tgldok"
                    value="{{ $item->TGL_DOK ?? '' }}"
                    readonly
                    class="mt-1 h-10 w-full rounded-md border border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 dark:border-white/10 dark:bg-white/5 dark:text-slate-200"
                >

            </div>


            {{-- JENIS DOKUMEN --}}
            <div>

                <label class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                    Jenis Dokumen
                </label>

                <input
                    type="text"
                    name="jnsdok"
                    value="{{ $item->JNS_DOK ?? '' }}"
                    readonly
                    class="mt-1 h-10 w-full rounded-md border border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 dark:border-white/10 dark:bg-white/5 dark:text-slate-200"
                >

            </div>


            {{-- KETERANGAN --}}
            <div>

                <label class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                    Keterangan
                </label>

                <input
                    type="text"
                    name="keterangan"
                    value="{{ $item->KETERANGAN ?? 'HOLD' }}"
                    readonly
                    class="mt-1 h-10 w-full rounded-md border border-slate-200 bg-slate-100 px-3 text-sm font-semibold text-red-700 dark:border-white/10 dark:bg-white/5 dark:text-red-300"
                >

            </div>


            {{-- WARNA --}}
            <div>

                <label class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                    Warna Hold
                </label>

                <input
                    type="text"
                    value="{{ $item->WARNA ?? '-' }}"
                    readonly
                    class="mt-1 h-10 w-full rounded-md border border-slate-200 bg-slate-100 px-3 text-sm font-semibold text-slate-700 dark:border-white/10 dark:bg-white/5 dark:text-slate-200"
                >

            </div>


            {{-- BUTTON --}}
            <div class="pt-2">

                <button
                    type="button"
                    data-release-form-button
                    class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 disabled:cursor-not-allowed disabled:opacity-60"
                >

                    <flux:icon.arrow-uturn-left class="size-4" />

                    RELEASE CONTAINER

                </button>

            </div>

        </form>

    </div>


@else

    <div class="p-4 text-center text-sm text-slate-500 dark:text-slate-400">
        Data container tidak tersedia.
    </div>

@endif