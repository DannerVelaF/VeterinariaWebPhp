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
    public $loading = false;
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
    // Modal edición
    public $modalEditar = false;
    public $clienteSeleccionado;
    public $cliente = [
        'id_cliente' => null,
    ];

    public function mount()
    {
        $this->departamentos = Ubigeo::select("departamento")->distinct()->pluck('departamento')->toArray();
        $this->tipos_documentos = Tipo_documento::select("id_tipo_documento", "nombre_tipo_documento")->get();
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
                'before_or_equal' => 'El cliente debe tener al menos 18 años de edad.',
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
    // 🔸 REGISTRO DE CLIENTE
    // ============================================================

    public function guardar()
    {
        $this->loading = true;
        DB::beginTransaction();
        try {
            // 🔹 Validaciones completas
            $validatedData = Validator::make(
                [
                    'persona' => $this->persona,
                    'direccion' => $this->direccion,
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
                ],
                [
                    // ⚠️ Mensajes personalizados
                    'required' => 'El campo es obligatorio.',
                    'before_or_equal' => 'El cliente debe tener al menos 18 años de edad.',
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

            // 🧾 Crear cliente
            $cliente = ModelsClientes::create([
                'id_persona' => $persona->id_persona,
                'fecha_creacion' => now(),
            ]);

            DB::commit();

            // ✅ Notificación y reset
            $this->dispatch('notify', title: 'Success', description: 'Cliente registrado con éxito', type: 'success');
            $this->resetForm();
            $this->dispatch('clientesUpdated');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            $this->dispatch('notify', title: 'Error', description: 'Error de validación. Verifique los campos.', type: 'error');
            throw $e; // Permite que Livewire muestre los errores debajo de los inputs

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', title: 'Error', description: 'Error al registrar el cliente: ' . $e->getMessage(), type: 'error');

            Log::error('Error al registrar cliente', [
                'error' => $e->getMessage(),
                'persona' => $this->persona,
                'direccion' => $this->direccion,
            ]);
        }
    }

    // ============================================================
    // 🔸 EDICIÓN DE CLIENTE
    // ============================================================

    public function guardarEdicion()
    {
        $this->loading = true;
        try {
            if (!$this->clienteSeleccionado) {
                $this->dispatch('notify', title: 'Error', description: 'No hay un cliente seleccionado para editar.', type: 'error');
                return;
            }

            // ✅ Validaciones completas (idénticas a las de guardar)
            $validatedData = Validator::make(
                [
                    'persona' => $this->persona,
                    'direccion' => $this->direccion,
                ],
                [
                    // 🧍 Datos personales
                    'persona.fecha_nacimiento' => 'required|date|before_or_equal:' . now()->subYears(18)->format('Y-m-d'),
                    'persona.nacionalidad' => 'required|string|max:50',
                    'persona.sexo' => 'required|in:M,F',

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
                ],
                [
                    // ⚠️ Mensajes personalizados
                    'required' => 'El campo es obligatorio.',
                    'before_or_equal' => 'El cliente debe tener al menos 18 años de edad.',
                    'email' => 'Ingrese un correo electrónico válido.',
                    'date' => 'El campo debe tener un formato de fecha válido.',
                    'in' => 'El valor seleccionado para no es válido.',
                    'integer' => 'El campo debe ser un número entero.',
                    'size' => 'El campo debe tener exactamente :size caracteres.',
                    'max' => 'El campo no puede exceder los :max caracteres.',
                    'min' => 'El campo debe tener al menos :min caracteres.',
                    'exists' => 'El valor seleccionado para no existe en la base de datos.',
                    'digits' => 'El campo debe tener exactamente :digits dígitos.',
                    'starts_with' => 'El número telefónico debe comenzar con 9.',
                ]
            )->validate();

            DB::transaction(function () {
                // ✅ Actualizar persona
                $this->clienteSeleccionado->persona->update([
                    'nombre' => $this->persona['nombre'],
                    'apellido_paterno' => $this->persona['apellido_paterno'],
                    'apellido_materno' => $this->persona['apellido_materno'],
                    'fecha_nacimiento' => $this->persona['fecha_nacimiento'],
                    'sexo' => $this->persona['sexo'],
                    'nacionalidad' => $this->persona['nacionalidad'] ?? null,
                    'id_tipo_documento' => $this->persona['id_tipo_documento'],
                    'correo_electronico_personal' => $this->persona['correo_electronico_personal'],
                    'correo_electronico_secundario' => $this->persona['correo_electronico_secundario'] ?? null,
                    'numero_telefono_personal' => $this->persona['numero_telefono_personal'] ?? null,
                    'numero_telefono_secundario' => $this->persona['numero_telefono_secundario'] ?? null,
                    'numero_documento' => $this->persona['numero_documento'],
                    'fecha_actualizacion' => now(),
                ]);

                // ✅ Actualizar dirección
                $this->clienteSeleccionado->persona->direccion->update([
                    'tipo_calle' => $this->direccion['tipo_calle'] ?? null,
                    'nombre_calle' => $this->direccion['nombre_calle'],
                    'numero' => $this->direccion['numero'],
                    'referencia' => $this->direccion['referencia'] ?? null,
                    'codigo_postal' => $this->direccion['codigo_postal'] ?? null,
                    'zona' => $this->direccion['zona'],
                    'codigo_ubigeo' => $this->direccion['codigo_ubigeo'],
                    'fecha_actualizacion' => now(),
                ]);
            });

            // ✅ Notificar y cerrar modal
            $this->cerrarModal();
            $this->dispatch('notify', title: 'Success', description: 'Cliente actualizado correctamente.', type: 'success');
            $this->dispatch('clientesUpdated');
            $this->loading = false;
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error de validación. Verifique los campos.', type: 'error');
            throw $e; // Livewire muestra los mensajes en los inputs
            $this->loading = false;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar cliente', [
                'error' => $e->getMessage(),
                'persona' => $this->persona,
                'direccion' => $this->direccion
            ]);
            $this->dispatch('notify', title: 'Error', description: 'Error al actualizar el cliente: ' . $e->getMessage(), type: 'error');
        } finally {
            $this->loading = false;
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

        $this->direccion = [
            'zona' => '',
            'tipo_calle' => '', // 🔹 Asegurar que se resetee
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
    }

    #[\Livewire\Attributes\On('abrirModalCliente')]
    public function abrirModalCliente($clienteId)
    {
        $this->loading = true;
        try {
            $this->clienteSeleccionado = ModelsClientes::with(['persona.direccion'])->findOrFail($clienteId);
            $persona = $this->clienteSeleccionado->persona;
            $direccion = $persona->direccion;

            // Cargar datos personales
            $this->persona = [
                'numero_documento' => $persona->numero_documento,
                'nombre' => $persona->nombre,
                'apellido_paterno' => $persona->apellido_paterno,
                'apellido_materno' => $persona->apellido_materno,
                'fecha_nacimiento' => $persona->fecha_nacimiento,
                'sexo' => $persona->sexo,
                'nacionalidad' => $persona->nacionalidad,
                'id_tipo_documento' => $persona->id_tipo_documento,
                'correo_electronico_personal' => $persona->correo_electronico_personal,
                'correo_electronico_secundario' => $persona->correo_electronico_secundario,
                'numero_telefono_personal' => $persona->numero_telefono_personal,
                'numero_telefono_secundario' => $persona->numero_telefono_secundario,
            ];

            // Cargar dirección
            $this->direccion = [
                'zona' => $direccion->zona,
                'tipo_calle' => $direccion->tipo_calle,
                'nombre_calle' => $direccion->nombre_calle,
                'numero' => $direccion->numero,
                'codigo_postal' => $direccion->codigo_postal,
                'referencia' => $direccion->referencia,
                'codigo_ubigeo' => $direccion->codigo_ubigeo,
            ];

            // Identificar Ubigeo y preparar selects
            $ubigeo = Ubigeo::where('codigo_ubigeo', $direccion->codigo_ubigeo)->first();
            if ($ubigeo) {
                $this->departamentoSeleccionado = $ubigeo->departamento;
                $this->provincias = Ubigeo::where('departamento', $ubigeo->departamento)
                    ->select('provincia')
                    ->distinct()
                    ->pluck('provincia')
                    ->toArray();

                $this->provinciaSeleccionada = $ubigeo->provincia;
                $this->distritos = Ubigeo::where('departamento', $ubigeo->departamento)
                    ->where('provincia', $ubigeo->provincia)
                    ->get();
            }

            $this->modalEditar = true;
        } catch (\Exception $e) {
            Log::error('Error al abrir modal de edición de cliente', [
                'error' => $e->getMessage(),
                'clienteId' => $clienteId,
            ]);

            $this->dispatch('notify', title: 'Error', description: 'No se pudo cargar el cliente.', type: 'error');
        } finally {
            $this->loading = false;
        }
    }

    public function redirigirAVentas()
    {
        return redirect()->route('ventas.registrar'); // Asegúrate de que la ruta 'ventas' exista
    }

    public function cerrarModal()
    {
        $this->resetValidation();

        $this->modalEditar = false;
        $this->clienteSeleccionado = null;

        // Limpiar los arrays de datos
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

        $this->direccion = [
            'zona' => '',
            'tipo_calle' => '', // 🔹 Asegurar que se resetee
            'nombre_calle' => '',
            'numero' => '',
            'codigo_postal' => '',
            'referencia' => '',
            'codigo_ubigeo' => ''
        ];

        // Resetear ubigeo del modal de edición
        $this->departamentoSeleccionado = '';
        $this->provinciaSeleccionada = '';
        $this->provincias = [];
        $this->distritos = [];
        // Resetar errores
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.mantenimiento.mascotas.clientes');
    }
}
