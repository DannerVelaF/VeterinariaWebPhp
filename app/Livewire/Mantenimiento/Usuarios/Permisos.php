<?php

namespace App\Livewire\Mantenimiento\Usuarios;

use Livewire\Component;
use App\Models\Permiso;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Permisos extends Component
{
    public $permisos = [];
    public $nombrePermiso;

    public function mount()
    {
        $this->permisos = Permiso::all();
    }

    public function guardar()
    {
        $this->validate([
            'nombrePermiso' => 'required|string|unique:permisos,nombre_permiso',
        ]);

        try {
            DB::transaction(function () {
                Permiso::create([
                    'nombre_permiso' => $this->nombrePermiso,
                    'estado' => 'activo',
                ]);
            });

            session()->flash('success', 'Permiso creado correctamente.');
            $this->reset('nombrePermiso');
            $this->permisos = Permiso::all();
            $this->dispatch("permisosUpdated");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear permiso', ['error' => $e->getMessage()]);
            session()->flash('error', 'Hubo un problema al crear el permiso.');
        }
    }

    public function cambiarEstado($id)
    {
        try {
            $permiso = Permiso::findOrFail($id);
            $permiso->estado = $permiso->estado === 'activo' ? 'inactivo' : 'activo';
            $permiso->save();

            session()->flash('success', 'Estado actualizado correctamente.');
            $this->permisos = Permiso::all();
            $this->dispatch("permisosUpdated");
        } catch (\Exception $e) {
            Log::error('Error al cambiar estado del permiso', ['error' => $e->getMessage()]);
            session()->flash('error', 'No se pudo cambiar el estado.');
        }
    }
    #[\Livewire\Attributes\On('rolesUpdated')]

    public function render()
    {
        return view('livewire.mantenimiento.usuarios.permisos');
    }
}
