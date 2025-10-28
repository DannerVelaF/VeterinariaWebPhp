<?php

namespace App\Livewire\Mantenimiento\Mascotas;

use App\Models\Mascota;
use App\Models\Raza;
use App\Models\Clientes;
use App\Models\Colores;
use App\Models\Especie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Carbon\Carbon;
use Exception;

class Mascotas extends Component
{
    protected $listeners = [
        'razaUpdated' => 'refreshData',
        'mascotaUpdated' => '$refresh',
    ];

    public $mascota = [
        'id_cliente' => '',
        'id_especie' => '',
        'id_raza' => '',
        'nombre_mascota' => '',
        'fecha_nacimiento' => '',
        'sexo' => '',
        'id_color' => '', // CAMBIO: De color_primario a id_color
        'peso_actual' => '',
        'observacion' => '',
    ];


    public $mascotaEditar = [
        'id_mascota' => null,
        'id_cliente' => '',
        'id_especie' => '',
        'id_raza' => '',
        'nombre_mascota' => '',
        'fecha_nacimiento' => '',
        'sexo' => '',
        'id_color' => '', // CAMBIO: De color_primario a id_color
        'peso_actual' => '',
        'observacion' => '',
    ];

    public $especieSeleccionada = '';
    public $buscarCliente = '';
    public $resultadosClientes = [];
    public $clienteSeleccionado = null;
    public $razas = [];
    public $colores = [];
    public $especies = [];
    public $mascotaSeleccionada = null;
    public $modalEditar = false;
    public $edad_meses = null;
    public $edad_humana = null;


