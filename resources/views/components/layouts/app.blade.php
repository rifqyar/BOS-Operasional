<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-slate-50 dark:bg-slate-950">
        <header class="sticky top-0 z-40 border-b border-slate-200 bg-white/95 backdrop-blur dark:border-white/10 dark:bg-slate-900/95">
            <div class="mx-auto flex min-h-16 w-full max-w-7xl items-center justify-between gap-3 px-4 sm:px-6 lg:px-8">
                <a href="{{ route('dashboard') }}" class="flex min-w-0 items-center gap-3" wire:navigate>
                    <x-app-logo class="size-8" href="#"></x-app-logo>
                </a>

                <div
                    class="flex shrink-0 items-center gap-2 rounded-md bg-sky-50 px-3 py-2 text-sky-800 dark:bg-sky-400/10 dark:text-sky-100"
                    data-clock
                    data-server-time="{{ now()->toIso8601String() }}"
                >
                    <flux:icon.clock class="size-4" />
                    <span class="font-mono text-sm font-semibold tabular-nums" data-clock-time>{{ now()->format('H:i:s') }}</span>
                    <span class="hidden text-xs font-semibold sm:inline">WIB</span>
                </div>

                <flux:dropdown position="bottom" align="end">
                    <button type="button"
                        class="flex min-w-0 items-center gap-3 rounded-md border border-slate-200 bg-slate-50 px-3 py-2 transition hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-sky-500 dark:border-white/10 dark:bg-white/5 dark:hover:bg-white/10">
                        <span class="flex size-9 shrink-0 items-center justify-center rounded-md bg-sky-700 text-sm font-semibold text-white">
                            {{ auth()->user()->initials() }}
                        </span>
                        <span class="hidden min-w-0 text-right sm:block">
                            <span class="block truncate text-sm font-semibold text-slate-950 dark:text-white">{{ auth()->user()->name }}</span>
                            <span class="block truncate text-xs text-slate-500 dark:text-slate-400">{{ auth()->user()->username }}</span>
                        </span>
                        <flux:icon.chevron-down class="size-4 shrink-0 text-slate-500 dark:text-slate-300" />
                    </button>

                    <flux:menu class="w-[240px]">
                        <div class="p-2 text-sm font-normal">
                            <div class="flex items-center gap-3">
                                <span class="flex size-10 shrink-0 items-center justify-center rounded-md bg-sky-700 text-sm font-semibold text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-slate-950 dark:text-white">{{ auth()->user()->name }}</p>
                                    <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ auth()->user()->username }}</p>
                                </div>
                            </div>
                        </div>

                        <flux:menu.separator />

                        <flux:menu.item href="/settings/profile" icon="cog" wire:navigate>Settings</flux:menu.item>

                        <flux:menu.separator />

                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                                Log Out
                            </flux:menu.item>
                        </form>
                    </flux:menu>
                </flux:dropdown>
            </div>
        </header>

        <main>
            {{ $slot }}
        </main>

        @fluxScripts
        <script>
            (() => {
                const clock = document.querySelector('[data-clock]');

                if (! clock) {
                    return;
                }

                const output = clock.querySelector('[data-clock-time]');
                const serverTime = new Date(clock.dataset.serverTime);
                const offset = serverTime.getTime() - Date.now();
                const formatter = new Intl.DateTimeFormat('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false,
                    timeZone: 'Asia/Jakarta',
                });

                const updateClock = () => {
                    output.textContent = formatter.format(new Date(Date.now() + offset)).replaceAll('.', ':');
                };

                updateClock();
                setInterval(updateClock, 1000);
            })();
        </script>
        @stack('scripts')
    </body>
</html>
