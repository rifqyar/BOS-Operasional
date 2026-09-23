@php
    $rows = $rows ?? [];

    $isPaginated =
        $rows instanceof \Illuminate\Contracts\Pagination\Paginator;

    $currentPage =
        $isPaginated
            ? $rows->currentPage()
            : 1;

    $perPage =
        $isPaginated
            ? $rows->perPage()
            : count($rows);

    $total =
        $isPaginated
            ? $rows->total()
            : count($rows);

    $from =
        $isPaginated
            ? $rows->firstItem()
            : (count($rows) > 0 ? 1 : 0);

    $to =
        $isPaginated
            ? $rows->lastItem()
            : count($rows);

    $lastPage =
        $isPaginated
            ? $rows->lastPage()
            : 1;
@endphp


@if ($total === 0)

    {{-- ========================================================= --}}
    {{-- EMPTY --}}
    {{-- ========================================================= --}}

    <div class="px-4 py-10 text-center">

        <div
            class="mx-auto mb-3 flex h-12 w-12 items-center justify-center
                   rounded-full bg-slate-100 text-slate-400
                   dark:bg-slate-800"
        >

            <flux:icon.magnifying-glass class="h-5 w-5" />

        </div>


        <p
            class="text-sm font-semibold text-slate-700 dark:text-slate-200"
        >
            Data tidak ditemukan
        </p>


        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
            Tidak ada pekerjaan Marshalling Yard yang sesuai.
        </p>

    </div>

