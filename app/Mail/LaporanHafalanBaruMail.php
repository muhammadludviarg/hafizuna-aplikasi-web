<?php

namespace App\Mail;

use App\Models\SesiHafalan; // Impor model
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LaporanHafalanBaruMail extends Mailable
{
    use Queueable, SerializesModels;

    public $sesi; // Buat properti publik

    /**
     * Buat instance pesan baru.
     */
    public function __construct(SesiHafalan $sesi)
    {
        $this->sesi = $sesi; // Terima data sesi
    }

    /**
     * Dapatkan amplop pesan.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Laporan Hafalan Baru - ' . $this->sesi->siswa->nama_siswa,
        );
    }

    /**
     * Dapatkan konten pesan.
     */
    public function content(): Content
    {
        // Tautkan ke file view Blade
        return new Content(
            view: 'emails.laporan-hafalan-baru',
        );
    }
}