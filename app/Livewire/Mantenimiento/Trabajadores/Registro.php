<?php

namespace App\Livewire\Mantenimiento\Trabajadores;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Livewire\Livewire;

class Registro extends Component
{
    public $user;
    public function mount()
    {
        $this->user = Auth::user();

        if (!$this->user) {
            abort(404, 'Usuario no encontrado');
        }
    }

    public function render()
    {
        return view('livewire.mantenimiento.trabajadores.registro');
    }
}
