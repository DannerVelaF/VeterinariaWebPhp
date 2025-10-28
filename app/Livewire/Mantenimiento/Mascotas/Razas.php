<?php

namespace App\Livewire\Mantenimiento\Mascotas;

use App\Models\Especie;
use App\Models\Raza;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class Razas extends Component
{
    protected $listeners = [
        'especiesUpdated' => 'refreshData',
    ];

    public $raza = [
        "nombre_raza" => "",
        "descripcion" => "",
        "id_especie" => "",
    ];

    public $especies = [];
    public $modalEditar = false;
    public $razaSeleccionado;

    public $razaEditar = [
        'id_raza' => null,
        'nombre_raza' => '',
        'descripcion' => '',
        'id_especie' => '',
    ];

    public function mount()
    {
        $this->especies = Especie::orderBy('nombre_especie', 'asc')->get();
    }

    #[\Livewire\Attributes\On('especiesUpdated')]
    public function refreshData()
    {
        $this->especies = Especie::where('estado', 'activo')->get();
    }

    // Validación en tiempo real para registro
    public function updated($propertyName)
    {
        // Validar campos de raza en registro
        if (str_starts_with($propertyName, 'raza.')) {
            $this->validateOnly($propertyName, [
                'raza.nombre_raza' => 'required|string|max:100|unique:razas,nombre_raza',
                'raza.descripcion' => 'nullable|string|max:1000',
                'raza.id_especie' => 'required|exists:especies,id_especie',
            ], $this->getValidationMessages());
        }

        // Validar campos de raza en edición
        if (str_starts_with($propertyName, 'razaEditar.')) {
            $this->validateOnly($propertyName, [
                'razaEditar.nombre_raza' => 'required|string|max:100|unique:razas,nombre_raza,' . ($this->razaEditar['id_raza'] ?? 'NULL') . ',id_raza',
                'razaEditar.descripcion' => 'nullable|string|max:1000',
                'razaEditar.id_especie' => 'required|exists:especies,id_especie',
            ], $this->getValidationMessages());
        }
    }

    // Mensajes de validación centralizados
    private function getValidationMessages()
    {
        return [
            'raza.nombre_raza.required' => 'El nombre de la raza es obligatorio.',
            'raza.nombre_raza.string' => 'El nombre debe ser texto.',
            'raza.nombre_raza.max' => 'El nombre no puede tener más de 100 caracteres.',
            'raza.nombre_raza.unique' => 'Esta raza ya está registrada.',
            'raza.descripcion.string' => 'La descripción debe ser texto.',
            'raza.descripcion.max' => 'La descripción no puede tener más de 1000 caracteres.',
            'raza.id_especie.required' => 'Debe seleccionar una especie.',
            'raza.id_especie.exists' => 'La especie seleccionada no es válida.',

            'razaEditar.nombre_raza.required' => 'El nombre de la raza es obligatorio.',
            'razaEditar.nombre_raza.string' => 'El nombre debe ser texto.',
            'razaEditar.nombre_raza.max' => 'El nombre no puede tener más de 100 caracteres.',
            'razaEditar.nombre_raza.unique' => 'Esta raza ya está registrada.',
            'razaEditar.descripcion.string' => 'La descripción debe ser texto.',
            'razaEditar.descripcion.max' => 'La descripción no puede tener más de 1000 caracteres.',
            'razaEditar.id_especie.required' => 'Debe seleccionar una especie.',
            'razaEditar.id_especie.exists' => 'La especie seleccionada no es válida.',
        ];
    }

    public function guardarRaza()
    {
        $validatedData = $this->validate([
            'raza.nombre_raza' => 'required|string|max:100|unique:razas,nombre_raza',
            'raza.descripcion' => 'nullable|string|max:1000',
            'raza.id_especie' => 'required|exists:especies,id_especie',
        ], $this->getValidationMessages());

        try {
            DB::transaction(function () use ($validatedData) {
                Raza::create([
                    'nombre_raza' => $validatedData['raza']['nombre_raza'],
                    'descripcion' => $validatedData['raza']['descripcion'] ?? null,
                    'id_especie' => $validatedData['raza']['id_especie'],
                ]);
            });

            $this->dispatch('razaRegistrada');
            $this->dispatch('notify', title: 'Success', description: 'Raza registrada con éxito', type: 'success');
            $this->resetForm();
            $this->dispatch('razasUpdated');
        } catch (\Exception $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error al registrar la raza: ' . $e->getMessage(), type: 'error');
            Log::error('Error al registrar raza', ['error' => $e->getMessage()]);
        }
    }

    public function resetForm()
    {
        $this->raza = [
            'nombre_raza' => '',
            'descripcion' => '',
            'id_especie' => '',
        ];

        // Limpiar errores de validación
        $this->resetErrorBag();
        $this->resetValidation();
    }

    #[\Livewire\Attributes\On('abrirModalRaza')]
    public function abrirModalEditar($razaId)
    {
        $this->razaSeleccionado = Raza::findOrFail($razaId);

        $this->razaEditar = [
            'id_raza' => $this->razaSeleccionado->id_raza,
            'nombre_raza' => $this->razaSeleccionado->nombre_raza,
            'descripcion' => $this->razaSeleccionado->descripcion,
            'id_especie' => $this->razaSeleccionado->id_especie,
        ];

        $this->modalEditar = true;
    }

    public function guardarEdicion()
    {
        $validatedData = Validator::make($this->razaEditar, [
            'nombre_raza' => 'required|string|max:100|unique:razas,nombre_raza,' . $this->razaEditar['id_raza'] . ',id_raza',
            'descripcion' => 'nullable|string|max:1000',
            'id_especie' => 'required|exists:especies,id_especie',
        ], $this->getValidationMessages())->validate();

        try {
            DB::transaction(function () use ($validatedData) {
                $raza = Raza::findOrFail($this->razaEditar['id_raza']);
                $raza->update([
                    'nombre_raza' => $validatedData['nombre_raza'],
                    'descripcion' => $validatedData['descripcion'] ?? null,
                    'id_especie' => $validatedData['id_especie'],
                ]);
            });

            $this->dispatch('notify', title: 'Success', description: 'Raza actualizada con éxito', type: 'success');
            $this->cerrarModal();
            $this->dispatch('razasUpdated');
        } catch (Exception $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error al actualizar la raza: ' . $e->getMessage(), type: 'error');
            Log::error('Error al actualizar raza', ['error' => $e->getMessage()]);
        }
    }

    public function cerrarModal()
    {
        $this->modalEditar = false;
        $this->razaSeleccionado = null;
        $this->razaEditar = [
            'id_raza' => null,
            'nombre_raza' => '',
            'descripcion' => '',
            'id_especie' => '',
        ];

        // Limpiar errores de validación
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function especie($especieId)
    {
        $especie = Especie::find($especieId);
        return $especie ? $especie->nombre_especie : 'Especie no encontrada';
    }

    public function render()
    {
        $razas = Raza::with('especie')->get();
        return view('livewire.mantenimiento.mascotas.razas', compact('razas'));
    }
}
