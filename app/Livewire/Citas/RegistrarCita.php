<?php

namespace App\Livewire\Citas;

use App\Services\DisponibilidadService;
use App\Models\Servicio;
use App\Models\Cita;
use App\Models\EstadoCita;
use App\Models\Clientes;
use App\Models\PuestoTrabajador;
use App\Models\Trabajador;
use App\Models\Mascota;
use App\Models\CitaServicio;
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

    public $clienteSeleccionado = '';
    public $trabajadorSeleccionado = '';
    public $mascotaSeleccionada = '';
    public $estadoCitaSeleccionado = '';

    // Agregar estas propiedades para estadísticas
    public $cantCitasNoAsistio = 0;
    public $cantCitasPendientes = 0;
    public $cantCitasConfirmadas = 0;
    public $cantCitasCanceladas = 0;
    public $cantCitasCompletadas = 0;


    public $filtroCliente = '';
    public $filtroMascota = '';

    // Nuevas propiedades para disponibilidad
    public $horariosDisponibles = [];
    public $fechaSeleccionada = '';
    public $horaSeleccionada = '';
    public $serviciosSeleccionados = [];
    public $serviciosDisponibles = [];
    public $duracionTotal = 0;

    protected $disponibilidadService;

    public function boot()
    {
        $this->disponibilidadService = new DisponibilidadService();
    }

    public $cita = [
        'fecha_programada' => '',
        'motivo' => '',
        'observaciones' => ''
    ];

    public bool $showModal = false;
    public ?Cita $citaSeleccionada = null;

    public function mount()
    {
        $this->cargarClientes();
        $this->cargarTrabajadores();
        $this->cargarEstadosCita();
        $this->inicializarFormulario();
        $this->cargarServicios();
        $this->calcularEstadisticas(); // Añadir esta línea

        // Fecha por defecto (mañana)
        $this->fechaSeleccionada = now()->addDay()->format('Y-m-d');
    }

    // Agregar este método para calcular estadísticas
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
                    $this->cantCitasConfirmadas = $count;
                    break;
                case 'Cancelada':
                    $this->cantCitasCanceladas = $count;
                    break;
                case 'Completada':
                    $this->cantCitasCompletadas = $count;
                    break;
                case 'No asistio':
                    $this->cantCitasNoAsistio = $count;
                    break;
            }
        }
    }

    public function actualizarEstadisticas()
    {
        $this->calcularEstadisticas();
    }

    public function cargarServicios()
    {
        $this->serviciosDisponibles = Servicio::where('estado', 'activo')
            ->orderBy('nombre_servicio')
            ->get();
    }

    public function updatedServiciosSeleccionados()
    {
        $this->calcularDuracionTotal();
        $this->actualizarHorariosDisponibles();
    }

    public function updatedTrabajadorSeleccionado($value)
    {
        if ($value && $this->fechaSeleccionada) {
            $this->actualizarHorariosDisponibles();
        } else {
            $this->horariosDisponibles = [];
        }
    }

    public function updatedFechaSeleccionada($value)
    {
        if ($value && $this->trabajadorSeleccionado) {
            $this->actualizarHorariosDisponibles();
        } else {
            $this->horariosDisponibles = [];
        }
    }

    private function calcularDuracionTotal()
    {
        $this->duracionTotal = (int) Servicio::whereIn('id_servicio', $this->serviciosSeleccionados)
            ->where('estado', 'activo')
            ->sum('duracion_estimada') ?: 60;
    }

    private function actualizarHorariosDisponibles()
    {
        if ($this->trabajadorSeleccionado && $this->fechaSeleccionada) {
            $this->horariosDisponibles = $this->disponibilidadService
                ->obtenerHorariosDisponibles(
                    $this->trabajadorSeleccionado,
                    $this->fechaSeleccionada,
                    $this->serviciosSeleccionados
                );
        } else {
            $this->horariosDisponibles = [];
        }

        $this->horaSeleccionada = '';
        $this->cita['fecha_programada'] = '';
    }

    public function seleccionarHora($hora, $fechaCompleta)
    {
        $this->horaSeleccionada = $hora;
        $this->cita['fecha_programada'] = $fechaCompleta;
    }

    public function updatedFiltroCliente()
    {
        $this->cargarClientes();
    }

    public function cargarClientes()
    {
        $query = Clientes::with(['persona']);

        if ($this->filtroCliente) {
            $query->whereHas('persona', function ($q) {
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
        $puestoVeterinario = PuestoTrabajador::where("estado", 'activo')->first();

        if (!$puestoVeterinario) {
            $this->trabajadores = collect();
            return;
        }

        $this->trabajadores = Trabajador::with(['persona', 'puestoTrabajo'])
            ->where('id_puesto_trabajo', $puestoVeterinario->id_puesto_trabajo)
            ->whereHas('estadoTrabajador', function ($q) {
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
            'fecha_programada' => '',
            'motivo' => '',
            'observaciones' => ''
        ];

        // Estado por defecto SIEMPRE será "pendiente"
        $estadoPendiente = EstadoCita::where('nombre_estado_cita', 'pendiente')->first();
        if ($estadoPendiente) {
            $this->estadoCitaSeleccionado = $estadoPendiente->id_estado_cita;
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
            'serviciosSeleccionados' => 'required|array|min:1',
            'serviciosSeleccionados.*' => 'exists:servicios,id_servicio',
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
            'serviciosSeleccionados.required' => 'Debe seleccionar al menos un servicio.',
            'serviciosSeleccionados.min' => 'Debe seleccionar al menos un servicio.',
        ]);

        // Validar Disponibilidad antes de guardar
        $disponibilidad = $this->disponibilidadService->verificarDisponibilidadTrabajador(
            $this->trabajadorSeleccionado,
            $this->cita['fecha_programada'],
            $this->serviciosSeleccionados
        );
        
        if (!$disponibilidad['disponible']) {
            $this->dispatch('notify',
                title: 'No disponible',
                description: $disponibilidad['mensaje'],
                type: 'error'
            );
            return;
        }

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

                // Crear servicios de la cita
                foreach ($this->serviciosSeleccionados as $servicioId) {
                    $servicio = Servicio::find($servicioId);
                    CitaServicio::create([
                        'id_cita' => $cita->id_cita,
                        'id_servicio' => $servicioId,
                        'precio_aplicado' => $servicio->precio_unitario,
                        'cantidad' => 1,
                        'fecha_registro' => now(),
                        'fecha_actualizacion' => now(),
                    ]);
                }
            });

            $this->resetForm();
            $this->closeModal();

            $this->dispatch('notify', 
                title: 'Éxito', 
                description: 'Cita registrada correctamente ✅', 
                type: 'success'
            );

            // Actualizar estadísticas después de guardar
            $this->actualizarEstadisticas();
            
            // Redirigir a la vista de citas
            return redirect()->route('citas.ver');

        } catch (\Exception $e) {
            Log::error('Error al registrar la cita', ['error' => $e->getMessage()]);
            $this->dispatch('notify', 
                title: 'Error', 
                description: 'Error al registrar la cita: ' . $e->getMessage(), 
                type: 'error'
            );
        }
    }

    public function getInfoTurnosTrabajadorProperty()
    {
        if (!$this->trabajadorSeleccionado) {
            return null;
        }

        $trabajador = Trabajador::with(['turnos.horarios'])->find($this->trabajadorSeleccionado);

        if (!$trabajador) {
            return null;
        }

        $info = [];

        $diasSemana = [
            'lunes' => ['nombre' => 'Lunes', 'trabaja' => false, 'horarios' => [], 'descanso' => true],
            'martes' => ['nombre' => 'Martes', 'trabaja' => false, 'horarios' => [], 'descanso' => true],
            'miércoles' => ['nombre' => 'Miércoles', 'trabaja' => false, 'horarios' => [], 'descanso' => true],
            'jueves' => ['nombre' => 'Jueves', 'trabaja' => false, 'horarios' => [], 'descanso' => true],
            'viernes' => ['nombre' => 'Viernes', 'trabaja' => false, 'horarios' => [], 'descanso' => true],
            'sábado' => ['nombre' => 'Sábado', 'trabaja' => false, 'horarios' => [], 'descanso' => true],
            'domingo' => ['nombre' => 'Domingo', 'trabaja' => false, 'horarios' => [], 'descanso' => true],
        ];

        foreach ($trabajador->turnos as $turno) {
            foreach ($turno->horarios as $horario) {
                $dia = strtolower($horario->dia_semana);

                if (isset($diasSemana[$dia])) {
                    if ($horario->es_descanso) {
                        $diasSemana[$dia]['descanso'] = true;
                        $diasSemana[$dia]['trabaja'] = false;
                    } else {
                        $diasSemana[$dia]['trabaja'] = true;
                        $diasSemana[$dia]['descanso'] = false;
                        $diasSemana[$dia]['horarios'][] = [
                            'inicio' => \Carbon\Carbon::parse($horario->hora_inicio)->format('H:i'),
                            'fin' => \Carbon\Carbon::parse($horario->hora_fin)->format('H:i'),
                            'turno' => $turno->nombre_turno
                        ];
                    }
                }
            }
        }

        $diasOrdenados = [
            'lunes' => $diasSemana['lunes'],
            'martes' => $diasSemana['martes'],
            'miércoles' => $diasSemana['miércoles'],
            'jueves' => $diasSemana['jueves'],
            'viernes' => $diasSemana['viernes'],
            'sábado' => $diasSemana['sábado'],
            'domingo' => $diasSemana['domingo'],
        ];

        return [
            'nombre_trabajador' => $trabajador->persona ?
                $trabajador->persona->nombre . ' ' . $trabajador->persona->apellido_paterno :
                'Trabajador #' . $trabajador->id_trabajador,
            'puesto' => $trabajador->puestoTrabajo?->nombre_puesto ?? 'Sin puesto asignado',
            'dias_semana' => $diasOrdenados
        ];
    }

    public function getDuracionTotalFormateadaProperty()
    {
        $horas = floor($this->duracionTotal / 60);
        $minutos = $this->duracionTotal % 60;

        if ($horas > 0) {
            return "{$horas}h {$minutos}min";
        }

        return "{$minutos} min";
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

    public function resetForm()
    {
        $this->inicializarFormulario();
        $this->clienteSeleccionado = '';
        $this->mascotaSeleccionada = '';
        $this->trabajadorSeleccionado = '';
        $this->filtroCliente = '';
        $this->mascotas = [];
        $this->serviciosSeleccionados = [];
        $this->horariosDisponibles = [];
        $this->fechaSeleccionada = now()->addDay()->format('Y-m-d');
        $this->horaSeleccionada = '';
        $this->duracionTotal = 0;

        $estadoPendiente = EstadoCita::where('nombre_estado_cita', 'pendiente')->first();
        if ($estadoPendiente) {
            $this->estadoCitaSeleccionado = $estadoPendiente->id_estado_cita;
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

    public function redirigirAClientes()
    {
        return redirect()->route('mantenimiento.clientes');
    }

    public function redirigirAMascotas()
    {
        return redirect()->route('mantenimiento.clientes.mascotas');
    }

    public function render()
    {
        return view('livewire.citas.registrar-cita');
    }
}