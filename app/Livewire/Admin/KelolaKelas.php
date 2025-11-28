<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Kelas;
use App\Imports\KelasImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TemplateKelasExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KelolaKelas extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $search = '';
    public $showModal = false;
    public $editMode = false;

    // Form fields
    public $kelasId;
    public $nama_kelas;
    public $tahun_ajaran;

    // Import fields
    public $importFile;
    public $showImportModal = false;

    protected function rules()
    {
        return [
            'nama_kelas' => 'required|min:2',
            'tahun_ajaran' => 'required|string',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->kelasId = null;
        $this->nama_kelas = '';
        $this->tahun_ajaran = '';
        $this->editMode = false;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function store()
    {
        $this->validate();

        try {
            Kelas::create([
                'nama_kelas' => $this->nama_kelas,
                'tahun_ajaran' => $this->tahun_ajaran,
            ]);

            session()->flash('message', 'Data kelas berhasil ditambahkan.');
            $this->closeModal();
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menambahkan data: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $kelas = Kelas::findOrFail($id);

            $this->kelasId = $kelas->id_kelas;
            $this->nama_kelas = $kelas->nama_kelas;
            $this->tahun_ajaran = $kelas->tahun_ajaran;

            $this->editMode = true;
            $this->showModal = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memuat data: ' . $e->getMessage());
        }
    }

    public function update()
    {
        $this->validate();

        try {
            $kelas = Kelas::findOrFail($this->kelasId);

            $kelas->update([
                'nama_kelas' => $this->nama_kelas,
                'tahun_ajaran' => $this->tahun_ajaran,
            ]);

            session()->flash('message', 'Data kelas berhasil diperbarui.');
            $this->closeModal();
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $kelas = Kelas::findOrFail($id);

            // 1. CEK SISWA (PROTEKSI)
            // Kita tidak ingin siswa tiba-tiba "hilang kelasnya" tanpa sepengetahuan admin
            $jumlahSiswa = DB::table('siswa')->where('id_kelas', $id)->count();

            if ($jumlahSiswa > 0) {
                session()->flash('error', "Gagal: Kelas <strong>{$kelas->nama_kelas}</strong> masih memiliki <strong>$jumlahSiswa siswa</strong> aktif. Harap pindahkan atau keluarkan siswa terlebih dahulu.");
                return;
            }

            DB::beginTransaction();

            // 2. Hapus Kelompok Mengaji terkait (Pembersihan)
            // Karena kelas dihapus, kelompok di dalamnya juga harus dihapus
            DB::table('kelompok')->where('id_kelas', $id)->delete();

            // 3. Hapus Kelas
            $kelas->delete();

            DB::commit();

            session()->flash('message', 'Data kelas berhasil dihapus.');
            $this->dispatch('modal-closed');
            $this->resetPage();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    // ==========================================
    // IMPORT FUNCTIONS
    // ==========================================

    public function openImportModal()
    {
        $this->showImportModal = true;
    }

    public function closeImportModal()
    {
        $this->showImportModal = false;
        $this->importFile = null;
    }

    public function import()
    {
        $this->validate([
            'importFile' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            Excel::import(new KelasImport, $this->importFile);
            session()->flash('message', '✅ Berhasil import data kelas!');

            $this->showImportModal = false;
            $this->importFile = null;
            $this->dispatch('import-success');

            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', '❌ Gagal import: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        return Excel::download(new TemplateKelasExport, 'template_kelas.xlsx');
    }

    public function render()
    {
        $kelas = Kelas::withCount('siswa')
            ->where(function ($query) {
                $query->where('nama_kelas', 'like', '%' . $this->search . '%')
                    ->orWhere('tahun_ajaran', 'like', '%' . $this->search . '%');
            })
            ->paginate(10);

        return view('livewire.admin.kelola-kelas', [
            'kelasList' => $kelas,
        ])->layout('layouts.app');
    }
}