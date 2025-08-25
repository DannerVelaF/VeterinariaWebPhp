<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Livewire\Component;



class Login extends Component
{
    public $username;
    public $password;
    public $alertMessage = null;
    public $alertType = null;

    protected $rules = [
        'username' => 'required|string',
        'password' => 'required|string|min:4',
    ];

    protected $messages = [
        'username.required' => 'El usuario es obligatorio.',
        'password.required' => 'La contraseña es obligatoria.',
        'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
    ];

    public function mount()
    {
        if (Session::has('user')) {
            return redirect()->route('ventas');
        }
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('components.layouts.guest');
    }

    public function login()
    {
        $this->validate();

        $user = User::where('username', trim($this->username))->first();

        if (!$user) {
            $this->alertMessage = "Usuario no encontrado.";
            $this->alertType = "error";
            return;
        }

        // Validación de usuario activo
        if ($user->estado !== "activo") {
            $this->alertMessage = 'Usuario inactivo. Contacta al administrador.';
            $this->alertType = 'error';
            return;
        }

        // Verificación de contraseña usando hash
        if (!Hash::check($this->password, $user->password_hash)) {
            $this->alertMessage = 'Contraseña incorrecta.';
            $this->alertType = 'error';
            return;
        }

        // Autenticación exitosa
        Session::put('user', $user);
        return redirect()->route('two.factor');
    }
}
