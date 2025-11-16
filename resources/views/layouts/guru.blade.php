<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Hafizuna') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="font-sans antialiased">
    <div class="flex h-screen bg-gray-100">

        <aside class="z-20 flex-shrink-0 hidden w-64 overflow-y-auto bg-green-800 md:block">
            <div class="py-4 text-gray-200">
                <a class="flex items-center justify-center text-lg font-bold text-white" href="#">
                    <svg class="w-8 h-8 mr-2 bg-white text-green-800 p-1 rounded" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253">
                        </path>
                    </svg>
                    <span>HAFIZUNA</span>
                </a>

                <div class="flex flex-col items-center mt-6">
                    <span class="mt-2 text-md font-semibold text-white">Guru (Dev Mode)</span>
                    <span class="text-xs text-green-300">Guru</span>
                </div>

                <ul class="mt-8 space-y-2">
                    <li class="relative px-6 py-3">
                        <span class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg bg-white"
                            aria-hidden="true"></span>
                        <a class="inline-flex items-center w-full text-sm font-semibold text-white transition-colors duration-150"
                            href="#">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                            <span class="ml-4">Input Data Hafalan</span>
                        </a>
                    </li>

                    <li class="relative px-6 py-3">
                        <a href="{{ route('guru.ganti-password') }}"
                        class="inline-flex items-center w-full text-sm font-semibold {{ request()->routeIs('guru.ganti-password') ? 'text-white' : 'text-green-300 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                            </svg>
                            <span class="ml-4">Ganti Password</span>
                        </a>
                    </li>
                </ul>
            </div>
        </aside>
        <div class="flex flex-col flex-1 w-full">
            <header class="z-10 py-4 bg-white shadow-md">
                <div class="container flex items-center justify-end h-full px-6 mx-auto text-green-600">
                </div>
            </header>
            <main class="h-full overflow-y-auto">
                <div class="container px-6 py-8 mx-auto grid">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
    @livewireScripts
</body>

</html>