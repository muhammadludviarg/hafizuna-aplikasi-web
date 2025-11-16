<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use Illuminate\Support\Facades\Auth; // <-- 1. Import Auth

class GantiPassword extends Component
{
    public function render()
    {
        // 2. Ambil data user dan kirimkan ke view
        return view('livewire.guru.ganti-password', [
            'user' => Auth::user() 
        ])
        ->layout('layouts.guru'); 
    }
}