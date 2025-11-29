<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <!-- Header -->
        <div class="bg-green-600 rounded-xl p-6 mb-8 text-white shadow-lg relative overflow-hidden">
            <div class="absolute top-0 right-0 -mr-16 -mt-16 w-48 h-48 rounded-full bg-white opacity-10"></div>
            <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-32 h-32 rounded-full bg-white opacity-10"></div>

            <div class="relative z-10">
                <h2 class="text-2xl md:text-3xl font-bold mb-2">Assalamu'alaikum, {{ Auth::user()->nama_lengkap }}!</h2>
                <p class="text-green-100">Selamat datang di Panel Administrator Hafizuna. Semoga hari Anda berkah dan
                    produktif.</p>
            </div>

        </div>

        <!-- Dashboard Statistik Header -->
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Dashboard Statistik</h3>
            <p class="text-gray-600 text-sm">Overview sistem dan performa hafalan</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            <!-- Total Guru -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-500">Total Guru</div>
                        <div class="text-3xl font-bold text-blue-600 mt-2">{{ $totalGuru }}</div>
                        <p class="text-xs text-gray-400 mt-1">Guru aktif</p>
                    </div>
                    <div class="p-3 rounded-full bg-blue-100 text-blue-500">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Siswa -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-500">Total Siswa</div>
                        <div class="text-3xl font-bold text-green-600 mt-2">{{ $totalSiswa }}</div>
                        <p class="text-xs text-gray-400 mt-1">Siswa terdaftar</p>
                    </div>
                    <div class="p-3 rounded-full bg-green-100 text-green-500">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Kelas -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-500">Total Kelas</div>
                        <div class="text-3xl font-bold text-purple-600 mt-2">{{ $totalKelas }}</div>
                        <p class="text-xs text-gray-400 mt-1">Kelas aktif</p>
                    </div>
                    <div class="p-3 rounded-full bg-purple-100 text-purple-500">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Rata-rata Nilai -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-500">Rata-rata Nilai</div>
                        <div class="text-3xl font-bold text-yellow-600 mt-2">{{ $rataRataNilai }}</div>
                        <p class="text-xs text-gray-400 mt-1">Keseluruhan sistem</p>
                    </div>
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-500">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Chart Statistik Kelas -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Statistik Per Kelas</h3>
                <p class="text-gray-600 text-sm mb-4">Jumlah siswa dan rata-rata nilai per kelas</p>
                <div id="chartKelas" style="height: 300px;"></div>
            </div>

            <!-- Chart Tren Aktivitas -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Tren Aktivitas Bulanan</h3>
                <p class="text-gray-600 text-sm mb-4">Perkembangan sesi hafalan dan partisipasi siswa</p>
                <div id="chartAktivitas" style="height: 300px;"></div>
            </div>
        </div>

        <!-- Analytics Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Analisis Aspek Penilaian (Radar Chart) -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Analisis Aspek Penilaian</h3>
                <p class="text-gray-600 text-sm mb-4">Perbandingan nilai aktual vs target</p>
                <div id="chartRadar" style="height: 300px;"></div>
            </div>

            <!-- Performa Guru -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Performa Guru</h3>
                <p class="text-gray-600 text-sm mb-4">Kelas, sesi, dan rata-rata nilai per guru</p>
                <div id="chartGuruPerforma" style="height: 300px;"></div>
            </div>
        </div>

        <!-- Aktivitas Terbaru -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Aktivitas Terbaru</h3>
            <p class="text-gray-600 text-sm mb-4">5 sesi hafalan terakhir di semua kelas</p>

            <div class="space-y-3">
                @foreach($aktivitasTerbaru as $aktivitas)
                    <div
                        class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        <div class="flex items-center space-x-4">
                            <!-- Avatar -->
                            <div class="flex-shrink-0">
                                <div
                                    class="h-10 w-10 rounded-full {{ $aktivitas['warna_avatar'] }} flex items-center justify-center text-white font-bold text-sm">
                                    {{ $aktivitas['index'] }}
                                </div>
                            </div>

                            <!-- Info -->
                            <div class="flex-grow">
                                <h4 class="font-semibold text-gray-900">{{ $aktivitas['nama_siswa'] }}</h4>
                                <!-- Update guru name display - pastikan nama guru tidak "N/A" -->
                                <p class="text-sm text-gray-600">{{ $aktivitas['surah'] }} • Ustadz {{ $aktivitas['guru'] }}
                                </p>
                                <p class="text-xs text-gray-400 mt-1">{{ $aktivitas['tanggal'] }}</p>
                            </div>
                        </div>

                        <!-- Nilai -->
                        <div class="flex-shrink-0 text-right">
                            <span
                                class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-green-100 text-green-700 font-bold text-sm">
                                {{ $aktivitas['nilai'] }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- ApexCharts Library -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        (function () {
            function initCharts() {
                if (typeof ApexCharts === 'undefined') {
                    console.error('ApexCharts library is not loaded.');
                    return;
                }

                // Chart Kelas (Bar Chart)
                var chartKelasEl = document.querySelector("#chartKelas");
                if (chartKelasEl && !chartKelasEl.querySelector('svg')) {
                    var optionsKelas = {
                        series: [{
                            name: 'Jumlah Siswa',
                            data: @json($chartKelasData)
                        }],
                        chart: {
                            type: 'bar',
                            height: 300,
                            toolbar: { show: false }
                        },
                        plotOptions: {
                            bar: {
                                borderRadius: 4,
                                horizontal: false,
                                columnWidth: '70%'
                            }
                        },
                        dataLabels: { enabled: false },
                        xaxis: {
                            categories: @json($chartKelasLabels),
                            axisBorder: { show: false }
                        },
                        yaxis: {
                            title: { text: 'Jumlah' }
                        },
                        colors: ['#10B981'],
                        grid: { show: true, borderColor: '#e5e7eb' }
                    };

                    var chartKelas = new ApexCharts(chartKelasEl, optionsKelas);
                    chartKelas.render();
                }

                // Chart Aktivitas (Area Chart)
                var chartAktivitasEl = document.querySelector("#chartAktivitas");
                if (chartAktivitasEl && !chartAktivitasEl.querySelector('svg')) {
                    var optionsAktivitas = {
                        series: [{
                            name: 'Aktivitas',
                            data: @json($chartAktivitasData)
                        }],
                        chart: {
                            height: 300,
                            type: 'area',
                            toolbar: { show: false }
                        },
                        dataLabels: { enabled: false },
                        stroke: { curve: 'smooth', width: 2 },
                        fill: { type: 'gradient', gradient: { opacityFrom: 0.6, opacityTo: 0.1 } },
                        xaxis: {
                            categories: @json($chartAktivitasLabels),
                            axisBorder: { show: false }
                        },
                        yaxis: {
                            title: { text: 'Total Aktivitas' }
                        },
                        colors: ['#8B5CF6'],
                        grid: { show: true, borderColor: '#e5e7eb' }
                    };

                    var chartAktivitas = new ApexCharts(chartAktivitasEl, optionsAktivitas);
                    chartAktivitas.render();
                }

                // Chart Radar: Analisis Aspek Penilaian
                var chartRadarEl = document.querySelector("#chartRadar");
                if (chartRadarEl && !chartRadarEl.querySelector('svg')) {
                    var optionsRadar = {
                        series: [{
                            name: 'Nilai Aktual',
                            data: @json($radarAspekData)
                        }],
                        chart: {
                            type: 'radar',
                            height: 300,
                            toolbar: { show: false }
                        },
                        xaxis: {
                            categories: @json($radarAspekLabels)
                        },
                        colors: ['#8B5CF6'],
                        plotOptions: {
                            radar: {
                                size: 140,
                                polygons: {
                                    strokeColors: '#e5e7eb'
                                }
                            }
                        },
                        dataLabels: { enabled: true }
                    };

                    var chartRadar = new ApexCharts(chartRadarEl, optionsRadar);
                    chartRadar.render();
                }

                // Chart Bar: Performa Guru (Horizontal)
                var chartGuruPerformaEl = document.querySelector("#chartGuruPerforma");
                if (chartGuruPerformaEl && !chartGuruPerformaEl.querySelector('svg')) {
                    var optionsGuruPerforma = {
                        series: [
                            {
                                name: 'Avg Nilai',
                                data: @json($guruPerformaAvgNilai)
                            },
                            {
                                name: 'Total Sesi',
                                data: @json($guruPerformaTotalSesi)
                            }
                        ],
                        chart: {
                            type: 'bar',
                            height: 300,
                            toolbar: { show: false }
                        },
                        plotOptions: {
                            bar: {
                                horizontal: true,
                                columnWidth: '70%'
                            }
                        },
                        xaxis: {
                            categories: @json($guruPerformaLabels)
                        },
                        dataLabels: { enabled: false },
                        colors: ['#F59E0B', '#3B82F6'],
                        grid: { show: true, borderColor: '#e5e7eb' }
                    };

                    var chartGuruPerforma = new ApexCharts(chartGuruPerformaEl, optionsGuruPerforma);
                    chartGuruPerforma.render();
                }
            }

            document.addEventListener('livewire:navigated', function () {
                initCharts();
            });

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initCharts);
            } else {
                initCharts();
            }
        })();
    </script>
</div>