<?php

namespace App\Livewire\OrangTua;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class GantiPassword extends Component
{
    // ... (properti dan rules sama seperti sebelumnya) ...
    public $password_lama = '';
    public $password_baru = '';
    public $password_baru_confirmation = '';
    
    public $showPasswordLama = false;
    public $showPasswordBaru = false;
    public $showPasswordKonfirmasi = false;
    
    public $showPasswordStrength = false;
    public $passwordStrength = [
        'length' => false,
        'uppercase' => false,
        'number' => false,
        'symbol' => false,
    ];

    protected function rules()
    {
        return [
            'password_lama' => 'required',
            'password_baru' => [
                'required',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
                'confirmed',
            ],
            'password_baru_confirmation' => 'required',
        ];
    }

    // ... (messages dan updatedPasswordBaru sama) ...
    protected $messages = [
        'password_lama.required' => 'Password lama wajib diisi',
        'password_baru.required' => 'Password baru wajib diisi',
        'password_baru.min' => 'Password minimal 8 karakter',
        'password_baru.regex' => 'Password harus mengandung huruf besar, angka, dan simbol',
        'password_baru.confirmed' => 'Konfirmasi password tidak cocok dengan password baru',
        'password_baru_confirmation.required' => 'Konfirmasi password wajib diisi',
    ];

    public function updatedPasswordBaru($value)
    {
        $this->showPasswordStrength = !empty($value);
        
        $this->passwordStrength['length'] = strlen($value) >= 8;
        $this->passwordStrength['uppercase'] = preg_match('/[A-Z]/', $value);
        $this->passwordStrength['number'] = preg_match('/[0-9]/', $value);
        $this->passwordStrength['symbol'] = preg_match('/[@$!%*#?&]/', $value);
    }

    public function gantiPassword()
    {
        $this->validate();

        $user = Auth::user();

        // Cek password lama (ke sandi_hash)
        if (!Hash::check($this->password_lama, $user->sandi_hash)) {
            $this->addError('password_lama', 'Password lama yang Anda masukkan tidak sesuai. Silakan coba lagi.');
            return;
        }

        // Update password (ke sandi_hash)
        $user->sandi_hash = Hash::make($this->password_baru);
        $user->save();

        // --- TAMBAHKAN LOGOUT OTOMATIS DI SINI ---
        Auth::logout();     // Logout user
        session()->invalidate();    // Hapus sesi
        session()->regenerateToken(); // Regenerasi token CSRF

        // Redirect ke halaman login dengan pesan sukses
        return redirect('/login')->with('status', 'Password berhasil diubah! Silakan login kembali dengan password baru Anda.');
    }

    public function render()
    {
        return view('livewire.admin.ganti-password')
            ->layout('layouts.app');
    }
}