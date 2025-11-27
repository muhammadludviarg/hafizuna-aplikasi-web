<div>
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-700">Data Guru</h3>
            <p class="text-sm text-gray-500">Kelola data guru</p>
        </div>
        
        <!-- Button Group -->
        <div class="flex gap-2">
            <button wire:click="openModal" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Tambah Guru</span>
            </button>

            <!-- Button Import -->
            <button wire:click="openImportModal" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                <span>Import CSV</span>
            </button>

            <!-- Button Download Template -->
            <a href="{{ asset('templates/template_guru.csv') }}" download class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Download Template</span>
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg flex items-center justify-between">
            <span>{{ session('message') }}</span>
            <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">✕</button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg flex items-center justify-between">
            <span>{!! session('error') !!}</span>
            <button onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-900">✕</button>
        </div>
    @endif

    <!-- Search Bar -->
    <div class="mb-4">
        <input 
            type="text" 
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" 
            placeholder="Cari nama guru, email, atau no HP..."
            wire:model.live="search">
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Guru</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No HP</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($guruList as $guru)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $guru->id_guru }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-blue-600 text-white flex items-center justify-center mr-3 font-semibold">
                                    {{ $guru->akun ? strtoupper(substr($guru->akun->nama_lengkap, 0, 1)) : 'G' }}
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $guru->akun->nama_lengkap ?? '-' }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        ID: {{ $guru->id_guru }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $guru->akun->email ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $guru->no_hp }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                            <button wire:click="edit({{ $guru->id_guru }})" class="text-blue-600 hover:text-blue-900 mr-3" title="Edit">
                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button onclick="confirmDeleteGuru({{ $guru->id_guru }}, '{{ addslashes($guru->akun->nama_lengkap ?? 'guru ini') }}')"
                                    type="button"
                                    class="text-red-600 hover:text-red-900" title="Hapus">
                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">Tidak ada data guru</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $guruList->links() }}
    </div>

    <!-- Modal Form -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">
                                {{ $editMode ? 'Edit Guru' : 'Tambah Guru' }}
                            </h3>
                            <button wire:click="closeModal" class="text-gray-400 hover:text-gray-500">✕</button>
                        </div>

                        <form class="space-y-4">
                            <!-- Nama Lengkap -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="nama_lengkap" 
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 @error('nama_lengkap') border-red-500 @enderror">
                                @error('nama_lengkap') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="email" 
                                    wire:model="email" 
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 @error('email') border-red-500 @enderror"
                                    placeholder="guru@example.com">
                                @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- No HP -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    No HP <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="no_hp" 
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 @error('no_hp') border-red-500 @enderror"
                                    placeholder="08123456789">
                                @error('no_hp') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            @if(!$editMode)
                            <div class="bg-blue-50 border border-blue-200 rounded p-3">
                                <p class="text-xs text-blue-700">
                                    <strong>Info:</strong> Password default akan di-set ke: <code class="bg-blue-100 px-1 rounded">password123</code><br>
                                </p>
                            </div>
                            @endif
                        </form>
                    </div>

                    <!-- Modal Footer -->
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        @if($editMode)
                            <button wire:click="update" class="w-full sm:w-auto px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 sm:ml-3">
                                Update
                            </button>
                        @else
                            <button wire:click="store" class="w-full sm:w-auto px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 sm:ml-3">
                                Simpan
                            </button>
                        @endif
                        <button wire:click="closeModal" class="mt-3 sm:mt-0 w-full sm:w-auto px-4 py-2 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
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
                        <h3 class="text-lg font-semibold text-gray-900">📥 Import Data Guru</h3>
                        <button wire:click="closeImportModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Upload File Excel/CSV
                        </label>
                        <input 
                            type="file" 
                            wire:model="importFile"
                            accept=".xlsx,.xls,.csv"
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
                            📋 Kolom: <strong>nama_lengkap, email, no_hp</strong><br>
                            🔐 Password default: <strong>password123</strong>
                        </p>
                    </div>
                    
                    <div wire:loading wire:target="import" class="mb-4 text-center">
                        <span class="text-sm text-blue-600">⏳ Sedang mengimport...</span>
                    </div>
                    
                    <div class="flex justify-end space-x-2">
                        <button 
                            wire:click="closeImportModal"
                            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-100">
                            Batal
                        </button>
                        <button 
                            wire:click="import"
                            wire:loading.attr="disabled"
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
function confirmDeleteGuru(id, nama) {
    if (confirm('Yakin ingin menghapus data ' + nama + '?')) {
        Livewire.find('{{ $_instance->getId() }}').delete(id);
    }
}
</script>

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