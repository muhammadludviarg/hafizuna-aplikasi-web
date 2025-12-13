<?php

namespace App\Livewire\Admin;

use App\Models\Notifikasi;
use App\Models\Siswa;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\OrangTua;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Imports\OrangTuaImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TemplateOrangTuaExport;

class KelolaOrangTua extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $search = '';
    public $showModal = false;
    public $editMode = false;

    // Form fields
    public $ortuId;
    public $id_akun;
    public $no_hp;

    // Field untuk akun
    public $nama_lengkap;
    public $email;

    // Error message untuk ditampilkan di dalam modal
    public $modalError = '';

    // Import fields
    public $importFile;
    public $showImportModal = false;

    protected function rules()
    {
        $rules = [
            'no_hp' => 'nullable|string|max:20',
            'nama_lengkap' => 'required|min:3',
        ];

        if ($this->editMode && $this->id_akun) {
            // Edit mode - validasi unique kecuali record ini
            $rules['email'] = 'required|email|unique:akun,email,' . $this->id_akun . ',id_akun';
        } else {
            // Create mode - validasi unique total
            $rules['email'] = 'required|email|unique:akun,email';
        }

        return $rules;
    }

    protected $messages = [
        'nama_lengkap.required' => 'Nama lengkap wajib diisi',
        'nama_lengkap.min' => 'Nama lengkap minimal 3 karakter',
        'email.required' => 'Email wajib diisi',
        'email.email' => 'Format email tidak valid',
        'email.unique' => 'Email sudah terdaftar',
        'no_hp.max' => 'No HP maksimal 20 karakter',
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
        $this->ortuId = null;
        $this->id_akun = null;
        $this->no_hp = '';
        $this->nama_lengkap = '';
        $this->email = '';
        $this->editMode = false;
        $this->modalError = '';
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function store()
    {
        $this->modalError = '';

        $this->validate();

        try {
            DB::beginTransaction();

            Log::info('Creating orang tua', [
                'nama_lengkap' => $this->nama_lengkap,
                'email' => $this->email,
                'no_hp' => $this->no_hp,
            ]);

            // 1. Buat akun dulu
            $akun = User::create([
                'nama_lengkap' => $this->nama_lengkap,
                'email' => $this->email,
                'sandi_hash' => bcrypt('password123'),
                'status' => 1,
            ]);

            Log::info('Akun created', ['id_akun' => $akun->id_akun]);

            // 2. Buat orang tua
            $ortu = OrangTua::create([
                'id_akun' => $akun->id_akun,
                'no_hp' => $this->no_hp,
            ]);

            Log::info('Orang tua created', ['id_ortu' => $ortu->id_ortu]);

            DB::commit();

            session()->flash('message', 'Data orang tua berhasil ditambahkan. Password default: password123. Login menggunakan email.');
            $this->closeModal();
            $this->resetPage();

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();

            Log::error('Database error creating orang tua', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);

            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $this->modalError = 'Email sudah terdaftar. Silakan gunakan email lain.';
            } else {
                $this->modalError = 'Gagal menyimpan: ' . $e->getMessage();
            }

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error creating orang tua', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            $this->modalError = 'Terjadi kesalahan: ' . $e->getMessage();
        }
    }

    public function edit($id)
    {
        $this->modalError = '';

        try {
            Log::info('Editing orang tua', ['id_ortu' => $id]);

            $ortu = OrangTua::with('akun')->find($id);

            if (!$ortu) {
                session()->flash('error', 'Data orang tua tidak ditemukan.');
                return;
            }

            $this->ortuId = $ortu->id_ortu;
            $this->id_akun = $ortu->id_akun;
            $this->no_hp = $ortu->no_hp ?? '';

            if ($ortu->akun) {
                $this->nama_lengkap = $ortu->akun->nama_lengkap;
                $this->email = $ortu->akun->email;

                Log::info('Orang tua loaded for edit', [
                    'id_ortu' => $this->ortuId,
                    'id_akun' => $this->id_akun,
                    'nama' => $this->nama_lengkap,
                ]);
            } else {
                Log::warning('Orang tua has no akun', ['id_ortu' => $id]);
                $this->modalError = 'Data akun tidak ditemukan untuk orang tua ini.';
            }

            $this->editMode = true;
            $this->showModal = true;

        } catch (\Exception $e) {
            Log::error('Error loading orang tua for edit', [
                'id_ortu' => $id,
                'error' => $e->getMessage(),
            ]);

            session()->flash('error', 'Gagal memuat data: ' . $e->getMessage());
        }
    }

    public function update()
    {
        $this->modalError = '';

        $this->validate();

        try {
            DB::beginTransaction();

            Log::info('Updating orang tua', [
                'id_ortu' => $this->ortuId,
                'id_akun' => $this->id_akun,
            ]);

            $ortu = OrangTua::find($this->ortuId);

            if (!$ortu) {
                throw new \Exception('Data orang tua tidak ditemukan.');
            }

            // Update orang tua
            $ortu->update([
                'no_hp' => $this->no_hp,
            ]);

            Log::info('Orang tua updated', ['id_ortu' => $ortu->id_ortu]);

            // Update akun
            if ($ortu->akun) {
                $ortu->akun->update([
                    'nama_lengkap' => $this->nama_lengkap,
                    'email' => $this->email,
                ]);

                Log::info('Akun updated', ['id_akun' => $ortu->id_akun]);
            } else {
                Log::warning('No akun to update', ['id_ortu' => $ortu->id_ortu]);
            }

            DB::commit();

            session()->flash('message', 'Data orang tua berhasil diperbarui.');
            $this->closeModal();
            $this->resetPage();

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();

            Log::error('Database error updating orang tua', [
                'id_ortu' => $this->ortuId,
                'error' => $e->getMessage(),
            ]);

            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $this->modalError = 'Email sudah digunakan oleh orang tua lain.';
            } else {
                $this->modalError = 'Gagal memperbarui: ' . $e->getMessage();
            }

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error updating orang tua', [
                'id_ortu' => $this->ortuId,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);

            $this->modalError = 'Terjadi kesalahan: ' . $e->getMessage();
        }
    }

    public function delete($id)
    {
        try {
            $ortu = OrangTua::with('akun')->findOrFail($id);

            // 1. CEK RELASI SISWA (PROTEKSI UTAMA)
            // Kita gunakan count() untuk memastikan
            $jumlahAnak = DB::table('siswa')->where('id_ortu', $id)->count();

            if ($jumlahAnak > 0) {
                session()->flash('error', "Gagal: Orang Tua ini masih terdata memiliki $jumlahAnak siswa. Hapus data siswa terlebih dahulu.");
                return;
            }

            DB::beginTransaction();

            $id_akun = $ortu->id_akun;

            // 2. BERSIHKAN NOTIFIKASI (PENTING: Ini sering jadi penyebab gagal hapus)
            DB::table('notifikasi')->where('id_ortu', $id)->delete();

            // 3. FORCE UNLINK SISWA (JAGA-JAGA)
            // Jika ada data siswa yang 'ghost' (tidak terdeteksi di count awal karena cache/lainnya)
            // kita paksa set id_ortu jadi NULL biar tidak error foreign key.
            DB::table('siswa')->where('id_ortu', $id)->update(['id_ortu' => null]);

            // 4. HAPUS DATA ORANG TUA
            $ortu->delete();

            // 5. HAPUS AKUN LOGIN (HATI-HATI)
            if ($id_akun) {
                // Cek apakah akun ini dipakai juga di tabel 'Guru' (Double Role)
                $isGuru = DB::table('guru')->where('id_akun', $id_akun)->exists();

                if (!$isGuru) {
                    // Jika bukan guru, aman untuk dihapus total
                    DB::table('log_aktivitas')->where('id_akun', $id_akun)->delete(); // Bersihkan log
                    User::where('id_akun', $id_akun)->delete(); // Hapus user
                } else {
                    // Jika ternyata dia juga Guru, jangan hapus akunnya, cuma data ortunya saja
                    Log::info('Akun tidak dihapus karena juga terdaftar sebagai Guru', ['id_akun' => $id_akun]);
                }
            }

            DB::commit();

            session()->flash('message', 'Data orang tua berhasil dihapus.');
            $this->resetPage();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal hapus ortu: ' . $e->getMessage());
            session()->flash('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    // ==========================================
    // IMPORT FUNCTIONS
    // ==========================================

    public function downloadTemplate()
    {
        return Excel::download(new TemplateOrangTuaExport, 'template_orang_tua.xlsx');
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
            Excel::import(new OrangTuaImport, $this->importFile);
            session()->flash('message', '✅ Berhasil import data orang tua!');

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
        $orangTua = OrangTua::with(['akun', 'siswa'])
            ->when($this->search, function ($query) {
                $query->where('no_hp', 'like', '%' . $this->search . '%')
                    ->orWhereHas('akun', function ($q) {
                        $q->where('nama_lengkap', 'like', '%' . $this->search . '%')
                            ->orWhere('email', 'like', '%' . $this->search . '%');
                    });
            })
            ->orderBy('id_ortu', 'asc')
            ->paginate(10);

        return view('livewire.admin.kelola-orang-tua', [
            'orangTuaList' => $orangTua,
        ])->layout('layouts.app');
    }
}