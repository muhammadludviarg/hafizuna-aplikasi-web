<!-- create guru-specific sidebar with different menu items -->
<aside class="w-64 bg-white border-r border-gray-200 flex flex-col h-screen">
    <!-- Header -->
    <div class="p-6 border-b border-gray-200">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center text-white font-bold text-lg">
                📖
            </div>
            <div>
                <h1 class="font-bold text-gray-900">HAFIZUNA</h1>
                <p class="text-xs text-gray-600">SD Islam Al-Azhar 27</p>
            </div>
        </div>
    </div>

    <!-- User Info -->
    <div class="p-6 border-b border-gray-200">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center text-white font-bold">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div>
                <p class="font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-600">Guru</p>
            </div>
        </div>
    </div>

    <!-- Menu Items -->
    <nav class="flex-1 px-4 py-6 space-y-2">
        <a href="{{ route('guru.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg font-medium {{ request()->routeIs('guru.dashboard') ? 'bg-green-500 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
            <span class="text-lg">🏠</span>
            Beranda
        </a>
        
        <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg font-medium text-gray-700 hover:bg-gray-100">
            <span class="text-lg">📝</span>
            Input Data Hafalan
        </a>
        
        <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg font-medium text-gray-700 hover:bg-gray-100">
            <span class="text-lg">👥</span>
            Kelola Siswa & Kelompok
        </a>
        
        <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg font-medium text-gray-700 hover:bg-gray-100">
            <span class="text-lg">📊</span>
            Laporan Hafalan
        </a>
        
        <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg font-medium text-gray-700 hover:bg-gray-100">
            <span class="text-lg">🔐</span>
            Ganti Password
        </a>
    </nav>

    <!-- Logout Button -->
    <div class="p-4 border-t border-gray-200">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg font-medium text-red-600 hover:bg-red-50">
                <span class="text-lg">🚪</span>
                Keluar
            </button>
        </form>
    </div>
</aside>
