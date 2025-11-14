@extends('layouts.ortu')

@section('content')
<div class="p-8 max-w-7xl mx-auto">
    <!-- Greeting Box -->
    <div class="bg-green-500 text-white rounded-lg p-8 mb-8 flex justify-between items-center">
        <div>
            <h2 class="text-3xl font-bold mb-2">Assalamu'alaikum!</h2>
            <p class="text-green-100">Pantau perkembangan hafalan putra-putri Anda</p>
        </div>
        <div class="text-6xl opacity-50">
            <svg class="w-20 h-20" fill="currentColor" viewBox="0 0 24 24">
                <path d="M4 6h16V4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h4v4h8v-4h4c1.1 0 2-.9 2-2V6z"/>
            </svg>
        </div>
    </div>

    <!-- Child Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        @forelse($children as $child)
            <div class="bg-white rounded-lg border-l-4 border-green-500 p-6 shadow">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">{{ $child->nama }}</h3>
                        <p class="text-gray-500">{{ $child->kelas }}</p>
                    </div>
                    <svg class="w-6 h-6 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2l-2.81 6.63L2 9.24l5.46 4.73L5.82 21 12 17.27z"/>
                    </svg>
                </div>
                
                <!-- Progress Bar -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">Progres Hafalan</span>
                        <span class="text-green-500 font-bold">67%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" style="width: 67%"></div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-gray-50 p-3 rounded">
                        <p class="text-gray-500 text-sm">Total Sesi</p>
                        <p class="text-2xl font-bold text-gray-800">5</p>
                    </div>
                    <div class="bg-green-50 p-3 rounded">
                        <p class="text-gray-500 text-sm">Rata-rata</p>
                        <p class="text-2xl font-bold text-green-500">89</p>
                    </div>
                </div>

                <!-- Latest Sessions -->
                <div>
                    <p class="text-sm text-gray-600 font-semibold mb-3">Sesi Terbaru:</p>
                    @foreach($sesiTerakhir as $sesi)
                        <div class="flex justify-between items-center mb-2 text-sm">
                            <span class="text-gray-600">{{ $sesi['surah'] }}</span>
                            <span class="text-gray-800 font-semibold">{{ $sesi['nilai'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="col-span-2 bg-white rounded-lg p-8 text-center">
                <p class="text-gray-500">Belum ada data anak</p>
            </div>
        @endforelse
    </div>

    <!-- Analysis Section -->
    <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-6">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center h-12 w-12 rounded-full bg-blue-500">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-800 mb-1">Analisis Detail Hafalan</h3>
                <p class="text-gray-600 text-sm mb-4">Pilih anak untuk melihat grafik perkembangan dan analisis</p>
                @if($children->count() > 0)
                    <div class="flex items-center gap-2">
                        <label class="text-sm text-gray-600">Pilih Anak:</label>
                        <select class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                            @foreach($children as $child)
                                <option value="{{ $child->id }}" {{ $activeChild->id == $child->id ? 'selected' : '' }}>
                                    {{ $child->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
