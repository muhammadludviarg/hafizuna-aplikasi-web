<?php

namespace App\Livewire\Guru;

use Livewire\Component;

class DetailKelompok extends Component
{
    public function render()
    {
        return view('livewire.guru.detail-kelompok')
        ->layout('layouts.guru');
    }
}
