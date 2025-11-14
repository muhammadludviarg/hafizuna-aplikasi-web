@extends('layouts.app')

@section('content')
<div class="flex-1 overflow-auto bg-gray-50">
    <!-- Header Section -->
    <div class="bg-white border-b border-gray-200 px-8 py-4">
        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
    </div>

    <div class="p-8">
        <!-- Page Title & Subtitle -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Dashboard Statistik</h2>
            <p class="text-gray-600 text-sm mt-1">Overview sistem dan performa hafalan</p>
        </div>

        <!-- 4 Stat Cards in single row with colored borders -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Guru Card -->
            <div class="bg-white rounded-lg p-6 shadow-sm border-l-4 border-blue-400">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium mb-2">Total Guru</p>
                        <p class="text-4xl font-bold text-blue-600">{{ $totalGuru }}</p>
                        <p class="text-xs text-gray-500 mt-2">Guru aktif</p>
                    </div>
                    <div class="bg-blue-100 p-2 rounded">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM9 19c-4.3 0-8-1.343-8-3s3.582-3 8-3 8 1.343 8 3-3.582 3-8 3z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Siswa Card -->
            <div class="bg-white rounded-lg p-6 shadow-sm border-l-4 border-green-400">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium mb-2">Total Siswa</p>
                        <p class="text-4xl font-bold text-green-600">{{ $totalSiswa }}</p>
                        <p class="text-xs text-gray-500 mt-2">Siswa terdaftar</p>
                    </div>
                    <div class="bg-green-100 p-2 rounded">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM6 20a3 3 0 003-3v-2a3 3 0 00-3-3H3a3 3 0 00-3 3v2a3 3 0 003 3z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Kelas Card -->
            <div class="bg-white rounded-lg p-6 shadow-sm border-l-4 border-purple-400">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium mb-2">Total Kelas</p>
                        <p class="text-4xl font-bold text-purple-600">{{ $totalKelas }}</p>
                        <p class="text-xs text-gray-500 mt-2">Kelas aktif</p>
                    </div>
                    <div class="bg-purple-100 p-2 rounded">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.5 6.75 1.747 9.016 1.747 14.5c0 5.52 4.753 7.75 10.253 8.5m0-13c5.5-.75 10.253 2.98 10.253 8.5 0 5.52-4.753 7.75-10.253 8.5"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Rata-rata Nilai Card -->
            <div class="bg-white rounded-lg p-6 shadow-sm border-l-4 border-orange-400">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium mb-2">Rata-rata Nilai</p>
                        <p class="text-4xl font-bold text-orange-600">{{ round($rataRataNilai) }}</p>
                        <p class="text-xs text-gray-500 mt-2">Kesuksesan sistem</p>
                    </div>
                    <div class="bg-orange-100 p-2 rounded">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4 Charts Grid in 2x2 layout -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Statistik Per Kelas -->
            <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
                <h3 class="text-base font-semibold text-gray-900 mb-1">Statistik Per Kelas</h3>
                <p class="text-sm text-gray-500 mb-6">Jumlah siswa dan rata-rata nilai per kelas</p>
                <div class="h-64 flex items-end justify-between gap-4 px-2">
                    @forelse($statistikPerKelas as $kelas)
                        <div class="flex-1 flex flex-col items-center">
                            <div class="w-full bg-green-500 rounded-t" style="height: {{ min(($kelas->jumlah_siswa ?? 1) * 20, 200) }}px;"></div>
                            <p class="text-xs text-gray-600 mt-2 text-center truncate w-full">{{ $kelas->nama_kelas ?? 'Kelas' }}</p>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center w-full">Tidak ada data</p>
                    @endforelse
                </div>
            </div>

            <!-- Tren Aktivitas Bulanan -->
            <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
                <h3 class="text-base font-semibold text-gray-900 mb-1">Tren Aktivitas Bulanan</h3>
                <p class="text-sm text-gray-500 mb-6">Perkembangan sesi hafalan dan partisipasi siswa</p>
                <div class="h-64 flex items-end justify-between gap-1">
                    @forelse($trendAktivitas as $trend)
                        <div class="flex-1 flex flex-col items-center">
                            <div class="w-full bg-gradient-to-t from-blue-500 to-purple-500 rounded-t opacity-80" style="height: {{ min(($trend->jumlah_sesi ?? 1) * 25, 200) }}px;"></div>
                            <p class="text-xs text-gray-600 mt-2">{{ $trend->bulan }}</p>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center w-full">Tidak ada data</p>
                    @endforelse
                </div>
            </div>

            <!-- Analisis Aspek Penilaian -->
            <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
                <h3 class="text-base font-semibold text-gray-900 mb-1">Analisis Aspek Penilaian</h3>
                <p class="text-sm text-gray-500 mb-6">Perbandingan nilai aktual vs target</p>
                <div class="flex justify-center items-center h-64">
                    <svg viewBox="0 0 200 200" class="w-48 h-48">
                        <!-- Pentagon/Radar chart -->
                        <polygon points="100,20 180,68 147,170 53,170 20,68" fill="rgba(167, 139, 250, 0.1)" stroke="rgba(168, 85, 247, 0.3)" stroke-width="1"/>
                        <polygon points="100,40 160,71 138,155 62,155 40,71" fill="rgba(236, 72, 153, 0.1)" stroke="rgba(236, 72, 153, 0.3)" stroke-width="1"/>
                        <polygon points="100,50 150,75 135,150 65,150 50,75" fill="rgba(251, 146, 60, 0.2)" stroke="rgba(251, 146, 60, 0.5)" stroke-width="2"/>
                        
                        <text x="100" y="15" text-anchor="middle" class="text-xs fill-gray-700" font-weight="600">Tajwid</text>
                        <text x="175" y="80" text-anchor="start" class="text-xs fill-gray-700" font-weight="600">Kelancaran</text>
                        <text x="100" y="190" text-anchor="middle" class="text-xs fill-gray-700" font-weight="600">Makhroj</text>
                    </svg>
                </div>
                <div class="mt-4 flex justify-center gap-8 text-xs">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                        <span class="text-gray-600">Nilai Aktual</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-orange-500 rounded-full"></div>
                        <span class="text-gray-600">Target</span>
                    </div>
                </div>
            </div>

            <!-- Performa Guru -->
            <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
                <h3 class="text-base font-semibold text-gray-900 mb-1">Performa Guru</h3>
                <p class="text-sm text-gray-500 mb-6">Kelas, sesi, dan rata-rata nilai per guru</p>
                <div class="space-y-3">
                    @forelse($performaGuru as $guru)
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                            <span class="text-sm text-gray-700">{{ $guru->nama_lengkap ?? 'Guru' }}</span>
                            <span class="text-sm font-semibold text-orange-600">{{ round($guru->rata_nilai ?? 0) }}</span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">Tidak ada data performa guru</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Aktivitas Terbaru Section -->
        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
            <h3 class="text-base font-semibold text-gray-900 mb-2">Aktivitas Terbaru</h3>
            <p class="text-sm text-gray-500 mb-6">5 sesi hafalan terakhir di semua kelas</p>
            
            <div class="space-y-4">
                @forelse($aktivitasTerbaru as $index => $aktivitas)
                    <div class="flex items-start gap-4 pb-4 border-b border-gray-100 last:border-b-0">
                        <!-- Avatar -->
                        <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0 text-white font-semibold text-sm" style="background: @if($index % 3 === 0)#3B82F6 @elseif($index % 3 === 1)#10B981 @else#A855F7 @endif">
                            {{ substr($aktivitas->nama_lengkap ?? $aktivitas->nama_siswa ?? 'U', 0, 1) }}
                        </div>
                        
                        <!-- Content -->
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-900">{{ $aktivitas->nama_lengkap ?? $aktivitas->nama_siswa ?? 'Siswa' }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $aktivitas->aktivitas ?? 'Sesi hafalan' }} • Ustadz Ahmad Fauzi</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $aktivitas->timestamp ? $aktivitas->timestamp->format('d F Y') : date('d F Y') }}</p>
                        </div>
                        
                        <!-- Score Badge -->
                        <div class="bg-green-100 text-green-800 font-semibold px-3 py-1 rounded text-sm flex-shrink-0">
                            {{ round($aktivitas->nilai ?? 0) }}
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm text-center py-8">Belum ada aktivitas terbaru</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
