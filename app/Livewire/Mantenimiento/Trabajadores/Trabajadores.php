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
use App\Models\Trabajador;
use App\Models\Ubigeo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class Trabajadores extends Component
{
    protected $listeners = ['abrirModalTrabajador'];
    public $modalEditar = false;
    public $trabajadorSeleccionado;
    public $puestoNuevo;
    public $estadoNuevo;

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

        $this->tipos_documentos = Tipo_documento::select("id_tipo_documento", "nombre_tipo_documento")->get();

        $this->puestos = PuestoTrabajador::where("estado", "activo")->get();

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
                'id_direccion' => $direccion->id_direccion
            ]));


            $trabajador = $persona->trabajador()->create($this->trabajador);


            DB::commit();

            $this->dispatch('notify', title: 'Success', description: 'Trabajador registrado correctamente.', type: 'success');

            Log::info('Trabajador registrado con éxito', [
                'id_persona' => $persona->id_persona,
                'id_trabajador' => $trabajador->id_trabajador
            ]);

            $this->resetForm();
        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('notify', title: 'Error', description: 'Error al registrar trabajador.', type: 'error');

            Log::error('Error al registrar trabajador', [
                'error' => $e->getMessage(),
                'persona' => $this->persona,
                'trabajador' => $this->trabajador,
                'direccion' => $this->direccion
            ]);
        }
    }

    #[\Livewire\Attributes\On('abrirModalTrabajador')]
    public function abrirModalTrabajador($trabajadorId)
    {
        $this->trabajadorSeleccionado = Trabajador::with('persona', 'puestoTrabajo', 'estadoTrabajador')
            ->findOrFail($trabajadorId);

        $this->puestoNuevo = $this->trabajadorSeleccionado->id_puesto_trabajo;
        $this->estadoNuevo = $this->trabajadorSeleccionado->id_estado_trabajador;

        $this->persona = $this->trabajadorSeleccionado->persona->toArray();
        $this->trabajador = $this->trabajadorSeleccionado->toArray();
        $this->direccion = $this->trabajadorSeleccionado->persona->direccion->toArray();

        $this->modalEditar = true;
    }

    public function guardarEdicion()
    {
        if (!$this->trabajadorSeleccionado) return;

        DB::transaction(function () {
            // Actualizar persona
            $this->trabajadorSeleccionado->persona->update([
                'nombre' => $this->persona['nombre'],
                'apellido_paterno' => $this->persona['apellido_paterno'],
                'apellido_materno' => $this->persona['apellido_materno'],
                'fecha_nacimiento' => $this->persona['fecha_nacimiento'],
                'sexo' => $this->persona['sexo'],
                'nacionalidad' => $this->persona['nacionalidad'],
                'id_tipo_documento' => $this->persona['id_tipo_documento'],
                'id_direccion' => $this->persona['id_direccion'],
                "correo_electronico_personal" => $this->persona['correo_electronico_personal'],
                "correo_electronico_secundario" => $this->persona['correo_electronico_secundario'],
                "numero_telefono_personal" => $this->persona['numero_telefono_personal'],
                "numero_telefono_secundario" => $this->persona['numero_telefono_secundario'],
                "numero_documento" => $this->persona['numero_documento'],
                "fecha_actualizacion" => now(),
            ]);

            // Actualizar trabajador
            $this->trabajadorSeleccionado->update([
                'salario' => $this->trabajador['salario'],
                'numero_seguro_social' => $this->trabajador['numero_seguro_social'],
                'id_puesto_trabajo' => $this->puestoNuevo,
                'id_estado_trabajador' => $this->estadoNuevo,
                'fecha_actualizacion' => now(),
            ]);

            // Actualizar dirección
            $this->trabajadorSeleccionado->persona->direccion->update([
                'tipo_calle' => $this->direccion['tipo_calle'],
                'nombre_calle' => $this->direccion['nombre_calle'],
                'numero' => $this->direccion['numero'],
                'referencia' => $this->direccion['referencia'],
                'codigo_postal' => $this->direccion['codigo_postal'],
                'zona' => $this->direccion['zona'],
                'codigo_ubigeo' => $this->direccion['codigo_ubigeo'],
                "fecha_actualizacion" => now(),
            ]);

            $this->trabajadorSeleccionado->refresh()->load('estadoTrabajador');

            $this->trabajadorSeleccionado->persona->user()->update([
                'estado' => $this->estadoNuevo == 1 ? 'activo' : 'inactivo',
            ]);
        });

        $this->modalEditar = false;
        $this->dispatch('notify', title: 'Success', description: 'Trabajador actualizado correctamente.', type: 'success');
        $this->dispatch('trabajadoresUpdated');
    }

    public function buscarDni()
    {
        $dni = $this->persona['numero_documento'];

        if (!$dni || strlen($dni) !== 8) {
            session()->flash('error', 'El DNI debe tener 8 dígitos.');
            return;
        }

        try {
            $response = Http::withHeaders([
                "content-type" => "application/json",
                'Authorization' => "Bearer " . env("DECOLECTA_API_KEY"),
            ])->withOptions(['verify' => false])
                ->get("https://api.decolecta.com/v1/reniec/dni", [
                    'numero' => $dni, // <- CORRECTO: 'numero' en vez de 'dni'
                ]);

            if ($response->successful()) {
                $data = $response->json();

                if (!empty($data['full_name'])) {
                    // Si estamos registrando uno nuevo, llenamos $this->persona
                    $this->persona['nombre'] = $data['first_name'];
                    $this->persona['apellido_paterno'] = $data['first_last_name'];
                    $this->persona['apellido_materno'] = $data['second_last_name'];
                    $this->persona['nacionalidad'] = "Peruana";
                    $this->dispatch('notify', title: 'Success', description: 'Datos cargados desde RENIEC.', type: 'success');
                } else {
                    $this->dispatch('notify', title: 'Error', description: 'No se encontró información para este DNI.', type: 'error');
                }
            } else {
                $this->dispatch('notify', title: 'Error', description: 'Error al consultar el DNI ' . $dni . '. Intente nuevamente.', type: 'error');
            }
        } catch (\Exception $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error al conectar con la API: ' . $e->getMessage(), type: 'error');
            Log::error('Error al conectar con la API', ['error' => $e->getMessage()]);
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
    public function cerrarModal()
    {
        $this->modalEditar = false;
        $this->resetForm();
    }

    #[\Livewire\Attributes\On('ubigeosUpdated')]
    public function refresh()
    {
        $this->trabajador['fecha_ingreso'] = now()->format('Y-m-d');

        $this->departamentos = Ubigeo::select("departamento")->distinct()->pluck('departamento')->toArray();

        $this->tipos_documentos = Tipo_documento::select("id_tipo_documento", "nombre_tipo_documento")->get();

        $this->puestos = PuestoTrabajador::where("estado", "activo")->get();

        $this->estados = EstadoTrabajadores::all();
    }

    public function render()
    {
        return view('livewire.mantenimiento.trabajadores.trabajadores');
    }
}
