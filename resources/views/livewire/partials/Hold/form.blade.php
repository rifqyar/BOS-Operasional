@if (($status ?? null) === 2)

    {{-- ============================================================
         STATUS 2
         HASIL PENCARIAN CONTAINER
    ============================================================= --}}

    <div class="divide-y divide-slate-200 dark:divide-white/10">

        <div class="px-4 py-3">

            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                Hasil Pencarian
            </p>

            <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">
                Pilih container yang akan diproses HOLD.
            </p>

        </div>


        <div class="divide-y divide-slate-200 dark:divide-white/10">

            @foreach ($data as $row)

                <button
                    type="button"
                    data-hold-detail
                    data-no-cont="{{ $row->NO_CONT }}"
                    class="group flex w-full items-center justify-between gap-3 px-4 py-3 text-left transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-sky-500 dark:hover:bg-white/[0.03]">

                    <div class="min-w-0">

                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400 dark:text-slate-500">
                            No Container
                        </p>

                        <p class="mt-1 truncate text-sm font-semibold text-slate-950 dark:text-white">
                            {{ $row->NO_CONT }}
                        </p>

                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                            SPK: {{ $row->NO_SPK }}
                        </p>

                    </div>


                    <div class="flex shrink-0 items-center gap-2">

                        <span class="hidden text-xs font-semibold text-sky-600 sm:inline dark:text-sky-400">
                            Pilih
                        </span>

                        <span
                            class="inline-flex size-8 items-center justify-center rounded-md bg-sky-100 text-sky-700 transition group-hover:bg-sky-600 group-hover:text-white dark:bg-sky-400/10 dark:text-sky-300">

                            <flux:icon.arrow-right class="size-4" />

                        </span>

                    </div>

                </button>

            @endforeach

        </div>

    </div>


