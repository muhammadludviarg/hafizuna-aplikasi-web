<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Target Hafalan</h2>
        <p class="text-gray-600 mt-1">Atur target hafalan untuk setiap kelompok</p>
    </div>

    <!-- Flash Message -->
    @if (session()->has('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Form Tambah/Edit Target -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-lg font-semibold mb-4">
            {{ $isEditing ? 'Edit Target Hafalan' : 'Tambah Target Hafalan' }}
        </h3>

        <form wire:submit.prevent="simpanTarget">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Pilih Kelas -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Kelas</label>
                    <select wire:model.live="id_kelas"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500">
                        <option value="">Pilih kelas terlebih dahulu</option>
                        @foreach($daftarKelas as $kelas)
                            <option value="{{ $kelas->id_kelas }}" {{ $id_kelas == $kelas->id_kelas ? 'selected' : '' }}>
                                {{ $kelas->nama_kelas }} ({{ $kelas->tahun_ajaran }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Pilih Kelompok -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Kelompok</label>
                    <select wire:model="selectedKelompok" id="kelompok"
                        class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 transition duration-200">
                        <option value="">-- Pilih Kelompok --</option>
                        @foreach ($kelompokList as $kelompok)
                            <option value="{{ $kelompok->id_kelompok }}">
                                {{-- Perbaikan Logika Tampilan: --}}
                                {{-- Gunakan nama/ID kelompok dulu, baru ditambahkan info kelasnya --}}
                                {{ $kelompok->nama_kelompok ?? 'Kelompok ' . $kelompok->id_kelompok }} -
                                {{ $kelompok->kelas->nama_kelas ?? 'Tanpa Kelas' }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_kelompok') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <!-- Periode -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Periode</label>
                    <input type="text" wire:model="periode" value="{{ $periode }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500"
                        placeholder="Semester 1 2024/2025">
                    @error('periode') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <!-- Spacer -->
                <div></div>

                <!-- Tanggal Mulai -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                    <input type="date" value="{{ $tanggal_mulai }}"
                        wire:change="$set('tanggal_mulai', $event.target.value)"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500">
                    @error('tanggal_mulai') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <!-- Tanggal Selesai -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
                    <input type="date" value="{{ $tanggal_selesai }}"
                        wire:change="$set('tanggal_selesai', $event.target.value)"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500">
                    @error('tanggal_selesai') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <!-- Surah Awal -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Surah Awal</label>
                    <select wire:model="id_surah_awal"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500">
                        <option value="">Pilih surah awal</option>
                        @foreach($daftarSurah as $surah)
                            <option value="{{ $surah->id_surah }}" {{ $id_surah_awal == $surah->id_surah ? 'selected' : '' }}>
                                {{ $surah->nomor_surah }}. {{ $surah->nama_surah }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_surah_awal') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <!-- Surah Akhir -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Surah Akhir</label>
                    <select wire:model="id_surah_akhir"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500">
                        <option value="">Pilih surah akhir</option>
                        @foreach($daftarSurah as $surah)
                            <option value="{{ $surah->id_surah }}" {{ $id_surah_akhir == $surah->id_surah ? 'selected' : '' }}>
                                {{ $surah->nomor_surah }}. {{ $surah->nama_surah }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_surah_akhir') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Buttons -->
            <div class="mt-6 flex gap-3">
                <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-6 rounded-lg transition duration-200 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    {{ $isEditing ? 'Update Target' : 'Simpan Target' }}
                </button>

                @if($isEditing)
                    <button type="button" wire:click="resetForm"
                        class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-6 rounded-lg transition duration-200">
                        Batal
                    </button>
                @endif
            </div>
        </form>
    </div>

    <!-- Daftar Target yang Telah Diatur -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold mb-2">Target Hafalan yang Telah Diatur</h3>
        <p class="text-sm text-gray-600 mb-6">Daftar target hafalan untuk setiap kelompok</p>

        @if($daftarTarget->isEmpty())
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                <p class="text-gray-500">Belum ada target hafalan yang diatur</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($daftarTarget as $target)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition duration-200">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h4 class="font-semibold text-gray-800">{{ $target->nama_kelompok_display }}</h4>
                                <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded mt-1">
                                    {{ $target->periode }}
                                </span>
                            </div>
                            <button wire:click="edit({{ $target->id_target }})"
                                class="text-gray-500 hover:text-green-600 transition duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                            </button>
                        </div>

                        <div class="text-sm text-gray-600">
                            <p class="mb-1">
                                <strong>Target:</strong>
                                @if($target->surahAwal && $target->surahAkhir)
                                    @if($target->surahAwal->id_surah == $target->surahAkhir->id_surah)
                                        {{ $target->surahAwal->nama_surah }}
                                    @else
                                        {{ $target->surahAwal->nama_surah }} - {{ $target->surahAkhir->nama_surah }}
                                    @endif
                                @endif
                            </p>
                            <p>
                                <strong>Periode:</strong>
                                {{ $target->tanggal_mulai->format('d/m/Y') }} - {{ $target->tanggal_selesai->format('d/m/Y') }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>