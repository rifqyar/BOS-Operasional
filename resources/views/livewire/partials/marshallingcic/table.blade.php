@php
    /*
    |--------------------------------------------------------------------------
    | Pagination detection
    |--------------------------------------------------------------------------
    | getAllJobs()     -> LengthAwarePaginator
    | search()         -> array
    |
    */

    $isPaginated =
        is_object($jobs) &&
        method_exists($jobs, 'currentPage') &&
        method_exists($jobs, 'lastPage');

    $currentPage = $isPaginated
        ? $jobs->currentPage()
        : 1;

    $lastPage = $isPaginated
        ? $jobs->lastPage()
        : 1;

    $perPage = $isPaginated
        ? $jobs->perPage()
        : count($jobs);

    $total = $isPaginated
        ? $jobs->total()
        : count($jobs);

    $from = $isPaginated
        ? $jobs->firstItem()
        : ($total > 0 ? 1 : 0);

    $to = $isPaginated
        ? $jobs->lastItem()
        : $total;
@endphp


<div
    class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200
           dark:bg-slate-900 dark:ring-slate-800"
>

    {{-- =========================================================
         HEADER
         ========================================================= --}}

    <div
        class="border-b border-slate-200 px-4 py-4
               dark:border-slate-800"
    >

        <div
            class="flex flex-col gap-1
                   sm:flex-row sm:items-center
                   sm:justify-between"
        >

            <div>

                <h2
                    class="text-base font-bold
                           text-slate-900 dark:text-white"
                >
                    Monitoring Marshalling CIC
                </h2>

                <p
                    class="text-xs text-slate-500
                           dark:text-slate-400"
                >
                    Daftar pekerjaan Marshalling CIC
                </p>

            </div>


            <span
                class="text-xs font-medium
                       text-slate-500
                       dark:text-slate-400"
            >
                {{ $total }} data
            </span>

        </div>

    </div>


    {{-- =========================================================
         DESKTOP TABLE
         ========================================================= --}}

    <div class="hidden overflow-x-auto md:block">

        <table
            class="w-full min-w-[1100px] text-left text-sm"
        >

            <thead
                class="bg-slate-50 text-xs uppercase
                       tracking-wider text-slate-500
                       dark:bg-slate-800/70"
            >

                <tr>

                    {{-- NO --}}
                    <th
                        class="px-4 py-3 text-center"
                    >
                        No
                    </th>


                    {{-- ID JOB --}}
                    <th class="px-4 py-3">
                        ID Job
                    </th>


                    {{-- CONTAINER --}}
                    <th class="px-4 py-3">
                        No Container
                    </th>


                    {{-- UKURAN --}}
                    <th class="px-4 py-3">
                        Ukuran
                    </th>


                    {{-- LOKASI AWAL --}}
                    <th class="px-4 py-3">
                        Lokasi Awal
                    </th>


                    {{-- LOKASI AKHIR --}}
                    <th class="px-4 py-3">
                        Lokasi Akhir
                    </th>


                    {{-- JOB --}}
                    <th class="px-4 py-3">
                        Job
                    </th>


                    {{-- RESPON --}}
                    <th class="px-4 py-3">
                        Respon
                    </th>


                    {{-- ACTION --}}
                    <th
                        class="px-4 py-3 text-center"
                    >
                        Marshalling
                    </th>

                </tr>

            </thead>


            <tbody
                class="divide-y divide-slate-200
                       dark:divide-slate-800"
            >

                @forelse ($jobs as $index => $job)

                    @php
                        /*
                        |--------------------------------------------------------------------------
                        | Global row number
                        |--------------------------------------------------------------------------
                        */

                        $rowNumber = $isPaginated
                            ? (($currentPage - 1) * $perPage) + $index + 1
                            : $index + 1;
                    @endphp


                    <tr
                        class="transition hover:bg-slate-50
                               dark:hover:bg-slate-800/50"
                    >

                        {{-- =================================================
                             NO
                             ================================================= --}}

                        <td
                            class="px-4 py-3 text-center
                                   text-slate-500
                                   dark:text-slate-400"
                        >
                            {{ $rowNumber }}
                        </td>


                        {{-- =================================================
                             ID JOB
                             ================================================= --}}

                        <td
                            class="px-4 py-3 font-semibold
                                   text-slate-900
                                   dark:text-white"
                        >
                            {{ $job->ID_JOB_SLIP ?? '-' }}
                        </td>


                        {{-- =================================================
                             NO CONTAINER
                             ================================================= --}}

                        <td
                            class="px-4 py-3 font-semibold
                                   text-slate-900
                                   dark:text-white"
                        >
                            {{ $job->NO_CONT ?? '-' }}
                        </td>


                        {{-- =================================================
                             UKURAN
                             ================================================= --}}

                        <td
                            class="px-4 py-3 text-slate-700
                                   dark:text-slate-300"
                        >
                            {{ $job->UKR_CONT ?? '-' }}
                        </td>


                        {{-- =================================================
                             LOKASI AWAL
                             ================================================= --}}

                        <td
                            class="px-4 py-3 text-slate-700
                                   dark:text-slate-300"
                        >

                            @if (!empty($job->LOKASI_AWAL))

                                {{ $job->LOKASI_AWAL }}0{{ $job->TIER_AWAL ?? '' }}

                            @else

                                -

                            @endif

                        </td>


                        {{-- =================================================
                             LOKASI AKHIR
                             ================================================= --}}

                        <td
                            class="px-4 py-3 text-slate-700
                                   dark:text-slate-300"
                        >

                            @if (!empty($job->LOKASI_AKHIR))

                                {{ $job->LOKASI_AKHIR }}0{{ $job->TIER_AKHIR ?? '' }}

                            @else

                                -

                            @endif

                        </td>


                        {{-- =================================================
                             JOB
                             ================================================= --}}

                        <td
                            class="px-4 py-3 text-slate-700
                                   dark:text-slate-300"
                        >
                            {{ $job->JENIS ?? '-' }}
                        </td>


                        {{-- =================================================
                             RESPON
                             ================================================= --}}

                        <td class="px-4 py-3">

                            @if (!empty($job->RESPON))

                                <span
                                    class="font-medium
                                           text-slate-700
                                           dark:text-slate-300"
                                >
                                    {{ $job->RESPON }}
                                </span>

                            @else

                                <span
                                    class="text-slate-400"
                                >
                                    NO RESPON
                                </span>

                            @endif

                        </td>


                        {{-- =================================================
                             PROSES
                             ================================================= --}}

                        <td
                            class="px-4 py-3 text-center"
                        >

                            <button
                                type="button"
                                data-detail-id="{{ $job->ID_JOB_SLIP }}"
                                data-detail-button
                                class="inline-flex items-center
                                       justify-center rounded-xl
                                       bg-sky-600 px-4 py-2
                                       text-xs font-bold
                                       text-white transition
                                       hover:bg-sky-700
                                       focus:outline-none
                                       focus:ring-2
                                       focus:ring-sky-500
                                       disabled:cursor-not-allowed
                                       disabled:opacity-60"
                            >
                                PROSES
                            </button>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="9"
                            class="px-4 py-10 text-center
                                   text-sm text-slate-500
                                   dark:text-slate-400"
                        >
                            Tidak ada pekerjaan Marshalling CIC yang menunggu.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- =========================================================
         MOBILE TABLE
         ========================================================= --}}

    <div class="block md:hidden">

        <div class="overflow-hidden">

            <table
                class="w-full table-fixed text-left text-sm"
            >

                <thead
                    class="bg-slate-50 text-[10px]
                           uppercase tracking-wide
                           text-slate-500
                           dark:bg-slate-800/70"
                >

                    <tr>

                        {{-- ! --}}
                        <th
                            class="w-9 px-1 py-3 text-center"
                        >
                            !
                        </th>


                        {{-- NO --}}
                        <th
                            class="w-9 px-1 py-3 text-center"
                        >
                            No
                        </th>


                        {{-- CONTAINER --}}
                        <th class="px-2 py-3">
                            Container
                        </th>


                        {{-- RESPON --}}
                        <th class="px-2 py-3">
                            Respon
                        </th>


                        {{-- PROSES --}}
                        <th
                            class="w-[72px] px-1 py-3
                                   text-center"
                        >
                            Proses
                        </th>

                    </tr>

                </thead>


                <tbody
                    class="divide-y divide-slate-200
                           dark:divide-slate-800"
                >

                    @forelse ($jobs as $index => $job)

                        @php
                            /*
                            |--------------------------------------------------------------------------
                            | Global row number
                            |--------------------------------------------------------------------------
                            */

                            $rowNumber = $isPaginated
                                ? (($currentPage - 1) * $perPage) + $index + 1
                                : $index + 1;
                        @endphp


                        {{-- =================================================
                             MOBILE MAIN ROW
                             ================================================= --}}

                        <tr
                            class="bg-white transition
                                   hover:bg-slate-50
                                   dark:bg-slate-900
                                   dark:hover:bg-slate-800/50"
                        >

                            {{-- ! --}}
                            <td
                                class="px-1 py-4 text-center"
                            >

                                <button
                                    type="button"
                                    data-cic-mobile-detail="{{ $index + 1 }}"
                                    aria-expanded="false"
                                    aria-label="Lihat detail {{ $job->NO_CONT ?? 'container' }}"
                                    class="inline-flex size-7
                                           items-center justify-center
                                           rounded-full
                                           text-sm font-black
                                           text-amber-500
                                           transition
                                           hover:bg-amber-50
                                           hover:text-amber-600
                                           focus:outline-none
                                           focus:ring-2
                                           focus:ring-amber-500
                                           dark:hover:bg-amber-400/10"
                                >
                                    !
                                </button>

                            </td>


                            {{-- NO --}}
                            <td
                                class="px-1 py-4 text-center"
                            >

                                <span
                                    class="text-sm font-semibold
                                           text-slate-500
                                           dark:text-slate-400"
                                >
                                    {{ $rowNumber }}
                                </span>

                            </td>


                            {{-- CONTAINER --}}
                            <td class="px-2 py-4">

                                <div
                                    class="truncate font-semibold
                                           text-slate-900
                                           dark:text-white"
                                >
                                    {{ $job->NO_CONT ?? '-' }}
                                </div>

                            </td>


                            {{-- RESPON --}}
                            <td class="px-2 py-4">

                                @if (!empty($job->RESPON))

                                    <div
                                        class="truncate text-xs
                                               font-semibold
                                               text-slate-700
                                               dark:text-slate-300"
                                    >
                                        {{ $job->RESPON }}
                                    </div>

                                @else

                                    <span
                                        class="whitespace-nowrap
                                               text-[10px]
                                               font-medium
                                               text-slate-400"
                                    >
                                        NO RESPON
                                    </span>

                                @endif

                            </td>


                            {{-- PROSES --}}
                            <td
                                class="px-1 py-4 text-center"
                            >

                                <button
                                    type="button"
                                    data-detail-id="{{ $job->ID_JOB_SLIP }}"
                                    data-detail-button
                                    class="inline-flex min-h-8
                                           items-center
                                           justify-center
                                           rounded-lg
                                           bg-sky-600 px-2 py-1.5
                                           text-[10px] font-bold
                                           text-white transition
                                           hover:bg-sky-700
                                           focus:outline-none
                                           focus:ring-2
                                           focus:ring-sky-500
                                           disabled:cursor-not-allowed
                                           disabled:opacity-60"
                                >
                                    PROSES
                                </button>

                            </td>

                        </tr>


                        {{-- =================================================
                             MOBILE DETAIL
                             ================================================= --}}

                        <tr
                            data-cic-mobile-detail-row="{{ $index + 1 }}"
                            class="hidden bg-slate-50
                                   dark:bg-slate-800/30"
                        >

                            <td
                                colspan="5"
                                class="px-4 py-4"
                            >

                                <div
                                    class="rounded-xl border
                                           border-slate-200
                                           bg-white p-4 shadow-sm
                                           dark:border-slate-700
                                           dark:bg-slate-900"
                                >

                                    {{-- =====================================
                                         DETAIL HEADER
                                         ===================================== --}}

                                    <div
                                        class="mb-4 flex items-center
                                               justify-between gap-3"
                                    >

                                        <div>

                                            <p
                                                class="text-[10px]
                                                       font-semibold
                                                       uppercase
                                                       tracking-wide
                                                       text-slate-500
                                                       dark:text-slate-400"
                                            >
                                                Detail Job
                                            </p>

                                            <p
                                                class="mt-1 text-sm font-bold
                                                       text-slate-900
                                                       dark:text-white"
                                            >
                                                {{ $job->NO_CONT ?? '-' }}
                                            </p>

                                        </div>


                                        <span
                                            class="rounded-lg bg-sky-100
                                                   px-2 py-1 text-[10px]
                                                   font-bold uppercase
                                                   text-sky-700
                                                   dark:bg-sky-400/10
                                                   dark:text-sky-300"
                                        >
                                            {{ $job->JENIS ?? '-' }}
                                        </span>

                                    </div>


                                    {{-- =====================================
                                         DETAIL DATA
                                         ===================================== --}}

                                    <div class="space-y-3">

                                        {{-- ID JOB --}}
                                        <div
                                            class="flex items-start
                                                   justify-between gap-4"
                                        >

                                            <span
                                                class="shrink-0 text-xs
                                                       text-slate-500
                                                       dark:text-slate-400"
                                            >
                                                ID Job
                                            </span>

                                            <span
                                                class="text-right text-xs
                                                       font-semibold
                                                       text-slate-800
                                                       dark:text-slate-200"
                                            >
                                                {{ $job->ID_JOB_SLIP ?? '-' }}
                                            </span>

                                        </div>


                                        {{-- NO CONTAINER --}}
                                        <div
                                            class="flex items-start
                                                   justify-between gap-4"
                                        >

                                            <span
                                                class="shrink-0 text-xs
                                                       text-slate-500
                                                       dark:text-slate-400"
                                            >
                                                No Container
                                            </span>

                                            <span
                                                class="text-right text-xs
                                                       font-semibold
                                                       text-slate-800
                                                       dark:text-slate-200"
                                            >
                                                {{ $job->NO_CONT ?? '-' }}
                                            </span>

                                        </div>


                                        {{-- UKURAN --}}
                                        <div
                                            class="flex items-start
                                                   justify-between gap-4"
                                        >

                                            <span
                                                class="shrink-0 text-xs
                                                       text-slate-500
                                                       dark:text-slate-400"
                                            >
                                                Ukuran
                                            </span>

                                            <span
                                                class="text-right text-xs
                                                       font-semibold
                                                       text-slate-800
                                                       dark:text-slate-200"
                                            >
                                                {{ $job->UKR_CONT ?? '-' }}
                                            </span>

                                        </div>


                                        {{-- LOKASI AWAL --}}
                                        <div
                                            class="flex items-start
                                                   justify-between gap-4"
                                        >

                                            <span
                                                class="shrink-0 text-xs
                                                       text-slate-500
                                                       dark:text-slate-400"
                                            >
                                                Lokasi Awal
                                            </span>

                                            <span
                                                class="text-right text-xs
                                                       font-semibold
                                                       text-slate-800
                                                       dark:text-slate-200"
                                            >

                                                @if (!empty($job->LOKASI_AWAL))

                                                    {{ $job->LOKASI_AWAL }}0{{ $job->TIER_AWAL ?? '' }}

                                                @else

                                                    -

                                                @endif

                                            </span>

                                        </div>


                                        {{-- LOKASI AKHIR --}}
                                        <div
                                            class="flex items-start
                                                   justify-between gap-4"
                                        >

                                            <span
                                                class="shrink-0 text-xs
                                                       text-slate-500
                                                       dark:text-slate-400"
                                            >
                                                Lokasi Akhir
                                            </span>

                                            <span
                                                class="text-right text-xs
                                                       font-semibold
                                                       text-slate-800
                                                       dark:text-slate-200"
                                            >

                                                @if (!empty($job->LOKASI_AKHIR))

                                                    {{ $job->LOKASI_AKHIR }}0{{ $job->TIER_AKHIR ?? '' }}

                                                @else

                                                    -

                                                @endif

                                            </span>

                                        </div>


                                        {{-- JOB --}}
                                        <div
                                            class="flex items-start
                                                   justify-between gap-4"
                                        >

                                            <span
                                                class="shrink-0 text-xs
                                                       text-slate-500
                                                       dark:text-slate-400"
                                            >
                                                Job
                                            </span>

                                            <span
                                                class="text-right text-xs
                                                       font-semibold
                                                       text-slate-800
                                                       dark:text-slate-200"
                                            >
                                                {{ $job->JENIS ?? '-' }}
                                            </span>

                                        </div>


                                        {{-- RESPON --}}
                                        <div
                                            class="flex items-start
                                                   justify-between gap-4"
                                        >

                                            <span
                                                class="shrink-0 text-xs
                                                       text-slate-500
                                                       dark:text-slate-400"
                                            >
                                                Respon
                                            </span>

                                            @if (!empty($job->RESPON))

                                                <span
                                                    class="text-right text-xs
                                                           font-semibold
                                                           text-slate-800
                                                           dark:text-slate-200"
                                                >
                                                    {{ $job->RESPON }}
                                                </span>

                                            @else

                                                <span
                                                    class="text-right text-xs
                                                           text-slate-400"
                                                >
                                                    NO RESPON
                                                </span>

                                            @endif

                                        </div>

                                    </div>


                                    {{-- =====================================
                                         PROSES DI DETAIL
                                         ===================================== --}}

                                    <div
                                        class="mt-4 border-t
                                               border-slate-200 pt-4
                                               dark:border-slate-700"
                                    >

                                        <button
                                            type="button"
                                            data-detail-id="{{ $job->ID_JOB_SLIP }}"
                                            data-detail-button
                                            class="inline-flex min-h-10 w-full
                                                   items-center justify-center
                                                   rounded-xl bg-sky-600
                                                   px-4 py-2 text-xs
                                                   font-bold text-white
                                                   transition hover:bg-sky-700
                                                   focus:outline-none
                                                   focus:ring-2
                                                   focus:ring-sky-500
                                                   disabled:cursor-not-allowed
                                                   disabled:opacity-60"
                                        >
                                            PROSES MARSHALLING
                                        </button>

                                    </div>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="px-4 py-10 text-center
                                       text-sm text-slate-500
                                       dark:text-slate-400"
                            >
                                Tidak ada pekerjaan Marshalling CIC yang menunggu.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    {{-- =========================================================
         PAGINATION
         HANYA MUNCUL UNTUK DATA PAGINATOR
         ========================================================= --}}

    @if ($isPaginated && $lastPage > 1)

        <div
            class="border-t border-slate-200
                   px-4 py-4
                   dark:border-slate-800"
        >

            {{-- =====================================================
                 PAGINATION INFO
                 ===================================================== --}}

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


            {{-- =====================================================
                 PAGINATION BUTTONS
                 ===================================================== --}}

            <div
                class="flex flex-wrap items-center
                       justify-center gap-1.5"
            >

                {{-- PREVIOUS --}}
                @if ($currentPage > 1)

                    <button
                        type="button"
                        data-cic-page="{{ $currentPage - 1 }}"
                        class="inline-flex h-9 min-w-9
                               items-center justify-center
                               rounded-lg border
                               border-slate-200
                               bg-white px-2
                               text-xs font-semibold
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


                {{-- =================================================
                     PAGE NUMBERS
                     ================================================= --}}

                @php
                    $paginationPages = [];

                    if ($lastPage <= 7) {

                        $paginationPages = range(
                            1,
                            $lastPage
                        );

                    } else {

                        $paginationPages[] = 1;

                        if ($currentPage > 4) {
                            $paginationPages[] = '...';
                        }

                        $startPage = max(
                            2,
                            $currentPage - 1
                        );

                        $endPage = min(
                            $lastPage - 1,
                            $currentPage + 1
                        );

                        for (
                            $page = $startPage;
                            $page <= $endPage;
                            $page++
                        ) {
                            $paginationPages[] = $page;
                        }

                        if ($currentPage < $lastPage - 3) {
                            $paginationPages[] = '...';
                        }

                        $paginationPages[] = $lastPage;
                    }
                @endphp


                @foreach ($paginationPages as $page)

                    @if ($page === '...')

                        <span
                            class="inline-flex h-9 min-w-9
                                   items-center justify-center
                                   px-1 text-xs font-semibold
                                   text-slate-400"
                        >
                            ...
                        </span>

                    @else

                        <button
                            type="button"
                            data-cic-page="{{ $page }}"
                            @if ($page == $currentPage)
                                disabled
                            @endif
                            class="inline-flex h-9 min-w-9
                                   items-center justify-center
                                   rounded-lg px-2.5
                                   text-xs font-bold
                                   transition
                                   focus:outline-none
                                   focus:ring-2
                                   focus:ring-sky-500
                                   {{ $page == $currentPage
                                        ? 'bg-sky-600 text-white'
                                        : 'border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 hover:text-sky-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800'
                                   }}"
                        >
                            {{ $page }}
                        </button>

                    @endif

                @endforeach


                {{-- NEXT --}}
                @if ($currentPage < $lastPage)

                    <button
                        type="button"
                        data-cic-page="{{ $currentPage + 1 }}"
                        class="inline-flex h-9 min-w-9
                               items-center justify-center
                               rounded-lg border
                               border-slate-200
                               bg-white px-2
                               text-xs font-semibold
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
                        ›
                    </button>

                @endif

            </div>

        </div>

    @endif

</div>