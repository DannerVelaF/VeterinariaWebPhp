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

            $this->dispatch('notify', title: 'Success', description: 'Permiso creado correctamente.', type: 'success');
            $this->reset('nombrePermiso');
            $this->permisos = Permiso::all();
            $this->dispatch("permisosUpdated");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear permiso', ['error' => $e->getMessage()]);
            $this->dispatch('notify', title: 'Error', description: 'Hubo un problema al crear el permiso.', type: 'error');
        }
    }

    public function cambiarEstado($id)
    {
        try {
            $permiso = Permiso::findOrFail($id);
            $permiso->estado = $permiso->estado === 'activo' ? 'inactivo' : 'activo';
            $permiso->save();

            $this->dispatch('notify', title: 'Success', description: 'Estado actualizado correctamente.', type: 'success');
            $this->permisos = Permiso::all();
            $this->dispatch("permisosUpdated");
        } catch (\Exception $e) {
            Log::error('Error al cambiar estado del permiso', ['error' => $e->getMessage()]);
            $this->dispatch('notify', title: 'Error', description: 'No se pudo cambiar el estado.', type: 'error');
        }
    }
    #[\Livewire\Attributes\On('rolesUpdated')]

    public function render()
    {
        return view('livewire.mantenimiento.usuarios.permisos');
    }
}
