<x-guest-layout bg-image="login3.jpg">
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-gray-900">Lupa Kata Sandi?</h2>
        <p class="text-sm text-gray-600 mt-2 leading-relaxed">
            Jangan khawatir. Masukkan alamat email Anda, dan kami akan mengirimkan tautan untuk mengatur ulang kata
            sandi.
        </p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email"
                class="block mt-1 w-full py-3 px-4 rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm transition-colors"
                type="email" name="email" :value="old('email')" required autofocus placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-primary-button class="w-full justify-center text-lg py-3 
                                                    bg-green-700 hover:bg-green-800 
                                                    focus:bg-green-700 active:bg-green-900 
                                                    focus:ring-green-500">
                {{ __('Kirim Tautan Reset') }}
            </x-primary-button>
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('login') }}"
                class="text-sm text-gray-500 hover:text-green-600 font-medium transition-colors flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke halaman login
            </a>
        </div>
    </form>
</x-guest-layout>