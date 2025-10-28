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

    // Validación en tiempo real
    public function updated($propertyName)
    {
        // Validar campos de especie en registro
        if (str_starts_with($propertyName, 'especie.')) {
            $this->validateOnly($propertyName, [
                'especie.nombre_especie' => 'required|string|max:255|unique:especies,nombre_especie',
                'especie.descripcion' => 'nullable|string|max:1000',
            ], $this->getValidationMessages());
        }

        // Validar campos de especie en edición
        if (str_starts_with($propertyName, 'especieEditar.')) {
            $this->validateOnly($propertyName, [
                'especieEditar.nombre_especie' => 'required|string|max:255|unique:especies,nombre_especie,' . ($this->especieEditar['id_especie'] ?? 'NULL') . ',id_especie',
                'especieEditar.descripcion' => 'nullable|string|max:1000',
            ], $this->getValidationMessages());
        }
    }

    // Mensajes de validación centralizados
    private function getValidationMessages()
    {
        return [
            // Mensajes para registro
            'especie.nombre_especie.required' => 'El nombre de la especie es obligatorio.',
            'especie.nombre_especie.string' => 'El nombre debe ser texto.',
            'especie.nombre_especie.max' => 'El nombre no puede tener más de 255 caracteres.',
            'especie.nombre_especie.unique' => 'Esta especie ya está registrada.',
            'especie.descripcion.string' => 'La descripción debe ser texto.',
            'especie.descripcion.max' => 'La descripción no puede tener más de 1000 caracteres.',

            // Mensajes para edición
            'especieEditar.nombre_especie.required' => 'El nombre de la especie es obligatorio.',
            'especieEditar.nombre_especie.string' => 'El nombre debe ser texto.',
            'especieEditar.nombre_especie.max' => 'El nombre no puede tener más de 255 caracteres.',
            'especieEditar.nombre_especie.unique' => 'Esta especie ya está registrada.',
            'especieEditar.descripcion.string' => 'La descripción debe ser texto.',
            'especieEditar.descripcion.max' => 'La descripción no puede tener más de 1000 caracteres.',
        ];
    }

    public function guardar()
    {
        // Validación
        $validatedData = $this->validate([
            'especie.nombre_especie' => 'required|string|max:255|unique:especies,nombre_especie',
            'especie.descripcion' => 'nullable|string|max:1000',
        ], $this->getValidationMessages());

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

        // Limpiar errores de validación
        $this->resetErrorBag();
        $this->resetValidation();
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
            'especieEditar.nombre_especie' => 'required|string|max:255|unique:especies,nombre_especie,' . $this->especieEditar['id_especie'] . ',id_especie',
            'especieEditar.descripcion' => 'nullable|string|max:1000',
        ], $this->getValidationMessages());

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
            $this->cerrarModal();
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

        // Limpiar errores de validación
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.mantenimiento.mascotas.especies');
    }
}
