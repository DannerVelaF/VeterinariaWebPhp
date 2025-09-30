<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrabajadorRequest extends FormRequest
{
    // Autorización (puedes ajustarlo según tus políticas de acceso)
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Persona
            'persona.numero_documento' => 'required|string|max:20|unique:personas,numero_documento',
            'persona.nombre' => 'required|string|max:255',
            'persona.apellido_paterno' => 'required|string|max:255',
            'persona.apellido_materno' => 'required|string|max:255',
            'persona.fecha_nacimiento' => 'required|date',
            'persona.fecha_nacimiento' => 'required|date|before_or_equal:' . now()->subYears(18)->format('Y-m-d'),
            'persona.sexo' => 'required|in:M,F,Otro',
            'persona.correo_electronico_personal' => 'nullable|email|max:255',
            'persona.correo_electronico_secundario' => 'nullable|email|max:255',
            'persona.numero_telefono_personal' => 'nullable|string|max:20',
            'persona.numero_telefono_secundario' => 'nullable|string|max:20',
            'persona.nacionalidad' => 'nullable|string|max:100',
            'persona.id_tipo_documento' => 'required|exists:tipo_documentos,id_tipo_documento',

            // Dirección
            'direccion.tipo_calle' => 'required|string|max:50',
            'direccion.nombre_calle' => 'required|string|max:255',
            'direccion.codigo_ubigeo' => 'required|exists:ubigeos,codigo_ubigeo',

            // Trabajador
            'trabajador.fecha_ingreso' => 'required|date',
            'trabajador.fecha_salida' => 'nullable|date',
            'trabajador.salario' => 'required|numeric|min:0',
            'trabajador.numero_seguro_social' => 'nullable|string|max:50',
            'trabajador.id_puesto_trabajo' => 'required|exists:puesto_trabajadores,id_puesto_trabajo',
            'trabajador.id_estado_trabajador' => 'required|exists:estado_trabajadores,id_estado_trabajador',

        ];
    }

    public function messages(): array
    {
        return [
            // Persona
            'persona.numero_documento.required' => 'El número de documento es obligatorio.',
            'persona.numero_documento.unique'   => 'Este número de documento ya está registrado.',
            'persona.nombre.required'           => 'El nombre es obligatorio.',
            'persona.apellido_paterno.required' => 'El apellido paterno es obligatorio.',
            'persona.apellido_materno.required' => 'El apellido materno es obligatorio.',
            'persona.fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'persona.fecha_nacimiento.date'     => 'La fecha de nacimiento no es válida.',
            'persona.fecha_nacimiento.before_or_equal' => 'La persona debe ser mayor de 18 años.',
            'persona.sexo.required'             => 'Debe seleccionar un sexo.',
            'persona.sexo.in'                   => 'El sexo seleccionado no es válido.',
            'persona.id_tipo_documento.required' => 'Debe seleccionar un tipo de documento.',
            'persona.id_tipo_documento.exists'   => 'El tipo de documento no existe.',

            // Dirección
            'direccion.tipo_calle.required'     => 'El tipo de calle es obligatorio.',
            'direccion.nombre_calle.required'   => 'El nombre de la calle es obligatorio.',
            'direccion.codigo_ubigeo.required'  => 'Debe seleccionar un distrito.',
            'direccion.codigo_ubigeo.exists'    => 'El distrito seleccionado no existe.',

            // Trabajador
            'trabajador.fecha_ingreso.required' => 'La fecha de ingreso es obligatoria.',
            'trabajador.fecha_ingreso.date'     => 'La fecha de ingreso no es válida.',
            'trabajador.salario.required'       => 'El salario es obligatorio.',
            'trabajador.salario.numeric'        => 'El salario debe ser un número.',
            'trabajador.id_puesto_trabajo.required' => 'Debe seleccionar un puesto.',
            'trabajador.id_puesto_trabajo.exists'   => 'El puesto seleccionado no existe.',
            'trabajador.id_estado_trabajador.required' => 'Debe seleccionar un estado.',
            'trabajador.id_estado_trabajador.exists'   => 'El estado seleccionado no existe.',
        ];
    }
}
