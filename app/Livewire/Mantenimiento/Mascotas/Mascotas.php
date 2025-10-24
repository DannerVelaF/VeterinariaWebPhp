<?php

namespace App\Livewire\Mantenimiento\Mascotas;

use App\Models\Mascota;
use App\Models\Raza;
use App\Models\Clientes;
//use App\Models\Especie;
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
        //'id_especie' => '',
        'id_raza' => '',
        'nombre_mascota' => '',
        'fecha_nacimiento' => '',
        'sexo' => '',
        'color_primario' => '',
        'peso_actual' => '',
        'observacion' => '',
    ];

    public $mascotaEditar = [
        'id_mascota' => null,
        'id_cliente' => '',
        //'id_especie' => '',
        'id_raza' => '',
        'nombre_mascota' => '',
        'fecha_nacimiento' => '',
        'sexo' => '',
        'color_primario' => '',
        'peso_actual' => '',
        'observacion' => '',
    ];


    public $buscarCliente = '';
    public $resultadosClientes = [];
    public $clienteSeleccionado = null;
    public $razas = [];
    //public $especies = [];
    public $mascotaSeleccionada = null;
    public $modalEditar = false;
    public $edad_meses = null;
    public $edad_humana = null;

    public function mount()
    {
        $this->razas = Raza::where('estado', 'activo')->get();
        // $this->especies = Especie::where('estado', 'activo')->get();
    }

    public function refreshData()
    {
        $this->razas = Raza::where('estado', 'activo')->get();
    }

    /** 🔍 Búsqueda automática de clientes */
    public function updatedBuscarCliente($valor)
    {
        if (strlen($valor) >= 2) {
            $this->resultadosClientes = Clientes::join('personas', 'clientes.id_persona', '=', 'personas.id_persona')
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
        } else {
            $this->resultadosClientes = [];
        }
    }
    /* public function updatedBuscarCliente($valor)
    {
        if (strlen($valor) >= 2) {
            $this->resultadosClientes = Clientes::join('personas', 'clientes.id_persona', '=', 'personas.id_persona')
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
        } else {
            $this->resultadosClientes = [];
        }
    } */

    /** 🧭 Seleccionar cliente */
    public function seleccionarCliente($idCliente)
    {
        $this->clienteSeleccionado = Clientes::join('personas', 'clientes.id_persona', '=', 'personas.id_persona')
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
            ->first();

        $this->mascota['id_cliente'] = $this->clienteSeleccionado->id_cliente;
        $this->buscarCliente = '';
        $this->resultadosClientes = [];
    }

    public function limpiarCliente()
    {
        $this->clienteSeleccionado = null;
        $this->mascota['id_cliente'] = '';
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
            'mascota.id_raza' => 'required|exists:razas,id_raza',
            'mascota.nombre_mascota' => 'required|string|max:150',
            'mascota.nombre_mascota' => 'required|string|max:150|regex:/^[\pL\s\-]+$/u',
            'mascota.fecha_nacimiento' => 'nullable|date',
            'mascota.sexo' => 'nullable|in:Macho,Hembra',
            'mascota.color_primario' => 'nullable|string|max:100',
            'mascota.peso_actual' => 'nullable|numeric|min:0',
            'mascota.observacion' => 'nullable|string|max:500',
        ], [
            'mascota.id_cliente.required' => 'Debe seleccionar un cliente.',
            'mascota.id_raza.required' => 'Debe seleccionar una raza.',
            'mascota.nombre_mascota.required' => 'El nombre de la mascota es obligatorio.',
        ]);

        try {
            DB::transaction(function () use ($validatedData) {
                Mascota::create([
                    'id_cliente' => $validatedData['mascota']['id_cliente'],
                    'id_raza' => $validatedData['mascota']['id_raza'],
                    'nombre_mascota' => $validatedData['mascota']['nombre_mascota'],
                    'fecha_nacimiento' => $validatedData['mascota']['fecha_nacimiento'] ?? null,
                    'sexo' => $validatedData['mascota']['sexo'] ?? null,
                    'color_primario' => $validatedData['mascota']['color_primario'] ?? null,
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
            //'id_especie' => '',
            'id_raza' => '',
            'nombre_mascota' => '',
            'fecha_nacimiento' => '',
            'sexo' => '',
            'color_primario' => '',
            'peso_actual' => '',
            'observacion' => '',
        ];

        if ($limpiarCliente) {
            $this->clienteSeleccionado = null;
        }

        $this->edad_meses = null;
        $this->edad_humana = null;
    }

    #[\Livewire\Attributes\On('abrirModalMascota')]
    public function abrirModalMascota($mascotaId)
    {
        $this->mascotaSeleccionada = Mascota::findOrFail($mascotaId);
        $this->mascotaEditar = [
            'id_mascota' => $mascotaId,
            'id_cliente' => $this->mascotaSeleccionada->id_cliente,
            //'id_especie' => $this->mascotaSeleccionada->id_especie,
            'id_raza' => $this->mascotaSeleccionada->id_raza,
            'nombre_mascota' => $this->mascotaSeleccionada->nombre_mascota,
            'fecha_nacimiento' => $this->mascotaSeleccionada->fecha_nacimiento,
            'sexo' => $this->mascotaSeleccionada->sexo,
            'color_primario' => $this->mascotaSeleccionada->color_primario,
            'peso_actual' => $this->mascotaSeleccionada->peso_actual,
            'observacion' => $this->mascotaSeleccionada->observacion,
        ];

        $this->modalEditar = true;
    }
    public function cerrarModal()
    {
        $this->reset(['modalEditar', 'mascotaEditar', 'mascotaSeleccionada']);
    }

    public function guardarEdicion()
    {
        $this->validate([
            'mascotaEditar.nombre_mascota' => 'required|string|max:100',
            'mascotaEditar.id_cliente' => 'required|integer|exists:clientes,id_cliente',
            'mascotaEditar.id_raza' => 'required|integer|exists:razas,id_raza',
            'mascotaEditar.fecha_nacimiento' => 'nullable|date',
            'mascotaEditar.sexo' => 'required|string',
        ]);

        $mascota = Mascota::findOrFail($this->mascotaEditar['id_mascota']);

        $mascota->update([
            'id_cliente' => $this->mascotaEditar['id_cliente'],
            'id_raza' => $this->mascotaEditar['id_raza'],
            'nombre_mascota' => $this->mascotaEditar['nombre_mascota'],
            'fecha_nacimiento' => $this->mascotaEditar['fecha_nacimiento'],
            'sexo' => $this->mascotaEditar['sexo'],
            'color_primario' => $this->mascotaEditar['color_primario'],
            'peso_actual' => $this->mascotaEditar['peso_actual'],
            'observacion' => $this->mascotaEditar['observacion'],
        ]);

        session()->flash('success', 'Mascota actualizada correctamente.');

        // Cerrar el modal y notificar a la tabla que se actualice
        $this->dispatch('mascotaActualizada');
        $this->cerrarModal();
    }


    public function render()
    {
        return view('livewire.mantenimiento.mascotas.mascotas', [
            'razas' => $this->razas,
            //'especies' => $this->especies,
            'edad_meses' => $this->edad_meses,
            'edad_humana' => $this->edad_humana,
        ]);
    }
}
