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
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse ($daftarKelompok as $kelompok)
                    <button wire:click="selectKelompok({{ $kelompok->id_kelompok }})"
                        class="p-4 bg-gray-50 rounded-lg shadow border border-gray-200 hover:bg-green-100 hover:border-green-400 transition-all text-left">
                        <p class="text-xl font-semibold text-gray-800">{{ $kelompok->nama_kelompok ?? 'Tanpa Nama' }}</p>
                        <p class="text-sm text-gray-500">Kelas: {{ $kelompok->kelas->nama_kelas ?? 'Tanpa Kelas' }}</p>
                        <p class="text-sm text-gray-500">{{ $kelompok->tahun_ajaran }}</p>

                    </button>
                @empty
                    <p class="text-gray-500 col-span-3">Data kelompok tidak ditemukan. (Pastikan Anda sudah menjalankan Seeder)
                    </p>
                @endforelse
            </div>
        </div>
    @endif

    @if($step == 2)
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex justify-between items-center border-b pb-2 mb-4">
                <h3 class="text-lg font-semibold text-gray-700">Langkah 2: Pilih Siswa</h3>
                <button wire:click="backStep(1)" class="text-sm text-gray-500 hover:text-gray-800">&larr; Kembali ke
                    Kelompok</button>
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
                <button wire:click="backStep(2)" class="text-sm text-gray-500 hover:text-gray-800">&larr; Kembali ke
                    Siswa</button>
            </div>

            <p class="mb-4">Siswa: <span class="font-bold">{{ $selectedSiswaNama }}</span></p>

            <form wire:submit.prevent="loadAyats">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700">Pilih Surah</label>
                        <select wire:model.live="id_surah" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">Pilih Surah</option>
                            @foreach($daftarSurah as $surah)
                                <option value="{{ $surah->id_surah }}">{{ $surah->nomor_surah }}. {{ $surah->nama_surah }}
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
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        @error('ayat_mulai') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700">Ayat Selesai</label>
                        <input wire:model="ayat_selesai" type="number" min="1"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        @error('ayat_selesai') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-6 text-right">
                    <button type="submit" wire:loading.attr="disabled"
                        class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-6 rounded-lg">
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
                <button wire:click="backStep(3)" class="text-sm text-gray-500 hover:text-gray-800">&larr; Ganti
                    Rentang</button>
            </div>

            <div class="mb-4 p-3 bg-gray-50 rounded-lg flex flex-wrap justify-center gap-4">
                <span class="font-medium">Klik Kesalahan:</span>
                <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-medium">Tajwid</span>
                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium">Makhroj</span>
                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm font-medium">Kelancaran</span>
            </div>

            <div class="p-4 border rounded-lg bg-gray-50 mb-6"
                style="direction: rtl; font-family: 'Times New Roman', Times, serif; font-size: 24px; line-height: 1.75;">
                @foreach($ayatsToReview as $ayat)
                    <p class="mb-4">
                        @php $words = explode(' ', $ayat->teks_arab); @endphp

                        @foreach($words as $index => $word)
                            @php
                                $key = 'id_ayat_' . $ayat->id_ayat . '_kata_' . $index;
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
                                class="relative inline-block p-1 rounded cursor-pointer {{ $bgColor }} hover:bg-gray-200">
                                <span @click="open = !open">{{ $word }}</span>

                                <span x-show="open" @click.away="open = false" x-transition
                                    class="absolute top-full left-1/2 -translate-x-1/2 z-10 p-1 bg-white shadow-lg rounded-lg flex flex-col space-y-1"
                                    style="direction: ltr; display: none;">

                                    <button @click="open = false"
                                        wire:click="addKoreksi({{ $ayat->id_ayat }}, {{ $index }}, 'tajwid', '{{ $word }}')"
                                        class="px-2 py-1 bg-red-100 hover:bg-red-300 rounded text-xs font-bold text-red-800">Tajwid</button>

                                    <button @click="open = false"
                                        wire:click="addKoreksi({{ $ayat->id_ayat }}, {{ $index }}, 'makhroj', '{{ $word }}')"
                                        class="px-2 py-1 bg-blue-100 hover:bg-blue-300 rounded text-xs font-bold text-blue-800">Makhroj</button>

                                    <button @click="open = false"
                                        wire:click="addKoreksi({{ $ayat->id_ayat }}, {{ $index }}, 'kelancaran', '{{ $word }}')"
                                        class="px-2 py-1 bg-yellow-100 hover:bg-yellow-300 rounded text-xs font-bold text-yellow-800">Kelancaran</button>
                                </span>
                            </span>
                        @endforeach
                        <span class="text-green-700 text-lg font-bold"> ({{ $ayat->nomor_ayat }}) </span>
                    </p>
                @endforeach
            </div>

            <div class="bg-gray-50 p-6 rounded-lg shadow-inner mb-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Statistik Penilaian (Real-time)</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <h4 class="font-semibold text-gray-700">Statistik Kesalahan</h4>
                        <p class="text-sm text-gray-600">Total Kata: <span
                                class="font-bold">{{ $this->statistik['totalKata'] }}</span></an>
                        <ul class="mt-2 space-y-1 text-sm">
                            <li>Tajwid: <span class="font-bold">{{ $this->statistik['totalKesalahanTajwid'] }} /
                                    {{ $this->statistik['totalKata'] }}</span> ({{ $this->statistik['proporsiTajwid'] }}%)
                            </li>
                            <li>Makhroj: <span class="font-bold">{{ $this->statistik['totalKesalahanMakhroj'] }} /
                                    {{ $this->statistik['totalKata'] }}</span> ({{ $this->statistik['proporsiMakhroj'] }}%)
                            </li>
                            <li>Kelancaran: <span class="font-bold">{{ $this->statistik['totalKesalahanKelancaran'] }} /
                                    {{ $this->statistik['totalKata'] }}</span>
                                ({{ $this->statistik['proporsiKelancaran'] }}%)</li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="font-semibold text-gray-700">Skor (100 - % Kesalahan)</h4>
                        <ul class="mt-2 space-y-1 text-sm">
                            <li>Skor Tajwid: <span
                                    class="font-bold text-blue-600">{{ $this->statistik['skorTajwid'] }}</span></li>
                            <li>Skor Makhroj: <span
                                    class="font-bold text-blue-600">{{ $this->statistik['skorMakhroj'] }}</span></li>
                            <li>Skor Kelancaran: <span
                                    class="font-bold text-blue-600">{{ $this->statistik['skorKelancaran'] }}</span></li>
                        </ul>
                        <hr class="my-2">
                        <h4 class="text-lg font-bold text-gray-800">Nilai Akhir (Rata-rata):
                            <span class="text-green-600">{{ $this->statistik['nilaiAkhir'] }}</span>
                        </h4>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-4">
                <button wire:click="simpanSesi(false)" wire:loading.attr="disabled"
                    class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-8 rounded-lg">
                    Simpan (Tanpa Notif)
                </button>
                <button wire:click="simpanSesi(true)" wire:loading.attr="disabled"
                    class="bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-8 rounded-lg shadow-lg">
                    Simpan & Kirim Notifikasi
                </button>
            </div>

        </div>
    @endif
</div>