<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Laporan Hafalan') }}
        </h2>
    </x-slot>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-4 md:p-6 text-gray-900 dark:text-gray-100">

            @if (!$selectedKelasId)
                {{-- ... KODE VIEW KELAS TETAP SAMA ... --}}
                <div wire:key="view-kelas">
                    <h3 class="text-xl md:text-2xl font-bold text-gray-800 mb-2">Laporan Hafalan</h3>
                    <p class="text-sm md:text-base text-gray-600 mb-6">Pilih kelas untuk melihat laporan</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse ($kelasList as $kelas)
                            <div wire:click="selectKelas({{ $kelas['id'] }})"
                                class="bg-white border-2 border-gray-200 rounded-lg p-5 md:p-6 cursor-pointer hover:shadow-lg hover:border-green-500 transition-all duration-200 active:bg-green-50">
                                <h4 class="text-lg font-bold text-gray-800 mb-1">{{ $kelas['nama_kelas'] }}</h4>
                                <p class="text-sm text-gray-600 mb-4">{{ $kelas['tahun_ajaran'] }}</p>

                                <div class="flex items-center justify-between">
                                    <span
                                        class="text-sm font-semibold text-gray-700 bg-gray-100 px-3 py-1 rounded-full">{{ $kelas['jumlah_siswa'] }}
                                        siswa</span>
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                        @empty
                            <div
                                class="col-span-full text-center py-12 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                                <p class="text-gray-500">Tidak ada data kelas</p>
                            </div>
                        @endforelse
                    </div>
                </div>

            @elseif (!$selectedSiswaId && !$selectedSurahId)
                <div wire:key="view-daftar-siswa">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                        <div>
                            <button wire:click="backToList()"
                                class="inline-flex items-center text-green-600 hover:text-green-800 font-semibold mb-2 group">
                                <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Kembali
                            </button>
                            <h3 class="text-xl md:text-2xl font-bold text-gray-800">
                                {{ $kelasDetail['nama_kelas'] ?? 'Detail Laporan' }}
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">
                                Tahun Ajaran: {{ $kelasDetail['tahun_ajaran'] ?? 'N/A' }}
                            </p>
                        </div>

                        <div class="flex gap-2 w-full md:w-auto">
                            <button wire:click="downloadPdf()"
                                class="flex-1 md:flex-none justify-center items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold shadow-sm text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                PDF
                            </button>
                            <button wire:click="downloadExcel()"
                                class="flex-1 md:flex-none justify-center items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold shadow-sm text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                Excel
                            </button>
                        </div>
                    </div>


                    <div class="w-full overflow-x-auto border rounded-lg shadow-sm">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-green-600 text-white">
                                <tr>
                                    <th class="px-4 md:px-6 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider whitespace-nowrap">
                                        No</th>
                                    <th class="px-4 md:px-6 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider whitespace-nowrap">
                                        Nama Siswa</th>
                                    <th class="px-4 py-2 md:px-6 py-3 text-left text-xs md:text-sm font-semibold uppercase tracking-wider whitespace-nowrap">
                                        Kelompok</th>
                                    <th class="px-4 md:px-6 py-3 text-center text-xs md:text-sm font-semibold uppercase tracking-wider whitespace-nowrap">
                                        Jumlah Sesi</th>
                                    {{-- UPDATE KOLOM DISINI: Total Ayat -> Progress Target --}}
                                    <th class="px-4 md:px-6 py-3 text-center text-xs md:text-sm font-semibold uppercase tracking-wider whitespace-nowrap">
                                        Progres Target</th>
                                    <th class="px-4 md:px-6 py-3 text-center text-xs md:text-sm font-semibold uppercase tracking-wider whitespace-nowrap">
                                        Rata-rata</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($detailLaporan as $index => $siswa)
                                    <tr wire:click="selectSiswa({{ $siswa['id_siswa'] }})"
                                        class="hover:bg-green-50 cursor-pointer transition-colors">
                                        <td class="px-4 md:px-6 py-4 text-sm text-gray-700">{{ $index + 1 }}</td>
                                        <td class="px-4 md:px-6 py-4 text-sm font-medium text-gray-900">
                                            {{ $siswa['nama_siswa'] }}
                                        </td>
                                        {{-- Karena $detailLaporan berbentuk Array, cara aksesnya pakai kurung siku ['key'] --}}
                                        <td class="px-4 md:px-6 py-4 text-sm font-medium text-gray-700">
                                                {{ $siswa['nama_kelompok'] }}
                                        </td>
                                        <td class="px-4 md:px-6 py-4 text-center text-sm text-gray-700">
                                            {{ $siswa['jumlah_sesi'] }}</td>
                                        {{-- UPDATE ISI DISINI --}}
                                        <td class="px-4 md:px-6 py-4 text-center text-sm text-gray-700">
                                            {{ $siswa['progress_target'] }}</td>
                                        <td class="px-4 md:px-6 py-4 text-center text-sm font-semibold">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ $siswa['nilai_rata_rata'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-500 italic">
                                            Tidak ada data siswa untuk ditampilkan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            @elseif ($selectedSiswaId && !$selectedSurahId)
                <div wire:key="view-detail-siswa">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                        <button wire:click="backToKelas()"
                            class="inline-flex items-center text-green-600 hover:text-green-700 font-semibold group">
                            <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                                </path>
                            </svg>
                            Kembali
                        </button>

                        <div class="flex gap-2 w-full md:w-auto">
                            <button wire:click="downloadPdfSiswa()"
                                class="flex-1 md:flex-none justify-center items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold shadow-sm text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                PDF
                            </button>
                            <button wire:click="downloadExcelSiswa()"
                                class="flex-1 md:flex-none justify-center items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold shadow-sm text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                Excel
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 md:gap-4 mb-6">
                        <div class="col-span-2 lg:col-span-1 border-2 border-green-600 rounded-lg p-4 bg-green-50">
                            <p class="text-green-800 text-xs md:text-sm mb-1 uppercase tracking-wide font-semibold">Total
                                Sesi</p>
                            <p class="text-2xl md:text-3xl font-bold text-green-700">{{ $siswaDetail['jumlah_sesi'] ?? 0 }}
                            </p>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4 bg-white shadow-sm">
                            <p class="text-gray-500 text-xs md:text-sm mb-1">Tajwid</p>
                            <p class="text-xl md:text-2xl font-bold text-gray-800">{{ $siswaDetail['nilai_tajwid'] ?? 0 }}
                            </p>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4 bg-white shadow-sm">
                            <p class="text-gray-500 text-xs md:text-sm mb-1">Kelancaran</p>
                            <p class="text-xl md:text-2xl font-bold text-gray-800">
                                {{ $siswaDetail['nilai_kelancaran'] ?? 0 }}</p>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4 bg-white shadow-sm">
                            <p class="text-gray-500 text-xs md:text-sm mb-1">Makhroj</p>
                            <p class="text-xl md:text-2xl font-bold text-gray-800">{{ $siswaDetail['nilai_makhroj'] ?? 0 }}
                            </p>
                        </div>
                        <div class="col-span-2 lg:col-span-1 border-2 border-green-100 rounded-lg p-4 bg-white shadow-sm">
                            <p class="text-green-600 text-xs md:text-sm mb-1 uppercase font-bold">Rata-rata</p>
                            <p class="text-2xl md:text-3xl font-bold text-green-600">
                                {{ $siswaDetail['nilai_rata_rata'] ?? 0 }}</p>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4 mb-6 border border-gray-200">
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                                </path>
                            </svg>
                            <span class="font-semibold text-gray-700 text-sm md:text-base">Filter Periode</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Dari Tanggal</label>
                                <input type="date" wire:model="tanggalMulai"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Sampai Tanggal</label>
                                <input type="date" wire:model="tanggalAkhir"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500">
                            </div>
                        </div>
                        <button wire:click="filterPeriode()"
                            class="mt-3 w-full md:w-auto px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-semibold transition-colors shadow-sm">
                            Terapkan Filter
                        </button>
                    </div>

                    <div class="mb-8">
                        <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <span class="w-1 h-6 bg-green-500 rounded-full block"></span>
                            Riwayat Sesi
                        </h4>
                        <div class="space-y-3">
                            @forelse ($siswaDetail['riwayat_sesi'] as $index => $sesi)
                                <div wire:click="selectSurah({{ $sesi['id_surah_mulai'] }})"
                                    class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md cursor-pointer transition-all active:bg-gray-50">
                                    <div class="flex flex-col sm:flex-row gap-4">
                                        <div class="flex items-start gap-3 flex-1">
                                            <span
                                                class="flex-shrink-0 bg-green-100 text-green-700 rounded-lg w-10 h-10 flex items-center justify-center font-bold text-sm">
                                                #{{ count($siswaDetail['riwayat_sesi']) - $index }}
                                            </span>
                                            <div>
                                                <p class="font-bold text-gray-800 text-sm md:text-base">
                                                    {{ $sesi['surah_text'] }}</p>
                                                <p
                                                    class="text-xs text-gray-500 bg-gray-100 inline-block px-2 py-0.5 rounded mt-1">
                                                    Ayat {{ $sesi['ayat_mulai'] }}-{{ $sesi['ayat_selesai'] }}
                                                </p>
                                                <p class="text-gray-400 text-xs mt-1 flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M8 7V3m8 4V3m-9 8h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                        </path>
                                                    </svg>
                                                    {{ $sesi['tanggal_setor'] }}
                                                </p>
                                            </div>
                                        </div>

                                        <div
                                            class="grid grid-cols-4 gap-2 sm:gap-4 border-t sm:border-t-0 sm:border-l border-gray-100 pt-3 sm:pt-0 sm:pl-4">
                                            <div class="text-center">
                                                <p class="text-[10px] text-gray-400 uppercase">Tajwid</p>
                                                <p class="font-bold text-gray-700 text-sm">{{ $sesi['skor_tajwid'] }}</p>
                                            </div>
                                            <div class="text-center">
                                                <p class="text-[10px] text-gray-400 uppercase">Lancar</p>
                                                <p class="font-bold text-gray-700 text-sm">{{ $sesi['skor_kelancaran'] }}</p>
                                            </div>
                                            <div class="text-center">
                                                <p class="text-[10px] text-gray-400 uppercase">Makhroj</p>
                                                <p class="font-bold text-gray-700 text-sm">{{ $sesi['skor_makhroj'] }}</p>
                                            </div>
                                            <div class="text-center bg-green-50 rounded p-1">
                                                <p class="text-[10px] text-green-600 uppercase font-bold">Rata</p>
                                                <p class="font-bold text-green-700 text-sm">{{ $sesi['nilai_rata'] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 text-gray-500 bg-gray-50 rounded-lg border border-dashed">
                                    Belum ada riwayat sesi
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- TAMBAHAN BARU: SURAH SUDAH DIHAFAL (TUNTAS) --}}
                    <div class="mb-8">
                        <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <span class="w-1 h-6 bg-blue-600 rounded-full block"></span>
                            Surah Sudah Dihafal (Tuntas)
                        </h4>
                        
                        <div class="w-full overflow-x-auto rounded-lg border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-blue-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider whitespace-nowrap">No</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider whitespace-nowrap">Surah</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold text-blue-800 uppercase tracking-wider whitespace-nowrap">Total Sesi</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold text-blue-800 uppercase tracking-wider whitespace-nowrap">Tajwid</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold text-blue-800 uppercase tracking-wider whitespace-nowrap">Lancar</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold text-blue-800 uppercase tracking-wider whitespace-nowrap">Makhroj</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold text-blue-800 uppercase tracking-wider whitespace-nowrap">Nilai Akhir</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($siswaDetail['surah_sudah_dihafal'] as $index => $item)
                                        <tr class="hover:bg-blue-50 transition-colors">
                                            <td class="px-4 py-3 text-sm text-gray-600">{{ $index + 1 }}</td>
                                            <td class="px-4 py-3 text-sm font-bold text-gray-800">{{ $item['nama_surah'] }}</td>
                                            <td class="px-4 py-3 text-sm text-center text-gray-600">{{ $item['jumlah_sesi'] }}x</td>
                                            <td class="px-4 py-3 text-sm text-center text-gray-600">{{ $item['nilai_tajwid'] }}</td>
                                            <td class="px-4 py-3 text-sm text-center text-gray-600">{{ $item['nilai_kelancaran'] }}</td>
                                            <td class="px-4 py-3 text-sm text-center text-gray-600">{{ $item['nilai_makhroj'] }}</td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                                    {{ $item['nilai_rata'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-4 py-6 text-center text-gray-500 italic">
                                                Belum ada surah yang tuntas 100%.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <span class="w-1 h-6 bg-orange-500 rounded-full block"></span>
                            Target Belum Tuntas
                        </h4>
                        <div class="w-full overflow-x-auto rounded-lg border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-orange-50">
                                    <tr>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-semibold text-orange-800 uppercase tracking-wider whitespace-nowrap">
                                            No</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-semibold text-orange-800 uppercase tracking-wider whitespace-nowrap">
                                            Surah</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-semibold text-orange-800 uppercase tracking-wider whitespace-nowrap">
                                            Status</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-semibold text-orange-800 uppercase tracking-wider whitespace-nowrap">
                                            Progress</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($siswaDetail['target_belum_dihafalkan'] as $index => $target)
                                        <tr>
                                            <td class="px-4 py-3 text-sm text-gray-600">{{ $target['no'] }}</td>
                                            <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $target['nama_surah'] }}
                                            </td>
                                            <td class="px-4 py-3 text-sm">
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800">
                                                    {{ $target['status'] }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-600">{{ $target['progress'] }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-6 text-center text-gray-500 italic">
                                                Alhamdulillah, semua target sudah tuntas!
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            @else
                <div wire:key="view-detail-surah">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                        <button type="button" wire:click="backToSiswa()"
                            class="inline-flex items-center text-green-600 hover:text-green-700 font-semibold group">
                            <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                                </path>
                            </svg>
                            Kembali
                        </button>

                        {{-- Tombol Download Sesi PDF/Excel Tetap Sama --}}
                        <div class="flex gap-2 w-full md:w-auto">
                            <button wire:click="downloadPdfSesi()"
                                class="flex-1 md:flex-none justify-center items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold shadow-sm text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                PDF
                            </button>
                            <button wire:click="downloadExcelSesi()"
                                class="flex-1 md:flex-none justify-center items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold shadow-sm text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                Excel
                            </button>
                        </div>
                    </div>

                    {{-- DETAIL SURAH SEPERTI SEBELUMNYA --}}
                    <div
                        class="bg-white border-l-4 border-green-600 rounded-r-lg shadow-sm p-4 mb-6 flex items-center justify-between">
                        <div>
                            <h4 class="text-lg md:text-xl font-bold text-gray-800">
                                {{ $surahDetail['nama_surah'] ?? 'Surah' }}</h4>
                            <p class="text-sm text-gray-600">
                                Surah ke-{{ $surahDetail['nomor_surah'] ?? '-' }} • {{ $surahDetail['jumlah_ayat'] ?? '-' }}
                                Ayat
                            </p>
                        </div>
                        <div
                            class="bg-green-100 text-green-800 rounded-full w-12 h-12 flex items-center justify-center font-bold text-xl border border-green-200">
                            {{ $surahDetail['nomor_surah'] ?? '0' }}
                        </div>
                    </div>

                    {{-- GRID NILAI --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
                        <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                            <p class="text-[10px] uppercase text-gray-500 font-semibold">Tajwid</p>
                            <p class="text-xl font-bold text-gray-800">{{ $surahDetail['nilai_tajwid'] ?? 0 }}</p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                            <p class="text-[10px] uppercase text-gray-500 font-semibold">Kelancaran</p>
                            <p class="text-xl font-bold text-gray-800">{{ $surahDetail['nilai_kelancaran'] ?? 0 }}</p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                            <p class="text-[10px] uppercase text-gray-500 font-semibold">Makhroj</p>
                            <p class="text-xl font-bold text-gray-800">{{ $surahDetail['nilai_makhroj'] ?? 0 }}</p>
                        </div>
                        <div class="bg-green-50 p-3 rounded-lg border border-green-100">
                            <p class="text-[10px] uppercase text-green-600 font-semibold">Rata-rata</p>
                            <p class="text-xl font-bold text-green-700">{{ $surahDetail['nilai_rata_rata'] ?? 0 }}</p>
                        </div>
                    </div>

                    {{-- DETAIL SETORAN --}}
                    <div>
                        <h4 class="text-base font-bold text-gray-800 mb-3">Detail Setoran</h4>
                        <div class="space-y-3">
                            @forelse ($surahDetail['sesi_formatnya'] as $index => $sesi)
                                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-semibold text-gray-800 text-sm">{{ $sesi['tanggal_setor'] }}</p>
                                            <div
                                                class="inline-flex items-center mt-1 px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-600">
                                                Ayat {{ $sesi['ayat_mulai'] }}-{{ $sesi['ayat_selesai'] }}
                                            </div>
                                        </div>
                                        <button wire:click="selectSesi({{ $sesi['id_sesi'] }})"
                                            class="text-green-600 hover:text-green-800 text-sm font-medium underline px-2 py-1 hover:bg-green-50 rounded transition-colors">
                                            Lihat Koreksi
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-6 text-gray-500 text-sm">Tidak ada data sesi</div>
                            @endforelse
                        </div>
                    </div>

                    @if(isset($selectedSesiDetail) && $selectedSesiDetail)
                        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
                            aria-modal="true">
                            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm"
                                wire:click="closeSesiDetail"></div>

                            <div class="flex min-h-full items-end justify-center p-0 md:p-4 text-center sm:items-center sm:p-0">
                                <div
                                    class="relative transform overflow-hidden rounded-t-2xl md:rounded-2xl bg-white text-left shadow-xl transition-all w-full sm:max-w-2xl md:max-w-4xl max-h-[90vh] flex flex-col">

                                    <div
                                        class="bg-green-600 px-4 py-3 md:px-6 md:py-4 flex items-center justify-between shrink-0">
                                        <h3 class="text-lg md:text-xl font-bold text-white flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Detail Sesi
                                        </h3>
                                        <button wire:click="closeSesiDetail"
                                            class="text-green-100 hover:text-white transition-colors">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>

                                    <div class="p-4 md:p-6 overflow-y-auto">
                                        <div class="grid grid-cols-2 gap-4 mb-6">
                                            <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                                                <p class="text-xs text-gray-500 uppercase font-semibold">Tanggal</p>
                                                <p class="font-bold text-gray-800">{{ $selectedSesiDetail['tanggal'] }}</p>
                                            </div>
                                            <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                                                <p class="text-xs text-gray-500 uppercase font-semibold">Guru</p>
                                                <p class="font-bold text-gray-800 text-sm truncate">
                                                    {{ $selectedSesiDetail['guru'] }}</p>
                                            </div>
                                        </div>

                                        <div class="mb-6 text-center">
                                            <p class="text-sm text-gray-500 mb-1">Capaian Ayat</p>
                                            <div
                                                class="inline-flex items-center gap-2 bg-green-50 px-4 py-2 rounded-full border border-green-100">
                                                <span class="text-lg font-bold text-green-700">
                                                    {{ $selectedSesiDetail['ayat_mulai'] }} -
                                                    {{ $selectedSesiDetail['ayat_selesai'] }}
                                                </span>
                                                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-4 gap-2 mb-6">
                                            <div class="text-center p-2 bg-gray-50 rounded">
                                                <span class="block text-[10px] text-gray-500 uppercase">Tajwid</span>
                                                <span
                                                    class="block text-lg font-bold text-gray-800">{{ $selectedSesiDetail['nilai_tajwid'] }}</span>
                                            </div>
                                            <div class="text-center p-2 bg-gray-50 rounded">
                                                <span class="block text-[10px] text-gray-500 uppercase">Lancar</span>
                                                <span
                                                    class="block text-lg font-bold text-gray-800">{{ $selectedSesiDetail['nilai_kelancaran'] }}</span>
                                            </div>
                                            <div class="text-center p-2 bg-gray-50 rounded">
                                                <span class="block text-[10px] text-gray-500 uppercase">Makhroj</span>
                                                <span
                                                    class="block text-lg font-bold text-gray-800">{{ $selectedSesiDetail['nilai_makhroj'] }}</span>
                                            </div>
                                            <div class="text-center p-2 bg-green-100 rounded border border-green-200">
                                                <span class="block text-[10px] text-green-700 uppercase font-bold">Rata</span>
                                                <span
                                                    class="block text-lg font-bold text-green-800">{{ $selectedSesiDetail['nilai_rata'] }}</span>
                                            </div>
                                        </div>

                                        <div>
                                            <h4 class="font-bold text-gray-800 mb-3 text-sm uppercase flex items-center gap-2">
                                                <span class="w-1 h-4 bg-red-500 rounded-full block"></span>
                                                Catatan Koreksi
                                            </h4>

                                            <div class="border rounded-lg overflow-hidden">
                                                <div class="overflow-x-auto">
                                                    <table class="min-w-full divide-y divide-gray-200">
                                                        <thead class="bg-gray-50">
                                                            <tr>
                                                                <th
                                                                    class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                                                    Lokasi</th>
                                                                <th
                                                                    class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                                                    Jenis</th>
                                                                <th
                                                                    class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">
                                                                    Lafadz</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="bg-white divide-y divide-gray-200">
                                                            @forelse($selectedSesiDetail['koreksi'] as $kor)
                                                                <tr>
                                                                    <td class="px-3 py-3 text-sm text-gray-800 font-medium">
                                                                        {{ $kor['lokasi'] }}</td>
                                                                    <td class="px-3 py-3 text-sm text-red-600">
                                                                        {{ $kor['jenis_kesalahan'] }}</td>
                                                                    <td class="px-3 py-3 text-sm text-right font-arabic"
                                                                        style="font-family: 'Amiri', serif; font-size: 1.2em;">
                                                                        {{ $kor['catatan'] }}
                                                                    </td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="3"
                                                                        class="px-4 py-6 text-center text-sm text-gray-500 italic bg-gray-50">
                                                                        Tidak ada catatan koreksi.
                                                                    </td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        class="bg-gray-50 px-4 py-3 sm:px-6 flex flex-row-reverse border-t border-gray-100 shrink-0">
                                        <button type="button" wire:click="closeSesiDetail"
                                            class="w-full sm:w-auto inline-flex justify-center rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 transition-all">
                                            Tutup
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            @endif

        </div>
    </div>
</div>