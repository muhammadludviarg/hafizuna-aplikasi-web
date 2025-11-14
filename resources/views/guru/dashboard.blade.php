@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-6">
    <!-- Header -->
    <div class="bg-white px-6 py-4 rounded-lg shadow-sm border-b">
        <h1 class="text-2xl font-bold text-gray-900">Beranda</h1>
    </div>

    <!-- Green Greeting Box -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-8 text-white flex justify-between items-center shadow-lg">
        <div>
            <h2 class="text-3xl font-bold mb-2">Assalamu'alaikum, {{ Auth::user()->name }}!</h2>
            <p class="text-green-100">Semoga Allah memberkahi usaha kita dalam membimbing para penghafal Al-Qur'an</p>
        </div>
        <div class="text-6xl opacity-20">
            📖
        </div>
    </div>

    <!-- 4 Stat Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Kelas Diampu -->
        <div class="bg-white rounded-lg shadow-sm border-l-4 border-green-500 p-6">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-gray-600 font-medium">Kelas Diampu</h3>
                <div class="bg-green-100 p-2 rounded-lg text-green-600">
                    📚
                </div>
            </div>
            <p class="text-4xl font-bold text-green-600 mb-2">{{ $totalKelasDiampu }}</p>
            <p class="text-sm text-gray-500">
                @if($kelasD->count() > 0)
                    {{ $kelasD->first()->nama_kelas ?? 'Kelas aktif' }}
                @else
                    Belum ada kelas
                @endif
            </p>
        </div>

        <!-- Total Siswa -->
        <div class="bg-white rounded-lg shadow-sm border-l-4 border-blue-500 p-6">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-gray-600 font-medium">Total Siswa</h3>
                <div class="bg-blue-100 p-2 rounded-lg text-blue-600">
                    👥
                </div>
            </div>
            <p class="text-4xl font-bold text-blue-600 mb-2">{{ $totalSiswa }}</p>
            <p class="text-sm text-gray-500">Siswa bimbingan aktif</p>
        </div>

        <!-- Rata-rata Nilai -->
        <div class="bg-white rounded-lg shadow-sm border-l-4 border-orange-500 p-6">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-gray-600 font-medium">Rata-rata Nilai</h3>
                <div class="bg-orange-100 p-2 rounded-lg text-orange-600">
                    🏆
                </div>
            </div>
            <p class="text-4xl font-bold text-orange-600 mb-2">{{ $rataRataNilai }}</p>
            <p class="text-sm text-gray-500">Dari semua sesi hafalan</p>
        </div>

        <!-- Sesi Bulan Ini -->
        <div class="bg-white rounded-lg shadow-sm border-l-4 border-purple-500 p-6">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-gray-600 font-medium">Sesi Bulan Ini</h3>
                <div class="bg-purple-100 p-2 rounded-lg text-purple-600">
                    ⏰
                </div>
            </div>
            <p class="text-4xl font-bold text-purple-600 mb-2">{{ $totalSesiBulanIni }}</p>
            <p class="text-sm text-gray-500">Total sesi hafalan</p>
        </div>
    </div>

    <!-- 2 Charts in Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Progres Hafalan Per Kelas -->
        <div class="bg-white rounded-lg shadow-sm border border-green-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-2">Progres Hafalan Per Kelas</h3>
            <p class="text-sm text-gray-600 mb-4">Persentase pencapaian target hafalan</p>
            <div class="h-80">
                <svg id="progressChart" viewBox="0 0 800 300"></svg>
            </div>
        </div>

        <!-- Tren Nilai 7 Hari Terakhir -->
        <div class="bg-white rounded-lg shadow-sm border border-blue-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-2">Tren Nilai 7 Hari Terakhir</h3>
            <p class="text-sm text-gray-600 mb-4">Perkembangan nilai per aspek penilaian</p>
            <div class="h-80">
                <svg id="trendChart" viewBox="0 0 800 300"></svg>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/recharts@latest"></script>
<script>
    // Progress Chart Data
    const progresData = @json($progresPerKelas);
    const maxWidth = 700;
    const barWidth = Math.min(150, maxWidth / (progresData.length * 1.5));
    const spacing = 20;
    
    // Tren Chart Data
    const trenData = @json($trenNilai);
    const days = Object.keys(trenData);
    
    // Simple bar chart for progres
    let svg = document.getElementById('progressChart');
    let xOffset = 50;
    
    progresData.forEach((item, idx) => {
        const progres = item.progres || 0;
        const height = (progres / 100) * 200;
        
        // Progres bar
        const rect1 = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
        rect1.setAttribute('x', xOffset);
        rect1.setAttribute('y', 220 - height);
        rect1.setAttribute('width', barWidth - 5);
        rect1.setAttribute('height', height);
        rect1.setAttribute('fill', '#15803d');
        svg.appendChild(rect1);
        
        // Target bar
        const rect2 = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
        rect2.setAttribute('x', xOffset + barWidth);
        rect2.setAttribute('y', 20);
        rect2.setAttribute('width', barWidth - 5);
        rect2.setAttribute('height', 200);
        rect2.setAttribute('fill', '#86efac');
        svg.appendChild(rect2);
        
        // Label
        const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        text.setAttribute('x', xOffset + barWidth - 5);
        text.setAttribute('y', 250);
        text.setAttribute('font-size', '12');
        text.setAttribute('text-anchor', 'middle');
        text.textContent = item.nama_kelas;
        svg.appendChild(text);
        
        xOffset += barWidth * 2 + spacing;
    });
    
    // Legend
    const legend1 = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
    legend1.setAttribute('x', 50);
    legend1.setAttribute('y', 270);
    legend1.setAttribute('width', 15);
    legend1.setAttribute('height', 15);
    legend1.setAttribute('fill', '#15803d');
    svg.appendChild(legend1);
    
    const legendText1 = document.createElementNS('http://www.w3.org/2000/svg', 'text');
    legendText1.setAttribute('x', 70);
    legendText1.setAttribute('y', 282);
    legendText1.setAttribute('font-size', '12');
    legendText1.textContent = 'Progres (%)';
    svg.appendChild(legendText1);
    
    const legend2 = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
    legend2.setAttribute('x', 200);
    legend2.setAttribute('y', 270);
    legend2.setAttribute('width', 15);
    legend2.setAttribute('height', 15);
    legend2.setAttribute('fill', '#86efac');
    svg.appendChild(legend2);
    
    const legendText2 = document.createElementNS('http://www.w3.org/2000/svg', 'text');
    legendText2.setAttribute('x', 220);
    legendText2.setAttribute('y', 282);
    legendText2.setAttribute('font-size', '12');
    legendText2.textContent = 'Target (%)';
    svg.appendChild(legendText2);
</script>
@endpush

@endsection
