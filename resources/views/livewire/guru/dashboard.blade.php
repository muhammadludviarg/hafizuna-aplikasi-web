<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <!-- Header Greeting -->

        <div class="bg-green-600 rounded-xl p-6 mb-8 text-white shadow-lg relative overflow-hidden">
            <div class="absolute top-0 right-0 -mr-16 -mt-16 w-48 h-48 rounded-full bg-white opacity-10"></div>
            <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-32 h-32 rounded-full bg-white opacity-10"></div>

            <div class="relative z-10">
                <h2 class="text-2xl md:text-3xl font-bold mb-2">Assalamu'alaikum, {{ Auth::user()->nama_lengkap }}!</h2>
                <p class="text-green-100 text-sm md:text-base">
                    Selamat beraktivitas kembali. Mari bimbing para santri menjadi penghafal Al-Qur'an terbaik.
                </p>
            </div>

        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            <!-- Kelas Diampu -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Kelompok Diampu</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2">{{ $kelasCount }}</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-lg">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747c5.5 0 10-4.998 10-10.747S17.5 6.253 12 6.253z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Siswa -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Total Siswa</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2">{{ $siswaCount }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Rata-rata Nilai -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Rata-rata Nilai</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2">{{ $rataRataNilai }}</p>
                    </div>
                    <div class="p-3 bg-yellow-100 rounded-lg">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Sesi Bulan Ini -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Sesi Bulan Ini</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2">{{ $sesiBlnIni }}</p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Chart Progress Hafalan Per Kelas -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center gap-3 mb-2">
                    <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900">Progress Hafalan Per Kelas</h3>
                </div>
                <p class="text-gray-600 text-sm mb-6">Persentase pencapaian target hafalan</p>
                <div id="chartProgress" style="height: 280px;"></div>
            </div>

            <!-- Chart Tren Nilai 7 Hari Terakhir -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center gap-3 mb-2">
                    <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900">Tren Nilai 7 Hari Terakhir</h3>
                </div>
                <p class="text-gray-600 text-sm mb-6">Perkembangan nilai per aspek penilaian</p>
                <div id="chartTren" style="height: 280px;"></div>
            </div>
        </div>

        <!-- Charts Section Row 2 -->
        <div class="grid grid-cols-1 gap-6">

            <!-- Pie Chart Distribusi Nilai -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center gap-3 mb-2">
                    <svg class="w-5 h-5 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900">Distribusi Nilai Siswa</h3>
                </div>
                <p class="text-gray-600 text-sm mb-6">Sebaran kategori nilai seluruh siswa bimbingan</p>
                <div id="chartDistribusi" style="height: 280px;"></div>
            </div>

            <!-- Setoran Terbaru -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">5 Setoran Terakhir</h3>
                <p class="text-gray-600 text-sm mb-6">Riwayat setoran hafalan terbaru</p>
                <div class="space-y-3">
                    @forelse($setorTerbaru as $idx => $setor)
                        <div
                            class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:shadow-sm transition">
                            <div class="flex items-center gap-4 flex-1">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                                    style="background-color: {{ ['#22C55E', '#10B981', '#8B5CF6', '#F59E0B', '#EF4444'][$idx] }}">
                                    {{ $idx + 1 }}
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900 text-sm">{{ $setor->nama_siswa }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $setor->nama_surah }} •
                                        {{ $setor->tanggal_setor->format('d/m/Y') }}
                                    </p>
                                </div>
                            </div>
                            <div class="bg-green-500 text-white font-bold px-3 py-1 rounded-lg text-sm flex-shrink-0">
                                {{ round($setor->nilai_rata) }}
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm text-center py-6">Belum ada data setoran</p>
                    @endforelse
                </div>
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

                // Chart Progress (Bar Chart)
                var chartProgressEl = document.querySelector("#chartProgress");
                if (chartProgressEl && !chartProgressEl.querySelector('svg')) {
                    var optionsProgress = {
                        series: [{
                            name: 'Progress (%)',
                            data: @json($chartProgressData)
                        }],
                        chart: {
                            type: 'bar',
                            height: 280,
                            toolbar: { show: false }
                        },
                        plotOptions: {
                            bar: {
                                borderRadius: 4,
                                columnWidth: '70%'
                            }
                        },
                        dataLabels: { enabled: false },
                        xaxis: {
                            categories: @json($chartProgressLabels),
                            axisBorder: { show: false }
                        },
                        yaxis: {
                            title: { text: 'Progress (%)' },
                            max: 100
                        },
                        colors: ['#10B981'],
                        grid: { show: true, borderColor: '#e5e7eb' }
                    };
                    var chartProgress = new ApexCharts(chartProgressEl, optionsProgress);
                    chartProgress.render();
                }

                // Chart Tren (Multi-line Chart)
                var chartTrenEl = document.querySelector("#chartTren");
                if (chartTrenEl && !chartTrenEl.querySelector('svg')) {
                    var optionsTren = {
                        series: [
                            { name: 'Tajwid', data: @json($chartTrenTajwid) },
                            { name: 'Makhroj', data: @json($chartTrenMakhroj) },
                            { name: 'Kelancaran', data: @json($chartTrenKelancaran) }
                        ],
                        chart: {
                            type: 'line',
                            height: 280,
                            toolbar: { show: false }
                        },
                        dataLabels: { enabled: false },
                        stroke: { curve: 'smooth', width: 2 },
                        xaxis: {
                            categories: @json($chartTrenLabels),
                            axisBorder: { show: false }
                        },
                        yaxis: {
                            title: { text: 'Nilai' },
                            min: 70,
                            max: 100
                        },
                        colors: ['#0084FF', '#E63946', '#F1B14E'],
                        grid: { show: true, borderColor: '#e5e7eb' }
                    };
                    var chartTren = new ApexCharts(chartTrenEl, optionsTren);
                    chartTren.render();
                }

                // Chart Distribusi (Pie Chart)
                var chartDistribusiEl = document.querySelector("#chartDistribusi");
                if (chartDistribusiEl && !chartDistribusiEl.querySelector('svg')) {
                    var optionsDistribusi = {
                        series: @json($chartDistribusiData),
                        chart: {
                            type: 'pie',
                            height: 280
                        },
                        labels: @json($chartDistribusiLabels),
                        colors: ['#10B981', '#84CC16', '#FBBF24', '#EF4444'],
                        legend: {
                            position: 'bottom',
                            fontSize: 12
                        }
                    };
                    var chartDistribusi = new ApexCharts(chartDistribusiEl, optionsDistribusi);
                    chartDistribusi.render();
                }
            }

            // Initialize on page load
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