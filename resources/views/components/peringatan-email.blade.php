@if (auth()->check() && \Illuminate\Support\Str::endsWith(auth()->user()->email, '@hafizuna.com'))
    <div x-data="{ show: true }" x-show="show" x-transition.opacity
        class="mb-6 bg-orange-50 border-l-4 border-orange-500 p-4 rounded-r-lg shadow-sm flex items-start justify-between">
        <div class="flex items-start gap-3">
            <div class="p-1 bg-orange-100 rounded-full text-orange-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                    </path>
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-orange-800">Saran Keamanan Akun</h3>
                <p class="text-sm text-orange-700 mt-1 leading-relaxed">
                    Saat ini Anda login menggunakan email bawaan (<span
                        class="font-mono bg-orange-100 px-1 rounded">{{ auth()->user()->email }}</span>).
                    Demi kemudahan memulihkan *password* di masa depan, silakan ganti ke alamat email pribadi Anda.
                </p>
                <div class="mt-3">
                    <a href="{{ route('ganti-email') }}"
                        class="inline-flex items-center justify-center px-4 py-1.5 text-xs font-bold text-white bg-orange-600 rounded-md hover:bg-orange-700 transition-colors shadow-sm">
                        Ganti Email Sekarang
                    </a>
                </div>
            </div>
        </div>
        <button @click="show = false" class="text-orange-400 hover:text-orange-600 p-1">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
@endif