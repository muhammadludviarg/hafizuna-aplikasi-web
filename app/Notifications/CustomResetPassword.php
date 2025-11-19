<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Notifikasi Kustom untuk Reset Password.
 * Ini menggantikan notifikasi bawaan Laravel untuk mengontrol pesan.
 */
class CustomResetPassword extends ResetPassword
{
    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        // Link untuk reset password, token diambil dari properti parent ($this->token)
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false)); // Gunakan false agar link di email menggunakan HTTP (jika belum force HTTPS)

        return (new MailMessage)
            // Baris Subjek Email
            ->subject('Permintaan Atur Ulang Kata Sandi Akun Hafizuna')

            // Isi Email (Diterjemahkan ke Bahasa Indonesia)
            ->greeting("Assalamu'alaikum, Bapak/Ibu.")

            // Baris Pertama
            ->line('Anda menerima email ini karena kami menerima permintaan reset kata sandi untuk akun Anda di Hafizuna.')

            // Baris Kedua
            ->action('Atur Ulang Kata Sandi', $url) // Tombol Aksi

            // Baris Ketiga (Penting untuk Keamanan)
            ->line('Tautan pengaturan ulang kata sandi ini akan kedaluwarsa dalam ' . config('auth.passwords.users.expire') . ' menit.')

            // Baris Keempat
            ->line('Jika Anda tidak merasa mengajukan permintaan ini, tidak perlu melakukan tindakan lebih lanjut. Kata sandi Anda akan tetap sama.')

            // Footer (Sesuai dengan nama aplikasi Anda)
            ->salutation('Wassalamu\'alaikum, Salam Hormat dari Tim Hafizuna');
    }
}