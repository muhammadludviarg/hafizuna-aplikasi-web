<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Login - {{ config('app.name', 'Hafizuna') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex">
            <!-- Left Side - Green Section -->
            <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-b from-green-400 to-green-600 flex-col justify-center items-center p-12">
                <div class="text-center">
                    <!-- Logo -->
                    <div class="mb-8 inline-flex items-center justify-center w-24 h-24 bg-green-500 rounded-2xl">
                        <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z"></path>
                        </svg>
                    </div>
                    
                    <!-- Title -->
                    <h1 class="text-5xl font-bold text-white mb-2">HAFIZUNA</h1>
                    <p class="text-green-100 text-lg mb-12">Sistem Manajemen Hafalan Al-Qur'an</p>
                    
                    <!-- School Info Box -->
                    <div class="bg-green-500 rounded-3xl px-8 py-6 text-white border-2 border-green-300">
                        <h2 class="text-2xl font-bold mb-2">SD Islam Al-Azhar 27</h2>
                        <p class="text-green-100">Cibinong, Bogor</p>
                    </div>
                </div>
            </div>

            <!-- Right Side - Login Form -->
            <div class="w-full lg:w-1/2 flex flex-col justify-center items-center p-6 sm:p-12 bg-gray-50">
                <div class="w-full max-w-md">
                    <!-- Header -->
                    <h2 class="text-4xl font-bold text-gray-900 mb-3">Selamat Datang</h2>
                    <p class="text-gray-600 mb-8">Silakan masuk dengan akun Anda</p>

                    <!-- Login Form -->
                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf

                        <!-- Email Input -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </span>
                                <input 
                                    id="email" 
                                    type="email" 
                                    name="email" 
                                    value="{{ old('email') }}"
                                    placeholder="nama@hafizuna.sch.id"
                                    required
                                    class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition"
                                >
                            </div>
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password Input -->
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label for="password" class="block text-sm font-medium text-gray-700">Kata Sandi</label>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="text-sm text-green-600 hover:text-green-700 font-medium">
                                        Lupa Password?
                                    </a>
                                @endif
                            </div>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </span>
                                <input 
                                    id="password" 
                                    type="password" 
                                    name="password" 
                                    placeholder="••••••••"
                                    required
                                    class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition"
                                >
                            </div>
                            @error('password')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center">
                            <input 
                                id="remember_me" 
                                type="checkbox" 
                                name="remember"
                                class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-2 focus:ring-green-500"
                            >
                            <label for="remember_me" class="ml-2 text-sm text-gray-600">
                                Ingat saya
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 rounded-lg transition duration-200 shadow-md hover:shadow-lg">
                            Masuk
                        </button>
                    </form>

                    <!-- Register Link -->
                    <p class="text-center text-gray-600 mt-8">
                        Belum punya akun? 
                        <a href="{{ route('register') }}" class="text-green-600 hover:text-green-700 font-semibold">
                            Daftar di sini
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>
