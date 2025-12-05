<div>
    <h2 class="text-2xl font-semibold text-gray-700">Input Data Hafalan</h2>
    <h4 class="text-sm text-gray-500 mb-6">
        Pilih kelompok, siswa, dan masukkan penilaian hafalan.
    </h4>

    <!-- Moved flash messages to top-right fixed position for consistency -->
    <div class="fixed top-6 right-6 z-[9999] w-96 space-y-4 pointer-events-none">
        @if (session()->has('message'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded shadow-lg flex justify-between items-start animate-fade-in-down transition-all duration-500 pointer-events-auto" role="alert">
                <span class="block sm:inline">{{ session('message') }}</span>
                <button onclick="this.parentElement.remove()" class="ml-auto font-bold">×</button>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded shadow-lg flex justify-between items-start animate-fade-in-down transition-all duration-500 pointer-events-auto" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
                <button onclick="this.parentElement.remove()" class="ml-auto font-bold">×</button>
            </div>
        @endif
    </div>

    @if($step == 1)
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold text-gray-700 border-b pb-2 mb-4">Langkah 1: Pilih Kelompok</h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse ($daftarKelompok as $kelompok)
                    <div wire:click="selectKelompok({{ $kelompok['id'] }})"
                        class="bg-white border-2 border-gray-200 rounded-lg p-5 md:p-6 cursor-pointer hover:shadow-lg hover:border-green-500 transition-all duration-200 active:bg-green-50 flex flex-col justify-between h-full group">
                        
                        <div>
                                    {{-- Nama Kelompok --}}
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="bg-green-100 text-green-800 text-xs font-bold px-2.5 py-0.5 rounded border border-green-200">Kelompok</span>
                                        <h4 class="text-lg font-bold text-gray-800">{{ $kelompok['nama_kelompok_utama'] }}</h4>
                                    </div>
                                    
                                    {{-- Nama Kelas Asal --}}
                                    <h5 class="text-md font-semibold text-gray-600 mb-1 flex items-center gap-1">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        {{ $kelompok['nama_kelas_kecil'] }}
                                    </h5>
                                </div>

                                <div class="flex items-center justify-between border-t border-gray-100 pt-4 mt-2">
                                    <span class="text-sm font-semibold text-gray-700 bg-gray-100 px-3 py-1 rounded-full">
                                        {{ $kelompok['jumlah_siswa'] }} siswa
                                    </span>
                                    <div class="bg-green-50 p-2 rounded-full text-green-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">Anda belum memiliki kelompok hafalan.</p>
                    </div>
                @endforelse
            </div>
        </div>
    @endif

    @if($step == 2)
        <div class="bg-white p-6 rounded-lg shadow-md">
            <!-- Header -->
            <div class="flex justify-between items-center border-b pb-2 mb-4">
                <h3 class="text-lg font-semibold text-gray-700">Langkah 2: Pilih Siswa</h3>
                <button wire:click="backStep(1)" class="flex items-center gap-2 text-green-600 hover:text-green-700 font-semibold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg> 
                    Kembali ke Kelompok
                </button>
            </div>

            <div class="mb-6">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="searchSiswa"
                        placeholder="Cari nama siswa..."
                        class="w-full pl-10 pr-10 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all"
                    >
                    
                    <!-- Clear Button -->
                    @if($searchSiswa)
                        <button 
                            wire:click="$set('searchSiswa', '')" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    @endif
                </div>
                
                <!-- Info Hasil -->
                @if($searchSiswa)
                    <p class="text-sm text-gray-600 mt-2">
                        Menampilkan <strong class="text-green-600">{{ $this->filteredSiswa->count() }}</strong> dari {{ $daftarSiswa->count() }} siswa
                    </p>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse ($this->filteredSiswa as $siswa)
                    <button wire:click="selectSiswa({{ $siswa->id_siswa }})"
                        class="p-4 bg-gray-50 rounded-lg shadow border border-gray-200 hover:bg-green-100 hover:border-green-400 transition-all text-left group">
                        
                        <div class="flex items-center gap-3">
                            <!-- Avatar -->
                            <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-md group-hover:scale-105 transition-transform">
                                {{ strtoupper(substr($siswa->nama_siswa, 0, 1)) }}
                            </div>
                            
                            <!-- Info -->
                            <div class="flex-1">
                                <h5 class="font-semibold text-gray-800 group-hover:text-green-700 transition-colors">{{ $siswa->nama_siswa }}</h5>
                                <p class="text-xs text-gray-500">{{ $siswa->kelas ? $siswa->kelas->nama_kelas : 'Tanpa Kelas' }}</p>
                            </div>
                            
                            <!-- Arrow -->
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-green-600 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </button>
                @empty
                    <div class="col-span-full text-center py-12 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">
                            @if($searchSiswa)
                                Tidak ada siswa yang sesuai dengan pencarian "{{ $searchSiswa }}"
                            @else
                                Tidak ada siswa dalam kelompok ini.
                            @endif
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    @endif

    @if($step == 3)
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex justify-between items-center border-b pb-2 mb-4">
                <h3 class="text-lg font-semibold text-gray-700">Langkah 3: Pilih Rentang Ayat (Siswa: {{ $selectedSiswaNama }})</h3>
                <button wire:click="backStep(2)" class="text-sm text-gray-500 hover:text-gray-800">&larr; Ganti Siswa</button>
            </div>

            @if($targetHafalanInfo)
                <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-blue-800">
                        <strong>Info:</strong> {{ $targetHafalanInfo }}
                    </p>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Surah</label>
                    <select wire:model.live="id_surah"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">-- Pilih Surah --</option>
                        @foreach($daftarSurah as $surah)
                            <option value="{{ $surah->id_surah }}">
                                {{ $surah->nomor_surah }}. {{ $surah->nama_surah }} ({{ $surah->jumlah_ayat }} ayat) 
                                <span class="{{ $surah->status_color ?? '' }}">{{ $surah->status_hafalan ?? '' }}</span>
                            </option>
                        @endforeach
                    </select>
                    @error('id_surah') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ayat Mulai</label>
                    <input type="number" wire:model.live="ayat_mulai" min="1" max="{{ $jumlahAyatSurah }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                        placeholder="-- Pilih Ayat Mulai --">
                    @error('ayat_mulai') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ayat Selesai</label>
                    <input type="number" wire:model.live="ayat_selesai" min="1" max="{{ $jumlahAyatSurah }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                        placeholder="-- Pilih Ayat Selesai --">
                    @error('ayat_selesai') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            @if($jumlahAyatSurah)
                <p class="text-sm text-gray-600 mb-4">
                    <strong>Jumlah ayat dalam surah:</strong> {{ $jumlahAyatSurah }}
                </p>
            @endif

            <button wire:click="loadAyats"
                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg">
                Lanjutkan ke Penilaian
            </button>
        </div>
    @endif

    @if($step == 4)
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex justify-between items-center border-b pb-2 mb-4">
                <h3 class="text-lg font-semibold text-gray-700">Langkah 4: Penilaian (Siswa: {{ $selectedSiswaNama }})</h3>
                <button wire:click="backStep(3)" class="text-sm text-gray-500 hover:text-gray-800">&larr; Ganti Rentang</button>
            </div>

            <div class="mb-4 p-3 bg-gray-50 rounded-lg flex flex-wrap justify-center gap-4">
                <span class="font-medium">Klik kata untuk pilih kesalahan:</span>
                <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-medium">Tajwid</span>
                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium">Makhroj</span>
                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm font-medium">Kelancaran</span>
            </div>

            <div class="p-4 border rounded-lg bg-gray-50 mb-6"
                style="direction: rtl; font-family: 'Times New Roman', Times, serif; font-size: 24px; line-height: 2.5;">
                @foreach($ayatsToReview as $ayat)
                    <p class="mb-4 border-b border-gray-200 pb-4 last:border-0">
                        @php 
                            $teksArab = is_array($ayat) ? $ayat['teks_arab'] : $ayat->teks_arab;
                            $idAyat = is_array($ayat) ? $ayat['id_ayat'] : $ayat->id_ayat;
                            $nomorAyat = is_array($ayat) ? $ayat['nomor_ayat'] : $ayat->nomor_ayat;
                            $words = explode(' ', $teksArab); 
                        @endphp

                        @foreach($words as $index => $word)
                            @php
                                $key = 'id_ayat_' . $idAyat . '_kata_' . $index;
                                $koreksiKata = $koreksi[$key] ?? null;

                                // Background color berdasarkan kesalahan yang dipilih
                                $bgColors = [];
                                if ($koreksiKata && isset($koreksiKata['kategori'])) {
                                    if (in_array('tajwid', $koreksiKata['kategori'])) {
                                        $bgColors[] = 'bg-red-200';
                                    }
                                    if (in_array('makhroj', $koreksiKata['kategori'])) {
                                        $bgColors[] = 'bg-blue-200';
                                    }
                                    if (in_array('kelancaran', $koreksiKata['kategori'])) {
                                        $bgColors[] = 'bg-yellow-200';
                                    }
                                }
                                
                                // Jika ada multiple kesalahan, tampilkan gradient atau kombinasi
                                if (count($bgColors) > 1) {
                                    $bgColor = 'bg-gradient-to-r from-red-200 via-blue-200 to-yellow-200';
                                } elseif (count($bgColors) == 1) {
                                    $bgColor = $bgColors[0];
                                } else {
                                    $bgColor = '';
                                }
                            @endphp

                            <span x-data="{ open: false }"
                                class="relative inline-block p-1 rounded cursor-pointer {{ $bgColor }} hover:bg-gray-200 mx-1 select-none">
                                <span @click="open = !open">{{ $word }}</span>

                                {{-- REVISI: Popup dengan CHECKBOX --}}
                                <div x-show="open" 
                                     @click.away="open = false" 
                                     x-transition
                                     class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 z-10 p-3 bg-white shadow-lg rounded-lg min-w-[140px]"
                                     style="direction: ltr; display: none;">
                                    
                                    <div class="text-xs font-bold text-gray-700 mb-2 text-center border-b pb-2">
                                        Pilih Kesalahan
                                    </div>

                                    {{-- Checkbox Tajwid --}}
                                    <label class="flex items-center gap-2 p-2 hover:bg-red-50 rounded cursor-pointer">
                                        <input 
                                            type="checkbox" 
                                            wire:click="toggleKoreksi({{ $idAyat }}, {{ $index }}, 'tajwid', '{{ $word }}')"
                                            @if($this->isKoreksiChecked($idAyat, $index, 'tajwid')) checked @endif
                                            class="w-4 h-4 text-red-600 rounded focus:ring-red-500"
                                        >
                                        <span class="text-sm font-medium text-red-700">Tajwid</span>
                                    </label>

                                    {{-- Checkbox Makhroj --}}
                                    <label class="flex items-center gap-2 p-2 hover:bg-blue-50 rounded cursor-pointer">
                                        <input 
                                            type="checkbox" 
                                            wire:click="toggleKoreksi({{ $idAyat }}, {{ $index }}, 'makhroj', '{{ $word }}')"
                                            @if($this->isKoreksiChecked($idAyat, $index, 'makhroj')) checked @endif
                                            class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500"
                                        >
                                        <span class="text-sm font-medium text-blue-700">Makhroj</span>
                                    </label>

                                    {{-- Checkbox Kelancaran --}}
                                    <label class="flex items-center gap-2 p-2 hover:bg-yellow-50 rounded cursor-pointer">
                                        <input 
                                            type="checkbox" 
                                            wire:click="toggleKoreksi({{ $idAyat }}, {{ $index }}, 'kelancaran', '{{ $word }}')"
                                            @if($this->isKoreksiChecked($idAyat, $index, 'kelancaran')) checked @endif
                                            class="w-4 h-4 text-yellow-600 rounded focus:ring-yellow-500"
                                        >
                                        <span class="text-sm font-medium text-yellow-700">Kelancaran</span>
                                    </label>

                                    {{-- Tombol Tutup --}}
                                    <button 
                                        @click="open = false"
                                        class="w-full mt-2 px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded text-xs font-medium text-gray-700 border-t pt-2"
                                    >
                                        Tutup
                                    </button>
                                </div>
                            </span>
                        @endforeach
                        <span class="text-green-700 text-lg font-bold inline-block mr-2 border border-green-700 rounded-full w-8 h-8 text-center leading-7 text-base"> {{ $nomorAyat }} </span>
                    </p>
                @endforeach
            </div>

            <div class="bg-gray-50 p-6 rounded-lg shadow-inner mb-6 border border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Statistik Penilaian</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                    <div>
                        <h4 class="font-semibold text-gray-700 mb-2">Rincian Kesalahan</h4>
                        <p class="text-sm text-gray-600 mb-2">Total Kata: <span class="font-bold">{{ $this->statistik['totalKata'] }}</span></p>
                        <ul class="space-y-2 text-sm">
                            <li class="flex justify-between items-center bg-white p-2 rounded shadow-sm">
                                <span>Tajwid</span>
                                <span><strong class="text-red-600">{{ $this->statistik['totalKesalahanTajwid'] }}</strong> ({{ $this->statistik['proporsiTajwid'] }}%)</span>
                            </li>
                            <li class="flex justify-between items-center bg-white p-2 rounded shadow-sm">
                                <span>Makhroj</span>
                                <span><strong class="text-blue-600">{{ $this->statistik['totalKesalahanMakhroj'] }}</strong> ({{ $this->statistik['proporsiMakhroj'] }}%)</span>
                            </li>
                            <li class="flex justify-between items-center bg-white p-2 rounded shadow-sm">
                                <span>Kelancaran</span>
                                <span><strong class="text-yellow-600">{{ $this->statistik['totalKesalahanKelancaran'] }}</strong> ({{ $this->statistik['proporsiKelancaran'] }}%)</span>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="font-semibold text-gray-700 mb-2">Nilai Akhir</h4>
                        <ul class="space-y-2 text-sm mb-4">
                            <li class="flex justify-between"><span>Skor Tajwid:</span> <span class="font-bold text-gray-800">{{ $this->statistik['skorTajwid'] }}</span></li>
                            <li class="flex justify-between"><span>Skor Makhroj:</span> <span class="font-bold text-gray-800">{{ $this->statistik['skorMakhroj'] }}</span></li>
                            <li class="flex justify-between"><span>Skor Kelancaran:</span> <span class="font-bold text-gray-800">{{ $this->statistik['skorKelancaran'] }}</span></li>
                        </ul>
                        <div class="bg-green-100 p-3 rounded-lg text-center border border-green-300">
                            <span class="block text-xs text-green-600 uppercase font-bold">Rata-rata</span>
                            <span class="block text-3xl font-bold text-green-700">{{ $this->statistik['nilaiAkhir'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex flex-col sm:flex-row justify-end gap-3">
                <button wire:click="simpanSesi(false)" wire:loading.attr="disabled"
                    class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded-lg text-sm">
                    Simpan (Tanpa Notif)
                </button>
                <button wire:click="simpanSesi(true)" wire:loading.attr="disabled"
                    class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg text-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Simpan & Kirim Notifikasi
                </button>
            </div>

        </div>
    @endif
</div>
