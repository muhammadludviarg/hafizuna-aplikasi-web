<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Hafizuna') }} - Atur Ulang Password</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex">

        {{-- BAGIAN KIRI (WARNA SOLID) - Sesuai dengan Lupa Password --}}
        <div class="hidden lg:block lg:w-1/2 bg-green-700 relative">
            <div class="absolute inset-0 flex flex-col justify-center items-center p-12 text-white">
                {{-- Logo --}}
                <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253">
                    </path>
                </svg>

                <h1 class="text-5xl font-bold text-center mt-4">HAFIZUNA</h1>
                <p class="mt-2 text-xl text-green-200 text-center">
                    Atur ulang sandi Anda dengan aman.
                </p>
            </div>
        </div>

        {{-- BAGIAN KANAN (FORM RESET PASSWORD) --}}
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12 bg-white">
            <div class="max-w-md w-full">

                <h2 class="text-3xl font-bold text-center text-gray-900">
                    Atur Ulang Kata Sandi
                </h2>
                <p class="text-center text-gray-600 mt-2 mb-4">
                    Masukkan alamat email, password baru, dan konfirmasi.
                </p>

                <x-auth-session-status class="my-4" :status="session('status')" />

                <form method="POST" action="{{ route('password.store') }}" class="mt-8 space-y-6">
                    @csrf

                    <!-- Token Reset Password -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <!-- Email Address -->
                    <div>
                        <x-input-label for="email" value="Email" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                            :value="old('email', $request->email)" required autofocus autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password Baru -->
                    <div class="mt-4">
                        <x-input-label for="password" value="Password Baru" />
                        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password"
                            required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Konfirmasi Password -->
                    <div class="mt-4">
                        <x-input-label for="password_confirmation" value="Konfirmasi Password" />
                        <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                            name="password_confirmation" required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    {{-- Tombol Reset (Hijau) --}}
                    <div class="flex items-center justify-end mt-4">
                        <x-primary-button class="w-full justify-center text-lg py-3 
                                                    bg-green-700 hover:bg-green-800 
                                                    focus:bg-green-700 active:bg-green-900 
                                                    focus:ring-green-500">
                            {{ __('Atur Ulang Password') }}
                        </x-primary-button>
                    </div>

                    {{-- Tautan Kembali ke Login --}}
                    <div class="text-center mt-4">
                        <a class="text-sm text-green-600 hover:text-green-800 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                            href="{{ route('login') }}">
                            &larr; Kembali ke Login
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>