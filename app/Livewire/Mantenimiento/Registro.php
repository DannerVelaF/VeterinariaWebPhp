<?php

namespace App\Livewire\Mantenimiento;

use Livewire\Component;

class Registro extends Component
{

    public $tab = "proveedores";


    public function setTab($tab)
    {
        $this->tab = $tab;
    }

    public function render()
    {
        return view('livewire.mantenimiento.registro');
    }
}
