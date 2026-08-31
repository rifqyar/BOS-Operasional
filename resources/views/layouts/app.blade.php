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


    {{-- ============================================================
        GOOGLE FONT
    ============================================================= --}}

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


    {{-- ============================================================
        MATERIAL SYMBOLS
    ============================================================= --}}

    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap"
        rel="stylesheet"
    >


    {{-- ============================================================
        TAILWIND CSS
    ============================================================= --}}

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


    {{-- ============================================================
        MATERIAL SYMBOL CONFIG
    ============================================================= --}}

    <style>

        .material-symbols-outlined {

            font-variation-settings:
                'FILL' 0,
                'wght' 400,
                'GRAD' 0,
                'opsz' 24;

            font-size: 24px;

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

            background-color: #f8f9ff;

        }

    </style>


    @livewireStyles

</head>


<body>

    {{-- ============================================================
        LIVEWIRE PAGE
    ============================================================= --}}

    {{ $slot }}


    {{-- ============================================================
        LIVEWIRE
    ============================================================= --}}

    @livewireScripts

</body>

</html>