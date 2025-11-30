<div>
    <h2 class="text-2xl font-semibold text-gray-700">Input Data Hafalan</h2>
    <h4 class="text-sm text-gray-500 mb-6">
        Pilih kelompok, siswa, dan masukkan penilaian hafalan.
    </h4>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

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
            <div class="flex justify-between items-center border-b pb-2 mb-4">
                <h3 class="text-lg font-semibold text-gray-700">Langkah 2: Pilih Siswa</h3>
                <button wire:click="backStep(1)"class="flex items-center gap-2 text-green-600 hover:text-green-700 font-semibold mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                            </path>
                        </svg> Kembali ke Kelompok</button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse ($daftarSiswa as $siswa)
                    <button wire:click="selectSiswa({{ $siswa->id_siswa }})"
                        class="p-4 bg-gray-50 rounded-lg shadow border border-gray-200 hover:bg-green-100 hover:border-green-400 transition-all text-left">
                        <p class="text-xl font-semibold text-gray-800">{{ $siswa->nama_siswa }}</p>
                    </button>
                @empty
                    <p class="text-gray-500 col-span-3">Tidak ada siswa di kelompok ini.</p>
                @endforelse
            </div>
        </div>
    @endif

    @if($step == 3)
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex justify-between items-center border-b pb-2 mb-4">
                <h3 class="text-lg font-semibold text-gray-700">Langkah 3: Tentukan Rentang Setoran</h3>
                <button wire:click="backStep(2)" class="flex items-center gap-2 text-green-600 hover:text-green-700 font-semibold mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                            </path>
                        </svg> Kembali ke Siswa</button>
            </div>

            <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-500 rounded-r-lg">
                <p class="text-gray-700">Siswa: <span class="font-bold text-gray-900 text-lg">{{ $selectedSiswaNama }}</span></p>
                @if($targetHafalanInfo)
                    <p class="text-sm text-blue-700 mt-1 font-medium">{{ $targetHafalanInfo }}</p>
                @endif
            </div>

            <form wire:submit.prevent="loadAyats">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700">Pilih Surah</label>
                        <select wire:model.live="id_surah" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="">Pilih Surah</option>
                            @foreach($daftarSurah as $surah)
                                <option value="{{ $surah->id_surah }}" class="{{ $surah->status_color ?? '' }}">
                                    {{ $surah->nomor_surah }}. {{ $surah->nama_surah }}
                                    @if(isset($surah->status_hafalan))
                                        ({{ $surah->status_hafalan }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('id_surah') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        @if($jumlahAyatSurah)
                            <span class="text-xs text-gray-500">(Total Ayat: {{ $jumlahAyatSurah }})</span>
                        @endif
                    </div>
                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700">Ayat Mulai</label>
                        <input wire:model="ayat_mulai" type="number" min="1"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('ayat_mulai') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700">Ayat Selesai</label>
                        <input wire:model="ayat_selesai" type="number" min="1"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('ayat_selesai') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-6 text-right">
                    <button type="submit" wire:loading.attr="disabled"
                        class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-6 rounded-lg shadow hover:shadow-md transition-all">
                        Mulai Menilai &rarr;
                    </button>
                </div>
            </form>
        </div>
    @endif

    @if($step == 4)
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex justify-between items-center border-b pb-2 mb-4">
                <h3 class="text-lg font-semibold text-gray-700">Langkah 4: Penilaian (Siswa: {{ $selectedSiswaNama }})</h3>
                <button wire:click="backStep(3)" class="text-sm text-gray-500 hover:text-gray-800">&larr; Ganti Rentang</button>
            </div>

            <div class="mb-4 p-3 bg-gray-50 rounded-lg flex flex-wrap justify-center gap-4">
                <span class="font-medium">Klik Kesalahan:</span>
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

                                $bgColor = ''; // Default
                                if ($koreksiKata) {
                                    if ($koreksiKata['kategori'] == 'tajwid')
                                        $bgColor = 'bg-red-200';
                                    elseif ($koreksiKata['kategori'] == 'makhroj')
                                        $bgColor = 'bg-blue-200';
                                    elseif ($koreksiKata['kategori'] == 'kelancaran')
                                        $bgColor = 'bg-yellow-200';
                                }
                            @endphp

                            <span x-data="{ open: false }"
                                class="relative inline-block p-1 rounded cursor-pointer {{ $bgColor }} hover:bg-gray-200 mx-1 select-none">
                                <span @click="open = !open">{{ $word }}</span>

                                <span x-show="open" @click.away="open = false" x-transition
                                    class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 z-10 p-1 bg-white shadow-lg rounded-lg flex flex-col space-y-1 min-w-[100px]"
                                    style="direction: ltr; display: none;">
                                    
                                    <button @click="open = false"
                                        wire:click="addKoreksi({{ $idAyat }}, {{ $index }}, 'tajwid', '{{ $word }}')"
                                        class="px-2 py-1 bg-red-100 hover:bg-red-200 rounded text-xs font-bold text-red-800 w-full text-center">Tajwid</button>

                                    <button @click="open = false"
                                        wire:click="addKoreksi({{ $idAyat }}, {{ $index }}, 'makhroj', '{{ $word }}')"
                                        class="px-2 py-1 bg-blue-100 hover:bg-blue-200 rounded text-xs font-bold text-blue-800 w-full text-center">Makhroj</button>

                                    <button @click="open = false"
                                        wire:click="addKoreksi({{ $idAyat }}, {{ $index }}, 'kelancaran', '{{ $word }}')"
                                        class="px-2 py-1 bg-yellow-100 hover:bg-yellow-200 rounded text-xs font-bold text-yellow-800 w-full text-center">Kelancaran</button>
                                </span>
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