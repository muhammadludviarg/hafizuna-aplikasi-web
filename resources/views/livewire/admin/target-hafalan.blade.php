<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Target Hafalan</h2>
        <p class="text-gray-600 mt-1">Atur target hafalan untuk setiap kelompok</p>
    </div>

    <!-- Toast notification moved to top-right corner -->
    @if ($showSuccessToast)
        <div class="fixed top-6 right-6 z-50 animate-slide-in" x-data
            x-init="setTimeout(() => $wire.set('showSuccessToast', false), 3000)" @scroll-to-top="$el.remove()">
            <div class="flex items-center gap-3 px-4 py-3 rounded-lg shadow-lg bg-white border-l-4"
                :class="'{{ $toastType }}' === 'success' ? 'border-green-500 bg-green-50' : 'border-red-500 bg-red-50'">

                @if ($toastType === 'success')
                    <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm font-medium text-green-800">{{ $successMessage }}</span>
                @else
                    <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 001.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293a1 1 0 000-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm font-medium text-red-800">{{ $successMessage }}</span>
                @endif

                <button @click="$wire.set('showSuccessToast', false)" class="ml-auto text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
    @endif

    <!-- Target List Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Target Hafalan yang Telah Diatur</h3>
                <p class="text-sm text-gray-600 mt-1">Daftar target hafalan untuk setiap kelompok</p>
            </div>
            <div class="flex items-center gap-3">
                <span
                    class="inline-flex items-center justify-center w-8 h-8 text-sm font-bold text-white bg-green-600 rounded-full">
                    {{ count($daftarTarget) }}
                </span>
                <button wire:click="openCreateForm"
                    class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 shadow-md hover:shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Target
                </button>
            </div>
        </div>

        @if($daftarTarget->isEmpty())
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                <p class="text-gray-500 font-medium">Belum ada target hafalan yang diatur</p>
                <p class="text-gray-400 text-sm mt-1">Klik tombol 'Tambah Target' untuk membuat target baru</p>
            </div>
        @else
            <div class="space-y-3" id="targetList">
                @foreach($daftarTarget as $target)
                    <div
                        class="group border border-gray-200 rounded-lg p-4 hover:border-green-300 hover:shadow-md transition duration-200 bg-gradient-to-r from-white to-green-50 hover:to-green-100">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1">
                                <div class="mb-2">
                                    <h4 class="font-bold text-gray-900 group-hover:text-green-700 transition text-base">
                                        {{ $target->kelompok->kelas->nama_kelas ?? 'Tanpa Kelas' }}
                                    </h4>
                                    <p class="text-sm text-gray-600 mt-1">
                                        <span class="font-medium">Kelompok:</span>
                                        {{ $target->kelompok->nama_kelompok ?? 'Kelompok ' . $target->kelompok->id_kelompok }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        <span class="font-medium">Pembimbing:</span>
                                        {{ $target->kelompok->guru->akun->nama_lengkap ?? 'N/A' }}
                                    </p>
                                </div>

                                <div class="flex flex-wrap gap-2 mt-3">
                                    <span
                                        class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded font-medium">
                                        {{ $target->periode->label ?? 'Tanpa Label' }}
                                    </span>
                                    <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded font-medium">
                                        {{ $target->tanggal_mulai->format('d/m/Y') }} -
                                        {{ $target->tanggal_selesai->format('d/m/Y') }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex gap-2 ml-4 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button wire:click="edit({{ $target->id_target }})"
                                    class="p-2 text-gray-500 hover:text-green-600 hover:bg-green-100 rounded-lg transition duration-200"
                                    title="Edit target">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                </button>

                                <button wire:click="hapus({{ $target->id_target }})"
                                    wire:confirm="Yakin ingin menghapus target ini?"
                                    class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-100 rounded-lg transition duration-200"
                                    title="Hapus target">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="text-sm text-gray-600 border-t border-gray-200 pt-3 mt-3">
                            <p>
                                <strong class="text-gray-800">Target Surah:</strong>
                                <span class="text-green-700 font-medium">
                                    @if($target->surahAwal && $target->surahAkhir)
                                        @if($target->surahAwal->id_surah == $target->surahAkhir->id_surah)
                                            {{ $target->surahAwal->nama_surah }}
                                        @else
                                            {{ $target->surahAwal->nama_surah }} - {{ $target->surahAkhir->nama_surah }}
                                        @endif
                                    @endif
                                </span>
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Form converted to modal popup -->
    @if ($showModal)
        <!-- Modal Overlay -->
        <div class="fixed inset-0 bg-black bg-opacity-50 z-40 transition-opacity" wire:click="resetForm"></div>

        <!-- Modal Container -->
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto" @click.stop>
                <!-- Modal Header -->
                <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">
                        {{ $isEditing ? 'Edit Target Hafalan' : 'Tambah Target Hafalan Baru' }}
                    </h3>
                    <button wire:click="resetForm" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="px-6 py-4">
                    <form wire:submit.prevent="simpanTarget" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Pilih Kelas -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Kelas</label>
                                <select wire:model.live="id_kelas"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500 transition-colors">
                                    <option value="">Pilih kelas terlebih dahulu</option>
                                    @foreach($daftarKelas as $kelas)
                                        <option value="{{ $kelas->id_kelas }}">
                                            {{ $kelas->nama_kelas }} ({{ $kelas->tahun_ajaran }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_kelas') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Pilih Periode -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Periode</label>
                                <select wire:model.live="id_periode"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500 transition-colors">
                                    <option value="">-- Pilih Periode --</option>
                                    @foreach($daftarPeriode as $periode)
                                        <option value="{{ $periode->id_periode }}">
                                            {{ $periode->label }}
                                            @if($periode->is_active) <strong>(AKTIF)</strong> @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_periode') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Pilih Kelompok -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Kelompok</label>
                                <select wire:model="id_kelompok"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500 transition-colors">
                                    <option value="">-- Pilih Kelompok --</option>
                                    @foreach ($daftarKelompok as $kelompok)
                                        <option value="{{ $kelompok->id_kelompok }}" {{ $kelompok->has_target && !$isEditing ? 'disabled' : '' }}>
                                            {{ $kelompok->nama_kelompok ?? 'Kelompok ' . $kelompok->id_kelompok }} -
                                            {{ $kelompok->guru->akun->nama_lengkap ?? 'N/A' }}
                                            @if($kelompok->has_target && !$isEditing)
                                                - Target sudah dibuat
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_kelompok') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span>
                                @enderror
                                <p class="text-xs text-gray-500 mt-2">Kelompok yang sudah memiliki target ditandai "(Target
                                    sudah dibuat)"</p>
                            </div>

                            <!-- Tanggal Mulai -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                                <input type="date" value="{{ $tanggal_mulai }}"
                                    wire:change="$set('tanggal_mulai', $event.target.value)"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500 transition-colors cursor-pointer">
                                @error('tanggal_mulai') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Tanggal Selesai -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
                                <input type="date" value="{{ $tanggal_selesai }}"
                                    wire:change="$set('tanggal_selesai', $event.target.value)"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500 transition-colors cursor-pointer">
                                @error('tanggal_selesai') <span
                                class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Surah Awal -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Surah Awal</label>
                                <select wire:model="id_surah_awal"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500 transition-colors">
                                    <option value="">Pilih surah awal</option>
                                    @foreach($daftarSurah as $surah)
                                        <option value="{{ $surah->id_surah }}">
                                            {{ $surah->nomor_surah }}. {{ $surah->nama_surah }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_surah_awal') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Surah Akhir -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Surah Akhir</label>
                                <select wire:model="id_surah_akhir"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500 transition-colors">
                                    <option value="">Pilih surah akhir</option>
                                    @foreach($daftarSurah as $surah)
                                        <option value="{{ $surah->id_surah }}">
                                            {{ $surah->nomor_surah }}. {{ $surah->nama_surah }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_surah_akhir') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="border-t border-gray-200 pt-4 flex gap-3 justify-end">
                            <button type="button" wire:click="resetForm"
                                class="inline-flex items-center gap-2 bg-gray-500 hover:bg-gray-600 active:bg-gray-700 text-white font-medium py-2 px-6 rounded-lg transition duration-200 shadow-md hover:shadow-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Batal
                            </button>

                            <button type="submit"
                                class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 active:bg-green-800 text-white font-medium py-2 px-6 rounded-lg transition duration-200 shadow-md hover:shadow-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                {{ $isEditing ? 'Update Target' : 'Simpan Target' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <style>
        @keyframes slide-in {
            from {
                transform: translateX(400px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .animate-slide-in {
            animation: slide-in 0.3s ease-out forwards;
        }
    </style>
</div>