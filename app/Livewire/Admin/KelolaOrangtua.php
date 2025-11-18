<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\OrangTua;
use App\Models\User;

class KelolaOrangTua extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editMode = false;
    
    // Form fields
    public $ortuId;
    public $id_akun;
    public $no_hp;
    
    // Field untuk akun (jika create new)
    public $nama_lengkap;
    public $email;

    protected function rules()
    {
        $rules = [
            'no_hp' => 'required|string|max:20',
        ];

        if ($this->editMode) {
            $rules['id_akun'] = 'required|exists:akun,id';
        } else {
            // Untuk tambah baru, butuh data akun
            $rules['nama_lengkap'] = 'required|min:3';
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
        $this->ortuId = null;
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
            // Buat akun dulu
            $akun = User::create([
                'nama_lengkap' => $this->nama_lengkap,
                'email' => $this->email,
                'password' => bcrypt('password123'), // Default password
                'role' => 'ortu',
            ]);

            // Buat orang tua
            OrangTua::create([
                'id_akun' => $akun->id,
                'no_hp' => $this->no_hp,
            ]);

            session()->flash('message', 'Data orang tua berhasil ditambahkan. Password default: password123');
            $this->closeModal();
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menambahkan data: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $ortu = OrangTua::with('akun')->findOrFail($id);
            
            $this->ortuId = $ortu->id_ortu;
            $this->id_akun = $ortu->id_akun;
            $this->no_hp = $ortu->no_hp;
            
            // Load data akun
            if ($ortu->akun) {
                $this->nama_lengkap = $ortu->akun->nama_lengkap;
                $this->email = $ortu->akun->email;
            }
            
            $this->editMode = true;
            $this->showModal = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memuat data: ' . $e->getMessage());
        }
    }

    public function update()
    {
        $this->validate([
            'no_hp' => 'required|string|max:20',
            'nama_lengkap' => 'required|min:3',
            'email' => 'required|email|unique:akun,email,' . $this->id_akun,
        ]);

        try {
            $ortu = OrangTua::findOrFail($this->ortuId);

            // Update orang tua
            $ortu->update([
                'no_hp' => $this->no_hp,
            ]);

            // Update akun
            if ($ortu->akun) {
                $ortu->akun->update([
                    'nama_lengkap' => $this->nama_lengkap,
                    'email' => $this->email,
                ]);
            }

            session()->flash('message', 'Data orang tua berhasil diperbarui.');
            $this->closeModal();
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $ortu = OrangTua::findOrFail($id);
            
            // Check apakah masih punya siswa
            if ($ortu->siswa && $ortu->siswa->count() > 0) {
                session()->flash('error', 'Tidak dapat menghapus orang tua yang masih memiliki siswa terdaftar.');
                return;
            }
            
            $ortu->delete();
            session()->flash('message', 'Data orang tua berhasil dihapus.');
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $orangTua = OrangTua::with(['akun', 'siswa'])
            ->whereHas('akun', function($query) {
                $query->where('nama_lengkap', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->orWhere('no_hp', 'like', '%' . $this->search . '%')
            ->paginate(10);

        return view('livewire.admin.kelola-orang-tua', [
            'orangTuaList' => $orangTua,
        ])->layout('layouts.app');
    }
}