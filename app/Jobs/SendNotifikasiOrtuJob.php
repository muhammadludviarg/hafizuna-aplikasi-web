<?php

namespace App\Jobs;

use App\Models\SesiHafalan;
use App\Mail\LaporanHafalanBaruMail; // Impor Mailable kita
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail; // Impor Mail Facade
use Illuminate\Support\Facades\Log; // Untuk debugging

class SendNotifikasiOrtuJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $sesi;

    /**
     * Buat instance job baru.
     */
    public function __construct(SesiHafalan $sesi)
    {
        $this->sesi = $sesi;
    }

    /**
     * Eksekusi job.
     */
    public function handle(): void
    {
        // 1. Ambil data Ortu & Akun
        // Sesi -> Siswa -> Ortu -> Akun -> Email
        // Kita gunakan 'fresh()' untuk memastikan kita dapat data terbaru
        $this->sesi->load(['siswa.ortu.akun', 'guru.akun']);

        $ortu = $this->sesi->siswa->ortu;

        // Pastikan ortu dan akunnya ada
        if ($ortu && $ortu->akun) {
            $emailOrtu = $ortu->akun->email;

            // Log untuk debugging
            Log::info("Mencoba kirim email ke: {$emailOrtu} untuk sesi: {$this->sesi->id_sesi}");

            // 2. Kirim email
            Mail::to($emailOrtu)->send(new LaporanHafalanBaruMail($this->sesi));
        } else {
            // Log jika ortu atau akun tidak ditemukan
            Log::warning("Gagal kirim notifikasi: Akun Ortu tidak ditemukan untuk siswa ID: {$this->sesi->id_siswa}");
        }
    }
}