@else


    {{-- ========================================================= --}}
    {{-- DESKTOP --}}
    {{-- ========================================================= --}}

    <div class="hidden overflow-x-auto md:block">

        <table class="w-full min-w-[1000px] text-left text-sm">

            <thead
                class="bg-slate-50 text-xs uppercase tracking-wider
                       text-slate-500 dark:bg-slate-800/70"
            >

                <tr>

                    <th class="px-4 py-3 text-center">
                        No
                    </th>

                    <th class="px-4 py-3">
                        ID Job
                    </th>

                    <th class="px-4 py-3">
                        No Container
                    </th>

                    <th class="px-4 py-3">
                        Ukuran
                    </th>

                    <th class="px-4 py-3">
                        Lokasi Awal
                    </th>

                    <th class="px-4 py-3">
                        Lokasi Akhir
                    </th>

                    <th class="px-4 py-3">
                        Job
                    </th>

                    <th class="px-4 py-3 text-center">
                        Proses
                    </th>

                </tr>

            </thead>


            <tbody
                class="divide-y divide-slate-200
                       dark:divide-slate-800"
            >

                @foreach ($rows as $index => $row)

                    @php

                        $number =
                            $isPaginated
                                ? (
                                    (($currentPage - 1) * $perPage)
                                    + $index
                                    + 1
                                )
                                : ($index + 1);


                        $lokasiAwal = trim(
                            (string) ($row->LOKASI_AWAL ?? '')
                        );


                        $tierAwal = trim(
                            (string) ($row->TIER_AWAL ?? '')
                        );


                        $lokasiAkhir = trim(
                            (string) ($row->LOKASI_AKHIR ?? '')
                        );


                        $tierAkhir = trim(
                            (string) ($row->TIER_AKHIR ?? '')
                        );


                        $displayLokasiAwal =
                            $lokasiAwal !== ''
                                ? $lokasiAwal .
                                    (
                                        $tierAwal !== ''
                                            ? '0' . $tierAwal
                                            : ''
                                    )
                                : '-';


                        $displayLokasiAkhir =
                            $lokasiAkhir !== ''
                                ? $lokasiAkhir .
                                    (
                                        $tierAkhir !== ''
                                            ? '0' . $tierAkhir
                                            : ''
                                    )
                                : '-';

                    @endphp


                    <tr
                        class="transition hover:bg-slate-50
                               dark:hover:bg-slate-800/50"
                    >

                        {{-- NO --}}
                        <td
                            class="px-4 py-3 text-center
                                   text-slate-500"
                        >
                            {{ $number }}
                        </td>


                        {{-- ID JOB --}}
                        <td
                            class="px-4 py-3 font-semibold
                                   text-slate-900
                                   dark:text-white"
                        >
                            {{ $row->ID_JOB_SLIP ?? '-' }}
                        </td>


                        {{-- NO CONTAINER --}}
                        <td
                            class="px-4 py-3 font-bold
                                   text-slate-900
                                   dark:text-white"
                        >
                            {{ $row->NO_CONT ?? '-' }}
                        </td>


                        {{-- UKURAN --}}
                        <td class="px-4 py-3">
                            {{ $row->UKR_CONT ?? '-' }}
                        </td>


                        {{-- LOKASI AWAL --}}
                        <td class="px-4 py-3">
                            {{ $displayLokasiAwal }}
                        </td>


                        {{-- LOKASI AKHIR --}}
                        <td class="px-4 py-3">
                            {{ $displayLokasiAkhir }}
                        </td>


                        {{-- JOB --}}
                        <td class="px-4 py-3">

                            <span
                                class="rounded-full bg-slate-100
                                       px-2.5 py-1 text-xs
                                       font-semibold
                                       text-slate-700
                                       dark:bg-slate-800
                                       dark:text-slate-200"
                            >
                                {{ $row->JENIS ?? '-' }}
                            </span>

                        </td>


                        {{-- PROSES --}}
                        <td class="px-4 py-3 text-center">

                            <button
                                type="button"
                                data-detail-button
                                data-id-job-slip="{{ $row->ID_JOB_SLIP }}"
                                data-detail-id="{{ $row->ID_JOB_SLIP }}"
                                data-container="{{ $row->NO_CONT }}"
                                class="inline-flex min-h-11
                                       items-center justify-center
                                       rounded-xl bg-sky-600
                                       px-4 py-2.5
                                       text-xs font-bold
                                       text-white transition
                                       hover:bg-sky-700
                                       active:scale-[0.98]"
                            >
                                PROSES
                            </button>

                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

    </div>


    {{-- ========================================================= --}}
    {{-- MOBILE --}}
    {{-- ========================================================= --}}

    <div class="block md:hidden">

        <div class="overflow-hidden">

            <table class="w-full text-left text-sm">

                {{-- MOBILE HEADER --}}
                <thead
                    class="bg-slate-50 text-[10px]
                           uppercase tracking-wider
                           text-slate-500
                           dark:bg-slate-800/70"
                >

                    <tr>

                        <th
                            class="w-10 px-2 py-3 text-center"
                        >
                            !
                        </th>

                        <th
                            class="w-10 px-2 py-3 text-center"
                        >
                            No
                        </th>

                        <th class="px-2 py-3">
                            Container
                        </th>

                        <th class="px-2 py-3">
                            Job
                        </th>

                        <th
                            class="px-2 py-3 text-center"
                        >
                            Proses
                        </th>

                    </tr>

                </thead>


                <tbody
                    class="divide-y divide-slate-200
                           dark:divide-slate-800"
                >

                    @foreach ($rows as $index => $row)

                        @php

                            $number =
                                $isPaginated
                                    ? (
                                        (($currentPage - 1) * $perPage)
                                        + $index
                                        + 1
                                    )
                                    : ($index + 1);


                            $lokasiAwal = trim(
                                (string) ($row->LOKASI_AWAL ?? '')
                            );


                            $tierAwal = trim(
                                (string) ($row->TIER_AWAL ?? '')
                            );


                            $lokasiAkhir = trim(
                                (string) ($row->LOKASI_AKHIR ?? '')
                            );


                            $tierAkhir = trim(
                                (string) ($row->TIER_AKHIR ?? '')
                            );


                            $displayLokasiAwal =
                                $lokasiAwal !== ''
                                    ? $lokasiAwal .
                                        (
                                            $tierAwal !== ''
                                                ? '0' . $tierAwal
                                                : ''
                                        )
                                    : '-';


                            $displayLokasiAkhir =
                                $lokasiAkhir !== ''
                                    ? $lokasiAkhir .
                                        (
                                            $tierAkhir !== ''
                                                ? '0' . $tierAkhir
                                                : ''
                                        )
                                    : '-';

                        @endphp


                        {{-- ================================================= --}}
                        {{-- MAIN ROW --}}
                        {{-- ================================================= --}}

                        <tr
                            class="bg-white transition
                                   hover:bg-slate-50
                                   dark:bg-slate-900
                                   dark:hover:bg-slate-800/50"
                        >

                            {{-- ! --}}
                            <td
                                class="px-2 py-4 text-center"
                            >

                                <button
                                    type="button"
                                    data-yard-mobile-detail="{{ $number }}"
                                    aria-expanded="false"
                                    aria-label="Lihat detail {{ $row->NO_CONT }}"
                                    class="inline-flex h-10 w-10
                                           items-center justify-center
                                           rounded-full
                                           border-2 border-amber-400
                                           bg-amber-50
                                           text-sm font-black
                                           text-amber-500
                                           transition
                                           hover:bg-amber-100
                                           active:scale-95
                                           dark:border-amber-400
                                           dark:bg-amber-400/10
                                           dark:text-amber-400"
                                >
                                    !
                                </button>

                            </td>


                            {{-- NO --}}
                            <td
                                class="px-2 py-4 text-center
                                       text-sm font-semibold
                                       text-slate-500
                                       dark:text-slate-400"
                            >
                                {{ $number }}
                            </td>


                            {{-- CONTAINER --}}
                            <td
                                class="px-2 py-4 font-bold
                                       text-slate-900
                                       dark:text-white"
                            >
                                {{ $row->NO_CONT ?? '-' }}
                            </td>


                            {{-- JOB --}}
                            <td
                                class="px-2 py-4"
                            >

                                <span
                                    class="inline-flex
                                           max-w-[110px]
                                           rounded-xl
                                           bg-sky-500/10
                                           px-2.5 py-1.5
                                           text-[10px]
                                           font-semibold
                                           text-sky-500
                                           dark:bg-sky-400/10
                                           dark:text-sky-400"
                                >
                                    {{ $row->JENIS ?? '-' }}
                                </span>

                            </td>


                            {{-- PROSES --}}
                            <td
                                class="px-2 py-4 text-center"
                            >

                                <button
                                    type="button"
                                    data-detail-button
                                    data-id-job-slip="{{ $row->ID_JOB_SLIP }}"
                                    data-detail-id="{{ $row->ID_JOB_SLIP }}"
                                    data-container="{{ $row->NO_CONT }}"
                                    class="inline-flex min-h-10
                                           items-center
                                           justify-center
                                           rounded-xl
                                           bg-sky-600
                                           px-3 py-2.5
                                           text-[10px]
                                           font-bold text-white
                                           transition
                                           hover:bg-sky-700
                                           active:scale-[0.98]"
                                >
                                    PROSES
                                </button>

                            </td>

                        </tr>


                        {{-- ================================================= --}}
                        {{-- MOBILE DETAIL --}}
                        {{-- ================================================= --}}

                        <tr
                            data-yard-mobile-detail-row="{{ $number }}"
                            class="hidden"
                        >

                            <td
                                colspan="5"
                                class="px-4 pb-4 pt-5"
                            >

                                <div
                                    class="rounded-2xl
                                           border
                                           border-slate-700
                                           bg-slate-900
                                           px-5 py-5
                                           shadow-sm
                                           dark:border-slate-700
                                           dark:bg-slate-900"
                                >

                                    {{-- DETAIL HEADER --}}
                                    <div
                                        class="flex items-start
                                               justify-between
                                               gap-3"
                                    >

                                        <div class="min-w-0">

                                            <p
                                                class="text-xs
                                                       font-medium
                                                       uppercase
                                                       tracking-wide
                                                       text-sky-300"
                                            >
                                                DETAIL JOB
                                            </p>

                                            <p
                                                class="mt-2 truncate
                                                       text-base
                                                       font-bold
                                                       text-white"
                                            >
                                                {{ $row->NO_CONT ?? '-' }}
                                            </p>

                                        </div>


                                        {{-- JOB BADGE --}}
                                        <span
                                            class="shrink-0
                                                   rounded-xl
                                                   bg-sky-500/10
                                                   px-2.5 py-1.5
                                                   text-[10px]
                                                   font-semibold
                                                   text-sky-400"
                                        >
                                            {{ $row->JENIS ?? '-' }}
                                        </span>

                                    </div>


                                    {{-- DETAIL CONTENT --}}
                                    <div
                                        class="mt-6 space-y-4"
                                    >

                                        {{-- ID JOB --}}
                                        <div
                                            class="flex items-center
                                                   justify-between
                                                   gap-4"
                                        >

                                            <span
                                                class="text-sm
                                                       text-sky-300"
                                            >
                                                ID Job
                                            </span>

                                            <span
                                                class="text-right
                                                       text-sm
                                                       font-bold
                                                       text-white"
                                            >
                                                {{ $row->ID_JOB_SLIP ?? '-' }}
                                            </span>

                                        </div>


                                        {{-- NO CONTAINER --}}
                                        <div
                                            class="flex items-center
                                                   justify-between
                                                   gap-4"
                                        >

                                            <span
                                                class="text-sm
                                                       text-sky-300"
                                            >
                                                No Container
                                            </span>

                                            <span
                                                class="text-right
                                                       text-sm
                                                       font-bold
                                                       text-white"
                                            >
                                                {{ $row->NO_CONT ?? '-' }}
                                            </span>

                                        </div>


                                        {{-- UKURAN --}}
                                        <div
                                            class="flex items-center
                                                   justify-between
                                                   gap-4"
                                        >

                                            <span
                                                class="text-sm
                                                       text-sky-300"
                                            >
                                                Ukuran
                                            </span>

                                            <span
                                                class="text-right
                                                       text-sm
                                                       font-bold
                                                       text-white"
                                            >
                                                {{ $row->UKR_CONT ?? '-' }}
                                            </span>

                                        </div>


                                        {{-- LOKASI AWAL --}}
                                        <div
                                            class="flex items-center
                                                   justify-between
                                                   gap-4"
                                        >

                                            <span
                                                class="text-sm
                                                       text-sky-300"
                                            >
                                                Lokasi Awal
                                            </span>

                                            <span
                                                class="text-right
                                                       text-sm
                                                       font-bold
                                                       text-white"
                                            >
                                                {{ $displayLokasiAwal }}
                                            </span>

                                        </div>


                                        {{-- LOKASI AKHIR --}}
                                        <div
                                            class="flex items-center
                                                   justify-between
                                                   gap-4"
                                        >

                                            <span
                                                class="text-sm
                                                       text-sky-300"
                                            >
                                                Lokasi Akhir
                                            </span>

                                            <span
                                                class="text-right
                                                       text-sm
                                                       font-bold
                                                       text-white"
                                            >
                                                {{ $displayLokasiAkhir }}
                                            </span>

                                        </div>


                                        {{-- JOB --}}
                                        <div
                                            class="flex items-center
                                                   justify-between
                                                   gap-4"
                                        >

                                            <span
                                                class="text-sm
                                                       text-sky-300"
                                            >
                                                Job
                                            </span>

                                            <span
                                                class="text-right
                                                       text-sm
                                                       font-bold
                                                       text-white"
                                            >
                                                {{ $row->JENIS ?? '-' }}
                                            </span>

                                        </div>

                                    </div>


                                    {{-- DIVIDER --}}
                                    <div
                                        class="my-5 border-t
                                               border-slate-700"
                                    ></div>


                                    {{-- PROSES --}}
                                    <button
                                        type="button"
                                        data-detail-button
                                        data-id-job-slip="{{ $row->ID_JOB_SLIP }}"
                                        data-detail-id="{{ $row->ID_JOB_SLIP }}"
                                        data-container="{{ $row->NO_CONT }}"
                                        class="flex min-h-12 w-full
                                               items-center
                                               justify-center
                                               rounded-xl
                                               bg-sky-600
                                               px-4 py-3
                                               text-sm font-bold
                                               text-white
                                               transition
                                               hover:bg-sky-700
                                               active:scale-[0.98]"
                                    >
                                        PROSES MARSHALLING
                                    </button>

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

    @if ($isPaginated && $lastPage > 1)

        <div
            class="border-t border-slate-200
                   px-4 py-4
                   dark:border-slate-800"
        >

            {{-- INFO --}}
            <div
                class="mb-3 text-center text-xs
                       text-slate-500
                       dark:text-slate-400"
            >

                Menampilkan

                <span
                    class="font-semibold
                           text-slate-700
                           dark:text-slate-200"
                >
                    {{ $from ?? 0 }}
                </span>

                sampai

                <span
                    class="font-semibold
                           text-slate-700
                           dark:text-slate-200"
                >
                    {{ $to ?? 0 }}
                </span>

                dari

                <span
                    class="font-semibold
                           text-slate-700
                           dark:text-slate-200"
                >
                    {{ $total }}
                </span>

                data

            </div>


            {{-- PAGINATION BUTTONS --}}
            <div
                class="flex flex-wrap
                       items-center
                       justify-center
                       gap-1.5"
            >

                {{-- PREVIOUS --}}
                @if ($currentPage > 1)

                    <button
                        type="button"
                        data-yard-page="{{ $currentPage - 1 }}"
                        class="inline-flex h-9 min-w-9
                               items-center justify-center
                               rounded-lg border
                               border-slate-200
                               bg-white px-2
                               text-xs font-semibold
                               text-slate-600 transition
                               hover:bg-slate-50
                               hover:text-sky-600
                               focus:outline-none
                               focus:ring-2
                               focus:ring-sky-500
                               dark:border-slate-700
                               dark:bg-slate-900
                               dark:text-slate-300
                               dark:hover:bg-slate-800"
                    >
                        ‹
                    </button>

                @else

                    <button
                        type="button"
                        disabled
                        class="inline-flex h-9 min-w-9
                               cursor-not-allowed
                               items-center justify-center
                               rounded-lg border
                               border-slate-200
                               bg-slate-100 px-2
                               text-xs font-semibold
                               text-slate-400
                               dark:border-slate-700
                               dark:bg-slate-800
                               dark:text-slate-600"
                    >
                        ‹
                    </button>

                @endif


                @php

                    $current =
                        $currentPage;

                    $last =
                        $lastPage;

                    $paginationPages = [];


                    if ($last <= 7) {

                        $paginationPages =
                            range(
                                1,
                                $last
                            );

                    } else {

                        $paginationPages[] =
                            1;


                        if ($current > 4) {

                            $paginationPages[] =
                                '...';
                        }


                        $startPage =
                            max(
                                2,
                                $current - 1
                            );


                        $endPage =
                            min(
                                $last - 1,
                                $current + 1
                            );


                        for (
                            $page = $startPage;
                            $page <= $endPage;
                            $page++
                        ) {

                            $paginationPages[] =
                                $page;
                        }


                        if (
                            $current <
                            $last - 3
                        ) {

                            $paginationPages[] =
                                '...';
                        }


                        $paginationPages[] =
                            $last;
                    }

                @endphp


                {{-- PAGE NUMBERS --}}
                @foreach (
                    $paginationPages
                    as $page
                )

                    @if ($page === '...')

                        <span
                            class="inline-flex h-9
                                   min-w-9
                                   items-center
                                   justify-center
                                   px-1
                                   text-xs
                                   font-semibold
                                   text-slate-400"
                        >
                            ...
                        </span>

                    @else

                        <button
                            type="button"
                            data-yard-page="{{ $page }}"
                            @if ($page == $current)
                                disabled
                            @endif
                            class="inline-flex h-9
                                   min-w-9
                                   items-center
                                   justify-center
                                   rounded-lg
                                   px-2.5
                                   text-xs
                                   font-bold
                                   transition
                                   focus:outline-none
                                   focus:ring-2
                                   focus:ring-sky-500
                                   {{ $page == $current
                                       ? 'bg-sky-600 text-white'
                                       : 'border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 hover:text-sky-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800' }}"
                        >
                            {{ $page }}
                        </button>

                    @endif

                @endforeach


                {{-- NEXT --}}
                @if ($currentPage < $lastPage)

                    <button
                        type="button"
                        data-yard-page="{{ $currentPage + 1 }}"
                        class="inline-flex h-9
                               min-w-9
                               items-center
                               justify-center
                               rounded-lg
                               border
                               border-slate-200
                               bg-white
                               px-2
                               text-xs
                               font-semibold
                               text-slate-600
                               transition
                               hover:bg-slate-50
                               hover:text-sky-600
                               focus:outline-none
                               focus:ring-2
                               focus:ring-sky-500
                               dark:border-slate-700
                               dark:bg-slate-900
                               dark:text-slate-300
                               dark:hover:bg-slate-800"
                    >
                        ›
                    </button>

                @else

                    <button
                        type="button"
                        disabled
                        class="inline-flex h-9
                               min-w-9
                               cursor-not-allowed
                               items-center
                               justify-center
                               rounded-lg
                               border
                               border-slate-200
                               bg-slate-100
                               px-2
                               text-xs
                               font-semibold
                               text-slate-400
                               dark:border-slate-700
                               dark:bg-slate-800
                               dark:text-slate-600"
                    >
                        ›
                    </button>

                @endif

            </div>

        </div>

    @endif

@endif