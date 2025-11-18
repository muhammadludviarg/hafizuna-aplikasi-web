<div>
    <!-- Header -->
    <h2 class="text-2xl font-semibold text-gray-700 mb-2">Data Master</h2>
    <p class="text-sm text-gray-600 mb-6">Manajemen data guru, siswa, kelas, dan orang tua</p>

    <!-- Card Container -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        
        <!-- Tab Navigation -->
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button 
                    wire:click="setTab('siswa')"
                    class="px-6 py-3 text-sm font-medium border-b-2 transition-colors duration-200 {{ $activeTab === 'siswa' ? 'border-green-600 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span>Siswa</span>
                    </div>
                </button>

                <button 
                    wire:click="setTab('guru')"
                    class="px-6 py-3 text-sm font-medium border-b-2 transition-colors duration-200 {{ $activeTab === 'guru' ? 'border-green-600 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span>Guru</span>
                    </div>
                </button>

                <button 
                    wire:click="setTab('kelas')"
                    class="px-6 py-3 text-sm font-medium border-b-2 transition-colors duration-200 {{ $activeTab === 'kelas' ? 'border-green-600 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <span>Kelas</span>
                    </div>
                </button>

                <button 
                    wire:click="setTab('orang-tua')"
                    class="px-6 py-3 text-sm font-medium border-b-2 transition-colors duration-200 {{ $activeTab === 'orang-tua' ? 'border-green-600 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span>Orang Tua</span>
                    </div>
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
            @if($activeTab === 'siswa')
                @livewire('admin.kelola-siswa')
            @elseif($activeTab === 'guru')
                @livewire('admin.kelola-guru')
            @elseif($activeTab === 'kelas')
                @livewire('admin.kelola-kelas')
            @elseif($activeTab === 'orang-tua')
                @livewire('admin.kelola-orang-tua')
            @endif
        </div>
    </div>
</div>