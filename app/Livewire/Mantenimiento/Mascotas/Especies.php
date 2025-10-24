<?php

namespace App\Livewire\Mantenimiento\Mascotas;

use App\Models\Especie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Especies extends Component
{
    public $modalEditar = false;
    public $especieSeleccionado;

    public $especie = [
        'nombre_especie' => '',
        'descripcion' => '',
    ];

    public $especieEditar = [
        'id_especie' => null,
        'nombre_especie' => '',
        'descripcion' => '',
    ];

    public $mensajes = [
        'especie.nombre_especie.required' => 'El nombre de la especie es obligatorio.',
        'especie.nombre_especie.max' => 'El nombre no puede tener más de 100 caracteres.',
        'especie.descripcion.max' => 'La descripción no puede tener más de 1000 caracteres.',
    ];

    public function guardar()
    {
        // Validación
        $validatedData = $this->validate([
            'especie.nombre_especie' => 'required|string|max:255',
            'especie.descripcion' => 'nullable|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($validatedData) {
                Especie::create([
                    'nombre_especie' => $validatedData['especie']['nombre_especie'],
                    'descripcion' => $validatedData['especie']['descripcion'] ?? null,
                ]);
            });

            // Si llegamos aquí, todo se guardó correctamente
            $this->dispatch('especieRegistrado');
            $this->dispatch('notify', title: 'Success', description: 'Especie registrada con éxito', type: 'success');
            $this->resetForm();
            $this->dispatch('especieUpdated');
        } catch (\Exception $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error al registrar la especie: ' . $e->getMessage(), type: 'error');
            Log::error('Error al registrar especie', ['error' => $e->getMessage()]);
        }
    }

    public function resetForm()
    {
        $this->especie = [
            'nombre_especie' => '',
            'descripcion' => '',
        ];
    }

    #[\Livewire\Attributes\On('abrirModalEspecie')]
    public function abrirModalEditar($especieId)
    {
        $this->especieSeleccionado = Especie::findOrFail($especieId);

        $this->especieEditar = [
            'id_especie' => $especieId,
            'nombre_especie' => $this->especieSeleccionado->nombre_especie,
            'descripcion' => $this->especieSeleccionado->descripcion,
        ];

        $this->modalEditar = true;
    }

    public function guardarEdicion()
    {
        if (! $this->especieSeleccionado) return;

        // Validación
        $validated = $this->validate([
            'especieEditar.nombre_especie' => 'required|string|max:255',
            'especieEditar.descripcion' => 'nullable|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($validated) {

                $this->especieSeleccionado->update([
                    'nombre_especie' => $validated['especieEditar']['nombre_especie'],
                    'descripcion' => $validated['especieEditar']['descripcion'] ?? null,
                    'fecha_actualizacion' => now(),
                ]);
            });

            // Si llegamos aquí, todo se guardó correctamente
            $this->dispatch('notify', title: 'Success', description: 'Especie actualizada con éxito', type: 'success');
            $this->modalEditar = false;
            $this->dispatch('especieUpdated');
        } catch (\Exception $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error al actualizar la especie: ' . $e->getMessage(), type: 'error');
            Log::error('Error al actualizar especie', ['error' => $e->getMessage()]);
        }
    }

    public function cerrarModal()
    {
        $this->modalEditar = false;
        $this->especieSeleccionado = null;
        $this->especieEditar = [
            'id_especie' => null,
            'nombre_especie' => '',
            'descripcion' => '',
        ];
    }

    public function render()
    {
        return view('livewire.mantenimiento.mascotas.especies');
    }
}
