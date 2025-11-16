<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class KelolaKelompok extends Component
{
    public function render()
    {
        return view('livewire.admin.kelola-kelompok')
        ->layout('layouts.admin');
    }
}
