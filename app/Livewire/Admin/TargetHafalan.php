<?php

namespace App\Livewire\Admin;

use App\Models\TargetHafalanKelompok;
use App\Models\Kelompok;
use App\Models\Kelas;
use App\Models\Surah;
use App\Models\Admin;
use App\Models\Periode;
use Livewire\Component;

class TargetHafalan extends Component
{
    // Form fields
    public $id_kelas;
    public $id_kelompok;
    public $id_periode;
    public $tanggal_mulai;
    public $tanggal_selesai;
    public $id_surah_awal;
    public $id_surah_akhir;

    public $showModal = false;

    // Edit mode
    public $isEditing = false;
    public $editingId;

    // Data untuk dropdown
    public $daftarKelas = [];
    public $daftarKelompok = [];
    public $daftarSurah = [];
    public $daftarPeriode = [];

    // Data target yang sudah dibuat
    public $daftarTarget = [];

    public $showSuccessToast = false;
    public $successMessage = '';
    public $toastType = 'success';

    protected $rules = [
        'id_kelompok' => 'required|exists:kelompok,id_kelompok',
        'id_periode' => 'required|exists:periode,id_periode',
        'tanggal_mulai' => 'required|date',
        'tanggal_selesai' => 'required|date|after:tanggal_mulai',
        'id_surah_awal' => 'required|exists:surah,id_surah',
        'id_surah_akhir' => 'required|exists:surah,id_surah',
    ];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->daftarKelas = Kelas::orderBy('nama_kelas')->get();
        $this->daftarSurah = Surah::orderBy('nomor_surah')->get();
        $this->daftarPeriode = Periode::orderBy('tahun_ajaran', 'desc')
            ->orderBy('semester', 'asc')
            ->get();

        if ($this->id_kelas) {
            $this->loadKelompok();
        }

        $this->loadDaftarTarget();
    }

    public function loadKelompok()
    {
        $query = Kelompok::with(['kelas', 'guru.akun'])
            ->where('id_kelas', $this->id_kelas);

        $this->daftarKelompok = $query->get()
            ->map(function ($kelompok) {
                $hasTarget = false;
                if ($this->id_periode && !$this->isEditing) {
                    // Hanya cek duplikasi saat CREATE, bukan EDIT
                    $hasTarget = TargetHafalanKelompok::where('id_kelompok', $kelompok->id_kelompok)
                        ->where('id_periode', $this->id_periode)
                        ->exists();
                }

                $kelompok->has_target = $hasTarget;
                return $kelompok;
            });
    }

    public function loadDaftarTarget()
    {
        $this->daftarTarget = TargetHafalanKelompok::with([
            'kelompok.kelas',
            'kelompok.guru.akun',
            'surahAwal',
            'surahAkhir',
            'periode'
        ])
            ->get()
            ->sortBy(function ($target) {
                return $target->kelompok->kelas->nama_kelas ?? '';
            })
            ->values();
    }

    public function updatedIdKelas($value)
    {
        if (!$this->isEditing) {
            $this->id_kelompok = null;
        }

        if ($value) {
            $this->loadKelompok();
        } else {
            $this->daftarKelompok = [];
        }
    }

    public function updatedIdPeriode($value)
    {
        if (!$this->isEditing && $this->id_kelas) {
            $this->loadKelompok();
        }
    }

    public function simpanTarget()
    {
        $this->validate();

        // Fix: Saat edit, exclude current target from duplicate check
        $exists = TargetHafalanKelompok::where('id_kelompok', $this->id_kelompok)
            ->where('id_periode', $this->id_periode)
            ->when($this->isEditing, function ($query) {
                // Saat EDIT, exclude target yang sedang diedit
                return $query->where('id_target', '!=', $this->editingId);
            })
            ->exists();

        if ($exists) {
            $this->showToast('Kelompok ini sudah memiliki target untuk periode ini', 'error');
            return;
        }

        $data = [
            'id_kelompok' => $this->id_kelompok,
            'id_periode' => $this->id_periode,
            'tanggal_mulai' => $this->tanggal_mulai,
            'tanggal_selesai' => $this->tanggal_selesai,
            'id_surah_awal' => $this->id_surah_awal,
            'id_surah_akhir' => $this->id_surah_akhir,
            'id_admin' => Admin::where('id_akun', auth()->id())->first()->id_admin,
        ];

        if ($this->isEditing) {
            TargetHafalanKelompok::find($this->editingId)->update($data);
            $this->showToast('Target hafalan berhasil diperbarui!', 'success');
        } else {
            TargetHafalanKelompok::create($data);
            $this->showToast('Target hafalan berhasil ditambahkan!', 'success');
        }

        $this->resetForm();
        $this->loadDaftarTarget();
        $this->loadKelompok();
    }

    public function openCreateForm()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $target = TargetHafalanKelompok::with(['kelompok', 'surahAwal', 'surahAkhir'])->findOrFail($id);

        $this->isEditing = true;
        $this->editingId = $id;
        $this->showModal = true;

        $this->id_kelas = $target->kelompok->id_kelas;
        $this->loadKelompok();

        $this->id_kelompok = $target->id_kelompok;
        $this->id_periode = $target->id_periode;
        $this->tanggal_mulai = \Carbon\Carbon::parse($target->tanggal_mulai)->format('Y-m-d');
        $this->tanggal_selesai = \Carbon\Carbon::parse($target->tanggal_selesai)->format('Y-m-d');
        $this->id_surah_awal = $target->id_surah_awal;
        $this->id_surah_akhir = $target->id_surah_akhir;
    }

    public function hapus($id)
    {
        TargetHafalanKelompok::destroy($id);
        $this->showToast('Target hafalan berhasil dihapus!', 'success');
        $this->loadDaftarTarget();
        $this->loadKelompok();
    }

    public function showToast($message, $type = 'success')
    {
        $this->successMessage = $message;
        $this->toastType = $type;
        $this->showSuccessToast = true;

        $this->dispatch('toast-timer', delay: 3000);
    }

    public function resetForm()
    {
        $this->reset([
            'id_kelas',
            'id_kelompok',
            'id_periode',
            'tanggal_mulai',
            'tanggal_selesai',
            'id_surah_awal',
            'id_surah_akhir',
            'isEditing',
            'editingId',
            'showModal'
        ]);
        $this->daftarKelompok = [];
    }

    public function render()
    {
        return view('livewire.admin.target-hafalan')->layout('layouts.app');
    }
}
