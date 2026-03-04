<div>
    @section('header', 'Pengaturan Email Akun')

    <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mt-4">
        <div class="p-6 md:p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-green-100 text-green-700 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Ganti Email Pribadi</h3>
                    <p class="text-sm text-gray-500">Email saat ini: <span class="font-medium text-gray-700">{{ Auth::user()->email }}</span></p>
                </div>
            </div>

            @if($statusPesan)
                <div class="p-4 mb-6 text-sm text-green-800 bg-green-50 border border-green-200 rounded-lg flex items-start gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ $statusPesan }}</span>
                </div>
            @endif

            <form wire:submit.prevent="requestPerubahan" class="space-y-5">
                <div>
                    <label for="email_baru" class="block text-sm font-medium text-gray-700 mb-1">Email Baru</label>
                    <input type="email" wire:model="email_baru" id="email_baru" placeholder="contoh: nama@gmail.com" 
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 transition-colors">
                    @error('email_baru') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="inline-flex justify-center px-6 py-2.5 text-sm font-medium text-white bg-green-700 border border-transparent rounded-lg hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors shadow-sm">
                        Kirim Link Verifikasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>