    public function updated($propertyName)
    {
        // Validar campos de mascota en registro
        if (str_starts_with($propertyName, 'mascota.')) {
            $this->validateOnly($propertyName, [
                'mascota.id_cliente' => 'required|exists:clientes,id_cliente',
                'mascota.id_especie' => 'required|exists:especies,id_especie',
                'mascota.id_raza' => 'required|exists:razas,id_raza',
                'mascota.nombre_mascota' => 'required|string|max:150|regex:/^[\pL\s\-]+$/u',
                'mascota.fecha_nacimiento' => 'nullable|date|before_or_equal:today',
                'mascota.sexo' => 'nullable|in:macho,hembra',
                'mascota.id_color' => 'required|exists:colores,id_color', // CAMBIO: Validación para id_color
                'mascota.peso_actual' => 'nullable|numeric|min:0',
                'mascota.observacion' => 'nullable|string|max:500',
            ], [
                'mascota.id_cliente.required' => 'Debe seleccionar un cliente.',
                'mascota.id_cliente.exists' => 'El cliente seleccionado no es válido.',
                'mascota.id_especie.required' => 'Debe seleccionar una especie.',
                'mascota.id_especie.exists' => 'La especie seleccionada no es válida.',
                'mascota.id_raza.required' => 'Debe seleccionar una raza.',
                'mascota.id_raza.exists' => 'La raza seleccionada no es válida.',
                'mascota.nombre_mascota.required' => 'El nombre de la mascota es obligatorio.',
                'mascota.nombre_mascota.string' => 'El nombre debe ser texto.',
                'mascota.nombre_mascota.max' => 'El nombre no puede tener más de 150 caracteres.',
                'mascota.nombre_mascota.regex' => 'El nombre solo puede contener letras, espacios y guiones.',
                'mascota.fecha_nacimiento.date' => 'La fecha de nacimiento debe ser una fecha válida.',
                'mascota.fecha_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser futura.',
                'mascota.sexo.in' => 'El sexo debe ser Macho o Hembra.',
                'mascota.id_color.required' => 'El color es obligatorio.', // NUEVO mensaje
                'mascota.id_color.exists' => 'El color seleccionado no es válido.', // NUEVO mensaje
                'mascota.peso_actual.numeric' => 'El peso debe ser un número.',
                'mascota.peso_actual.min' => 'El peso no puede ser negativo.',
                'mascota.observacion.string' => 'La observación debe ser texto.',
                'mascota.observacion.max' => 'La observación no puede tener más de 500 caracteres.',
            ]);
        }

        // Validar campos de mascota en edición
        if (str_starts_with($propertyName, 'mascotaEditar.')) {
            $this->validateOnly($propertyName, [
                'mascotaEditar.nombre_mascota' => 'required|string|max:100',
                'mascotaEditar.id_cliente' => 'required|integer|exists:clientes,id_cliente',
                'mascotaEditar.id_especie' => 'required|integer|exists:especies,id_especie',
                'mascotaEditar.id_raza' => 'required|integer|exists:razas,id_raza',
                'mascotaEditar.fecha_nacimiento' => 'nullable|date|before_or_equal:today',
                'mascotaEditar.sexo' => 'required|string|in:macho,macho',
                'mascotaEditar.id_color' => 'required|exists:colores,id_color', // CAMBIO
                'mascotaEditar.peso_actual' => 'nullable|numeric|min:0',
                'mascotaEditar.observacion' => 'nullable|string|max:500',
            ], [
                'mascotaEditar.nombre_mascota.required' => 'El nombre de la mascota es obligatorio.',
                'mascotaEditar.nombre_mascota.string' => 'El nombre debe ser texto.',
                'mascotaEditar.nombre_mascota.max' => 'El nombre no puede tener más de 100 caracteres.',
                'mascotaEditar.id_cliente.required' => 'Debe seleccionar un cliente.',
                'mascotaEditar.id_cliente.integer' => 'El ID del cliente debe ser un número.',
                'mascotaEditar.id_cliente.exists' => 'El cliente seleccionado no es válido.',
                'mascotaEditar.id_especie.required' => 'Debe seleccionar una especie.',
                'mascotaEditar.id_especie.integer' => 'El ID de la especie debe ser un número.',
                'mascotaEditar.id_especie.exists' => 'La especie seleccionada no es válida.',
                'mascotaEditar.id_raza.required' => 'Debe seleccionar una raza.',
                'mascotaEditar.id_raza.integer' => 'El ID de la raza debe ser un número.',
                'mascotaEditar.id_raza.exists' => 'La raza seleccionada no es válida.',
                'mascotaEditar.fecha_nacimiento.date' => 'La fecha de nacimiento debe ser una fecha válida.',
                'mascotaEditar.fecha_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser futura.',
                'mascotaEditar.sexo.required' => 'El sexo es obligatorio.',
                'mascotaEditar.sexo.string' => 'El sexo debe ser texto.',
                'mascotaEditar.sexo.in' => 'El sexo debe ser Macho o Hembra.',
                'mascotaEditar.id_color.required' => 'El color es obligatorio.', // NUEVO
                'mascotaEditar.id_color.exists' => 'El color seleccionado no es válido.', // NUEVO
                'mascotaEditar.peso_actual.numeric' => 'El peso debe ser un número.',
                'mascotaEditar.peso_actual.min' => 'El peso no puede ser negativo.',
                'mascotaEditar.observacion.string' => 'La observación debe ser texto.',
                'mascotaEditar.observacion.max' => 'La observación no puede tener más de 500 caracteres.',
            ]);
        }

        // Validación específica para fecha de nacimiento en tiempo real
        if ($propertyName === 'mascota.fecha_nacimiento') {
            $this->validateOnly($propertyName, [
                'mascota.fecha_nacimiento' => 'nullable|date|before_or_equal:today',
            ], [
                'mascota.fecha_nacimiento.date' => 'La fecha de nacimiento debe ser una fecha válida.',
                'mascota.fecha_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser futura.',
            ]);
        }

        // Validación específica para fecha de nacimiento en edición en tiempo real
        if ($propertyName === 'mascotaEditar.fecha_nacimiento') {
            $this->validateOnly($propertyName, [
                'mascotaEditar.fecha_nacimiento' => 'nullable|date|before_or_equal:today',
            ], [
                'mascotaEditar.fecha_nacimiento.date' => 'La fecha de nacimiento debe ser una fecha válida.',
                'mascotaEditar.fecha_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser futura.',
            ]);
        }
    }
    protected $casts = [
        'clienteSeleccionado' => 'array',
        'resultadosClientes' => 'array',
        'razas' => 'collection',
        'especies' => 'collection',
    ];

    public function mount()
    {
        $this->especies = Especie::where('estado', 'activo')->get();
        $this->razas = collect();
        $this->colores = Colores::orderBy('nombre_color', 'asc')->get(); // Cargar colores ordenados
        $this->clienteSeleccionado = null;
    }

    public function refreshData()
    {
        $this->especies = Especie::where('estado', 'activo')->get();
        // Si hay una especie seleccionada, recargar sus razas
        if ($this->mascota['id_especie']) {
            $this->razas = Raza::where('id_especie', $this->mascota['id_especie'])
                ->where('estado', 'activo')
                ->get();
        }
    }

