<?php

namespace App\Livewire\Laporan;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Kelompok;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination; // Import trait Pagination

class Index extends Component
{
    use WithPagination; // Gunakan trait Pagination

    public $role;
    public $daftarKelas = [];
    public $daftarKelompok = [];

    // Properti filter
    public $selectedKelas = '';
    public $selectedKelompok = '';
    public $searchSiswa = '';

    public function mount()
    {
        $user = Auth::user();

        // Tentukan role berdasarkan method hasRole()
        if (!empty($user) && $user->hasRole('admin')) $this->role = 'admin';
        if (!empty($user) && $user->hasRole('guru')) $this->role = 'guru';
        if (!empty($user) && $user->hasRole('orangtua')) $this->role = 'orangtua';

        // Muat data filter awal
        $this->loadInitialFilters();
    }

    // Saat filter berubah, reset halaman pagination ke 1
    public function updatedSelectedKelas() { 
        $this->selectedKelompok = ''; 
        $this->resetPage(); // Reset pagination
    }
    public function updatedSelectedKelompok() { 
        $this->resetPage(); // Reset pagination
    }
    public function updatedSearchSiswa() { 
        $this->resetPage(); // Reset pagination
    }

    public function loadInitialFilters()
    {
        if ($this->role == 'admin') {
            $this->daftarKelas = Kelas::orderBy('nama_kelas')->get();
        }

        if ($this->role == 'guru') {
            // [PERBAIKAN] Relasi 'kelompok' ada di model 'guru', bukan 'user'
            // Pastikan model User Anda (app/Models/User.php) memiliki relasi 'guru()'
            // Dan model Guru (app/Models/Guru.php) memiliki relasi 'kelompok()'
            $this->daftarKelompok = Auth::user()->guru->kelompok()->orderBy('id_kelompok')->get();
        }
    }

    public function render()
    {
        $query = Siswa::query()->with('kelas', 'kelompok'); // Load relasi
        
        $user = Auth::user(); 

        if ($this->role == 'admin') {
            // Admin: filter berdasarkan kelas, lalu kelompok, lalu search
            if ($this->selectedKelas) {
                $query->where('id_kelas', $this->selectedKelas);
                
                // [Dinamis] Update daftar kelompok untuk dropdown filter
                $this->daftarKelompok = Kelompok::where('id_kelas', $this->selectedKelas)->orderBy('nama_kelompok')->get();
                
            } else {
                 if (empty($this->selectedKelas)) {
                     // Jika Admin tidak memilih kelas, tampilkan semua kelompok
                     $this->daftarKelompok = Kelompok::orderBy('nama_kelompok')->get(); 
                 }
            }

            if ($this->selectedKelompok) {
                $query->whereHas('kelompok', fn($q) => $q->where('kelompok.id_kelompok', $this->selectedKelompok));
            }
        } 
        
        elseif ($this->role == 'guru') {
            // [PERBAIKAN] Relasi 'kelompok' ada di model 'guru', bukan 'user'
            // Pastikan model User Anda memiliki relasi 'guru()'
            $guruKelompokIds = $user->guru->kelompok()->pluck('id_kelompok');
            $query->whereHas('kelompok', fn($q) => $q->whereIn('kelompok.id_kelompok', $guruKelompokIds));

            // Filter lagi jika guru memilih kelompok spesifik dari dropdown-nya
            if ($this->selectedKelompok) {
                $query->whereHas('kelompok', fn($q) => $q->where('kelompok.id_kelompok', $this->selectedKelompok));
            }
        } 
        
        elseif ($this->role == 'orangtua') {
            // [PERBAIKAN] Relasi 'siswa' ada di model 'orangTua', bukan 'user'
            // Pastikan model User Anda (app/Models/User.php) memiliki relasi 'orangTua()'
            // Dan model OrangTua (app/Models/OrangTua.php) memiliki relasi 'siswa()'
            $siswaIds = $user->orangTua->siswa()->pluck('id_siswa'); 
            $query->whereIn('id_siswa', $siswaIds);
        }

        // Terapkan pencarian nama untuk semua role
        if ($this->searchSiswa) {
            $query->where('nama', 'like', '%'.$this->searchSiswa.'%');
        }

        // Terapkan pagination
        $daftarSiswa = $query->orderBy('nama_siswa')->paginate(15); // 15 siswa per halaman

        // Kirim data ke view
        return view('livewire.laporan.indexx', [
            'daftarSiswa' => $daftarSiswa 
        ])->layout($this->role == 'guru' ? 'layouts.guru' : 'layouts.app'); // Gunakan layout guru jika dia guru
    }
}