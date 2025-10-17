<?php

namespace App\Livewire\Mantenimiento\Mascotas;

use App\Models\Direccion;
use App\Models\Persona;
use App\Models\Tipo_documento;
use App\Models\Ubigeo;
use App\Models\Clientes as ModelsClientes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class Clientes extends Component
{
    protected $listeners = ['abrirModalCliente'];

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

    // Modal edición
    public $modalEditar = false;
    public $clienteSeleccionado;
    public $cliente = [];

    public function mount()
    {
        $this->departamentos = Ubigeo::select("departamento")->distinct()->pluck('departamento')->toArray();
        $this->tipos_documentos = Tipo_documento::select("id_tipo_documento", "nombre_tipo_documento")->get();
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
                ],
                [
                    'persona.numero_documento' => 'required',
                    'persona.nombre' => 'required',
                    'persona.apellido_paterno' => 'required',
                    'persona.fecha_nacimiento' => 'required|date',
                    'persona.sexo' => 'required',
                    'persona.correo_electronico_personal' => 'required|email',
                    'direccion.nombre_calle' => 'required',
                    'direccion.numero' => 'required',
                    'direccion.zona' => 'required',
                    'direccion.codigo_ubigeo' => 'required',
                ],
                [
                    'required' => 'El campo :attribute es obligatorio.',
                    'email' => 'Ingrese un correo válido.',
                ]
            )->validate();

            $direccion = Direccion::create($this->direccion);

            $persona = Persona::create(array_merge($this->persona, [
                'id_direccion' => $direccion->id_direccion
            ]));

            $cliente = ModelsClientes::create([
                'id_persona' => $persona->id_persona,
                'fecha_creacion' => now(),
            ]);

            DB::commit();

            session()->flash('success', '✅ Cliente registrado correctamente');
            Log::info('Cliente registrado con éxito', [
                'id_persona' => $persona->id_persona,
                'id_cliente' => $cliente->id_cliente
            ]);

            $this->resetForm();
        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash('error', '❌ Error al registrar el cliente: ' . $e->getMessage());
            Log::error('Error al registrar cliente', [
                'error' => $e->getMessage(),
                'persona' => $this->persona,
                'direccion' => $this->direccion
            ]);
        }
    }

    public function guardarEdicion()
    {
        if (!$this->clienteSeleccionado) return;

        DB::transaction(function () {
            // Actualizar persona
            $this->clienteSeleccionado->persona->update([
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

            // Actualizar dirección
            $this->clienteSeleccionado->persona->direccion->update([
                'tipo_calle' => $this->direccion['tipo_calle'],
                'nombre_calle' => $this->direccion['nombre_calle'],
                'numero' => $this->direccion['numero'],
                'referencia' => $this->direccion['referencia'],
                'codigo_postal' => $this->direccion['codigo_postal'],
                'zona' => $this->direccion['zona'],
                'codigo_ubigeo' => $this->direccion['codigo_ubigeo'],
                "fecha_actualizacion" => now(),
            ]);
        });

        $this->modalEditar = false;
        session()->flash('success', '✅ Cliente actualizado correctamente.');
        $this->dispatch('clientesUpdated');
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
                    'numero' => $dni,
                ]);

            if ($response->successful()) {
                $data = $response->json();

                if (!empty($data['full_name'])) {
                    $this->persona['nombre'] = $data['first_name'];
                    $this->persona['apellido_paterno'] = $data['first_last_name'];
                    $this->persona['apellido_materno'] = $data['second_last_name'];
                    $this->persona['nacionalidad'] = "Peruana";
                    session()->flash('success', '✅ Datos cargados desde RENIEC.');
                } else {
                    session()->flash('error', 'No se encontró información para este DNI.');
                }
            } else {
                session()->flash('error', 'Error al consultar el DNI ' . $dni . '. Intente nuevamente.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error al conectar con la API: ' . $e->getMessage());
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
        return view('livewire.mantenimiento.mascotas.clientes');
    }
}
