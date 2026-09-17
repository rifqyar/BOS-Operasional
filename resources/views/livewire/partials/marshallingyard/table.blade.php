@php
    $rows = $rows ?? [];
@endphp

@if (empty($rows))

    <div class="px-4 py-10 text-center">
        <div
            class="mx-auto mb-3 flex h-12 w-12 items-center justify-center
                   rounded-full bg-slate-100 text-slate-400
                   dark:bg-slate-800"
        >
            <flux:icon.magnifying-glass class="h-5 w-5" />
        </div>

        <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">
            Data tidak ditemukan
        </p>

        <p class="mt-1 text-xs text-slate-500">
            Tidak ada pekerjaan Marshalling Yard yang sesuai.
        </p>
    </div>

@else

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
            class="divide-y divide-slate-200 dark:divide-slate-800"
        >
            @foreach ($rows as $index => $row)

                @php
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
                            ? $lokasiAwal . ($tierAwal !== '' ? '0' . $tierAwal : '')
                            : '-';

                    $displayLokasiAkhir =
                        $lokasiAkhir !== ''
                            ? $lokasiAkhir . ($tierAkhir !== '' ? '0' . $tierAkhir : '')
                            : '-';
                @endphp

                <tr
                    class="transition hover:bg-slate-50
                           dark:hover:bg-slate-800/50"
                >
                    <td
                        class="px-4 py-3 text-center text-slate-500"
                    >
                        {{ $index + 1 }}
                    </td>

                    <td
                        class="px-4 py-3 font-semibold
                               text-slate-900 dark:text-white"
                    >
                        {{ $row->ID_JOB_SLIP ?? '-' }}
                    </td>

                    <td
                        class="px-4 py-3 font-bold
                               text-slate-900 dark:text-white"
                    >
                        {{ $row->NO_CONT ?? '-' }}
                    </td>

                    <td class="px-4 py-3">
                        {{ $row->UKR_CONT ?? '-' }}
                    </td>

                    <td class="px-4 py-3">
                        {{ $displayLokasiAwal }}
                    </td>

                    <td class="px-4 py-3">
                        {{ $displayLokasiAkhir }}
                    </td>

                    <td class="px-4 py-3">
                        <span
                            class="rounded-full bg-slate-100 px-2.5 py-1
                                   text-xs font-semibold
                                   text-slate-700
                                   dark:bg-slate-800
                                   dark:text-slate-200"
                        >
                            {{ $row->JENIS ?? '-' }}
                        </span>
                    </td>

                    <td class="px-4 py-3 text-center">
                        <button
                            type="button"
                            data-detail-button
                            data-id-job-slip="{{ $row->ID_JOB_SLIP }}"
                            data-container="{{ $row->NO_CONT }}"
                            class="inline-flex min-h-11 items-center
                                   justify-center rounded-xl
                                   bg-sky-600 px-4 py-2.5
                                   text-xs font-bold text-white
                                   transition hover:bg-sky-700
                                   active:scale-[0.98]"
                        >
                            PROSES
                        </button>
                    </td>
                </tr>

            @endforeach
        </tbody>
    </table>

@endif