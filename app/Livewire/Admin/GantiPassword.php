<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\Auth; // <-- 1. Import Auth

class GantiPassword extends Component
{
    public function render()
    {
        // 2. Ambil data user dan kirimkan ke view
        // 3. Gunakan layout admin Anda (sesuai file yang Anda kirim sebelumnya)
        return view('livewire.admin.ganti-password', [
            'user' => Auth::user()
        ])
            ->layout('layouts.admin'); // <-- Menggunakan layout Admin
    }
}