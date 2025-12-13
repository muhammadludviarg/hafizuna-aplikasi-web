<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Manajemen Periode</h2>
        <p class="text-gray-600 mt-1">Kelola tahun ajaran dan semester untuk target hafalan</p>
    </div>

    <!-- Using Livewire-specific toast component for consistency across all pages -->
    @if($showSuccessToast)
        <div class="fixed top-6 right-6 z-[9999] animate-slide-in">
            <div class="bg-white rounded-lg shadow-2xl border-l-4 {{ $toastType === 'success' ? 'border-green-500' : 'border-red-500' }} p-4 max-w-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        @if($toastType === 'success')
                            <svg class="h-6 w-6 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        @else
                            <svg class="h-6 w-6 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 001.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        @endif
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium {{ $toastType === 'success' ? 'text-gray-800' : 'text-red-800' }}">
                            {{ $successMessage }}
                        </p>
                    </div>
                    <button wire:click="$set('showSuccessToast', false)" class="ml-3 text-gray-400 hover:text-gray-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Add Button -->
    <div class="mb-6">
        <button wire:click="$set('showModal', true)"
            class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 shadow-md hover:shadow-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Tahun Ajaran Baru
        </button>
    </div>

    <!-- Daftar Periode -->
    <div class="bg-white rounded-lg shadow-md p-6">
        @if($daftarPeriode->isEmpty())
            <div class="text-center py-12">
                <p class="text-gray-500 font-medium">Belum ada periode yang terdaftar</p>
                <p class="text-gray-400 text-sm mt-1">Klik tombol 'Tambah Tahun Ajaran Baru' untuk memulai</p>
            </div>
        @else
            <div class="space-y-4">
                @php
                    $groupedByTahun = $daftarPeriode->groupBy('tahun_ajaran');
                @endphp

                @foreach($groupedByTahun as $tahun => $periodes)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-bold text-gray-800">Tahun Ajaran {{ $tahun }}</h3>
                            <div class="flex gap-2">
                                @if($periodes->count() < 2)
                                    <button wire:click="tambahSemesterYangKurang('{{ $tahun }}')"
                                        class="text-blue-600 hover:text-blue-800 hover:bg-blue-50 px-3 py-1 rounded text-sm transition font-medium">
                                        Tambah Semester
                                    </button>
                                @endif
                                @php
                                    $canDeleteYear = $periodes->every(function($p) { return $p->targetHafalan()->count() === 0; });
                                @endphp
                                @if($canDeleteYear)
                                    <button wire:click="hapusTahunAjaran('{{ $tahun }}')" 
                                        wire:confirm="Yakin ingin menghapus tahun ajaran ini? Semua semester akan dihapus."
                                        class="text-red-600 hover:text-red-800 hover:bg-red-50 px-3 py-1 rounded text-sm transition font-medium">
                                        Hapus
                                    </button>
                                @else
                                    <button disabled
                                        title="Tidak bisa dihapus karena sudah ada target hafalan"
                                        class="text-gray-400 cursor-not-allowed px-3 py-1 rounded text-sm font-medium">
                                        Hapus
                                    </button>
                                @endif
                            </div>
                        </div>

                        <div class="space-y-2">
                            @foreach($periodes as $periode)
                                <div class="flex items-center justify-between bg-gray-50 p-3 rounded">
                                    <div class="flex items-center gap-3">
                                        <span class="font-medium text-gray-700">{{ $periode->label }}</span>
                                        @if($periode->is_active)
                                            <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded font-medium">
                                                Aktif
                                            </span>
                                        @endif
                                    </div>

                                    <div class="flex gap-2">
                                        @if(!$periode->is_active)
                                            <button wire:click="setAktif({{ $periode->id_periode }})"
                                                class="text-blue-600 hover:text-blue-800 hover:bg-blue-50 px-3 py-1 rounded text-sm transition">
                                                Aktifkan
                                            </button>
                                        @endif
                                        @if($periode->targetHafalan()->count() === 0)
                                            <button wire:click="hapusPeriode({{ $periode->id_periode }})" 
                                                wire:confirm="Yakin ingin menghapus semester ini?"
                                                class="text-red-600 hover:text-red-800 hover:bg-red-50 px-3 py-1 rounded text-sm transition">
                                                Hapus
                                            </button>
                                        @else
                                            <button disabled
                                                title="Tidak bisa dihapus karena sudah ada target hafalan"
                                                class="text-gray-400 cursor-not-allowed px-3 py-1 rounded text-sm">
                                                Hapus
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Modal -->
    @if ($showModal)
        <!-- Modal Overlay -->
        <div class="fixed inset-0 bg-black bg-opacity-50 z-40 transition-opacity" wire:click="resetForm"></div>

        <!-- Modal Container - Positioned to top-right -->
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-2xl max-w-md w-full" @click.stop>
                <!-- Modal Header -->
                <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">Tambah Tahun Ajaran Baru</h3>
                    <button wire:click="resetForm" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="px-6 py-4">
                    <form wire:submit.prevent="tambahTahunAjaran" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Ajaran</label>
                            <input type="text" 
                                wire:model="tahun_ajaran"
                                placeholder="Contoh: 2025/2026"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500 transition-colors">
                            @error('tahun_ajaran') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
                            <p class="text-xs text-gray-500 mt-2">Format: YYYY/YYYY (contoh: 2025/2026)</p>
                        </div>

                        <div class="border-t border-gray-200 pt-4 flex gap-3 justify-end">
                            <button type="button" wire:click="resetForm"
                                class="inline-flex items-center gap-2 bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-6 rounded-lg transition duration-200">
                                Batal
                            </button>

                            <button type="submit"
                                class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-6 rounded-lg transition duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Tambah
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
