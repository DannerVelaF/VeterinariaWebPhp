<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PerfilRequest extends FormRequest
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
        $user = $this->user();
        $userId = $user ? $user->id_usuario : null;
        $personaId = $user && $user->persona ? $user->persona->id_persona : null;

        $rules = [
            'usuario' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('usuarios', 'usuario')->ignore($userId, 'id_usuario')
            ],
        ];

        // Obtener reglas de persona
        $personaRules = $this->getPersonaRules($personaId);
        $rules = array_merge($rules, $personaRules);

        // Para actualización parcial de campos directos
        $directFields = [
            'correo_electronico_personal',
            'correo_electronico_secundario',
            'numero_telefono_personal',
            'numero_telefono_secundario'
        ];

        foreach ($directFields as $field) {
            if ($this->has($field)) {
                $personaField = "persona.{$field}";
                if (isset($personaRules[$personaField])) {
                    $rules[$field] = $personaRules[$personaField];
                }
            }
        }

        return $rules;
    }

    /**
     * Reglas de validación para los campos de persona
     */
    private function getPersonaRules($personaId): array
    {
        return [
            'persona.id_tipo_documento' => [
                'sometimes',
                'required',
                'integer',
                'exists:tipo_documentos,id_tipo_documento'
            ],

            'persona.numero_documento' => [
                'sometimes',
                'required',
                'string',
                'min:8',
                'max:15'
            ],

            'persona.nombre' => [
                'sometimes',
                'required',
                'string',
                'max:100'
            ],

            'persona.apellido_paterno' => [
                'sometimes',
                'required',
                'string',
                'max:100'
            ],

            'persona.apellido_materno' => [
                'sometimes',
                'nullable',
                'string',
                'max:100'
            ],

            'persona.fecha_nacimiento' => [
                'sometimes',
                'required',
                'date',
                'before_or_equal:' . now()->subYears(18)->format('Y-m-d')
            ],

            'persona.sexo' => [
                'sometimes',
                'required',
                'in:M,F'
            ],

            'persona.nacionalidad' => [
                'sometimes',
                'required',
                'string',
                'max:50'
            ],

            'persona.correo_electronico_personal' => [
                'sometimes',
                'required',
                'email',
                'max:150',
                Rule::unique('personas', 'correo_electronico_personal')->ignore($personaId, 'id_persona')
            ],

            'persona.correo_electronico_secundario' => [
                'sometimes',
                'nullable',
                'email',
                'max:150',
                Rule::unique('personas', 'correo_electronico_secundario')->ignore($personaId, 'id_persona')
            ],

            'persona.numero_telefono_personal' => [
                'sometimes',
                'required',
                'digits:9',
                'starts_with:9',
                Rule::unique('personas', 'numero_telefono_personal')->ignore($personaId, 'id_persona'),
                function ($attribute, $value, $fail) {
                    $this->validatePhoneNumbers($value, null, $fail, 'principal', 'secundario');
                }
            ],

            'persona.numero_telefono_secundario' => [
                'sometimes',
                'nullable',
                'digits:9',
                'starts_with:9',
                Rule::unique('personas', 'numero_telefono_secundario')->ignore($personaId, 'id_persona'),
                function ($attribute, $value, $fail) {
                    if (!empty($value)) {
                        $this->validatePhoneNumbers(null, $value, $fail, 'secundario', 'principal');
                    }
                }
            ],

            // Reglas para campos directos (sin el prefijo persona.)
            'numero_telefono_personal' => [
                'sometimes',
                'required',
                'digits:9',
                'starts_with:9',
                Rule::unique('personas', 'numero_telefono_personal')->ignore($personaId, 'id_persona'),
                function ($attribute, $value, $fail) {
                    $this->validatePhoneNumbers($value, null, $fail, 'principal', 'secundario');
                }
            ],

            'numero_telefono_secundario' => [
                'sometimes',
                'nullable',
                'digits:9',
                'starts_with:9',
                Rule::unique('personas', 'numero_telefono_secundario')->ignore($personaId, 'id_persona'),
                function ($attribute, $value, $fail) {
                    if (!empty($value)) {
                        $this->validatePhoneNumbers(null, $value, $fail, 'secundario', 'principal');
                    }
                }
            ],
        ];
    }

    /**
     * Validar que los números telefónicos no sean iguales
     */
    private function validatePhoneNumbers($principal, $secundario, $fail, $currentType, $otherType)
    {
        // Obtener los valores actuales de ambos teléfonos
        $currentPrincipal = $principal ?? $this->getPhoneValue('numero_telefono_personal');
        $currentSecundario = $secundario ?? $this->getPhoneValue('numero_telefono_secundario');

        // Si ambos números están presentes y son iguales
        if (!empty($currentPrincipal) && !empty($currentSecundario) && $currentPrincipal === $currentSecundario) {
            $fail("El número telefónico {$currentType} no puede ser igual al {$otherType}.");
        }
    }

    /**
     * Obtener el valor del teléfono desde diferentes fuentes
     */
    private function getPhoneValue($fieldName)
    {
        // Buscar en campos directos
        if ($this->has($fieldName)) {
            return $this->$fieldName;
        }

        // Buscar en campos anidados
        $nestedField = "persona.{$fieldName}";
        if ($this->has($nestedField)) {
            return $this->input($nestedField);
        }

        return null;
    }

    /**
     * Mensajes de validación personalizados
     */
    public function messages(): array
    {
        return [
            'required' => 'El campo es obligatorio.',
            'before_or_equal' => 'El cliente debe tener al menos 18 años de edad.',
            'email' => 'Ingrese un correo electrónico válido.',
            'date' => 'El campo debe tener un formato de fecha válido.',
            'in' => 'El valor seleccionado no es válido.',
            'max' => 'El campo no puede exceder los :max caracteres.',
            'min' => 'El campo debe tener al menos :min caracteres.',
            'exists' => 'El valor seleccionado no existe en la base de datos.',
            'digits' => 'El campo debe tener exactamente :digits dígitos.',
            'starts_with' => 'El número telefónico debe comenzar con 9.',
            'unique' => 'El valor ingresado ya está en uso.',

            // Mensajes específicos
            'usuario.required' => 'El nombre de usuario es obligatorio',
            'usuario.unique' => 'Este nombre de usuario ya está en uso',

            'persona.correo_electronico_personal.required' => 'El correo principal es obligatorio',
            'persona.correo_electronico_personal.email' => 'El correo principal debe ser válido',
            'persona.correo_electronico_personal.unique' => 'El correo principal ya está en uso',

            'persona.correo_electronico_secundario.email' => 'El correo secundario debe ser válido',
            'persona.correo_electronico_secundario.unique' => 'El correo secundario ya está en uso',

            'persona.numero_telefono_personal.required' => 'El teléfono principal es obligatorio',
            'persona.numero_telefono_personal.unique' => 'El teléfono principal ya está en uso',

            'persona.numero_telefono_secundario.unique' => 'El teléfono secundario ya está en uso',

            'numero_telefono_personal.required' => 'El teléfono principal es obligatorio',
            'numero_telefono_personal.unique' => 'El teléfono principal ya está en uso',

            'numero_telefono_secundario.unique' => 'El teléfono secundario ya está en uso',

            'persona.id_tipo_documento.required' => 'El tipo de documento es obligatorio',
            'persona.numero_documento.required' => 'El número de documento es obligatorio',
            'persona.nombre.required' => 'El nombre es obligatorio',
            'persona.apellido_paterno.required' => 'El apellido paterno es obligatorio',
            'persona.fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria',
            'persona.sexo.required' => 'El sexo es obligatorio',
            'persona.nacionalidad.required' => 'La nacionalidad es obligatoria',
        ];
    }

    /**
     * Preparar los datos para la validación
     */
    protected function prepareForValidation()
    {
        // Para campos directos (no anidados), moverlos a persona si es necesario
        $directFields = [
            'correo_electronico_personal',
            'correo_electronico_secundario',
            'numero_telefono_personal',
            'numero_telefono_secundario'
        ];

        foreach ($directFields as $field) {
            if ($this->has($field) && !$this->has("persona.{$field}")) {
                $this->merge([
                    "persona.{$field}" => $this->$field
                ]);
            }
        }
    }

    /**
     * Atributos personalizados para los mensajes de error
     */
    public function attributes(): array
    {
        return [
            'persona.id_tipo_documento' => 'tipo de documento',
            'persona.numero_documento' => 'número de documento',
            'persona.nombre' => 'nombre',
            'persona.apellido_paterno' => 'apellido paterno',
            'persona.apellido_materno' => 'apellido materno',
            'persona.fecha_nacimiento' => 'fecha de nacimiento',
            'persona.sexo' => 'sexo',
            'persona.nacionalidad' => 'nacionalidad',
            'persona.correo_electronico_personal' => 'correo electrónico personal',
            'persona.correo_electronico_secundario' => 'correo electrónico secundario',
            'persona.numero_telefono_personal' => 'número telefónico personal',
            'persona.numero_telefono_secundario' => 'número telefónico secundario',
            'numero_telefono_personal' => 'número telefónico personal',
            'numero_telefono_secundario' => 'número telefónico secundario',
        ];
    }
}
