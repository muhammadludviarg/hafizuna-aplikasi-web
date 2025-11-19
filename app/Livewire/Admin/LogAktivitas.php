<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\LogAktivitas as LogAktivitasModel; // Gunakan alias agar tidak bentrok
use Livewire\WithPagination;

class LogAktivitas extends Component
{
    use WithPagination;

    // Terapkan tema paginasi bootstrap agar sesuai dengan admin panel
    protected $paginationTheme = 'bootstrap'; 

    public function render()
    {
        // Ambil data log, gabungkan (join) dengan tabel akun untuk dapat nama & email
        // Urutkan berdasarkan timestamp terbaru
        $logs = LogAktivitasModel::join('akun', 'log_aktivitas.id_akun', '=', 'akun.id_akun')
                    ->select('log_aktivitas.*', 'akun.nama_lengkap', 'akun.email')
                    ->orderBy('log_aktivitas.timestamp', 'desc')
                    ->paginate(20); // Tampilkan 20 log per halaman

        return view('livewire.admin.log-aktivitas', [
            'logs' => $logs
        ]);
    }
}