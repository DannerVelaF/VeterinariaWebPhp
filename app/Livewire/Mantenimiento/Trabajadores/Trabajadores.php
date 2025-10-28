<?php

namespace App\Livewire\Mantenimiento\Trabajadores;

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
    protected $listeners = [
        'abrirModalTrabajador',
        'cerrarLoadingTrabajador' // 🔹 Nuevo listener
    ];
    public $loading = false;
    public $loadingUbigeo = false; // 🔹 Controlar carga de ubigeo

    // 🔹 Modal de edición
    public $modalEditar = false;
    public $trabajadorSeleccionado;

    // 🔹 Variables para EDICIÓN
    public $puestoEditar;
    public $estadoEditar;

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

    // Para el formulario de REGISTRO
    public $departamentoSeleccionado = '';
    public $provinciaSeleccionada = '';

    // Para el modal de EDICIÓN
    public $departamentoSeleccionadoEditar = '';
    public $provinciaSeleccionadaEditar = '';
    public $provinciasEditar = [];
    public $distritosEditar = [];

    public $tipos_calle = [
        'AV' => 'Avenida',
        'JR' => 'Jirón',
        'CL' => 'Calle',
        'PS' => 'Paseo',
        'CT' => 'Carretera',
        'MZ' => 'Manzana',
        'LT' => 'Lote',
        'URB' => 'Urbanización',
        'AAHH' => 'Asentamiento Humano',
        'PJ' => 'Pasaje',
        'GD' => 'Grupo',
        'SM' => 'Sector',
        'KM' => 'Kilómetro',
        'OTRO' => 'Otro'
    ];

    public function mount()
    {
        $this->resetForm();
        $this->trabajador['fecha_ingreso'] = now()->format('Y-m-d');

        $this->departamentos = Ubigeo::select("departamento")->distinct()->pluck('departamento')->toArray();
        $this->tipos_documentos = Tipo_documento::select("id_tipo_documento", "nombre_tipo_documento")->get();
        $this->puestos = PuestoTrabajador::where("estado", "activo")->get();
        $this->estados = EstadoTrabajadores::all();
    }

    public function updated($propertyName)
    {
        // Validar campos de persona en registro
        if (str_starts_with($propertyName, 'persona.')) {
            $this->validateOnly($propertyName, [
                'persona.id_tipo_documento' => 'required|integer|exists:tipo_documentos,id_tipo_documento',
                'persona.numero_documento' => 'required|string|min:8|max:15',
                'persona.nombre' => 'required|string|max:100',
                'persona.apellido_paterno' => 'required|string|max:100',
                'persona.apellido_materno' => 'nullable|string|max:100',
                'persona.fecha_nacimiento' => 'required|date|before_or_equal:' . now()->subYears(18)->format('Y-m-d'),
                'persona.sexo' => 'required|in:M,F',
                'persona.nacionalidad' => 'required|string|max:50',
                'persona.correo_electronico_personal' => 'required|email|max:150',
                'persona.correo_electronico_secundario' => 'nullable|email|max:150',
                'persona.numero_telefono_personal' => [
                    'required',
                    'digits:9',
                    'starts_with:9',
                    function ($attribute, $value, $fail) {
                        if (!empty($this->persona['numero_telefono_secundario']) && $value === $this->persona['numero_telefono_secundario']) {
                            $fail('El número telefónico principal no puede ser igual al secundario.');
                        }
                    }
                ],
                'persona.numero_telefono_secundario' => [
                    'nullable',
                    'digits:9',
                    'starts_with:9',
                    function ($attribute, $value, $fail) {
                        if (!empty($value) && $value === $this->persona['numero_telefono_personal']) {
                            $fail('El número telefónico secundario no puede ser igual al principal.');
                        }
                    }
                ],
            ], [
                'required' => 'El campo es obligatorio.',
                'before_or_equal' => 'El trabajador debe tener al menos 18 años de edad.',
                'email' => 'Ingrese un correo electrónico válido.',
                'date' => 'El campo debe tener un formato de fecha válido.',
                'in' => 'El valor seleccionado no es válido.',
                'max' => 'El campo no puede exceder los :max caracteres.',
                'min' => 'El campo debe tener al menos :min caracteres.',
                'exists' => 'El valor seleccionado no existe en la base de datos.',
                'digits' => 'El campo debe tener exactamente :digits dígitos.',
                'starts_with' => 'El número telefónico debe comenzar con 9.',
            ]);
        }

        // Validar campos de dirección en registro
        if (str_starts_with($propertyName, 'direccion.')) {
            $this->validateOnly($propertyName, [
                'direccion.tipo_calle' => 'required|string|max:50',
                'direccion.nombre_calle' => 'required|string|max:150',
                'direccion.numero' => 'required|string|max:15',
                'direccion.referencia' => 'nullable|string|max:255',
                'direccion.codigo_postal' => 'required|string|max:10',
                'direccion.zona' => 'required|string|max:100',
                'direccion.codigo_ubigeo' => 'required|string|size:6',
            ], [
                'required' => 'El campo es obligatorio.',
                'max' => 'El campo no puede exceder los :max caracteres.',
                'min' => 'El campo debe tener al menos :min caracteres.',
                'size' => 'El campo debe tener exactamente :size caracteres.',
            ]);
        }

        // Validar campos de trabajador en registro
        if (str_starts_with($propertyName, 'trabajador.')) {
            $this->validateOnly($propertyName, [
                'trabajador.salario' => 'required|numeric|min:0',
                'trabajador.numero_seguro_social' => 'required|string|max:50',
                'trabajador.id_puesto_trabajo' => 'required|integer|exists:puesto_trabajadores,id_puesto_trabajo',
            ], [
                'required' => 'El campo es obligatorio.',
                'numeric' => 'El campo debe ser un número válido.',
                'min' => 'El campo debe ser mayor o igual a :min.',
                'max' => 'El campo no puede exceder los :max caracteres.',
                'exists' => 'El valor seleccionado no existe en la base de datos.',
            ]);
        }

        // Validar campos de persona en edición
        if (str_starts_with($propertyName, 'personaEditar.')) {
            $this->validateOnly($propertyName, [
                'personaEditar.correo_electronico_personal' => 'required|email|max:150',
                'personaEditar.correo_electronico_secundario' => 'nullable|email|max:150',
                'personaEditar.numero_telefono_personal' => [
                    'required',
                    'digits:9',
                    'starts_with:9',
                    function ($attribute, $value, $fail) {
                        if (!empty($this->personaEditar['numero_telefono_secundario']) && $value === $this->personaEditar['numero_telefono_secundario']) {
                            $fail('El número telefónico principal no puede ser igual al secundario.');
                        }
                    }
                ],
                'personaEditar.numero_telefono_secundario' => [
                    'nullable',
                    'digits:9',
                    'starts_with:9',
                    function ($attribute, $value, $fail) {
                        if (!empty($value) && $value === $this->personaEditar['numero_telefono_personal']) {
                            $fail('El número telefónico secundario no puede ser igual al principal.');
                        }
                    }
                ],
            ], [
                'required' => 'El campo es obligatorio.',
                'email' => 'Ingrese un correo electrónico válido.',
                'max' => 'El campo no puede exceder los :max caracteres.',
                'digits' => 'El campo debe tener exactamente :digits dígitos.',
                'starts_with' => 'El número telefónico debe comenzar con 9.',
            ]);
        }

        // Validar campos de dirección en edición
        if (str_starts_with($propertyName, 'direccionEditar.')) {
            $this->validateOnly($propertyName, [
                'direccionEditar.tipo_calle' => 'nullable|string|max:50',
                'direccionEditar.nombre_calle' => 'required|string|max:150',
                'direccionEditar.numero' => 'required|string|max:15',
                'direccionEditar.zona' => 'required|string|max:100',
                'direccionEditar.codigo_ubigeo' => 'nullable|string|size:6',
            ], [
                'required' => 'El campo es obligatorio.',
                'max' => 'El campo no puede exceder los :max caracteres.',
                'size' => 'El campo debe tener exactamente :size caracteres.',
            ]);
        }

        // Validar campos de trabajador en edición
        if (str_starts_with($propertyName, 'trabajadorEditar.')) {
            $this->validateOnly($propertyName, [
                'trabajadorEditar.salario' => 'required|numeric|min:0',
                'trabajadorEditar.numero_seguro_social' => 'required|string|max:50',
            ], [
                'required' => 'El campo es obligatorio.',
                'numeric' => 'El campo debe ser un número válido.',
                'min' => 'El campo debe ser mayor o igual a :min.',
                'max' => 'El campo no puede exceder los :max caracteres.',
            ]);
        }

        // Validar puesto y estado en edición
        if ($propertyName === 'puestoEditar') {
            $this->validateOnly($propertyName, [
                'puestoEditar' => 'required|integer|exists:puesto_trabajadores,id_puesto_trabajo',
            ], [
                'required' => 'El campo es obligatorio.',
                'exists' => 'El valor seleccionado no existe en la base de datos.',
            ]);
        }

        if ($propertyName === 'estadoEditar') {
            $this->validateOnly($propertyName, [
                'estadoEditar' => 'required|integer|exists:estado_trabajadores,id_estado_trabajador',
            ], [
                'required' => 'El campo es obligatorio.',
                'exists' => 'El valor seleccionado no existe en la base de datos.',
            ]);
        }
    }

    // ============================================================
    // 🔸 MÉTODOS PARA CARGAR DINÁMICAMENTE UBIGEO EN REGISTRO
    // ============================================================

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

    // ============================================================
    // 🔸 MÉTODOS PARA UBIGEO EN MODAL DE EDICIÓN
    // ============================================================

    public function updatedDepartamentoSeleccionadoEditar($value)
    {
        $this->provinciaSeleccionadaEditar = '';
        $this->direccionEditar['codigo_ubigeo'] = '';
        $this->distritosEditar = [];

        if ($value) {
            $this->provinciasEditar = Ubigeo::where('departamento', $value)
                ->select('provincia')
                ->distinct()
                ->pluck('provincia')
                ->toArray();
        } else {
            $this->provinciasEditar = [];
        }
    }

    public function updatedProvinciaSeleccionadaEditar($value)
    {
        $this->direccionEditar['codigo_ubigeo'] = '';

        if ($value && $this->departamentoSeleccionadoEditar) {
            $this->distritosEditar = Ubigeo::where('departamento', $this->departamentoSeleccionadoEditar)
                ->where('provincia', $value)
                ->select('codigo_ubigeo', 'distrito')
                ->get();
        } else {
            $this->distritosEditar = [];
        }
    }

    // ============================================================
    // 🔸 REGISTRO DE TRABAJADOR
    // ============================================================

    public function guardar()
    {
        DB::beginTransaction();
        try {
            $validatedData = Validator::make(
                [
                    'persona' => $this->persona,
                    'direccion' => $this->direccion,
                    'trabajador' => $this->trabajador,
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
                    'persona.numero_telefono_personal' => [
                        'required',
                        'digits:9',
                        'starts_with:9',
                        function ($attribute, $value, $fail) {
                            if (!empty($this->persona['numero_telefono_secundario']) && $value === $this->persona['numero_telefono_secundario']) {
                                $fail('El número telefónico principal no puede ser igual al secundario.');
                            }
                        }
                    ],
                    'persona.numero_telefono_secundario' => [
                        'nullable',
                        'digits:9',
                        'starts_with:9',
                        function ($attribute, $value, $fail) {
                            if (!empty($value) && $value === $this->persona['numero_telefono_personal']) {
                                $fail('El número telefónico secundario no puede ser igual al principal.');
                            }
                        }
                    ],

                    // 🏠 Dirección
                    'direccion.tipo_calle' => 'required|string|max:50',
                    'direccion.nombre_calle' => 'required|string|max:150',
                    'direccion.numero' => 'required|string|max:15',
                    'direccion.referencia' => 'nullable|string|max:255',
                    'direccion.codigo_postal' => 'required|string|max:10',
                    'direccion.zona' => 'required|string|max:100',
                    'direccion.codigo_ubigeo' => 'required|string|size:6',

                    // 💼 Datos laborales
                    'trabajador.salario' => 'required|numeric|min:0',
                    'trabajador.numero_seguro_social' => 'required|string|max:50',
                    'trabajador.id_puesto_trabajo' => 'required|integer|exists:puesto_trabajadores,id_puesto_trabajo',
                ],
                [
                    'required' => 'El campo es obligatorio.',
                    'before_or_equal' => 'El trabajador debe tener al menos 18 años de edad.',
                    'email' => 'Ingrese un correo electrónico válido.',
                    'date' => 'El campo debe tener un formato de fecha válido.',
                    'in' => 'El valor seleccionado para :attribute no es válido.',
                    'integer' => 'El campo debe ser un número entero.',
                    'size' => 'El campo debe tener exactamente :size caracteres.',
                    'max' => 'El campo no puede exceder los :max caracteres.',
                    'min' => 'El campo debe tener al menos :min caracteres.',
                    'exists' => 'El valor seleccionado no existe en la base de datos.',
                    'digits' => 'El campo debe tener exactamente :digits dígitos.',
                    'starts_with' => 'El número telefónico debe comenzar con 9.',
                ]
            )->validate();

            // 🧩 Crear dirección
            $direccion = Direccion::create($this->direccion);

            // 🧍 Crear persona
            $persona = Persona::create(array_merge($this->persona, [
                'id_direccion' => $direccion->id_direccion
            ]));

            // 💼 Crear trabajador
            $trabajador = $persona->trabajador()->create([
                'fecha_ingreso' => $this->trabajador['fecha_ingreso'],
                'salario' => $this->trabajador['salario'],
                'numero_seguro_social' => $this->trabajador['numero_seguro_social'],
                'id_puesto_trabajo' => $this->trabajador['id_puesto_trabajo'],
                'id_estado_trabajador' => 1, // Por defecto: Activo
            ]);

            DB::commit();

            $this->dispatch('notify', title: 'Success', description: 'Trabajador registrado correctamente.', type: 'success');
            Log::info('Trabajador registrado con éxito', [
                'id_persona' => $persona->id_persona,
                'id_trabajador' => $trabajador->id_trabajador
            ]);

            $this->resetForm();
            $this->dispatch('trabajadoresUpdated');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            $this->dispatch('notify', title: 'Error', description: 'Error de validación. Verifique los campos.' . json_encode($e->errors()), type: 'error');
            throw $e;
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

    public function abrirModalTrabajador($trabajadorId)
    {
        $this->loading = true;
        try {
            // ✅ Consulta optimizada - solo los campos necesarios
            $this->trabajadorSeleccionado = Trabajador::with([
                'persona:id_persona,nombre,apellido_paterno,apellido_materno,correo_electronico_personal,correo_electronico_secundario,numero_telefono_personal,numero_telefono_secundario,id_direccion',
                'persona.direccion:id_direccion,zona,tipo_calle,nombre_calle,numero,codigo_ubigeo,codigo_postal,referencia',
                'puestoTrabajo:id_puesto_trabajo,nombre_puesto',
                'estadoTrabajador:id_estado_trabajador,nombre_estado_trabajador',
            ])->select('id_trabajador', 'salario', 'numero_seguro_social', 'id_puesto_trabajo', 'id_estado_trabajador', 'id_persona')
                ->findOrFail($trabajadorId);

            // ✅ Combos para EDICIÓN (operaciones rápidas)
            $this->puestoEditar = $this->trabajadorSeleccionado->id_puesto_trabajo;
            $this->estadoEditar = $this->trabajadorSeleccionado->id_estado_trabajador;

            // ✅ Datos básicos inmediatos
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
                'codigo_ubigeo' => $this->trabajadorSeleccionado->persona->direccion->codigo_ubigeo ?? '',
                'codigo_postal' => $this->trabajadorSeleccionado->persona->direccion->codigo_postal ?? '',
                'referencia' => $this->trabajadorSeleccionado->persona->direccion->referencia ?? '',
            ];

            $this->trabajadorEditar = [
                'salario' => $this->trabajadorSeleccionado->salario ?? '',
                'numero_seguro_social' => $this->trabajadorSeleccionado->numero_seguro_social ?? '',
            ];

            // ✅ CARGAR UBIGEO DE FORMA SÍNCRONA (antes de abrir el modal)
            $this->cargarUbigeoSincrono($this->direccionEditar['codigo_ubigeo'] ?? null);

            // ✅ Abrir modal inmediatamente
            $this->modalEditar = true;
        } catch (\Exception $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error al cargar datos: ' . $e->getMessage(), type: 'error');
        } finally {
            $this->loading = false;
        }
    }

    // ✅ Nuevo método para cargar ubigeo de forma síncrona
    public function cargarUbigeoSincrono($codigoUbigeo = null)
    {
        if (!$codigoUbigeo) return;
        $this->loadingUbigeo = true;
        try {
            $ubigeo = Ubigeo::where('codigo_ubigeo', $codigoUbigeo)->first();
            if ($ubigeo) {
                $this->departamentoSeleccionadoEditar = $ubigeo->departamento;
                $this->provinciaSeleccionadaEditar = $ubigeo->provincia;

                // Cargar provincias del departamento
                $this->provinciasEditar = Ubigeo::where('departamento', $ubigeo->departamento)
                    ->select('provincia')
                    ->distinct()
                    ->pluck('provincia')
                    ->toArray();

                // Cargar distritos de la provincia
                $this->distritosEditar = Ubigeo::where('departamento', $ubigeo->departamento)
                    ->where('provincia', $ubigeo->provincia)
                    ->select('codigo_ubigeo', 'distrito')
                    ->get();
            }
        } catch (\Exception $e) {
            Log::error('Error al cargar ubigeo síncrono: ' . $e->getMessage());
        } finally {
            $this->loadingUbigeo = false; // 🔹 DESACTIVAR loader
        }
    }

    // ✅ Mantener el método asíncrono por si acaso
    public function cargarUbigeoEditar($codigoUbigeo = null)
    {
        if (!$codigoUbigeo) return;
        $this->loadingUbigeo = true;
        try {
            $ubigeo = Ubigeo::where('codigo_ubigeo', $codigoUbigeo)->first();
            if ($ubigeo) {
                $this->departamentoSeleccionadoEditar = $ubigeo->departamento;
                $this->provinciaSeleccionadaEditar = $ubigeo->provincia;

                // Cargar provincias del departamento
                $this->provinciasEditar = Ubigeo::where('departamento', $ubigeo->departamento)
                    ->select('provincia')
                    ->distinct()
                    ->pluck('provincia')
                    ->toArray();

                // Cargar distritos de la provincia
                $this->distritosEditar = Ubigeo::where('departamento', $ubigeo->departamento)
                    ->where('provincia', $ubigeo->provincia)
                    ->select('codigo_ubigeo', 'distrito')
                    ->get();
            }
        } catch (\Exception $e) {
            Log::error('Error al cargar ubigeo: ' . $e->getMessage());
        } finally {
            $this->loadingUbigeo = false; // 🔹 DESACTIVAR loader
        }
    }

    public function guardarEdicion()
    {
        $this->loading = true;

        try {
            if (!$this->trabajadorSeleccionado) {
                $this->dispatch('notify', title: 'Error', description: 'No hay un trabajador seleccionado.', type: 'error');
                return;
            }

            // ✅ Solo validar campos editables en el modal
            $validatedData = $this->validate(
                [
                    // Persona (solo campos editables)
                    'personaEditar.correo_electronico_personal' => 'required|email|max:150',
                    'personaEditar.correo_electronico_secundario' => 'nullable|email|max:150',
                    'personaEditar.numero_telefono_personal' => [
                        'required',
                        'digits:9',
                        'starts_with:9',
                        function ($attribute, $value, $fail) {
                            if (!empty($this->personaEditar['numero_telefono_secundario']) && $value === $this->personaEditar['numero_telefono_secundario']) {
                                $fail('El número telefónico principal no puede ser igual al secundario.');
                            }
                        }
                    ],
                    'personaEditar.numero_telefono_secundario' => [
                        'nullable',
                        'digits:9',
                        'starts_with:9',
                        function ($attribute, $value, $fail) {
                            if (!empty($value) && $value === $this->personaEditar['numero_telefono_personal']) {
                                $fail('El número telefónico secundario no puede ser igual al principal.');
                            }
                        }
                    ],

                    // Dirección
                    'direccionEditar.tipo_calle' => 'nullable|string|max:50',
                    'direccionEditar.nombre_calle' => 'required|string|max:150',
                    'direccionEditar.numero' => 'required|string|max:15',
                    'direccionEditar.zona' => 'required|string|max:100',
                    'direccionEditar.codigo_ubigeo' => 'nullable|string|size:6',

                    // Trabajador
                    'trabajadorEditar.salario' => 'required|numeric|min:0',
                    'trabajadorEditar.numero_seguro_social' => 'required|string|max:50',
                    'puestoEditar' => 'required|integer|exists:puesto_trabajadores,id_puesto_trabajo',
                    'estadoEditar' => 'required|integer|exists:estado_trabajadores,id_estado_trabajador',
                ],
                [
                    'required' => 'El campo es obligatorio.',
                    'email' => 'Ingrese un correo electrónico válido.',
                    'integer' => 'El campo debe ser un número entero.',
                    'max' => 'El campo no puede exceder los :max caracteres.',
                    'min' => 'El campo debe tener al menos :min caracteres.',
                    'exists' => 'El valor seleccionado no existe en la base de datos.',
                    'digits' => 'El campo debe tener exactamente :digits dígitos.',
                    'numeric' => 'El campo debe ser un número válido.',
                    'starts_with' => 'El número telefónico debe comenzar con 9.',
                ]
            );

            DB::transaction(function () {
                // Actualizar persona (solo campos editables)
                $this->trabajadorSeleccionado->persona->update([
                    'correo_electronico_personal' => $this->personaEditar['correo_electronico_personal'],
                    'correo_electronico_secundario' => $this->personaEditar['correo_electronico_secundario'],
                    'numero_telefono_personal' => $this->personaEditar['numero_telefono_personal'],
                    'numero_telefono_secundario' => $this->personaEditar['numero_telefono_secundario'],
                ]);

                // Actualizar dirección
                $this->trabajadorSeleccionado->persona->direccion->update($this->direccionEditar);

                // Actualizar trabajador
                $this->trabajadorSeleccionado->update([
                    'salario' => $this->trabajadorEditar['salario'],
                    'numero_seguro_social' => $this->trabajadorEditar['numero_seguro_social'],
                    'id_puesto_trabajo' => $this->puestoEditar,
                    'id_estado_trabajador' => $this->estadoEditar,
                    'fecha_actualizacion' => now(),
                ]);

                // Actualizar usuario vinculado
                if ($this->trabajadorSeleccionado->persona->user) {
                    $this->trabajadorSeleccionado->persona->user->update([
                        'estado' => $this->estadoEditar == 1 ? 'activo' : 'inactivo',
                    ]);
                }
            });

            $this->cerrarModal();
            $this->dispatch('notify', title: 'Success', description: 'Trabajador actualizado correctamente.', type: 'success');
            $this->dispatch('trabajadoresUpdated');
            $this->loading = false;
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error de validación. Verifique los campos.', type: 'error');
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar trabajador', [
                'error' => $e->getMessage(),
                'trabajador_id' => $this->trabajadorSeleccionado->id_trabajador ?? null,
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
            $this->dispatch('notify', title: 'Error', description: 'El DNI debe tener 8 dígitos.', type: 'error');
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
                    $this->resetErrorBag([
                        'persona.nombre',
                        'persona.apellido_paterno',
                        'persona.apellido_materno',
                        'persona.nacionalidad'
                    ]);

                    $this->dispatch('notify', title: 'Success', description: 'Datos cargados desde RENIEC.', type: 'success');
                } else {
                    $this->dispatch('notify', title: 'Error', description: 'No se encontró información para este DNI.', type: 'error');
                }
            } else {
                $this->dispatch('notify', title: 'Error', description: 'Error al consultar el DNI', type: 'error');
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
            'nacionalidad' => '',
            'id_tipo_documento' => '',
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
        ];

        $this->direccion = [
            'tipo_calle' => '', // 🔹 Agregado
            'zona' => '',
            'nombre_calle' => '',
            'numero' => '',
            'codigo_postal' => '',
            'referencia' => '',
            'codigo_ubigeo' => ''
        ];

        // Resetear ubigeo de registro
        $this->departamentoSeleccionado = '';
        $this->provinciaSeleccionada = '';
        $this->provincias = [];
        $this->distritos = [];
        // Resetar errores
        $this->resetErrorBag();
    }

    public function cerrarModal()
    {
        $this->resetValidation();

        $this->modalEditar = false;
        $this->trabajadorSeleccionado = null;
        $this->personaEditar = [];
        $this->trabajadorEditar = [];
        $this->direccionEditar = [
            'tipo_calle' => '', // 🔹 Agregado
            'zona' => '',
            'nombre_calle' => '',
            'numero' => '',
            'codigo_ubigeo' => ''
        ];
        $this->puestoEditar = null;
        $this->estadoEditar = null;
        $this->loadingUbigeo = false;
        // Resetear ubigeo del modal de edición
        $this->departamentoSeleccionadoEditar = '';
        $this->provinciaSeleccionadaEditar = '';
        $this->provinciasEditar = [];
        $this->distritosEditar = [];
    }

    public function render()
    {
        return view('livewire.mantenimiento.trabajadores.trabajadores');
    }
}
