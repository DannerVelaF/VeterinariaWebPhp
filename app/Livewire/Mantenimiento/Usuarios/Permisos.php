<?php

namespace App\Livewire\Mantenimiento\Usuarios;

use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class Permisos extends Component
{
    public $permisos = [];
    public $nombrePermiso;

    public function mount()
    {
        $this->permisos = Permission::all();
    }

    public function guardar()
    {
        $this->validate([
            'nombrePermiso' => 'required|string|unique:permissions,name',
        ]);

        try {
            DB::transaction(function () {
                Permission::create([
                    'name' => $this->nombrePermiso,
                    'estado' => 'activo',
                ]);
            });

            session()->flash('success', 'Permiso creado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Hubo un problema al crear el permiso.');
        }

        $this->reset('nombrePermiso');
        $this->permisos = Permission::all();
    }

    public function cambiarEstado($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $permiso = Permission::findOrFail($id);
                $permiso->estado = $permiso->estado === 'activo' ? 'inactivo' : 'activo';
                $permiso->save();
            });

            session()->flash('success', 'Estado actualizado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'No se pudo cambiar el estado.');
        }

        $this->permisos = Permission::all();
    }

    public function render()
    {
        return view('livewire.mantenimiento.usuarios.permisos');
    }
}
