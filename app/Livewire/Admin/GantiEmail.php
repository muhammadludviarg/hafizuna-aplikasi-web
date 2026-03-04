<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Mail\VerifikasiEmailBaruMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class GantiEmail extends Component
{
    public $email_baru;
    public $statusPesan = '';

    protected $rules = [
        'email_baru' => 'required|email|max:30|unique:akun,email',
    ];

    public function requestPerubahan()
    {
        $this->validate();
        $user = Auth::user();

        $user->update(['email_sementara' => $this->email_baru]);
        Mail::to($this->email_baru)->send(new VerifikasiEmailBaruMail($user, $this->email_baru));

        $this->statusPesan = 'Tautan verifikasi telah dikirim ke ' . $this->email_baru . '. Silakan periksa kotak masuk/spam Anda.';
        $this->reset('email_baru');
    }

    public function render()
    {
        return view('livewire.admin.ganti-email')
            ->layout('layouts.app');
    }
}