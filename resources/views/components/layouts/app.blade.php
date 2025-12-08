<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Confidential Intranet for Associates</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        {{--    <script src="https://cdn.tailwindcss.com"></script>--}}
        {{--    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css"--}}
        {{--          integrity="sha512-wnea99uKIC3TJF7v4eKk4Y+lMz2Mklv18+r4na2Gn1abDRPPOeef95xTzdwGD9e6zXJBteMIhZ1+68QC5byJZw=="--}}
        {{--          crossorigin="anonymous" referrerpolicy="no-referrer"/>--}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
        <style>
            [x-cloak] {
                display: none;
            }

            body {
                font-family: 'Nunito', sans-serif;
            }

            .builder ul, .builder2 ul, .builder ol, .builder2 ol {
                padding-left: 30px;
            }

            .builder ul li, .builder2 ul li {
                list-style-type: square;
            }

            .builder ol li, .builder2 ul ol {
                list-style-type: number;
            }

            .builder a {
                color: #3B82F6 !important;
                text-decoration: underline !important;
            }

            .builder.promotion img {
                margin: 0 auto !important;
            }

            /*
            We will fix some bad builder styles here
             */
            .builder h1 {
                font-weight: bold;
                font-size: 24px;
            }
            .builder h2{
                font-weight: bold;
                font-size:20px;
            }
            .builder h3{
                font-weight: bold;
                font-size: 18px;
            }
            .builder h4{
                font-weight: bold;
                font-size: 16px;
            }
            .builder h5{
                font-weight: bold;
                font-size: 14px;
            }
            .builder h6 {
                font-size: 12pt !important;
                font-family: Arial, sans-serif !important;
                font-weight: normal !important;
            }

        </style>
        <style>[x-cloak] {
                display: none !important;
            }</style>
        @stack('styles')
        @filamentStyles
        @vite('resources/css/app.css')

    </head>
    <body class="antialiased test2 flex flex-col min-h-screen">

        <div class="flex-grow">
            {{$slot ?? ''}}
            @yield('content')
        </div>

        @livewire('notifications')
        @stack('scripts')
        @filamentScripts
        @vite('resources/js/app.js')

        @livewire('notifications')
    </body>
</html>
