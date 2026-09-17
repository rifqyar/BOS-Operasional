@if (!empty($data))

    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">

        @foreach ($data as $row)

            <div
                class="rounded-xl border border-slate-200 bg-slate-50 p-4 transition hover:border-sky-300 hover:bg-sky-50 dark:border-slate-700 dark:bg-slate-950 dark:hover:border-sky-700 dark:hover:bg-slate-900"
            >

                <div class="flex items-start justify-between gap-3">

                    <div>
                        <div class="text-[11px] font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            No Container
                        </div>

                        <div class="mt-1 text-base font-bold tracking-wide text-slate-900 dark:text-white">
                            {{ $row->NO_CONT ?? '-' }}
                        </div>
                    </div>

                    <div class="rounded-lg bg-sky-100 px-2.5 py-1 text-[10px] font-semibold text-sky-700 dark:bg-sky-950 dark:text-sky-300">
                        REEFER
                    </div>

                </div>


                <div class="mt-4">

                    <button
                        type="button"
                        data-detail-button
                        data-no-cont="{{ $row->NO_CONT ?? '' }}"
                        data-default-label="PROSES"
                        class="inline-flex min-h-[40px] w-full items-center justify-center rounded-xl bg-sky-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-sky-700 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        PROSES
                    </button>

                </div>

            </div>

        @endforeach

    </div>

@else

    <div class="rounded-xl border border-slate-200 bg-slate-50 p-5 text-center dark:border-slate-700 dark:bg-slate-950">

        <div class="text-sm font-semibold text-slate-700 dark:text-slate-200">
            Data tidak ditemukan
        </div>

    </div>

@endif