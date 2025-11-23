<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Hafizuna') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body class="font-sans antialiased">
    <div class="flex h-screen bg-gray-100">

        <aside class="z-20 flex-shrink-0 hidden w-64 overflow-y-auto bg-green-800 md:block">
            <div class="py-4 text-gray-200">

                <a class="flex items-center justify-center text-lg font-bold text-white"
                    href="{{ route('dashboard') }}"> <svg class="w-8 h-8 mr-2 bg-white text-green-800 p-1 rounded"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253">
                        </path>
                    </svg>
                    <span>HAFIZUNA</span>
                </a>

                <div class="flex flex-col items-center mt-6">
                    <div class="w-10 h-10 rounded-full bg-green-600 flex items-center justify-center font-bold text-xl">
                        A </div>
                    <span class="mt-2 text-md font-semibold">{{ Auth::user()->nama_lengkap ?? 'Admin Hafizuna' }}</span>
                    <span class="text-xs text-green-300">Admin</span>
                </div>

                <ul class="mt-8 space-y-2">

                    <li class="relative px-6 py-1.5">
                        @php $isActive = request()->routeIs('dashboard'); @endphp
                        <span
                            class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg {{ $isActive ? 'bg-white' : '' }}"
                            aria-hidden="true"></span>
                        <a class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 {{ $isActive ? 'text-white' : 'text-green-300 hover:text-white' }}"
                            href="{{ route('dashboard') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6-4h.01M12 12h.01M15 12h.01M12 9h.01M15 9h.01M9 9h.01">
                                </path>
                            </svg>
                            <span class="ml-4">Beranda</span>
                        </a>
                    </li>

                    <li class="relative px-6 py-1.5">
                        @php $isActive = false; @endphp <span
                            class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg {{ $isActive ? 'bg-white' : '' }}"
                            aria-hidden="true"></span>
                        <a class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 {{ $isActive ? 'text-white' : 'text-green-300 hover:text-white' }}"
                            href="/admin/data-master"> <svg class="w-5 h-5" fill="none" stroke="currentColor" 
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 4h.01M12 17h.01">
                                </path>
                            </svg>
                            <span class="ml-4">Data Master</span>
                        </a>
                    </li>

                    <li class="relative px-6 py-1.5">
                        @php $isActive = request()->routeIs('admin.target-hafalan'); @endphp
                        <span class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg {{ $isActive ? 'bg-white' : '' }}"
                            aria-hidden="true"></span>
                        <a class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 {{ $isActive ? 'text-white' : 'text-green-300 hover:text-white' }}"
                            href="{{ route('admin.target-hafalan') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                </path>
                            </svg>
                            <span class="ml-4">Target Hafalan</span>
                        </a>
                    </li>

                    <li class="relative px-6 py-1.5">
                        <a class="inline-flex items-center w-full text-sm font-medium text-green-300 hover:text-white transition-colors duration-150"
                            href="#">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.653-.084-1.284-.24-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.653.084-1.284.24-1.857m0 0a5.002 5.002 0 019.52 0M12 12a5 5 0 110-10 5 5 0 010 10z">
                                </path>
                            </svg>
                            <span class="ml-4">Kelas & Kelompok</span>
                        </a>
                    </li>

                    <li class="relative px-6 py-1.5">
                        @php $isActive = request()->routeIs('admin.laporan-hafalan'); @endphp
                        <span class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg {{ $isActive ? 'bg-white' : '' }}"
                            aria-hidden="true"></span>
                        <a class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 {{ $isActive ? 'text-white' : 'text-green-300 hover:text-white' }}"
                            href="{{ route('admin.laporan-hafalan') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <span class="ml-4">Laporan Hafalan</span>
                        </a>
                    </li>

                    <li class="relative px-6 py-1.5">
                        @php $isActive = request()->routeIs('admin.pengaturan-nilai'); @endphp
                        <span class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg {{ $isActive ? 'bg-white' : '' }}"
                            aria-hidden="true"></span>
                        <a class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 {{ $isActive ? 'text-white' : 'text-green-300 hover:text-white' }}"
                            href="{{ route('admin.pengaturan-nilai') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                </path>
                            </svg>
                            <span class="ml-4">Pengaturan Nilai</span>
                        </a>
                    </li>

                    {{-- LINK GANTI PASSWORD (ADMIN) - Gabungan dari kedua versi --}}
                    @php $isActive = request()->routeIs('admin.ganti-password'); @endphp
                    <li class="relative px-6 py-1.5">
                        <span class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg {{ $isActive ? 'bg-white' : '' }}" aria-hidden="true"></span>
                        <a class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 {{ $isActive ? 'text-white' : 'text-green-300 hover:text-white' }}"
                            href="{{ route('admin.ganti-password') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                            </svg>
                            <span class="ml-4">Ganti Password</span>
                        </a>
                    </li>

                </ul>

                <div class="absolute bottom-0 w-full px-6 my-6">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();"
                            class="inline-flex items-center w-full px-4 py-2 text-sm font-medium text-green-300 hover:text-white transition-colors duration-150">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                </path>
                            </svg>
                            <span class="ml-4">Keluar</span>
                        </a>
                    </form>
                </div>
            </div>
        </aside>
        <div class="flex flex-col flex-1 w-full">

            <header class="z-10 py-4 bg-white shadow-md">
                <div class="container flex items-center justify-end h-full px-6 mx-auto text-green-600">
                    <button class="relative align-middle rounded-full focus:outline-none" aria-label="Account"
                        aria-haspopup="true">
                        <div
                            class="w-8 h-8 rounded-full bg-green-800 text-white flex items-center justify-center font-bold text-sm">
                            A </div>
                    </button>
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
