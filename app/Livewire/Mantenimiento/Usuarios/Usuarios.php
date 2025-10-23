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
    public $dniTrabajador = null;
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

    public function updatedTrabajadorSeleccionado($value)
    {
        $this->trabajadorSeleccionado = $value;

        $trabajador = Trabajador::find($value);
        if ($trabajador) {
            $this->dniTrabajador = $trabajador->persona->numero_documento;
        } else {
            $this->dniTrabajador = null;
        }
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
            // 'password' => [
            //     'required',
            //     'string',
            //     'min:8',
            //     'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\w\d\s]).{8,}$/',
            // ],
            'rolSeleccionado' => 'required|exists:roles,id_rol',
        ], [
            "username.unique" => "El usuario ya existe.",
            "username.required" => "El campo 'Usuario' es requerido.",
            // "password.required" => "El campo 'Contraseña' es requerido.",
            "trabajadorSeleccionado.required" => "El campo 'Trabajador' es requerido.",
            "rolSeleccionado.required" => "El campo 'Rol' es requerido.",
            // 'password.regex' => 'La contraseña debe contener al menos una letra mayúscula, una minúscula, un número y un carácter especial.',
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
                    'contrasena' => Hash::make($trabajador->persona->numero_documento),
                    'estado' => 'activo',
                    'id_persona' => $trabajador->id_persona,
                    'id_rol' => $this->rolSeleccionado,
                ]);

                // Asignar rol
                // Asignar permisos adicionales (opcional)
                if (!empty($this->permisosSeleccionados)) {
                    $user->givePermissionTo($this->permisosSeleccionados);
                }

                $this->dispatch('notify', title: 'Success', description: 'Usuario registrado correctamente.', type: 'success');
                $this->reset(['trabajadorSeleccionado', 'username', 'password', 'rolSeleccionado', 'permisosSeleccionados']);
                $this->dniTrabajador = null;
            });
        } catch (\Exception $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error al registrar el usuario: ' . $e->getMessage(), type: 'error');
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


    public function resetContrasena()
    {
        if (!$this->usuarioSeleccionado) return;

        try {
            $this->usuarioSeleccionado->update([
                'contrasena' => Hash::make($this->usuarioSeleccionado->trabajador->dni), // contraseña = DNI
                'ultimo_login' => null, // resetea la fecha de último login
            ]);

            $this->dispatch('notify', title: 'Success', description: 'Contraseña reseteada correctamente. La nueva contraseña es el DNI.', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error al resetear la contraseña: ' . $e->getMessage(), type: 'error');
            Log::error('Error al resetear contraseña', ['error' => $e->getMessage()]);
        }
    }


    public function guardarRol()
    {
        if (!$this->usuarioSeleccionado) return;

        $rules = [];
        $messages = [];

        // Solo validar si el username cambió
        if ($this->usernameEdit !== $this->usuarioSeleccionado->usuario) {
            $rules['usernameEdit'] = 'required|string|unique:usuarios,usuario';
            $messages['usernameEdit.unique'] = 'Este nombre de usuario ya existe.';
            $messages['usernameEdit.required'] = 'El campo Usuario es requerido.';
        }

        $this->validate($rules, $messages);

        try {
            $data = [
                'usuario' => $this->usernameEdit,
                'id_rol' => $this->rolNuevo,
                'estado' => $this->estadoEdit,
            ];

            $this->usuarioSeleccionado->update($data);

            $this->modalRol = false;
            $this->dispatch('notify', title: 'Success', description: 'Usuario actualizado correctamente.', type: 'success');
            $this->dispatch("userUpdated");
        } catch (\Exception $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error al actualizar el usuario: ' . $e->getMessage(), type: 'error');
            Log::error('Error al actualizar usuario', ['error' => $e->getMessage()]);
        }
    }
}
