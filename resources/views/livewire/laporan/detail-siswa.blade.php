<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Laporan Siswa: {{ $siswa->nama }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-10 bg-white border-b border-gray-200">
                
                    <!-- KOP SURAT / HEADER -->
                    <div class="text-center mb-6">
                        <img src="{{ asset('logo.png') }}" alt="Logo Hafizuna" class="h-20 mx-auto mb-2">
                        <h1 class="text-3xl font-bold text-green-700">HAFIZUNA</h1>
                        <p class="text-gray-600">SD Islam Al-Azhar 27 Cibinong Bogor</p>
                        <h2 class="text-2xl font-semibold mt-2">Laporan Hafalan Al-Qur'an</h2>
                    </div>

                    <!-- INFO SISWA & TOMBOL GENERATE -->
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <p><span class="font-semibold">Nama Siswa:</span> {{ $siswa->nama }}</p>
                            <p><span class="font-semibold">Kelas:</span> {{ $siswa->kelas->nama_kelas ?? 'N/A' }}</p>
                            <p><span class="font-semibold">Tanggal:</span> {{ now()->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <!-- [BARU] Tombol Generate PDF -->
                            <button wire:click="generatePdf" 
                                    wire:loading.attr="disabled"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-800 focus:outline-none focus:border-red-800 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <svg wire:loading wire:target="generatePdf" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span wire:loading.remove wire:target="generatePdf">Generate PDF</span>
                                <span wire:loading wire:target="generatePdf">Generating...</span>
                            </button>
                        </div>
                    </div>

                    <!-- 1. TABEL SURAH YANG SUDAH DIHAFALKAN -->
                    <h3 class="text-xl font-semibold mb-3 text-gray-800">Surah yang Sudah Dihafalkan</h3>
                    <div class="bg-white shadow-md rounded-lg overflow-x-auto mb-8">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Surah</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sesi ke-</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tajwid</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelancaran</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Makhroj</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rata-rata</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($sudahDihapal as $hafalan)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $hafalan->nama_surah }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $hafalan->total_sesi }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($hafalan->avg_tajwid, 0) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($hafalan->avg_kelancaran, 0) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($hafalan->avg_makhroj, 0) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap font-bold">{{ number_format($hafalan->avg_rata_rata, 0) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada hafalan yang dinilai.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- 2. TABEL TARGET HAFALAN YANG BELUM DIHAFALKAN -->
                    <h3 class="text-xl font-semibold mb-3 text-gray-800">Target Hafalan yang Belum Dihafalkan</h3>
                    <div class="bg-white shadow-md rounded-lg overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Surah</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progress</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($belumDihapal as $target)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $target->nama_surah }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Belum Dimulai
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">0/{{ $target->jumlah_ayat }} ayat</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">Semua target hafalan telah diselesaikan!</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>