<?php

namespace App\Livewire\Mantenimiento\Productos;

use Illuminate\Support\Facades\DB;
use App\Models\Unidades as UnidadesModel;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Unidades extends Component
{
    public $unidades = [];
    public $nombre;

    public function mount()
    {
        $this->unidades = UnidadesModel::all();
    }

    public function guardar()
    {
        $this->validate([
            'nombre' => 'required|string|max:255',
        ]);

        try {
            DB::transaction(function () {
                $unidad = UnidadesModel::create([
                    'nombre_unidad' => $this->nombre,
                ]);
            });

            $this->unidades = UnidadesModel::all(); // refrescar lista
            $this->nombre = null;
            $this->dispatch('unidadesUpdated');
            $this->dispatch('notify', title: 'Success', description: 'Unidad creada correctamente.', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error al crear la unidad: ' . $e->getMessage(), type: 'error');
            Log::error('Error al crear la unidad', ['error' => $e->getMessage()]);
        }
    }

    public function eliminar($id)
    {
        try {
            DB::transaction(function () use ($id) {
                UnidadesModel::findOrFail($id)->delete();
            });

            $this->unidades = UnidadesModel::all(); // refrescar lista
            $this->dispatch('unidadesUpdated');
            $this->dispatch('notify', title: 'Success', description: 'Unidad eliminada correctamente.', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error al eliminar la unidad: ' . $e->getMessage(), type: 'error');
            Log::error('Error al eliminar la unidad', ['error' => $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.mantenimiento.productos.unidades');
    }
}
