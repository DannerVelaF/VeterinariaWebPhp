<?php

namespace App\Livewire\Ventas;

use Illuminate\Support\Facades\Session;
use Livewire\Component;

class RegistroVenta extends Component
{

    public function mount()
    {
        if (!Session::has('user')) {
            return redirect()->route('login');
        }
    }

    public function render()
    {
        return view('livewire.ventas.registro-venta');
    }
}
