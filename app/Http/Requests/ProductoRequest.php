<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'producto.nombre_producto' => 'required|string|max:255',
            "imagenProducto" => "nullable|image|max:2048",
            'producto.descripcion' => 'string|max:1000',
            'producto.id_unidad' => 'required|exists:unidades,id_unidad',
            'producto.id_categoria_producto' => 'required|exists:categoria_productos,id_categoria_producto',
            'producto.id_proveedor' => 'required|exists:proveedores,id_proveedor',
            "producto.precio_unitario" => "required|numeric|min:1",
        ];
    }

    public function messages(): array
    {
        return [
            "imagenProducto" => "Debe subir una imagen.",
            "imagenProducto" => "El tamaño de la imagen no puede exceder los 2MB.",
            'producto.nombre_producto.required' => 'El nombre del producto es obligatorio.',
            'producto.nombre_producto.max' => 'El nombre no puede tener más de 255 caracteres.',
            'producto.descripcion.required' => 'La descripción del producto es obligatoria.',
            'producto.descripcion.max' => 'La descripción no puede tener más de 1000 caracteres.',
            'producto.id_categoria_producto.required' => 'Debe seleccionar una categoría.',
            'producto.id_categoria_producto.exists' => 'La categoría seleccionada no es válida.',
            'producto.id_proveedor.required' => 'Debe seleccionar un proveedor.',
            'producto.id_proveedor.exists' => 'El proveedor seleccionado no es válido.',
            'producto.id_unidad.required' => 'La unidad es obligatoria.',
            'producto.id_unidad.exists' => 'La unidad seleccionada no es válida.',
            "producto.precio_unitario.required" => "El precio unitario es obligatorio.",
            "producto.precio_unitario.numeric" => "El precio unitario debe ser un número"
        ];
    }
}
