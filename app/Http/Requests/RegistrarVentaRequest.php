<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegistrarVentaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Cambiar a true para permitir la validación
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_persona' => 'required|integer|exists:personas,id_persona',
            'subtotal' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'descuento' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.id_producto' => 'required|integer|exists:productos,id_producto',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.precio_unitario' => 'required|numeric|min:0',
            'id_metodo_pago' => 'required|integer|exists:metodo_pagos,id_metodo_pago',
            'observacion' => 'nullable|string|max:500'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'id_persona.required' => 'El ID de persona es obligatorio.',
            'id_persona.integer' => 'El ID de persona debe ser un número entero.',
            'id_persona.exists' => 'La persona especificada no existe en el sistema.',

            'subtotal.required' => 'El subtotal es obligatorio.',
            'subtotal.numeric' => 'El subtotal debe ser un valor numérico.',
            'subtotal.min' => 'El subtotal no puede ser negativo.',

            'total.required' => 'El total es obligatorio.',
            'total.numeric' => 'El total debe ser un valor numérico.',
            'total.min' => 'El total no puede ser negativo.',

            'descuento.required' => 'El descuento es obligatorio.',
            'descuento.numeric' => 'El descuento debe ser un valor numérico.',
            'descuento.min' => 'El descuento no puede ser negativo.',

            'items.required' => 'Debe agregar al menos un producto al carrito.',
            'items.array' => 'Los items deben ser un arreglo válido.',
            'items.min' => 'Debe agregar al menos un producto al carrito.',

            'items.*.id_producto.required' => 'El ID del producto es obligatorio.',
            'items.*.id_producto.integer' => 'El ID del producto debe ser un número entero.',
            'items.*.id_producto.exists' => 'Uno o más productos no existen en el sistema.',

            'items.*.cantidad.required' => 'La cantidad del producto es obligatoria.',
            'items.*.cantidad.integer' => 'La cantidad debe ser un número entero.',
            'items.*.cantidad.min' => 'La cantidad debe ser al menos 1.',

            'items.*.precio_unitario.required' => 'El precio unitario del producto es obligatorio.',
            'items.*.precio_unitario.numeric' => 'El precio unitario debe ser un valor numérico.',
            'items.*.precio_unitario.min' => 'El precio unitario no puede ser negativo.',

            'id_metodo_pago.required' => 'El método de pago es obligatorio.',
            'id_metodo_pago.integer' => 'El método de pago debe ser un número entero.',
            'id_metodo_pago.exists' => 'El método de pago seleccionado no existe.',

            'observacion.string' => 'La observación debe ser texto válido.',
            'observacion.max' => 'La observación no puede exceder los 500 caracteres.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'id_persona' => 'persona',
            'items.*.id_producto' => 'producto',
            'items.*.cantidad' => 'cantidad',
            'items.*.precio_unitario' => 'precio unitario',
            'id_metodo_pago' => 'método de pago'
        ];
    }
}
