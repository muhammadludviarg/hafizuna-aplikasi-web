<div class="min-h-screen bg-gray-50 flex items-center justify-center px-4 py-8">
    <div class="w-full max-w-2xl">
        <!-- Header - RATA KIRI -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Ganti Password</h2>
            <p class="text-gray-600 mt-1">Ubah password akun Anda untuk keamanan yang lebih baik</p>
        </div>

        <!-- Moved flash message to top-right fixed position for consistency across all roles -->
        <div class="fixed top-6 right-6 z-[9999] w-96 space-y-4 pointer-events-none">
            @if (session()->has('success'))
                <div class="bg-green-50 border-l-4 border-green-500 text-green-800 p-4 rounded-lg shadow-lg flex justify-between items-start animate-fade-in-down transition-all duration-500 pointer-events-auto">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0 mt-0.5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <strong class="font-semibold">Berhasil!</strong>
                            <p class="text-sm mt-1">{{ session('success') }}</p>
                        </div>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900 font-bold ml-2">×</button>
                </div>
            @endif
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-lg shadow-lg border border-gray-200 p-8">
            <!-- Info Keamanan Akun - BACKGROUND HIJAU -->
            <div class="mb-6 bg-green-50 border border-green-200 p-4 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-green-700 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <h3 class="font-semibold text-green-900 mb-1">Keamanan Akun</h3>
                        <p class="text-sm text-green-800">
                            Email: <strong>{{ Auth::user()->email }}</strong>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form wire:submit.prevent="gantiPassword">
                <!-- Password Lama -->
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password Lama</label>
                    <div class="relative">
                        <input type="{{ $showPasswordLama ? 'text' : 'password' }}" 
                               wire:model="password_lama" 
                               class="w-full px-4 py-3 pr-12 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition @error('password_lama') border-red-400 bg-red-50 @else border-gray-300 @enderror"
                               placeholder="••••••••">
                        
                        <!-- Toggle Show/Hide Password -->
                        <button type="button" 
                                wire:click="$toggle('showPasswordLama')"
                                class="absolute right-3 top-3.5 text-gray-400 hover:text-gray-600 focus:outline-none transition">
                            @if($showPasswordLama)
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                </svg>
                            @endif
                        </button>
                    </div>
                    @error('password_lama') 
                        <div class="mt-2 flex items-start text-sm text-red-600">
                            <svg class="w-4 h-4 mr-1.5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Password Baru -->
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                    <div class="relative">
                        <input type="{{ $showPasswordBaru ? 'text' : 'password' }}" 
                               wire:model.live="password_baru" 
                               class="w-full px-4 py-3 pr-12 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition @error('password_baru') border-red-400 bg-red-50 @else border-gray-300 @enderror"
                               placeholder="Masukkan password baru">
                        
                        <button type="button" 
                                wire:click="$toggle('showPasswordBaru')"
                                class="absolute right-3 top-3.5 text-gray-400 hover:text-gray-600 focus:outline-none transition">
                            @if($showPasswordBaru)
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                </svg>
                            @endif
                        </button>
                    </div>
                    @error('password_baru') 
                        <div class="mt-2 flex items-start text-sm text-red-600">
                            <svg class="w-4 h-4 mr-1.5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </div>
                    @enderror
                    
                    <!-- Password Strength Indicator -->
                    @if($showPasswordStrength)
                        <div class="mt-3 p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <p class="text-xs font-semibold text-gray-700 mb-3">Kekuatan Password:</p>
                            <ul class="space-y-2 text-xs">
                                <li class="flex items-center {{ $passwordStrength['length'] ? 'text-green-700' : 'text-gray-500' }}">
                                    @if($passwordStrength['length'])
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                    Minimal 8 karakter
                                </li>
                                <li class="flex items-center {{ $passwordStrength['uppercase'] ? 'text-green-700' : 'text-gray-500' }}">
                                    @if($passwordStrength['uppercase'])
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                    Minimal 1 huruf besar
                                </li>
                                <li class="flex items-center {{ $passwordStrength['number'] ? 'text-green-700' : 'text-gray-500' }}">
                                    @if($passwordStrength['number'])
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                    Minimal 1 angka
                                </li>
                                <li class="flex items-center {{ $passwordStrength['symbol'] ? 'text-green-700' : 'text-gray-500' }}">
                                    @if($passwordStrength['symbol'])
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                    Minimal 1 simbol (@$!%*#?&)
                                </li>
                            </ul>
                        </div>
                    @endif
                </div>

                <!-- Konfirmasi Password Baru -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password Baru</label>
                    <div class="relative">
                        <input type="{{ $showPasswordKonfirmasi ? 'text' : 'password' }}" 
                               wire:model="password_baru_confirmation" 
                               class="w-full px-4 py-3 pr-12 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition @error('password_baru_confirmation') border-red-400 bg-red-50 @enderror @error('password_baru') border-red-400 bg-red-50 @enderror"
                               placeholder="Ketik ulang password baru">
                        
                        <button type="button" 
                                wire:click="$toggle('showPasswordKonfirmasi')"
                                class="absolute right-3 top-3.5 text-gray-400 hover:text-gray-600 focus:outline-none transition">
                            @if($showPasswordKonfirmasi)
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                </svg>
                            @endif
                        </button>
                    </div>
                    @error('password_baru_confirmation') 
                        <div class="mt-2 flex items-start text-sm text-red-600">
                            <svg class="w-4 h-4 mr-1.5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </div>
                    @enderror
                    @error('password_baru') 
                        @if($message === 'Konfirmasi password tidak cocok dengan password baru')
                            <div class="mt-2 flex items-start text-sm text-red-600">
                                <svg class="w-4 h-4 mr-1.5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </div>
                        @endif
                    @enderror
                </div>

                <!-- Button -->
                <button type="submit" 
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3.5 px-6 rounded-lg transition duration-200 flex items-center justify-center shadow-md hover:shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                    </svg>
                    Ubah Password
                </button>
            </form>
        </div>
    </div>
</div>
