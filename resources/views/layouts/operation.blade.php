<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        @yield('title', 'BOS Operasional')
    </title>


    {{-- FONT --}}

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >


    {{-- MATERIAL SYMBOLS --}}

    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap"
        rel="stylesheet"
    >


    {{-- TAILWIND --}}

    <script src="https://cdn.tailwindcss.com"></script>


    <script>

        tailwind.config = {

            theme: {

                extend: {

                    fontFamily: {

                        sans: [
                            'Inter',
                            'ui-sans-serif',
                            'system-ui',
                            'sans-serif'
                        ],

                    },

                },

            },

        };

    </script>


    <style>

        .material-symbols-outlined {

            font-variation-settings:
                'FILL' 0,
                'wght' 400,
                'GRAD' 0,
                'opsz' 24;

            line-height: 1;

        }


        html,
        body {

            margin: 0;

            padding: 0;

            min-height: 100%;

        }


        body {

            font-family: 'Inter', sans-serif;

            background: #f8f9ff;

        }

    </style>


    @livewireStyles

</head>


<body class="bg-[#f8f9ff] text-[#0b1c30]">

    <div class="min-h-screen">


        {{-- ========================================================
            SIDEBAR
        ========================================================= --}}

        <aside
            class="fixed
                   left-0
                   top-0
                   z-40
                   hidden
                   h-screen
                   w-[260px]
                   flex-col
                   bg-[#213145]
                   border-r
                   border-[#c3c6d7]/20
                   md:flex"
        >


            {{-- BRAND --}}

            <div
                class="border-b
                       border-[#c3c6d7]/10
                       px-6
                       py-6"
            >

                <h1
                    class="text-base
                           font-bold
                           text-[#dbe1ff]"
                >
                    PortOps Central
                </h1>

                <p
                    class="mt-1
                           text-[13px]
                           text-[#bec6e0]"
                >
                    Terminal A-101
                </p>

            </div>


            {{-- MENU --}}

            <nav class="flex-1 overflow-y-auto py-4">

                <ul class="space-y-1">


                    {{-- DASHBOARD --}}

                    <li>

                        <a
                            href="#"
                            class="flex
                                   items-center
                                   gap-4
                                   border-l-4
                                   border-transparent
                                   px-6
                                   py-3
                                   text-[#bec6e0]/70
                                   transition
                                   hover:bg-[#d3e4fe]/10
                                   hover:text-[#bec6e0]"
                        >

                            <span class="material-symbols-outlined">
                                dashboard
                            </span>

                            <span
                                class="text-xs
                                       font-semibold
                                       tracking-wider"
                            >
                                Dashboard
                            </span>

                        </a>

                    </li>


                    {{-- OPERATIONS --}}

                    <li class="mt-5">

                        <div
                            class="px-6
                                   py-2
                                   text-[10px]
                                   font-semibold
                                   uppercase
                                   tracking-widest
                                   text-[#bec6e0]/50"
                        >
                            Operations
                        </div>

                    </li>


                    {{-- PICKUP --}}

                    <li>

                        <a
                            href="{{ route('operation.pickup') }}"
                            class="flex
                                   items-center
                                   gap-4
                                   border-l-4
                                   border-[#004ac6]
                                   bg-[#2563eb]/10
                                   px-6
                                   py-3
                                   text-[#dbe1ff]"
                        >

                            <span
                                class="material-symbols-outlined"
                                style="font-variation-settings: 'FILL' 1;"
                            >
                                local_shipping
                            </span>

                            <span
                                class="text-xs
                                       font-bold
                                       tracking-wider"
                            >
                                PICK UP
                            </span>

                        </a>

                    </li>


                    {{-- BEHANDLE IN --}}

                    <li>

                        <a
                            href="#"
                            class="flex
                                   items-center
                                   gap-4
                                   border-l-4
                                   border-transparent
                                   px-6
                                   py-3
                                   text-[#bec6e0]/70
                                   transition
                                   hover:bg-[#d3e4fe]/10
                                   hover:text-[#bec6e0]"
                        >

                            <span class="material-symbols-outlined">
                                move_to_inbox
                            </span>

                            <span
                                class="text-xs
                                       font-semibold
                                       tracking-wider"
                            >
                                BEHANDLE IN
                            </span>

                        </a>

                    </li>


                    {{-- HOLD --}}

                    <li>

                        <a
                            href="#"
                            class="flex
                                   items-center
                                   gap-4
                                   border-l-4
                                   border-transparent
                                   px-6
                                   py-3
                                   text-[#bec6e0]/70
                                   transition
                                   hover:bg-[#d3e4fe]/10
                                   hover:text-[#bec6e0]"
                        >

                            <span class="material-symbols-outlined">
                                front_hand
                            </span>

                            <span
                                class="text-xs
                                       font-semibold
                                       tracking-wider"
                            >
                                HOLD
                            </span>

                        </a>

                    </li>


                    {{-- MARSHALLING --}}

                    <li>

                        <a
                            href="#"
                            class="flex
                                   items-center
                                   gap-4
                                   border-l-4
                                   border-transparent
                                   px-6
                                   py-3
                                   text-[#bec6e0]/70
                                   transition
                                   hover:bg-[#d3e4fe]/10
                                   hover:text-[#bec6e0]"
                        >

                            <span class="material-symbols-outlined">
                                warehouse
                            </span>

                            <span
                                class="text-xs
                                       font-semibold
                                       tracking-wider"
                            >
                                MARSHALLING
                            </span>

                        </a>

                    </li>


                    {{-- INSPECTION --}}

                    <li>

                        <a
                            href="#"
                            class="flex
                                   items-center
                                   gap-4
                                   border-l-4
                                   border-transparent
                                   px-6
                                   py-3
                                   text-[#bec6e0]/70
                                   transition
                                   hover:bg-[#d3e4fe]/10
                                   hover:text-[#bec6e0]"
                        >

                            <span class="material-symbols-outlined">
                                fact_check
                            </span>

                            <span
                                class="text-xs
                                       font-semibold
                                       tracking-wider"
                            >
                                INSPECTION
                            </span>

                        </a>

                    </li>

                </ul>

            </nav>


            {{-- LOGOUT --}}

            <div
                class="border-t
                       border-[#c3c6d7]/10
                       py-3"
            >

                <a
                    href="#"
                    class="flex
                           items-center
                           gap-4
                           px-6
                           py-3
                           text-[#bec6e0]/70
                           transition
                           hover:bg-[#d3e4fe]/10
                           hover:text-[#bec6e0]"
                >

                    <span class="material-symbols-outlined">
                        logout
                    </span>

                    <span
                        class="text-xs
                               font-semibold
                               tracking-wider"
                    >
                        Logout
                    </span>

                </a>

            </div>

        </aside>


        {{-- ========================================================
            MAIN
        ========================================================= --}}

        <div class="min-h-screen md:ml-[260px]">


            {{-- TOPBAR --}}

            <header
                class="sticky
                       top-0
                       z-30
                       flex
                       h-14
                       items-center
                       justify-between
                       border-b
                       border-[#c3c6d7]/30
                       bg-[#f8f9ff]
                       px-6"
            >


                {{-- SEARCH --}}

                <div class="flex items-center gap-4">

                    <div class="relative hidden sm:block">

                        <span
                            class="material-symbols-outlined
                                   absolute
                                   left-3
                                   top-1/2
                                   -translate-y-1/2
                                   text-[#737686]"
                        >
                            search
                        </span>

                        <input
                            type="text"
                            placeholder="Search operations..."
                            class="w-64
                                   rounded-lg
                                   border
                                   border-[#c3c6d7]/50
                                   bg-[#eff4ff]
                                   py-2
                                   pl-10
                                   pr-4
                                   text-xs
                                   outline-none
                                   focus:border-[#004ac6]
                                   focus:ring-1
                                   focus:ring-[#004ac6]"
                        >

                    </div>

                </div>


                {{-- ACTIONS --}}

                <div class="flex items-center gap-3">

                    <button
                        type="button"
                        class="rounded-lg
                               p-2
                               text-[#434655]
                               hover:bg-[#eff4ff]"
                    >

                        <span class="material-symbols-outlined text-[20px]">
                            notifications
                        </span>

                    </button>


                    <button
                        type="button"
                        class="hidden
                               rounded-lg
                               p-2
                               text-[#434655]
                               hover:bg-[#eff4ff]
                               sm:block"
                    >

                        <span class="material-symbols-outlined text-[20px]">
                            help_outline
                        </span>

                    </button>


                    <div
                        class="mx-1
                               h-6
                               w-px
                               bg-[#c3c6d7]/30"
                    ></div>


                    <div
                        class="flex
                               h-8
                               w-8
                               items-center
                               justify-center
                               rounded-full
                               bg-[#2563eb]"
                    >

                        <span
                            class="material-symbols-outlined
                                   text-[18px]
                                   text-white"
                        >
                            person
                        </span>

                    </div>

                </div>

            </header>


            {{-- ====================================================
                PAGE CONTENT
            ===================================================== --}}

            <main
                class="min-h-[calc(100vh-56px)]
                       bg-[#f8f9ff]
                       p-4
                       sm:p-6"
            >

                {{ $slot }}

            </main>


        </div>

    </div>


    @livewireScripts

</body>

</html>