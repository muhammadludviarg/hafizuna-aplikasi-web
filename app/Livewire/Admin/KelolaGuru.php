<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Imports\GuruImport;
use Maatwebsite\Excel\Facades\Excel;

class KelolaGuru extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $search = '';
    public $showModal = false;
    public $editMode = false;
    
    // Form fields
    public $guruId;
    public $id_akun;
    public $no_hp;
    
    // Field untuk akun (jika create new)
    public $nama_lengkap;
    public $email;

    // Import fields
    public $importFile;
    public $showImportModal = false;

    protected function rules()
    {
        $rules = [
            'no_hp' => 'required|string|max:20',
            'nama_lengkap' => 'required|min:3',
        ];

        if ($this->editMode) {
            // Edit mode - validasi unique kecuali record ini
            $rules['email'] = 'required|email|unique:akun,email,' . $this->id_akun . ',id_akun';
        } else {
            // Create mode - validasi unique total
            $rules['email'] = 'required|email|unique:akun,email';
        }

        return $rules;
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
        $this->guruId = null;
        $this->id_akun = '';
        $this->no_hp = '';
        $this->nama_lengkap = '';
        $this->email = '';
        $this->editMode = false;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function store()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            // Buat akun dulu (sesuai struktur tabel akun yang ada)
            $akun = User::create([
                'nama_lengkap' => $this->nama_lengkap,
                'email' => $this->email,
                'sandi_hash' => bcrypt('password123'), // Kolom password di tabel Anda namanya sandi_hash
                'status' => 1, // Aktif
            ]);

            // Buat guru
            Guru::create([
                'id_akun' => $akun->id_akun, // Primary key tabel akun adalah id_akun
                'no_hp' => $this->no_hp,
            ]);

            DB::commit();

            session()->flash('message', 'Data guru berhasil ditambahkan. Password default: password123');
            $this->closeModal();
            $this->resetPage();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menambahkan data: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $guru = Guru::with('akun')->findOrFail($id);
            
            $this->guruId = $guru->id_guru;
            $this->id_akun = $guru->id_akun;
            $this->no_hp = $guru->no_hp;
            
            // Load data akun
            if ($guru->akun) {
                $this->nama_lengkap = $guru->akun->nama_lengkap;
                $this->email = $guru->akun->email;
            }
            
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
            DB::beginTransaction();

            $guru = Guru::findOrFail($this->guruId);

            // Update guru
            $guru->update([
                'no_hp' => $this->no_hp,
            ]);

            // Update akun
            if ($guru->akun) {
                $guru->akun->update([
                    'nama_lengkap' => $this->nama_lengkap,
                    'email' => $this->email,
                ]);
            }

            DB::commit();

            session()->flash('message', 'Data guru berhasil diperbarui.');
            $this->closeModal();
            $this->resetPage();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();

            $guru = Guru::with('akun')->findOrFail($id);
            
            // Simpan ID akun sebelum hapus guru
            $id_akun = $guru->id_akun;
            
            // Hapus data guru dulu
            $guru->delete();
            
            // Hapus akun juga (CASCADE DELETE)
            if ($id_akun) {
                User::where('id_akun', $id_akun)->delete();
            }

            DB::commit();

            session()->flash('message', 'Data guru dan akun berhasil dihapus.');
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
            Excel::import(new GuruImport, $this->importFile);
            session()->flash('message', '✅ Berhasil import data guru!');
            
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
        $guru = Guru::with('akun')
            ->when($this->search, function($query) {
                $query->where('no_hp', 'like', '%' . $this->search . '%')
                    ->orWhereHas('akun', function($q) {
                        $q->where('nama_lengkap', 'like', '%' . $this->search . '%')
                          ->orWhere('email', 'like', '%' . $this->search . '%');
                    });
            })
            ->paginate(10);

        return view('livewire.admin.kelola-guru', [
            'guruList' => $guru,
        ])->layout('layouts.app');
    }
}