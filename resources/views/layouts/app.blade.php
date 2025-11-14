<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Hafizuna') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50">
        <div class="flex h-screen">
            <!-- Sidebar fixed width on left -->
            <div class="w-64 bg-white">
                <!-- Changed background from bg-gray-900 to bg-white for light sidebar -->
                @include('layouts.sidebar')
            </div>

            <!-- Main content area flexible -->
            <div class="flex-1 flex flex-col overflow-hidden bg-gray-50">
                @include('layouts.navigation')

                <!-- Scrollable content area -->
                <main class="flex-1 overflow-auto">
                    @yield('content')
                </main>
            </div>
        </div>
    </body>
</html>
