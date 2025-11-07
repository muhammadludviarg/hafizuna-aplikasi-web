<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Siswa; // Kita pakai Model Siswa
use App\Models\Kelas; // Kita butuh data Kelas untuk dropdown
use Livewire\WithPagination;

class KelolaSiswa extends Component
{
    use WithPagination;

    // Properti untuk form
    public $nama_siswa;
    public $kode_siswa;
    public $id_kelas;

    // Properti untuk UI
    public $showModal = false;
    public $editMode = false;
    public $siswaId;
    public $search = ''; // Untuk pencarian

    // Aturan validasi
    protected function rules()
    {
        // Aturan 'unique' perlu diperbarui saat mode edit
        $kodeSiswaRules = 'nullable|string|max:50|unique:siswa,kode_siswa';
        if ($this->editMode) {
            $kodeSiswaRules .= ',' . $this->siswaId . ',id_siswa';
        }

        return [
            'nama_siswa' => 'required|string|max:255',
            'kode_siswa' => $kodeSiswaRules,
            'id_kelas' => 'required|exists:kelas,id_kelas',
        ];
    }

    // Fungsi untuk menyimpan (Create & Update)
    public function simpan()
    {
        $this->validate();

        Siswa::updateOrCreate(
            ['id_siswa' => $this->siswaId],
            [
                'nama_siswa' => $this->nama_siswa,
                'kode_siswa' => $this->kode_siswa,
                'id_kelas' => $this->id_kelas,
            ]
        );

        $this->closeModal();
        session()->flash('message', $this->editMode ? 'Data Siswa berhasil diperbarui.' : 'Data Siswa berhasil ditambahkan.');
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
        $siswa = Siswa::findOrFail($id);
        $this->siswaId = $id;
        $this->nama_siswa = $siswa->nama_siswa;
        $this->kode_siswa = $siswa->kode_siswa;
        $this->id_kelas = $siswa->id_kelas;
        
        $this->editMode = true;
        $this->showModal = true;
    }

    // Fungsi untuk menghapus
    public function hapus($id)
    {
        Siswa::destroy($id);
        session()->flash('message', 'Data Siswa berhasil dihapus.');
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
        $this->nama_siswa = '';
        $this->kode_siswa = '';
        $this->id_kelas = '';
        $this->siswaId = null;
    }

    public function render()
    {
        // Ambil semua kelas untuk dropdown
        $daftarKelas = Kelas::orderBy('nama_kelas')->get();

        // Ambil data siswa dengan pencarian & relasi
        $query = Siswa::with('kelas') // 'kelas' adalah nama fungsi relasi di Model Siswa
            ->where('nama_siswa', 'like', '%'.$this->search.'%')
            ->orderBy('nama_siswa');

        return view('livewire.admin.kelola-siswa', [
            'daftarSiswa' => $query->paginate(10),
            'semuaKelas' => $daftarKelas,
        ])->layout('layouts.app');
    }
}
