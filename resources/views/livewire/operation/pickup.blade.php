<div class="min-h-screen bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-white">

    <div class="mx-auto w-full max-w-6xl px-4 py-6 sm:px-6 lg:px-8">

        <div class="mb-5 flex items-center justify-between gap-3">

            <a
                href="{{ route('dashboard') }}"
                wire:navigate
                class="inline-flex min-h-10 items-center gap-2 rounded-md border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-sky-500 dark:border-white/10 dark:bg-white/5 dark:text-slate-200 dark:hover:bg-white/10"
            >
                <flux:icon.arrow-left class="size-4" />

                <span>
                    Menu Handheld
                </span>
            </a>

            <span
                class="rounded-md bg-sky-100 px-3 py-2 text-xs font-bold uppercase tracking-wide text-sky-700 dark:bg-sky-400/10 dark:text-sky-300"
            >
                Pick Up
            </span>

        </div>


        {{-- SEARCH --}}

        <div
            class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-white/5"
        >

            <label
                for="search-spk"
                class="text-sm font-semibold text-slate-700 dark:text-slate-200"
            >
                Nomor SPK
            </label>

            <div class="mt-2 flex flex-col gap-3 sm:flex-row">

                <div class="relative min-w-0 flex-1">

                    <flux:icon.magnifying-glass
                        class="pointer-events-none absolute left-3 top-1/2 size-5 -translate-y-1/2 text-slate-400"
                    />

                    <input
                        id="search-spk"
                        type="text"
                        wire:model="searchSpk"
                        wire:keydown.enter="search"
                        autocomplete="off"
                        placeholder="SEARCH NO SPK"
                        class="h-12 w-full rounded-md border border-slate-200 bg-white pl-10 pr-3 text-sm font-medium text-slate-950 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 dark:border-white/10 dark:bg-slate-950 dark:text-white"
                    >

                </div>

                <button
                    type="button"
                    wire:click="search"
                    wire:loading.attr="disabled"
                    wire:target="search"
                    class="inline-flex h-12 items-center justify-center gap-2 rounded-md bg-sky-700 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 focus:ring-offset-white disabled:cursor-not-allowed disabled:opacity-70 dark:focus:ring-offset-slate-950"
                >

                    <span wire:loading.remove wire:target="search">
                        <flux:icon.magnifying-glass class="size-4" />
                    </span>

                    <span
                        wire:loading
                        wire:target="search"
                        class="size-4 animate-spin rounded-full border-2 border-white/40 border-t-white"
                    ></span>

                    <span wire:loading.remove wire:target="search">
                        Search
                    </span>

                    <span wire:loading wire:target="search">
                        Searching...
                    </span>

                </button>

            </div>

            @error('searchSpk')
                <p class="mt-2 text-sm font-medium text-red-600 dark:text-red-400">
                    {{ $message }}
                </p>
            @enderror

        </div>


        {{-- MESSAGE --}}

        @if ($pickupMessage)

            <div
                class="mt-4 rounded-lg border px-4 py-3 text-sm font-semibold
                @if ($pickupMessageType === 'success')
                    border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-400/20 dark:bg-emerald-400/10 dark:text-emerald-300
                @elseif ($pickupMessageType === 'warning')
                    border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-400/20 dark:bg-amber-400/10 dark:text-amber-300
                @else
                    border-red-200 bg-red-50 text-red-700 dark:border-red-400/20 dark:bg-red-400/10 dark:text-red-300
                @endif"
            >
                {{ $pickupMessage }}
            </div>

        @endif


        {{-- RESULT --}}

        @if ($spk)

            <div
                class="mt-5 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-slate-950"
            >

                <div
                    class="border-b border-slate-200 bg-slate-50 px-4 py-4 dark:border-white/10 dark:bg-white/5"
                >

                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                        NO SPK
                    </p>

                    <p class="mt-1 text-lg font-bold text-slate-950 dark:text-white">
                        {{ $spk->no_spk }}
                    </p>

                </div>


                <div
                    class="hidden grid-cols-[minmax(0,1.3fr)_minmax(5rem,0.45fr)_minmax(0,1.4fr)_auto] gap-3 border-b border-slate-200 px-4 py-3 text-xs font-bold uppercase tracking-wide text-slate-500 dark:border-white/10 dark:text-slate-400 lg:grid"
                >
                    <span>No Kontainer</span>
                    <span>Ukuran</span>
                    <span>Nomer Truck</span>
                    <span>Action</span>
                </div>


                <div class="divide-y divide-slate-200 dark:divide-white/10">

                    @foreach ($containers as $spkContainer)

                        @php
                            $container = $spkContainer->container;
                            $operation = $operations->get($spkContainer->container_id);
                            $pickup = $operation?->pickup;
                            $truck = $pickup?->truck;
                        @endphp

                        <div
                            wire:key="pickup-container-{{ $spkContainer->container_id }}"
                            class="p-4"
                        >

                            <div
                                class="grid gap-3 lg:grid-cols-[minmax(0,1.3fr)_minmax(5rem,0.45fr)_minmax(0,1.4fr)_auto] lg:items-center"
                            >

                                {{-- CONTAINER --}}

                                <div class="min-w-0">

                                    <span class="mb-1 block text-xs font-semibold uppercase text-slate-500 lg:hidden dark:text-slate-400">
                                        No Kontainer
                                    </span>

                                    <div
                                        class="flex h-11 items-center rounded-md border border-slate-200 bg-slate-100 px-3 text-sm font-bold text-slate-800 dark:border-white/10 dark:bg-white/5 dark:text-white"
                                    >
                                        {{ $container?->no_cont ?? '-' }}
                                    </div>

                                </div>


                                {{-- SIZE --}}

                                <div class="min-w-0">

                                    <span class="mb-1 block text-xs font-semibold uppercase text-slate-500 lg:hidden dark:text-slate-400">
                                        Ukuran
                                    </span>

                                    <div
                                        class="flex h-11 items-center rounded-md border border-slate-200 bg-slate-100 px-3 text-sm font-bold text-slate-800 dark:border-white/10 dark:bg-white/5 dark:text-white"
                                    >
                                        {{ $container?->type?->size ?? '-' }}
                                    </div>

                                </div>


                                {{-- TRUCK --}}

                                <div class="min-w-0">

                                    <span class="mb-1 block text-xs font-semibold uppercase text-slate-500 lg:hidden dark:text-slate-400">
                                        Nomer Truck
                                    </span>

                                    @if ($truck)

                                        <div
                                            class="flex h-11 items-center rounded-md border border-emerald-200 bg-emerald-50 px-3 text-sm font-bold text-emerald-700 dark:border-emerald-400/20 dark:bg-emerald-400/10 dark:text-emerald-300"
                                        >
                                            Truck ID {{ $truck->id }}
                                        </div>

                                    @else

                                        <select
                                            wire:model="selectedTrucks.{{ $spkContainer->container_id }}"
                                            class="h-11 w-full min-w-0 rounded-md border border-slate-200 bg-white px-3 pr-9 text-sm font-semibold text-slate-700 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 dark:border-white/10 dark:bg-slate-950 dark:text-slate-200"
                                        >

                                            <option value="">
                                                Pilih Truck
                                            </option>

                                            @foreach ($trucks as $availableTruck)

                                                <option
                                                    value="{{ $availableTruck->id }}"
                                                >
                                                    Truck ID {{ $availableTruck->id }}
                                                </option>

                                            @endforeach

                                        </select>

                                    @endif

                                </div>


                                {{-- ACTION --}}

                                <div class="min-w-0">

                                    <span class="mb-1 block text-xs font-semibold uppercase text-slate-500 lg:hidden dark:text-slate-400">
                                        Action
                                    </span>

                                    @if ($pickup)

                                        <button
                                            type="button"
                                            disabled
                                            class="inline-flex h-11 w-full items-center justify-center rounded-md bg-slate-500 px-4 text-sm font-bold text-white opacity-80 lg:w-auto"
                                        >
                                            Terkirim
                                        </button>

                                    @else

                                        <button
                                            type="button"
                                            wire:click="send({{ $spkContainer->container_id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="send({{ $spkContainer->container_id }})"
                                            class="inline-flex h-11 w-full items-center justify-center rounded-md bg-sky-700 px-4 text-sm font-bold text-white shadow-sm transition hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60 lg:w-auto dark:focus:ring-offset-slate-950"
                                        >
                                            <span
                                                wire:loading.remove
                                                wire:target="send({{ $spkContainer->container_id }})"
                                            >
                                                Send
                                            </span>

                                            <span
                                                wire:loading
                                                wire:target="send({{ $spkContainer->container_id }})"
                                            >
                                                Sending...
                                            </span>
                                        </button>

                                    @endif

                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>

            </div>

        @endif

    </div>

</div>