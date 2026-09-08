```blade
<div class="min-h-screen bg-slate-50 text-slate-950 dark:bg-slate-950 dark:text-white">

    <div class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 lg:px-8">

        {{-- ============================================================
             PAGE HEADER
        ============================================================= --}}
        <div class="mb-6 flex flex-col gap-1">
            <h1 class="text-xl font-bold tracking-tight sm:text-2xl">
                Dashboard
            </h1>

            <p class="text-sm text-slate-500 dark:text-slate-400">
                Monitoring dan akses proses operasional handheld.
            </p>
        </div>


        {{-- ============================================================
             SUMMARY PROCESS
        ============================================================= --}}
        <section class="mb-8">

            <div class="mb-4">
                <h2 class="text-base font-bold sm:text-lg">
                    Summary Process
                </h2>

                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400 sm:text-sm">
                    Ringkasan container berdasarkan proses operasional
                </p>
            </div>


            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

                {{-- WAITING PICKUP --}}
                <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-slate-900">
                    <div class="p-5">
                        <div class="flex items-start justify-between gap-3">

                            <div>
                                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
                                    Waiting Pickup
                                </p>

                                <p class="mt-2 text-3xl font-bold tabular-nums">
                                    {{ $countWaitingPickupCont->TOTAL_PICKUP ?? 0 }}
                                </p>
                            </div>

                            <span class="flex size-11 shrink-0 items-center justify-center rounded-lg bg-sky-50 text-sky-700 dark:bg-sky-400/10 dark:text-sky-300">
                                <flux:icon.truck class="size-6" />
                            </span>

                        </div>

                        <div class="mt-4 flex items-center gap-2">
                            <span class="size-2 rounded-full bg-sky-500"></span>

                            <span class="text-xs text-slate-500 dark:text-slate-400">
                                Container menunggu pickup
                            </span>
                        </div>
                    </div>
                </div>


                {{-- WAITING INSPECTION --}}
                <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-slate-900">
                    <div class="p-5">
                        <div class="flex items-start justify-between gap-3">

                            <div>
                                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
                                    Waiting Inspection
                                </p>

                                <p class="mt-2 text-3xl font-bold tabular-nums">
                                    {{ $countWaitingInspectionCont->TOTAL_WAITING ?? 0 }}
                                </p>
                            </div>

                            <span class="flex size-11 shrink-0 items-center justify-center rounded-lg bg-amber-50 text-amber-700 dark:bg-amber-400/10 dark:text-amber-300">
                                <flux:icon.clock class="size-6" />
                            </span>

                        </div>

                        <div class="mt-4 flex items-center gap-2">
                            <span class="size-2 rounded-full bg-amber-500"></span>

                            <span class="text-xs text-slate-500 dark:text-slate-400">
                                Container menunggu inspection
                            </span>
                        </div>
                    </div>
                </div>


                {{-- ON INSPECTION --}}
                <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-slate-900">
                    <div class="p-5">
                        <div class="flex items-start justify-between gap-3">

                            <div>
                                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
                                    On Inspection
                                </p>

                                <p class="mt-2 text-3xl font-bold tabular-nums">
                                    {{ $countOnInspectionCont->TOTAL_PROCESS ?? 0 }}
                                </p>
                            </div>

                            <span class="flex size-11 shrink-0 items-center justify-center rounded-lg bg-sky-50 text-sky-700 dark:bg-sky-400/10 dark:text-sky-300">
                                <flux:icon.arrow-path class="size-6" />
                            </span>

                        </div>

                        <div class="mt-4 flex items-center gap-2">
                            <span class="size-2 rounded-full bg-sky-500"></span>

                            <span class="text-xs text-slate-500 dark:text-slate-400">
                                Container sedang inspection
                            </span>
                        </div>
                    </div>
                </div>


                {{-- INSPECTION DONE --}}
                <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-slate-900">
                    <div class="p-5">
                        <div class="flex items-start justify-between gap-3">

                            <div>
                                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
                                    Inspection Done
                                </p>

                                <p class="mt-2 text-3xl font-bold tabular-nums">
                                    {{ $countInspectionDoneCont->TOTAL_PROCESS_DONE ?? 0 }}
                                </p>
                            </div>

                            <span class="flex size-11 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300">
                                <flux:icon.check-circle class="size-6" />
                            </span>

                        </div>

                        <div class="mt-4 flex items-center gap-2">
                            <span class="size-2 rounded-full bg-emerald-500"></span>

                            <span class="text-xs text-slate-500 dark:text-slate-400">
                                Inspection telah selesai
                            </span>
                        </div>
                    </div>
                </div>

            </div>

        </section>


        {{-- ============================================================
             MENU HANDHELD
        ============================================================= --}}
        <section>

            <div class="mb-4 flex items-center justify-between gap-3">

                <div class="min-w-0">
                    <h2 class="text-base font-bold sm:text-lg">
                        Menu Handheld
                    </h2>

                    <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400 sm:text-sm">
                        Akses operasi utama lapangan
                    </p>
                </div>

                <div class="flex shrink-0 items-center gap-2">

                    <span class="flex size-10 shrink-0 items-center justify-center rounded-md bg-sky-100 text-sky-700 dark:bg-sky-400/10 dark:text-sky-200">
                        <flux:icon.squares-2x2 class="size-5" />
                    </span>

                    <button
                        type="button"
                        wire:click="$refresh"
                        data-reload-handheld
                        class="flex size-10 shrink-0 items-center justify-center rounded-md border border-slate-200 text-slate-600 transition hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-sky-500 disabled:cursor-not-allowed disabled:opacity-70 dark:border-white/10 dark:text-slate-200 dark:hover:bg-white/10"
                        aria-label="Muat ulang data handheld"
                    >
                        <flux:icon.arrow-path class="size-5" />
                    </button>

                </div>

            </div>


            {{-- ========================================================
                 GRID MENU
            ========================================================= --}}
            <div class="mx-auto grid w-full max-w-5xl grid-cols-3 gap-3 lg:grid-cols-6 lg:gap-4">

                {{-- ====================================================
                     PICKUP
                ===================================================== --}}
                <a
                    href="{{ route('pickup.index') }}"
                    wire:navigate
                    class="group relative flex aspect-square min-h-20 flex-col items-center justify-center gap-2 rounded-lg border border-sky-200 bg-sky-50 p-3 text-center text-sky-900 shadow-sm transition hover:-translate-y-0.5 hover:border-sky-300 hover:bg-sky-100 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 focus:ring-offset-white sm:min-h-24 lg:aspect-auto lg:h-28 lg:min-h-0 dark:border-sky-400/20 dark:bg-sky-400/10 dark:text-sky-100 dark:hover:bg-sky-400/15 dark:focus:ring-offset-slate-900"
                >

                    <span class="flex size-10 items-center justify-center rounded-md bg-sky-700 text-white shadow-sm transition group-hover:bg-sky-800">
                        <flux:icon.truck class="size-5" />
                    </span>

                    <flux:icon.arrow-right class="absolute right-2 top-2 size-4 text-sky-600 dark:text-sky-300" />

                    <span class="text-xs font-bold uppercase leading-tight sm:text-sm">
                        Pickup
                    </span>

                </a>


                {{-- BEHANDLE IN --}}
                <a
                    href="#"
                    class="group flex aspect-square min-h-20 flex-col items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white p-3 text-center text-slate-800 shadow-sm transition hover:-translate-y-0.5 hover:border-sky-200 hover:bg-sky-50 hover:text-sky-900 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 focus:ring-offset-white sm:min-h-24 lg:aspect-auto lg:h-28 lg:min-h-0 dark:border-white/10 dark:bg-white/5 dark:text-slate-100 dark:hover:bg-sky-400/10 dark:hover:text-sky-100 dark:focus:ring-offset-slate-900"
                >

                    <span class="flex size-10 items-center justify-center rounded-md bg-slate-100 text-sky-700 transition group-hover:bg-sky-700 group-hover:text-white dark:bg-white/10 dark:text-sky-200">
                        <flux:icon.clipboard-document-check class="size-5" />
                    </span>

                    <flux:icon.arrow-right class="size-4 text-sky-500 dark:text-sky-300" />

                    <span class="text-xs font-bold uppercase leading-tight sm:text-sm">
                        Behandle In
                    </span>

                </a>


                {{-- HOLD --}}
                <a
                    href="#"
                    class="group flex aspect-square min-h-20 flex-col items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white p-3 text-center text-slate-800 shadow-sm transition hover:-translate-y-0.5 hover:border-sky-200 hover:bg-sky-50 hover:text-sky-900 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 focus:ring-offset-white sm:min-h-24 lg:aspect-auto lg:h-28 lg:min-h-0 dark:border-white/10 dark:bg-white/5 dark:text-slate-100 dark:hover:bg-sky-400/10 dark:hover:text-sky-100 dark:focus:ring-offset-slate-900"
                >

                    <span class="flex size-10 items-center justify-center rounded-md bg-slate-100 text-amber-700 transition group-hover:bg-amber-500 group-hover:text-white dark:bg-white/10 dark:text-amber-200">
                        <flux:icon.pause-circle class="size-5" />
                    </span>

                    <flux:icon.arrow-right class="size-4 text-sky-500 dark:text-sky-300" />

                    <span class="text-xs font-bold uppercase leading-tight sm:text-sm">
                        Hold
                    </span>

                </a>


                {{-- MARSHALLING CIC --}}
                <a
                    href="#"
                    class="group relative flex aspect-square min-h-20 flex-col items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white p-3 text-center text-slate-800 shadow-sm transition hover:-translate-y-0.5 hover:border-sky-200 hover:bg-sky-50 hover:text-sky-900 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 focus:ring-offset-white sm:min-h-24 lg:aspect-auto lg:h-28 lg:min-h-0 dark:border-white/10 dark:bg-white/5 dark:text-slate-100 dark:hover:bg-sky-400/10 dark:hover:text-sky-100 dark:focus:ring-offset-slate-900"
                >

                    <span class="absolute right-2 top-2 rounded-sm bg-amber-300 px-2 py-0.5 text-xs font-bold text-slate-950">
                        55
                    </span>

                    <span class="flex size-10 items-center justify-center rounded-md bg-slate-100 text-emerald-700 transition group-hover:bg-emerald-600 group-hover:text-white dark:bg-white/10 dark:text-emerald-200">
                        <flux:icon.map-pin class="size-5" />
                    </span>

                    <span class="text-xs font-bold uppercase leading-tight sm:text-sm">
                        Marshalling CIC
                    </span>

                </a>


                {{-- MARSHALLING YARD --}}
                <a
                    href="#"
                    class="group relative flex aspect-square min-h-20 flex-col items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white p-3 text-center text-slate-800 shadow-sm transition hover:-translate-y-0.5 hover:border-sky-200 hover:bg-sky-50 hover:text-sky-900 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 focus:ring-offset-white sm:min-h-24 lg:aspect-auto lg:h-28 lg:min-h-0 dark:border-white/10 dark:bg-white/5 dark:text-slate-100 dark:hover:bg-sky-400/10 dark:hover:text-sky-100 dark:focus:ring-offset-slate-900"
                >

                    <span class="absolute right-2 top-2 rounded-sm bg-amber-300 px-2 py-0.5 text-xs font-bold text-slate-950">
                        2
                    </span>

                    <span class="flex size-10 items-center justify-center rounded-md bg-slate-100 text-emerald-700 transition group-hover:bg-emerald-600 group-hover:text-white dark:bg-white/10 dark:text-emerald-200">
                        <flux:icon.map-pin class="size-5" />
                    </span>

                    <span class="text-xs font-bold uppercase leading-tight sm:text-sm">
                        Marshalling Yard
                    </span>

                </a>


                {{-- PEMERIKSAAN BEHANDLE --}}
                <a
                    href="#"
                    class="group flex aspect-square min-h-20 flex-col items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white p-3 text-center text-slate-800 shadow-sm transition hover:-translate-y-0.5 hover:border-sky-200 hover:bg-sky-50 hover:text-sky-900 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 focus:ring-offset-white sm:min-h-24 lg:aspect-auto lg:h-28 lg:min-h-0 dark:border-white/10 dark:bg-white/5 dark:text-slate-100 dark:hover:bg-sky-400/10 dark:hover:text-sky-100 dark:focus:ring-offset-slate-900"
                >

                    <span class="flex size-10 items-center justify-center rounded-md bg-slate-100 text-sky-700 transition group-hover:bg-sky-700 group-hover:text-white dark:bg-white/10 dark:text-sky-200">
                        <flux:icon.document-magnifying-glass class="size-5" />
                    </span>

                    <flux:icon.arrow-right class="size-4 text-sky-500 dark:text-sky-300" />

                    <span class="text-xs font-bold uppercase leading-tight sm:text-sm">
                        Pemeriksaan Behandle
                    </span>

                </a>


                {{-- PLUG REEFER --}}
                <a
                    href="#"
                    class="group flex aspect-square min-h-20 flex-col items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white p-3 text-center text-slate-800 shadow-sm transition hover:-translate-y-0.5 hover:border-sky-200 hover:bg-sky-50 hover:text-sky-900 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 focus:ring-offset-white sm:min-h-24 lg:aspect-auto lg:h-28 lg:min-h-0 dark:border-white/10 dark:bg-white/5 dark:text-slate-100 dark:hover:bg-sky-400/10 dark:hover:text-sky-100 dark:focus:ring-offset-slate-900"
                >

                    <span class="flex size-10 items-center justify-center rounded-md bg-slate-100 text-yellow-700 transition group-hover:bg-yellow-500 group-hover:text-white dark:bg-white/10 dark:text-yellow-200">
                        <flux:icon.bolt class="size-5" />
                    </span>

                    <flux:icon.arrow-right class="size-4 text-sky-500 dark:text-sky-300" />

                    <span class="text-xs font-bold uppercase leading-tight sm:text-sm">
                        Plug Reefer
                    </span>

                </a>


                {{-- MONITORING REEFER --}}
                <a
                    href="#"
                    class="group flex aspect-square min-h-20 flex-col items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white p-3 text-center text-slate-800 shadow-sm transition hover:-translate-y-0.5 hover:border-sky-200 hover:bg-sky-50 hover:text-sky-900 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 focus:ring-offset-white sm:min-h-24 lg:aspect-auto lg:h-28 lg:min-h-0 dark:border-white/10 dark:bg-white/5 dark:text-slate-100 dark:hover:bg-sky-400/10 dark:hover:text-sky-100 dark:focus:ring-offset-slate-900"
                >

                    <span class="flex size-10 items-center justify-center rounded-md bg-slate-100 text-cyan-700 transition group-hover:bg-cyan-600 group-hover:text-white dark:bg-white/10 dark:text-cyan-200">
                        <flux:icon.adjustments-horizontal class="size-5" />
                    </span>

                    <flux:icon.arrow-right class="size-4 text-sky-500 dark:text-sky-300" />

                    <span class="text-xs font-bold uppercase leading-tight sm:text-sm">
                        Monitoring Reefer
                    </span>

                </a>


                {{-- DELIVERY --}}
                <a
                    href="#"
                    class="group flex aspect-square min-h-20 flex-col items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white p-3 text-center text-slate-800 shadow-sm transition hover:-translate-y-0.5 hover:border-sky-200 hover:bg-sky-50 hover:text-sky-900 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 focus:ring-offset-white sm:min-h-24 lg:aspect-auto lg:h-28 lg:min-h-0 dark:border-white/10 dark:bg-white/5 dark:text-slate-100 dark:hover:bg-sky-400/10 dark:hover:text-sky-100 dark:focus:ring-offset-slate-900"
                >

                    <span class="flex size-10 items-center justify-center rounded-md bg-slate-100 text-indigo-700 transition group-hover:bg-indigo-600 group-hover:text-white dark:bg-white/10 dark:text-indigo-200">
                        <flux:icon.arrow-right-circle class="size-5" />
                    </span>

                    <flux:icon.arrow-right class="size-4 text-sky-500 dark:text-sky-300" />

                    <span class="text-xs font-bold uppercase leading-tight sm:text-sm">
                        Delivery
                    </span>

                </a>


                {{-- INSPECTION OUT --}}
                <a
                    href="#"
                    class="group flex aspect-square min-h-20 flex-col items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white p-3 text-center text-slate-800 shadow-sm transition hover:-translate-y-0.5 hover:border-sky-200 hover:bg-sky-50 hover:text-sky-900 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 focus:ring-offset-white sm:min-h-24 lg:aspect-auto lg:h-28 lg:min-h-0 dark:border-white/10 dark:bg-white/5 dark:text-slate-100 dark:hover:bg-sky-400/10 dark:hover:text-sky-100 dark:focus:ring-offset-slate-900"
                >

                    <span class="flex size-10 items-center justify-center rounded-md bg-slate-100 text-emerald-700 transition group-hover:bg-emerald-600 group-hover:text-white dark:bg-white/10 dark:text-emerald-200">
                        <flux:icon.check-circle class="size-5" />
                    </span>

                    <flux:icon.arrow-right class="size-4 text-sky-500 dark:text-sky-300" />

                    <span class="text-xs font-bold uppercase leading-tight sm:text-sm">
                        Inspection Out
                    </span>

                </a>


                {{-- ON CHASSIS --}}
                <a
                    href="#"
                    class="group relative flex aspect-square min-h-20 flex-col items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white p-3 text-center text-slate-800 shadow-sm transition hover:-translate-y-0.5 hover:border-sky-200 hover:bg-sky-50 hover:text-sky-900 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 focus:ring-offset-white sm:min-h-24 lg:aspect-auto lg:h-28 lg:min-h-0 dark:border-white/10 dark:bg-white/5 dark:text-slate-100 dark:hover:bg-sky-400/10 dark:hover:text-sky-100 dark:focus:ring-offset-slate-900"
                >

                    <span class="absolute right-2 top-2 rounded-sm bg-amber-300 px-2 py-0.5 text-xs font-bold text-slate-950">
                        6
                    </span>

                    <span class="flex size-10 items-center justify-center rounded-md bg-slate-100 text-rose-700 transition group-hover:bg-rose-600 group-hover:text-white dark:bg-white/10 dark:text-rose-200">
                        <flux:icon.wrench-screwdriver class="size-5" />
                    </span>

                    <span class="text-xs font-bold uppercase leading-tight sm:text-sm">
                        On Chassis
                    </span>

                </a>


                {{-- COPY YARD --}}
                <a
                    href="#"
                    class="group flex aspect-square min-h-20 flex-col items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white p-3 text-center text-slate-800 shadow-sm transition hover:-translate-y-0.5 hover:border-sky-200 hover:bg-sky-50 hover:text-sky-900 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 focus:ring-offset-white sm:min-h-24 lg:aspect-auto lg:h-28 lg:min-h-0 dark:border-white/10 dark:bg-white/5 dark:text-slate-100 dark:hover:bg-sky-400/10 dark:hover:text-sky-100 dark:focus:ring-offset-slate-900"
                >

                    <span class="flex size-10 items-center justify-center rounded-md bg-slate-100 text-violet-700 transition group-hover:bg-violet-600 group-hover:text-white dark:bg-white/10 dark:text-violet-200">
                        <flux:icon.archive-box class="size-5" />
                    </span>

                    <flux:icon.arrow-right class="size-4 text-sky-500 dark:text-sky-300" />

                    <span class="text-xs font-bold uppercase leading-tight sm:text-sm">
                        Copyyard
                    </span>

                </a>

            </div>

        </section>

    </div>

</div>
```
