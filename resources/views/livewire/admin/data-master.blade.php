<div>
    <h2 class="text-2xl font-semibold text-gray-700 mb-2">Data Master</h2>
    <p class="text-sm text-gray-600 mb-6">Manajemen data guru, siswa, kelas, dan orang tua</p>

    <div>
        <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Panduan Urutan Import Data</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>Agar data saling terhubung otomatis, mohon lakukan import dengan urutan berikut:</p>
                        <ol class="list-decimal list-inside mt-1 space-y-1">
                            <li><strong>Data Kelas</strong></li>
                            <li><strong>Data Orang Tua</strong></li>
                            <li><strong>Data Siswa</strong></li>
                            <li><strong>Data Guru</strong></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">

            <div class="border-b border-gray-200 overflow-x-auto">
                <nav class="flex -mb-px min-w-full">
                    <button wire:click="setTab('siswa')"
                        class="flex-1 min-w-[100px] px-4 py-3 text-sm font-medium border-b-2 transition-colors duration-200 whitespace-nowrap {{ $activeTab === 'siswa' ? 'border-green-600 text-green-600 bg-green-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}">
                        <div class="flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                </path>
                            </svg>
                            <span>Siswa</span>
                        </div>
                    </button>

                    <button wire:click="setTab('guru')"
                        class="flex-1 min-w-[100px] px-4 py-3 text-sm font-medium border-b-2 transition-colors duration-200 whitespace-nowrap {{ $activeTab === 'guru' ? 'border-green-600 text-green-600 bg-green-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}">
                        <div class="flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                            <span>Guru</span>
                        </div>
                    </button>

                    <button wire:click="setTab('kelas')"
                        class="flex-1 min-w-[100px] px-4 py-3 text-sm font-medium border-b-2 transition-colors duration-200 whitespace-nowrap {{ $activeTab === 'kelas' ? 'border-green-600 text-green-600 bg-green-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}">
                        <div class="flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                            <span>Kelas</span>
                        </div>
                    </button>

                    <button wire:click="setTab('orang-tua')"
                        class="flex-1 min-w-[100px] px-4 py-3 text-sm font-medium border-b-2 transition-colors duration-200 whitespace-nowrap {{ $activeTab === 'orang-tua' ? 'border-green-600 text-green-600 bg-green-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}">
                        <div class="flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                            <span>Orang Tua</span>
                        </div>
                    </button>
                </nav>
            </div>

            <div class="p-4 md:p-6">
                <div class="w-full overflow-x-auto">
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
    </div>
</div>