    /** 🔍 Búsqueda automática de clientes */
    public function updatedBuscarCliente($valor)
    {
        if (strlen($valor) >= 2) {
            $clientes = Clientes::join('personas', 'clientes.id_persona', '=', 'personas.id_persona')
                ->where(function ($query) use ($valor) {
                    $query->where('personas.nombre', 'like', "%{$valor}%")
                        ->orWhere('personas.apellido_paterno', 'like', "%{$valor}%")
                        ->orWhere('personas.apellido_materno', 'like', "%{$valor}%")
                        ->orWhere('personas.numero_documento', 'like', "%{$valor}%");
                })
                ->select(
                    'clientes.id_cliente',
                    'personas.nombre',
                    'personas.apellido_paterno',
                    'personas.apellido_materno',
                    'personas.numero_documento as dni',
                    'personas.numero_telefono_personal as telefono',
                    'personas.correo_electronico_personal as correo'
                )
                ->limit(10)
                ->get();

            // Convertir a array simple para evitar problemas de serialización
            $this->resultadosClientes = $clientes->map(function ($cliente) {
                return [
                    'id_cliente' => $cliente->id_cliente,
                    'nombre' => $cliente->nombre,
                    'apellido_paterno' => $cliente->apellido_paterno,
                    'apellido_materno' => $cliente->apellido_materno,
                    'dni' => $cliente->dni,
                    'telefono' => $cliente->telefono,
                    'correo' => $cliente->correo,
                ];
            })->toArray();
        } else {
            $this->resultadosClientes = [];
        }
    }

    /** 🧭 Seleccionar cliente */
    public function seleccionarCliente($idCliente)
    {
        try {
            $cliente = Clientes::join('personas', 'clientes.id_persona', '=', 'personas.id_persona')
                ->where('clientes.id_cliente', $idCliente)
                ->select(
                    'clientes.id_cliente',
                    'personas.nombre',
                    'personas.apellido_paterno',
                    'personas.apellido_materno',
                    'personas.numero_documento as dni',
                    'personas.numero_telefono_personal as telefono',
                    'personas.correo_electronico_personal as correo'
                )
                ->firstOrFail();

            // Asignar como array simple
            $this->clienteSeleccionado = [
                'id_cliente' => $cliente->id_cliente,
                'nombre' => $cliente->nombre,
                'apellido_paterno' => $cliente->apellido_paterno,
                'apellido_materno' => $cliente->apellido_materno,
                'dni' => $cliente->dni,
                'telefono' => $cliente->telefono,
                'correo' => $cliente->correo,
            ];

            $this->mascota['id_cliente'] = $cliente->id_cliente;
            $this->buscarCliente = '';
            $this->resultadosClientes = [];
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->clienteSeleccionado = null;
            $this->mascota['id_cliente'] = '';
            session()->flash('error', 'Cliente no encontrado.');
        }
    }

    /** 🐾 Cargar razas cuando se selecciona una especie - CORREGIDO */
    public function updatedMascota($value, $key)
    {
        if ($key === 'id_especie') {
            if ($value) {
                $this->razas = Raza::where('id_especie', $value)
                    ->where('estado', 'activo')
                    ->get();
                // Limpiar la raza seleccionada cuando cambia la especie
                $this->mascota['id_raza'] = '';
            } else {
                $this->razas = collect();
                $this->mascota['id_raza'] = '';
            }
        }
    }

    /** 🐾 Cargar razas para edición - CORREGIDO */
    public function updatedMascotaEditar($value, $key)
    {
        if ($key === 'id_especie') {
            if ($value) {
                $this->razas = Raza::where('id_especie', $value)
                    ->where('estado', 'activo')
                    ->get();
            } else {
                $this->razas = collect();
            }
        }
    }

    // Método alternativo si el anterior no funciona
    public function cambiarEspecie($especieId)
    {
        if ($especieId) {
            $this->razas = Raza::where('id_especie', $especieId)
                ->where('estado', 'activo')
                ->get();
            $this->mascota['id_raza'] = '';
        } else {
            $this->razas = collect();
            $this->mascota['id_raza'] = '';
        }
    }

