<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Kelas; 
use Livewire\WithPagination; 

class KelolaKelas extends Component
{
    use WithPagination; 

    // Properti untuk form
    public $nama_kelas;
    public $tahun_ajaran;

    // Properti untuk UI
    public $showModal = false;
    public $editMode = false;
    public $kelasId;

    // Aturan validasi
    protected $rules = [
        'nama_kelas' => 'required|string|max:255',
        'tahun_ajaran' => 'required|string|max:10',
    ];

    // Fungsi untuk menyimpan (Create & Update)
    public function simpan()
    {
        $this->validate(); 

        Kelas::updateOrCreate(
            ['id_kelas' => $this->kelasId], 
            [
                'nama_kelas' => $this->nama_kelas,
                'tahun_ajaran' => $this->tahun_ajaran,
                // 'id_guru' bisa ditambahkan nanti
            ]
        );

        $this->closeModal(); // Tutup modal
        session()->flash('message', $this->editMode ? 'Kelas berhasil diperbarui.' : 'Kelas berhasil ditambahkan.');
    }

    // Fungsi untuk membuka modal tambah
    public function tambah()
    {
        $this->resetForm();
        $this->editMode = false;
        $this->showModal = true;
    }

    // Fungsi untuk membuka modal edit
    public function edit($id)
    {
        $kelas = Kelas::findOrFail($id);
        $this->kelasId = $id;
        $this->nama_kelas = $kelas->nama_kelas;
        $this->tahun_ajaran = $kelas->tahun_ajaran;
        
        $this->editMode = true;
        $this->showModal = true;
    }

    // Fungsi untuk menghapus
    public function hapus($id)
    {
        Kelas::destroy($id);
        session()->flash('message', 'Kelas berhasil dihapus.');
    }

    // Fungsi untuk menutup modal
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    // Fungsi untuk reset form
    public function resetForm()
    {
        $this->nama_kelas = '';
        $this->tahun_ajaran = '';
        $this->kelasId = null;
    }

    // Fungsi 'render' adalah yang utama untuk menampilkan view
    public function render()
    {
        return view('livewire.admin.kelola-kelas', [
            // 4. Kirim data kelas ke view dengan pagination
            'daftarKelas' => Kelas::paginate(10) 
        ])->layout('layouts.app'); 
    }
}