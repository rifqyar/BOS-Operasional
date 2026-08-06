@php
    $loginImage = file_exists(public_path('assets/login_image.png'))
        ? asset('assets/login_image.png')
        : asset('assets/img/login_image.png');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head', ['title' => 'Login - BOS Operasional'])
    </head>
    <body class="min-h-screen bg-[#08111f] font-sans antialiased text-white">
        <main class="relative min-h-svh overflow-hidden">
            <div
                class="absolute inset-0 bg-cover bg-center"
                style="background-image: url('{{ $loginImage }}');"
                aria-hidden="true"
            ></div>
            <div class="absolute inset-0 bg-[linear-gradient(110deg,rgba(8,17,31,.95)_0%,rgba(8,17,31,.84)_12%,rgba(8,17,31,.42)_32%)]" aria-hidden="true"></div>

            <section class="relative z-10 grid min-h-svh items-center px-5 py-8 sm:px-8 lg:grid-cols-[minmax(0,1fr)_480px] lg:px-12">
                <div class="hidden max-w-2xl self-center pb-10 lg:block">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-3 text-white" wire:navigate>
                        <span class="flex size-12 items-center justify-center rounded-lg bg-white/12 ring-1 ring-white/20 backdrop-blur">
                            <x-app-logo-icon class="size-8 fill-current text-white" />
                        </span>
                        <span class="text-xl font-semibold tracking-normal">BOS Operasional</span>
                    </a>

                    <div class="mt-10 space-y-5">
                        <p class="inline-flex rounded-full bg-white/10 px-4 py-2 text-xl font-medium text-white/85 ring-1 ring-white/15 backdrop-blur">
                            Behandle Operation System
                        </p>
                    </div>
                </div>

                <div class="mx-auto w-full max-w-[440px] lg:mx-0">
                    <div class="mb-8 flex items-center justify-center gap-3 lg:hidden">
                        <span class="flex size-11 items-center justify-center rounded-lg bg-white/12 ring-1 ring-white/20 backdrop-blur">
                            <x-app-logo-icon class="size-7 fill-current text-white" />
                        </span>
                        <span class="text-lg font-semibold">BOS Operasional</span>
                    </div>

                    <div class="rounded-lg border border-white/16 bg-white/[.92] p-6 text-neutral-950 shadow-2xl shadow-black/30 backdrop-blur-xl sm:p-8 dark:bg-white/[.92]">
                        {{ $slot }}
                    </div>
                </div>
            </section>
        </main>

        @fluxScripts
    </body>
</html>
