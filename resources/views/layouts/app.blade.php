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

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-50 text-gray-900">

    <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden bg-gray-100" x-cloak>

        <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
            class="fixed inset-0 z-30 bg-gray-900 bg-opacity-50 backdrop-blur-sm md:hidden" aria-hidden="true">
        </div>

        <aside
            class="fixed inset-y-0 left-0 z-40 flex flex-col h-full bg-green-800 border-r border-green-900 shadow-2xl transition-all duration-300 ease-in-out transform md:relative md:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0 w-64' : '-translate-x-full w-64 md:w-0 md:translate-x-0 md:overflow-hidden'">

            <div class="flex items-center justify-between h-16 px-4 bg-green-900 md:hidden shrink-0">
                <span class="text-lg font-bold text-white">Menu</span>
                <button @click="sidebarOpen = false" class="text-green-200 hover:text-white focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto py-6 flex flex-col">
                <ul class="space-y-2 px-3 transition-opacity duration-200"
                    :class="sidebarOpen ? 'opacity-100' : 'opacity-100 md:opacity-0'">

                    <li>
                        <a href="{{ route('dashboard') }}" class="group flex items-center px-4 py-3 text-sm font-medium transition-all duration-200 rounded-r-lg rounded-bl-lg whitespace-nowrap
                           {{ request()->routeIs('dashboard')
    ? 'bg-green-900 text-white border-l-4 border-yellow-400 shadow-inner'
    : 'text-green-100 hover:bg-green-700 hover:text-white hover:pl-6' }}">
                            Beranda
                        </a>
                    </li>

                    <li>
                        <a href="/admin/data-master" class="group flex items-center px-4 py-3 text-sm font-medium transition-all duration-200 rounded-r-lg rounded-bl-lg whitespace-nowrap
                           {{ request()->is('admin/data-master*')
    ? 'bg-green-900 text-white border-l-4 border-yellow-400 shadow-inner'
    : 'text-green-100 hover:bg-green-700 hover:text-white hover:pl-6' }}">
                            Data Master
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.target-hafalan') }}" class="group flex items-center px-4 py-3 text-sm font-medium transition-all duration-200 rounded-r-lg rounded-bl-lg whitespace-nowrap
                           {{ request()->routeIs('admin.target-hafalan')
    ? 'bg-green-900 text-white border-l-4 border-yellow-400 shadow-inner'
    : 'text-green-100 hover:bg-green-700 hover:text-white hover:pl-6' }}">
                            Target Hafalan
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.kelola-kelompok') }}" class="group flex items-center px-4 py-3 text-sm font-medium transition-all duration-200 rounded-r-lg rounded-bl-lg whitespace-nowrap
                           {{ request()->routeIs('admin.kelola-kelompok')
    ? 'bg-green-900 text-white border-l-4 border-yellow-400 shadow-inner'
    : 'text-green-100 hover:bg-green-700 hover:text-white hover:pl-6' }}">
                            Siswa & Kelompok
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.laporan-hafalan') }}" class="group flex items-center px-4 py-3 text-sm font-medium transition-all duration-200 rounded-r-lg rounded-bl-lg whitespace-nowrap
                           {{ request()->routeIs('admin.laporan-hafalan')
    ? 'bg-green-900 text-white border-l-4 border-yellow-400 shadow-inner'
    : 'text-green-100 hover:bg-green-700 hover:text-white hover:pl-6' }}">
                            Laporan Hafalan
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.pengaturan-nilai') }}" class="group flex items-center px-4 py-3 text-sm font-medium transition-all duration-200 rounded-r-lg rounded-bl-lg whitespace-nowrap
                           {{ request()->routeIs('admin.pengaturan-nilai')
    ? 'bg-green-900 text-white border-l-4 border-yellow-400 shadow-inner'
    : 'text-green-100 hover:bg-green-700 hover:text-white hover:pl-6' }}">
                            Pengaturan Nilai
                        </a>
                    </li>

                    @if(Auth::check())
                                    <li>
                                        <a href="{{ route('admin.log-aktivitas') }}" class="group flex items-center px-4 py-3 text-sm font-medium transition-all duration-200 rounded-r-lg rounded-bl-lg whitespace-nowrap
                                           {{ request()->routeIs('admin.log-aktivitas')
                        ? 'bg-green-900 text-white border-l-4 border-yellow-400 shadow-inner'
                        : 'text-green-100 hover:bg-green-700 hover:text-white hover:pl-6' }}">
                                            Log Aktivitas
                                        </a>
                                    </li>
                    @endif
                </ul>
            </div>
        </aside>

        <div class="flex flex-col flex-1 min-w-0 transition-all duration-300">

            <header
                class="flex items-center justify-between px-4 md:px-6 py-3 md:py-4 bg-white shadow-md border-b border-gray-100 sticky top-0 z-30">

                <div class="flex items-center gap-3 md:gap-4">

                    <button x-show="!sidebarOpen" @click.stop="sidebarOpen = true"
                        class="p-2 -ml-2 md:ml-0 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-green-700 focus:outline-none transition-colors">
                        <svg class="w-6 h-6 md:w-7 md:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h7"></path>
                        </svg>
                    </button>

                    <div class="flex items-center gap-2">
                        <div class="bg-green-700 text-white p-1 md:p-1.5 rounded-md shadow-sm">
                            <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253">
                                </path>
                            </svg>
                        </div>
                        <h1 class="text-lg md:text-xl font-bold tracking-tight text-green-800">HAFIZUNA</h1>
                    </div>
                </div>

                <div class="flex items-center" @click.stop>
                    <div x-data="{ dropdownOpen: false }" class="relative">
                        <button @click="dropdownOpen = !dropdownOpen"
                            class="flex items-center space-x-2 md:space-x-3 focus:outline-none group">
                            <div class="hidden md:block text-right">
                                <div class="text-sm font-semibold text-gray-700 group-hover:text-green-700">
                                    {{ Auth::user()->nama_lengkap ?? 'Admin' }}</div>
                                <div class="text-xs text-gray-400">Administrator</div>
                            </div>
                            <div
                                class="w-9 h-9 md:w-10 md:h-10 rounded-full bg-gradient-to-tr from-green-600 to-green-400 text-white flex items-center justify-center font-bold text-base md:text-lg shadow-md ring-2 ring-white group-hover:ring-green-100 transition-all">
                                {{ substr(Auth::user()->nama_lengkap ?? 'A', 0, 1) }}
                            </div>
                        </button>

                        <div x-show="dropdownOpen" @click.away="dropdownOpen = false"
                            class="absolute right-0 w-48 md:w-56 mt-3 origin-top-right bg-white rounded-xl shadow-xl py-2 ring-1 ring-black ring-opacity-5 z-50 transform transition-all"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            style="display: none;">

                            <div class="px-4 py-2 border-b border-gray-100 mb-1">
                                <p class="text-xs text-gray-400 uppercase tracking-wider">Akun Anda</p>
                                <p class="text-sm font-bold text-gray-800 truncate">{{ Auth::user()->email }}</p>
                            </div>

                            <a href="{{ route('admin.ganti-password') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-700 transition-colors">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z">
                                        </path>
                                    </svg>
                                    Ganti Password
                                </div>
                            </a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); this.closest('form').submit();"
                                    class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors cursor-pointer rounded-b-xl">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                            </path>
                                        </svg>
                                        Keluar
                                    </div>
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 relative z-0"
                @click="if(window.innerWidth >= 768 && sidebarOpen) sidebarOpen = false">

                <div class="flex flex-col min-h-full">

                    <div class="flex-grow container px-4 md:px-6 py-6 md:py-8 mx-auto">
                        @if(trim($__env->yieldContent('header')))
                            <div class="mb-4 md:mb-6 pb-2 md:pb-4 border-b border-gray-200">
                                <h2 class="text-xl md:text-2xl font-bold text-gray-800">
                                    @yield('header')
                                </h2>
                            </div>
                        @endif

                        <div class="w-full overflow-x-auto">
                            {{ $slot }}
                        </div>
                    </div>

                    <footer class="bg-white border-t border-gray-200 py-6 mt-auto">
                        <div
                            class="container mx-auto px-4 md:px-6 flex flex-col md:flex-row justify-between items-center text-sm text-gray-500 gap-2 md:gap-0">
                            <div class="text-center md:text-left">
                                <span class="font-semibold text-gray-700">Sekolah Islam Terpadu Hafizuna</span>
                                <span class="hidden md:inline mx-2">|</span>
                                <span class="block md:inline mt-1 md:mt-0">&copy; {{ date('Y') }} All rights
                                    reserved.</span>
                            </div>
                            <div class="text-center md:text-right">
                                Developed with <span class="text-red-500">❤</span> by <span
                                    class="text-green-600 font-medium hover:underline cursor-pointer">Tim
                                    Developer</span>
                            </div>
                        </div>
                    </footer>

                </div>
            </main>

        </div>
    </div>

    @livewireScripts
</body>

</html>