<div
    class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200
           dark:bg-slate-900 dark:ring-slate-800"
>
    <div
        class="border-b border-slate-200 px-4 py-4
               dark:border-slate-800"
    >
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900 dark:text-white">
                    Monitoring Marshalling CIC
                </h2>

                <p class="text-xs text-slate-500">
                    Daftar pekerjaan Marshalling CIC
                </p>
            </div>

            <span
                class="text-xs font-medium text-slate-500"
            >
                {{ count($jobs) }} data
            </span>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[1100px] text-left text-sm">
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

                    <th class="px-4 py-3">
                        Respon
                    </th>

                    <th class="px-4 py-3 text-center">
                        Marshalling
                    </th>
                </tr>
            </thead>

            <tbody
                class="divide-y divide-slate-200 dark:divide-slate-800"
            >
                @forelse ($jobs as $index => $job)
                    <tr
                        class="hover:bg-slate-50 dark:hover:bg-slate-800/50"
                    >
                        {{-- NO --}}
                        <td
                            class="px-4 py-3 text-center text-slate-500"
                        >
                            {{ $index + 1 }}
                        </td>

                        {{-- ID JOB --}}
                        <td
                            class="px-4 py-3 font-semibold text-slate-900
                                   dark:text-white"
                        >
                            {{ $job->ID_JOB_SLIP ?? '-' }}
                        </td>

                        {{-- NO CONTAINER --}}
                        <td
                            class="px-4 py-3 font-semibold text-slate-900
                                   dark:text-white"
                        >
                            {{ $job->NO_CONT ?? '-' }}
                        </td>

                        {{-- UKURAN --}}
                        <td class="px-4 py-3">
                            {{ $job->UKR_CONT ?? '-' }}
                        </td>

                        {{-- LOKASI AWAL --}}
                        <td class="px-4 py-3">
                            @if (!empty($job->LOKASI_AWAL))
                                {{ $job->LOKASI_AWAL }}
                                0
                                {{ $job->TIER_AWAL ?? '' }}
                            @else
                                -
                            @endif
                        </td>

                        {{-- LOKASI AKHIR --}}
                        <td class="px-4 py-3">
                            @if (!empty($job->LOKASI_AKHIR))
                                {{ $job->LOKASI_AKHIR }}
                                0
                                {{ $job->TIER_AKHIR ?? '' }}
                            @else
                                -
                            @endif
                        </td>

                        {{-- JOB --}}
                        <td class="px-4 py-3">
                            {{ $job->JENIS ?? '-' }}
                        </td>

                        {{-- RESPON --}}
                        <td class="px-4 py-3">
                            @if (!empty($job->RESPON))
                                {{ $job->RESPON }}
                            @else
                                <span class="text-slate-400">
                                    NO RESPON
                                </span>
                            @endif
                        </td>

                        {{-- PROSES --}}
                        <td class="px-4 py-3 text-center">
                            <button
                                type="button"
                                data-detail-id="{{ $job->ID_JOB_SLIP }}"
                                data-detail-button
                                class="inline-flex items-center justify-center
                                       rounded-xl bg-sky-600 px-4 py-2 text-xs
                                       font-bold text-white hover:bg-sky-700
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
                            class="px-4 py-10 text-center text-sm text-slate-500"
                        >
                            Tidak ada pekerjaan Marshalling CIC yang menunggu.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>