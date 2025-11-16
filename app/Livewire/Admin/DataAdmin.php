<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class DataAdmin extends Component
{
    public function render()
    {
        return view('livewire.admin.data-admin')
            ->layout('layouts.admin');
    }
}
