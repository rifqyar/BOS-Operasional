@if ($data->isEmpty())

    <div class="px-4 py-8 text-center">

        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
            Tidak ada container yang sedang HOLD.
        </p>

    </div>

@else

    <div class="overflow-x-auto">

        <table class="w-full min-w-[1000px] text-left">

            <thead class="border-b border-slate-200 bg-slate-50 dark:border-white/10 dark:bg-white/5">

                <tr>

                    <th class="px-5 py-3 text-xs font-bold uppercase tracking-wide text-slate-500">
                        No
                    </th>

                    <th class="px-5 py-3 text-xs font-bold uppercase tracking-wide text-slate-500">
                        No. Container
                    </th>

                    <th class="px-5 py-3 text-xs font-bold uppercase tracking-wide text-slate-500">
                        No. SPK
                    </th>

                    <th class="px-5 py-3 text-xs font-bold uppercase tracking-wide text-slate-500">
                        No. Dokumen
                    </th>

                    <th class="px-5 py-3 text-xs font-bold uppercase tracking-wide text-slate-500">
                        Tanggal Dokumen
                    </th>

                    <th class="px-5 py-3 text-xs font-bold uppercase tracking-wide text-slate-500">
                        Jenis Dokumen
                    </th>

                    <th class="px-5 py-3 text-xs font-bold uppercase tracking-wide text-slate-500">
                        Status
                    </th>

                    <th class="px-5 py-3 text-xs font-bold uppercase tracking-wide text-slate-500">
                        Warna
                    </th>

                    <th class="px-5 py-3 text-right text-xs font-bold uppercase tracking-wide text-slate-500">
                        Action
                    </th>

                </tr>

            </thead>


            <tbody class="divide-y divide-slate-200 dark:divide-white/10">

                @foreach ($data as $index => $row)

                    <tr class="transition hover:bg-slate-50 dark:hover:bg-white/5">

                        <td class="px-5 py-4 text-sm text-slate-500">
                            {{ $index + 1 }}
                        </td>


                        <td class="px-5 py-4">

                            <span class="font-bold text-slate-950 dark:text-white">
                                {{ $row->NO_CONT ?? '-' }}
                            </span>

                        </td>


                        <td class="px-5 py-4 text-sm text-slate-700 dark:text-slate-300">
                            {{ $row->NO_SPK ?? '-' }}
                        </td>


                        <td class="px-5 py-4 text-sm text-slate-700 dark:text-slate-300">
                            {{ $row->NO_DOK ?? '-' }}
                        </td>


                        <td class="px-5 py-4 text-sm text-slate-700 dark:text-slate-300">
                            {{ $row->TGL_DOK ?? '-' }}
                        </td>


                        <td class="px-5 py-4 text-sm text-slate-700 dark:text-slate-300">
                            {{ $row->JNS_DOK ?? '-' }}
                        </td>


                        <td class="px-5 py-4">

                            <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 dark:bg-amber-400/10 dark:text-amber-300">
                                {{ $row->KETERANGAN ?? 'HOLD' }}
                            </span>

                        </td>


                        <td class="px-5 py-4">

                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                                {{ $row->WARNA ?? '-' }}
                            </span>

                        </td>


                        <td class="px-5 py-4 text-right">

                            <button
                                type="button"
                                data-release-row
                                data-id="{{ $row->ID ?? '' }}"
                                data-no-spk="{{ $row->NO_SPK ?? '' }}"
                                data-no-cont="{{ $row->NO_CONT ?? '' }}"
                                class="inline-flex items-center gap-2 rounded-md bg-emerald-600 px-3 py-2 text-xs font-bold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-70"
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

@endif