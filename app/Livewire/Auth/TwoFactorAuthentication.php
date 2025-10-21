<?php

namespace App\Livewire\Auth;

use App\Mail\TwoFactorCodeMail;
use App\Models\modulo;
use App\Models\modulo_roles;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class TwoFactorAuthentication extends Component
{
    public $user;
    public $code;
    public $inputCode;
    public $alertMessage = null;
    public $alertType = null;

    public function mount()
    {
        $userId = Session::get('two_factor_user_id');
        if (!$userId) {
            return redirect()->route('login'); // no hay usuario en 2FA
        }

        $this->user = User::find($userId);

        if (!$this->user) {
            abort(404, 'Usuario no encontrado');
        }

        // Generar código 2FA y enviarlo
        $this->code = rand(100000, 999999);
        Session::put('two_factor_code', $this->code);

        Mail::to($this->user->persona->correo_electronico_personal)
            ->send(new TwoFactorCodeMail($this->code));
    }

    public function verifyCode()
    {
        if ($this->inputCode == Session::get('two_factor_code')) {

            // ✅ Autenticar al usuario solo después de verificar 2FA
            auth()->loginUsingId(Session::get('two_factor_user_id'));

            // Limpiar sesión temporal
            Session::forget('two_factor_code');
            Session::forget('two_factor_user_id');

            // Registrar último login
            $this->user->ultimo_login = now();
            $this->user->save();
            return redirect()->route('inicio');
        } else {
            $this->alertMessage = "Código incorrecto, intente nuevamente.";
            $this->alertType = "error";
        }
    }

    public function render()
    {
        return view('livewire.auth.two-factor-authentication')->layout('components.layouts.guest');
    }
}
