<?php
// app/Livewire/Admin/TargetHafalan.php

namespace App\Livewire\Admin;

use App\Models\TargetHafalanKelompok;
use App\Models\Kelompok;
use App\Models\Kelas;
use App\Models\Surah;
use App\Models\Admin;
use Livewire\Component;

class TargetHafalan extends Component
{
    // Form fields
    public $id_kelas;
    public $id_kelompok;
    public $periode;
    public $tanggal_mulai;
    public $tanggal_selesai;
    public $id_surah_awal;
    public $id_surah_akhir;
    
    // Edit mode
    public $isEditing = false;
    public $editingId;
    
    // Data untuk dropdown
    public $daftarKelas = [];
    public $daftarKelompok = [];
    public $daftarSurah = [];
    
    // Data target yang sudah dibuat
    public $daftarTarget = [];

    protected $rules = [
        'id_kelompok' => 'required|exists:kelompok,id_kelompok',
        'periode' => 'required|string|max:255',
        'tanggal_mulai' => 'required|date',
        'tanggal_selesai' => 'required|date|after:tanggal_mulai',
        'id_surah_awal' => 'required|exists:surah,id_surah',
        'id_surah_akhir' => 'required|exists:surah,id_surah',
    ];

    public function mount()
    {
        $this->loadData();
        $this->generatePeriodeOtomatis();
    }

    public function loadData()
    {
        $this->daftarKelas = Kelas::orderBy('nama_kelas')->get();
        $this->daftarSurah = Surah::orderBy('nomor_surah')->get();
        
        if ($this->id_kelas) {
            $this->loadKelompok();
        }
        
        $this->loadDaftarTarget();
    }

    public function loadKelompok()
    {
        $this->daftarKelompok = Kelompok::with(['kelas', 'guru.akun'])
            ->where('id_kelas', $this->id_kelas)
            ->get();
    }

    public function loadDaftarTarget()
    {
        $this->daftarTarget = TargetHafalanKelompok::with([
            'kelompok.kelas',
            'kelompok.guru.akun',
            'surahAwal',
            'surahAkhir'
        ])
        ->orderBy('tanggal_mulai', 'desc')
        ->get()
        ->map(function($target) {
            // Format nama kelompok
            $target->nama_kelompok_display = $target->kelompok->kelas->nama_kelas . 
                                            ' - Kelompok ' . 
                                            ($target->kelompok->guru->akun->nama_lengkap ?? 'N/A');
            return $target;
        });
    }

    public function generatePeriodeOtomatis()
    {
        $tahun = now()->year;
        $bulan = now()->month;
        
        if ($bulan >= 7) {
            $this->periode = "Semester 1 {$tahun}/" . ($tahun + 1);
        } else {
            $this->periode = "Semester 2 " . ($tahun - 1) . "/{$tahun}";
        }
    }

    public function updatedIdKelas($value)
    {
        // Jangan reset kelompok saat edit mode
        if (!$this->isEditing) {
            $this->id_kelompok = null;
        }
        
        if ($value) {
            $this->loadKelompok();
        } else {
            $this->daftarKelompok = [];
        }
    }

    public function simpanTarget()
    {
        $this->validate();

        $data = [
            'id_kelompok' => $this->id_kelompok,
            'periode' => $this->periode,
            'tanggal_mulai' => $this->tanggal_mulai,
            'tanggal_selesai' => $this->tanggal_selesai,
            'id_surah_awal' => $this->id_surah_awal,
            'id_surah_akhir' => $this->id_surah_akhir,
            'id_admin' => Admin::where('id_akun', auth()->id())->first()->id_admin,
        ];

        if ($this->isEditing) {
            TargetHafalanKelompok::find($this->editingId)->update($data);
            session()->flash('success', 'Target hafalan berhasil diperbarui!');
        } else {
            TargetHafalanKelompok::create($data);
            session()->flash('success', 'Target hafalan berhasil ditambahkan!');
        }

        $this->resetForm();
        $this->loadDaftarTarget();
    }

    public function edit($id)
    {
        $target = TargetHafalanKelompok::with(['kelompok', 'surahAwal', 'surahAkhir'])->findOrFail($id);
        
        // Set editing mode FIRST
        $this->isEditing = true;
        $this->editingId = $id;
        
        // Load kelas dulu untuk populate dropdown kelompok
        $this->id_kelas = $target->kelompok->id_kelas;
        $this->loadKelompok();
        
        // Set semua field
        $this->id_kelompok = $target->id_kelompok;
        $this->periode = $target->periode;
        
        // PENTING: Format tanggal HARUS Y-m-d untuk input type="date"
        // Gunakan Carbon parse untuk handle berbagai format dari database
        $this->tanggal_mulai = \Carbon\Carbon::parse($target->tanggal_mulai)->format('Y-m-d');
        $this->tanggal_selesai = \Carbon\Carbon::parse($target->tanggal_selesai)->format('Y-m-d');
        
        $this->id_surah_awal = $target->id_surah_awal;
        $this->id_surah_akhir = $target->id_surah_akhir;
        
        // DEBUG: Uncomment untuk cek format tanggal
        // dd([
        //     'tanggal_mulai' => $this->tanggal_mulai,
        //     'tanggal_selesai' => $this->tanggal_selesai,
        //     'raw_mulai' => $target->tanggal_mulai,
        // ]);
    }

    public function hapus($id)
    {
        TargetHafalanKelompok::destroy($id);
        session()->flash('success', 'Target hafalan berhasil dihapus!');
        $this->loadDaftarTarget();
    }

    public function resetForm()
    {
        $this->reset([
            'id_kelas',
            'id_kelompok',
            'tanggal_mulai',
            'tanggal_selesai',
            'id_surah_awal',
            'id_surah_akhir',
            'isEditing',
            'editingId'
        ]);
        $this->daftarKelompok = [];
        $this->generatePeriodeOtomatis();
    }

    public function render()
    {
        return view('livewire.admin.target-hafalan')
            ->layout('layouts.admin');
    }    
}