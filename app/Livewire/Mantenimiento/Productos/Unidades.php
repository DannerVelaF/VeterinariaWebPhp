<?php

namespace App\Livewire\Mantenimiento\Productos;

use Illuminate\Support\Facades\DB;
use App\Models\Unidades as UnidadesModel;
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
            session()->flash('success', 'Unidad registrada con éxito');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al registrar la unidad: ' . $e->getMessage());
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
            session()->flash('success', 'Unidad eliminada con éxito');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar la unidad: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.mantenimiento.productos.unidades');
    }
}
