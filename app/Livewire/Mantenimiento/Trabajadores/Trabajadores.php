<?php

namespace App\Livewire\Mantenimiento\Trabajadores;

use App\Http\Requests\TrabajadorRequest;
use App\Models\Direccion;
use App\Models\EstadoTrabajadores;
use App\Models\Persona;
use App\Models\PuestoTrabajador;
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

    // 🔹 Modal de edición
    public $modalEditar = false;
    public $trabajadorSeleccionado;
    public $puestoNuevo;
    public $estadoNuevo;

    // 🔹 Datos de registro
    public $persona = [];
    public $trabajador = [];
    public $direccion = [];

    // 🔹 Datos de edición
    public $personaEditar = [];
    public $trabajadorEditar = [];
    public $direccionEditar = [];

    // 🔹 Datos auxiliares
    public $puestos = [];
    public $estados = [];
    public $tipos_documentos = [];
    public $departamentos = [];
    public $provincias = [];
    public $distritos = [];

    public $departamentoSeleccionado = '';
    public $provinciaSeleccionada = '';

    public function mount()
    {
        $this->resetForm();

        $this->departamentos = Ubigeo::select("departamento")->distinct()->pluck('departamento')->toArray();
        $this->tipos_documentos = Tipo_documento::select("id_tipo_documento", "nombre_tipo_documento")->get();
        $this->puestos = PuestoTrabajador::where("estado", "activo")->get();
        $this->estados = EstadoTrabajadores::all();
    }

    // ============================================================
    // 🔸 REGISTRO DE TRABAJADOR
    // ============================================================

    public function guardar()
    {
        DB::beginTransaction();
        try {
            // 🔹 Usar Validator::make en lugar de $this->validate()
            $validatedData = Validator::make(
                [
                    'persona' => $this->persona,
                    'direccion' => $this->direccion,
                    'trabajador' => $this->trabajador,
                    'puestoNuevo' => $this->puestoNuevo,
                    'estadoNuevo' => $this->estadoNuevo,
                ],
                [
                    // 🧍 Datos personales
                    'persona.id_tipo_documento' => 'required|integer|exists:tipo_documentos,id_tipo_documento',
                    'persona.numero_documento' => 'required|string|min:8|max:15',
                    'persona.nombre' => 'required|string|max:100',
                    'persona.apellido_paterno' => 'required|string|max:100',
                    'persona.apellido_materno' => 'nullable|string|max:100',
                    'persona.fecha_nacimiento' => 'required|date|before_or_equal:' . now()->subYears(18)->format('Y-m-d'),
                    'persona.sexo' => 'required|in:M,F',
                    'persona.nacionalidad' => 'required|string|max:50',

                    // 📧 Contacto
                    'persona.correo_electronico_personal' => 'required|email|max:150',
                    'persona.correo_electronico_secundario' => 'nullable|email|max:150',
                    'persona.numero_telefono_personal' => 'required|digits:9',
                    'persona.numero_telefono_secundario' => 'nullable|digits:9',

                    // 🏠 Dirección
                    'direccion.tipo_calle' => 'required|string|max:50',
                    'direccion.nombre_calle' => 'required|string|max:150',
                    'direccion.numero' => 'required|string|max:15',
                    'direccion.referencia' => 'required|string|max:255',
                    'direccion.codigo_postal' => 'required|string|max:10',
                    'direccion.zona' => 'required|string|max:100',
                    'direccion.codigo_ubigeo' => 'required|string|size:6',

                    // 💼 Datos laborales
                    'trabajador.salario' => 'required|numeric|min:0',
                    'trabajador.numero_seguro_social' => 'required|string|max:50',
                    'puestoNuevo' => 'required|integer|exists:puesto_trabajadores,id_puesto_trabajo',
                    'estadoNuevo' => 'required|integer|exists:estado_trabajadores,id_estado_trabajador',
                ],
                [
                    // ⚠️ Mensajes personalizados (los mismos que ya tienes)
                    'required' => 'El campo es obligatorio.',
                    'before_or_equal' => 'El trabajador debe tener al menos 18 años de edad.',
                    'email' => 'Ingrese un correo electrónico válido.',
                    'date' => 'El campo debe tener un formato de fecha válido.',
                    'in' => 'El valor seleccionado para :attribute no es válido.',
                    'integer' => 'El campo debe ser un número entero.',
                    'regex' => 'El campo solo debe contener números válidos.',
                    'size' => 'El campo debe tener exactamente :size caracteres.',
                    'max' => 'El campo no puede exceder los :max caracteres.',
                    'min' => 'El campo debe tener al menos :min caracteres.',
                    'exists' => 'El valor seleccionado no existe en la base de datos.',
                    'digits' => 'El campo debe tener exactamente :digits dígitos.',
                ]
            )->validate();

            // 🧩 Crear dirección
            $direccion = Direccion::create($this->direccion);

            // 🧍 Crear persona
            $persona = Persona::create(array_merge($this->persona, [
                'id_direccion' => $direccion->id_direccion
            ]));

            // 💼 Crear trabajador
            $trabajador = $persona->trabajador()->create(array_merge($this->trabajador, [
                'id_puesto_trabajo' => $this->puestoNuevo,
                'id_estado_trabajador' => $this->estadoNuevo,
            ]));

            DB::commit();

            $this->dispatch('notify', title: 'Success', description: 'Trabajador registrado correctamente.', type: 'success');
            Log::info('Trabajador registrado con éxito', [
                'id_persona' => $persona->id_persona,
                'id_trabajador' => $trabajador->id_trabajador
            ]);

            $this->resetForm();
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            $this->dispatch('notify', title: 'Error', description: 'Error de validación. Verifique los campos.', type: 'error');
            throw $e; // Permite que Livewire muestre los errores debajo de los inputs

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', title: 'Error', description: 'Error al registrar trabajador: ' . $e->getMessage(), type: 'error');
            Log::error('Error al registrar trabajador', [
                'error' => $e->getMessage(),
                'persona' => $this->persona,
                'trabajador' => $this->trabajador,
                'direccion' => $this->direccion
            ]);
        }
    }

    // ============================================================
    // 🔸 EDICIÓN DE TRABAJADOR
    // ============================================================
    #[\Livewire\Attributes\On('abrirModalTrabajador')]
    public function abrirModalTrabajador($trabajadorId)
    {
        $this->trabajadorSeleccionado = Trabajador::with('persona.direccion', 'puestoTrabajo', 'estadoTrabajador')
            ->findOrFail($trabajadorId);

        // Combos
        $this->puestoNuevo = $this->trabajadorSeleccionado->id_puesto_trabajo;
        $this->estadoNuevo = $this->trabajadorSeleccionado->id_estado_trabajador;

        // ✅ Extraer información correctamente
        $this->personaEditar = [
            'nombre' => $this->trabajadorSeleccionado->persona->nombre ?? '',
            'apellido_paterno' => $this->trabajadorSeleccionado->persona->apellido_paterno ?? '',
            'apellido_materno' => $this->trabajadorSeleccionado->persona->apellido_materno ?? '',
            'correo_electronico_personal' => $this->trabajadorSeleccionado->persona->correo_electronico_personal ?? '',
            'correo_electronico_secundario' => $this->trabajadorSeleccionado->persona->correo_electronico_secundario ?? '',
            'numero_telefono_personal' => $this->trabajadorSeleccionado->persona->numero_telefono_personal ?? '',
            'numero_telefono_secundario' => $this->trabajadorSeleccionado->persona->numero_telefono_secundario ?? '',
        ];

        $this->direccionEditar = [
            'zona' => $this->trabajadorSeleccionado->persona->direccion->zona ?? '',
            'tipo_calle' => $this->trabajadorSeleccionado->persona->direccion->tipo_calle ?? '',
            'nombre_calle' => $this->trabajadorSeleccionado->persona->direccion->nombre_calle ?? '',
            'numero' => $this->trabajadorSeleccionado->persona->direccion->numero ?? '',
        ];

        $this->trabajadorEditar = [
            'salario' => $this->trabajadorSeleccionado->salario ?? '',
            'numero_seguro_social' => $this->trabajadorSeleccionado->numero_seguro_social ?? '',
        ];

        // Abrir modal
        $this->modalEditar = true;
    }


    public function guardarEdicion()
    {
        try {
            if (!$this->trabajadorSeleccionado) {
                $this->dispatch('notify', title: 'Error', description: 'No hay un trabajador seleccionado.', type: 'error');
                return;
            }

            $validatedData = $this->validate(
                [
                    'personaEditar.id_tipo_documento' => 'required|integer|exists:tipo_documentos,id_tipo_documento',
                    'personaEditar.numero_documento' => 'required|string|min:8|max:15',
                    'personaEditar.nombre' => 'required|string|max:100',
                    'personaEditar.apellido_paterno' => 'required|string|max:100',
                    'personaEditar.apellido_materno' => 'nullable|string|max:100',
                    'personaEditar.fecha_nacimiento' => 'required|date|before_or_equal:' . now()->subYears(18)->format('Y-m-d'),
                    'personaEditar.sexo' => 'required|in:M,H',
                    'personaEditar.correo_electronico_personal' => 'required|email|max:150',
                    "personaEditar.correo_electronico_secundario" => "nullable|email|max:150",
                    "personaEditar.numero_telefono_personal" => "required|digits:9",
                    "personaEditar.numero_telefono_secundario" => "nullable|digits:9",
                    'direccionEditar.tipo_calle' => 'required|string|max:50',
                    'direccionEditar.nombre_calle' => 'required|string|max:150',
                    'direccionEditar.numero' => 'required|string|max:15',
                    'direccionEditar.zona' => 'required|string|max:100',
                    'direccionEditar.codigo_ubigeo' => 'required|string|size:6',
                    'trabajadorEditar.salario' => 'required|numeric|min:0',
                    'puestoNuevo' => 'required|integer|exists:puesto_trabajadores,id_puesto_trabajo',
                    'estadoNuevo' => 'required|integer|exists:estado_trabajadores,id_estado_trabajador',
                ],
                [
                    'required' => 'El campo es obligatorio.',
                    'before_or_equal' => 'El cliente debe tener al menos 18 años de edad.',
                    'email' => 'Ingrese un correo electrónico válido.',
                    'date' => 'El campo debe tener un formato de fecha válido.',
                    'in' => 'El valor seleccionado no es válido.',
                    'integer' => 'El campo debe ser un número entero.',
                    'regex' => 'El campo solo debe contener números válidos.',
                    'size' => 'El campo debe tener exactamente :size caracteres.',
                    'max' => 'El campo no puede exceder los :max caracteres.',
                    'min' => 'El campo debe tener al menos :min caracteres.',
                    'exists' => 'El valor seleccionado no existe en la base de datos.',
                    'digits' => 'El campo debe tener exactamente :digits dígitos.',
                ]
            );

            DB::transaction(function () {
                // Persona
                $this->trabajadorSeleccionado->persona->update($this->personaEditar);

                // Dirección
                $this->trabajadorSeleccionado->persona->direccion->update($this->direccionEditar);

                // Trabajador
                $this->trabajadorSeleccionado->update(array_merge($this->trabajadorEditar, [
                    'id_puesto_trabajo' => $this->puestoNuevo,
                    'id_estado_trabajador' => $this->estadoNuevo,
                    'fecha_actualizacion' => now(),
                ]));

                // Usuario vinculado
                $this->trabajadorSeleccionado->persona->user()->update([
                    'estado' => $this->estadoNuevo == 1 ? 'activo' : 'inactivo',
                ]);
            });

            $this->modalEditar = false;
            $this->dispatch('notify', title: 'Success', description: 'Trabajador actualizado correctamente.', type: 'success');
            $this->dispatch('trabajadoresUpdated');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error de validación. Verifique los campos.', type: 'error');
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar trabajador', [
                'error' => $e->getMessage(),
                'persona' => $this->personaEditar,
                'direccion' => $this->direccionEditar,
                'trabajador' => $this->trabajadorEditar,
            ]);
            $this->dispatch('notify', title: 'Error', description: 'Error al actualizar el trabajador: ' . $e->getMessage(), type: 'error');
        }
    }

    // ============================================================
    // 🔸 AUXILIARES
    // ============================================================

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

                if (!empty($data['first_name'])) {
                    $this->persona['nombre'] = $data['first_name'];
                    $this->persona['apellido_paterno'] = $data['first_last_name'];
                    $this->persona['apellido_materno'] = $data['second_last_name'];
                    $this->persona['nacionalidad'] = "Peruana";
                    $this->dispatch('notify', title: 'Success', description: 'Datos cargados desde RENIEC.', type: 'success');
                } else {
                    $this->dispatch('notify', title: 'Error', description: 'No se encontró información para este DNI.', type: 'error');
                }
            } else {
                $this->dispatch('notify', title: 'Error', description: 'Error al consultar el DNI ' . $dni, type: 'error');
            }
        } catch (\Exception $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error al conectar con la API: ' . $e->getMessage(), type: 'error');
        }
    }


    public function resetForm()
    {
        $this->persona = [
            'numero_documento' => '',
            'nombre' => '',
            'apellido_paterno' => '',
            'apellido_materno' => '',
            'fecha_nacimiento' => '',
            'sexo' => '',
            'correo' => '',
            'nacionalidad' => '',
            'id_tipo_documento' => '',
            'id_direccion' => '',
            'correo_electronico_personal' => '',
            'correo_electronico_secundario' => '',
            'numero_telefono_personal' => '',
            'numero_telefono_secundario' => '',
        ];

        $this->trabajador = [
            'fecha_ingreso' => now()->format('Y-m-d'),
            'salario' => '',
            'numero_seguro_social' => '',
            'id_puesto_trabajo' => '',
            'id_estado_trabajador' => 1,
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
    }

    public function cerrarModal()
    {
        $this->modalEditar = false;
        $this->personaEditar = [];
        $this->trabajadorEditar = [];
        $this->direccionEditar = [];
    }

    public function render()
    {
        return view('livewire.mantenimiento.trabajadores.trabajadores');
    }
}
