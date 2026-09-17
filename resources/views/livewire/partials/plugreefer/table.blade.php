@if (empty($rows))

    <div class="p-6 text-center">

        <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">
            Container tidak ditemukan.
        </p>

    </div>

@else

    <div class="overflow-x-auto">

        <table class="w-full min-w-[700px] text-left text-sm">

            <thead class="bg-slate-100 dark:bg-slate-800">

                <tr>

                    <th class="px-4 py-3 font-semibold text-slate-700 dark:text-slate-200">
                        No
                    </th>

                    <th class="px-4 py-3 font-semibold text-slate-700 dark:text-slate-200">
                        No Container
                    </th>

                    <th class="px-4 py-3 font-semibold text-slate-700 dark:text-slate-200">
                        Temperature Default
                    </th>

                    <th class="px-4 py-3 font-semibold text-slate-700 dark:text-slate-200">
                        Temperature TPS
                    </th>

                    <th class="px-4 py-3 text-center font-semibold text-slate-700 dark:text-slate-200">
                        Proses
                    </th>

                </tr>

            </thead>

            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">

                @foreach ($rows as $index => $row)

                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">

                        <td class="px-4 py-3 text-slate-500 dark:text-slate-400">
                            {{ $index + 1 }}
                        </td>

                        <td class="px-4 py-3 font-bold text-slate-900 dark:text-white">
                            {{ $row->NO_CONT ?? '-' }}
                        </td>

                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                            {{ $row->SUHU_CUST ?? '-' }}
                        </td>

                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                            {{ $row->SUHU_TERMINAL ?? '-' }}
                        </td>

                        <td class="px-4 py-3 text-center">

                            <button
                                type="button"
                                data-detail-button
                                data-no-cont="{{ $row->NO_CONT ?? '' }}"
                                class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                Proses
                            </button>

                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

    </div>

@endif