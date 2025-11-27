<div>
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Kelola Siswa dan Kelompok</h1>
        <p class="text-gray-600 mt-1">Kelola siswa dalam kelompok setoran hafalan</p>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-start">
            <svg class="w-5 h-5 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-start">
            <svg class="w-5 h-5 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Main Content --}}
    <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
        {{-- Header --}}
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-800">Kelompok Saya</h2>
            <p class="text-sm text-gray-600 mt-1">Kelola siswa dalam kelompok yang Anda bimbing</p>
        </div>

        {{-- Search Bar --}}
        <div class="mb-6">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" 
                       wire:model.live="search" 
                       placeholder="Cari nama kelompok atau kelas..."
                       class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Kelompok</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah Anggota</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($kelompok as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $item->nama_kelompok }}</div>
                                <div class="text-xs text-gray-500">{{ $item->tahun_ajaran }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->kelas->nama_kelas ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $item->siswa->count() }} siswa
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <button wire:click="editKelompok({{ $item->id_kelompok }})" 
                                        class="text-blue-600 hover:text-blue-900 font-medium flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Kelola Siswa
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.653-.084-1.284-.24-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.653.084-1.284.24-1.857m0 0a5.002 5.002 0 019.52 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <p class="text-lg font-medium">Belum ada kelompok</p>
                                    <p class="text-sm mt-1">Anda belum diberi kelompok untuk dibimbing</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Edit (Kelola Siswa) --}}
    @if($isModalOpen)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closeModal">
            <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white" wire:click.stop>
                {{-- Modal Header --}}
                <div class="flex items-center justify-between pb-4 border-b">
                    <h3 class="text-xl font-semibold text-gray-900">
                        Kelola Siswa - {{ $nama_kelompok }}
                    </h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                {{-- Form --}}
                <form wire:submit.prevent="updateKelompok" class="mt-4 space-y-4">
                    {{-- Periode Kelompok --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                            <input type="date" wire:model="tgl_mulai" 
                                   class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('tgl_mulai') border-red-400 @enderror">
                            @error('tgl_mulai') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
                            <input type="date" wire:model="tgl_selesai" 
                                   class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('tgl_selesai') border-red-400 @enderror">
                            @error('tgl_selesai') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Pilih Siswa --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Siswa</label>
                        
                        @if(count($daftar_siswa) > 0)
                            <div class="border rounded-lg max-h-60 overflow-y-auto p-3 space-y-2 @error('siswa_dipilih') border-red-400 @enderror">
                                @foreach($daftar_siswa as $siswa)
                                    @php
                                        $kelompokLain = $siswa->kelompok->first();
                                        $sudahPunyaKelompok = $kelompokLain !== null;
                                    @endphp
                                    
                                    <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer {{ $sudahPunyaKelompok ? 'bg-yellow-50 border border-yellow-200' : '' }}">
                                        <input type="checkbox" 
                                               wire:model="siswa_dipilih" 
                                               value="{{ $siswa->id_siswa }}" 
                                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        
                                        <span class="ml-3 flex-1">
                                            <span class="text-sm text-gray-700">{{ $siswa->nama_siswa }} ({{ $siswa->kode_siswa }})</span>
                                            
                                            @if($sudahPunyaKelompok)
                                                <span class="ml-2 text-xs text-yellow-700 bg-yellow-100 px-2 py-0.5 rounded-full">
                                                    Kelompok {{ $kelompokLain->nama_kelompok }}
                                                </span>
                                            @endif
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            
                            <p class="mt-2 text-xs text-gray-500">{{ count($siswa_dipilih) }} siswa dipilih</p>
                            
                            @if($daftar_siswa->where('kelompok', '!=', null)->count() > 0)
                                <div class="mt-2 bg-blue-50 border border-blue-200 p-2 rounded-lg">
                                    <p class="text-xs text-blue-800">
                                        <strong>Info:</strong> Jika siswa yang ada di kelompok lain dipilih, siswa akan <strong> dipindahkan </strong> ke kelompok ini.
                                    </p>
                                </div>
                            @endif
                        @else
                            <div class="border rounded-lg p-4 text-center text-gray-500">
                                <p class="text-sm">Tidak ada siswa di kelas ini</p>
                            </div>
                        @endif
                        
                        @error('siswa_dipilih') 
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Buttons --}}
                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <button type="button" wire:click="closeModal" 
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>