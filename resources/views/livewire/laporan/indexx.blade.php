<?php

namespace App\Livewire\Laporan;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Kelompok;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination; // Import trait Pagination

class Indexx extends Component
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
        // [PERBAIKAN] Ambil user yang sedang login dari guard default
        $user = Auth::user();

        // [PERBAIKAN] Tentukan role berdasarkan method hasRole()
        // Pastikan model User Anda (app/Models/User.php) memiliki method hasRole()
        if ($user->hasRole('admin')) $this->role = 'admin';
        if ($user->hasRole('guru')) $this->role = 'guru';
        if ($user->hasRole('orangtua')) $this->role = 'orangtua';

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
            // [PERBAIKAN] Gunakan Auth::user() untuk mendapatkan relasi
            $this->daftarKelompok = Auth::user()->kelompok()->orderBy('nama_kelompok')->get();
        }
    }

    public function render()
    {
        $query = Siswa::query()->with('kelas', 'kelompok'); // Load relasi
        
        // [PERBAIKAN] Ambil user yang sedang login di sini
        $user = Auth::user(); 

        if ($this->role == 'admin') {
            // Admin: filter berdasarkan kelas, lalu kelompok, lalu search
            if ($this->selectedKelas) {
                $query->where('id_kelas', $this->selectedKelas);
                
                // [Dinamis] Update daftar kelompok untuk dropdown filter
                $this->daftarKelompok = Kelompok::where('id_kelas', $this->selectedKelas)->orderBy('nama_kelompok')->get();
                
            } else {
                 $this->daftarKelompok = collect(); // Kosongkan jika tidak ada kelas dipilih
            }

            if ($this->selectedKelompok) {
                // Asumsi Siswa punya relasi 'kelompok' (many to many via siswa_kelompok)
                $query->whereHas('kelompok', fn($q) => $q->where('kelompok.id_kelompok', $this->selectedKelompok));
            }
        } 
        
        elseif ($this->role == 'guru') {
            // [PERBAIKAN] Gunakan $user untuk mendapatkan relasi
            $guruKelompokIds = $user->kelompok()->pluck('id_kelompok');
            $query->whereHas('kelompok', fn($q) => $q->whereIn('kelompok.id_kelompok', $guruKelompokIds));

            // Filter lagi jika guru memilih kelompok spesifik dari dropdown-nya
            if ($this->selectedKelompok) {
                $query->whereHas('kelompok', fn($q) => $q->where('kelompok.id_kelompok', $this->selectedKelompok));
            }
        } 
        
        elseif ($this->role == 'orangtua') {
            // [PERBAIKAN] Gunakan $user untuk mendapatkan relasi
            $siswaIds = $user->siswa()->pluck('id_siswa'); 
            $query->whereIn('id_siswa', $siswaIds);
        }

        // Terapkan pencarian nama untuk semua role
        if ($this->searchSiswa) {
            $query->where('nama', 'like', '%'.$this->searchSiswa.'%');
        }

        // Terapkan pagination
        $daftarSiswa = $query->orderBy('nama')->paginate(15); // 15 siswa per halaman

        // Kirim data ke view
        return view('livewire.laporan.indexx', [
            'daftarSiswa' => $daftarSiswa 
        ])->layout($this->role == 'guru' ? 'layouts.guru' : 'layouts.app'); // Gunakan layout guru jika dia guru
    }
}