<?php

namespace App\Livewire\Mantenimiento\Mascotas;

use App\Http\Requests\RazaRequest;
use App\Models\Especie;
use App\Models\Raza;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Illuminate\Support\Str;

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
    ]; // array para editar

    public function mount()
    {
        $this->especies = Especie::orderBy('nombre_especie', 'asc')->get();
    }

    #[\Livewire\Attributes\On('especiesUpdated')]
    public function refreshData()
    {
        $this->especies = Especie::where('estado', 'activo')->get();
    }

    public $messajes = [
        'raza.nombre_raza.required' => 'El nombre de la raza es obligatorio.',
        'raza.nombre_raza.max' => 'El nombre no puede tener más de 100 caracteres.',
        'raza.descripcion.max' => 'La descripción no puede tener más de 1000 caracteres.',
        'raza.id_especie.required' => 'Debe seleccionar una especie.',
        'raza.id_especie.exists' => 'La especie seleccionada no es válida.',
    ];

    public function guardarRaza()
    {
        $validatedData = $this->validate([
            'raza.nombre_raza' => 'required|string|max:100',
            'raza.descripcion' => 'nullable|string|max:1000',
            'raza.id_especie' => 'required|exists:especies,id_especie',
        ]);

        try {
            DB::transaction(function () use ($validatedData) {
                Raza::create([
                    'nombre_raza' => $validatedData['raza']['nombre_raza'],
                    'descripcion' => $validatedData['raza']['descripcion'] ?? null,
                    'id_especie' => $validatedData['raza']['id_especie'],
                ]);
            });

            // Si llegamos aquí, todo se guardó correctamente
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
            'nombre_raza' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:1000',
            'id_especie' => 'required|exists:especies,id_especie',
        ])->validate();

        try {
            DB::transaction(function () use ($validatedData) {
                $raza = Raza::findOrFail($this->razaEditar['id_raza']);
                $raza->update([
                    'nombre_raza' => $validatedData['nombre_raza'],
                    'descripcion' => $validatedData['descripcion'] ?? null,
                    'id_especie' => $validatedData['id_especie'],
                ]);
            });

            // Si llegamos aquí, todo se guardó correctamente
            $this->dispatch('notify', title: 'Success', description: 'Raza actualizada con éxito', type: 'success');
            $this->modalEditar = false;
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