    // Método alternativo para edición
    public function cambiarEspecieEditar($especieId)
    {
        if ($especieId) {
            $this->razas = Raza::where('id_especie', $especieId)
                ->where('estado', 'activo')
                ->get();
        } else {
            $this->razas = collect();
        }
    }

    public function limpiarCliente()
    {
        $this->clienteSeleccionado = null;
        $this->mascota['id_cliente'] = '';
        $this->buscarCliente = '';
        $this->resultadosClientes = [];
    }


    public function buscarClientes()
    {
        $this->updatedBuscarCliente($this->buscarCliente);
    }

    /** 🧮 Calcular edad automáticamente */
    public function updatedMascotaFechaNacimiento($valor)
    {
        if ($valor) {
            $fechaNacimiento = Carbon::parse($valor);
            $hoy = Carbon::now();

            $this->edad_meses = $fechaNacimiento->diffInMonths($hoy);
            $this->edad_humana = round(($this->edad_meses / 12) * 7, 1); // 1 año perro = 7 humano
        } else {
            $this->edad_meses = null;
            $this->edad_humana = null;
        }
    }

    /** 🐶 Guardar mascota */
    public function guardarMascota()
    {
        if (!$this->clienteSeleccionado) {
            session()->flash('error', 'Debe seleccionar un cliente antes de registrar una mascota.');
            return;
        }

        $validatedData = $this->validate([
            'mascota.id_cliente' => 'required|exists:clientes,id_cliente',
            'mascota.id_especie' => 'required|exists:especies,id_especie',
            'mascota.id_raza' => 'required|exists:razas,id_raza',
            'mascota.nombre_mascota' => 'required|string|max:150|regex:/^[\pL\s\-]+$/u',
            'mascota.fecha_nacimiento' => 'required|date|before_or_equal:today',
            'mascota.sexo' => 'required|in:macho,hembra',
            'mascota.id_color' => 'required|exists:colores,id_color', // CAMBIO
            'mascota.peso_actual' => 'required|numeric|min:0',
            'mascota.observacion' => 'nullable|string|max:500',
        ], [
            'mascota.id_cliente.required' => 'Debe seleccionar un cliente.',
            'mascota.id_cliente.exists' => 'El cliente seleccionado no es válido.',
            'mascota.id_especie.required' => 'Debe seleccionar una especie.',
            'mascota.id_especie.exists' => 'La especie seleccionada no es válida.',
            'mascota.id_raza.required' => 'Debe seleccionar una raza.',
            'mascota.id_raza.exists' => 'La raza seleccionada no es válida.',
            'mascota.nombre_mascota.required' => 'El nombre de la mascota es obligatorio.',
            'mascota.nombre_mascota.string' => 'El nombre debe ser texto.',
            'mascota.nombre_mascota.max' => 'El nombre no puede tener más de 150 caracteres.',
            'mascota.nombre_mascota.regex' => 'El nombre solo puede contener letras, espacios y guiones.',
            'mascota.fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'mascota.fecha_nacimiento.date' => 'La fecha de nacimiento debe ser una fecha válida.',
            'mascota.fecha_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser futura.',
            'mascota.sexo.required' => 'El sexo es obligatorio.',
            'mascota.sexo.in' => 'El sexo debe ser Macho o Hembra.',
            'mascota.id_color.required' => 'El color es obligatorio.', // NUEVO
            'mascota.id_color.exists' => 'El color seleccionado no es válido.', // NUEVO
            'mascota.peso_actual.required' => 'El peso actual es obligatorio.',
            'mascota.peso_actual.numeric' => 'El peso debe ser un número.',
            'mascota.peso_actual.min' => 'El peso no puede ser negativo.',
            'mascota.observacion.string' => 'La observación debe ser texto.',
            'mascota.observacion.max' => 'La observación no puede tener más de 500 caracteres.',
        ]);

        try {
            DB::transaction(function () use ($validatedData) {
                Mascota::create([
                    'id_cliente' => $validatedData['mascota']['id_cliente'],
                    'id_raza' => $validatedData['mascota']['id_raza'],
                    'nombre_mascota' => $validatedData['mascota']['nombre_mascota'],
                    'fecha_nacimiento' => $validatedData['mascota']['fecha_nacimiento'] ?? null,
                    'sexo' => $validatedData['mascota']['sexo'] ?? null,
                    'id_color' => $validatedData['mascota']['id_color'], // CAMBIO
                    'peso_actual' => $validatedData['mascota']['peso_actual'] ?? null,
                    'observacion' => $validatedData['mascota']['observacion'] ?? null,
                    'estado' => 'activo',
                ]);
            });

            $this->resetForm();
            $this->dispatch("notify", title: 'Success', description: 'Mascota registrada con éxito', type: 'success');
            $this->dispatch('scrollToTop');
        } catch (Exception $e) {
            Log::error('Error al registrar mascota', ['error' => $e->getMessage()]);
            $this->dispatch('notify', title: 'Error', description: 'Error al registrar la mascota: ' . $e->getMessage(), type: 'error');
        }
    }

