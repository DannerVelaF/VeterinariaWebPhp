<?php

namespace App\Livewire\Inventario;

use Livewire\Component;

class Lotes extends Component
{
    public $lotes = [];

    public function mount()
    {
        $this->lotes = Lotes::all();
    }

    public function render()
    {
        return view('livewire.inventario.lotes');
    }
}
