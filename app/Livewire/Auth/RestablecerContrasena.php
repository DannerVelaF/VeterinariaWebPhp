<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Log;
use Livewire\Component;
use App\Models\User;

class RestablecerContrasena extends Component
{
    public $email;
    public $password;
    public $password_confirmation;
    public $alertMessage = '';
    public $alertType = '';
    public $step = 1; // 1: Solicitar email, 2: Nueva contraseña
    public $user;

    protected $rules = [
        'email' => 'required|email',
    ];

    protected $messages = [
        'email.required' => 'El correo electrónico es obligatorio.',
        'email.email' => 'Debe ingresar un correo electrónico válido.',
    ];

    protected $validationAttributes = [
        'password' => 'contraseña',
        'password_confirmation' => 'confirmación de contraseña',
    ];

    public function validateEmail()
    {
        $this->validate();

        try {
            // Buscar el usuario a través de la relación con persona
            $this->user = User::with('persona')
                ->whereHas('persona', function ($query) {
                    $query->where('correo_electronico_personal', $this->email);
                })
                ->first();

            if ($this->user) {
                // Cambiar al paso 2 (formulario de nueva contraseña)
                $this->step = 2;
                $this->alertMessage = '';
            } else {
                $this->alertMessage = 'No encontramos un usuario con este correo electrónico.';
                $this->alertType = 'error';
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $this->alertMessage = 'Error del servidor. Por favor, intenta más tarde.';
            $this->alertType = 'error';
        }
    }

    public function resetPassword()
    {
        $this->validate([
            'password' => 'required|min:8|confirmed',
        ], [
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        try {
            // Actualizar la contraseña del usuario
            $this->user->update([
                'contrasena' => bcrypt($this->password)
            ]);

            $this->alertMessage = '¡Contraseña actualizada correctamente! Ahora puedes iniciar sesión con tu nueva contraseña.';
            $this->alertType = 'success';

            // Redirigir al login después de 3 segundos
            redirect()->route('login');

        } catch (\Exception $e) {
            $this->alertMessage = 'Error al actualizar la contraseña. Por favor, intenta nuevamente.';
            $this->alertType = 'error';
        }
    }

    public function goBack()
    {
        $this->step = 1;
        $this->reset(['password', 'password_confirmation']);
        $this->alertMessage = '';
    }

    public function render()
    {
        return view('livewire.auth.restablecer-contrasena')->layout('components.layouts.guest');
    }
}
