<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Kelas;
use App\Imports\KelasImport;
use Maatwebsite\Excel\Facades\Excel;

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
            
            // Check apakah ada siswa di kelas ini
            if ($kelas->siswa && $kelas->siswa->count() > 0) {
                session()->flash('error', 'Tidak dapat menghapus kelas yang masih memiliki siswa.');
                return;
            }
            
            $kelas->delete();
            session()->flash('message', 'Data kelas berhasil dihapus.');
            $this->resetPage();
        } catch (\Exception $e) {
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
            
            // TAMBAHKAN INI:
            $this->showImportModal = false;  // Tutup modal
            $this->importFile = null;         // Reset file
            $this->dispatch('import-success'); // Trigger event
            
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', '❌ Gagal import: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $kelas = Kelas::withCount('siswa')
            ->where(function($query) {
                $query->where('nama_kelas', 'like', '%' . $this->search . '%')
                      ->orWhere('tahun_ajaran', 'like', '%' . $this->search . '%');
            })
            ->paginate(10);

        return view('livewire.admin.kelola-kelas', [
            'kelasList' => $kelas,
        ])->layout('layouts.app');
    }
}