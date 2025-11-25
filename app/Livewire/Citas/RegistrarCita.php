<?php

namespace App\Livewire\Citas;

use App\Models\Cita;
use App\Models\EstadoCita;
use App\Models\Clientes;
use App\Models\Trabajador;
use App\Models\Mascota;
use App\Models\Persona;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class RegistrarCita extends Component
{
    public $clientes = [];
    public $trabajadores = [];
    public $mascotas = [];
    public $estadosCita = [];
    public $cantCitasNoAsistio = 0;

    public $clienteSeleccionado = '';
    public $trabajadorSeleccionado = '';
    public $mascotaSeleccionada = '';
    public $estadoCitaSeleccionado = '';

    public $filtroCliente = '';
    public $filtroMascota = '';

    // Estadísticas
    public $cantCitasPendientes = 0;
    public $cantCitasConfirmadas = 0;
    public $cantCitasCanceladas = 0;
    public $cantCitasCompletadas = 0;

    public $cita = [
        'fecha_programada' => '',
        'motivo' => '',
        'observaciones' => ''
    ];

    public bool $showModal = false;
    public bool $showModalDetalle = false;
    public ?Cita $citaSeleccionada = null;

    public function mount()
    {
        $this->cargarClientes();
        $this->cargarTrabajadores();
        $this->cargarEstadosCita();
        $this->inicializarFormulario();
        $this->calcularEstadisticas();
    }

    public function updatedFiltroCliente()
    {
        $this->cargarClientes();
    }

    public function cargarClientes()
    {
        $query = Clientes::with(['persona']);
        
        if ($this->filtroCliente) {
            $query->whereHas('persona', function($q) {
                $q->where('numero_documento', 'like', '%' . $this->filtroCliente . '%')
                ->orWhere('nombre', 'like', '%' . $this->filtroCliente . '%')
                ->orWhere('apellido_paterno', 'like', '%' . $this->filtroCliente . '%')
                ->orWhere('apellido_materno', 'like', '%' . $this->filtroCliente . '%');
            });
        }
        
        $this->clientes = $query->get();
    }

    public function cargarTrabajadores()
    {
        $this->trabajadores = Trabajador::with(['persona', 'puestoTrabajo'])
            ->whereHas('estadoTrabajador', function($q) {
                $q->where('nombre_estado_trabajador', 'activo');
            })
            ->get();
    }

    public function cargarEstadosCita()
    {
        $this->estadosCita = EstadoCita::all();
    }

    public function cargarMascotas()
    {
        if ($this->clienteSeleccionado) {
            $this->mascotas = Mascota::with(['raza'])
                ->where('id_cliente', $this->clienteSeleccionado)
                ->where('estado', 'activo')
                ->get();
        } else {
            $this->mascotas = [];
        }
    }

    public function updatedClienteSeleccionado($value)
    {
        if ($value) {
            $this->cargarMascotas();
            $this->mascotaSeleccionada = '';
        } else {
            $this->mascotas = [];
            $this->mascotaSeleccionada = '';
        }
    }

    public function seleccionarCliente($idCliente)
    {
        $this->clienteSeleccionado = $idCliente;
        $this->filtroCliente = '';
        $this->cargarMascotas();
    }

    public function limpiarCliente()
    {
        $this->clienteSeleccionado = '';
        $this->filtroCliente = '';
        $this->mascotas = [];
        $this->mascotaSeleccionada = '';
    }

    public function inicializarFormulario()
    {
        $this->cita = [
            'fecha_programada' => now()->format('Y-m-d\TH:i'),
            'motivo' => '',
            'observaciones' => ''
        ];

        // Estado por defecto (pendiente)
        $estadoPendiente = EstadoCita::where('nombre_estado_cita', 'pendiente')->first();
        if ($estadoPendiente) {
            $this->estadoCitaSeleccionado = $estadoPendiente->id_estado_cita;
        }
    }

    public function calcularEstadisticas()
    {
        $estados = EstadoCita::all();
        
        foreach ($estados as $estado) {
            $count = Cita::where('id_estado_cita', $estado->id_estado_cita)->count();
            
            switch ($estado->nombre_estado_cita) {
                case 'Pendiente':
                    $this->cantCitasPendientes = $count;
                    break;
                case 'En progreso':
                    $this->cantCitasConfirmadas = $count; // Cambiar nombre si quieres
                    break;
                case 'Cancelada':
                    $this->cantCitasCanceladas = $count;
                    break;
                case 'Completada':
                    $this->cantCitasCompletadas = $count;
                    break;
                case 'No asistio':
                    $this->cantCitasNoAsistio = $count; // Nueva propiedad
                    break;
            }
        }
    }

    public function actualizarEstadisticas()
    {
        $estados = EstadoCita::all();
        
        foreach ($estados as $estado) {
            $count = Cita::where('id_estado_cita', $estado->id_estado_cita)->count();
            
            switch ($estado->nombre_estado_cita) {
                case 'pendiente':
                    $this->cantCitasPendientes = $count;
                    break;
                case 'confirmada':
                    $this->cantCitasConfirmadas = $count;
                    break;
                case 'cancelada':
                    $this->cantCitasCanceladas = $count;
                    break;
                case 'completada':
                    $this->cantCitasCompletadas = $count;
                    break;
            }
        }
    }

    public function guardar()
    {
        $this->validate([
            'clienteSeleccionado' => 'required|exists:clientes,id_cliente',
            'mascotaSeleccionada' => 'required|exists:mascotas,id_mascota',
            'trabajadorSeleccionado' => 'required|exists:trabajadores,id_trabajador',
            'estadoCitaSeleccionado' => 'required|exists:estado_citas,id_estado_cita',
            'cita.fecha_programada' => 'required|date|after:now',
            'cita.motivo' => 'required|string|max:500',
            'cita.observaciones' => 'nullable|string|max:1000',
        ], [
            'clienteSeleccionado.required' => 'El cliente es obligatorio.',
            'clienteSeleccionado.exists' => 'El cliente seleccionado no es válido.',
            'mascotaSeleccionada.required' => 'La mascota es obligatoria.',
            'mascotaSeleccionada.exists' => 'La mascota seleccionada no es válida.',
            'trabajadorSeleccionado.required' => 'El trabajador asignado es obligatorio.',
            'trabajadorSeleccionado.exists' => 'El trabajador seleccionado no es válido.',
            'estadoCitaSeleccionado.required' => 'El estado de la cita es obligatorio.',
            'estadoCitaSeleccionado.exists' => 'El estado seleccionado no es válido.',
            'cita.fecha_programada.required' => 'La fecha programada es obligatoria.',
            'cita.fecha_programada.date' => 'La fecha programada no es válida.',
            'cita.fecha_programada.after' => 'La fecha programada debe ser futura.',
            'cita.motivo.required' => 'El motivo es obligatorio.',
            'cita.motivo.max' => 'El motivo no debe exceder los 500 caracteres.',
            'cita.observaciones.max' => 'Las observaciones no deben exceder los 1000 caracteres.',
        ]);

        try {
            DB::transaction(function () {
                // Crear la cita
                $cita = Cita::create([
                    'id_cliente' => $this->clienteSeleccionado,
                    'id_mascota' => $this->mascotaSeleccionada,
                    'id_trabajador_asignado' => $this->trabajadorSeleccionado,
                    'id_estado_cita' => $this->estadoCitaSeleccionado,
                    'fecha_programada' => $this->cita['fecha_programada'],
                    'motivo' => $this->cita['motivo'],
                    'observaciones' => $this->cita['observaciones'] ?? null,
                    'fecha_registro' => now(),
                    'fecha_actualizacion' => now(),
                ]);
            });

            // Actualizar estadísticas después de guardar
            $this->actualizarEstadisticas();

            $this->resetForm();
            $this->closeModal();

            $this->dispatch('notify', title: 'Éxito', description: 'Cita registrada correctamente ✅', type: 'success');
            $this->dispatch('citasUpdated');

        } catch (\Exception $e) {
            Log::error('Error al registrar la cita', ['error' => $e->getMessage()]);
            $this->dispatch('notify', title: 'Error', description: 'Error al registrar la cita: ' . $e->getMessage(), type: 'error');
        }
    }

    // Nuevo método para marcar como "En progreso"
    public function enProgresoCita()
    {
        try {
            DB::transaction(function () {
                $estadoEnProgreso = EstadoCita::where('nombre_estado_cita', 'En progreso')->first();

                $cita = $this->citaSeleccionada;
                $cita->id_estado_cita = $estadoEnProgreso->id_estado_cita;
                $cita->fecha_actualizacion = now();
                $cita->save();
            });

            $this->actualizarEstadisticas();
            $this->dispatch('notify', title: 'Éxito', description: 'Cita marcada como En progreso ✅', type: 'success');
            $this->closeModalDetalle();
            $this->dispatch('citasUpdated');

        } catch (\Exception $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error al cambiar estado de la cita: ' . $e->getMessage(), type: 'error');
        }
    }

    public function confirmarCita()
    {
        try {
            DB::transaction(function () {
                $estadoCitaConfirmada = EstadoCita::where('nombre_estado_cita', 'confirmada')->first();

                $cita = $this->citaSeleccionada;
                $cita->id_estado_cita = $estadoCitaConfirmada->id_estado_cita;
                $cita->fecha_actualizacion = now();
                $cita->save();
            });

            $this->actualizarEstadisticas();
            $this->dispatch('notify', title: 'Éxito', description: 'Cita confirmada correctamente ✅', type: 'success');
            $this->closeModalDetalle();
            $this->dispatch('citasUpdated');

        } catch (\Exception $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error al confirmar la cita: ' . $e->getMessage(), type: 'error');
        }
    }

    public function cancelarCita()
    {
        try {
            DB::transaction(function () {
                $estadoCitaCancelada = EstadoCita::where('nombre_estado_cita', 'Cancelada')->first();

                $cita = $this->citaSeleccionada;
                $cita->id_estado_cita = $estadoCitaCancelada->id_estado_cita;
                $cita->fecha_actualizacion = now();
                $cita->save();
            });

            $this->actualizarEstadisticas();
            $this->dispatch('notify', title: 'Éxito', description: 'Cita cancelada correctamente ❌', type: 'success');
            $this->closeModalDetalle();
            $this->dispatch('citasUpdated');

        } catch (\Exception $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error al cancelar la cita: ' . $e->getMessage(), type: 'error');
        }
    }

    public function completarCita()
    {
        try {
            DB::transaction(function () {
                $estadoCitaCompletada = EstadoCita::where('nombre_estado_cita', 'Completada')->first();

                $cita = $this->citaSeleccionada;
                $cita->id_estado_cita = $estadoCitaCompletada->id_estado_cita;
                $cita->fecha_actualizacion = now();
                $cita->save();
            });

            $this->actualizarEstadisticas();
            $this->dispatch('notify', title: 'Éxito', description: 'Cita marcada como Completada ✅', type: 'success');
            $this->closeModalDetalle();
            $this->dispatch('citasUpdated');

        } catch (\Exception $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error al completar la cita: ' . $e->getMessage(), type: 'error');
        }
    }

    // Nuevo método para "No asistió"
    public function noAsistioCita()
    {
        try {
            DB::transaction(function () {
                $estadoNoAsistio = EstadoCita::where('nombre_estado_cita', 'No asistio')->first();

                $cita = $this->citaSeleccionada;
                $cita->id_estado_cita = $estadoNoAsistio->id_estado_cita;
                $cita->fecha_actualizacion = now();
                $cita->save();
            });

            $this->actualizarEstadisticas();
            $this->dispatch('notify', title: 'Éxito', description: 'Cita marcada como No asistió ⚠️', type: 'success');
            $this->closeModalDetalle();
            $this->dispatch('citasUpdated');

        } catch (\Exception $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error al marcar como no asistió: ' . $e->getMessage(), type: 'error');
        }
    }

    public function render()
    {
        $citas = Cita::with([
                'cliente.persona', 
                'trabajadorAsignado.persona', 
                'mascota', 
                'estadoCita'
            ])
            ->orderBy('fecha_programada', 'desc')
            ->orderBy('id_cita', 'desc')
            ->get();

        return view('livewire.citas.registro-citas', [
            'citas' => $citas
        ]);
    }

    public function redirigirAClientes()
    {
        $this->cerrarModalCliente();
        return redirect()->route('mantenimiento.clientes'); // Ajusta la ruta según tu configuración
    }

    public function redirigirAMascotas()
    {
        $this->cerrarModalCliente();
        return redirect()->route('mantenimiento.clientes.mascotas'); // Ajusta la ruta según tu configuración
    }

    // Agregar los nuevos eventos
    #[\Livewire\Attributes\On('en-progreso-cita')]
    public function enProgresoCitaFn(int $rowId): void
    {
        $this->citaSeleccionada = Cita::find($rowId);
        if ($this->citaSeleccionada) {
            $this->enProgresoCita();
        }
    }

    #[\Livewire\Attributes\On('no-asistio-cita')]
    public function noAsistioCitaFn(int $rowId): void
    {
        $this->citaSeleccionada = Cita::find($rowId);
        if ($this->citaSeleccionada) {
            $this->noAsistioCita();
        }
    }

    public function openModal(): void
    {
        $this->showModal = true;
        $this->inicializarFormulario();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function closeModalDetalle(): void
    {
        $this->showModalDetalle = false;
        $this->citaSeleccionada = null;
    }

    public function resetForm()
    {
        $this->inicializarFormulario();
        $this->clienteSeleccionado = '';
        $this->mascotaSeleccionada = '';
        $this->trabajadorSeleccionado = '';
        $this->filtroCliente = '';
        $this->mascotas = [];
        
        // Restablecer estado por defecto
        $estadoPendiente = EstadoCita::where('nombre_estado_cita', 'pendiente')->first();
        if ($estadoPendiente) {
            $this->estadoCitaSeleccionado = $estadoPendiente->id_estado_cita;
        }
    }

    #[\Livewire\Attributes\On('show-modal-cita')]
    public function showModal(int $rowId): void
    {
        $this->citaSeleccionada = Cita::with([
            'cliente.persona',
            'trabajadorAsignado.persona',
            'mascota',
            'estadoCita'
        ])->find($rowId);

        $this->showModalDetalle = true;
    }

    #[\Livewire\Attributes\On('confirmar-cita')]
    public function confirmarCitaFn(int $rowId): void
    {
        $this->citaSeleccionada = Cita::find($rowId);
        if ($this->citaSeleccionada) {
            $this->confirmarCita();
        }
    }

    #[\Livewire\Attributes\On('cancelar-cita')]
    public function cancelarCitaFn(int $rowId): void
    {
        $this->citaSeleccionada = Cita::find($rowId);
        if ($this->citaSeleccionada) {
            $this->cancelarCita();
        }
    }

    #[\Livewire\Attributes\On('completar-cita')]
    public function completarCitaFn(int $rowId): void
    {
        $this->citaSeleccionada = Cita::find($rowId);
        if ($this->citaSeleccionada) {
            $this->completarCita();
        }
    }

    public function buscarClientes()
    {
        $this->cargarClientes();
    }

    public function getClienteSeleccionadoFormateado()
    {
        if (!$this->clienteSeleccionado) {
            return null;
        }

        $cliente = Clientes::with(['persona'])->find($this->clienteSeleccionado);
        
        if (!$cliente || !$cliente->persona) {
            return null;
        }

        return [
            'nombre' => $cliente->persona->nombre,
            'apellido_paterno' => $cliente->persona->apellido_paterno,
            'apellido_materno' => $cliente->persona->apellido_materno,
            'dni' => $cliente->persona->numero_documento,
            'telefono' => $cliente->persona->numero_telefono_personal,
            'correo' => $cliente->persona->correo_electronico_personal,
        ];
    }

    public function cerrarModalCliente()
    {
        // Método para cerrar modal si es necesario
        $this->filtroCliente = '';
    }
}