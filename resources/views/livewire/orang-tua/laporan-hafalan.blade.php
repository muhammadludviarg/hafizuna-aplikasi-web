<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Laporan Hafalan Anak') }}
    </h2>
</x-slot>

<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 text-gray-900 dark:text-gray-100">

        @if (!$selectedSiswaId)
            <div wire:key="view-pilih-anak">
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Putra/Putri Anda</h3>
                <p class="text-gray-600 mb-6">Pilih anak untuk melihat laporan hafalan</p>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($anakList as $anak)
                        <div wire:click="selectSiswa({{ $anak['id_siswa'] }})"
                            class="bg-white border-2 border-gray-200 rounded-lg p-6 cursor-pointer hover:shadow-lg hover:border-green-500 transition-all duration-200 group">

                            <div class="flex items-center gap-4 mb-4">
                                <div
                                    class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center text-xl font-bold text-green-700 border-2 border-green-200 group-hover:bg-green-600 group-hover:text-white transition-colors">
                                    {{ substr($anak['nama_siswa'], 0, 1) }}
                                </div>
                                <div>
                                    <h4 class="text-lg font-bold text-gray-800 group-hover:text-green-700">
                                        {{ $anak['nama_siswa'] }}
                                    </h4>
                                    {{-- UPDATED: Menampilkan Progress Target --}}
                                    <p class="text-sm text-gray-500 flex items-center gap-1">
                                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Target: {{ $anak['progress_target'] }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center justify-between border-t pt-3 mt-2">
                                <span class="text-sm font-semibold text-gray-600">Lihat Laporan</span>
                                <svg class="w-5 h-5 text-green-600 group-hover:translate-x-1 transition-transform" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                    </path>
                                </svg>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-12 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                            <p class="text-gray-500">Data anak belum terhubung ke akun Anda.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        @elseif ($selectedSiswaId && !$selectedSurahId)
            <div wire:key="view-detail-siswa">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                    <button type="button" wire:click="backToSiswaList"
                        class="flex items-center gap-2 text-green-600 hover:text-green-700 font-semibold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                            </path>
                        </svg>
                        Ganti Anak
                    </button>

                    <div class="flex gap-2">
                        <button wire:click="downloadPdfSiswa()"
                            class="flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold shadow-sm text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            PDF
                        </button>
                        <button wire:click="downloadExcelSiswa()"
                            class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold shadow-sm text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            Excel
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                    <div class="border-2 border-green-600 rounded-lg p-4 bg-green-50">
                        <p class="text-green-800 text-xs md:text-sm mb-1 uppercase tracking-wide font-semibold">Total Sesi
                        </p>
                        <p class="text-3xl font-bold text-green-600">{{ $siswaDetail['jumlah_sesi'] ?? 0 }}</p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4 bg-white shadow-sm">
                        <p class="text-gray-600 text-sm mb-1">Nilai Tajwid</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $siswaDetail['nilai_tajwid'] ?? 0 }}</p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4 bg-white shadow-sm">
                        <p class="text-gray-600 text-sm mb-1">Nilai Kelancaran</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $siswaDetail['nilai_kelancaran'] ?? 0 }}</p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4 bg-white shadow-sm">
                        <p class="text-gray-600 text-sm mb-1">Nilai Makhroj</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $siswaDetail['nilai_makhroj'] ?? 0 }}</p>
                    </div>
                    <div class="border-2 border-green-100 rounded-lg p-4 bg-white shadow-sm">
                        <p class="text-gray-600 text-sm mb-1">Rata-rata</p>
                        <p class="text-2xl font-bold text-green-600">{{ $siswaDetail['nilai_rata_rata'] ?? 0 }}</p>
                    </div>
                </div>

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
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                            <input type="date" wire:model="tanggalAkhir"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        </div>
                    </div>
                    <button wire:click="filterPeriode()"
                        class="mt-4 px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold text-sm">
                        Terapkan Filter
                    </button>
                </div>

                {{-- RIWAYAT SESI --}}
                <div class="mb-8">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="w-1 h-6 bg-green-500 rounded-full block"></span>
                        Riwayat Sesi Setoran
                    </h4>
                    <div class="space-y-4">
                        @forelse ($siswaDetail['riwayat_sesi'] as $index => $sesi)
                            <div wire:click="selectSurah({{ $sesi['id_surah_mulai'] }})"
                                class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg cursor-pointer transition-all">
                                <div class="flex flex-col sm:flex-row gap-4 justify-between">
                                    <div class="flex items-start gap-4 flex-1">
                                        <span
                                            class="flex-shrink-0 inline-block bg-green-600 text-white rounded-full w-10 h-10 flex items-center justify-center font-bold text-sm">
                                            #{{ count($siswaDetail['riwayat_sesi']) - $index }}
                                        </span>
                                        <div class="flex-1">
                                            <p class="font-bold text-gray-800 text-base">{{ $sesi['surah_text'] }}</p>
                                            <div
                                                class="inline-flex items-center mt-1 px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-600 mb-1">
                                                Ayat {{ $sesi['ayat_mulai'] }}-{{ $sesi['ayat_selesai'] }}
                                            </div>
                                            <p class="text-gray-500 text-xs flex items-center gap-1">
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
                                        class="text-right mt-2 sm:mt-0 border-t sm:border-t-0 sm:border-l border-gray-100 pt-2 sm:pt-0 sm:pl-4">
                                        <div class="grid grid-cols-4 gap-2 sm:gap-4 text-sm">
                                            <div class="text-center">
                                                <p class="text-[10px] text-gray-500 uppercase">Tajwid</p>
                                                <p class="font-bold text-gray-800">{{ $sesi['skor_tajwid'] }}</p>
                                            </div>
                                            <div class="text-center">
                                                <p class="text-[10px] text-gray-500 uppercase">Lancar</p>
                                                <p class="font-bold text-gray-800">{{ $sesi['skor_kelancaran'] }}</p>
                                            </div>
                                            <div class="text-center">
                                                <p class="text-[10px] text-gray-500 uppercase">Makhroj</p>
                                                <p class="font-bold text-gray-800">{{ $sesi['skor_makhroj'] }}</p>
                                            </div>
                                            <div class="text-center bg-green-50 rounded p-1">
                                                <p class="text-[10px] text-green-600 uppercase font-bold">Rata</p>
                                                <p class="font-bold text-green-600">{{ $sesi['nilai_rata'] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500 bg-gray-50 border border-dashed rounded-lg">Tidak ada
                                riwayat sesi</div>
                        @endforelse
                    </div>
                </div>

                {{-- TABEL SURAH SUDAH DIHAFAL (BARU) --}}
                <div class="mb-8">
                    <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="w-1 h-6 bg-blue-600 rounded-full block"></span>
                        Surah Sudah Dihafal (Tuntas)
                    </h4>

                    <div class="w-full overflow-x-auto rounded-lg border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-blue-50">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider whitespace-nowrap">
                                        No</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider whitespace-nowrap">
                                        Surah</th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-semibold text-blue-800 uppercase tracking-wider whitespace-nowrap">
                                        Total Sesi</th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-semibold text-blue-800 uppercase tracking-wider whitespace-nowrap">
                                        Tajwid</th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-semibold text-blue-800 uppercase tracking-wider whitespace-nowrap">
                                        Lancar</th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-semibold text-blue-800 uppercase tracking-wider whitespace-nowrap">
                                        Makhroj</th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-semibold text-blue-800 uppercase tracking-wider whitespace-nowrap">
                                        Nilai Akhir</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($siswaDetail['surah_sudah_dihafal'] as $index => $item)
                                    <tr class="hover:bg-blue-50 transition-colors">
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3 text-sm font-bold text-gray-800">{{ $item['nama_surah'] }}</td>
                                        <td class="px-4 py-3 text-sm text-center text-gray-600">{{ $item['jumlah_sesi'] }}x</td>
                                        <td class="px-4 py-3 text-sm text-center text-gray-600">{{ $item['nilai_tajwid'] }}</td>
                                        <td class="px-4 py-3 text-sm text-center text-gray-600">{{ $item['nilai_kelancaran'] }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-center text-gray-600">{{ $item['nilai_makhroj'] }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
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

                {{-- TARGET BELUM TUNTAS (UPDATED) --}}
                <div class="mt-8">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="w-1 h-6 bg-orange-500 rounded-full block"></span>
                        Target Hafalan yang Belum Tuntas
                    </h4>
                    <div class="bg-white border border-gray-200 rounded-lg overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-orange-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-orange-800 uppercase">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-orange-800 uppercase">Nama
                                        Surah</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-orange-800 uppercase">Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-orange-800 uppercase">Progress
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($siswaDetail['target_belum_dihafalkan'] as $index => $target)
                                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                                        <td class="px-6 py-3 text-sm text-gray-600">{{ $target['no'] }}</td>
                                        <td class="px-6 py-3 text-sm font-medium text-gray-800">{{ $target['nama_surah'] }}</td>
                                        <td class="px-6 py-3 text-sm">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $target['status'] == 'Sedang Menghafal' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $target['status'] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3 text-sm text-gray-600">{{ $target['progress'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-gray-500 italic">
                                            Semua target hafalan sudah selesai!
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        @else
            {{-- BAGIAN DETAIL SURAH & SESI --}}
            <div wire:key="view-detail-surah">
                <div class="flex items-center justify-between mb-6">
                    <button type="button" wire:click="backToSiswa"
                        class="flex items-center gap-2 text-green-600 hover:text-green-700 font-semibold mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

                {{-- Saya sertakan bagian header surah agar lengkap --}}
                <div
                    class="bg-white border-l-4 border-green-600 rounded-r-lg shadow-sm p-6 mb-6 flex items-center justify-between">
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

                {{-- Grid Nilai Surah --}}
                <div class="grid grid-cols-4 gap-4 mt-6 pt-4 border-t border-gray-200 mb-6">
                    <div class="text-center bg-gray-50 p-2 rounded">
                        <p class="text-gray-500 text-xs mb-1 uppercase">Tajwid</p>
                        <p class="text-xl font-bold text-gray-800">{{ $surahDetail['nilai_tajwid'] ?? 0 }}</p>
                    </div>
                    <div class="text-center bg-gray-50 p-2 rounded">
                        <p class="text-gray-500 text-xs mb-1 uppercase">Lancar</p>
                        <p class="text-xl font-bold text-gray-800">{{ $surahDetail['nilai_kelancaran'] ?? 0 }}</p>
                    </div>
                    <div class="text-center bg-gray-50 p-2 rounded">
                        <p class="text-gray-500 text-xs mb-1 uppercase">Makhroj</p>
                        <p class="text-xl font-bold text-gray-800">{{ $surahDetail['nilai_makhroj'] ?? 0 }}</p>
                    </div>
                    <div class="text-center bg-green-50 p-2 rounded border border-green-100">
                        <p class="text-green-600 text-xs mb-1 uppercase font-bold">Rata-rata</p>
                        <p class="text-xl font-bold text-green-600">{{ $surahDetail['nilai_rata_rata'] ?? 0 }}</p>
                    </div>
                </div>

                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Sesi untuk Surah Ini</h4>
                    <div class="space-y-4">
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
                    {{-- MODAL SESI (Sama persis dengan kode Guru/Admin) --}}
                    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
                        aria-modal="true">
                        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm"
                            wire:click="closeSesiDetail"></div>

                        <div class="flex min-h-full items-end justify-center p-0 md:p-4 text-center sm:items-center sm:p-0">
                            <div
                                class="relative transform overflow-hidden rounded-t-2xl md:rounded-2xl bg-white text-left shadow-xl transition-all w-full sm:max-w-2xl md:max-w-4xl max-h-[90vh] flex flex-col">

                                <div class="bg-green-600 px-4 py-3 md:px-6 md:py-4 flex items-center justify-between shrink-0">
                                    <h3 class="text-lg md:text-xl font-bold text-white flex items-center gap-2">Detail Sesi</h3>
                                    <button wire:click="closeSesiDetail"
                                        class="text-green-100 hover:text-white transition-colors">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="p-4 md:p-6 overflow-y-auto">
                                    {{-- ISI MODAL SAMA DENGAN GURU/ADMIN --}}
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
                                        </div>
                                    </div>

                                    {{-- Grid Nilai Modal --}}
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

                                    {{-- Tabel Koreksi --}}
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