<div>
    @php
        $userName = auth()->user()->name ?? 'Operator';
        $firstName = str($userName)->before(' ')->toString();
        $currentTime = now('Asia/Jakarta');
        $currentMinutes = $currentTime->hour * 60 + $currentTime->minute;
        $shifts = [
            [
                'name' => 'Shift Pagi',
                'range' => '07:00 sampai 15:00 WIB',
                'starts_at' => 7 * 60,
                'ends_at' => 15 * 60,
            ],
            [
                'name' => 'Shift Sore',
                'range' => '15:00 sampai 23:00 WIB',
                'starts_at' => 15 * 60,
                'ends_at' => 23 * 60,
            ],
            [
                'name' => 'Shift Malam',
                'range' => '23:00 sampai 07:00 WIB',
                'starts_at' => 23 * 60,
                'ends_at' => 7 * 60,
            ],
        ];
        $activeShift = collect($shifts)->first(function ($shift) use ($currentMinutes) {
            if ($shift['starts_at'] < $shift['ends_at']) {
                return $currentMinutes >= $shift['starts_at'] && $currentMinutes < $shift['ends_at'];
            }

            return $currentMinutes >= $shift['starts_at'] || $currentMinutes < $shift['ends_at'];
        });
        $activePanel = request()->routeIs('pickup.index') ? 'pickup' : null;
    @endphp

    <div class="min-h-[calc(100vh-5rem)] bg-slate-50 pb-24 text-slate-950 dark:bg-slate-950 dark:text-white lg:pb-6">
        <div class="mx-auto flex w-full max-w-7xl flex-col gap-5 px-4 py-4 sm:px-6 lg:px-8">
            <section
                class="rounded-lg border border-sky-900/15 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-slate-900">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-sky-700 dark:text-sky-300">{{ $activeShift['name'] }} - Area
                            Behandle</p>
                        <h1
                            class="mt-1 text-2xl font-semibold leading-tight tracking-normal text-slate-950 dark:text-white">
                            Halo, {{ $userName }}
                        </h1>
                        <p class="mt-1 text-sm leading-6 text-slate-600 dark:text-slate-300">
                            {{ $currentTime->translatedFormat('l, d M Y') }} - {{ $activeShift['range'] }}
                        </p>
                    </div>

                    <div
                        class="flex shrink-0 items-center gap-2 rounded-md border border-emerald-200 bg-emerald-50 px-3 py-2 text-emerald-800 dark:border-emerald-400/20 dark:bg-emerald-400/10 dark:text-emerald-200">
                        <span class="size-2 rounded-full bg-emerald-500"></span>
                        <span class="text-xs font-semibold uppercase tracking-wide">Aktif</span>
                    </div>
                </div>

                <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
                    <div class="rounded-md bg-slate-100 p-3 dark:bg-white/5">
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Menunggu Pickup</p>
                        <p class="mt-1 text-2xl font-semibold">{{ $countWaitingPickupCont->TOTAL_PICKUP }}</p>
                    </div>
                    <div class="rounded-md bg-amber-50 p-3 text-amber-900 dark:bg-amber-400/10 dark:text-amber-100">
                        <p class="text-xs font-medium text-amber-700 dark:text-amber-300">Menunggu Periksa</p>
                        <p class="mt-1 text-2xl font-semibold">{{ $countWaitingInspectionCont->TOTAL_WAITING }}</p>
                    </div>
                    <div class="rounded-md bg-sky-50 p-3 text-sky-900 dark:bg-sky-400/10 dark:text-sky-100">
                        <p class="text-xs font-medium text-sky-700 dark:text-sky-300">Sedang Periksa</p>
                        <p class="mt-1 text-2xl font-semibold">{{ $countOnInspectionCont->TOTAL_PROCESS }}</p>
                    </div>
                    <div
                        class="rounded-md bg-emerald-50 p-3 text-emerald-900 dark:bg-emerald-400/10 dark:text-emerald-100">
                        <p class="text-xs font-medium text-emerald-700 dark:text-emerald-300">Selesai Periksa</p>
                        <p class="mt-1 text-2xl font-semibold">{{ $countInspectionDoneCont->TOTAL_PROCESS_DONE }}</p>
                    </div>
                </div>
            </section>

            <section
                class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-slate-900">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-semibold">Menu Handheld</h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Akses operasi utama lapangan</p>
                    </div>
                    <button type="button" wire:click="$refresh" data-reload-handheld
                        class="flex size-10 shrink-0 items-center justify-center rounded-md border border-slate-200 text-slate-600 transition hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-sky-500 disabled:cursor-not-allowed disabled:opacity-70 dark:border-white/10 dark:text-slate-200 dark:hover:bg-white/10"
                        aria-label="Muat ulang data handheld">
                        <flux:icon.arrow-path class="size-5" />
                    </button>
                </div>

                <div @class(['mx-auto grid w-full max-w-5xl grid-cols-3 gap-3 lg:grid-cols-6 lg:gap-4', 'hidden' => $activePanel]) data-handheld-menu>
                    <a href="{{ route('pickup.index') }}" wire:navigate data-open-pickup
                        class="group relative flex aspect-square min-h-20 flex-col items-center justify-center gap-2 rounded-lg border border-sky-200 bg-sky-50 p-3 text-center text-sky-900 shadow-sm transition hover:-translate-y-0.5 hover:border-sky-300 hover:bg-sky-100 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 focus:ring-offset-white sm:min-h-24 lg:aspect-auto lg:h-28 lg:min-h-0 dark:border-sky-400/20 dark:bg-sky-400/10 dark:text-sky-100 dark:hover:bg-sky-400/15 dark:focus:ring-offset-slate-900">
                        <span class="flex size-10 items-center justify-center rounded-md bg-sky-700 text-white shadow-sm transition group-hover:bg-sky-800">
                            <flux:icon.truck class="size-5" />
                        </span>
                        <span class="text-xs font-bold uppercase leading-tight sm:text-sm">Pick Up</span>
                    </a>

                    <a href="#"
                        class="group flex aspect-square min-h-20 flex-col items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white p-3 text-center text-slate-800 shadow-sm transition hover:-translate-y-0.5 hover:border-sky-200 hover:bg-sky-50 hover:text-sky-900 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 focus:ring-offset-white sm:min-h-24 lg:aspect-auto lg:h-28 lg:min-h-0 dark:border-white/10 dark:bg-white/5 dark:text-slate-100 dark:hover:bg-sky-400/10 dark:hover:text-sky-100 dark:focus:ring-offset-slate-900">
                        <span class="flex size-10 items-center justify-center rounded-md bg-slate-100 text-sky-700 transition group-hover:bg-sky-700 group-hover:text-white dark:bg-white/10 dark:text-sky-200">
                            <flux:icon.clipboard-document-check class="size-5" />
                        </span>
                        <span class="text-xs font-bold uppercase leading-tight sm:text-sm">Behandle In</span>
                    </a>

                    <a href="#"
                        class="group flex aspect-square min-h-20 flex-col items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white p-3 text-center text-slate-800 shadow-sm transition hover:-translate-y-0.5 hover:border-sky-200 hover:bg-sky-50 hover:text-sky-900 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 focus:ring-offset-white sm:min-h-24 lg:aspect-auto lg:h-28 lg:min-h-0 dark:border-white/10 dark:bg-white/5 dark:text-slate-100 dark:hover:bg-sky-400/10 dark:hover:text-sky-100 dark:focus:ring-offset-slate-900">
                        <span class="flex size-10 items-center justify-center rounded-md bg-slate-100 text-amber-700 transition group-hover:bg-amber-500 group-hover:text-white dark:bg-white/10 dark:text-amber-200">
                            <flux:icon.pause-circle class="size-5" />
                        </span>
                        <span class="text-xs font-bold uppercase leading-tight sm:text-sm">Hold</span>
                    </a>

                    <a href="#"
                        class="group relative flex aspect-square min-h-20 flex-col items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white p-3 text-center text-slate-800 shadow-sm transition hover:-translate-y-0.5 hover:border-sky-200 hover:bg-sky-50 hover:text-sky-900 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 focus:ring-offset-white sm:min-h-24 lg:aspect-auto lg:h-28 lg:min-h-0 dark:border-white/10 dark:bg-white/5 dark:text-slate-100 dark:hover:bg-sky-400/10 dark:hover:text-sky-100 dark:focus:ring-offset-slate-900">
                        <span class="absolute right-2 top-2 rounded-sm bg-amber-300 px-2 py-0.5 text-xs font-bold text-slate-950">55</span>
                        <span class="flex size-10 items-center justify-center rounded-md bg-slate-100 text-emerald-700 transition group-hover:bg-emerald-600 group-hover:text-white dark:bg-white/10 dark:text-emerald-200">
                            <flux:icon.map-pin class="size-5" />
                        </span>
                        <span class="text-xs font-bold uppercase leading-tight sm:text-sm">Marshalling CIC</span>
                    </a>

                    <a href="#"
                        class="group relative flex aspect-square min-h-20 flex-col items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white p-3 text-center text-slate-800 shadow-sm transition hover:-translate-y-0.5 hover:border-sky-200 hover:bg-sky-50 hover:text-sky-900 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 focus:ring-offset-white sm:min-h-24 lg:aspect-auto lg:h-28 lg:min-h-0 dark:border-white/10 dark:bg-white/5 dark:text-slate-100 dark:hover:bg-sky-400/10 dark:hover:text-sky-100 dark:focus:ring-offset-slate-900">
                        <span class="absolute right-2 top-2 rounded-sm bg-amber-300 px-2 py-0.5 text-xs font-bold text-slate-950">2</span>
                        <span class="flex size-10 items-center justify-center rounded-md bg-slate-100 text-emerald-700 transition group-hover:bg-emerald-600 group-hover:text-white dark:bg-white/10 dark:text-emerald-200">
                            <flux:icon.map-pin class="size-5" />
                        </span>
                        <span class="text-xs font-bold uppercase leading-tight sm:text-sm">Marshalling Yard</span>
                    </a>

                    <a href="#"
                        class="group flex aspect-square min-h-20 flex-col items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white p-3 text-center text-slate-800 shadow-sm transition hover:-translate-y-0.5 hover:border-sky-200 hover:bg-sky-50 hover:text-sky-900 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 focus:ring-offset-white sm:min-h-24 lg:aspect-auto lg:h-28 lg:min-h-0 dark:border-white/10 dark:bg-white/5 dark:text-slate-100 dark:hover:bg-sky-400/10 dark:hover:text-sky-100 dark:focus:ring-offset-slate-900">
                        <span class="flex size-10 items-center justify-center rounded-md bg-slate-100 text-sky-700 transition group-hover:bg-sky-700 group-hover:text-white dark:bg-white/10 dark:text-sky-200">
                            <flux:icon.document-magnifying-glass class="size-5" />
                        </span>
                        <span class="text-xs font-bold uppercase leading-tight sm:text-sm">Pemeriksaan Behandle</span>
                    </a>

                    <a href="#"
                        class="group flex aspect-square min-h-20 flex-col items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white p-3 text-center text-slate-800 shadow-sm transition hover:-translate-y-0.5 hover:border-sky-200 hover:bg-sky-50 hover:text-sky-900 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 focus:ring-offset-white sm:min-h-24 lg:aspect-auto lg:h-28 lg:min-h-0 dark:border-white/10 dark:bg-white/5 dark:text-slate-100 dark:hover:bg-sky-400/10 dark:hover:text-sky-100 dark:focus:ring-offset-slate-900">
                        <span class="flex size-10 items-center justify-center rounded-md bg-slate-100 text-yellow-700 transition group-hover:bg-yellow-500 group-hover:text-white dark:bg-white/10 dark:text-yellow-200">
                            <flux:icon.bolt class="size-5" />
                        </span>
                        <span class="text-xs font-bold uppercase leading-tight sm:text-sm">Plug Reefer</span>
                    </a>

                    <a href="#"
                        class="group flex aspect-square min-h-20 flex-col items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white p-3 text-center text-slate-800 shadow-sm transition hover:-translate-y-0.5 hover:border-sky-200 hover:bg-sky-50 hover:text-sky-900 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 focus:ring-offset-white sm:min-h-24 lg:aspect-auto lg:h-28 lg:min-h-0 dark:border-white/10 dark:bg-white/5 dark:text-slate-100 dark:hover:bg-sky-400/10 dark:hover:text-sky-100 dark:focus:ring-offset-slate-900">
                        <span class="flex size-10 items-center justify-center rounded-md bg-slate-100 text-cyan-700 transition group-hover:bg-cyan-600 group-hover:text-white dark:bg-white/10 dark:text-cyan-200">
                            <flux:icon.adjustments-horizontal class="size-5" />
                        </span>
                        <span class="text-xs font-bold uppercase leading-tight sm:text-sm">Monitoring Reefer</span>
                    </a>

                    <a href="#"
                        class="group flex aspect-square min-h-20 flex-col items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white p-3 text-center text-slate-800 shadow-sm transition hover:-translate-y-0.5 hover:border-sky-200 hover:bg-sky-50 hover:text-sky-900 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 focus:ring-offset-white sm:min-h-24 lg:aspect-auto lg:h-28 lg:min-h-0 dark:border-white/10 dark:bg-white/5 dark:text-slate-100 dark:hover:bg-sky-400/10 dark:hover:text-sky-100 dark:focus:ring-offset-slate-900">
                        <span class="flex size-10 items-center justify-center rounded-md bg-slate-100 text-indigo-700 transition group-hover:bg-indigo-600 group-hover:text-white dark:bg-white/10 dark:text-indigo-200">
                            <flux:icon.arrow-right-circle class="size-5" />
                        </span>
                        <span class="text-xs font-bold uppercase leading-tight sm:text-sm">Delivery</span>
                    </a>

                    <a href="#"
                        class="group flex aspect-square min-h-20 flex-col items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white p-3 text-center text-slate-800 shadow-sm transition hover:-translate-y-0.5 hover:border-sky-200 hover:bg-sky-50 hover:text-sky-900 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 focus:ring-offset-white sm:min-h-24 lg:aspect-auto lg:h-28 lg:min-h-0 dark:border-white/10 dark:bg-white/5 dark:text-slate-100 dark:hover:bg-sky-400/10 dark:hover:text-sky-100 dark:focus:ring-offset-slate-900">
                        <span class="flex size-10 items-center justify-center rounded-md bg-slate-100 text-emerald-700 transition group-hover:bg-emerald-600 group-hover:text-white dark:bg-white/10 dark:text-emerald-200">
                            <flux:icon.check-circle class="size-5" />
                        </span>
                        <span class="text-xs font-bold uppercase leading-tight sm:text-sm">Inspection Out</span>
                    </a>

                    <a href="#"
                        class="group relative flex aspect-square min-h-20 flex-col items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white p-3 text-center text-slate-800 shadow-sm transition hover:-translate-y-0.5 hover:border-sky-200 hover:bg-sky-50 hover:text-sky-900 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 focus:ring-offset-white sm:min-h-24 lg:aspect-auto lg:h-28 lg:min-h-0 dark:border-white/10 dark:bg-white/5 dark:text-slate-100 dark:hover:bg-sky-400/10 dark:hover:text-sky-100 dark:focus:ring-offset-slate-900">
                        <span class="absolute right-2 top-2 rounded-sm bg-amber-300 px-2 py-0.5 text-xs font-bold text-slate-950">6</span>
                        <span class="flex size-10 items-center justify-center rounded-md bg-slate-100 text-rose-700 transition group-hover:bg-rose-600 group-hover:text-white dark:bg-white/10 dark:text-rose-200">
                            <flux:icon.wrench-screwdriver class="size-5" />
                        </span>
                        <span class="text-xs font-bold uppercase leading-tight sm:text-sm">On Chassis</span>
                    </a>

                    <a href="#"
                        class="group flex aspect-square min-h-20 flex-col items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white p-3 text-center text-slate-800 shadow-sm transition hover:-translate-y-0.5 hover:border-sky-200 hover:bg-sky-50 hover:text-sky-900 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 focus:ring-offset-white sm:min-h-24 lg:aspect-auto lg:h-28 lg:min-h-0 dark:border-white/10 dark:bg-white/5 dark:text-slate-100 dark:hover:bg-sky-400/10 dark:hover:text-sky-100 dark:focus:ring-offset-slate-900">
                        <span class="flex size-10 items-center justify-center rounded-md bg-slate-100 text-violet-700 transition group-hover:bg-violet-600 group-hover:text-white dark:bg-white/10 dark:text-violet-200">
                            <flux:icon.archive-box class="size-5" />
                        </span>
                        <span class="text-xs font-bold uppercase leading-tight sm:text-sm">Copyyard</span>
                    </a>
                </div>

                <div id="panel">
                    @if ($activePanel === 'pickup')
                        @include('livewire.partials.pickup.pickup-panel')
                    @endif
                </div>
            </section>

            <div class="grid gap-5 xl:grid-cols-[1.45fr_0.9fr]">
                <section
                    class="rounded-lg border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-slate-900">
                    <div
                        class="flex items-center justify-between gap-3 border-b border-slate-200 p-4 dark:border-white/10">
                        <div>
                            <h2 class="text-base font-semibold">Antrian Prioritas</h2>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Pekerjaan terdekat dari posisi
                                operator</p>
                        </div>
                        <button type="button"
                            class="flex size-10 shrink-0 items-center justify-center rounded-md border border-slate-200 text-slate-600 transition hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-sky-500 dark:border-white/10 dark:text-slate-200 dark:hover:bg-white/10"
                            aria-label="Muat ulang antrian">
                            <flux:icon.arrow-path class="size-5" />
                        </button>
                    </div>

                    <div class="divide-y divide-slate-200 dark:divide-white/10">
                        <a href="#"
                            class="grid gap-3 p-4 transition hover:bg-slate-50 dark:hover:bg-white/5 sm:grid-cols-[1fr_auto] sm:items-center">
                            <div class="flex gap-3">
                                <span
                                    class="mt-1 flex size-10 shrink-0 items-center justify-center rounded-md bg-sky-100 text-sky-700 dark:bg-sky-400/10 dark:text-sky-200">
                                    <flux:icon.truck class="size-5" />
                                </span>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="font-semibold">MSKU 812345-6</p>
                                        <span
                                            class="rounded-sm bg-red-100 px-2 py-1 text-xs font-semibold text-red-700 dark:bg-red-400/10 dark:text-red-200">High</span>
                                    </div>
                                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Blok C2 - Jalur 04 -
                                        Dokumen lengkap</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between gap-3 sm:justify-end">
                                <span class="text-sm font-medium text-slate-600 dark:text-slate-300">08:20</span>
                                <span
                                    class="rounded-md bg-sky-700 px-3 py-2 text-sm font-semibold text-white">Ambil</span>
                            </div>
                        </a>

                        <a href="#"
                            class="grid gap-3 p-4 transition hover:bg-slate-50 dark:hover:bg-white/5 sm:grid-cols-[1fr_auto] sm:items-center">
                            <div class="flex gap-3">
                                <span
                                    class="mt-1 flex size-10 shrink-0 items-center justify-center rounded-md bg-slate-100 text-slate-700 dark:bg-white/10 dark:text-slate-200">
                                    <flux:icon.truck class="size-5" />
                                </span>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="font-semibold">TCLU 443210-9</p>
                                        <span
                                            class="rounded-sm bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-600 dark:bg-white/10 dark:text-slate-300">Normal</span>
                                    </div>
                                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Blok B1 - Jalur 02 -
                                        Menunggu segel</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between gap-3 sm:justify-end">
                                <span class="text-sm font-medium text-slate-600 dark:text-slate-300">08:45</span>
                                <span
                                    class="rounded-md border border-slate-200 px-3 py-2 text-sm font-semibold dark:border-white/10">Detail</span>
                            </div>
                        </a>

                        <a href="#"
                            class="grid gap-3 p-4 transition hover:bg-slate-50 dark:hover:bg-white/5 sm:grid-cols-[1fr_auto] sm:items-center">
                            <div class="flex gap-3">
                                <span
                                    class="mt-1 flex size-10 shrink-0 items-center justify-center rounded-md bg-amber-100 text-amber-700 dark:bg-amber-400/10 dark:text-amber-200">
                                    <flux:icon.clock class="size-5" />
                                </span>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="font-semibold">BMOU 772801-3</p>
                                        <span
                                            class="rounded-sm bg-amber-100 px-2 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-400/10 dark:text-amber-200">Tahan</span>
                                    </div>
                                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Blok A4 - Perlu
                                        konfirmasi petugas BC</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between gap-3 sm:justify-end">
                                <span class="text-sm font-medium text-slate-600 dark:text-slate-300">09:10</span>
                                <span
                                    class="rounded-md border border-slate-200 px-3 py-2 text-sm font-semibold dark:border-white/10">Detail</span>
                            </div>
                        </a>
                    </div>
                </section>

                <div class="grid gap-5">
                    <section
                        class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-slate-900">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h2 class="text-base font-semibold">Kondisi Area</h2>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Ringkasan lapangan saat ini
                                </p>
                            </div>
                            <span
                                class="flex size-10 items-center justify-center rounded-md bg-emerald-100 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-200">
                                <flux:icon.map-pin class="size-5" />
                            </span>
                        </div>

                        <div class="mt-4 grid grid-cols-3 gap-2">
                            <div class="rounded-md bg-slate-100 p-3 text-center dark:bg-white/5">
                                <p class="text-lg font-semibold">6</p>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Jalur buka</p>
                            </div>
                            <div class="rounded-md bg-slate-100 p-3 text-center dark:bg-white/5">
                                <p class="text-lg font-semibold">2</p>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Crane aktif</p>
                            </div>
                            <div class="rounded-md bg-slate-100 p-3 text-center dark:bg-white/5">
                                <p class="text-lg font-semibold">14m</p>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Rata tunggu</p>
                            </div>
                        </div>
                    </section>

                    <section
                        class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-slate-900">
                        <div class="flex items-center gap-3">
                            <span
                                class="flex size-10 shrink-0 items-center justify-center rounded-md bg-sky-100 text-sky-700 dark:bg-sky-400/10 dark:text-sky-200">
                                <flux:icon.check-circle class="size-5" />
                            </span>
                            <div>
                                <h2 class="text-base font-semibold">Checklist Operasi</h2>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">3 dari 4 sudah siap</p>
                            </div>
                        </div>

                        <div class="mt-4 space-y-3">
                            <label
                                class="flex min-h-11 items-center gap-3 rounded-md bg-slate-100 px-3 py-2 dark:bg-white/5">
                                <input type="checkbox" checked
                                    class="size-5 rounded border-slate-300 text-sky-700 focus:ring-sky-600">
                                <span class="text-sm font-medium">HT dan jaringan aktif</span>
                            </label>
                            <label
                                class="flex min-h-11 items-center gap-3 rounded-md bg-slate-100 px-3 py-2 dark:bg-white/5">
                                <input type="checkbox" checked
                                    class="size-5 rounded border-slate-300 text-sky-700 focus:ring-sky-600">
                                <span class="text-sm font-medium">APD lengkap</span>
                            </label>
                            <label
                                class="flex min-h-11 items-center gap-3 rounded-md bg-slate-100 px-3 py-2 dark:bg-white/5">
                                <input type="checkbox"
                                    class="size-5 rounded border-slate-300 text-sky-700 focus:ring-sky-600">
                                <span class="text-sm font-medium">Foto awal container</span>
                            </label>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <nav class="fixed inset-x-0 bottom-0 z-40 border-t border-slate-200 bg-white/95 px-3 py-2 shadow-lg backdrop-blur lg:hidden dark:border-white/10 dark:bg-slate-900/95"
        aria-label="Navigasi cepat">
        <div class="mx-auto grid max-w-md grid-cols-4 gap-1">
            <a href="#"
                class="flex min-h-14 flex-col items-center justify-center gap-1 rounded-md bg-sky-50 text-sky-700 dark:bg-sky-400/10 dark:text-sky-200">
                <flux:icon.squares-2x2 class="size-5" />
                <span class="text-xs font-semibold">Home</span>
            </a>
            <a href="#"
                class="flex min-h-14 flex-col items-center justify-center gap-1 rounded-md text-slate-500 dark:text-slate-300">
                <flux:icon.qr-code class="size-5" />
                <span class="text-xs font-semibold">Scan</span>
            </a>
            <a href="#"
                class="flex min-h-14 flex-col items-center justify-center gap-1 rounded-md text-slate-500 dark:text-slate-300">
                <flux:icon.truck class="size-5" />
                <span class="text-xs font-semibold">Job</span>
            </a>
            <a href="#"
                class="flex min-h-14 flex-col items-center justify-center gap-1 rounded-md text-slate-500 dark:text-slate-300">
                <flux:icon.user-group class="size-5" />
                <span class="text-xs font-semibold">Tim</span>
            </a>
        </div>
    </nav>
</div>

@push('scripts')
    @vite('resources/js/pickup.js')
@endpush('scripts')
