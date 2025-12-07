<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\OrangTua;
use App\Imports\SiswaImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TemplateSiswaExport;

class KelolaSiswa extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $search = '';
    public $showModal = false;
    public $editMode = false;

    // Form fields
    public $siswaId;
    public $nama_siswa;
    public $kode_siswa;
    public $id_kelas;
    public $id_ortu;

    // Import fields
    public $importFile;
    public $showImportModal = false;

    protected $rules = [
        'nama_siswa' => 'required|min:3',
        'kode_siswa' => 'required|unique:siswa,kode_siswa',
        'id_kelas' => 'required|exists:kelas,id_kelas',
        'id_ortu' => 'nullable|exists:orang_tua,id_ortu',
    ];

    protected $messages = [
        'nama_siswa.required' => 'Nama siswa harus diisi',
        'nama_siswa.min' => 'Nama siswa minimal 3 karakter',
        'kode_siswa.required' => 'Kode siswa harus diisi',
        'kode_siswa.unique' => 'Kode siswa sudah digunakan',
        'id_kelas.required' => 'Kelas harus dipilih',
        'id_kelas.exists' => 'Kelas tidak valid',
    ];

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
        $this->siswaId = null;
        $this->nama_siswa = '';
        $this->kode_siswa = '';
        $this->id_kelas = '';
        $this->id_ortu = '';
        $this->editMode = false;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function store()
    {
        $this->validate();

        try {
            Siswa::create([
                'nama_siswa' => $this->nama_siswa,
                'kode_siswa' => $this->kode_siswa,
                'id_kelas' => $this->id_kelas,
                'id_ortu' => $this->id_ortu,
            ]);

            session()->flash('message', 'Data siswa berhasil ditambahkan.');
            $this->closeModal();
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menambahkan data: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $siswa = Siswa::findOrFail($id);

            $this->siswaId = $siswa->id_siswa;
            $this->nama_siswa = $siswa->nama_siswa;
            $this->kode_siswa = $siswa->kode_siswa;
            $this->id_kelas = $siswa->id_kelas;
            $this->id_ortu = $siswa->id_ortu;

            $this->editMode = true;
            $this->showModal = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memuat data siswa: ' . $e->getMessage());
        }
    }

    public function update()
    {
        $this->validate([
            'nama_siswa' => 'required|min:3',
            'kode_siswa' => 'required|unique:siswa,kode_siswa,' . $this->siswaId . ',id_siswa',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'id_ortu' => 'nullable|exists:orang_tua,id_ortu',
        ]);

        try {
            $siswa = Siswa::findOrFail($this->siswaId);

            $siswa->update([
                'nama_siswa' => $this->nama_siswa,
                'kode_siswa' => $this->kode_siswa,
                'id_kelas' => $this->id_kelas,
                'id_ortu' => $this->id_ortu,
            ]);

            session()->flash('message', 'Data siswa berhasil diperbarui.');
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $siswa = Siswa::findOrFail($id);
            $siswa->delete();
            session()->flash('message', 'Data siswa berhasil dihapus.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        return Excel::download(new TemplateSiswaExport, 'template_siswa.xlsx');
    }

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
            Excel::import(new SiswaImport, $this->importFile);
            session()->flash('message', '✅ Berhasil import data siswa!');

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
        $siswa = Siswa::with(['kelas', 'ortu.akun'])
            ->where(function ($query) {
                $query->where('nama_siswa', 'like', '%' . $this->search . '%')
                    ->orWhere('kode_siswa', 'like', '%' . $this->search . '%');
            })
            ->paginate(10);

        $kelasList = Kelas::all();
        $orangTuaList = OrangTua::with('akun')->get();

        return view('livewire.admin.kelola-siswa', [
            'siswaList' => $siswa,
            'kelasList' => $kelasList,
            'orangTuaList' => $orangTuaList,
        ])->layout('layouts.app');
    }
}