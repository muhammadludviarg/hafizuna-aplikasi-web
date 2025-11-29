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
                        <a href="{{ route('guru.dashboard') }}" class="group flex items-center px-4 py-3 text-sm font-medium transition-all duration-200 rounded-r-lg rounded-bl-lg whitespace-nowrap
                           {{ request()->routeIs('guru.dashboard')
    ? 'bg-green-900 text-white border-l-4 border-yellow-400 shadow-inner'
    : 'text-green-100 hover:bg-green-700 hover:text-white hover:pl-6' }}">
                            Beranda
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('guru.kelompok.index') }}" class="group flex items-center px-4 py-3 text-sm font-medium transition-all duration-200 rounded-r-lg rounded-bl-lg whitespace-nowrap
                           {{ request()->routeIs('guru.kelompok.index') || request()->routeIs('guru.kelompok.detail')
    ? 'bg-green-900 text-white border-l-4 border-yellow-400 shadow-inner'
    : 'text-green-100 hover:bg-green-700 hover:text-white hover:pl-6' }}">
                            Manajemen Kelompok
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('guru.input-nilai') }}" class="group flex items-center px-4 py-3 text-sm font-medium transition-all duration-200 rounded-r-lg rounded-bl-lg whitespace-nowrap
                           {{ request()->routeIs('guru.input-nilai')
    ? 'bg-green-900 text-white border-l-4 border-yellow-400 shadow-inner'
    : 'text-green-100 hover:bg-green-700 hover:text-white hover:pl-6' }}">
                            Input Nilai
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('guru.laporan-hafalan') }}" class="group flex items-center px-4 py-3 text-sm font-medium transition-all duration-200 rounded-r-lg rounded-bl-lg whitespace-nowrap
                           {{ request()->routeIs('guru.laporan-hafalan')
    ? 'bg-green-900 text-white border-l-4 border-yellow-400 shadow-inner'
    : 'text-green-100 hover:bg-green-700 hover:text-white hover:pl-6' }}">
                            Laporan Hafalan
                        </a>
                    </li>

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
                                    {{ Auth::user()->nama_lengkap ?? 'Guru' }}
                                </div>
                                <div class="text-xs text-gray-400">Guru Al-Qur'an</div>
                            </div>
                            <div
                                class="w-9 h-9 md:w-10 md:h-10 rounded-full bg-gradient-to-tr from-green-600 to-green-400 text-white flex items-center justify-center font-bold text-base md:text-lg shadow-md ring-2 ring-white group-hover:ring-green-100 transition-all">
                                {{ substr(Auth::user()->nama_lengkap ?? 'G', 0, 1) }}
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

                            <a href="{{ route('guru.ganti-password') }}"
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

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50"
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

                    <footer class="bg-green-800 text-white border-t border-green-700 pt-8 pb-6 mt-auto">
                        <div class="container mx-auto px-4 md:px-6">

                            <div class="flex flex-col md:flex-row justify-between gap-8 mb-8">
                                <div class="flex-1">
                                    <h5 class="font-bold text-white text-lg mb-2">SD Islam Al-Azhar 27 Cibinong</h5>

                                    <div class="text-sm text-green-100 mb-4 max-w-md leading-relaxed">
                                        <span class="block font-medium text-white">Aplikasi Manajemen Hafalan
                                            Al-Qur'an</span>
                                        <a href="https://www.google.com/maps/search/SD+Islam+Al-Azhar+27+Cibinong"
                                            target="_blank" rel="noopener noreferrer"
                                            class="hover:text-white hover:underline transition-colors block mt-1 flex items-center gap-1">
                                            Jl. Raya Jakarta - Bogor Km. 44, Pakansari, Cibinong, Bogor 16915
                                        </a>
                                    </div>

                                    <div class="flex flex-col sm:flex-row gap-3 sm:gap-6 text-sm text-green-100">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-green-300" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                                </path>
                                            </svg>
                                            (021) 87915527
                                        </div>
                                        <a href="mailto:sdia27@al-azhar.sch.id"
                                            class="flex items-center gap-2 hover:text-white hover:underline transition-colors group">
                                            <svg class="w-4 h-4 text-green-300 group-hover:text-white" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                            sdia27@al-azhar.sch.id
                                        </a>
                                    </div>
                                </div>

                                <div class="text-left md:text-right flex flex-col justify-end">
                                    <p class="text-xs text-green-300 uppercase tracking-wider mb-1 font-semibold">
                                        Dikembangkan oleh</p>
                                    <p class="font-bold text-white text-base">Tim Hafizuna</p>
                                    <p class="text-sm text-green-100 font-medium">Politeknik Statistika STIS</p>
                                </div>
                            </div>

                            <div
                                class="border-t border-green-700 pt-6 flex flex-col md:flex-row justify-between items-center text-xs text-green-200 gap-2">
                                <p>© {{ date('Y') }} Hafizuna App. All rights reserved.</p>
                                <div class="flex items-center gap-2">
                                    <a href="https://www.instagram.com/sdialazhar27cibinong/" target="_blank"
                                        rel="noopener noreferrer"
                                        class="flex items-center gap-2 hover:text-white transition-colors group">
                                        <svg class="w-4 h-4 text-green-300 group-hover:text-white" fill="currentColor"
                                            viewBox="0 0 24 24" aria-hidden="true">
                                            <path fill-rule="evenodd"
                                                d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772 4.902 4.902 0 011.772-1.153c.636-.247 1.363-.416 2.427-.465 1.067-.047 1.407-.06 4.123-.06h.08zm-1.634 9a2.634 2.634 0 100 5.268 2.634 2.634 0 000-5.268zM12 7.333a4.667 4.667 0 110 9.334 4.667 4.667 0 010-9.334zm7.333-3.083a1.25 1.25 0 11-2.5 0 1.25 1.25 0 012.5 0z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="font-medium">@sdialazhar27cibinong</span>
                                    </a>
                                </div>
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