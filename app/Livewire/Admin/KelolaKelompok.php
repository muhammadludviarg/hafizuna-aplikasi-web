<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Kelompok;
use App\Models\Kelas;
use App\Models\Guru;
use App\Models\Siswa;

use Illuminate\Support\Facades\DB;

class KelolaKelompok extends Component
{
    public $search = '';

    // Form fields
    public $nama_kelompok;
    public $id_kelas;
    public $id_guru;
    public $siswa_dipilih = [];
    public $tgl_mulai;      
    public $tgl_selesai;    

    // State management
    public $isModalOpen = false;
    public $isEditMode = false;
    public $kelompok_id;

    // Dropdown data
    public $daftar_kelas = [];
    public $daftar_guru = [];
    public $daftar_siswa = [];

    // Validation rules
    protected $rules = [
        'nama_kelompok' => 'required|string|max:100',
        'id_kelas' => 'required|exists:kelas,id_kelas',
        'id_guru' => 'required|exists:guru,id_guru',
        'siswa_dipilih' => 'required|array|min:1',
        'siswa_dipilih.*' => 'exists:siswa,id_siswa',
        'tgl_mulai' => 'required|date',        
        'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai', 
    ];

    protected $messages = [
        'nama_kelompok.required' => 'Nama kelompok wajib diisi',
        'id_kelas.required' => 'Kelas wajib dipilih',
        'id_guru.required' => 'Guru pembimbing wajib dipilih',
        'siswa_dipilih.required' => 'Minimal pilih 1 siswa',
        'siswa_dipilih.min' => 'Minimal pilih 1 siswa',
        'tgl_mulai.required' => 'Tanggal mulai wajib diisi',
        'tgl_mulai.date' => 'Format tanggal mulai tidak valid',
        'tgl_selesai.required' => 'Tanggal selesai wajib diisi',
        'tgl_selesai.date' => 'Format tanggal selesai tidak valid',
        'tgl_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai',
    ];

    public function mount()
    {
        $this->loadKelas();
        $this->loadGuru();
    }

    public function loadKelas()
    {
        $this->daftar_kelas = Kelas::orderBy('nama_kelas')->get();
    }

    public function loadGuru()
    {
        $this->daftar_guru = Guru::with('akun')->get();
    }

    public function updatedIdKelas($value)
    {
        if ($value) {
            // Load SEMUA siswa di kelas ini (dengan info kelompok mereka)
            $this->daftar_siswa = Siswa::where('id_kelas', $value)
                ->with(['kelompok' => function($query) {
                    // Load kelompok aktif (yang bukan kelompok yang sedang diedit)
                    if ($this->isEditMode && $this->kelompok_id) {
                        $query->where('siswa_kelompok.id_kelompok', '!=', $this->kelompok_id);
                    }
                }])
                ->orderBy('nama_siswa')
                ->get();
        } else {
            $this->daftar_siswa = [];
        }
        
        if (!$this->isEditMode) {
            $this->siswa_dipilih = [];
        }
    }

