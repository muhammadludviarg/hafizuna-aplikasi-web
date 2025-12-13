<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Laporan Hafalan Kelompok') }}
        </h2>
    </x-slot>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-4 md:p-6 text-gray-900 dark:text-gray-100">

            {{-- ========================================================================
            BAGIAN 1: PILIH KELOMPOK (KHUSUS GURU)
            ======================================================================== --}}
            @if (!$selectedKelompokId)
                <div wire:key="view-kelompok">
                    <h3 class="text-xl md:text-2xl font-bold text-gray-800 mb-2">Pilih Kelompok</h3>
                    <p class="text-sm md:text-base text-gray-600 mb-6">Pilih kelompok halaqoh untuk melihat laporan</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        @forelse ($kelompokList as $kelompok)
                            <div wire:click="selectKelompok({{ $kelompok['id'] }})"
                                class="group bg-white border-2 border-gray-200 rounded-xl p-5 md:p-6 cursor-pointer hover:shadow-xl hover:shadow-green-100 hover:border-green-500 transition-all duration-300 transform hover:-translate-y-1 active:scale-95">
                                <h4 class="text-lg font-bold text-gray-800 mb-1 group-hover:text-green-700 transition-colors">
                                    {{ $kelompok['nama_kelompok'] }}
                                </h4>
                                {{-- Menampilkan info kelas di bawah nama kelompok --}}
                                <p class="text-sm text-gray-500 mb-4">
                                    {{-- Asumsi di controller nama_kelompok sudah digabung dengan kelas atau ada field lain --}}
                                    {{-- Jika murni nama kelompok, bisa dikosongkan atau diganti info lain --}}
                                </p>

                                <div class="flex items-center justify-between">
                                    <span
                                        class="text-xs font-bold text-green-700 bg-green-50 border border-green-100 px-3 py-1.5 rounded-full group-hover:bg-green-600 group-hover:text-white transition-colors">
                                        {{ $kelompok['jumlah_siswa'] }} siswa
                                    </span>
                                    <div
                                        class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center group-hover:bg-green-100 transition-colors">
                                        <svg class="w-5 h-5 text-gray-400 group-hover:text-green-600" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div
                                class="col-span-full text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                                <p class="text-gray-500">Belum ada kelompok yang diampu</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- ========================================================================
                BAGIAN 2: DAFTAR SISWA DALAM KELOMPOK
                ======================================================================== --}}
            @elseif (!$selectedSiswaId && !$selectedSurahId)
                <div wire:key="view-daftar-siswa">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                        <div>
                            {{-- Tombol Kembali ke List Kelompok --}}
                            <button wire:click="backToList()"
                                class="inline-flex items-center text-green-600 hover:text-green-800 font-bold transition-colors mb-2 group">
                                <svg class="w-5 h-5 mr-1 transform group-hover:-translate-x-1 transition-transform"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Kembali
                            </button>
                            <h3 class="text-xl md:text-2xl font-bold text-gray-800">
                                {{ $kelompokDetail['nama_kelompok'] ?? 'Detail Kelompok' }}
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">
                                Kelas: {{ $kelompokDetail['nama_kelas'] ?? '-' }}
                            </p>
                        </div>

                        <div class="flex flex-row w-full md:w-auto gap-3 self-end mt-2 md:mt-0">
                            <button wire:click="downloadPdf()"
                                class="flex-1 md:flex-none flex justify-center items-center gap-2 px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 hover:shadow-lg transition-all font-semibold text-sm">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                <span>PDF</span>
                            </button>
                            <button wire:click="downloadExcel()"
                                class="flex-1 md:flex-none flex justify-center items-center gap-2 px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 hover:shadow-lg transition-all font-semibold text-sm">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                <span>Excel</span>
                            </button>
                        </div>
                    </div>

                    {{-- Wrapper Tabel Scrollable --}}
                    <div class="w-full overflow-x-auto border border-gray-300 rounded-xl shadow-sm">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-green-700 text-white">
                                <tr>
                                    <th
                                        class="px-4 md:px-6 py-4 text-center text-xs md:text-sm font-bold uppercase tracking-wider whitespace-nowrap">
                                        No</th>
                                    <th
                                        class="px-4 md:px-6 py-4 text-left text-xs md:text-sm font-bold uppercase tracking-wider whitespace-nowrap">
                                        Nama Siswa</th>
                                    <th
                                        class="px-4 md:px-6 py-4 text-center text-xs md:text-sm font-bold uppercase tracking-wider whitespace-nowrap">
                                        Jumlah Sesi</th>
                                    <th
                                        class="px-4 md:px-6 py-4 text-center text-xs md:text-sm font-bold uppercase tracking-wider whitespace-nowrap">
                                        Progres Target</th>
                                    <th
                                        class="px-4 md:px-6 py-4 text-center text-xs md:text-sm font-bold uppercase tracking-wider whitespace-nowrap">
                                        Rata-rata</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($detailLaporan as $index => $siswa)
                                    <tr wire:click="selectSiswa({{ $siswa['id_siswa'] }})"
                                        class="hover:bg-green-50 cursor-pointer transition-colors duration-150 group">
                                        <td class="px-4 md:px-6 py-4 text-center text-sm text-gray-700">{{ $index + 1 }}</td>
                                        <td
                                            class="px-4 md:px-6 py-4 text-left text-sm font-bold text-gray-800 group-hover:text-green-700">
                                            {{ $siswa['nama_siswa'] }}
                                        </td>
                                        <td class="px-4 md:px-6 py-4 text-center text-sm text-gray-700">
                                            <span
                                                class="inline-block bg-gray-100 rounded px-2 py-1 font-semibold text-gray-600 group-hover:bg-white group-hover:border group-hover:border-green-200">
                                                {{ $siswa['jumlah_sesi'] }}
                                            </span>
                                        </td>
                                        <td class="px-4 md:px-6 py-4 text-center text-sm text-gray-700">
                                            {{ $siswa['progress_target'] }}
                                        </td>
                                        <td class="px-4 md:px-6 py-4 text-center text-sm">
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 border border-green-200">
                                                {{ $siswa['nilai_rata_rata'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic bg-gray-50">
                                            Tidak ada data siswa dalam kelompok ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- ========================================================================
                BAGIAN 3: DETAIL SISWA (SAMA PERSIS DENGAN ADMIN, KECUALI TOMBOL KEMBALI)
                ======================================================================== --}}
            @elseif ($selectedSiswaId && !$selectedSurahId)
                <div wire:key="view-detail-siswa">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">

                        {{-- Tombol Kembali ke Kelompok --}}
                        <button wire:click="backToKelompok()"
                            class="inline-flex items-center text-green-600 hover:text-green-800 font-bold transition-colors group w-max">
                            <svg class="w-5 h-5 mr-1 transform group-hover:-translate-x-1 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                                </path>
                            </svg>
                            Kembali
                        </button>

                        {{-- Tombol Download --}}
                        <div class="flex flex-row w-full md:w-auto gap-3 self-end mt-2 md:mt-0">
                            <button wire:click="downloadPdfSiswa()"
                                class="flex-1 md:flex-none flex justify-center items-center gap-2 px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold shadow-sm hover:shadow-md text-sm transition-all">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                <span>PDF</span>
                            </button>
                            <button wire:click="downloadExcelSiswa()"
                                class="flex-1 md:flex-none flex justify-center items-center gap-2 px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold shadow-sm hover:shadow-md text-sm transition-all">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                <span>Excel</span>
                            </button>
                        </div>
                    </div>

                    {{-- STATS CARDS, FILTER, LIST SURAH TUNTAS, TARGET, RIWAYAT SESI (Sama Persis) --}}
                    {{-- ... (Gunakan kode yang sama dengan Admin mulai dari sini sampai akhir file) ... --}}

                    {{-- STATS CARDS --}}
                    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 md:gap-4 mb-6">
                        <div
                            class="col-span-2 lg:col-span-1 border border-green-200 bg-green-50 rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow">
                            <p class="text-green-800 text-xs md:text-sm mb-1 uppercase tracking-wide font-bold">Total Sesi
                            </p>
                            <p class="text-3xl font-extrabold text-green-700">{{ $siswaDetail['jumlah_sesi'] ?? 0 }}</p>
                        </div>
                        <div
                            class="border border-gray-200 rounded-xl p-4 bg-white shadow-sm hover:shadow-md transition-shadow hover:border-green-300">
                            <p class="text-gray-500 text-xs md:text-sm mb-1 font-semibold uppercase">Tajwid</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $siswaDetail['nilai_tajwid'] ?? 0 }}</p>
                        </div>
                        <div
                            class="border border-gray-200 rounded-xl p-4 bg-white shadow-sm hover:shadow-md transition-shadow hover:border-green-300">
                            <p class="text-gray-500 text-xs md:text-sm mb-1 font-semibold uppercase">Kelancaran</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $siswaDetail['nilai_kelancaran'] ?? 0 }}</p>
                        </div>
                        <div
                            class="border border-gray-200 rounded-xl p-4 bg-white shadow-sm hover:shadow-md transition-shadow hover:border-green-300">
                            <p class="text-gray-500 text-xs md:text-sm mb-1 font-semibold uppercase">Makhroj</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $siswaDetail['nilai_makhroj'] ?? 0 }}</p>
                        </div>
                        <div
                            class="col-span-2 lg:col-span-1 border border-green-500 bg-green-600 rounded-xl p-4 shadow-md text-white">
                            <p class="text-green-100 text-xs md:text-sm mb-1 uppercase font-bold">Rata-rata Total</p>
                            <p class="text-3xl font-extrabold">{{ $siswaDetail['nilai_rata_rata'] ?? 0 }}</p>
                        </div>
                    </div>

                    {{-- FILTER TANGGAL --}}
                    <div class="bg-white rounded-xl p-5 mb-8 border border-gray-200 shadow-sm">
                        <div class="flex items-center gap-2 mb-4 text-gray-800">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            <span class="font-bold text-base">Filter Periode</span>
                        </div>
                        <div class="flex flex-col md:flex-row gap-4 items-end">
                            <div class="w-full md:w-auto">
                                <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">Dari Tanggal</label>
                                <input type="date" wire:model="tanggalMulai"
                                    class="w-full md:w-48 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all shadow-sm">
                            </div>
                            <div class="w-full md:w-auto">
                                <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">Sampai Tanggal</label>
                                <input type="date" wire:model="tanggalAkhir"
                                    class="w-full md:w-48 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all shadow-sm">
                            </div>
                            <div class="w-full md:w-auto">
                                <button wire:click="filterPeriode()"
                                    class="w-full md:w-auto px-6 py-2 bg-green-700 text-white rounded-lg hover:bg-green-800 text-sm font-semibold transition-colors shadow-sm active:transform active:scale-95">Terapkan</button>
                            </div>
                        </div>
                    </div>

                    {{-- URUTAN 1: SURAH TUNTAS --}}
                    <div class="mb-10">
                        <h4 class="text-xl font-bold text-gray-800 mb-5 flex items-center gap-3">
                            <div class="w-1.5 h-8 bg-blue-600 rounded-full"></div> Surah Tuntas (Sudah Dihafal)
                        </h4>
                        <div class="w-full overflow-x-auto rounded-xl border border-gray-300 shadow-sm">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-blue-600 text-white">
                                    <tr>
                                        <th
                                            class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider whitespace-nowrap">
                                            No</th>
                                        <th
                                            class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider whitespace-nowrap">
                                            Surah</th>
                                        <th
                                            class="px-5 py-3.5 text-center text-xs font-bold uppercase tracking-wider whitespace-nowrap">
                                            Total Sesi</th>
                                        <th
                                            class="px-5 py-3.5 text-center text-xs font-bold uppercase tracking-wider whitespace-nowrap">
                                            Tajwid</th>
                                        <th
                                            class="px-5 py-3.5 text-center text-xs font-bold uppercase tracking-wider whitespace-nowrap">
                                            Lancar</th>
                                        <th
                                            class="px-5 py-3.5 text-center text-xs font-bold uppercase tracking-wider whitespace-nowrap">
                                            Makhroj</th>
                                        <th
                                            class="px-5 py-3.5 text-center text-xs font-bold uppercase tracking-wider whitespace-nowrap">
                                            Nilai Akhir</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($siswaDetail['surah_sudah_dihafal'] as $index => $item)
                                        <tr wire:click="selectSurah({{ $item['id_surah'] }})"
                                            class="hover:bg-blue-50 transition-colors cursor-pointer group">
                                            <td class="px-5 py-4 text-sm text-gray-600 font-medium">{{ $item['nomor_surah'] }}
                                            </td>
                                            <td
                                                class="px-5 py-4 text-sm font-bold text-gray-800 whitespace-nowrap group-hover:text-blue-700">
                                                {{ $item['nama_surah'] }}</td>
                                            <td class="px-5 py-4 text-sm text-center text-gray-600"><span
                                                    class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded text-xs font-bold">{{ $item['jumlah_sesi'] }}x</span>
                                            </td>
                                            <td class="px-5 py-4 text-sm text-center text-gray-600">{{ $item['nilai_tajwid'] }}
                                            </td>
                                            <td class="px-5 py-4 text-sm text-center text-gray-600">
                                                {{ $item['nilai_kelancaran'] }}</td>
                                            <td class="px-5 py-4 text-sm text-center text-gray-600">{{ $item['nilai_makhroj'] }}
                                            </td>
                                            <td class="px-5 py-4 text-center"><span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">{{ $item['nilai_rata'] }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-4 py-8 text-center text-gray-500 italic">Belum ada surah
                                                yang tuntas 100%.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($totalPagesSurahSelesai > 1)
                            <div
                                class="mt-5 flex flex-col sm:flex-row items-center justify-between border-t border-gray-200 pt-4 gap-4">
                                <div class="text-sm text-gray-600 font-medium text-center sm:text-left">Halaman <span
                                        class="text-gray-900 font-bold">{{ $currentPageSurahSelesai }}</span> dari
                                    {{ $totalPagesSurahSelesai }}</div>
                                <div class="flex gap-2 w-full sm:w-auto justify-center">
                                    <button wire:click="prevPageSurahSelesai" @if($currentPageSurahSelesai <= 1) disabled @endif
                                        class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition text-sm font-medium shadow-sm w-1/2 sm:w-auto">←
                                        Sebelumnya</button>
                                    <button wire:click="nextPageSurahSelesai" @if($currentPageSurahSelesai >= $totalPagesSurahSelesai) disabled @endif
                                        class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition text-sm font-medium shadow-sm w-1/2 sm:w-auto">Selanjutnya
                                        →</button>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- URUTAN 2: TARGET BELUM TUNTAS --}}
                    <div class="mb-10">
                        <h4 class="text-xl font-bold text-gray-800 mb-5 flex items-center gap-3">
                            <div class="w-1.5 h-8 bg-orange-500 rounded-full"></div> Target Belum Tuntas
                        </h4>
                        <div class="w-full overflow-x-auto rounded-xl border border-gray-300 shadow-sm">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-orange-500 text-white">
                                    <tr>
                                        <th
                                            class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider whitespace-nowrap">
                                            No</th>
                                        <th
                                            class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider whitespace-nowrap">
                                            Surah</th>
                                        <th
                                            class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider whitespace-nowrap">
                                            Status</th>
                                        <th
                                            class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider whitespace-nowrap">
                                            Progress</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($siswaDetail['target_belum_dihafalkan'] as $index => $target)
                                        <tr class="hover:bg-orange-50 transition-colors">
                                            <td class="px-5 py-4 text-sm text-gray-600 font-medium">{{ $target['no'] }}</td>
                                            <td class="px-5 py-4 text-sm font-bold text-gray-800 whitespace-nowrap">
                                                {{ $target['nama_surah'] }}</td>
                                            <td class="px-5 py-4 text-sm whitespace-nowrap"><span
                                                    class="inline-flex items-center px-2.5 py-1 rounded text-xs font-bold bg-orange-100 text-orange-800 border border-orange-200">{{ $target['status'] }}</span>
                                            </td>
                                            <td class="px-5 py-4 text-sm text-gray-600 font-medium whitespace-nowrap">
                                                {{ $target['progress'] }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-8 text-center text-gray-500 italic">Alhamdulillah,
                                                semua target sudah tuntas!</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($totalPagesTarget > 1)
                            <div
                                class="mt-5 flex flex-col sm:flex-row items-center justify-between border-t border-gray-200 pt-4 gap-4">
                                <div class="text-sm text-gray-600 font-medium text-center sm:text-left">Halaman <span
                                        class="text-gray-900 font-bold">{{ $currentPageTarget }}</span> dari
                                    {{ $totalPagesTarget }}</div>
                                <div class="flex gap-2 w-full sm:w-auto justify-center">
                                    <button wire:click="prevPageTarget" @if($currentPageTarget <= 1) disabled @endif
                                        class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition text-sm font-medium shadow-sm w-1/2 sm:w-auto">←
                                        Sebelumnya</button>
                                    <button wire:click="nextPageTarget" @if($currentPageTarget >= $totalPagesTarget) disabled
                                    @endif
                                        class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition text-sm font-medium shadow-sm w-1/2 sm:w-auto">Selanjutnya
                                        →</button>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- URUTAN 3: RIWAYAT SESI (LIST) --}}
                    <div>
                        <h4 class="text-xl font-bold text-gray-800 mb-5 flex items-center gap-3">
                            <div class="w-1.5 h-8 bg-green-600 rounded-full"></div>
                            <div>Riwayat Sesi <span class="block text-xs font-normal text-gray-500 mt-0.5">Total
                                    {{ count($siswaDetail['riwayat_sesi']) > 0 ? $siswaDetail['jumlah_sesi'] : 0 }} sesi
                                    terekam</span></div>
                        </h4>
                        <div class="space-y-3">
                            @forelse ($siswaDetail['riwayat_sesi'] as $index => $sesi)
                                <div wire:click="selectSesi({{ $sesi['id_sesi'] }})"
                                    class="group bg-white border border-gray-200 rounded-xl p-4 hover:shadow-lg hover:border-green-400 cursor-pointer transition-all duration-200 active:bg-gray-50">
                                    <div class="flex flex-col sm:flex-row gap-4 items-center">
                                        <div class="flex items-start gap-4 flex-1 w-full">
                                            <span
                                                class="flex-shrink-0 bg-green-50 text-green-700 border border-green-100 rounded-lg w-12 h-12 flex items-center justify-center font-bold text-lg group-hover:bg-green-600 group-hover:text-white transition-colors">{{ (($currentPageSesi - 1) * $perPageSesi) + $index + 1 }}</span>
                                            <div>
                                                <p
                                                    class="font-bold text-gray-800 text-base md:text-lg group-hover:text-green-700 transition-colors">
                                                    {{ $sesi['surah_text'] }}</p>
                                                <div class="flex flex-wrap gap-2 mt-1">
                                                    <span
                                                        class="text-xs font-semibold text-gray-600 bg-gray-100 px-2 py-1 rounded border border-gray-200">Ayat
                                                        {{ $sesi['ayat_mulai'] }}-{{ $sesi['ayat_selesai'] }}</span>
                                                    <span class="text-xs text-gray-500 flex items-center gap-1 px-1"><svg
                                                            class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M8 7V3m8 4V3m-9 8h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                            </path>
                                                        </svg>{{ $sesi['tanggal_setor'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="w-full sm:w-auto grid grid-cols-4 gap-2 sm:gap-6 border-t sm:border-t-0 sm:border-l border-gray-100 pt-3 sm:pt-0 sm:pl-6">
                                            <div class="text-center">
                                                <p class="text-[10px] text-gray-400 uppercase font-bold mb-1">Tajwid</p>
                                                <p class="font-bold text-gray-700 text-base">{{ $sesi['skor_tajwid'] }}</p>
                                            </div>
                                            <div class="text-center">
                                                <p class="text-[10px] text-gray-400 uppercase font-bold mb-1">Lancar</p>
                                                <p class="font-bold text-gray-700 text-base">{{ $sesi['skor_kelancaran'] }}</p>
                                            </div>
                                            <div class="text-center">
                                                <p class="text-[10px] text-gray-400 uppercase font-bold mb-1">Makhroj</p>
                                                <p class="font-bold text-gray-700 text-base">{{ $sesi['skor_makhroj'] }}</p>
                                            </div>
                                            <div class="text-center bg-green-50 rounded-lg p-1 border border-green-100">
                                                <p class="text-[10px] text-green-700 uppercase font-bold mb-0.5">Rata</p>
                                                <p class="font-extrabold text-green-700 text-base">{{ $sesi['nilai_rata'] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div
                                    class="text-center py-10 text-gray-500 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                                    Belum ada riwayat sesi</div>
                            @endforelse
                        </div>
                        @if($totalPagesSesi > 1)
                            <div
                                class="mt-5 flex flex-col sm:flex-row items-center justify-between border-t border-gray-200 pt-4 gap-4">
                                <div class="text-sm text-gray-600 font-medium text-center sm:text-left">Halaman <span
                                        class="text-gray-900 font-bold">{{ $currentPageSesi }}</span> dari {{ $totalPagesSesi }}
                                </div>
                                <div class="flex gap-2 w-full sm:w-auto justify-center">
                                    <button wire:click="prevPageSesi" @if($currentPageSesi <= 1) disabled @endif
                                        class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition text-sm font-medium shadow-sm w-1/2 sm:w-auto">←
                                        Sebelumnya</button>
                                    <button wire:click="nextPageSesi" @if($currentPageSesi >= $totalPagesSesi) disabled @endif
                                        class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition text-sm font-medium shadow-sm w-1/2 sm:w-auto">Selanjutnya
                                        →</button>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- MODAL DETAIL SESI (POPUP) --}}
                    @if(isset($selectedSesiDetail) && $selectedSesiDetail)
                        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
                            aria-modal="true">
                            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm"
                                wire:click="closeSesiDetail"></div>
                            <div class="flex min-h-full items-end justify-center p-0 md:p-4 text-center sm:items-center sm:p-0">
                                <div
                                    class="relative transform overflow-hidden rounded-t-2xl md:rounded-2xl bg-white text-left shadow-2xl transition-all w-full sm:max-w-2xl md:max-w-4xl max-h-[90vh] flex flex-col">
                                    <div class="bg-green-700 px-4 py-4 md:px-6 flex items-center justify-between shrink-0">
                                        <h3 class="text-lg md:text-xl font-bold text-white flex items-center gap-2">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                                </path>
                                            </svg>
                                            Detail Sesi
                                        </h3>
                                        <button wire:click="closeSesiDetail"
                                            class="text-green-200 hover:text-white transition-colors hover:bg-green-600 rounded-full p-1">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="p-6 overflow-y-auto">
                                        <div class="grid grid-cols-2 gap-4 mb-6">
                                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                                                <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Tanggal</p>
                                                <p class="font-bold text-gray-800 text-lg">{{ $selectedSesiDetail['tanggal'] }}
                                                </p>
                                            </div>
                                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                                                <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Guru
                                                    Pengampu</p>
                                                <p class="font-bold text-gray-800 text-sm md:text-base truncate">
                                                    {{ $selectedSesiDetail['guru'] }}</p>
                                            </div>
                                        </div>
                                        <div class="mb-8 text-center bg-green-50 rounded-xl p-6 border border-green-100">
                                            <p class="text-sm font-semibold text-green-700 uppercase tracking-wide mb-2">Capaian
                                                Ayat</p>
                                            <div class="inline-flex items-center gap-3">
                                                <span
                                                    class="text-3xl font-extrabold text-gray-800">{{ $selectedSesiDetail['ayat_mulai'] }}</span>
                                                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                                </svg>
                                                <span
                                                    class="text-3xl font-extrabold text-gray-800">{{ $selectedSesiDetail['ayat_selesai'] }}</span>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-4 gap-3 mb-8">
                                            <div class="text-center p-3 bg-white rounded-xl border border-gray-200 shadow-sm">
                                                <span
                                                    class="block text-[10px] text-gray-400 uppercase font-bold">Tajwid</span><span
                                                    class="block text-xl font-bold text-gray-800">{{ $selectedSesiDetail['nilai_tajwid'] }}</span>
                                            </div>
                                            <div class="text-center p-3 bg-white rounded-xl border border-gray-200 shadow-sm">
                                                <span
                                                    class="block text-[10px] text-gray-400 uppercase font-bold">Lancar</span><span
                                                    class="block text-xl font-bold text-gray-800">{{ $selectedSesiDetail['nilai_kelancaran'] }}</span>
                                            </div>
                                            <div class="text-center p-3 bg-white rounded-xl border border-gray-200 shadow-sm">
                                                <span
                                                    class="block text-[10px] text-gray-400 uppercase font-bold">Makhroj</span><span
                                                    class="block text-xl font-bold text-gray-800">{{ $selectedSesiDetail['nilai_makhroj'] }}</span>
                                            </div>
                                            <div class="text-center p-3 bg-green-600 rounded-xl shadow-md"><span
                                                    class="block text-[10px] text-green-200 uppercase font-bold">Rata</span><span
                                                    class="block text-xl font-bold text-white">{{ $selectedSesiDetail['nilai_rata'] }}</span>
                                            </div>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-800 mb-3 text-sm uppercase flex items-center gap-2">
                                                <span class="w-2 h-2 bg-red-500 rounded-full block"></span> Catatan Koreksi</h4>
                                            <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                                                <table class="min-w-full divide-y divide-gray-200">
                                                    <thead class="bg-gray-50">
                                                        <tr>
                                                            <th
                                                                class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">
                                                                Lokasi</th>
                                                            <th
                                                                class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">
                                                                Jenis</th>
                                                            <th
                                                                class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase">
                                                                Lafadz</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bg-white divide-y divide-gray-200">
                                                        @forelse($selectedSesiDetail['koreksi'] as $kor)
                                                            <tr>
                                                                <td class="px-4 py-3 text-sm text-gray-800 font-bold">
                                                                    {{ $kor['lokasi'] }}</td>
                                                                <td class="px-4 py-3 text-sm text-red-600 font-medium bg-red-50">
                                                                    {{ $kor['jenis_kesalahan'] }}</td>
                                                                <td class="px-4 py-3 text-sm text-right font-arabic"
                                                                    style="font-family: 'Amiri', serif; font-size: 1.4em; line-height: 1.6;">
                                                                    {{ $kor['catatan'] }}</td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="3"
                                                                    class="px-4 py-8 text-center text-sm text-gray-500 italic">Tidak
                                                                    ada catatan koreksi.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="bg-gray-50 px-4 py-4 sm:px-6 flex flex-col sm:flex-row sm:justify-end gap-3 border-t border-gray-200 shrink-0">
                                        <button wire:click="downloadPdfDetailSesi()"
                                            class="flex-1 sm:flex-none inline-flex justify-center items-center gap-2 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-red-700 transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                </path>
                                            </svg> PDF
                                        </button>
                                        <button wire:click="downloadExcelDetailSesi()"
                                            class="flex-1 sm:flex-none inline-flex justify-center items-center gap-2 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-700 transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                </path>
                                            </svg> Excel
                                        </button>
                                        <button type="button" wire:click="closeSesiDetail"
                                            class="flex-1 sm:flex-none inline-flex justify-center rounded-lg bg-white border border-gray-300 px-6 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-100 hover:text-gray-900 transition-all">
                                            Tutup
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- ========================================================================
                BAGIAN 4: DETAIL SURAH / SESI (SAMA PERSIS DENGAN ADMIN, KECUALI TOMBOL KEMBALI)
                ======================================================================== --}}
            @else
                <div wire:key="view-detail-surah">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                        <button type="button" wire:click="backToSiswa()"
                            class="inline-flex items-center text-green-600 hover:text-green-800 font-bold transition-colors group w-max">
                            <svg class="w-5 h-5 mr-1 transform group-hover:-translate-x-1 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                                </path>
                            </svg>
                            Kembali
                        </button>
                        <div class="flex flex-row w-full md:w-auto gap-3 self-end mt-2 md:mt-0">
                            <button wire:click="downloadPdfSesi()"
                                class="flex-1 md:flex-none flex justify-center items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold shadow-sm text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg> PDF
                            </button>
                            <button wire:click="downloadExcelSesi()"
                                class="flex-1 md:flex-none flex justify-center items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold shadow-sm text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg> Excel
                            </button>
                        </div>
                    </div>

                    {{-- DETAIL SURAH HEADER, STATS, LIST (Sama Persis) --}}
                    {{-- ... (Gunakan kode yang sama dengan Admin mulai dari sini sampai akhir file, termasuk Popup
                    Duplicate) ... --}}

                    <div
                        class="bg-white border-l-4 border-green-600 rounded-r-xl shadow p-5 mb-6 flex items-center justify-between hover:shadow-md transition-shadow">
                        <div>
                            <h4 class="text-xl md:text-2xl font-bold text-gray-800">
                                {{ $surahDetail['nama_surah'] ?? 'Surah' }}</h4>
                            <p class="text-sm text-gray-600 mt-1 font-medium">Surah
                                ke-{{ $surahDetail['nomor_surah'] ?? '-' }} • {{ $surahDetail['jumlah_ayat'] ?? '-' }} Ayat
                            </p>
                        </div>
                        <div
                            class="bg-green-50 text-green-700 rounded-full w-14 h-14 flex items-center justify-center font-bold text-2xl border border-green-200">
                            {{ $surahDetail['nomor_surah'] ?? '0' }}</div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                        <div
                            class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm hover:border-green-300 transition-colors">
                            <p class="text-[10px] uppercase text-gray-400 font-bold mb-1">Tajwid</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $surahDetail['nilai_tajwid'] ?? 0 }}</p>
                        </div>
                        <div
                            class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm hover:border-green-300 transition-colors">
                            <p class="text-[10px] uppercase text-gray-400 font-bold mb-1">Kelancaran</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $surahDetail['nilai_kelancaran'] ?? 0 }}</p>
                        </div>
                        <div
                            class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm hover:border-green-300 transition-colors">
                            <p class="text-[10px] uppercase text-gray-400 font-bold mb-1">Makhroj</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $surahDetail['nilai_makhroj'] ?? 0 }}</p>
                        </div>
                        <div class="bg-green-600 p-4 rounded-xl shadow-md text-white">
                            <p class="text-[10px] uppercase text-green-200 font-bold mb-1">Rata-rata</p>
                            <p class="text-2xl font-extrabold">{{ $surahDetail['nilai_rata_rata'] ?? 0 }}</p>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Detail Setoran</h4>
                        <div class="space-y-3">
                            @forelse ($surahDetail['sesi_formatnya'] as $index => $sesi)
                                <div
                                    class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm hover:shadow-md transition-all">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="flex items-center gap-2 mb-1">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                    </path>
                                                </svg>
                                                <p class="font-bold text-gray-800 text-sm">{{ $sesi['tanggal_setor'] }}</p>
                                            </div>
                                            <div
                                                class="inline-flex items-center px-2.5 py-1 rounded text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                                                Ayat {{ $sesi['ayat_mulai'] }} - {{ $sesi['ayat_selesai'] }}
                                            </div>
                                        </div>
                                        <button wire:click="selectSesi({{ $sesi['id_sesi'] }})"
                                            class="text-green-600 hover:text-white hover:bg-green-600 border border-green-600 text-sm font-semibold px-4 py-2 rounded-lg transition-all">
                                            Lihat Koreksi
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 text-gray-500 text-sm bg-gray-50 rounded-lg border border-dashed">
                                    Tidak ada data sesi</div>
                            @endforelse
                        </div>
                    </div>

                    {{-- MODAL DETAIL SESI (POPUP) - DUPLICATE FOR CONTEXT SAFETY --}}
                    @if(isset($selectedSesiDetail) && $selectedSesiDetail)
                        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
                            aria-modal="true">
                            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm"
                                wire:click="closeSesiDetail"></div>
                            <div class="flex min-h-full items-end justify-center p-0 md:p-4 text-center sm:items-center sm:p-0">
                                <div
                                    class="relative transform overflow-hidden rounded-t-2xl md:rounded-2xl bg-white text-left shadow-2xl transition-all w-full sm:max-w-2xl md:max-w-4xl max-h-[90vh] flex flex-col">
                                    <div class="bg-green-700 px-4 py-4 md:px-6 flex items-center justify-between shrink-0">
                                        <h3 class="text-lg md:text-xl font-bold text-white flex items-center gap-2">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                                </path>
                                            </svg>
                                            Detail Sesi
                                        </h3>
                                        <button wire:click="closeSesiDetail"
                                            class="text-green-200 hover:text-white transition-colors hover:bg-green-600 rounded-full p-1">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="p-6 overflow-y-auto">
                                        <div class="grid grid-cols-2 gap-4 mb-6">
                                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                                                <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Tanggal</p>
                                                <p class="font-bold text-gray-800 text-lg">{{ $selectedSesiDetail['tanggal'] }}
                                                </p>
                                            </div>
                                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                                                <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Guru
                                                    Pengampu</p>
                                                <p class="font-bold text-gray-800 text-sm md:text-base truncate">
                                                    {{ $selectedSesiDetail['guru'] }}</p>
                                            </div>
                                        </div>
                                        <div class="mb-8 text-center bg-green-50 rounded-xl p-6 border border-green-100">
                                            <p class="text-sm font-semibold text-green-700 uppercase tracking-wide mb-2">Capaian
                                                Ayat</p>
                                            <div class="inline-flex items-center gap-3">
                                                <span
                                                    class="text-3xl font-extrabold text-gray-800">{{ $selectedSesiDetail['ayat_mulai'] }}</span>
                                                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                                </svg>
                                                <span
                                                    class="text-3xl font-extrabold text-gray-800">{{ $selectedSesiDetail['ayat_selesai'] }}</span>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-4 gap-3 mb-8">
                                            <div class="text-center p-3 bg-white rounded-xl border border-gray-200 shadow-sm">
                                                <span
                                                    class="block text-[10px] text-gray-400 uppercase font-bold">Tajwid</span><span
                                                    class="block text-xl font-bold text-gray-800">{{ $selectedSesiDetail['nilai_tajwid'] }}</span>
                                            </div>
                                            <div class="text-center p-3 bg-white rounded-xl border border-gray-200 shadow-sm">
                                                <span
                                                    class="block text-[10px] text-gray-400 uppercase font-bold">Lancar</span><span
                                                    class="block text-xl font-bold text-gray-800">{{ $selectedSesiDetail['nilai_kelancaran'] }}</span>
                                            </div>
                                            <div class="text-center p-3 bg-white rounded-xl border border-gray-200 shadow-sm">
                                                <span
                                                    class="block text-[10px] text-gray-400 uppercase font-bold">Makhroj</span><span
                                                    class="block text-xl font-bold text-gray-800">{{ $selectedSesiDetail['nilai_makhroj'] }}</span>
                                            </div>
                                            <div class="text-center p-3 bg-green-600 rounded-xl shadow-md"><span
                                                    class="block text-[10px] text-green-200 uppercase font-bold">Rata</span><span
                                                    class="block text-xl font-bold text-white">{{ $selectedSesiDetail['nilai_rata'] }}</span>
                                            </div>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-800 mb-3 text-sm uppercase flex items-center gap-2">
                                                <span class="w-2 h-2 bg-red-500 rounded-full block"></span> Catatan Koreksi</h4>
                                            <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                                                <table class="min-w-full divide-y divide-gray-200">
                                                    <thead class="bg-gray-50">
                                                        <tr>
                                                            <th
                                                                class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">
                                                                Lokasi</th>
                                                            <th
                                                                class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">
                                                                Jenis</th>
                                                            <th
                                                                class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase">
                                                                Lafadz</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bg-white divide-y divide-gray-200">
                                                        @forelse($selectedSesiDetail['koreksi'] as $kor)
                                                            <tr>
                                                                <td class="px-4 py-3 text-sm text-gray-800 font-bold">
                                                                    {{ $kor['lokasi'] }}</td>
                                                                <td class="px-4 py-3 text-sm text-red-600 font-medium bg-red-50">
                                                                    {{ $kor['jenis_kesalahan'] }}</td>
                                                                <td class="px-4 py-3 text-sm text-right font-arabic"
                                                                    style="font-family: 'Amiri', serif; font-size: 1.4em; line-height: 1.6;">
                                                                    {{ $kor['catatan'] }}</td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="3"
                                                                    class="px-4 py-8 text-center text-sm text-gray-500 italic">Tidak
                                                                    ada catatan koreksi.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="bg-gray-50 px-4 py-4 sm:px-6 flex flex-col sm:flex-row sm:justify-end gap-3 border-t border-gray-200 shrink-0">
                                        <button wire:click="downloadPdfDetailSesi()"
                                            class="flex-1 sm:flex-none inline-flex justify-center items-center gap-2 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-red-700 transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                </path>
                                            </svg> PDF
                                        </button>
                                        <button wire:click="downloadExcelDetailSesi()"
                                            class="flex-1 sm:flex-none inline-flex justify-center items-center gap-2 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-700 transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                </path>
                                            </svg> Excel
                                        </button>
                                        <button type="button" wire:click="closeSesiDetail"
                                            class="flex-1 sm:flex-none inline-flex justify-center rounded-lg bg-white border border-gray-300 px-6 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-100 hover:text-gray-900 transition-all">
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