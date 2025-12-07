<div>
    @include('components.admin.nav-data-master')
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-700">Data Siswa</h3>
            <p class="text-sm text-gray-500">Kelola data siswa</p>
        </div>

        <!-- Button Group -->
        <div class="flex gap-2">
            <button wire:click="openModal" type="button"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Tambah Siswa</span>
            </button>

            <!-- Button Import -->
            <button wire:click="openImportModal"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                    </path>
                </svg>
                <span>Import Excel</span>
            </button>

            <!-- Button Download Template -->
            <button wire:click="downloadTemplate" wire:loading.attr="disabled"
                class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200 flex items-center space-x-2">
                <svg wire:loading wire:target="downloadTemplate" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <svg wire:loading.remove wire:target="downloadTemplate" class="w-5 h-5" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                <span>Download Template Excel</span>
            </button>
        </div>
    </div>

    <!-- Flash Messages Success -->
    @if (session()->has('message'))
        <div
            class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg flex items-center justify-between">
            <span>{{ session('message') }}</span>
            <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    <!-- Flash Messages Error -->
    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg flex items-center justify-between">
            <span>{!! session('error') !!}</span>
            <button onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-900">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    <!-- Search Bar -->
    <div class="mb-4">
        <input type="text"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
            placeholder="Cari nama atau kode siswa..." wire:model.live="search">
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama
                        Siswa</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode
                        Siswa</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orang Tua
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($siswaList as $siswa)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $siswa->id_siswa }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div
                                    class="h-10 w-10 rounded-full bg-green-600 text-white flex items-center justify-center mr-3 font-semibold">
                                    {{ strtoupper(substr($siswa->nama_siswa, 0, 1)) }}
                                </div>
                                <div class="text-sm font-medium text-gray-900">{{ $siswa->nama_siswa }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full">
                                {{ $siswa->kode_siswa }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($siswa->kelas)
                                <div class="text-sm">
                                    <div class="font-medium text-gray-900">{{ $siswa->kelas->nama_kelas }}</div>
                                    <div class="text-gray-500">{{ $siswa->kelas->tahun_ajaran }}</div>
                                </div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($siswa->ortu && $siswa->ortu->akun)
                                <div class="text-xs">
                                    <div class="font-medium">{{ $siswa->ortu->akun->nama_lengkap }}</div>
                                    <div class="text-gray-400">{{ $siswa->ortu->no_hp }}</div>
                                </div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                            <button wire:click="edit({{ $siswa->id_siswa }})" type="button"
                                class="text-blue-600 hover:text-blue-900 mr-3">
                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                            </button>
                            <button wire:click="delete({{ $siswa->id_siswa }})"
                                wire:confirm="Yakin ingin menghapus siswa {{ addslashes($siswa->nama_siswa) }}?"
                                type="button" class="text-red-600 hover:text-red-900">
                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                </path>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">Tidak ada data siswa</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $siswaList->links() }}
    </div>

    <!-- Modal Form -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" wire:key="modal-{{ now() }}">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">
                                {{ $editMode ? 'Edit Siswa' : 'Tambah Siswa' }}
                            </h3>
                            <button wire:click="closeModal" type="button" class="text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <!-- Nama Siswa -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama Siswa <span
                                        class="text-red-500">*</span></label>
                                <input type="text" wire:model.defer="nama_siswa"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 px-3 py-2 @error('nama_siswa') border-red-500 @enderror">
                                @error('nama_siswa') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Kode Siswa -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Kode Siswa <span
                                        class="text-red-500">*</span></label>
                                <input type="text" wire:model.defer="kode_siswa"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 px-3 py-2 @error('kode_siswa') border-red-500 @enderror"
                                    placeholder="Contoh: S-004">
                                @error('kode_siswa') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Kelas -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Kelas <span
                                        class="text-red-500">*</span></label>
                                <select wire:model.defer="id_kelas"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 px-3 py-2 @error('id_kelas') border-red-500 @enderror">
                                    <option value="">Pilih Kelas</option>
                                    @foreach($kelasList as $kelas)
                                        <option value="{{ $kelas->id_kelas }}">{{ $kelas->nama_kelas }}
                                            ({{ $kelas->tahun_ajaran }})</option>
                                    @endforeach
                                </select>
                                @error('id_kelas') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Orang Tua -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Orang Tua</label>
                                <select wire:model.defer="id_ortu"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 px-3 py-2">
                                    <option value="">Pilih Orang Tua</option>
                                    @foreach($orangTuaList as $ortu)
                                        @if($ortu->akun)
                                            <option value="{{ $ortu->id_ortu }}">{{ $ortu->akun->nama_lengkap }}
                                                ({{ $ortu->no_hp }})</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        @if($editMode)
                            <button wire:click="update" type="button"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:w-auto sm:text-sm">
                                Update
                            </button>
                        @else
                            <button wire:click="store" type="button"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:w-auto sm:text-sm">
                                Simpan
                            </button>
                        @endif
                        <button wire:click="closeModal" type="button"
                            class="mt-3 sm:mt-0 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Import -->
    @if($showImportModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75" wire:click="closeImportModal"></div>

                <div class="bg-white rounded-lg p-6 max-w-md w-full relative z-10 shadow-xl">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">📥 Import Data Siswa</h3>
                        <button wire:click="closeImportModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Error Message INSIDE Modal -->
                    @if (session()->has('error'))
                        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-red-400 mr-2 flex-shrink-0 mt-0.5" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="flex-1">{!! session('error') !!}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Success Message INSIDE Modal -->
                    @if (session()->has('message'))
                        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-green-400 mr-2 flex-shrink-0 mt-0.5" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="flex-1">{{ session('message') }}</span>
                            </div>
                        </div>
                    @endif

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Upload File Excel/CSV
                        </label>
                        <input type="file" wire:model="importFile" accept=".xlsx,.xls,.csv"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('importFile')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                        <p class="text-xs text-blue-800 leading-relaxed">
                            ✅ Format: .xlsx, .xls, .csv<br>
                            ✅ Max size: 2MB<br>
                            💡 Download template dulu jika belum punya<br>
                            📋 Kolom: <strong>nama_siswa, kode_siswa, nama_kelas, email_ortu</strong>
                        </p>
                    </div>

                    <div wire:loading wire:target="import" class="mb-4 text-center">
                        <span class="text-sm text-blue-600">⏳ Sedang mengimport...</span>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <button wire:click="closeImportModal"
                            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-100">
                            Batal
                        </button>
                        <button wire:click="import" wire:loading.attr="disabled"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50">
                            Import Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    // Fix z-index modal overlay
    document.addEventListener('livewire:init', () => {
        Livewire.on('modal-closed', () => {
            document.body.style.overflow = 'auto';
            // Hapus semua backdrop yang mungkin tertinggal
            const backdrops = document.querySelectorAll('.fixed.inset-0.bg-gray-500');
            backdrops.forEach(backdrop => {
                if (!backdrop.closest('[wire\\:model]')) {
                    backdrop.remove();
                }
            });
        });
    });

    // Re-attach event listener setelah Livewire update
    document.addEventListener('livewire:navigated', () => {
        // Fungsi confirm delete akan otomatis ter-attach ulang
    });
</script>