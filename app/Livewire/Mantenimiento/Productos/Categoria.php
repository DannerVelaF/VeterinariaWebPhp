<?php

namespace App\Livewire\Mantenimiento\Productos;

use App\Models\CategoriaProducto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Categoria extends Component
{
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
                    'nombre_categoria' => $validatedData['categoria']['nombre_categoria'],
                    'descripcion' => $validatedData['categoria']['descripcion'] ?? null,
                ]);
            });

            // Si llegamos aquí, todo se guardó correctamente
            $this->dispatch('categoriaRegistrado');
            session()->flash('success', 'Categoria registrada con éxito');
            $this->resetForm();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al registrar la categoria: ' . $e->getMessage());
            Log::error('Error al registrar categoria', ['error' => $e->getMessage()]);
        }
    }


    public function resetForm()
    {
        $this->categoria = [
            'nombre' => '',
            'descripcion' => '',
        ];
    }


    public function render()
    {
        return view('livewire.mantenimiento.productos.categoria');
    }
}
