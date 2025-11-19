<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use App\Models\Kelompok;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ManajemenKelompok extends Component
{
    // Search
    public $search = '';
    
    // Form fields
    public $kelompok_id;
    public $nama_kelompok;
    public $id_kelas;
    public $id_guru;
    public $siswa_dipilih = [];
    public $tgl_mulai;
    public $tgl_selesai;

    // State management
    public $isModalOpen = false;

    // Dropdown data
    public $daftar_siswa = [];

    protected $rules = [
        'siswa_dipilih' => 'required|array|min:1',
        'siswa_dipilih.*' => 'exists:siswa,id_siswa',
        'tgl_mulai' => 'required|date',
        'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
    ];

    protected $messages = [
        'siswa_dipilih.required' => 'Minimal pilih 1 siswa',
        'siswa_dipilih.min' => 'Minimal pilih 1 siswa',
        'tgl_mulai.required' => 'Tanggal mulai wajib diisi',
        'tgl_selesai.required' => 'Tanggal selesai wajib diisi',
        'tgl_selesai.after_or_equal' => 'Tanggal selesai harus >= tanggal mulai',
    ];

    /**
     * Edit kelompok (pindahkan siswa)
     */
    public function editKelompok($id)
    {
        $kelompok = Kelompok::with(['siswa', 'kelas'])->findOrFail($id);
        
        // ✅ Cek apakah kelompok ini milik guru yang login
        if ($kelompok->id_guru != Auth::user()->guru->id_guru) {
            session()->flash('error', 'Anda tidak memiliki akses ke kelompok ini!');
            return;
        }
        
        $this->kelompok_id = $kelompok->id_kelompok;
        $this->nama_kelompok = $kelompok->nama_kelompok;
        $this->id_kelas = $kelompok->id_kelas;
        $this->id_guru = $kelompok->id_guru;
        
        // Load semua siswa di kelas ini (dengan info kelompok mereka)
        $this->daftar_siswa = Siswa::where('id_kelas', $this->id_kelas)
            ->with(['kelompok' => function($query) {
                $query->where('siswa_kelompok.id_kelompok', '!=', $this->kelompok_id);
            }])
            ->orderBy('nama_siswa')
            ->get();
        
        // Set siswa yang sudah terpilih
        $this->siswa_dipilih = $kelompok->siswa->pluck('id_siswa')->toArray();
        
        // Set tanggal
        $pivotData = $kelompok->siswa->first()?->pivot;
        $this->tgl_mulai = $pivotData?->tgl_mulai ?? now()->format('Y-m-d');
        $this->tgl_selesai = $pivotData?->tgl_selesai ?? now()->addYear()->format('Y-m-d');
        
        $this->isModalOpen = true;
    }

    /**
     * Update kelompok (pindahkan siswa)
     */
    public function updateKelompok()
    {
        $this->validate();

        DB::beginTransaction();
        
        try {
            $kelompok = Kelompok::findOrFail($this->kelompok_id);
            
            // ✅ Cek akses lagi
            if ($kelompok->id_guru != Auth::user()->guru->id_guru) {
                throw new \Exception('Anda tidak memiliki akses ke kelompok ini!');
            }
            
            // Pindahkan siswa
            $this->pindahkanSiswa($kelompok);

            DB::commit();
            session()->flash('success', 'Kelompok berhasil diperbarui!');
            $this->closeModal();
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Pindahkan siswa ke kelompok
     */
    private function pindahkanSiswa($kelompok)
    {
        $attachData = [];
        
        foreach ($this->siswa_dipilih as $id_siswa) {
            // Hapus siswa dari kelompok lain
            DB::table('siswa_kelompok')
                ->where('id_siswa', $id_siswa)
                ->where('id_kelompok', '!=', $kelompok->id_kelompok)
                ->delete();
            
            $attachData[$id_siswa] = [
                'tgl_mulai' => $this->tgl_mulai,
                'tgl_selesai' => $this->tgl_selesai,
            ];
        }
        
        // Sync siswa ke kelompok ini
        $kelompok->siswa()->sync($attachData);
    }

    /**
     * Close modal
     */
    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
        $this->resetValidation();
    }

    /**
     * Reset form
     */
    private function resetForm()
    {
        $this->kelompok_id = null;
        $this->nama_kelompok = '';
        $this->id_kelas = null;
        $this->id_guru = null;
        $this->siswa_dipilih = [];
        $this->daftar_siswa = [];
        $this->tgl_mulai = now()->format('Y-m-d');
        $this->tgl_selesai = now()->addYear()->format('Y-m-d');
    }

    /**
     * Render view
     */
    public function render()
    {
        // ✅ Ambil ID guru yang login
        $guruId = Auth::user()->guru->id_guru;

        // ✅ Hanya tampilkan kelompok milik guru yang login
        $query = Kelompok::with(['kelas', 'guru.akun', 'siswa'])
            ->where('id_guru', $guruId)
            ->orderBy('nama_kelompok');

        // ✅ Search: nama kelompok atau kelas
        if ($this->search) {
            $query->where(function($q) {
                $q->where('nama_kelompok', 'like', '%' . $this->search . '%')
                  ->orWhereHas('kelas', function($subQuery) {
                      $subQuery->where('nama_kelas', 'like', '%' . $this->search . '%');
                  });
            });
        }

        $kelompok = $query->get();

        return view('livewire.guru.manajemen-kelompok', [
            'kelompok' => $kelompok
        ])->layout('layouts.guru');
    }
}