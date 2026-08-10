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

                <div class="flex min-w-0 items-center gap-3 rounded-md border border-slate-200 bg-slate-50 px-3 py-2 dark:border-white/10 dark:bg-white/5">
                    <span class="flex size-9 shrink-0 items-center justify-center rounded-md bg-sky-700 text-sm font-semibold text-white">
                        {{ auth()->user()->initials() }}
                    </span>
                    <div class="min-w-0 text-right">
                        <p class="truncate text-sm font-semibold text-slate-950 dark:text-white">{{ auth()->user()->name }}</p>
                        <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ auth()->user()->username }}</p>
                    </div>
                </div>
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
    </body>
</html>
