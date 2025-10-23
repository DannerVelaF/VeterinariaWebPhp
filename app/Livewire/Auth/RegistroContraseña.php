<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class RegistroContraseña extends Component
{
    public $newPassword;
    public $newPassword_confirmation;
    public $passwordStrength = 'débil';

    public function mount()
    {
        if (Auth::check() && Auth::user()->ultimo_login !== null) {
            return redirect()->route('inicio');
        }
        $this->newPassword = '';
        $this->newPassword_confirmation = '';
    }

    // === Verifica seguridad de la contraseña ===
    public function updatedNewPassword($value)
    {
        $score = 0;
        if (strlen($value) >= 8) $score++;
        if (preg_match('/[A-Z]/', $value)) $score++;
        if (preg_match('/[a-z]/', $value)) $score++;
        if (preg_match('/[0-9]/', $value)) $score++;
        if (preg_match('/[^A-Za-z0-9]/', $value)) $score++;

        if ($score <= 2) {
            $this->passwordStrength = 'débil';
        } elseif ($score <= 4) {
            $this->passwordStrength = 'media';
        } else {
            $this->passwordStrength = 'fuerte';
        }

        // Revalidar coincidencia cuando cambia la contraseña principal
        $this->validarCoincidencia();
    }

    // === Valida coincidencia al cambiar la confirmación ===
    public function updatedNewPasswordConfirmation($value)
    {
        $this->validarCoincidencia();
    }

    // === Lógica reutilizable de coincidencia ===
    private function validarCoincidencia()
    {
        if ($this->newPassword && $this->newPassword_confirmation) {
            if ($this->newPassword !== $this->newPassword_confirmation) {
                $this->addError('newPassword_confirmation', 'Las contraseñas no coinciden.');
            } else {
                $this->resetErrorBag('newPassword_confirmation');
            }
        }
    }

    public function guardar()
    {
        $this->validate([
            'newPassword' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\w\d\s]).{8,}$/',
            ],
            'newPassword_confirmation' => 'required|same:newPassword',
        ], [
            'newPassword.required' => 'La contraseña es requerida.',
            'newPassword.min' => 'Debe tener al menos 8 caracteres.',
            'newPassword.regex' => 'Debe contener mayúscula, minúscula, número y carácter especial.',
            'newPassword_confirmation.same' => 'Las contraseñas no coinciden.',
        ]);

        $user = Auth::user();

        try {
            DB::transaction(function () use ($user) {
                $user->contrasena = Hash::make($this->newPassword);
                $user->ultimo_login = now();
                $user->save();
            });

            $this->dispatch('notify', title: 'Éxito', description: 'Contraseña registrada correctamente.', type: 'success');
            $this->reset('newPassword', 'newPassword_confirmation');
            return redirect()->route('inicio');
        } catch (\Exception $e) {
            Log::error('Error al actualizar la contraseña', ['error' => $e->getMessage()]);
            $this->dispatch('notify', title: 'Error', description: 'No se pudo actualizar la contraseña.', type: 'error');
        }
    }

    public function render()
    {
        return view('livewire.auth.registro-contraseña')->layout('components.layouts.guest');
    }
}
