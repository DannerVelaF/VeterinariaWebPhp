<?php

namespace App\Livewire\Mantenimiento\Usuarios;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class Roles extends Component
{
    public $roles = [];
    public $nombreRol;

    public function mount()
    {
        $this->roles = Role::all();
    }

    public function guardar()
    {
        $this->validate([
            'nombreRol' => 'required|string|unique:roles,name',
        ]);

        try {
            DB::transaction(function () {
                Role::create([
                    'name' => $this->nombreRol,
                    'estado' => 'activo',
                ]);
            });

            session()->flash('success', 'Rol creado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Hubo un problema al crear el rol.');
        }

        $this->reset('nombreRol');
        $this->roles = Role::all();
    }

    public function cambiarEstado($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $rol = Role::findOrFail($id);
                $rol->estado = $rol->estado === 'activo' ? 'inactivo' : 'activo';
                $rol->save();
            });

            session()->flash('success', 'Estado actualizado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'No se pudo cambiar el estado.');
        }

        $this->roles = Role::all();
    }

    public function render()
    {
        return view('livewire.mantenimiento.usuarios.roles');
    }
}
