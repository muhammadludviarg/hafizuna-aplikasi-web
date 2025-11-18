<?php

namespace App\Livewire\OrangTua;

use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.orang-tua.dashboard')
            ->layout('layouts.orang-tua');
    }
}
