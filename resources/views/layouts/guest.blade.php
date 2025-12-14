<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Hafizuna') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo-sd-transparan.png') }}">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="font-sans text-gray-900 antialiased bg-white overflow-hidden">

    <div class="flex h-screen w-full">

        <div
            class="hidden lg:flex lg:w-1/2 relative bg-green-900 h-full flex-col justify-center items-center text-center overflow-hidden">

            <img src="{{ asset('images/' . $attributes->get('bg-image', 'login-bg.jpg')) }}" alt="Background Sekolah"
                class="absolute inset-0 w-full h-full object-cover transition-opacity duration-500 ease-in-out">

            <div class="absolute inset-0 bg-green-900/85 mix-blend-multiply z-10"></div>

            <div class="relative z-20 text-white max-w-lg px-10">
                <div class="flex justify-center mb-8">
                    <x-application-logo class="w-24 h-24 text-white fill-current" />
                </div>

                <h1 class="text-4xl font-bold tracking-tight mb-4">HAFIZUNA</h1>
                <p class="text-lg text-green-100 font-medium mb-8">Sistem Manajemen Hafalan Al-Qur'an</p>
                <div class="w-16 h-px bg-green-200/50 mx-auto mb-8"></div>
                <h2 class="text-xl font-semibold mb-3">SD Islam Al-Azhar 27 Cibinong</h2>
                <p class="text-sm text-green-100/80 leading-relaxed">
                    Mewujudkan generasi Qur'ani yang beradab, cerdas, dan berwawasan global.
                </p>
            </div>
        </div>

        <div
            class="w-full lg:w-1/2 flex flex-col justify-center items-center bg-white relative z-10 overflow-y-auto h-full px-6 py-12">
            <div class="w-full max-w-md">
                <div class="lg:hidden flex justify-center mb-8">
                    <x-application-logo class="w-16 h-16 fill-current text-green-700" />
                </div>

                {{ $slot }}

                <div class="mt-10 pt-6 border-t border-gray-100 text-center">
                    <p class="text-xs text-gray-400">
                        &copy; {{ date('Y') }} <span class="font-semibold text-green-700">Hafizuna</span>
                        <br>SD Islam Al-Azhar 27 Cibinong
                    </p>
                </div>
            </div>
        </div>

    </div>
    @livewireScripts
</body>

</html>