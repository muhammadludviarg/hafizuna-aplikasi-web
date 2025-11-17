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

        {{-- Sidebar --}}
        <aside class="z-20 flex-shrink-0 hidden w-64 overflow-y-auto bg-green-800 md:block">
            {{-- Kontainer dibuat relative h-full agar tombol absolute bottom-0 berfungsi --}}
            <div class="relative flex flex-col h-full py-4 text-gray-200"> 
                
                {{-- Logo --}}
                <a class="flex items-center justify-center text-lg font-bold text-white" 
                   href="{{ route('guru.dashboard') }}">
                    <svg class="w-8 h-8 mr-2 bg-white text-green-800 p-1 rounded" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253">
                        </path>
                    </svg>
                    <span>HAFIZUNA</span>
                </a>

                {{-- Info Pengguna --}}
                <div class="flex flex-col items-center mt-6">
                    <span
                        class="mt-2 text-md font-semibold text-white">{{ Auth::user()->nama_lengkap ?? 'Guru' }}</span>
                    <span class="text-xs text-green-300">Guru</span>
                </div>

                {{-- Daftar Menu (flex-grow untuk mendorong Logout ke bawah) --}}
                <ul class="mt-8 space-y-2 flex-grow">
                    {{-- LINK DASHBOARD --}}
                    @php $isActive = request()->routeIs('guru.dashboard'); @endphp
                    <li class="relative px-6 py-3">
                        <span class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg {{ $isActive ? 'bg-white' : '' }}" aria-hidden="true"></span>
                        <a class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 
                            {{ $isActive ? 'text-white' : 'text-gray-200 hover:text-white' }}"
                            href="{{ route('guru.dashboard') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6m-6 0v-4a1 1 0 011-1h2a1 1 0 011 1v4" />
                            </svg>
                            <span class="ml-4">Dashboard</span>
                        </a>
                    </li>

                    {{-- LINK INPUT NILAI --}}
                    @php $isActive = request()->routeIs('guru.input-nilai'); @endphp
                    <li class="relative px-6 py-3">
                        <span class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg {{ $isActive ? 'bg-white' : '' }}" aria-hidden="true"></span>
                        <a class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 
                            {{ $isActive ? 'text-white' : 'text-gray-200 hover:text-white' }}"
                            href="{{ route('guru.input-nilai') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            <span class="ml-4">Input Nilai Hafalan</span>
                        </a>
                    </li>

                    {{-- LINK KELOLA SISWA DAN KELOMPOK --}}
                    @php $isActive = request()->routeIs('guru.kelompok.*'); @endphp
                    <li class="relative px-6 py-3">
                        <span class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg {{ $isActive ? 'bg-white' : '' }}" aria-hidden="true"></span>
                        <a class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 
                            {{ $isActive ? 'text-white' : 'text-gray-200 hover:text-white' }}"
                            href="{{ route('guru.kelompok.index') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h-4a2 2 0 01-2-2V8a2 2 0 012-2h4a2 2 0 012 2v10a2 2 0 01-2 2zM7 20h2a2 2 0 002-2v-4a2 2 0 00-2-2H7a2 2 0 00-2 2v4a2 2 0 002 2z" />
                            </svg>
                            <span class="ml-4">Kelola Siswa dan Kelompok</span>
                        </a>
                    </li>

                    {{-- LINK LAPORAN HAFALAN --}}
                    @php $isActive = request()->routeIs('guru.laporan-hafalan'); @endphp
                    <li class="relative px-6 py-3">
                        <span class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg {{ $isActive ? 'bg-white' : '' }}" aria-hidden="true"></span>
                        <a class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 
                            {{ $isActive ? 'text-white' : 'text-gray-200 hover:text-white' }}"
                            href="{{ route('guru.laporan-hafalan') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-4m0 0a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v4a2 2 0 01-2 2h-6z" />
                            </svg>
                            <span class="ml-4">Laporan Hafalan</span>
                        </a>
                    </li>

                    {{-- LINK GANTI PASSWORD (Mengarah ke route guru.ganti-password) --}}
                    @php $isActive = request()->routeIs('guru.ganti-password'); @endphp
                    <li class="relative px-6 py-3">
                        <span class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg {{ $isActive ? 'bg-white' : '' }}" aria-hidden="true"></span>
                        <a class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 
                            {{ $isActive ? 'text-white' : 'text-gray-200 hover:text-white' }}"
                            href="{{ route('guru.ganti-password') }}"> {{-- <-- PERBAIKAN DI SINI --}}
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4m-1 0h10a1 1 0 011 1v7a1 1 0 01-1 1H6a1 1 0 01-1-1v-7a1 1 0 011-1z" />
                            </svg>
                            <span class="ml-4">Ganti Password</span>
                        </a>
                    </li>

                    <!-- <li class="relative px-6 py-3">
                        <a href="{{ route('guru.ganti-password') }}"
                        class="inline-flex items-center w-full text-sm font-semibold {{ request()->routeIs('guru.ganti-password') ? 'text-white' : 'text-green-300 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                            </svg>
                            <span class="ml-4">Ganti Password</span>
                        </a>
                    </li> -->
                </ul>

                {{-- --- TOMBOL KELUAR (SESUAI ADMIN & DIPOSISIKAN DENGAN BENAR) --- --}}
                <div class="px-6 my-6">
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

        {{-- Konten Utama --}}
        <div class="flex flex-col flex-1 w-full">
            <header class="z-10 py-4 bg-white shadow-md">
                <div class="container flex items-center justify-end h-full px-6 mx-auto text-green-600">
                    {{-- Header konten (kosong sesuai layout guru sebelumnya) --}}
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