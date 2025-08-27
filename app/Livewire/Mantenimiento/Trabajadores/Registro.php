<?php

namespace App\Livewire\Mantenimiento\Trabajadores;

use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Livewire\Livewire;

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
        return view('livewire.mantenimiento.trabajadores.registro');
    }
}
