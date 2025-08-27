<?php

namespace App\Livewire\Mantenimiento\Productos;

use Illuminate\Support\Facades\Session;
use Livewire\Component;

class Registro extends Component
{
    public function mount()
    {
        if (!Session::has('user')) {
            return redirect()->route('login');
        }
    }

    public function render()
    {
        return view('livewire.mantenimiento.productos.registro');
    }
}
