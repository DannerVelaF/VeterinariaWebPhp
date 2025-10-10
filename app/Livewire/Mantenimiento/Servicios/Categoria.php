<?php

namespace App\Livewire\Mantenimiento\Servicios;

use App\Models\CategoriaServicio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Categoria extends Component
{

    public $modalEditar = false;
    public $categoriaSeleccionado;

    public $categoria = [
        'nombre_categoria_servicio' => '',
        'descripcion' => '',
    ];

    public $categoriaEditar = [
        'id_categoria_servicio' => null,
        'nombre_categoria_servicio' => '',
        'descripcion' => '',
    ];

    

    public $mensajes = [
        'categoria.nombre_categoria_servicio.required' => 'El nombre de la categoria es obligatorio.',
        'categoria.nombre_categoria_servicio.max' => 'El nombre no puede tener más de 100 caracteres.',
        'categoria.descripcion.max' => 'La descripción no puede tener más de 1000 caracteres.',
    ];

    public function guardar()
    {
        // Validación
        $validatedData = $this->validate([
            'categoria.nombre_categoria_servicio' => 'required|string|max:255',
            'categoria.descripcion' => 'nullable|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($validatedData) {
                CategoriaServicio::create([
                    'nombre_categoria_servicio' => $validatedData['categoria']['nombre_categoria_servicio'],
                    'descripcion' => $validatedData['categoria']['descripcion'] ?? null,
                ]);
            });

            // Si llegamos aquí, todo se guardó correctamente
            $this->dispatch('categoriaRegistrado');
            session()->flash('success', 'Categoria registrada con éxito');
            $this->resetForm();
            $this->dispatch('categoriaUpdated');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al registrar la categoria: ' . $e->getMessage());
            Log::error('Error al registrar categoria', ['error' => $e->getMessage()]);
        }
    }

    public function resetForm()
    {
        $this->categoria = [
            'nombre_categoria_servicio' => '',
            'descripcion' => '',
        ];
    }

    #[\Livewire\Attributes\On('abrirModalCategoria')]
    public function abrirModalEditar($categoriaId)
    {
        $this->categoriaSeleccionado = CategoriaServicio::findOrFail($categoriaId);

        $this->categoriaEditar = [
            'id_categoria_servicio' => $categoriaId,
            'nombre_categoria_servicio' => $this->categoriaSeleccionado->nombre_categoria_servicio,
            'descripcion' => $this->categoriaSeleccionado->descripcion,
        ];

        $this->modalEditar = true;
    }

    public function guardarEdicion()
    {        
        if (! $this->categoriaSeleccionado) return;
    
        // Validación
        $validated = $this->validate([
            'categoriaEditar.nombre_categoria_servicio' => 'required|string|max:255',
            'categoriaEditar.descripcion' => 'nullable|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($validated) {

                $this->categoriaSeleccionado->update([
                    'nombre_categoria_servicio' => $validated['categoriaEditar']['nombre_categoria_servicio'],
                    'descripcion' => $validated['categoriaEditar']['descripcion'] ?? null,
                    'fecha_actualizacion' => now(),
                ]);
            });

            // Si llegamos aquí, todo se guardó correctamente
            session()->flash('success', '✅ Categoria actualizada con éxito');
            $this->modalEditar = false;
            $this->dispatch('categoriaUpdated');
        } catch (\Exception $e) {
            session()->flash('error', '❌ Error al actualizar la categoria: ' . $e->getMessage());
            Log::error('Error al actualizar categoria', ['error' => $e->getMessage()]);
        }
    }

    public function cerrarModal()
    {
        $this->modalEditar = false;
        $this->categoriaSeleccionado = null;
        $this->categoriaEditar = [
            'id_categoria_servicio' => null,
            'nombre_categoria_servicio' => '',
            'descripcion' => '',
        ];
    }

    public function render()
    {
        return view('livewire.mantenimiento.servicios.categoria');
    }
}