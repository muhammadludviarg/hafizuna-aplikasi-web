<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Laporan Hafalan') }}
    </h2>
</x-slot>

<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 text-gray-900 dark:text-gray-100">

        @if (!$selectedKelasId)
            <!-- TAMPILAN 1: DAFTAR KELAS -->
            <div wire:key="view-kelas">
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Laporan Hafalan</h3>
                <p class="text-gray-600 mb-6">Pilih kelas untuk melihat laporan</p>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse ($kelasList as $kelas)
                        <div wire:click="selectKelas({{ $kelas['id'] }})"
                            class="bg-white border-2 border-gray-200 rounded-lg p-6 cursor-pointer hover:shadow-lg hover:border-green-500 transition-all duration-200">
                            <h4 class="text-lg font-bold text-gray-800 mb-1">{{ $kelas['nama_kelas'] }}</h4>
                            <p class="text-sm text-gray-600 mb-4">{{ $kelas['tahun_ajaran'] }}</p>

                            <div class="flex items-center justify-between">
                                <span class="text-sm font-semibold text-gray-700">{{ $kelas['jumlah_siswa'] }} siswa</span>
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                    </path>
                                </svg>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-12">
                            <p class="text-gray-500">Tidak ada data kelas</p>
                        </div>
                    @endforelse
                </div>
            </div>

        @elseif (!$selectedSiswaId && !$selectedSurahId)
            <!-- TAMPILAN 2: DAFTAR SISWA DALAM KELAS -->
            <div wire:key="view-daftar-siswa">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <button wire:click="backToList()"
                            class="inline-flex items-center text-green-600 hover:text-green-800 font-semibold mb-2">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                                </path>
                            </svg>
                            Kembali
                        </button>
                        <h3 class="text-2xl font-bold text-gray-800">
                            {{ $kelasDetail['nama_kelas'] ?? 'Detail Laporan' }}
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Tahun Ajaran: {{ $kelasDetail['tahun_ajaran'] ?? 'N/A' }}
                        </p>
                    </div>
                    <!-- Added download buttons for PDF and Excel -->
                    <div class="flex gap-2">
                        <button wire:click="downloadPdf()"
                            class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 font-semibold text-gray-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2-2v14a2 2 0 002 2z">
                                </path>
                            </svg>
                            PDF
                        </button>
                        <button wire:click="downloadExcel()"
                            class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 font-semibold text-gray-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            Excel
                        </button>
                    </div>
                </div>

                <!-- Tabel Data Siswa -->
                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse">
                        <thead>
                            <tr class="bg-green-600 text-white">
                                <th class="px-6 py-3 text-left text-sm font-semibold">No</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold">Nama Siswa</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold">Jumlah Sesi</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold">Total Ayat</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold">Nilai Rata-rata</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($detailLaporan as $index => $siswa)
                                <tr wire:click="selectSiswa({{ $siswa['id_siswa'] }})"
                                    class="border-b border-gray-200 hover:bg-green-50 cursor-pointer transition-colors">
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-800 hover:text-green-600">
                                        {{ $siswa['nama_siswa'] }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm text-gray-700">{{ $siswa['jumlah_sesi'] }}</td>
                                    <td class="px-6 py-4 text-center text-sm text-gray-700">{{ $siswa['total_ayat'] }}</td>
                                    <td class="px-6 py-4 text-center text-sm font-semibold">
                                        <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full">
                                            {{ $siswa['nilai_rata_rata'] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        Tidak ada data siswa
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        @elseif ($selectedSiswaId && !$selectedSurahId)
            <!-- TAMPILAN 3: DETAIL SISWA - RIWAYAT SESI -->
            <div wire:key="view-detail-siswa">
                <div class="flex items-center justify-between mb-6">
                    <button wire:click="backToKelas()"
                        class="flex items-center gap-2 text-green-600 hover:text-green-700 font-semibold mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                            </path>
                        </svg>
                        Kembali
                    </button>

                    <div class="flex gap-2">
                        <!-- Added parentheses () to method calls -->
                        <button wire:click="downloadPdfSiswa()"
                            class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            Unduh PDF
                        </button>
                        <!-- Added parentheses () to method calls -->
                        <button wire:click="downloadExcelSiswa()"
                            class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            Unduh Excel
                        </button>
                    </div>
                </div>

                <!-- Statistik Ringkas -->
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                    <div class="border-2 border-green-600 rounded-lg p-4">
                        <p class="text-gray-600 text-sm mb-1">Jumlah Sesi</p>
                        <p class="text-3xl font-bold text-green-600">{{ $siswaDetail['jumlah_sesi'] ?? 0 }}</p>
                    </div>
                    <div class="border-2 border-gray-300 rounded-lg p-4">
                        <p class="text-gray-600 text-sm mb-1">Nilai Tajwid</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $siswaDetail['nilai_tajwid'] ?? 0 }}</p>
                    </div>
                    <div class="border-2 border-gray-300 rounded-lg p-4">
                        <p class="text-gray-600 text-sm mb-1">Nilai Kelancaran</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $siswaDetail['nilai_kelancaran'] ?? 0 }}</p>
                    </div>
                    <div class="border-2 border-gray-300 rounded-lg p-4">
                        <p class="text-gray-600 text-sm mb-1">Nilai Makhroj</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $siswaDetail['nilai_makhroj'] ?? 0 }}</p>
                    </div>
                    <div class="border-2 border-gray-300 rounded-lg p-4">
                        <p class="text-gray-600 text-sm mb-1">Rata-rata</p>
                        <p class="text-2xl font-bold text-green-600">{{ $siswaDetail['nilai_rata_rata'] ?? 0 }}</p>
                    </div>
                </div>

                <!-- Filter Periode -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6 border border-gray-200">
                    <div class="flex items-center gap-4">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        <span class="font-semibold text-gray-700">Filter Periode</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                            <input type="date" wire:model="tanggalMulai"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                            <input type="date" wire:model="tanggalAkhir"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                    </div>
                    <button wire:click="filterPeriode()"
                        class="mt-4 px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold">
                        Terapkan Filter
                    </button>
                </div>

                <!-- Riwayat Sesi Setoran -->
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Sesi Setoran</h4>
                    <div class="space-y-4">
                        @forelse ($siswaDetail['riwayat_sesi'] as $index => $sesi)
                            <div wire:click="selectSurah({{ $sesi['id_surah_mulai'] }})"
                                class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg cursor-pointer transition-all">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start gap-4 flex-1">
                                        <span
                                            class="inline-block bg-green-600 text-white rounded-full w-12 h-12 flex items-center justify-center font-bold text-sm">
                                            Sesi {{ count($siswaDetail['riwayat_sesi']) - $index }}
                                        </span>
                                        <div class="flex-1">
                                            <p class="font-semibold text-gray-800">{{ $sesi['surah_text'] }}</p>
                                            <p class="text-gray-600 text-sm">Ayat
                                                {{ $sesi['ayat_mulai'] }}-{{ $sesi['ayat_selesai'] }}
                                            </p>
                                            <p class="text-gray-500 text-sm mt-1">{{ $sesi['tanggal_setor'] }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="grid grid-cols-4 gap-4 text-sm">
                                            <div>
                                                <p class="text-gray-600 text-xs">Tajwid</p>
                                                <p class="font-bold text-gray-800">{{ $sesi['skor_tajwid'] }}</p>
                                            </div>
                                            <div>
                                                <p class="text-gray-600 text-xs">Kelancaran</p>
                                                <p class="font-bold text-gray-800">{{ $sesi['skor_kelancaran'] }}</p>
                                            </div>
                                            <div>
                                                <p class="text-gray-600 text-xs">Makhroj</p>
                                                <p class="font-bold text-gray-800">{{ $sesi['skor_makhroj'] }}</p>
                                            </div>
                                            <div>
                                                <p class="text-gray-600 text-xs">Rata-rata</p>
                                                <p class="font-bold text-green-600">{{ $sesi['nilai_rata'] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500">
                                Tidak ada riwayat sesi
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Target Hafalan yang Belum Dihafalkan -->
                <div class="mt-8">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Target Hafalan yang Belum Dihafalkan</h4>
                    <div class="bg-gray-50 rounded-lg overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-orange-600 text-white">
                                    <th class="px-6 py-3 text-left text-sm font-semibold">No</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold">Nama Surah</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold">Status</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold">Progress</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Menampilkan data target hafalan yang belum dihafalkan -->
                                @forelse ($siswaDetail['target_belum_dihafalkan'] as $index => $target)
                                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} border-b">
                                        <td class="px-6 py-3 text-sm text-gray-800">{{ $target['no'] }}</td>
                                        <td class="px-6 py-3 text-sm font-medium text-gray-800">{{ $target['nama_surah'] }}</td>
                                        <td class="px-6 py-3 text-sm text-gray-600">{{ $target['status'] }}</td>
                                        <td class="px-6 py-3 text-sm text-gray-600">{{ $target['progress'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                            Semua target hafalan sudah selesai
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        @else
            <!-- TAMPILAN 4: DETAIL SURAH -->
            <div wire:key="view-detail-surah">
                <div class="flex items-center justify-between mb-6">
                    <button type="button" wire:click="backToSiswa()"
                        class="flex items-center gap-2 text-green-600 hover:text-green-700 font-semibold mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                            </path>
                        </svg>
                        Kembali
                    </button>

                    <!-- Updated buttons to use new sesi-specific export methods -->
                    <div class="flex gap-2">
                        <button wire:click="downloadPdfSesi()"
                            class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Unduh PDF
                        </button>
                        <button wire:click="downloadExcelSesi()"
                            class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Unduh Excel
                        </button>
                    </div>
                </div>

                <!-- Detail Surah -->
                <div class="bg-white border-2 border-green-600 rounded-lg p-6 mb-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h4 class="text-xl font-bold text-gray-800">{{ $surahDetail['nama_surah'] ?? 'Surah' }}</h4>
                            <p class="text-gray-600">Surah ke-{{ $surahDetail['nomor_surah'] ?? 'N/A' }} |
                                {{ $surahDetail['jumlah_ayat'] ?? 'N/A' }} Ayat
                            </p>
                        </div>
                        <div
                            class="inline-block bg-green-600 text-white rounded-full w-16 h-16 flex items-center justify-center">
                            <span class="text-2xl font-bold">{{ $surahDetail['nomor_surah'] ?? '0' }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-4 gap-4 mt-6 pt-4 border-t border-gray-200">
                        <div>
                            <p class="text-gray-600 text-sm mb-1">Nilai Tajwid</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $surahDetail['nilai_tajwid'] ?? 0 }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm mb-1">Nilai Kelancaran</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $surahDetail['nilai_kelancaran'] ?? 0 }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm mb-1">Nilai Makhroj</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $surahDetail['nilai_makhroj'] ?? 0 }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm mb-1">Rata-rata</p>
                            <p class="text-2xl font-bold text-green-600">{{ $surahDetail['nilai_rata_rata'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <!-- Riwayat Sesi untuk Surah ini -->
                <div>
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Sesi untuk Surah Ini</h4>
                    <div class="space-y-4">
                        @forelse ($surahDetail['sesi_formatnya'] as $index => $sesi)
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-800">{{ $sesi['tanggal_setor'] }}</p>
                                        <p class="text-gray-600 text-sm mt-1">Ayat
                                            {{ $sesi['ayat_mulai'] }}-{{ $sesi['ayat_selesai'] }}
                                        </p>
                                        <button wire:click="selectSesi({{ $sesi['id_sesi'] }})"
                                            class="mt-2 text-sm text-green-600 hover:text-green-800 font-semibold underline">
                                            Lihat Detail Koreksi
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                        @endforelse
                    </div>

                    @if(isset($selectedSesiDetail) && $selectedSesiDetail)
                        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
                            aria-modal="true">
                            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm"
                                wire:click="closeSesiDetail"></div>

                            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                                <div
                                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-4xl border-t-8 border-green-600">

                                    <div
                                        class="bg-green-50 px-6 py-4 flex items-center justify-between border-b border-green-100">
                                        <h3 class="text-xl font-bold text-green-800 flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            Detail Sesi Setoran
                                        </h3>
                                        <button wire:click="closeSesiDetail"
                                            class="text-green-600 hover:text-red-500 transition-colors p-1 rounded-full hover:bg-green-100">
                                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>

                                    <div class="px-6 py-6">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                                            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                                                <p class="text-sm uppercase tracking-wide text-gray-500 font-semibold mb-1">
                                                    Tanggal & Guru</p>
                                                <p class="text-lg font-bold text-gray-800">{{ $selectedSesiDetail['tanggal'] }}
                                                </p>
                                                <p class="text-green-600 font-medium">{{ $selectedSesiDetail['guru'] }}</p>
                                            </div>
                                            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                                                <p class="text-sm uppercase tracking-wide text-gray-500 font-semibold mb-1">
                                                    Capaian Ayat</p>
                                                <div class="flex items-center gap-3">
                                                    <span class="text-2xl font-bold text-green-700">Ayat
                                                        {{ $selectedSesiDetail['ayat_mulai'] }} -
                                                        {{ $selectedSesiDetail['ayat_selesai'] }}</span>
                                                    <span
                                                        class="bg-green-100 text-green-800 text-xs font-bold px-2.5 py-0.5 rounded-full">Tuntas</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-4 gap-4 mb-8 text-center">
                                            <div class="bg-gray-50 p-3 rounded-lg">
                                                <span class="block text-gray-500 text-xs uppercase">Tajwid</span>
                                                <span
                                                    class="block text-xl font-bold text-gray-800">{{ $selectedSesiDetail['nilai_tajwid'] }}</span>
                                            </div>
                                            <div class="bg-gray-50 p-3 rounded-lg">
                                                <span class="block text-gray-500 text-xs uppercase">Kelancaran</span>
                                                <span
                                                    class="block text-xl font-bold text-gray-800">{{ $selectedSesiDetail['nilai_kelancaran'] }}</span>
                                            </div>
                                            <div class="bg-gray-50 p-3 rounded-lg">
                                                <span class="block text-gray-500 text-xs uppercase">Makhroj</span>
                                                <span
                                                    class="block text-xl font-bold text-gray-800">{{ $selectedSesiDetail['nilai_makhroj'] }}</span>
                                            </div>
                                            <div class="bg-green-50 p-3 rounded-lg border border-green-200">
                                                <span class="block text-green-600 text-xs uppercase font-bold">Rata-Rata</span>
                                                <span
                                                    class="block text-2xl font-bold text-green-700">{{ $selectedSesiDetail['nilai_rata'] }}</span>
                                            </div>
                                        </div>

                                        <div>
                                            <h4 class="font-bold text-gray-800 mb-4 flex items-center gap-2 border-b pb-2">
                                                <span class="bg-red-100 text-red-600 p-1 rounded">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                                        fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </span>
                                                Catatan Koreksi
                                            </h4>

                                            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 rounded-lg">
                                                <table class="min-w-full divide-y divide-gray-300">
                                                    <thead class="bg-green-600 text-white">
                                                        <tr>
                                                            <th scope="col"
                                                                class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold">Lokasi
                                                            </th>
                                                            <th scope="col"
                                                                class="px-3 py-3.5 text-center text-sm font-semibold">Sesi Ke-
                                                            </th>
                                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold">
                                                                Jenis Kesalahan</th>
                                                            <th scope="col"
                                                                class="px-3 py-3.5 text-right text-sm font-semibold">Catatan
                                                                (Lafadz)</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-gray-200 bg-white">
                                                        @forelse($selectedSesiDetail['koreksi'] as $kor)
                                                            <tr class="hover:bg-green-50 transition-colors">
                                                                <td
                                                                    class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900">
                                                                    {{ $kor['lokasi'] }}
                                                                </td>
                                                                <td
                                                                    class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 text-center">
                                                                    <span
                                                                        class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                                                        Sesi #{{ $kor['sesi_ke'] }}
                                                                    </span>
                                                                </td>
                                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-red-600">
                                                                    {{ $kor['jenis_kesalahan'] }}
                                                                </td>
                                                                <td class="px-3 py-4 text-sm text-gray-800 text-right font-arabic"
                                                                    dir="rtl"
                                                                    style="font-family: 'Amiri', serif; font-size: 1.1em;">
                                                                    {{ $kor['catatan'] }}
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="4"
                                                                    class="px-3 py-8 text-center text-sm text-gray-500 italic">
                                                                    Alhamdulillah, tidak ada catatan koreksi untuk sesi ini.
                                                                </td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-100">
                                        <button type="button" wire:click="closeSesiDetail"
                                            class="inline-flex w-full justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 sm:ml-3 sm:w-auto transition-all">
                                            Tutup Detail
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </div>

        @endif

    </div>
</div>