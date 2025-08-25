<?php

namespace App\Livewire\Auth;

use App\Models\User;
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

        $userId = Session::get('user')->id;
        $this->user = User::find($userId);

        if (!$this->user) {
            abort(404, 'Usuario no encontrado');
        }

        // Generar código de 2FA
        $this->code = rand(100000, 999999);
        Session::put('two_factor_code', $this->code);
        Session::put('two_factor_user_id', $this->user->id);

        // Enviar correo
        Mail::raw("Tu código de verificación es: {$this->code}", function ($message) {
            $message->to($this->user->persona->correo)
                ->subject("Código de verificación");
        });
    }

    public function verifyCode()
    {
        if ($this->inputCode == Session::get('two_factor_code')) {
            // Autenticar al usuario
            auth()->loginUsingId(Session::get('two_factor_user_id'));

            // Limpiar sesión
            Session::forget('two_factor_code');
            Session::forget('two_factor_user_id');

            return redirect()->route('ventas');
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
