<?php

namespace App\Livewire\Mantenimiento\Usuarios;

use App\Models\Roles;
use App\Models\Trabajador;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Usuarios extends Component
{
    protected $listeners = [
        'abrirModalRol', 
        'rolesUpdated', 
        'roles-created-global' => 'cargarDatos', // Escuchar evento global
    ];

    public $usernameEdit;
    public $passwordEdit;
    public $estadoEdit;

    public $trabajadores = [];
    public $roles = [];
    public $permisos = [];

    public $trabajadorSeleccionado = '';
    public $username;
    public $password;
    public $rolSeleccionado = '';
    public $permisosSeleccionados = [];

    public $modalRol = false;
    public $usuarioSeleccionado; // instancia del usuario que vamos a editar
    public $rolNuevo; // rol seleccionado en el modal

    public function updateTrabajadorSelecccionado($value)
    {
        $this->trabajadorSeleccionado = $value;
    }

    public function mount()
    {
        $this->cargarDatos();
    }
    

    // ✅ MÉTODO PARA CARGAR LOS DATOS
    public function cargarDatos()
    {
        $this->trabajadores = Trabajador::whereHas('estadoTrabajador', function ($q) {
            $q->where('nombre_estado_trabajador', 'activo');
        })->get();
        
        $this->roles = Roles::where('estado', 'activo')->get();
    }


    public function guardar()
    {
        $this->validate([
            'trabajadorSeleccionado' => 'required|exists:trabajadores,id_trabajador',
            'username' => 'required|string|unique:usuarios,usuario',
            'password' => 'required|string|min:6',
            'rolSeleccionado' => 'required|exists:roles,id_rol',
        ]);

        $trabajador = Trabajador::find($this->trabajadorSeleccionado);

        // Validar si el trabajador ya tiene usuario
        if (User::where('id_persona', $trabajador->id_persona)->exists()) {
            session()->flash('error', 'Este trabajador ya tiene un usuario asignado.');
            return;
        }

        try {
            DB::transaction(function () use ($trabajador) {
                // Crear usuario
                $user = User::create([
                    'usuario' => $this->username,
                    'contrasena' => Hash::make($this->password),
                    'estado' => 'activo',
                    'id_persona' => $trabajador->id_persona,
                    'id_rol' => $this->rolSeleccionado,
                ]);

                // Asignar rol
                // Asignar permisos adicionales (opcional)
                if (!empty($this->permisosSeleccionados)) {
                    $user->givePermissionTo($this->permisosSeleccionados);
                }

                session()->flash('success', 'Usuario registrado correctamente.');
                $this->reset(['trabajadorSeleccionado', 'username', 'password', 'rolSeleccionado', 'permisosSeleccionados']);
            });
        } catch (\Exception $e) {
            session()->flash('error', 'Error al registrar el usuario: ' . $e->getMessage());
            Log::error('Error al crear usuario', ['error' => $e->getMessage()]);
        }
    }

    #[\Livewire\Attributes\On('abrirModalRol')]
    public function abrirModalRol($userId)
    {
        $this->usuarioSeleccionado = User::findOrFail($userId);

        // Cargar valores actuales
        $this->rolNuevo = $this->usuarioSeleccionado->rol?->id_rol;
        $this->usernameEdit = $this->usuarioSeleccionado->usuario;
        $this->estadoEdit = $this->usuarioSeleccionado->estado;

        $this->modalRol = true;
    }

    /* // ✅ ACTUALIZAR ROLES CUANDO SE CREA UNO NUEVO
    #[\Livewire\Attributes\On('rolesCreated')]
    public function actualizarRoles()
    {
        $this->roles = Roles::where('estado', 'activo')->get();
    } */

    #[\Livewire\Attributes\On('rolesUpdated')]
    public function refresh()
    {
        //$this->roles = Roles::where('estado', 'activo')->get();
        $this->cargarDatos();

        // ✅ Forzar carga de datos cada vez que se monte el componente
        if (session()->has('roles_updated')) {
            $this->cargarDatos();
            session()->forget('roles_updated');
        }
    }

    public function guardarRol()
    {
        if (!$this->usuarioSeleccionado) return;

        try {
            $data = [
                'usuario' => $this->usernameEdit,
                'id_rol' => $this->rolNuevo,
                'estado' => $this->estadoEdit,
            ];

            // Solo actualizar contraseña si se ingresó
            if (!empty($this->passwordEdit)) {
                $data['contrasena'] = Hash::make($this->passwordEdit);
            }

            $this->usuarioSeleccionado->update($data);

            $this->modalRol = false;
            session()->flash('success', '✅ Usuario actualizado correctamente.');
            $this->dispatch("userUpdated");
        } catch (\Exception $e) {
            session()->flash('error', '❌ Error al actualizar el usuario: ' . $e->getMessage());
            Log::error('Error al actualizar usuario', ['error' => $e->getMessage()]);
        }
    }
}
