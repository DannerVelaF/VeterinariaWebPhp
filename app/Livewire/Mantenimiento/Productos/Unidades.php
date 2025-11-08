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
    public $contieneUnidades = false;

    public function mount()
    {
        $this->unidades = UnidadesModel::all();
    }

    public function guardar()
    {
        $this->validate([
            'nombre' => 'required|string|max:255',
            'contieneUnidades' => 'boolean',
        ]);

        try {
            // Validar que no exista una unidad con el mismo nombre (case insensitive)
            $nombreNormalizado = strtolower(trim($this->nombre));
            $existeUnidad = UnidadesModel::whereRaw('LOWER(TRIM(nombre_unidad)) = ?', [$nombreNormalizado])
                ->exists();

            if ($existeUnidad) {
                $this->dispatch('notify', title: 'Error', description: 'Ya existe una unidad con ese nombre.', type: 'error');
                return;
            }

            DB::transaction(function () {
                $unidad = UnidadesModel::create([
                    'nombre_unidad' => $this->nombre,
                    'contiene_unidades' => $this->contieneUnidades,
                ]);
            });

            $this->unidades = UnidadesModel::all(); // refrescar lista
            $this->nombre = null;
            $this->contieneUnidades = false;
            $this->dispatch('unidadesUpdated');
            $this->dispatch('notify', title: 'Success', description: 'Unidad creada correctamente.', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error al crear la unidad: ' . $e->getMessage(), type: 'error');
            Log::error('Error al crear la unidad', ['error' => $e->getMessage()]);
        }
    }

    // ✅ NUEVO MÉTODO PARA ACTUALIZAR EL CHECKBOX
    public function actualizarContieneUnidades($id, $valor)
    {
        try {
            DB::transaction(function () use ($id, $valor) {
                $unidad = UnidadesModel::findOrFail($id);
                $unidad->update([
                    'contiene_unidades' => $valor,
                    'fecha_actualizacion' => now(),
                ]);
            });

            $this->unidades = UnidadesModel::all(); // refrescar lista
            $this->dispatch('unidadesUpdated');
            $this->dispatch('notify', title: 'Success', description: 'Unidad actualizada correctamente.', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error al actualizar la unidad: ' . $e->getMessage(), type: 'error');
            Log::error('Error al actualizar la unidad', ['error' => $e->getMessage()]);
        }
    }

    public function cambiarEstado($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $unidad = UnidadesModel::findOrFail($id);
                if ($unidad->estado == 'activo') {
                    $unidad->update(['estado' => 'inactivo']);
                } else {
                    $unidad->update(['estado' => 'activo']);
                }
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
