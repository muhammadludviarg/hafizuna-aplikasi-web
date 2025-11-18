<?php

namespace App\Livewire\OrangTua;

use Livewire\Component;

class LaporanHafalan extends Component
{
    public function render()
    {
        return view('livewire.orang-tua.laporan-hafalan')
            ->layout('layouts.orang-tua');
    }
}
