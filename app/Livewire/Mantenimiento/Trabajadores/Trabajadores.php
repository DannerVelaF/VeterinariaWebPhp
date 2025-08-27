<?php

namespace App\Livewire\Mantenimiento\Trabajadores;

use App\Http\Requests\TrabajadorRequest;
use App\Models\Direccion;
use App\Models\EstadoTrabajador;
use App\Models\EstadoTrabajadores;
use App\Models\Persona;
use App\Models\PuestoTrabajador;
use App\Models\PuestoTrabajadores;
use App\Models\Tipo_documento;
use App\Models\Ubigeo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class Trabajadores extends Component
{

    public $persona = [
        "numero_documento" => '',
        'nombre' => '',
        'apellido_paterno' => '',
        'apellido_materno' => '',
        'fecha_nacimiento' => '',
        'sexo' => '',
        'correo' => '',
        'nacionalidad' => '',
        'id_tipo_documento' => '',
        'id_direccion' => '',
        "correo_electronico_personal" => "",
        "correo_electronico_secundario" => "",
        "numero_telefono_personal" => "",
        "numero_telefono_secundario" => "",
    ];

    public $trabajador = [
        'fecha_ingreso' => '',
        'salario' => '',
        'numero_seguro_social' => '',
        'id_puesto_trabajo' => '',
        'id_estado_trabajador' => 1,
    ];
    public $puestos = [];
    public $estados = [];
    public $tipos_documentos = [];
    public $direccion = [
        'zona' => '',
        'tipo_calle' => '',
        'nombre_calle' => '',
        'numero' => '',
        'codigo_postal' => '',
        'referencia' => '',
        'codigo_ubigeo' => ''
    ];

    public $departamentos = [];
    public $provincias = [];
    public $distritos = [];

    public $departamentoSeleccionado = '';
    public $provinciaSeleccionada = '';

    public function mount()
    {
        $this->trabajador['fecha_ingreso'] = now()->format('Y-m-d');

        $this->departamentos = Ubigeo::select("departamento")->distinct()->pluck('departamento')->toArray();

        $this->tipos_documentos = Tipo_documento::select("id", "nombre")->get();

        $this->puestos = PuestoTrabajador::select("id", "nombre")->get();

        $this->estados = EstadoTrabajadores::all();
    }
    public function updatedDepartamentoSeleccionado($value)
    {
        $this->provincias = [];
        $this->distritos = [];
        $this->provinciaSeleccionada = '';
        $this->direccion['codigo_ubigeo'] = '';

        if (!empty($value)) {
            $this->provincias = Ubigeo::where('departamento', $value)
                ->select('provincia')
                ->distinct()
                ->pluck('provincia')
                ->toArray();
        }
    }

    public function updatedProvinciaSeleccionada($value)
    {
        $this->distritos = [];
        $this->direccion['codigo_ubigeo'] = '';

        if (!empty($value) && !empty($this->departamentoSeleccionado)) {
            $this->distritos = Ubigeo::where('departamento', $this->departamentoSeleccionado)
                ->where('provincia', $value)
                ->get();
        }
    }

    public function guardar()
    {
        DB::beginTransaction();
        try {
            // Validaciones
            $validatedData = Validator::make(
                [
                    'persona' => $this->persona,
                    'direccion' => $this->direccion,
                    'trabajador' => $this->trabajador,
                ],
                (new TrabajadorRequest)->rules(),
                (new TrabajadorRequest)->messages()
            )->validate();


            $direccion = Direccion::create($this->direccion);


            $persona = Persona::create(array_merge($this->persona, [
                'id_direccion' => $direccion->id
            ]));


            $trabajador = $persona->trabajador()->create($this->trabajador);


            DB::commit();

            session()->flash('success', '✅ Trabajador registrado correctamente');
            Log::info('Trabajador registrado con éxito', [
                'persona_id' => $persona->id,
                'trabajador_id' => $trabajador->id
            ]);

            $this->resetForm();
        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash('error', '❌ Error al registrar el trabajador: ' . $e->getMessage());
            Log::error('Error al registrar trabajador', [
                'error' => $e->getMessage(),
                'persona' => $this->persona,
                'trabajador' => $this->trabajador,
                'direccion' => $this->direccion
            ]);
        }
    }

    public function resetForm()
    {
        $this->persona = [
            'nombre' => '',
            'apellido_paterno' => '',
            'apellido_materno' => '',
            'fecha_nacimiento' => '',
            'sexo' => '',
            'correo' => '',
            'nacionalidad' => '',
            'id_tipo_documento' => '',
            'id_direccion' => '',
            "correo_electronico_personal" => "",
            "correo_electronico_secundario" => "",
            "numero_telefono_personal" => "",
            "numero_telefono_secundario" => "",
            "numero_documento" => "",
        ];

        $this->trabajador = [
            'fecha_ingreso' => '',
            'salario' => '',
            'numero_seguro_social' => '',
            'id_puesto_trabajo' => '',
            'id_estado_trabajador' => '',
        ];

        $this->direccion = [
            'zona' => '',
            'tipo_calle' => '',
            'nombre_calle' => '',
            'numero' => '',
            'codigo_postal' => '',
            'referencia' => '',
            'codigo_ubigeo' => ''
        ];

        $this->departamentoSeleccionado = '';
        $this->provinciaSeleccionada = '';
        $this->provincias = [];
        $this->distritos = [];

        $this->mount();
    }


    public function render()
    {
        return view('livewire.mantenimiento.trabajadores.trabajadores');
    }
}
