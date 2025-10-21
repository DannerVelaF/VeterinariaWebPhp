<?php

namespace App\Livewire\Ventas;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class RegistroVenta extends Component
{
    public $user;
    public function mount()
    {

        $this->user = Auth::user();

        if (!$this->user) {
            Log::info('Usuario no autenticado');
            return redirect()->route('login');
        }
    }

    public function render()
    {
        return view('livewire.ventas.registro-venta');
    }
}
