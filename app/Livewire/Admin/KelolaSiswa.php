<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class KelolaSiswa extends Component
{
    public function render()
    {
        return view('livewire.admin.kelola-siswa')
        ->layout('layouts.admin');
    }
}
