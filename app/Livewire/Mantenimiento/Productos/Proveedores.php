<?php

namespace App\Livewire\Mantenimiento\Productos;

use App\Models\Direccion;
use App\Models\Proveedor;
use App\Models\Ubigeo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Proveedores extends Component
{
    public $proveedor = [
        'nombre_proveedor' => '',
        'ruc' => '',
        'telefono' => '',
        'correo_electronico_empresa' => '',
        'telefono_contacto' => '',
        'telefono_secundario' => '',
        'correo_electronico_encargado' => '',
        'pais' => '',
    ];

    public $direccion = [
        'zona' => '',
        'tipo_calle' => '',
        'nombre_calle' => '',
        'numero' => '',
        'codigo_postal' => '',
        'referencia' => '',
        'codigo_ubigeo' => ''
    ];

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

    protected $messages = [
        'proveedor.nombre_proveedor.required' => 'El nombre del proveedor es obligatorio.',
        'proveedor.nombre_proveedor.max' => 'El nombre no puede tener más de 255 caracteres.',
        'proveedor.ruc.required' => 'El RUC es obligatorio.',
        'proveedor.ruc.digits' => 'El RUC debe tener exactamente 11 dígitos.',
        'proveedor.ruc.unique' => 'Este RUC ya está registrado.',
        'proveedor.correo_electronico_empresa.required' => 'El correo electrónico de la empresa es obligatorio.',
        'proveedor.correo_electronico_empresa.email' => 'Ingrese un correo electrónico válido para la empresa.',
        'proveedor.correo_electronico_empresa.max' => 'El correo de la empresa no puede tener más de 255 caracteres.',
        'proveedor.correo_electronico_empresa.unique' => 'Este correo electrónico de empresa ya está registrado.',
        'proveedor.telefono_contacto.required' => 'El teléfono de contacto es obligatorio.',
        'proveedor.telefono_contacto.max' => 'El teléfono de contacto no puede tener más de 15 caracteres.',
        'proveedor.telefono_contacto.digits' => 'El teléfono de contacto debe tener exactamente 9 dígitos.',
        'proveedor.telefono_contacto.starts_with' => 'El teléfono de contacto debe comenzar con 9.',
        'proveedor.telefono_secundario.max' => 'El teléfono secundario no puede tener más de 15 caracteres.',
        'proveedor.telefono_secundario.digits' => 'El teléfono secundario debe tener exactamente 9 dígitos.',
        'proveedor.telefono_secundario.starts_with' => 'El teléfono secundario debe comenzar con 9.',
        'proveedor.correo_electronico_encargado.email' => 'Ingrese un correo electrónico válido para el encargado.',
        'proveedor.correo_electronico_encargado.max' => 'El correo del encargado no puede tener más de 255 caracteres.',
        'proveedor.pais.required' => 'Debe seleccionar un país.',
        'proveedor.pais.in' => 'Debe seleccionar un país válido.',

        // Mensajes para edición
        'proveedorEditar.telefono_contacto.required' => 'El teléfono de contacto es obligatorio.',
        'proveedorEditar.telefono_contacto.numeric' => 'El teléfono de contacto debe ser un número válido.',
        'proveedorEditar.telefono_contacto.digits' => 'El teléfono de contacto debe tener exactamente 9 dígitos.',
        'proveedorEditar.telefono_contacto.starts_with' => 'El teléfono de contacto debe comenzar con 9.',
        'proveedorEditar.telefono_secundario.numeric' => 'El teléfono secundario debe ser un número válido.',
        'proveedorEditar.telefono_secundario.digits' => 'El teléfono secundario debe tener exactamente 9 dígitos.',
        'proveedorEditar.telefono_secundario.starts_with' => 'El teléfono secundario debe comenzar con 9.',
        'proveedorEditar.correo_electronico_empresa.required' => 'El correo electrónico de la empresa es obligatorio.',
        'proveedorEditar.correo_electronico_empresa.email' => 'Ingrese un correo electrónico válido para la empresa.',
        'proveedorEditar.correo_electronico_empresa.max' => 'El correo de la empresa no puede tener más de 255 caracteres.',
        'proveedorEditar.correo_electronico_encargado.email' => 'Ingrese un correo electrónico válido para el encargado.',
        'proveedorEditar.correo_electronico_encargado.max' => 'El correo del encargado no puede tener más de 255 caracteres.',
        'proveedorEditar.pais.required' => 'Debe seleccionar un país.',
        'proveedorEditar.pais.in' => 'Debe seleccionar un país válido.',

        // Dirección - Registro
        'direccion.codigo_ubigeo.required' => 'El campo es obligatorio.',
        'direccion.codigo_ubigeo.exists' => 'El distrito seleccionado no es válido.',
        'direccion.tipo_calle.required' => 'El tipo de calle es obligatorio.',
        'direccion.tipo_calle.max' => 'El tipo de calle no puede tener más de 50 caracteres.',
        'direccion.nombre_calle.required' => 'El nombre de calle es obligatorio.',
        'direccion.nombre_calle.max' => 'El nombre de calle no puede tener más de 255 caracteres.',
        'direccion.numero.required' => 'El número es obligatorio.',
        'direccion.numero.max' => 'El número no puede tener más de 10 caracteres.',
        'direccion.zona.required' => 'La zona es obligatoria.',
        'direccion.zona.max' => 'La zona no puede tener más de 255 caracteres.',
        'direccion.codigo_postal.required' => 'El código postal es obligatorio.',
        'direccion.codigo_postal.max' => 'El código postal no puede tener más de 10 caracteres.',
        'direccion.referencia.max' => 'La referencia no puede tener más de 255 caracteres.',

        // Dirección - Edición
        'direccionEditar.tipo_calle.required' => 'El tipo de calle es obligatorio.',
        'direccionEditar.tipo_calle.max' => 'El tipo de calle no puede tener más de 50 caracteres.',
        'direccionEditar.nombre_calle.required' => 'El nombre de calle es obligatorio.',
        'direccionEditar.nombre_calle.max' => 'El nombre de calle no puede tener más de 255 caracteres.',
        'direccionEditar.numero.required' => 'El número es obligatorio.',
        'direccionEditar.numero.max' => 'El número no puede tener más de 10 caracteres.',
        'direccionEditar.zona.required' => 'La zona es obligatoria.',
        'direccionEditar.zona.max' => 'La zona no puede tener más de 255 caracteres.',
        'direccionEditar.codigo_postal.required' => 'El código postal es obligatorio.',
        'direccionEditar.codigo_postal.max' => 'El código postal no puede tener más de 10 caracteres.',
        'direccionEditar.referencia.max' => 'La referencia no puede tener más de 255 caracteres.',
        'direccionEditar.codigo_ubigeo.required' => 'Debe seleccionar un distrito.',
        'direccionEditar.codigo_ubigeo.size' => 'El código de ubigeo debe tener exactamente 6 caracteres.',
    ];

    public $departamentos = [];
    public $provincias = [];
    public $distritos = [];

    public $departamentoSeleccionado = '';
    public $provinciaSeleccionada = '';
    public $modalEditar = false;
    public $proveedorEncontrado = true;
    public $proveedorSeleccionado;

    // Variables para edición con mejor estructura
    public $proveedorEditar = [];
    public $direccionEditar = [];

    // Variables para ubigeo en edición
    public $departamentoSeleccionadoEditar = '';
    public $provinciaSeleccionadaEditar = '';
    public $provinciasEditar = [];
    public $distritosEditar = [];

    public $loading = false;
    public $loadingUbigeo = false;

    public function mount()
    {
        $this->departamentos = Ubigeo::select("departamento")->distinct()->pluck('departamento')->toArray();
        $this->resetForm();
    }

    public function updated($propertyName)
    {
        // Validar campos de proveedor en registro
        if (str_starts_with($propertyName, 'proveedor.')) {
            $this->validateOnly($propertyName, [
                'proveedor.nombre_proveedor' => 'required|string|max:255',
                'proveedor.ruc' => 'required|digits:11|unique:proveedores,ruc',
                'proveedor.correo_electronico_empresa' => 'required|unique:proveedores,correo_electronico_empresa|email|max:255',
                'proveedor.telefono_contacto' => [
                    'required',
                    'numeric',
                    'digits:9',
                    'starts_with:9',
                    function ($attribute, $value, $fail) {
                        if (!empty($this->proveedor['telefono_secundario']) && $value === $this->proveedor['telefono_secundario']) {
                            $fail('El teléfono de contacto no puede ser igual al teléfono secundario.');
                        }
                    }
                ],
                'proveedor.telefono_secundario' => [
                    'nullable',
                    'numeric',
                    'digits:9',
                    'starts_with:9',
                    function ($attribute, $value, $fail) {
                        if (!empty($value) && $value === $this->proveedor['telefono_contacto']) {
                            $fail('El teléfono secundario no puede ser igual al teléfono de contacto.');
                        }
                    }
                ],
                'proveedor.correo_electronico_encargado' => 'nullable|email|max:255',
                'proveedor.pais' => 'required|string|in:peru,colombia',
            ], [
                'required' => 'El campo es obligatorio.',
                'email' => 'Ingrese un correo electrónico válido.',
                'max' => 'El campo no puede exceder los :max caracteres.',
                'numeric' => 'El campo debe ser un número válido.',
                'digits' => 'El campo debe tener exactamente :digits dígitos.',
                'unique' => 'Este valor ya está registrado en el sistema.',
                'in' => 'El valor seleccionado no es válido.',
                'starts_with' => 'El número telefónico debe comenzar con 9.',
            ]);
        }

        // Validar campos de dirección en registro
        if (str_starts_with($propertyName, 'direccion.')) {
            $this->validateOnly($propertyName, [
                'direccion.tipo_calle' => 'required|string|max:50',
                'direccion.nombre_calle' => 'required|string|max:255',
                'direccion.numero' => 'required|string|max:10',
                'direccion.zona' => 'required|string|max:255',
                'direccion.codigo_postal' => 'required|string|max:10',
                'direccion.referencia' => 'nullable|string|max:255',
                'direccion.codigo_ubigeo' => 'required|exists:ubigeos,codigo_ubigeo',
            ], [
                'required' => 'El campo es obligatorio.',
                'max' => 'El campo no puede exceder los :max caracteres.',
                'exists' => 'El valor seleccionado no existe en la base de datos.',
            ]);
        }

        // Validar campos de proveedor en edición
        if (str_starts_with($propertyName, 'proveedorEditar.')) {
            $this->validateOnly($propertyName, [
                'proveedorEditar.telefono_contacto' => [
                    'required',
                    'numeric',
                    'digits:9',
                    'starts_with:9',
                    function ($attribute, $value, $fail) {
                        if (!empty($this->proveedorEditar['telefono_secundario']) && $value === $this->proveedorEditar['telefono_secundario']) {
                            $fail('El teléfono de contacto no puede ser igual al teléfono secundario.');
                        }
                    }
                ],
                'proveedorEditar.telefono_secundario' => [
                    'nullable',
                    'numeric',
                    'digits:9',
                    'starts_with:9',
                    function ($attribute, $value, $fail) {
                        if (!empty($value) && $value === $this->proveedorEditar['telefono_contacto']) {
                            $fail('El teléfono secundario no puede ser igual al teléfono de contacto.');
                        }
                    }
                ],
                'proveedorEditar.correo_electronico_empresa' => 'required|email|max:255',
                'proveedorEditar.correo_electronico_encargado' => 'nullable|email|max:255',
                'proveedorEditar.pais' => 'required|string|in:peru,colombia',
            ], [
                'required' => 'El campo es obligatorio.',
                'email' => 'Ingrese un correo electrónico válido.',
                'max' => 'El campo no puede exceder los :max caracteres.',
                'numeric' => 'El campo debe ser un número válido.',
                'digits' => 'El campo debe tener exactamente :digits dígitos.',
                'in' => 'El valor seleccionado no es válido.',
                'starts_with' => 'El número telefónico debe comenzar con 9.',
            ]);
        }

        // Validar campos de dirección en edición
        if (str_starts_with($propertyName, 'direccionEditar.')) {
            $this->validateOnly($propertyName, [
                'direccionEditar.tipo_calle' => 'required|string|max:50',
                'direccionEditar.nombre_calle' => 'required|string|max:255',
                'direccionEditar.numero' => 'required|string|max:10',
                'direccionEditar.zona' => 'required|string|max:255',
                'direccionEditar.codigo_postal' => 'required|string|max:10',
                'direccionEditar.referencia' => 'nullable|string|max:255',
                'direccionEditar.codigo_ubigeo' => 'required|string|size:6',
            ], [
                'required' => 'El campo es obligatorio.',
                'max' => 'El campo no puede exceder los :max caracteres.',
                'size' => 'El campo debe tener exactamente :size caracteres.',
            ]);
        }
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

    // Métodos para ubigeo en edición
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

    public function guardar()
    {
        $validatedData = $this->validate([
            'proveedor.nombre_proveedor' => 'required|string|max:255',
            'proveedor.ruc' => 'required|digits:11|unique:proveedores,ruc',
            'proveedor.correo_electronico_empresa' => 'required|unique:proveedores,correo_electronico_empresa|email|max:255',
            'proveedor.telefono_contacto' => [
                'required',
                'numeric',
                'digits:9',
                'starts_with:9',
                function ($attribute, $value, $fail) {
                    if (!empty($this->proveedor['telefono_secundario']) && $value === $this->proveedor['telefono_secundario']) {
                        $fail('El teléfono de contacto no puede ser igual al teléfono secundario.');
                    }
                }
            ],
            'proveedor.telefono_secundario' => [
                'nullable',
                'numeric',
                'digits:9',
                'starts_with:9',
                function ($attribute, $value, $fail) {
                    if (!empty($value) && $value === $this->proveedor['telefono_contacto']) {
                        $fail('El teléfono secundario no puede ser igual al teléfono de contacto.');
                    }
                }
            ],
            'proveedor.correo_electronico_encargado' => 'nullable|email|max:255',
            'proveedor.pais' => 'required|string|in:peru,colombia',
            'direccion.codigo_ubigeo' => 'required|exists:ubigeos,codigo_ubigeo',
            'direccion.tipo_calle' => 'required|string|max:50',
            'direccion.nombre_calle' => 'required|string|max:255',
            'direccion.numero' => 'required|string|max:10',
            'direccion.zona' => 'required|string|max:255',
            'direccion.codigo_postal' => 'required|string|max:10',
            'direccion.referencia' => 'nullable|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($validatedData, &$proveedor) {
                $direccion = Direccion::create($validatedData['direccion']);

                $proveedor = Proveedor::create(array_merge($validatedData['proveedor'], [
                    'id_direccion' => $direccion->id_direccion
                ]));
            });

            $this->dispatch('notify', title: 'Success', description: 'Proveedor creado correctamente.', type: 'success');
            $this->proveedorEncontrado = true;
            $this->resetForm();
            $this->dispatch('proveedoresUpdated');
        } catch (\Exception $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error al registrar el proveedor.', type: 'error');
            Log::error('Error al registrar proveedor', ['error' => $e->getMessage()]);
        }
    }

    public function buscarRuc()
    {
        $ruc = $this->proveedor['ruc'];

        if (strlen($ruc) !== 11) {
            $this->dispatch('notify', title: 'Error', description: 'El RUC debe tener exactamente 11 dígitos.', type: 'error');
            return;
        }

        try {
            $response = Http::withHeaders([
                "content-type" => "application/json",
                'Authorization' => "Bearer " . env("DECOLECTA_API_KEY"),
            ])->withOptions(['verify' => false])
                ->get("https://api.decolecta.com/v1/sunat/ruc", [
                    'numero' => $ruc
                ]);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['razon_social'])) {
                    $this->proveedor['nombre_proveedor'] = $data['razon_social'];

                    // 🔹 LIMPIAR ERRORES DEL CAMPO NOMBRE CUANDO SE ENCUENTRA EL RUC
                    $this->resetErrorBag(['proveedor.nombre_proveedor']);

                    $this->dispatch('notify', title: 'Success', description: 'Razón social cargada desde SUNAT.', type: 'success');
                    $this->proveedorEncontrado = true;
                } else {
                    $this->dispatch('notify', title: 'Error', description: 'No se encontró la razón social para este RUC. Ingrese los datos manualmente', type: 'error');
                    Log::error('Error al consultar RUC', ['error' => 'No se encontró la razón social']);
                    $this->proveedorEncontrado = false;
                }
            } else {
                $this->dispatch('notify', title: 'Error', description: 'Error al consultar el RUC. Intente nuevamente. Ingrese los datos manualmente', type: 'error');
                $this->proveedorEncontrado = false;
            }
        } catch (\Exception $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error al conectar con la API. Pruebe ingresando los datos manualmente', type: 'error');
            Log::error('Error al consultar RUC', ['error' => $e->getMessage()]);
            $this->proveedorEncontrado = false;
        }
    }

    #[\Livewire\Attributes\On('editarProveedor')]
    public function abrirModalEditar($proveedorId)
    {
        $this->loading = true;
        try {
            $this->proveedorSeleccionado = Proveedor::with(['direccion'])
                ->findOrFail($proveedorId);

            $this->proveedorEditar = [
                'id_proveedor' => $proveedorId,
                'nombre_proveedor' => $this->proveedorSeleccionado->nombre_proveedor,
                'ruc' => $this->proveedorSeleccionado->ruc,
                'telefono_contacto' => $this->proveedorSeleccionado->telefono_contacto,
                'correo_electronico_empresa' => $this->proveedorSeleccionado->correo_electronico_empresa,
                'telefono_secundario' => $this->proveedorSeleccionado->telefono_secundario,
                'correo_electronico_encargado' => $this->proveedorSeleccionado->correo_electronico_encargado,
                'pais' => $this->proveedorSeleccionado->pais,
                "estado" => $this->proveedorSeleccionado->estado,
            ];

            $this->direccionEditar = [
                'tipo_calle' => $this->proveedorSeleccionado->direccion->tipo_calle ?? '',
                'nombre_calle' => $this->proveedorSeleccionado->direccion->nombre_calle ?? '',
                'numero' => $this->proveedorSeleccionado->direccion->numero ?? '',
                'referencia' => $this->proveedorSeleccionado->direccion->referencia ?? '',
                'codigo_postal' => $this->proveedorSeleccionado->direccion->codigo_postal ?? '',
                'zona' => $this->proveedorSeleccionado->direccion->zona ?? '',
                'codigo_ubigeo' => $this->proveedorSeleccionado->direccion->codigo_ubigeo ?? '',
            ];

            // Cargar ubigeo de forma síncrona
            $this->cargarUbigeoSincrono($this->direccionEditar['codigo_ubigeo'] ?? null);

            $this->modalEditar = true;
        } catch (\Exception $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error al cargar datos: ' . $e->getMessage(), type: 'error');
        } finally {
            $this->loading = false;
        }
    }

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
            $this->loadingUbigeo = false;
        }
    }

    public function actualizarProveedor()
    {
        $this->loading = true;

        try {
            if (!$this->proveedorSeleccionado) {
                $this->dispatch('notify', title: 'Error', description: 'No hay un proveedor seleccionado.', type: 'error');
                return;
            }

            $validatedData = $this->validate([
                'proveedorEditar.telefono_contacto' => [
                    'required',
                    'numeric',
                    'digits:9',
                    'starts_with:9',
                    function ($attribute, $value, $fail) {
                        if (!empty($this->proveedorEditar['telefono_secundario']) && $value === $this->proveedorEditar['telefono_secundario']) {
                            $fail('El teléfono de contacto no puede ser igual al teléfono secundario.');
                        }
                    }
                ],
                'proveedorEditar.telefono_secundario' => [
                    'nullable',
                    'numeric',
                    'digits:9',
                    'starts_with:9',
                    function ($attribute, $value, $fail) {
                        if (!empty($value) && $value === $this->proveedorEditar['telefono_contacto']) {
                            $fail('El teléfono secundario no puede ser igual al teléfono de contacto.');
                        }
                    }
                ],
                'proveedorEditar.correo_electronico_empresa' => 'required|email|max:255',
                'proveedorEditar.correo_electronico_encargado' => 'nullable|email|max:255',
                'proveedorEditar.pais' => 'required|string|in:peru,colombia',
                'direccionEditar.tipo_calle' => 'required|string|max:50',
                'direccionEditar.nombre_calle' => 'required|string|max:255',
                'direccionEditar.numero' => 'required|string|max:10',
                'direccionEditar.zona' => 'required|string|max:255',
                'direccionEditar.codigo_postal' => 'required|string|max:10',
                'direccionEditar.referencia' => 'nullable|string|max:255',
                'direccionEditar.codigo_ubigeo' => 'required|string|size:6',
            ]);

            DB::transaction(function () {
                // Actualizar proveedor
                $this->proveedorSeleccionado->update([
                    'telefono_contacto' => $this->proveedorEditar['telefono_contacto'],
                    'telefono_secundario' => $this->proveedorEditar['telefono_secundario'],
                    'correo_electronico_empresa' => $this->proveedorEditar['correo_electronico_empresa'],
                    'correo_electronico_encargado' => $this->proveedorEditar['correo_electronico_encargado'],
                    'pais' => $this->proveedorEditar['pais'],
                    'fecha_actualizacion' => now(),
                    'estado' => $this->proveedorEditar['estado'],
                ]);

                // Actualizar dirección
                $this->proveedorSeleccionado->direccion->update($this->direccionEditar);
            });

            $this->cerrarModal();
            $this->dispatch('notify', title: 'Success', description: 'Proveedor actualizado correctamente.', type: 'success');
            $this->dispatch('proveedoresUpdated');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error de validación. Verifique los campos.', type: 'error');
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar proveedor', [
                'error' => $e->getMessage(),
                'proveedor_id' => $this->proveedorSeleccionado->id_proveedor ?? null,
            ]);
            $this->dispatch('notify', title: 'Error', description: 'Error al actualizar el proveedor: ' . $e->getMessage(), type: 'error');
        } finally {
            $this->loading = false;
        }
    }

    public function cerrarModal()
    {
        $this->modalEditar = false;
        $this->proveedorSeleccionado = null;
        $this->proveedorEditar = [];
        $this->direccionEditar = [];
        $this->departamentoSeleccionadoEditar = '';
        $this->provinciaSeleccionadaEditar = '';
        $this->provinciasEditar = [];
        $this->distritosEditar = [];
        $this->loadingUbigeo = false;
    }

    // Método para resetear el formulario
    public function resetForm()
    {
        $this->proveedor = [
            'nombre_proveedor' => '',
            'ruc' => '',
            'correo_electronico_empresa' => '',
            'telefono_contacto' => '',
            'telefono_secundario' => '',
            'correo_electronico_encargado' => '',
            'pais' => '',
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
        // Resetar errores
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.mantenimiento.productos.proveedores');
    }
}
