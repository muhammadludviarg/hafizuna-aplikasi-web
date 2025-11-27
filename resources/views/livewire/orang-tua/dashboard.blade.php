<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Header Greeting -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-lg p-8 mb-8 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold">Assalamu'alaikum!</h1>
                    <p class="text-green-100 mt-2">Pantau perkembangan hafalan putra-putri Anda</p>
                </div>
                <div class="bg-white bg-opacity-20 p-6 rounded-full">
                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747c5.5 0 10-4.998 10-10.747S17.5 6.253 12 6.253z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- List Anak-anak (Grid 2 Kolom) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            @foreach ($anakList as $index => $anak)
                <div 
                    wire:click="selectAnak({{ $anak['id_siswa'] }})"
                    class="bg-white rounded-lg shadow-md p-6 cursor-pointer transition hover:shadow-lg border-l-4 {{ $selectedAnak === $anak['id_siswa'] ? 'border-green-500 bg-green-50' : ($index % 2 === 0 ? 'border-green-500' : 'border-blue-500') }}"
                >
                    <!-- Header Card dengan Nama dan Icon -->
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $anak['nama_siswa'] }}</h3>
                            <p class="text-sm text-gray-600">{{ $anak['kelas'] }}</p>
                        </div>
                        <div class="p-2 {{ $index % 2 === 0 ? 'bg-green-100' : 'bg-blue-100' }} rounded-lg">
                            <svg class="w-5 h-5 {{ $index % 2 === 0 ? 'text-green-600' : 'text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mb-4">
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-600">Progress Hafalan</span>
                            <span class="text-green-600 font-semibold">{{ $anak['progress'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: {{ $anak['progress'] }}%"></div>
                        </div>
                    </div>

                    <!-- Total Sesi dan Rata-rata -->
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div class="bg-gray-50 p-3 rounded border">
                            <p class="text-gray-600 text-xs">Total Setoran</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $anak['total_sesi'] }}</p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded border">
                            <p class="text-gray-600 text-xs">Rata-rata</p>
                            <p class="text-2xl font-bold {{ $index % 2 === 0 ? 'text-green-600' : 'text-blue-600' }}">{{ $anak['rata_rata'] }}</p>
                        </div>
                    </div>

                    <!-- Setoran Terakhir dengan data dari component -->
                    @if (!empty($anak['setoran_terakhir']))
                    <div class="bg-gray-50 p-4 rounded border-t">
                        <p class="text-xs text-gray-600 mb-3 font-semibold">Setoran Terakhir:</p>
                        <div class="space-y-2">
                            @foreach ($anak['setoran_terakhir'] as $setoran)
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-700">{{ $setoran['surah'] }}</span>
                                <span class="font-semibold {{ $index % 2 === 0 ? 'text-green-600' : 'text-blue-600' }}">{{ $setoran['nilai'] }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Analisis Detail Hafalan -->
        @if ($selectedAnak && $selectedAnakData)
        <div class="bg-blue-50 rounded-lg shadow-md p-6 mb-8 border-l-4 border-blue-500">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900">Analisis Detail Hafalan</h3>
                    <p class="text-sm text-gray-600">Pilih anak untuk melihat grafik perkembangan dan analisis</p>
                </div>
                <div>
                    <div class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 font-semibold">
                        Pilih Anak: {{ $selectedAnakData['nama_siswa'] }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            
            <!-- Chart Perkembangan Nilai -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Perkembangan Nilai</h3>
                <p class="text-sm text-gray-600 mb-4">Tren nilai {{ $selectedAnakData['nama_siswa'] }} selama 4 minggu terakhir</p>
                <div id="chartPerkembangan" style="height: 300px;"></div>
            </div>

            <!-- Chart Target Hafalan Bulanan -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Target Hafalan Bulanan</h3>
                <p class="text-sm text-gray-600 mb-4">Target vs pencapaian {{ $selectedAnakData['nama_siswa'] }}</p>
                <div id="chartTarget" style="height: 300px;"></div>
            </div>
        </div>

        <!-- Nilai Per Aspek Penilaian -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center gap-3 mb-6">
                <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Nilai Per Aspek Penilaian</h3>
                    <p class="text-sm text-gray-600">Pencapaian nilai {{ $selectedAnakData['nama_siswa'] }} untuk setiap aspek hafalan</p>
                </div>
            </div>
            
            <div class="space-y-6">
                <!-- Tajwid -->
                <div>
                    <div class="flex justify-between mb-2">
                        <label class="text-gray-700 font-semibold">Tajwid</label>
                        <span class="text-green-600 font-bold">{{ $nilaiAspekTajwid }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-green-500 h-3 rounded-full" style="width: {{ $nilaiAspekTajwid }}%"></div>
                    </div>
                </div>

                <!-- Makhroj -->
                <div>
                    <div class="flex justify-between mb-2">
                        <label class="text-gray-700 font-semibold">Makhroj</label>
                        <span class="text-blue-600 font-bold">{{ $nilaiAspekMakhroj }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-blue-500 h-3 rounded-full" style="width: {{ $nilaiAspekMakhroj }}%"></div>
                    </div>
                </div>

                <!-- Kelancaran -->
                <div>
                    <div class="flex justify-between mb-2">
                        <label class="text-gray-700 font-semibold">Kelancaran</label>
                        <span class="text-orange-600 font-bold">{{ $nilaiAspekKelancaran }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-orange-500 h-3 rounded-full" style="width: {{ $nilaiAspekKelancaran }}%"></div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- ApexCharts Library -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        (function() {
            function initCharts() {
                if (typeof ApexCharts === 'undefined') {
                    console.error('ApexCharts library is not loaded.');
                    return;
                }

                // Chart Perkembangan Nilai
                var chartPerkembanganEl = document.querySelector("#chartPerkembangan");
                if (chartPerkembanganEl && !chartPerkembanganEl.querySelector('svg')) {
                    var optionsPerkembangan = {
                        series: [
                            { name: 'Tajwid', data: @json($chartPerkembanganTajwid) },
                            { name: 'Makhroj', data: @json($chartPerkembanganMakhroj) },
                            { name: 'Kelancaran', data: @json($chartPerkembanganKelancaran) }
                        ],
                        chart: {
                            type: 'line',
                            height: 300,
                            toolbar: { show: false }
                        },
                        dataLabels: { enabled: false },
                        stroke: { curve: 'smooth', width: 2 },
                        xaxis: {
                            categories: @json($chartPerkembanganLabels),
                            axisBorder: { show: false }
                        },
                        yaxis: {
                            min: 70,
                            max: 100,
                            title: { text: 'Nilai' }
                        },
                        colors: ['#10B981', '#EF4444', '#3B82F6'],
                        grid: { show: true, borderColor: '#e5e7eb' }
                    };
                    
                    var chartPerkembangan = new ApexCharts(chartPerkembanganEl, optionsPerkembangan);
                    chartPerkembangan.render();
                }

                // Chart Target Hafalan
                var chartTargetEl = document.querySelector("#chartTarget");
                if (chartTargetEl && !chartTargetEl.querySelector('svg')) {
                    var optionsTarget = {
                        series: [
                            { name: 'Pencapaian (Ayat)', data: @json($chartTargetPencapaian) },
                            { name: 'Target (Ayat)', data: @json($chartTargetTarget) }
                        ],
                        chart: {
                            type: 'bar',
                            height: 300,
                            toolbar: { show: false }
                        },
                        dataLabels: { enabled: false },
                        plotOptions: {
                            bar: {
                                borderRadius: 4,
                                columnWidth: '70%'
                            }
                        },
                        xaxis: {
                            categories: @json($chartTargetLabels),
                            axisBorder: { show: false }
                        },
                        yaxis: {
                            title: { text: 'Jumlah Ayat' }
                        },
                        colors: ['#10B981', '#D1D5DB'],
                        grid: { show: true, borderColor: '#e5e7eb' }
                    };
                    
                    var chartTarget = new ApexCharts(chartTargetEl, optionsTarget);
                    chartTarget.render();
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