    /** ➕ Agregar otra mascota del mismo cliente */
    public function agregarOtraMascota()
    {
        $cliente = $this->clienteSeleccionado;
        $this->resetForm(false);
        if ($cliente) {
            $this->clienteSeleccionado = $cliente;
            $this->mascota['id_cliente'] = $cliente->id_cliente;
        }
        $this->dispatch('notify', title: 'Success', description: 'Formulario listo para registrar otra mascota 🐾', type: 'success');
        $this->dispatch('scrollToTop');
    }

    /** 🧹 Limpiar formulario */
    public function resetForm($limpiarCliente = true)
    {
        $this->mascota = [
            'id_cliente' => $limpiarCliente ? '' : $this->mascota['id_cliente'],
            'id_especie' => '',
            'id_raza' => '',
            'nombre_mascota' => '',
            'fecha_nacimiento' => '',
            'sexo' => '',
            'id_color' => '', // CAMBIO
            'peso_actual' => '',
            'observacion' => '',
        ];

        if ($limpiarCliente) {
            $this->clienteSeleccionado = null;
            $this->buscarCliente = '';
            $this->resultadosClientes = [];
        }

        $this->razas = collect();
        $this->edad_meses = null;
        $this->edad_humana = null;

        $this->resetErrorBag();
        $this->resetValidation();
    }


    #[\Livewire\Attributes\On('abrirModalMascota')]
    public function abrirModalMascota($mascotaId)
    {
        $this->mascotaSeleccionada = Mascota::with('raza.especie', 'color')->findOrFail($mascotaId); // Incluir relación color
        $this->mascotaEditar = [
            'id_mascota' => $mascotaId,
            'id_cliente' => $this->mascotaSeleccionada->id_cliente,
            'id_especie' => $this->mascotaSeleccionada->raza->id_especie ?? '',
            'id_raza' => $this->mascotaSeleccionada->id_raza,
            'nombre_mascota' => $this->mascotaSeleccionada->nombre_mascota,
            'fecha_nacimiento' => $this->mascotaSeleccionada->fecha_nacimiento,
            'sexo' => $this->mascotaSeleccionada->sexo,
            'id_color' => $this->mascotaSeleccionada->id_color, // CAMBIO
            'peso_actual' => $this->mascotaSeleccionada->peso_actual,
            'observacion' => $this->mascotaSeleccionada->observacion,
        ];

        // Cargar las razas de la especie seleccionada
        if ($this->mascotaEditar['id_especie']) {
            $this->razas = Raza::where('id_especie', $this->mascotaEditar['id_especie'])
                ->where('estado', 'activo')
                ->get();
        }

        // Calcular edad si existe fecha de nacimiento
        if ($this->mascotaEditar['fecha_nacimiento']) {
            $fechaNacimiento = Carbon::parse($this->mascotaEditar['fecha_nacimiento']);
            $hoy = Carbon::now();

            $this->edad_meses = $fechaNacimiento->diffInMonths($hoy);
            $this->edad_humana = round(($this->edad_meses / 12) * 7, 1);
        }

        $this->modalEditar = true;
    }

    public function updatedMascotaEditarFechaNacimiento($valor)
    {
        if ($valor) {
            $fechaNacimiento = Carbon::parse($valor);
            $hoy = Carbon::now();

            $this->edad_meses = $fechaNacimiento->diffInMonths($hoy);
            $this->edad_humana = round(($this->edad_meses / 12) * 7, 1);
        } else {
            $this->edad_meses = null;
            $this->edad_humana = null;
        }
    }

