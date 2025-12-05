<?php

namespace App\Livewire\Admin;

use App\Models\Periode;
use Livewire\Component;

class ManajePeriode extends Component
{
    public $tahun_ajaran;
    public $daftarPeriode = [];
    
    public $showModal = false;
    public $showSuccessToast = false;
    public $successMessage = '';
    public $toastType = 'success';

    public function mount()
    {
        $this->loadPeriode();
    }

    public function loadPeriode()
    {
        $this->daftarPeriode = Periode::orderBy('tahun_ajaran', 'desc')
            ->orderBy('semester', 'desc')
            ->get();
    }

    public function tambahTahunAjaran()
    {
        $this->validate([
            'tahun_ajaran' => 'required|regex:/^\d{4}\/\d{4}$/|unique:periode',
        ], [
            'tahun_ajaran.required' => 'Tahun ajaran harus diisi',
            'tahun_ajaran.regex' => 'Format tahun ajaran harus YYYY/YYYY (contoh: 2025/2026)',
            'tahun_ajaran.unique' => 'Tahun ajaran ' . $this->tahun_ajaran . ' sudah terdaftar',
        ]);

        try {
            // Buat 2 periode (Semester 1 & 2)
            Periode::create([
                'tahun_ajaran' => $this->tahun_ajaran,
                'semester' => 1,
                'label' => 'Semester 1 ' . $this->tahun_ajaran,
                'is_active' => false,
            ]);

            Periode::create([
                'tahun_ajaran' => $this->tahun_ajaran,
                'semester' => 2,
                'label' => 'Semester 2 ' . $this->tahun_ajaran,
                'is_active' => false,
            ]);

            $this->showToast('Tahun ajaran ' . $this->tahun_ajaran . ' berhasil ditambahkan!', 'success');
            $this->resetForm();
            $this->loadPeriode();
        } catch (\Exception $e) {
            $this->showToast('Gagal menambahkan tahun ajaran: ' . $e->getMessage(), 'error');
        }
    }

    public function setAktif($id)
    {
        $periode = Periode::findOrFail($id);
        
        // Deaktifkan semua periode lain dengan tahun ajaran berbeda
        Periode::whereNot('id_periode', $id)->update(['is_active' => false]);
        
        $periode->update(['is_active' => true]);
        $this->showToast('Periode ' . $periode->label . ' sudah diaktifkan', 'success');
        $this->loadPeriode();
    }

    public function hapusPeriode($id)
    {
        $periode = Periode::findOrFail($id);
        
        // Cek apakah ada target hafalan yang menggunakan periode ini
        if ($periode->targetHafalan()->count() > 0) {
            $this->showToast('Tidak bisa menghapus periode yang sudah memiliki target hafalan', 'error');
            return;
        }

        $periode->delete();
        $this->showToast('Periode berhasil dihapus', 'success');
        $this->loadPeriode();
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
        $this->tahun_ajaran = '';
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.admin.manaje-periode')->layout('layouts.app');
    }
}
