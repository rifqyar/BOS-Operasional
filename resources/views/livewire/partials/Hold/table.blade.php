@if ($data->count() > 0)

    {{-- ========================================================= --}}
    {{-- DESKTOP TABLE --}}
    {{-- ========================================================= --}}

    <div class="hidden w-full overflow-x-auto md:block">

        <table class="min-w-[1200px] w-full table-auto text-left text-sm">

            <thead>
                <tr class="border-b border-slate-200 bg-slate-50 dark:border-white/10 dark:bg-white/5">

                    {{-- NO --}}
                    <th
                        class="whitespace-nowrap px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400"
                    >
                        No
                    </th>

                    {{-- NO CONTAINER --}}
                    <th
                        class="whitespace-nowrap px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400"
                    >
                        No Container
                    </th>

                    {{-- NO SPK --}}
                    <th
                        class="whitespace-nowrap px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400"
                    >
                        No SPK
                    </th>

                    {{-- NO DOKUMEN --}}
                    <th
                        class="whitespace-nowrap px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400"
                    >
                        No Dokumen
                    </th>

                    {{-- TGL DOKUMEN --}}
                    <th
                        class="whitespace-nowrap px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400"
                    >
                        Tgl Dokumen
                    </th>

                    {{-- JENIS DOKUMEN --}}
                    <th
                        class="whitespace-nowrap px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400"
                    >
                        Jenis Dokumen
                    </th>

                    {{-- KETERANGAN --}}
                    <th
                        class="whitespace-nowrap px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400"
                    >
                        Keterangan
                    </th>

                    {{-- WARNA --}}
                    <th
                        class="whitespace-nowrap px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400"
                    >
                        Warna
                    </th>

                    {{-- ACTION --}}
                    <th
                        class="whitespace-nowrap px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400"
                    >
                        Action
                    </th>

                </tr>
            </thead>


            <tbody class="divide-y divide-slate-200 dark:divide-white/10">

                @foreach ($data as $index => $row)

                    @php
                        $number =
                            (($data->currentPage() - 1) * $data->perPage())
                            + $index
                            + 1;

                        $warnaClass = match ($row->WARNA ?? null) {
                            'PUTIH'
                                => 'bg-slate-100 text-slate-700 dark:bg-slate-400/10 dark:text-slate-200',

                            'MERAH'
                                => 'bg-red-100 text-red-700 dark:bg-red-400/10 dark:text-red-300',

                            'TIMAH'
                                => 'bg-gray-200 text-gray-700 dark:bg-gray-400/10 dark:text-gray-300',

                            default
                                => 'bg-slate-100 text-slate-600 dark:bg-white/10 dark:text-slate-300',
                        };
                    @endphp


                    <tr class="transition hover:bg-slate-50 dark:hover:bg-white/5">

                        {{-- NO --}}
                        <td
                            class="whitespace-nowrap px-4 py-4 text-sm font-medium text-slate-500 dark:text-slate-400"
                        >
                            {{ $number }}
                        </td>


                        {{-- NO CONTAINER --}}
                        <td class="whitespace-nowrap px-4 py-4">

                            <span class="font-semibold text-slate-900 dark:text-white">
                                {{ $row->NO_CONT ?? '-' }}
                            </span>

                        </td>


                        {{-- NO SPK --}}
                        <td
                            class="whitespace-nowrap px-4 py-4 text-slate-700 dark:text-slate-300"
                        >
                            {{ $row->NO_SPK ?? '-' }}
                        </td>


                        {{-- NO DOKUMEN --}}
                        <td
                            class="whitespace-nowrap px-4 py-4 text-slate-700 dark:text-slate-300"
                        >
                            {{ $row->NO_DOK ?? '-' }}
                        </td>


                        {{-- TGL DOKUMEN --}}
                        <td
                            class="whitespace-nowrap px-4 py-4 text-slate-700 dark:text-slate-300"
                        >
                            {{ $row->TGL_DOK ?? '-' }}
                        </td>


                        {{-- JENIS DOKUMEN --}}
                        <td
                            class="whitespace-nowrap px-4 py-4 text-slate-700 dark:text-slate-300"
                        >
                            {{ $row->JNS_DOK ?? '-' }}
                        </td>


                        {{-- KETERANGAN --}}
                        <td class="whitespace-nowrap px-4 py-4">

                            <span
                                class="inline-flex rounded-sm bg-amber-100 px-2 py-1 text-xs font-semibold uppercase tracking-wide text-amber-700 dark:bg-amber-400/10 dark:text-amber-300"
                            >
                                {{ $row->KETERANGAN ?? 'HOLD' }}
                            </span>

                        </td>


                        {{-- WARNA --}}
                        <td class="whitespace-nowrap px-4 py-4">

                            <span
                                class="inline-flex rounded-sm px-2 py-1 text-xs font-semibold uppercase tracking-wide {{ $warnaClass }}"
                            >
                                {{ $row->WARNA ?? '-' }}
                            </span>

                        </td>


                        {{-- ACTION --}}
                        <td class="whitespace-nowrap px-4 py-4 text-right">

                            <button
                                type="button"
                                data-release-row
                                data-id="{{ $row->ID }}"
                                data-no-spk="{{ $row->NO_SPK }}"
                                data-no-cont="{{ $row->NO_CONT }}"
                                class="inline-flex min-h-9 items-center gap-2 rounded-md border border-amber-300 bg-white px-3 py-2 text-xs font-semibold text-amber-700 transition hover:bg-amber-50 focus:outline-none focus:ring-2 focus:ring-amber-500 dark:border-amber-400/30 dark:bg-slate-950 dark:text-amber-300 dark:hover:bg-amber-400/10"
                            >

                                <flux:icon.arrow-uturn-left class="size-3.5" />

                                Release

                            </button>

                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

    </div>


    {{-- ========================================================= --}}
    {{-- MOBILE TABLE --}}
    {{-- ========================================================= --}}

    <div class="block w-full md:hidden">

        <div class="overflow-hidden">

            <table class="w-full table-fixed text-left text-sm">

                <thead>

                    <tr
                        class="border-b border-slate-200 bg-slate-50 dark:border-white/10 dark:bg-white/5"
                    >

                        {{-- INDICATOR --}}
                        <th
                            class="w-9 px-1 py-3 text-center text-[10px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400"
                        >
                            !
                        </th>


                        {{-- NO --}}
                        <th
                            class="w-10 px-1 py-3 text-center text-[10px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400"
                        >
                            No
                        </th>


                        {{-- CONTAINER --}}
                        <th
                            class="px-2 py-3 text-[10px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400"
                        >
                            No Container
                        </th>


                        {{-- SPK --}}
                        <th
                            class="px-2 py-3 text-[10px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400"
                        >
                            No SPK
                        </th>


                        {{-- ACTION --}}
                        <th
                            class="w-[82px] px-1 py-3 text-center text-[10px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400"
                        >
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-200 dark:divide-white/10">

                    @foreach ($data as $index => $row)

                        @php
                            $number =
                                (($data->currentPage() - 1) * $data->perPage())
                                + $index
                                + 1;

                            $mobileWarnaClass = match ($row->WARNA ?? null) {
                                'PUTIH'
                                    => 'bg-slate-100 text-slate-700 dark:bg-slate-400/10 dark:text-slate-200',

                                'MERAH'
                                    => 'bg-red-100 text-red-700 dark:bg-red-400/10 dark:text-red-300',

                                'TIMAH'
                                    => 'bg-gray-200 text-gray-700 dark:bg-gray-400/10 dark:text-gray-300',

                                default
                                    => 'bg-slate-100 text-slate-600 dark:bg-white/10 dark:text-slate-300',
                            };
                        @endphp


                        {{-- ================================================= --}}
                        {{-- MAIN MOBILE ROW --}}
                        {{-- ================================================= --}}

                        <tr
                            class="bg-white transition dark:bg-slate-950"
                            data-mobile-row
                        >

                            {{-- ! --}}
                            <td class="px-1 py-4 text-center">

                                <button
                                    type="button"
                                    data-mobile-detail="{{ $number }}"
                                    aria-expanded="false"
                                    aria-label="Lihat detail container {{ $row->NO_CONT }}"
                                    class="inline-flex size-7 items-center justify-center rounded-full text-sm font-black text-amber-500 transition hover:bg-amber-50 hover:text-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-500 dark:hover:bg-amber-400/10"
                                >
                                    !
                                </button>

                            </td>


                            {{-- NO --}}
                            <td class="px-1 py-4 text-center">

                                <span
                                    class="text-sm font-semibold text-slate-500 dark:text-slate-400"
                                >
                                    {{ $number }}
                                </span>

                            </td>


                            {{-- NO CONTAINER --}}
                            <td class="px-2 py-4">

                                <div
                                    class="truncate font-semibold text-slate-900 dark:text-white"
                                >
                                    {{ $row->NO_CONT ?? '-' }}
                                </div>

                            </td>


                            {{-- NO SPK --}}
                            <td class="px-2 py-4">

                                <div
                                    class="truncate text-sm text-slate-700 dark:text-slate-300"
                                >
                                    {{ $row->NO_SPK ?? '-' }}
                                </div>

                            </td>


                            {{-- ACTION --}}
                            <td class="px-1 py-4 text-center">

                                <button
                                    type="button"
                                    data-release-row
                                    data-id="{{ $row->ID }}"
                                    data-no-spk="{{ $row->NO_SPK }}"
                                    data-no-cont="{{ $row->NO_CONT }}"
                                    class="inline-flex min-h-8 items-center justify-center gap-1 rounded-md border border-amber-300 bg-white px-2 py-1.5 text-[10px] font-semibold text-amber-700 transition hover:bg-amber-50 focus:outline-none focus:ring-2 focus:ring-amber-500 dark:border-amber-400/30 dark:bg-slate-950 dark:text-amber-300"
                                >

                                    <flux:icon.arrow-uturn-left class="size-3" />

                                    Release

                                </button>

                            </td>

                        </tr>


                        {{-- ================================================= --}}
                        {{-- MOBILE DETAIL --}}
                        {{-- ================================================= --}}

                        <tr
                            data-mobile-detail-row="{{ $number }}"
                            class="hidden bg-slate-50 dark:bg-white/[0.03]"
                        >

                            <td
                                colspan="5"
                                class="px-4 py-4"
                            >

                                <div
                                    class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-slate-900"
                                >

                                    {{-- HEADER DETAIL --}}
                                    <div
                                        class="mb-3 flex items-center justify-between gap-3"
                                    >

                                        <div>

                                            <p
                                                class="text-[10px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400"
                                            >
                                                Detail Container
                                            </p>

                                            <p
                                                class="mt-1 text-sm font-bold text-slate-900 dark:text-white"
                                            >
                                                {{ $row->NO_CONT ?? '-' }}
                                            </p>

                                        </div>


                                        <span
                                            class="inline-flex rounded-sm bg-amber-100 px-2 py-1 text-[10px] font-semibold uppercase tracking-wide text-amber-700 dark:bg-amber-400/10 dark:text-amber-300"
                                        >
                                            HOLD
                                        </span>

                                    </div>


                                    {{-- DETAIL --}}
                                    <div class="space-y-3">

                                        {{-- NO DOKUMEN --}}
                                        <div
                                            class="flex items-start justify-between gap-4"
                                        >

                                            <span
                                                class="shrink-0 text-xs text-slate-500 dark:text-slate-400"
                                            >
                                                No Dokumen
                                            </span>

                                            <span
                                                class="text-right text-xs font-semibold text-slate-800 dark:text-slate-200"
                                            >
                                                {{ $row->NO_DOK ?? '-' }}
                                            </span>

                                        </div>


                                        {{-- TGL DOKUMEN --}}
                                        <div
                                            class="flex items-start justify-between gap-4"
                                        >

                                            <span
                                                class="shrink-0 text-xs text-slate-500 dark:text-slate-400"
                                            >
                                                Tgl Dokumen
                                            </span>

                                            <span
                                                class="text-right text-xs font-semibold text-slate-800 dark:text-slate-200"
                                            >
                                                {{ $row->TGL_DOK ?? '-' }}
                                            </span>

                                        </div>


                                        {{-- JENIS DOKUMEN --}}
                                        <div
                                            class="flex items-start justify-between gap-4"
                                        >

                                            <span
                                                class="shrink-0 text-xs text-slate-500 dark:text-slate-400"
                                            >
                                                Jenis Dokumen
                                            </span>

                                            <span
                                                class="text-right text-xs font-semibold text-slate-800 dark:text-slate-200"
                                            >
                                                {{ $row->JNS_DOK ?? '-' }}
                                            </span>

                                        </div>


                                        {{-- KETERANGAN --}}
                                        <div
                                            class="flex items-start justify-between gap-4"
                                        >

                                            <span
                                                class="shrink-0 text-xs text-slate-500 dark:text-slate-400"
                                            >
                                                Keterangan
                                            </span>

                                            <span
                                                class="rounded-sm bg-amber-100 px-2 py-1 text-[10px] font-semibold uppercase tracking-wide text-amber-700 dark:bg-amber-400/10 dark:text-amber-300"
                                            >
                                                {{ $row->KETERANGAN ?? 'HOLD' }}
                                            </span>

                                        </div>


                                        {{-- WARNA --}}
                                        <div
                                            class="flex items-start justify-between gap-4"
                                        >

                                            <span
                                                class="shrink-0 text-xs text-slate-500 dark:text-slate-400"
                                            >
                                                Warna
                                            </span>

                                            <span
                                                class="rounded-sm px-2 py-1 text-[10px] font-semibold uppercase tracking-wide {{ $mobileWarnaClass }}"
                                            >
                                                {{ $row->WARNA ?? '-' }}
                                            </span>

                                        </div>

                                    </div>

                                </div>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- PAGINATION --}}
    {{-- SAMA SEPERTI MARSHALLING CIC --}}
    {{-- ========================================================= --}}

    @if ($data->lastPage() > 1)

        <div
            class="border-t border-slate-200 px-4 py-4 dark:border-slate-800"
        >

            {{-- ===================================================== --}}
            {{-- PAGINATION INFO --}}
            {{-- ===================================================== --}}

            <div
                class="mb-3 text-center text-xs text-slate-500 dark:text-slate-400"
            >

                Menampilkan

                <span
                    class="font-semibold text-slate-700 dark:text-slate-200"
                >
                    {{ $data->firstItem() }}
                </span>

                sampai

                <span
                    class="font-semibold text-slate-700 dark:text-slate-200"
                >
                    {{ $data->lastItem() }}
                </span>

                dari

                <span
                    class="font-semibold text-slate-700 dark:text-slate-200"
                >
                    {{ $data->total() }}
                </span>

                data

            </div>


            {{-- ===================================================== --}}
            {{-- PAGINATION BUTTONS --}}
            {{-- ===================================================== --}}

            <div
                class="flex flex-wrap items-center justify-center gap-1.5"
            >

                {{-- PREVIOUS --}}
                @if ($data->currentPage() > 1)

                    <button
                        type="button"
                        data-hold-page="{{ $data->currentPage() - 1 }}"
                        class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50 hover:text-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800"
                    >
                        ‹
                    </button>

                @else

                    <button
                        type="button"
                        disabled
                        class="inline-flex h-9 min-w-9 cursor-not-allowed items-center justify-center rounded-lg border border-slate-200 bg-slate-100 px-2 text-xs font-semibold text-slate-400 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-600"
                    >
                        ‹
                    </button>

                @endif


                @php
                    $current = $data->currentPage();
                    $last = $data->lastPage();

                    $paginationPages = [];

                    if ($last <= 7) {

                        $paginationPages = range(1, $last);

                    } else {

                        $paginationPages[] = 1;

                        if ($current > 4) {
                            $paginationPages[] = '...';
                        }

                        $startPage = max(2, $current - 1);
                        $endPage = min($last - 1, $current + 1);

                        for ($page = $startPage; $page <= $endPage; $page++) {
                            $paginationPages[] = $page;
                        }

                        if ($current < $last - 3) {
                            $paginationPages[] = '...';
                        }

                        $paginationPages[] = $last;
                    }
                @endphp


                {{-- PAGE NUMBERS --}}
                @foreach ($paginationPages as $page)

                    @if ($page === '...')

                        <span
                            class="inline-flex h-9 min-w-9 items-center justify-center px-1 text-xs font-semibold text-slate-400"
                        >
                            ...
                        </span>

                    @else

                        <button
                            type="button"
                            data-hold-page="{{ $page }}"
                            @if ($page == $current)
                                disabled
                            @endif
                            class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg px-2.5 text-xs font-bold transition focus:outline-none focus:ring-2 focus:ring-amber-500 {{ $page == $current
                                ? 'bg-amber-500 text-white'
                                : 'border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 hover:text-amber-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800' }}"
                        >
                            {{ $page }}
                        </button>

                    @endif

                @endforeach


                {{-- NEXT --}}
                @if ($data->currentPage() < $data->lastPage())

                    <button
                        type="button"
                        data-hold-page="{{ $data->currentPage() + 1 }}"
                        class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50 hover:text-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800"
                    >
                        ›
                    </button>

                @else

                    <button
                        type="button"
                        disabled
                        class="inline-flex h-9 min-w-9 cursor-not-allowed items-center justify-center rounded-lg border border-slate-200 bg-slate-100 px-2 text-xs font-semibold text-slate-400 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-600"
                    >
                        ›
                    </button>

                @endif

            </div>

        </div>

    @endif

@else

    {{-- ========================================================= --}}
    {{-- EMPTY --}}
    {{-- ========================================================= --}}

    <div class="px-4 py-8 text-center">

        <div
            class="mx-auto flex size-12 items-center justify-center rounded-full bg-slate-100 dark:bg-white/10"
        >

            <flux:icon.archive-box class="size-6 text-slate-400" />

        </div>


        <p
            class="mt-3 text-sm font-semibold text-slate-700 dark:text-slate-200"
        >
            Tidak ada container HOLD
        </p>


        <p
            class="mt-1 text-xs text-slate-500 dark:text-slate-400"
        >
            Belum terdapat container yang sedang HOLD.
        </p>

    </div>

@endif