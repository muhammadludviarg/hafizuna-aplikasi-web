<div>
    {{-- Judul Halaman --}}
    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-gray-700">Data Master</h2>
        <p class="text-sm text-gray-600">Manajemen data guru, siswa, kelas, dan orang tua</p>
    </div>

    {{-- Alert Panduan (Responsive) --}}
    <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded shadow-sm">
        <div class="flex items-start">
            <div class="flex-shrink-0 pt-0.5">
                <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                    fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                        clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3 w-full">
                <h3 class="text-sm font-medium text-blue-800">Panduan Urutan Import Data</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p class="mb-1">Agar data saling terhubung otomatis, mohon lakukan import dengan urutan:</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2">
                        <div class="bg-blue-100 px-2 py-1 rounded text-xs font-semibold">1. Data Kelas</div>
                        <div class="bg-blue-100 px-2 py-1 rounded text-xs font-semibold">2. Data Orang Tua</div>
                        <div class="bg-blue-100 px-2 py-1 rounded text-xs font-semibold">3. Data Siswa</div>
                        <div class="bg-blue-100 px-2 py-1 rounded text-xs font-semibold">4. Data Guru</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Navigasi Tab (Scrollable di HP) --}}
    <div class="mb-6 bg-white rounded-lg shadow-sm border-b border-gray-200">
        {{-- overflow-x-auto WAJIB ada agar tab bisa digeser di HP --}}
        <div class="overflow-x-auto scrollbar-hide">
            <nav class="flex -mb-px min-w-max sm:min-w-full">
                @php
                    $tabs = [
                        ['id' => 'kelas', 'label' => 'Kelas', 'route' => 'admin.master.kelas'],
                        ['id' => 'ortu', 'label' => 'Orang Tua', 'route' => 'admin.master.ortu'],
                        ['id' => 'siswa', 'label' => 'Siswa', 'route' => 'admin.master.siswa'],
                        ['id' => 'guru', 'label' => 'Guru', 'route' => 'admin.master.guru'],
                    ];
                @endphp

                @foreach($tabs as $tab)
                            <a href="{{ route($tab['route']) }}" wire:navigate
                                class="group flex-1 min-w-[120px] px-6 py-4 text-sm font-medium border-b-2 transition-all duration-200 whitespace-nowrap flex flex-col sm:flex-row items-center justify-center gap-2
                                   {{ request()->routeIs($tab['route'])
                    ? 'border-green-600 text-green-600 bg-green-50'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}">
                                <span>{{ $tab['label'] }}</span>
                            </a>
                @endforeach
            </nav>
        </div>
    </div>
</div>