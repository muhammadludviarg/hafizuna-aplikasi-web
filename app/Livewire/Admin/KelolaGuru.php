<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Guru;
use App\Models\User; // Model 'akun' kita
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class KelolaGuru extends Component
{
    use WithPagination;

    // Properti untuk form (dari tabel akun & guru)
    public $nama_lengkap;
    public $email;
    public $no_hp;
    public $password;

    // Properti untuk UI
    public $showModal = false;
    public $editMode = false;
    public $guruId;
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
                'role' => 'guru', // Set role-nya
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

            // 2. Simpan atau Update data di tabel 'guru'
            Guru::updateOrCreate(
                ['id_guru' => $this->guruId],
                [
                    'id_akun' => $akun->id_akun,
                    'no_hp' => $this->no_hp,
                ]
            );
        });

        $this->closeModal();
        session()->flash('message', $this->editMode ? 'Data Guru berhasil diperbarui.' : 'Data Guru berhasil ditambahkan.');
    }

    public function tambah()
    {
        $this->resetForm();
        $this->editMode = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $guru = Guru::with('akun')->findOrFail($id); // Ambil guru DAN akunnya
        $this->guruId = $id;
        $this->akunId = $guru->id_akun;
        $this->nama_lengkap = $guru->akun->nama_lengkap;
        $this->email = $guru->akun->email;
        $this->no_hp = $guru->no_hp;
        
        $this->editMode = true;
        $this->showModal = true;
    }

    public function hapus($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $guru = Guru::findOrFail($id);
                $akunId = $guru->id_akun;
                
                $guru->delete(); // Hapus data guru
                User::destroy($akunId); // Hapus data akun
            });
            session()->flash('message', 'Data Guru berhasil dihapus.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus: Guru ini mungkin masih terhubung dengan data kelas.');
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
        $this->guruId = null;
        $this->akunId = null;
    }

    public function render()
    {
        // Ambil data guru dengan relasi 'akun' dan pencarian
        $query = Guru::with('akun')
            ->whereHas('akun', function($q) {
                $q->where('nama_lengkap', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%');
            })
            ->orderBy('id_guru', 'desc');

        return view('livewire.admin.kelola-guru', [
            'daftarGuru' => $query->paginate(10),
        ])->layout('layouts.app');
    }
}