@php
    $items = collect($data ?? []);
@endphp

<div class="space-y-4">

    <div class="rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <div class="flex items-center gap-2">

                    <flux:icon
                        name="archive-box"
                        class="size-5 text-amber-600"
                    />

                    <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">
                        Container Sedang HOLD
                    </h2>

                </div>

                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                    {{ $items->count() }} container sedang dalam kondisi HOLD.
                </p>

            </div>

            <span class="inline-flex w-fit rounded-full bg-red-100 px-3 py-1.5 text-xs font-semibold text-red-700 dark:bg-red-900/30 dark:text-red-400">
                SEDANG HOLD
            </span>

        </div>

    </div>

    <div class="hidden overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm md:block dark:border-zinc-700 dark:bg-zinc-900">

        <div class="overflow-x-auto">

            <table class="w-full min-w-[1000px] text-left text-sm">

                <thead class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800">

                    <tr>

                        <th class="px-4 py-3 text-xs font-semibold text-zinc-500">
                            NO
                        </th>

                        <th class="px-4 py-3 text-xs font-semibold text-zinc-500">
                            NO CONT
                        </th>

                        <th class="px-4 py-3 text-xs font-semibold text-zinc-500">
                            NO SPK
                        </th>

                        <th class="px-4 py-3 text-xs font-semibold text-zinc-500">
                            NO DOK
                        </th>

                        <th class="px-4 py-3 text-xs font-semibold text-zinc-500">
                            TGL DOK
                        </th>

                        <th class="px-4 py-3 text-xs font-semibold text-zinc-500">
                            JENIS DOK
                        </th>

                        <th class="px-4 py-3 text-xs font-semibold text-zinc-500">
                            WARNA
                        </th>

                        <th class="px-4 py-3 text-right text-xs font-semibold text-zinc-500">
                            ACTION
                        </th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">

                    @forelse($items as $index => $item)

                        @php
                            $warna = match (
                                strtoupper((string) ($item->FL_WARNA_HOLD ?? ''))
                            ) {
                                'N' => 'PUTIH',
                                'M' => 'MERAH',
                                'T' => 'TIMAH',
                                default => '-',
                            };
                        @endphp

                        <tr class="transition hover:bg-zinc-50 dark:hover:bg-zinc-800/50">

                            <td class="px-4 py-4 text-zinc-500">
                                {{ $index + 1 }}
                            </td>

                            <td class="px-4 py-4">

                                <div class="font-bold text-zinc-900 dark:text-white">
                                    {{ $item->NO_CONT }}
                                </div>

                            </td>

                            <td class="px-4 py-4 text-zinc-700 dark:text-zinc-300">
                                {{ $item->NO_SPK ?? '-' }}
                            </td>

                            <td class="px-4 py-4 text-zinc-700 dark:text-zinc-300">
                                {{ $item->NO_DOK ?? '-' }}
                            </td>

                            <td class="px-4 py-4 text-zinc-700 dark:text-zinc-300">
                                {{ $item->TGL_DOK ?? '-' }}
                            </td>

                            <td class="px-4 py-4 text-zinc-700 dark:text-zinc-300">
                                {{ $item->JNS_DOK_NAMA ?? '-' }}
                            </td>

                            <td class="px-4 py-4">

                                <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                                    {{ $warna }}
                                </span>

                            </td>

                            <td class="px-4 py-4 text-right">

                                <button
                                    type="button"
                                    class="hold-release-button inline-flex items-center gap-2 rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-red-700"
                                    data-id="{{ $item->ID }}"
                                    data-no-cont="{{ $item->NO_CONT }}"
                                    data-no-spk="{{ $item->NO_SPK ?? '' }}"
                                >

                                    <flux:icon
                                        name="arrow-uturn-left"
                                        class="size-4"
                                    />

                                    RELEASE

                                </button>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="8"
                                class="px-4 py-12 text-center"
                            >

                                <flux:icon
                                    name="archive-box-x-mark"
                                    class="mx-auto size-8 text-zinc-400"
                                />

                                <div class="mt-3 text-sm font-medium text-zinc-600 dark:text-zinc-300">
                                    Belum ada container HOLD.
                                </div>

                                <div class="mt-1 text-xs text-zinc-400">
                                    Container yang di-HOLD akan muncul di sini.
                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    <div class="space-y-3 md:hidden">

        @forelse($items as $item)

            @php
                $warna = match (
                    strtoupper((string) ($item->FL_WARNA_HOLD ?? ''))
                ) {
                    'N' => 'PUTIH',
                    'M' => 'MERAH',
                    'T' => 'TIMAH',
                    default => '-',
                };
            @endphp

            <div class="rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">

                <div class="flex items-start justify-between gap-3">

                    <div>

                        <div class="text-base font-bold text-zinc-900 dark:text-white">
                            {{ $item->NO_CONT }}
                        </div>

                        <div class="mt-1 text-xs text-zinc-500">
                            {{ $item->NO_SPK ?? '-' }}
                        </div>

                    </div>

                    <span class="shrink-0 rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">
                        HOLD
                    </span>

                </div>

                <div class="mt-4 grid grid-cols-2 gap-3 text-xs">

                    <div>

                        <div class="text-zinc-400">
                            NO DOK
                        </div>

                        <div class="mt-1 font-medium text-zinc-700 dark:text-zinc-300">
                            {{ $item->NO_DOK ?? '-' }}
                        </div>

                    </div>

                    <div>

                        <div class="text-zinc-400">
                            TGL DOK
                        </div>

                        <div class="mt-1 font-medium text-zinc-700 dark:text-zinc-300">
                            {{ $item->TGL_DOK ?? '-' }}
                        </div>

                    </div>

                    <div class="col-span-2">

                        <div class="text-zinc-400">
                            JENIS DOK
                        </div>

                        <div class="mt-1 font-medium text-zinc-700 dark:text-zinc-300">
                            {{ $item->JNS_DOK_NAMA ?? '-' }}
                        </div>

                    </div>

                    <div>

                        <div class="text-zinc-400">
                            WARNA
                        </div>

                        <div class="mt-1 font-semibold text-zinc-700 dark:text-zinc-300">
                            {{ $warna }}
                        </div>

                    </div>

                </div>

                <div class="mt-4">

                    <button
                        type="button"
                        class="hold-release-button flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-red-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-red-700"
                        data-id="{{ $item->ID }}"
                        data-no-cont="{{ $item->NO_CONT }}"
                        data-no-spk="{{ $item->NO_SPK ?? '' }}"
                    >

                        <flux:icon
                            name="arrow-uturn-left"
                            class="size-4"
                        />

                        RELEASE CONTAINER

                    </button>

                </div>

            </div>

        @empty

            <div class="rounded-2xl border border-dashed border-zinc-300 bg-white p-8 text-center dark:border-zinc-700 dark:bg-zinc-900">

                <flux:icon
                    name="archive-box-x-mark"
                    class="mx-auto size-8 text-zinc-400"
                />

                <p class="mt-3 text-sm text-zinc-500">
                    Belum ada container HOLD.
                </p>

            </div>

        @endforelse

    </div>

    @if(($status ?? 0) === 2)

        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-900/50 dark:bg-emerald-900/20">

            <div class="flex items-start gap-3">

                <flux:icon
                    name="check-circle"
                    class="mt-0.5 size-5 shrink-0 text-emerald-600"
                />

                <div>

                    <div class="text-sm font-semibold text-emerald-800 dark:text-emerald-300">
                        Container siap di-HOLD
                    </div>

                    <div class="mt-1 text-xs text-emerald-700 dark:text-emerald-400">
                        Pilih tombol HOLD pada data container di atas.
                    </div>

                </div>

            </div>

        </div>

    @endif

    <div
        id="hold-modal"
        class="fixed inset-0 z-50 hidden"
    >

        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

        <div class="relative flex min-h-full items-center justify-center p-4">

            <div class="w-full max-w-md rounded-2xl bg-white p-5 shadow-xl dark:bg-zinc-900">

                <div class="flex items-start justify-between">

                    <div>

                        <h3 class="text-base font-semibold text-zinc-900 dark:text-white">
                            HOLD Container
                        </h3>

                        <p class="mt-1 text-xs text-zinc-500">
                            Pilih warna HOLD.
                        </p>

                    </div>

                    <button
                        type="button"
                        id="hold-modal-close"
                        class="rounded-lg p-2 text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800"
                    >

                        <flux:icon
                            name="x-mark"
                            class="size-5"
                        />

                    </button>

                </div>

                <div class="mt-5 rounded-xl bg-zinc-50 p-4 dark:bg-zinc-800">

                    <div class="text-xs text-zinc-500">
                        Container
                    </div>

                    <div
                        id="hold-modal-container"
                        class="mt-1 text-lg font-bold text-zinc-900 dark:text-white"
                    >
                        -
                    </div>

                </div>

                <div class="mt-5 space-y-3">

                    <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">

                        <input
                            type="radio"
                            name="hold_warna"
                            value="N"
                            checked
                            class="size-4"
                        >

                        <div>

                            <div class="text-sm font-semibold text-zinc-900 dark:text-white">
                                PUTIH
                            </div>

                            <div class="text-xs text-zinc-500">
                                Kode N
                            </div>

                        </div>

                    </label>

                    <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">

                        <input
                            type="radio"
                            name="hold_warna"
                            value="M"
                            class="size-4"
                        >

                        <div>

                            <div class="text-sm font-semibold text-zinc-900 dark:text-white">
                                MERAH
                            </div>

                            <div class="text-xs text-zinc-500">
                                Kode M
                            </div>

                        </div>

                    </label>

                    <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">

                        <input
                            type="radio"
                            name="hold_warna"
                            value="T"
                            class="size-4"
                        >

                        <div>

                            <div class="text-sm font-semibold text-zinc-900 dark:text-white">
                                TIMAH
                            </div>

                            <div class="text-xs text-zinc-500">
                                Kode T
                            </div>

                        </div>

                    </label>

                </div>

                <div class="mt-5 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">

                    <button
                        type="button"
                        id="hold-modal-cancel"
                        class="min-h-11 rounded-xl border border-zinc-300 px-4 py-2 text-sm font-semibold text-zinc-700 dark:border-zinc-600 dark:text-zinc-300"
                    >
                        Batal
                    </button>

                    <button
                        type="button"
                        id="hold-modal-submit"
                        class="min-h-11 rounded-xl bg-amber-500 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-600"
                    >
                        HOLD CONTAINER
                    </button>

                </div>

            </div>

        </div>

    </div>

    <div
        id="release-modal"
        class="fixed inset-0 z-50 hidden"
    >

        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

        <div class="relative flex min-h-full items-center justify-center p-4">

            <div class="w-full max-w-md rounded-2xl bg-white p-5 shadow-xl dark:bg-zinc-900">

                <div class="flex items-start justify-between">

                    <div>

                        <h3 class="text-base font-semibold text-zinc-900 dark:text-white">
                            RELEASE Container
                        </h3>

                        <p class="mt-1 text-xs text-zinc-500">
                            Konfirmasi release container.
                        </p>

                    </div>

                    <button
                        type="button"
                        id="release-modal-close"
                        class="rounded-lg p-2 text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800"
                    >

                        <flux:icon
                            name="x-mark"
                            class="size-5"
                        />

                    </button>

                </div>

                <div class="mt-5 rounded-xl bg-red-50 p-4 dark:bg-red-900/20">

                    <div class="text-xs text-red-600 dark:text-red-400">
                        Container
                    </div>

                    <div
                        id="release-modal-container"
                        class="mt-1 text-lg font-bold text-red-700 dark:text-red-400"
                    >
                        -
                    </div>

                </div>

                <div class="mt-5 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">

                    <button
                        type="button"
                        id="release-modal-cancel"
                        class="min-h-11 rounded-xl border border-zinc-300 px-4 py-2 text-sm font-semibold text-zinc-700 dark:border-zinc-600 dark:text-zinc-300"
                    >
                        Batal
                    </button>

                    <button
                        type="button"
                        id="release-modal-submit"
                        class="min-h-11 rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700"
                    >
                        RELEASE
                    </button>

                </div>

            </div>

        </div>

    </div>

</div>