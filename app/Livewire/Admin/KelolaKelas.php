<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class KelolaKelas extends Component
{
    public function render()
    {
        return view('livewire.admin.kelola-kelas')
        ->layout('layouts.admin');
    }
}
