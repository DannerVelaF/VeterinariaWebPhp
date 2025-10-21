<?php

namespace App\Livewire\Mantenimiento\Usuarios;

use App\Models\Permiso;
use App\Models\Roles as Rol;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Roles extends Component
{
    protected $listeners = ["permisosUpdated"];
    public $roles = [];
    public $permisos = [];
    public $nombreRol;
    public $rolSeleccionado; // Para el modal de permisos
    public $permisosSeleccionados = [];
    public $permisosActivos = [];
    public $modalPermisos = false; // controla si se muestra el modal

    public function mount()
    {
        $this->roles = Rol::all();
        $this->permisosActivos = Permiso::where('estado', 'activo')->get();
    }

    public function guardar()
    {
        $this->validate([
            'nombreRol' => 'required|string|unique:roles,nombre_rol',
        ]);

        try {
            DB::transaction(function () {
                Rol::create([
                    'nombre_rol' => $this->nombreRol,
                    'estado' => 'activo',
                ]);
            });

            // ✅ Guardar en sesión para detectar en el otro componente
            session()->put('roles_updated', true);

            $this->dispatch('notify', title: 'Success', description: 'Rol creado correctamente.', type: 'success');

            $this->reset('nombreRol');
            $this->roles = Rol::all();

            $this->dispatch('roles-created-global'); // Notificar a otros componentes
            $this->dispatch('rolesUpdated');
        } catch (\Exception $e) {
            Log::error('Error al crear el rol', ['error' => $e->getMessage()]);
            session()->flash('error', 'Hubo un problema al crear el rol.');
        }
    }

    public function cambiarEstado($id)
    {
        try {
            $rol = Rol::findOrFail($id);
            $rol->estado = $rol->estado === 'activo' ? 'inactivo' : 'activo';
            $rol->save();


            $usuarios = User::where('id_rol', $id)->get();
            foreach ($usuarios as $usuario) {
                $usuario->update(['id_rol' => null]);
            }

            $this->dispatch('notify', title: 'Success', description: 'Estado actualizado correctamente.', type: 'success');

            $this->roles = Rol::all();

            // ✅ DISPARAR EL MISMO EVENTO GLOBAL cuando cambia el estado
            $this->dispatch('roles-created-global');
            $this->dispatch('rolesUpdated');
        } catch (\Exception $e) {
            Log::error('Error al cambiar estado del rol', ['error' => $e->getMessage()]);
            $this->dispatch('notify', title: 'Error', description: 'No se pudo cambiar el estado.', type: 'error');
        }
    }

    // Abrir modal para editar permisos del rol
    public function editarPermisos($id)
    {
        $this->rolSeleccionado = Rol::findOrFail($id);
        $this->permisos = Permiso::all();

        // Seleccionados según los asignados al rol
        $this->permisosSeleccionados = $this->rolSeleccionado->permisos->pluck('id_permiso')->toArray();

        $this->modalPermisos = true;
        $this->dispatch('rolesUpdated');
    }

    // Guardar cambios de permisos
    public function guardarPermisos()
    {
        if (!$this->rolSeleccionado) return;

        try {
            $this->rolSeleccionado->permisos()->sync($this->permisosSeleccionados);
            $this->modalPermisos = false;

            $this->dispatch('notify', title: 'Success', description: 'Permisos guardados correctamente.', type: 'success');


            $this->dispatch('roles-created-global');
            $this->dispatch('rolesUpdated');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar permisos: ' . $e->getMessage());
        }
    }

    #[\Livewire\Attributes\On('rolesCreated')]
    public function handleRolesCreated(): void
    {
        $this->refresh(); // Recargar datos
    }

    #[\Livewire\Attributes\On('permisosUpdated')]
    public function refresh()
    {
        $this->roles = Rol::where('estado', 'activo')->get();
        $this->permisos = Permiso::where('estado', 'activo')->get();
    }

    public function render()
    {
        return view('livewire.mantenimiento.usuarios.roles');
    }
}