    public function cerrarModal()
    {
        $this->modalEditar = false;
        $this->mascotaEditar = [
            'id_mascota' => null,
            'id_cliente' => '',
            'id_especie' => '',
            'id_raza' => '',
            'nombre_mascota' => '',
            'fecha_nacimiento' => '',
            'sexo' => '',
            'id_color' => '', // CAMBIO
            'peso_actual' => '',
            'observacion' => '',
        ];
        $this->mascotaSeleccionada = null;
        $this->razas = collect();

        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function guardarEdicion()
    {
        $this->validate([
            'mascotaEditar.nombre_mascota' => 'required|string|max:100',
            'mascotaEditar.id_cliente' => 'required|integer|exists:clientes,id_cliente',
            'mascotaEditar.id_especie' => 'required|integer|exists:especies,id_especie',
            'mascotaEditar.id_raza' => 'required|integer|exists:razas,id_raza',
            'mascotaEditar.fecha_nacimiento' => 'nullable|date|before_or_equal:today',
            'mascotaEditar.sexo' => 'required|string|in:macho,hembra',
            'mascotaEditar.id_color' => 'required|exists:colores,id_color', // CAMBIO
            'mascotaEditar.peso_actual' => 'nullable|numeric|min:0',
            'mascotaEditar.observacion' => 'nullable|string|max:500',
        ], [
            'mascotaEditar.nombre_mascota.required' => 'El nombre de la mascota es obligatorio.',
            'mascotaEditar.nombre_mascota.string' => 'El nombre debe ser texto.',
            'mascotaEditar.nombre_mascota.max' => 'El nombre no puede tener más de 100 caracteres.',
            'mascotaEditar.id_cliente.required' => 'Debe seleccionar un cliente.',
            'mascotaEditar.id_cliente.integer' => 'El ID del cliente debe ser un número.',
            'mascotaEditar.id_cliente.exists' => 'El cliente seleccionado no es válido.',
            'mascotaEditar.id_especie.required' => 'Debe seleccionar una especie.',
            'mascotaEditar.id_especie.integer' => 'El ID de la especie debe ser un número.',
            'mascotaEditar.id_especie.exists' => 'La especie seleccionada no es válida.',
            'mascotaEditar.id_raza.required' => 'Debe seleccionar una raza.',
            'mascotaEditar.id_raza.integer' => 'El ID de la raza debe ser un número.',
            'mascotaEditar.id_raza.exists' => 'La raza seleccionada no es válida.',
            'mascotaEditar.fecha_nacimiento.date' => 'La fecha de nacimiento debe ser una fecha válida.',
            'mascotaEditar.fecha_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser futura.',
            'mascotaEditar.sexo.required' => 'El sexo es obligatorio.',
            'mascotaEditar.sexo.string' => 'El sexo debe ser texto.',
            'mascotaEditar.sexo.in' => 'El sexo debe ser Macho o Hembra.',
            'mascotaEditar.id_color.required' => 'El color es obligatorio.',
            'mascotaEditar.id_color.exists' => 'El color seleccionado no es válido.',
            'mascotaEditar.peso_actual.numeric' => 'El peso debe ser un número.',
            'mascotaEditar.peso_actual.min' => 'El peso no puede ser negativo.',
            'mascotaEditar.observacion.string' => 'La observación debe ser texto.',
            'mascotaEditar.observacion.max' => 'La observación no puede tener más de 500 caracteres.',
        ]);

        $mascota = Mascota::findOrFail($this->mascotaEditar['id_mascota']);

        $mascota->update([
            'id_cliente' => $this->mascotaEditar['id_cliente'],
            'id_raza' => $this->mascotaEditar['id_raza'],
            'nombre_mascota' => $this->mascotaEditar['nombre_mascota'],
            'fecha_nacimiento' => $this->mascotaEditar['fecha_nacimiento'],
            'sexo' => $this->mascotaEditar['sexo'],
            'id_color' => $this->mascotaEditar['id_color'], // CAMBIO
            'peso_actual' => $this->mascotaEditar['peso_actual'],
            'observacion' => $this->mascotaEditar['observacion'],
        ]);

        $this->cerrarModal();
        $this->dispatch('notify', title: 'Success', description: 'Mascota actualizada correctamente.', type: 'success');
        $this->dispatch('mascotaActualizada');
    }

    public function render()
    {
        return view('livewire.mantenimiento.mascotas.mascotas', [
            'razas' => $this->razas,
            'especies' => $this->especies,
            'colores' => $this->colores, // Pasar colores a la vista
            'edad_meses' => $this->edad_meses,
            'edad_humana' => $this->edad_humana,
        ]);
    }
}
