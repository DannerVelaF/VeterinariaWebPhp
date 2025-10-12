<?php

namespace App\Livewire\Mantenimiento\Mascotas;

use App\Models\Raza;
use App\Models\Especie;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class Razas extends Component
{
    protected $listeners = ['especiesUpdated' => 'refreshData', 'razaRegistrada' => '$refresh'];

    public $raza = [
        'id_especie' => '',
        'nombre_raza' => '',
        'descripcion' => '',
    ];

    public $especies = [];
    public $modalEditar = false;

    public $razaEditar = [
        'id_raza' => null,
        'nombre_raza' => '',
        'descripcion' => '',
        'id_especie' => '',
    ];

    public function mount()
    {
        $this->especies = Especie::all();
    }

    #[\Livewire\Attributes\On('especiesUpdated')]
    public function refreshData()
    {
        $this->especies = Especie::all();
    }

    public $mensajes = [
        'raza.nombre_raza.required' => 'El nombre de la raza es obligatorio.',
        'raza.nombre_raza.max' => 'El nombre no puede tener más de 255 caracteres.',
        'raza.nombre_raza.unique' => 'Esta raza ya existe.',
        'raza.descripcion.max' => 'La descripción no puede tener más de 500 caracteres.',
        'raza.id_especie.required' => 'Debe seleccionar una especie.',
        'raza.id_especie.exists' => 'La especie seleccionada no es válida.',
    ];

    /**
     * Guardar una nueva raza
     */
    public function guardar()
    {
        $validatedData = $this->validate([
            'raza.nombre_raza' => 'required|string|max:255|unique:razas,nombre_raza',
            'raza.descripcion' => 'nullable|string|max:500',
            'raza.id_especie' => 'required|exists:especies,id_especie',
        ]);

        try {
            DB::transaction(function () use ($validatedData) {
                $raza = Raza::create([
                    'nombre_raza' => $validatedData['raza']['nombre_raza'],
                    'descripcion' => $validatedData['raza']['descripcion'] ?? null,
                    'id_especie' => $validatedData['raza']['id_especie'],
                    'fecha_registro' => now(),
                ]);
            });

            $this->dispatch('razaRegistrada');
            session()->flash('success', '✅ Raza registrada correctamente.');
            $this->resetForm();
            $this->dispatch('razaUpdated');
        } catch (Exception $e) {
            Log::error('Error al registrar la raza', ['error' => $e->getMessage()]);
            session()->flash('error', 'Error al registrar la raza: ' . $e->getMessage());
        }
    }

    /**
     * Resetear formulario
     */
    public function resetForm()
    {
        $this->raza = [
            'nombre_raza' => '',
            'descripcion' => '',
            'id_especie' => '',
        ];
        $this->mount();
    }

    #[\Livewire\Attributes\On('editarRaza')]
    public function editarRaza($razaId)
    {
        $raza = Raza::findOrFail($razaId);
        $this->razaEditar = [
            'id_raza' => $raza->id_raza,
            'nombre_raza' => $raza->nombre_raza,
            'descripcion' => $raza->descripcion,
            'id_especie' => $raza->id_especie,
        ];
        $this->modalEditar = true;
    }

    public function actualizarRaza()
    {
        if (!$this->razaEditar['id_raza']) return;

        $validatedData = Validator::make($this->razaEditar, [
            'nombre_raza' => 'required|string|max:255|unique:razas,nombre_raza,' . $this->razaEditar['id_raza'] . ',id_raza',
            'descripcion' => 'nullable|string|max:500',
            'id_especie' => 'required|exists:especies,id_especie',
        ], $this->mensajes)->validate();

        try {
            DB::transaction(function () use ($validatedData) {
                $raza = Raza::findOrFail($this->razaEditar['id_raza']);
                $raza->update([
                    'nombre_raza' => $validatedData['nombre_raza'],
                    'descripcion' => $validatedData['descripcion'] ?? null,
                    'id_especie' => $validatedData['id_especie'],
                ]);
            });

            session()->flash('success', '✅ Raza actualizada correctamente.');
            $this->modalEditar = false;
            $this->dispatch('razaUpdated');
            $this->dispatch('razaRegistrada');
            $this->resetForm();
            $this->dispatch('razaUpdated');
        } catch (Exception $e) {
            Log::error('Error al actualizar la raza', ['error' => $e->getMessage()]);
            session()->flash('error', 'Error al actualizar la raza: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.mantenimiento.mascotas.razas', [
            'razas' => Raza::with('especie')->orderBy('nombre_raza')->get(),
        ]);
    }
}