@elseif (($status ?? null) === 1)

    {{-- ============================================================
         STATUS 1
         FORM HOLD
    ============================================================= --}}

    @php
        $item = $item ?? null;
    @endphp

    @if ($item)

        <div class="divide-y divide-slate-200 dark:divide-white/10">

            {{-- HEADER --}}

            <div class="px-4 py-3">

                <div class="flex items-start justify-between gap-3">

                    <div class="min-w-0">

                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Detail Container
                        </p>

                        <p class="mt-1 truncate text-base font-semibold text-slate-950 dark:text-white">
                            {{ $item->NO_CONT }}
                        </p>

                    </div>

                    <span
                        class="shrink-0 rounded-sm bg-sky-100 px-2 py-1 text-xs font-semibold uppercase tracking-wide text-sky-700 dark:bg-sky-400/10 dark:text-sky-200">

                        Ready Hold

                    </span>

                </div>

            </div>


            {{-- FORM --}}

            <form
                data-hold-store-form
                action="{{ route('hold.store') }}"
                method="POST"
                class="p-4">

                @csrf

                <input
                    type="hidden"
                    name="id"
                    value="{{ $item->ID }}">

                <input
                    type="hidden"
                    name="nospk"
                    value="{{ $item->NO_SPK }}">

                <input
                    type="hidden"
                    name="nomercont"
                    value="{{ $item->NO_CONT }}">

                <input
                    type="hidden"
                    name="nodok"
                    value="{{ $item->NO_DOK }}">

                <input
                    type="hidden"
                    name="tgldok"
                    value="{{ $item->TGL_DOK }}">

                <input
                    type="hidden"
                    name="jnsdok"
                    value="{{ $item->JNS_DOK }}">


                {{-- DATA CONTAINER --}}

                <div class="grid gap-3 sm:grid-cols-2">

                    <div>

                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            No SPK
                        </label>

                        <input
                            type="text"
                            value="{{ $item->NO_SPK }}"
                            readonly
                            class="mt-1.5 h-11 w-full rounded-md border border-slate-200 bg-slate-50 px-3 text-sm font-medium text-slate-700 outline-none dark:border-white/10 dark:bg-white/5 dark:text-slate-300">

                    </div>


                    <div>

                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            No Container
                        </label>

                        <input
                            type="text"
                            value="{{ $item->NO_CONT }}"
                            readonly
                            class="mt-1.5 h-11 w-full rounded-md border border-slate-200 bg-slate-50 px-3 text-sm font-semibold text-slate-900 outline-none dark:border-white/10 dark:bg-white/5 dark:text-white">

                    </div>


                    <div>

                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            No Dokumen
                        </label>

                        <input
                            type="text"
                            value="{{ $item->NO_DOK }}"
                            readonly
                            class="mt-1.5 h-11 w-full rounded-md border border-slate-200 bg-slate-50 px-3 text-sm font-medium text-slate-700 outline-none dark:border-white/10 dark:bg-white/5 dark:text-slate-300">

                    </div>


                    <div>

                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Tanggal Dokumen
                        </label>

                        <input
                            type="text"
                            value="{{ $item->TGL_DOK }}"
                            readonly
                            class="mt-1.5 h-11 w-full rounded-md border border-slate-200 bg-slate-50 px-3 text-sm font-medium text-slate-700 outline-none dark:border-white/10 dark:bg-white/5 dark:text-slate-300">

                    </div>


                    <div>

                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Jenis Dokumen
                        </label>

                        <input
                            type="text"
                            value="{{ $item->JNS_DOK ?? '-' }}"
                            readonly
                            class="mt-1.5 h-11 w-full rounded-md border border-slate-200 bg-slate-50 px-3 text-sm font-medium text-slate-700 outline-none dark:border-white/10 dark:bg-white/5 dark:text-slate-300">

                    </div>


                    <div>

                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Keterangan
                        </label>

                        <input
                            type="text"
                            value="{{ $item->KETERANGAN ?? '-' }}"
                            readonly
                            class="mt-1.5 h-11 w-full rounded-md border border-slate-200 bg-slate-50 px-3 text-sm font-medium text-slate-700 outline-none dark:border-white/10 dark:bg-white/5 dark:text-slate-300">

                    </div>

                </div>


                {{-- WARNA HOLD --}}

                <div class="mt-5">

                    <label class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                        Warna Hold
                    </label>

                    <div class="mt-2 grid grid-cols-3 gap-2">

                        {{-- PUTIH --}}

                        <label class="cursor-pointer">

                            <input
                                type="radio"
                                name="warna"
                                value="N"
                                class="peer sr-only">

                            <div
                                class="flex min-h-11 items-center justify-center rounded-md border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-700 transition hover:border-sky-300 peer-checked:border-sky-500 peer-checked:bg-sky-50 peer-checked:text-sky-700 dark:border-white/10 dark:bg-slate-950 dark:text-slate-300 dark:hover:border-sky-500 dark:peer-checked:border-sky-500 dark:peer-checked:bg-sky-400/10 dark:peer-checked:text-sky-300">

                                Putih

                            </div>

                        </label>


                        {{-- MERAH --}}

                        <label class="cursor-pointer">

                            <input
                                type="radio"
                                name="warna"
                                value="M"
                                class="peer sr-only">

                            <div
                                class="flex min-h-11 items-center justify-center rounded-md border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-700 transition hover:border-sky-300 peer-checked:border-sky-500 peer-checked:bg-sky-50 peer-checked:text-sky-700 dark:border-white/10 dark:bg-slate-950 dark:text-slate-300 dark:hover:border-sky-500 dark:peer-checked:border-sky-500 dark:peer-checked:bg-sky-400/10 dark:peer-checked:text-sky-300">

                                Merah

                            </div>

                        </label>


                        {{-- TIMAH --}}

                        <label class="cursor-pointer">

                            <input
                                type="radio"
                                name="warna"
                                value="T"
                                class="peer sr-only">

                            <div
                                class="flex min-h-11 items-center justify-center rounded-md border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-700 transition hover:border-sky-300 peer-checked:border-sky-500 peer-checked:bg-sky-50 peer-checked:text-sky-700 dark:border-white/10 dark:bg-slate-950 dark:text-slate-300 dark:hover:border-sky-500 dark:peer-checked:border-sky-500 dark:peer-checked:bg-sky-400/10 dark:peer-checked:text-sky-300">

                                Timah

                            </div>

                        </label>

                    </div>

                </div>


                {{-- ACTION --}}

                <div class="mt-5 flex justify-end">

                    <button
                        type="submit"
                        data-hold-submit
                        class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-md bg-sky-700 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 focus:ring-offset-white disabled:cursor-not-allowed disabled:opacity-70 sm:w-auto dark:focus:ring-offset-slate-950">

                        <flux:icon.lock-closed class="size-4" />

                        HOLD

                    </button>

                </div>

            </form>

        </div>

    @endif


