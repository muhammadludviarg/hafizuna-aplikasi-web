<?php

namespace App\Livewire\Guru;

use Livewire\Component;

class ManajemenKelompok extends Component
{
    public function render()
    {
        return view('livewire.guru.manajemen-kelompok')
        ->layout('layouts.guru');
    }
}
