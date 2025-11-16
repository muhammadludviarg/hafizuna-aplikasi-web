<?php

namespace App\Livewire\Guru;

use Livewire\Component;

class LaporanHafalan extends Component
{
    public function render()
    {
        return view('livewire.guru.laporan-hafalan')
            ->layout('layouts.guru');
    }
}
