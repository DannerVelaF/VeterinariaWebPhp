<?php

namespace App\Livewire\Mantenimiento\Usuarios;

use App\Models\Trabajador;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Usuarios extends Component
{

    public $trabajadores = [];
    public $roles = [];
    public $permisos = [];

    public $trabajadorSeleccionado = '';
    public $username;
    public $password;
    public $rolSeleccionado = '';
    public $permisosSeleccionados = [];

    public function updateTrabajadorSelecccionado($value)
    {
        $this->trabajadorSeleccionado = $value;
    }

    public function mount()
    {
        $this->trabajadores = Trabajador::whereHas('estadoTrabajador', function ($q) {
            $q->where('nombre', 'activo');
        })->get();
        $this->roles = Role::all();
        $this->permisos = Permission::all();
    }


    public function guardar()
    {
        $this->validate([
            'trabajadorSeleccionado' => 'required|exists:trabajadores,id',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:6',
            'rolSeleccionado' => 'required|exists:roles,name',
        ]);

        // Validar si el trabajador ya tiene usuario
        $trabajador = Trabajador::find($this->trabajadorSeleccionado);

        $existeUsuario = User::where('id_persona', $trabajador->id_persona)->exists();

        if ($existeUsuario) {
            session()->flash('error', 'Este trabajador ya tiene un usuario asignado.');
            return;
        }

        // Crear usuario
        $user = User::create([
            'username' => $this->username,
            'password_hash' => Hash::make($this->password),
            'estado' => 'activo',
            'id_persona' => Trabajador::find($this->trabajadorSeleccionado)->id_persona,
        ]);

        // Asignar rol
        $user->assignRole($this->rolSeleccionado);

        // Asignar permisos adicionales (opcional)
        if (!empty($this->permisosSeleccionados)) {
            $user->givePermissionTo($this->permisosSeleccionados);
        }

        session()->flash('success', 'Usuario registrado correctamente.');
        $this->reset(['trabajadorSeleccionado', 'username', 'password', 'rolSeleccionado', 'permisosSeleccionados']);
    }

    public function render()
    {
        return view('livewire.mantenimiento.usuarios.usuarios');
    }
}
