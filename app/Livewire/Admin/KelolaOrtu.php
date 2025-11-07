<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Ortu;
use App\Models\User; // Model 'akun' kita
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class KelolaOrtu extends Component
{
    use WithPagination;

    // Properti untuk form (dari tabel akun & Ortu)
    public $nama_lengkap;
    public $email;
    public $no_hp;
    public $password;

    // Properti untuk UI
    public $showModal = false;
    public $editMode = false;
    public $ortuId; // Perbaikan: ganti jadi 'ortuId'
    public $akunId;
    public $search = '';

    // Aturan validasi
    protected function rules()
    {
        return [
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:akun,email,' . $this->akunId . ',id_akun', // Cek unique kecuali ID saat ini
            'no_hp' => 'nullable|string|max:20',
            // Password hanya wajib saat membuat baru
            'password' => $this->editMode ? 'nullable|min:8' : 'required|min:8',
        ];
    }

    // Fungsi untuk menyimpan (Create & Update)
    public function simpan()
    {
        $this->validate();

        // Gunakan Transaksi Database untuk memastikan 2 tabel berhasil disimpan
        DB::transaction(function () {
            // 1. Simpan atau Update data di tabel 'akun'
            $dataAkun = [
                'nama_lengkap' => $this->nama_lengkap,
                'email' => $this->email,
                'role' => 'ortu', // Perbaikan: ganti jadi 'ortu' (konsistensi)
                'status' => true,
            ];
            
            // Hanya update password jika diisi
            if (!empty($this->password)) {
                $dataAkun['sandi_hash'] = Hash::make($this->password);
            }

            $akun = User::updateOrCreate(
                ['id_akun' => $this->akunId],
                $dataAkun
            );

            // 2. Simpan atau Update data di tabel 'orang_tua'
            Ortu::updateOrCreate(
                ['id_ortu' => $this->ortuId], // Perbaikan: ganti jadi 'id_ortu' dan 'ortuId'
                [
                    'id_akun' => $akun->id_akun,
                    'no_hp' => $this->no_hp,
                ]
            );
        });

        $this->closeModal();
        session()->flash('message', $this->editMode ? 'Data Ortu berhasil diperbarui.' : 'Data Ortu berhasil ditambahkan.');
    }

    public function tambah()
    {
        $this->resetForm();
        $this->editMode = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $ortu = Ortu::with('akun')->findOrFail($id); // Ambil Ortu DAN akunnya
        $this->ortuId = $id; // Perbaikan: ganti jadi 'ortuId'
        $this->akunId = $ortu->id_akun;
        $this->nama_lengkap = $ortu->akun->nama_lengkap;
        $this->email = $ortu->akun->email;
        $this->no_hp = $ortu->no_hp;
        
        $this->editMode = true;
        $this->showModal = true;
    }

    public function hapus($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $ortu = Ortu::findOrFail($id);
                $akunId = $ortu->id_akun;
                
                $ortu->delete(); // Hapus data Ortu
                User::destroy($akunId); // Hapus data akun
            });
            session()->flash('message', 'Data Ortu berhasil dihapus.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus: Ortu ini mungkin masih terhubung dengan data siswa.');
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->nama_lengkap = '';
        $this->email = '';
        $this->no_hp = '';
        $this->password = '';
        $this->ortuId = null; // Perbaikan: ganti jadi 'ortuId'
        $this->akunId = null;
    }

    public function render()
    {
        // Ambil data Ortu dengan relasi 'akun' dan pencarian
        $query = Ortu::with('akun')
            ->whereHas('akun', function($q) {
                $q->where('nama_lengkap', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%');
            })
            ->orderBy('id_ortu', 'desc'); // Perbaikan: ganti jadi 'id_ortu'

        return view('livewire.admin.kelola-ortu', [
            'daftarOrtu' => $query->paginate(10), // Perbaikan: 'daftarOrtu' (O besar)
        ])->layout('layouts.app');
    }
}