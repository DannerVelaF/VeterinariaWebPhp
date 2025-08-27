<?php

namespace App\Livewire\Ventas;

use Livewire\Component;

class RegistrarVenta extends Component
{


    public $nombre = "Danner";

    public function registarVenta() {}

    public function render()
    {
        return view('livewire.ventas.registrar-venta');
    }
}