    public function buatKelompok()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->isModalOpen = true;
    }

    public function editKelompok($id)
    {
        $kelompok = Kelompok::with(['siswa'])->findOrFail($id);
        
        $this->kelompok_id = $kelompok->id_kelompok;
        $this->nama_kelompok = $kelompok->nama_kelompok;
        $this->id_kelas = $kelompok->id_kelas;
        $this->id_guru = $kelompok->id_guru;
        
        $this->updatedIdKelas($this->id_kelas);
        $this->siswa_dipilih = $kelompok->siswa->pluck('id_siswa')->toArray();
        
        // Ambil tanggal dari pivot table (jika ada)
        $pivotData = $kelompok->siswa->first()?->pivot;
        
        if ($pivotData && $pivotData->tgl_mulai) {
            $this->tgl_mulai = \Carbon\Carbon::parse($pivotData->tgl_mulai)->format('Y-m-d');
        }
        
        if ($pivotData && $pivotData->tgl_selesai) {
            $this->tgl_selesai = \Carbon\Carbon::parse($pivotData->tgl_selesai)->format('Y-m-d');
        }
        
        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    public function simpanKelompok()
    {
        $this->validate();

        DB::beginTransaction();
        
        try {
            if ($this->isEditMode) {
                // UPDATE
                $kelompok = Kelompok::findOrFail($this->kelompok_id);
                $kelompok->update([
                    'nama_kelompok' => $this->nama_kelompok,
                    'id_kelas' => $this->id_kelas,
                    'id_guru' => $this->id_guru,
                ]);

                // PINDAHKAN SISWA: Detach dari kelompok lain, attach ke kelompok ini
                $this->pindahkanSiswa($kelompok);

                session()->flash('success', 'Kelompok berhasil diperbarui!');
                
            } else {
                // CREATE
                $kelompok = Kelompok::create([
                    'nama_kelompok' => $this->nama_kelompok,
                    'id_kelas' => $this->id_kelas,
                    'id_guru' => $this->id_guru,
                ]);

                // PINDAHKAN SISWA: Detach dari kelompok lain, attach ke kelompok baru
                $this->pindahkanSiswa($kelompok);

                session()->flash('success', 'Kelompok berhasil dibuat!');
            }

            DB::commit();
            $this->closeModal();
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function pindahkanSiswa($kelompok)
    {
        $attachData = [];
        
        foreach ($this->siswa_dipilih as $id_siswa) {
            // Detach siswa dari SEMUA kelompok lain terlebih dahulu
            DB::table('siswa_kelompok')
                ->where('id_siswa', $id_siswa)
                ->where('id_kelompok', '!=', $kelompok->id_kelompok)
                ->delete();
            
            // Siapkan data untuk attach dengan tanggal
            $attachData[$id_siswa] = [
                'tgl_mulai' => $this->tgl_mulai,
                'tgl_selesai' => $this->tgl_selesai,
            ];
        }
        
        // Sync siswa ke kelompok ini
        $kelompok->siswa()->sync($attachData);
    }

    public function hapusKelompok($id)
    {
        DB::beginTransaction();
        
        try {
            $kelompok = Kelompok::findOrFail($id);
            $kelompok->siswa()->detach();
            $kelompok->delete();
            
            DB::commit();
            session()->flash('success', 'Kelompok berhasil dihapus!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
        $this->resetValidation();
    }

    private function resetForm()
    {
        $this->kelompok_id = null;
        $this->nama_kelompok = '';
        $this->id_kelas = null;
        $this->id_guru = null;
        $this->siswa_dipilih = [];
        $this->daftar_siswa = [];
        $this->tgl_mulai = null;
        $this->tgl_selesai = null;
    }

    public function resetFilter()
    {
        $this->search = '';
        $this->filterKelas = '';
        $this->filterGuru = '';
    }

    public function render()
    {
        $query = Kelompok::with(['kelas', 'guru.akun', 'siswa'])
            ->orderBy('nama_kelompok');

        // Search universal: nama kelompok, kelas, atau guru
        if ($this->search) {
            $query->where(function($q) {
                // Cari di nama kelompok
                $q->where('nama_kelompok', 'like', '%' . $this->search . '%')
                // Cari di nama kelas
                ->orWhereHas('kelas', function($subQuery) {
                    $subQuery->where('nama_kelas', 'like', '%' . $this->search . '%');
                })
                // Cari di nama guru (dari tabel akun)
                ->orWhereHas('guru.akun', function($subQuery) {
                    $subQuery->where('nama_lengkap', 'like', '%' . $this->search . '%');
                });
            });
        }

        $kelompok = $query->get();

        return view('livewire.admin.kelola-kelompok', [
            'kelompok' => $kelompok
        ])->layout('layouts.app');
    }
}