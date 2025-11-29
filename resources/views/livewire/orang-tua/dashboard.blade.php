<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header Greeting -->

        <div class="bg-green-600 rounded-xl p-6 mb-8 text-white shadow-lg relative overflow-hidden">
            <div class="absolute top-0 right-0 -mr-16 -mt-16 w-48 h-48 rounded-full bg-white opacity-10"></div>
            <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-32 h-32 rounded-full bg-white opacity-10"></div>

            <div class="relative z-10">
                <h2 class="text-2xl md:text-3xl font-bold mb-2">Assalamu'alaikum, {{ Auth::user()->nama_lengkap }}!
                </h2>
                <p class="text-green-100 text-sm md:text-base">
                    Pantau perkembangan hafalan buah hati Anda dengan mudah di sini.
                </p>
            </div>

        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            @foreach ($anakList as $index => $anak)
                <div wire:click="selectAnak({{ $anak['id_siswa'] }})"
                    class="bg-white rounded-lg shadow-md p-6 cursor-pointer transition hover:shadow-lg border-l-4 {{ $selectedAnakId === $anak['id_siswa'] ? 'border-green-500 bg-green-50' : ($index % 2 === 0 ? 'border-green-500' : 'border-blue-500') }}">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $anak['nama_siswa'] }}</h3>
                            <p class="text-sm text-gray-600">{{ $anak['kelas'] }}</p>
                        </div>
                        <div class="p-2 {{ $index % 2 === 0 ? 'bg-green-100' : 'bg-blue-100' }} rounded-lg">
                            <svg class="w-5 h-5 {{ $index % 2 === 0 ? 'text-green-600' : 'text-blue-600' }}" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-600">Progress Hafalan</span>
                            <span class="text-green-600 font-semibold">{{ $anak['progress'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: {{ $anak['progress'] }}%"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div class="bg-gray-50 p-3 rounded border">
                            <p class="text-gray-600 text-xs">Total Setoran</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $anak['total_sesi'] }}</p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded border">
                            <p class="text-gray-600 text-xs">Rata-rata</p>
                            <p class="text-2xl font-bold {{ $index % 2 === 0 ? 'text-green-600' : 'text-blue-600' }}">
                                {{ $anak['rata_rata'] }}
                            </p>
                        </div>
                    </div>

                    @if (!empty($anak['setoran_terakhir']))
                        <div class="bg-gray-50 p-4 rounded border-t">
                            <p class="text-xs text-gray-600 mb-3 font-semibold">Setoran Terakhir:</p>
                            <div class="space-y-2">
                                @foreach ($anak['setoran_terakhir'] as $setoran)
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-gray-700">{{ $setoran['surah'] }}</span>
                                        <span
                                            class="font-semibold {{ $index % 2 === 0 ? 'text-green-600' : 'text-blue-600' }}">{{ $setoran['nilai'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        @if ($selectedAnakId && $selectedAnakData)
            <div class="bg-blue-50 rounded-lg shadow-md p-6 mb-8 border-l-4 border-blue-500">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">Analisis Detail Hafalan</h3>
                        <p class="text-sm text-gray-600">Statistik untuk: {{ $selectedAnakData['nama_siswa'] }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Perkembangan Nilai</h3>
                    <p class="text-sm text-gray-600 mb-4">Tren nilai 10 sesi terakhir</p>
                    <div style="position: relative; min-height: 300px;">
                        <div id="chartPerkembangan"></div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Target Hafalan Bulanan</h3>
                    <p class="text-sm text-gray-600 mb-4">Pencapaian ayat per bulan</p>
                    <div style="position: relative; min-height: 300px;">
                        <div id="chartTarget"></div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center gap-3 mb-6">
                    <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Nilai Per Aspek Penilaian</h3>
                    </div>
                </div>

                <div class="space-y-6">
                    <div>
                        <div class="flex justify-between mb-2">
                            <label class="text-gray-700 font-semibold">Tajwid</label>
                            <span class="text-green-600 font-bold">{{ $nilaiAspekTajwid }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-green-500 h-3 rounded-full" style="width: {{ $nilaiAspekTajwid }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-2">
                            <label class="text-gray-700 font-semibold">Makhroj</label>
                            <span class="text-blue-600 font-bold">{{ $nilaiAspekMakhroj }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-blue-500 h-3 rounded-full" style="width: {{ $nilaiAspekMakhroj }}%"></div>
                        </div>
                    </div>
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

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        document.addEventListener('livewire:init', () => {
            let chartPerkembangan = null;
            let chartTarget = null;

            function renderCharts(data) {
                // 1. Render Chart Perkembangan
                const elPerkembangan = document.querySelector("#chartPerkembangan");
                if (elPerkembangan) {
                    // Hapus chart lama jika ada agar tidak menumpuk
                    if (chartPerkembangan) chartPerkembangan.destroy();

                    const optionsPerkembangan = {
                        series: [
                            { name: 'Tajwid', data: data.perkembangan.tajwid },
                            { name: 'Makhroj', data: data.perkembangan.makhroj },
                            { name: 'Kelancaran', data: data.perkembangan.kelancaran }
                        ],
                        chart: { type: 'line', height: 300, toolbar: { show: false } },
                        stroke: { curve: 'smooth', width: 2 },
                        xaxis: { categories: data.perkembangan.labels },
                        yaxis: { min: 50, max: 100 }, // Agar grafik tidak terlalu gepeng
                        colors: ['#10B981', '#EF4444', '#3B82F6'],
                        grid: { show: true, borderColor: '#e5e7eb' }
                    };

                    chartPerkembangan = new ApexCharts(elPerkembangan, optionsPerkembangan);
                    chartPerkembangan.render();
                }

                // 2. Render Chart Target
                const elTarget = document.querySelector("#chartTarget");
                if (elTarget) {
                    if (chartTarget) chartTarget.destroy();

                    const optionsTarget = {
                        series: [
                            { name: 'Pencapaian', data: data.target.pencapaian },
                            { name: 'Target', data: data.target.target }
                        ],
                        chart: { type: 'bar', height: 300, toolbar: { show: false } },
                        plotOptions: { bar: { borderRadius: 4, columnWidth: '60%' } },
                        xaxis: { categories: data.target.labels },
                        colors: ['#10B981', '#D1D5DB'],
                        grid: { show: true, borderColor: '#e5e7eb' }
                    };

                    chartTarget = new ApexCharts(elTarget, optionsTarget);
                    chartTarget.render();
                }
            }

            // Inisialisasi Awal (Ambil data dari PHP Blade saat pertama load)
            renderCharts({
                perkembangan: {
                    labels: @json($chartPerkembanganLabels),
                    tajwid: @json($chartPerkembanganTajwid),
                    makhroj: @json($chartPerkembanganMakhroj),
                    kelancaran: @json($chartPerkembanganKelancaran)
                },
                target: {
                    labels: @json($chartTargetLabels),
                    pencapaian: @json($chartTargetPencapaian),
                    target: @json($chartTargetTarget)
                }
            });

            // Event Listener: Update saat anak diganti
            Livewire.on('update-charts', (eventData) => {
                // Livewire mengirim data dalam array [payload], jadi ambil index 0
                renderCharts(eventData[0]);
            });
        });
    </script>
</div>