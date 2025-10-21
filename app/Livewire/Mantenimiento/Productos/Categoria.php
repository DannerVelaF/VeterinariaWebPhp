<?php

namespace App\Livewire\Mantenimiento\Productos;

use App\Models\CategoriaProducto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Categoria extends Component
{

    public $modalEditar = false;
    public $categoriaSeleccionado;

    public $categoriaEditar = [
        'id_categoria_producto' => null,
        'nombre_categoria_producto' => '',
        'descripcion' => '',
    ];

    public $categoria = [
        'nombre_categoria' => '',
        'descripcion' => '',
    ];

    public $mensajes = [
        'categoria.nombre_categoria.required' => 'El nombre de la categoria es obligatorio.',
        'categoria.nombre_categoria.max' => 'El nombre no puede tener más de 255 caracteres.',
        'categoria.descripcion.max' => 'La descripción no puede tener más de 1000 caracteres.',
    ];

    public function guardar()
    {
        // Validación
        $validatedData = $this->validate([
            'categoria.nombre_categoria' => 'required|string|max:255',
            'categoria.descripcion' => 'nullable|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($validatedData, &$categoria) {
                $categoria = CategoriaProducto::create([
                    'nombre_categoria_producto' => $validatedData['categoria']['nombre_categoria'],
                    'descripcion' => $validatedData['categoria']['descripcion'] ?? null,
                ]);
            });

            // Si llegamos aquí, todo se guardó correctamente
            $this->dispatch('categoriaRegistrado');
            $this->dispatch('notify', title: 'Success', description: 'Categoria creada correctamente.', type: 'success');
            $this->resetForm();
            $this->dispatch('categoriaUpdated');
        } catch (\Exception $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error al registrar la categoria. ', type: 'error');
            Log::error('Error al registrar categoria', ['error' => $e->getMessage()]);
        }
    }


    public function resetForm()
    {
        $this->categoria = [
            'nombre_categoria' => '',
            'descripcion' => '',
        ];
    }

    #[\Livewire\Attributes\On('abrirModalCategoria')]
    public function abrirModalEditar($categoriaId)
    {
        $this->categoriaSeleccionado = CategoriaProducto::findOrFail($categoriaId);
        $this->categoriaEditar = [
            'id_categoria_producto' => $categoriaId,
            'nombre_categoria_producto' => $this->categoriaSeleccionado->nombre_categoria_producto,
            'descripcion' => $this->categoriaSeleccionado->descripcion,
        ];
        $this->modalEditar = true;
    }

    public function guardarEdicion()
    {
        if (!$this->categoriaSeleccionado) return;

        // ✅ Agregar validación para la edición
        $validatedData = $this->validate([
            'categoriaEditar.nombre_categoria_producto' => 'required|string|max:255',
            'categoriaEditar.descripcion' => 'nullable|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($validatedData) {
                // Actualizar categoria
                $this->categoriaSeleccionado->update([
                    'nombre_categoria_producto' => $validatedData['categoriaEditar']['nombre_categoria_producto'],
                    'descripcion' => $validatedData['categoriaEditar']['descripcion'],
                    'fecha_actualizacion' => now(),
                ]);
            });

            $this->modalEditar = false;
            $this->dispatch('notify', title: 'Success', description: 'Categoria actualizada correctamente.', type: 'success');
            $this->dispatch('categoriaUpdated');
        } catch (\Exception $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error al actualizar la categoria.', type: 'error');
            Log::error('Error al actualizar categoria', ['error' => $e->getMessage()]);
        }
    }

    public function cerrarModal()
    {
        $this->modalEditar = false;
        $this->categoriaSeleccionado = null;
        $this->categoriaEditar = [
            'id_categoria_producto' => null,
            'nombre_categoria_producto' => '',
            'descripcion' => '',
        ];
    }

    public function render()
    {
        return view('livewire.mantenimiento.productos.categoria');
    }
}
