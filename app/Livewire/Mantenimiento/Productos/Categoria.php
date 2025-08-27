<?php

namespace App\Livewire\Mantenimiento\Productos;

use App\Models\CategoriaProducto;
use Livewire\Component;

class Categoria extends Component
{
    public $categoria = [
        'nombre' => '',
        'descripcion' => '',
    ];

    public $mensajes = [
        'categoria.nombre.required' => 'El nombre de la categoria es obligatorio.',
        'categoria.nombre.max' => 'El nombre no puede tener más de 255 caracteres.',
        'categoria.descripcion.max' => 'La descripción no puede tener más de 1000 caracteres.',
    ];

    public function guardar()
    {
        $validate = $this->validate([
            'categoria.nombre' => 'required|string|max:255',
            'categoria.descripcion' => 'string|max:1000',
        ]);

        try {
            if ($validate) {
                $categoria = CategoriaProducto::create([
                    'nombre' => $this->categoria['nombre'],
                    'descripcion' => $this->categoria['descripcion'],
                ]);

                if ($categoria) {
                    $this->dispatch('categoriaRegistrado');

                    session()->flash('success', 'Categoria registrada con éxito');
                    $this->resetForm();
                }
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error al registrar la categoria: ' . $e->getMessage());
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
