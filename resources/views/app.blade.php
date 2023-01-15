<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        {{-- csrf対策? --}}
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        {{-- icon --}}
        <link rel="icon" type="image/png" href="/favicon.png">

        <!-- Scripts -->
        @routes
        @vite('resources/js/app.js')
        @inertiaHead

        {{-- google広告 --}}
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-6551381289717615"
     crossorigin="anonymous"></script>
    </head>
    <style>
        /* #app {
            height: 100vh;
        } */
    </style>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
