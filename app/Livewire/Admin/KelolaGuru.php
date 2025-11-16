<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class KelolaGuru extends Component
{
    public function render()
    {
        return view('livewire.admin.kelola-guru')
            ->layout('layouts.admin');
    }
}
