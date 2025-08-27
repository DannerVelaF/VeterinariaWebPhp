<?php

namespace App\Livewire\Mantenimiento\Trabajadores;

use App\Models\PuestoTrabajador;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class Puestos extends Component
{
    public $puesto = [
        'nombre' => '',
        'descripcion' => '',
    ];

    public function guardar()
    {
        DB::beginTransaction();
        try {
            // Validaciones mínimas
            $validatedData = Validator::make(
                ['puesto' => $this->puesto],
                [
                    'puesto.nombre' => 'required|string|max:150|unique:puesto_trabajadores,nombre',
                    'puesto.descripcion' => 'nullable|string|max:255',
                ],
                [
                    'puesto.nombre.required' => 'El nombre del puesto es obligatorio',
                    'puesto.nombre.unique' => 'Ya existe un puesto con este nombre',
                    'puesto.nombre.max' => 'El nombre no debe superar los 150 caracteres',
                    'puesto.descripcion.max' => 'La descripción no debe superar los 255 caracteres',
                ]
            )->validate();

            // Guardar en BD
            $puesto = PuestoTrabajador::create($this->puesto);

            DB::commit();

            session()->flash('success', '✅ Puesto registrado correctamente');
            Log::info('Puesto registrado con éxito', [
                'puesto_id' => $puesto->id,
                'nombre' => $puesto->nombre,
            ]);

            $this->resetForm();
        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash('error', '❌ Error al registrar el puesto: ' . $e->getMessage());
            Log::error('Error al registrar puesto', [
                'error' => $e->getMessage(),
                'puesto' => $this->puesto,
            ]);
        }
    }

    public function resetForm()
    {
        $this->puesto = [
            'nombre' => '',
            'descripcion' => '',
        ];
    }

    public function render()
    {
        return view('livewire.mantenimiento.trabajadores.puestos');
    }
}
