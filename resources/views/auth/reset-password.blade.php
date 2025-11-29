<x-guest-layout bg-image="reset-bg.jpg">

    <div class="mb-8 text-center">
        <h2 class="text-2xl font-bold text-gray-900">Atur Ulang Kata Sandi</h2>
        <p class="text-sm text-gray-600 mt-2">Silakan buat kata sandi baru untuk akun Anda.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email"
                class="block mt-1 w-full py-3 px-4 rounded-lg border-gray-300 bg-gray-100 text-gray-500 cursor-not-allowed focus:border-gray-300 focus:ring-0"
                type="email" name="email" :value="old('email', $request->email)" required autofocus readonly />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div x-data="{ show: false }">
            <x-input-label for="password" :value="__('Kata Sandi Baru')" />
            <div class="relative mt-1">
                <x-text-input id="password"
                    class="block w-full py-3 px-4 pr-10 rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm transition-colors"
                    type="password" x-bind:type="show ? 'text' : 'password'" name="password" required
                    autocomplete="new-password" placeholder="Minimal 8 karakter" />

                <button type="button" @click="show = !show"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none"
                    tabindex="-1">
                    <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.057 10.057 0 01-3.955 3.844m-4.106-4.106L19 19" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div x-data="{ show: false }">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Kata Sandi')" />
            <div class="relative mt-1">
                <x-text-input id="password_confirmation"
                    class="block w-full py-3 px-4 pr-10 rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm transition-colors"
                    type="password" x-bind:type="show ? 'text' : 'password'" name="password_confirmation" required
                    autocomplete="new-password" placeholder="Ulangi kata sandi" />

                <button type="button" @click="show = !show"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none"
                    tabindex="-1">
                    <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.057 10.057 0 01-3.955 3.844m-4.106-4.106L19 19" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="pt-2">
            <x-primary-button
                class="w-full justify-center py-3 text-lg bg-green-700 hover:bg-green-800 focus:ring-green-500 shadow-lg transition-transform transform hover:-translate-y-0.5">
                {{ __('Reset Kata Sandi') }}
                </x-primary-button>


        </div>
    </form>
</x-guest-layout>