@elseif (($status ?? null) === 3)

    {{-- ============================================================
         STATUS 3
         SUDAH HOLD
    ============================================================= --}}

    <div class="divide-y divide-slate-200 dark:divide-white/10">

        <div class="bg-amber-50 px-4 py-3 dark:bg-amber-400/5">

            <div class="flex items-center justify-between gap-3">

                <div>

                    <p class="text-xs font-semibold uppercase tracking-wide text-amber-600 dark:text-amber-300">
                        Container Sedang HOLD
                    </p>

                    <p class="mt-1 text-sm text-amber-700 dark:text-amber-200">
                        Container yang dicari sudah dalam status HOLD.
                    </p>

                </div>

                <span
                    class="shrink-0 rounded-sm bg-amber-100 px-2 py-1 text-xs font-semibold uppercase tracking-wide text-amber-700 dark:bg-amber-400/10 dark:text-amber-300">

                    HOLD

                </span>

            </div>

        </div>


        <div class="divide-y divide-slate-200 dark:divide-white/10">

            @foreach ($data as $row)

                <form
                    data-release-form
                    action="{{ route('hold.release') }}"
                    method="POST"
                    class="p-4">

                    @csrf

                    <input
                        type="hidden"
                        name="id"
                        value="{{ $row->ID }}">

                    <input
                        type="hidden"
                        name="nospk"
                        value="{{ $row->NO_SPK }}">

                    <input
                        type="hidden"
                        name="nomercont"
                        value="{{ $row->NO_CONT }}">

                    <input
                        type="hidden"
                        name="nodok"
                        value="{{ $row->NO_DOK }}">


                    <div class="mb-4 flex items-start justify-between gap-3">

                        <div class="min-w-0">

                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                No Container
                            </p>

                            <p class="mt-1 truncate text-base font-semibold text-slate-950 dark:text-white">
                                {{ $row->NO_CONT }}
                            </p>

                        </div>

                        <span
                            class="shrink-0 rounded-sm bg-amber-100 px-2 py-1 text-xs font-semibold uppercase tracking-wide text-amber-700 dark:bg-amber-400/10 dark:text-amber-300">

                            {{ $row->WARNA ?? 'HOLD' }}

                        </span>

                    </div>


                    <div class="grid gap-3 sm:grid-cols-2">

                        <div>

                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                No SPK
                            </label>

                            <input
                                type="text"
                                value="{{ $row->NO_SPK }}"
                                readonly
                                class="mt-1.5 h-11 w-full rounded-md border border-slate-200 bg-slate-50 px-3 text-sm font-medium text-slate-700 outline-none dark:border-white/10 dark:bg-white/5 dark:text-slate-300">

                        </div>


                        <div>

                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                No Dokumen
                            </label>

                            <input
                                type="text"
                                value="{{ $row->NO_DOK }}"
                                readonly
                                class="mt-1.5 h-11 w-full rounded-md border border-slate-200 bg-slate-50 px-3 text-sm font-medium text-slate-700 outline-none dark:border-white/10 dark:bg-white/5 dark:text-slate-300">

                        </div>


                        <div>

                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                Tanggal Dokumen
                            </label>

                            <input
                                type="text"
                                value="{{ $row->TGL_DOK }}"
                                readonly
                                class="mt-1.5 h-11 w-full rounded-md border border-slate-200 bg-slate-50 px-3 text-sm font-medium text-slate-700 outline-none dark:border-white/10 dark:bg-white/5 dark:text-slate-300">

                        </div>


                        <div>

                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                Jenis Dokumen
                            </label>

                            <input
                                type="text"
                                value="{{ $row->JNS_DOK ?? '-' }}"
                                readonly
                                class="mt-1.5 h-11 w-full rounded-md border border-slate-200 bg-slate-50 px-3 text-sm font-medium text-slate-700 outline-none dark:border-white/10 dark:bg-white/5 dark:text-slate-300">

                        </div>


                        <div>

                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                Keterangan
                            </label>

                            <input
                                type="text"
                                value="{{ $row->KETERANGAN ?? '-' }}"
                                readonly
                                class="mt-1.5 h-11 w-full rounded-md border border-slate-200 bg-slate-50 px-3 text-sm font-medium text-slate-700 outline-none dark:border-white/10 dark:bg-white/5 dark:text-slate-300">

                        </div>


                        <div>

                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                Warna Hold
                            </label>

                            <input
                                type="text"
                                value="{{ $row->WARNA ?? '-' }}"
                                readonly
                                class="mt-1.5 h-11 w-full rounded-md border border-slate-200 bg-slate-50 px-3 text-sm font-semibold text-slate-700 outline-none dark:border-white/10 dark:bg-white/5 dark:text-slate-300">

                        </div>

                    </div>


                    {{-- RELEASE --}}

                    <div class="mt-5 flex justify-end">

                        <button
                            type="submit"
                            data-release-submit
                            class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-md bg-amber-600 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 focus:ring-offset-white disabled:cursor-not-allowed disabled:opacity-70 sm:w-auto dark:focus:ring-offset-slate-950">

                            <flux:icon.arrow-uturn-left class="size-4" />

                            RELEASE

                        </button>

                    </div>

                </form>

            @endforeach

        </div>

    </div>

@endif