<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmarPagoRequest extends FormRequest
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
            'comprobante' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'referencia' => 'nullable|string|max:100' // Agregar referencia opcional
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
            'comprobante.required' => 'El comprobante de pago es obligatorio.',
            'comprobante.file' => 'El comprobante debe ser un archivo válido.',
            'comprobante.mimes' => 'El comprobante debe ser una imagen (JPG, JPEG, PNG) o un archivo PDF.',
            'comprobante.max' => 'El comprobante no puede pesar más de 5MB.'
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
            'comprobante' => 'comprobante de pago'
        ];
    }
}
