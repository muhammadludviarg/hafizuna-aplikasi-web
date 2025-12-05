<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class DataMaster extends Component
{
    public $activeTab = 'kelas';

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.admin.data-master')->layout('layouts.app');
    }
}