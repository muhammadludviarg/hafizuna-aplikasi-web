<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Pengaturan Sistem Penilaian</h2>
        <p class="text-gray-600 mt-1">Kelola kriteria penilaian hafalan berdasarkan proporsi kesalahan</p>
    </div>

    <!-- Flash Message -->
    @if (session()->has('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @error('bentrok')
        <div class="mt-4 mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md" role="alert">
            <strong class="font-bold">Validasi Gagal!</strong>
            <span class="block sm:inline">{{ $message }}</span>
        </div>
    @enderror

    <!-- Info Box -->
    <div class="mb-6 bg-blue-50 p-4">
        <div>
            <p class="font-bold text-blue-800 mb-1">Formula Penilaian:</p>
            <p class="text-sm text-blue-700">
                <strong>Nilai Numerik = 100 - Proporsi Kesalahan (%)</strong><br>
            </p>
        </div>
    </div>


    <!-- Main Content -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold mb-2">Kriteria Grade Berdasarkan Proporsi Kesalahan</h3>
        <p class="text-sm text-gray-600 mb-6">Atur rentang proporsi kesalahan (%) terhadap total kata untuk setiap grade (A, B, C)</p>

        @foreach(['kelancaran', 'tajwid', 'makhroj'] as $aspek)
            <div class="mb-8 pb-8 {{ $loop->last ? '' : 'border-b border-gray-200' }}">
                <h4 class="text-md font-semibold mb-2 capitalize flex items-center">
                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm mr-2">
                        Aspek {{ ucfirst($aspek) }}
                    </span>
                </h4>
                <p class="text-sm text-gray-600 mb-4">
                    @if($aspek === 'kelancaran')
                        Proporsi kesalahan kelancaran terhadap total kata (dalam %)
                    @elseif($aspek === 'tajwid')
                        Proporsi kesalahan tajwid terhadap total kata (dalam %)
                    @else ($aspek === 'makhroj')
                        Proporsi kesalahan makhroj terhadap total kata (dalam %)
                    @endif
                </p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Grade A -->
                    <div class="border-2 border-green-200 bg-green-50 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <span class="bg-green-600 text-white text-xs font-bold px-2 py-1 rounded">Grade A</span>
                                <span class="ml-2 text-xs text-gray-600">(Sangat Baik)</span>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Proporsi Minimal (%)</label>
                                <input type="number" wire:model="settings.{{ $aspek }}.A.proporsi_min" step="0.01" min="0" max="100"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-green-500 focus:border-green-500">
                                @error('settings.'.$aspek.'.A.proporsi_min') 
                                    <span class="text-xs text-red-500">{{ $message }}</span> 
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Proporsi Maksimal (%)</label>
                                <input type="number" wire:model="settings.{{ $aspek }}.A.proporsi_max" step="0.01" min="0" max="100"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-green-500 focus:border-green-500">
                                @error('settings.'.$aspek.'.A.proporsi_max') 
                                    <span class="text-xs text-red-500">{{ $message }}</span> 
                                @enderror
                            </div>
                            <!-- Preview Nilai -->
                            @if(isset($settings[$aspek]['A']))
                                <div class="bg-green-100 p-2 rounded text-center text-xs">
                                    <p class="font-semibold text-green-800">Rentang Nilai:</p>
                                    <p class="text-lg font-bold text-green-700">
                                        {{ 100 - $settings[$aspek]['A']['proporsi_max'] }} - 
                                        {{ 100 - $settings[$aspek]['A']['proporsi_min'] }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Grade B -->
                    <div class="border-2 border-yellow-200 bg-yellow-50 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <span class="bg-yellow-600 text-white text-xs font-bold px-2 py-1 rounded">Grade B</span>
                                <span class="ml-2 text-xs text-gray-600">(Baik)</span>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Proporsi Minimal (%)</label>
                                <input type="number" wire:model="settings.{{ $aspek }}.B.proporsi_min" step="0.01" min="0" max="100"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-yellow-500 focus:border-yellow-500">
                                @error('settings.'.$aspek.'.B.proporsi_min') 
                                    <span class="text-xs text-red-500">{{ $message }}</span> 
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Proporsi Maksimal (%)</label>
                                <input type="number" wire:model="settings.{{ $aspek }}.B.proporsi_max" step="0.01" min="0" max="100"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-yellow-500 focus:border-yellow-500">
                                @error('settings.'.$aspek.'.B.proporsi_max') 
                                    <span class="text-xs text-red-500">{{ $message }}</span> 
                                @enderror
                            </div>
                            <!-- Preview Nilai -->
                            @if(isset($settings[$aspek]['B']))
                                <div class="bg-yellow-100 p-2 rounded text-center text-xs">
                                    <p class="font-semibold text-yellow-800">Rentang Nilai:</p>
                                    <p class="text-lg font-bold text-yellow-700">
                                        {{ 100 - $settings[$aspek]['B']['proporsi_max'] }} - 
                                        {{ 100 - $settings[$aspek]['B']['proporsi_min'] }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Grade C -->
                    <div class="border-2 border-red-200 bg-red-50 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <span class="bg-red-600 text-white text-xs font-bold px-2 py-1 rounded">Grade C</span>
                                <span class="ml-2 text-xs text-gray-600">(Perlu Perbaikan)</span>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Proporsi Minimal (%)</label>
                                <input type="number" wire:model="settings.{{ $aspek }}.C.proporsi_min" step="0.01" min="0" max="100"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-red-500 focus:border-red-500">
                                @error('settings.'.$aspek.'.C.proporsi_min') 
                                    <span class="text-xs text-red-500">{{ $message }}</span> 
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Proporsi Maksimal (%)</label>
                                <input type="number" wire:model="settings.{{ $aspek }}.C.proporsi_max" step="0.01" min="0" max="100"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-red-500 focus:border-red-500">
                                @error('settings.'.$aspek.'.C.proporsi_max') 
                                    <span class="text-xs text-red-500">{{ $message }}</span> 
                                @enderror
                            </div>
                            <!-- Preview Nilai -->
                            @if(isset($settings[$aspek]['C']))
                                <div class="bg-red-100 p-2 rounded text-center text-xs">
                                    <p class="font-semibold text-red-800">Rentang Nilai:</p>
                                    <p class="text-lg font-bold text-red-700">
                                        {{ 100 - $settings[$aspek]['C']['proporsi_max'] }} - 
                                        {{ 100 - $settings[$aspek]['C']['proporsi_min'] }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-between items-center">
        <button wire:click="resetKeDefault" 
                class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-6 rounded-lg transition duration-200">
            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Reset ke Default
        </button>
        <button wire:click="simpanPengaturan" 
                class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-6 rounded-lg transition duration-200">
            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            Simpan Pengaturan
        </button>
    </div>
</div>