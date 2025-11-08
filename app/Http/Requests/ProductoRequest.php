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
            'producto.proveedores_seleccionados' => 'required|array|min:1', // ← ACTUALIZAR
            'producto.proveedores_seleccionados.*' => 'exists:proveedores,id_proveedor', // ← NUEVO
            "producto.precio_unitario" => "required|numeric|min:1",
            'producto.cantidad_por_unidad' => 'nullable|integer|min:0',
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
            'producto.proveedores_seleccionados.required' => 'Debe seleccionar al menos un proveedor.', // ← ACTUALIZAR
            'producto.proveedores_seleccionados.min' => 'Debe seleccionar al menos un proveedor.', // ← NUEVO
            'producto.proveedores_seleccionados.*.exists' => 'Uno de los proveedores seleccionados no es válido.', // ← NUEVO
            'producto.id_unidad.required' => 'La unidad es obligatoria.',
            'producto.id_unidad.exists' => 'La unidad seleccionada no es válida.',
            "producto.precio_unitario.required" => "El precio unitario es obligatorio.",
            "producto.precio_unitario.numeric" => "El precio unitario debe ser un número",
            'producto.cantidad_por_unidad.required' => 'La cantidad contenida en la unidad es obligatoria para este tipo de unidad.',
            'producto.cantidad_por_unidad.integer' => 'La cantidad debe ser un número entero.',
            'producto.cantidad_por_unidad.min' => 'La cantidad debe ser al menos 1.',
        ];
    }
}
