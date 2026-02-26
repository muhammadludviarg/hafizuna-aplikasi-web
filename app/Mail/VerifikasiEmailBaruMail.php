<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class VerifikasiEmailBaruMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $emailBaru;
    public $url;

    public function __construct($user, $emailBaru)
    {
        $this->user = $user;
        $this->emailBaru = $emailBaru;

        // Link kedaluwarsa dalam 10 menit
        $this->url = URL::temporarySignedRoute(
            'verify.email.change', 
            now()->addMinutes(10), 
            ['id' => $user->id_akun, 'hash' => sha1($emailBaru)]
        );
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Verifikasi Email Baru - Hafizuna');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.verifikasi-email-baru');
    }
}