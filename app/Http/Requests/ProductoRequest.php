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
            'producto.descripcion' => 'string|max:1000',
            'producto.precio_unitario' => 'required|numeric|min:0.01|max:999999999.99',
            'producto.stock' => 'required|integer|min:0|max:9999',
            'producto.id_categoria_producto' => 'required|exists:categoria__productos,id',
            'producto.id_proveedor' => 'required|exists:proveedores,id',
        ];
    }

    public function messages(): array
    {
        return [
            'producto.nombre_producto.required' => 'El nombre del producto es obligatorio.',
            'producto.nombre_producto.max' => 'El nombre no puede tener más de 255 caracteres.',
            'producto.descripcion.required' => 'La descripción del producto es obligatoria.',
            'producto.descripcion.max' => 'La descripción no puede tener más de 1000 caracteres.',
            'producto.precio_unitario.required' => 'El precio unitario es obligatorio.',
            'producto.precio_unitario.numeric' => 'El precio debe ser un número válido.',
            'producto.precio_unitario.min' => 'El precio debe ser mayor a 0.',
            'producto.precio_unitario.max' => 'El precio no puede ser mayor a 999,999,999.99.',
            'producto.id_categoria_producto.required' => 'Debe seleccionar una categoría.',
            'producto.id_categoria_producto.exists' => 'La categoría seleccionada no es válida.',
            'producto.id_proveedor.required' => 'Debe seleccionar un proveedor.',
            'producto.id_proveedor.exists' => 'El proveedor seleccionado no es válido.',
            'producto.stock.required' => 'El stock inicial es obligatorio.',
            'producto.stock.integer' => 'El stock debe ser un número entero.',
            'producto.stock.min' => 'El stock no puede ser menor que 0.',
            'producto.stock.max' => 'El stock no puede superar 9999 unidades.',
        ];
    }
}
