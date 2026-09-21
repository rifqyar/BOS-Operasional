@if ($data->isEmpty())

    <div class="px-4 py-8 text-center">

        <div class="mx-auto flex size-10 items-center justify-center rounded-md bg-slate-100 text-slate-500 dark:bg-white/5 dark:text-slate-400">

            <flux:icon.inbox class="size-5" />

        </div>

        <p class="mt-3 text-sm font-semibold text-slate-700 dark:text-slate-200">
            Belum ada container HOLD
        </p>

        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
            Tidak terdapat container yang sedang berstatus HOLD.
        </p>

    </div>

@else

    <div class="overflow-x-auto">

        <table class="w-full min-w-[720px] text-left">

            <thead class="border-b border-slate-200 bg-slate-50 dark:border-white/10 dark:bg-white/5">

                <tr>

                    <th class="whitespace-nowrap px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                        No SPK
                    </th>

                    <th class="whitespace-nowrap px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                        No Container
                    </th>

                    <th class="whitespace-nowrap px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                        No Dokumen
                    </th>

                    <th class="whitespace-nowrap px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                        Tanggal
                    </th>

                    <th class="whitespace-nowrap px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                        Jenis Dokumen
                    </th>

                    <th class="whitespace-nowrap px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                        Warna
                    </th>

                    <th class="whitespace-nowrap px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                        Action
                    </th>

                </tr>

            </thead>


            <tbody class="divide-y divide-slate-200 dark:divide-white/10">

                @foreach ($data as $row)

                    <tr class="transition hover:bg-slate-50 dark:hover:bg-white/[0.03]">

                        <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-700 dark:text-slate-300">
                            {{ $row->NO_SPK }}
                        </td>


                        <td class="whitespace-nowrap px-4 py-3">

                            <span class="text-sm font-semibold text-slate-950 dark:text-white">
                                {{ $row->NO_CONT }}
                            </span>

                        </td>


                        <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-700 dark:text-slate-300">
                            {{ $row->NO_DOK }}
                        </td>


                        <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-700 dark:text-slate-300">
                            {{ $row->TGL_DOK }}
                        </td>


                        <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-700 dark:text-slate-300">
                            {{ $row->JNS_DOK ?? '-' }}
                        </td>


                        <td class="whitespace-nowrap px-4 py-3">

                            <span
                                class="inline-flex rounded-sm bg-amber-100 px-2 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-400/10 dark:text-amber-300">

                                {{ $row->WARNA ?? '-' }}

                            </span>

                        </td>


                        <td class="px-4 py-3 text-center">

                            <button
                                type="button"
                                data-release-row
                                data-id="{{ $row->ID }}"
                                data-no-spk="{{ $row->NO_SPK }}"
                                data-no-cont="{{ $row->NO_CONT }}"
                                class="inline-flex min-h-9 items-center justify-center gap-1.5 rounded-md bg-amber-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 dark:focus:ring-offset-slate-950">

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