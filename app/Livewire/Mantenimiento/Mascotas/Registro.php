<?php

namespace App\Livewire\Mantenimiento\Mascotas;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

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
        return view('livewire.mantenimiento.mascotas.registro');
    }
}