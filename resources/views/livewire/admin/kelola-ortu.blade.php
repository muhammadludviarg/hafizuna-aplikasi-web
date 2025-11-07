<div>
    <h2 class="text-2xl font-semibold text-gray-700">Data Master</h2>
    <h4 class="text-sm text-gray-500 mb-6">Manajemen data guru, siswa, kelas, dan orang tua</h4>

    <div class="mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <a href="{{ route('admin.siswa') }}"
                    class="flex items-center whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Siswa
                </a>
                <a href="{{ route('admin.guru') }}"
                    class="flex items-center whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Guru
                </a>
                <a href="{{ route('admin.kelas') }}"
                    class="flex items-center whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Kelas
                </a>
                <a href="{{ route('admin.ortu') }}"
                    class="flex items-center whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm text-green-600 border-green-600">
                    Orang Tua
                </a>
            </nav>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md">

        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <div class="flex justify-between items-center mb-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-700">Data Orang Tua</h3>
                <p class="text-sm text-gray-500">Kelola data orang tua</p>
            </div>
            <button wire:click="tambah()"
                class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Tambah Ortu
            </button>
        </div>

        <div class="mb-4">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama atau email ortu..."
                class="w-full rounded-md border-gray-300 shadow-sm">
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Lengkap</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. HP</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">

                    @forelse ($daftarOrtu as $ortu)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $ortu->akun->nama_lengkap ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $ortu->akun->email ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $ortu->no_hp }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <button wire:click="edit({{ $ortu->id_ortu }})"
                                    class="text-yellow-600 hover:text-yellow-900">Edit</button>
                                <button wire:click="hapus({{ $ortu->id_ortu }})"
                                    onclick="return confirm('Yakin ingin menghapus ortu ini? Ini akan menghapus akun login mereka.')"
                                    class="text-red-600 hover:text-red-900">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Data ortu tidak ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $daftarOrtu->links() }}
        </div>
    </div>

    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-lg">
                <h3 class="text-lg font-semibold mb-4">{{ $editMode ? 'Edit Data Ortu' : 'Tambah Ortu Baru' }}</h3>

                <form wire:submit.prevent="simpan">
                    <div class="space-y-4">
                        <div>
                            <label for="nama_lengkap" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                            <input wire:model="nama_lengkap" type="text" id="nama_lengkap"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('nama_lengkap') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email (untuk Login)</label>
                            <input wire:model="email" type="email" id="email"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="no_hp" class="block text-sm font-medium text-gray-700">No. HP</label>
                            <input wire:model="no_hp" type="text" id="no_hp"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('no_hp') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                            <input wire:model="password" type="password" id="password"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                placeholder="{{ $editMode ? 'Isi untuk ganti password' : '' }}">
                            @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button wire:click="closeModal()" type="button"
                            class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg">Batal</button>
                        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>