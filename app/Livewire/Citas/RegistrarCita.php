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
use Carbon\CarbonPeriod;
use Illuminate\Support\Str;

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

     // MODIFICADO: Cambiar a propiedades de estadísticas con arreglos
    public $estadisticas = [
        'Pendiente' => ['total' => 0, 'porcentaje' => 0],
        'En progreso' => ['total' => 0, 'porcentaje' => 0],
        'Cancelada' => ['total' => 0, 'porcentaje' => 0],
        'Completada' => ['total' => 0, 'porcentaje' => 0],
        'No asistio' => ['total' => 0, 'porcentaje' => 0],
    ];

    protected $listeners = [
        'editar-cita-event' => 'cargarCitaParaEditar',
        'cambiar-estado-cita-event' => 'cambiarEstadoCita',
        'citasUpdated' => 'refreshCitas'
    ];

    public $filtroCliente = '';
    public $filtroMascota = '';

    // Nuevas propiedades para disponibilidad
    public $horariosDisponibles = [];
    public $fechaSeleccionada = '';
    public $horaSeleccionada = '';
    public $serviciosSeleccionados = [];
    public $serviciosDisponibles = [];
    public $duracionTotal = 0;

    // Nuevas propiedades para el calendario de citas del veterinario
    public $mostrarCalendario = false;
    public $citasVeterinario = [];
    public $rangoFechaCalendario = '';
    public $fechasDisponibilidad = [];
    public $eventosCalendario = [];

    public $diasNoLaborales = [];

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

    public function refreshCitas()
    {
        $this->calcularEstadisticas();
    }

    public function mount()
    {
        $this->cargarClientes();
        $this->cargarTrabajadores();
        $this->cargarEstadosCita();
        $this->inicializarFormulario();
        $this->cargarServicios();
        $this->calcularEstadisticas(); // Añadir esta línea

        // Fecha por defecto (mañana)
        $this->fechaSeleccionada = now()->format('Y-m-d');

        // Rango por defecto para calendario (semana actual)
        $this->rangoFechaCalendario = now()->format('Y-m-d');
    }

    public function cargarCitaParaEditar($citaId)
    {
        try {
            Log::info('Cargando cita para editar', ['cita_id' => $citaId]);
            
            $this->citaSeleccionada = Cita::with([
                'cliente.persona',
                'mascota',
                'estadoCita',
                'serviciosCita.servicio',
                'trabajadorAsignado.persona'
            ])->findOrFail($citaId);

            // Cargar datos en el formulario
            $this->clienteSeleccionado = $this->citaSeleccionada->id_cliente;
            $this->mascotaSeleccionada = $this->citaSeleccionada->id_mascota;
            $this->trabajadorSeleccionado = $this->citaSeleccionada->id_trabajador_asignado;
            $this->estadoCitaSeleccionado = $this->citaSeleccionada->id_estado_cita;
            
            // Cargar servicios seleccionados
            $this->serviciosSeleccionados = $this->citaSeleccionada->serviciosCita
                ->pluck('id_servicio')
                ->toArray();
            
            // Cargar datos adicionales de la cita
            $this->cita = [
                'fecha_programada' => $this->citaSeleccionada->fecha_programada,
                'motivo' => $this->citaSeleccionada->motivo,
                'observaciones' => $this->citaSeleccionada->observaciones
            ];
            
            // Parsear fecha y hora
            $fechaHora = Carbon::parse($this->citaSeleccionada->fecha_programada);
            $this->fechaSeleccionada = $fechaHora->format('Y-m-d');
            $this->horaSeleccionada = $fechaHora->format('H:i');
            
            // Cargar mascotas del cliente
            $this->cargarMascotas();
            
            // Recalcular duración
            $this->calcularDuracionTotal();

            // Actualizar horarios disponibles
            if ($this->trabajadorSeleccionado && $this->fechaSeleccionada) {
                $this->actualizarHorariosDisponibles();
            }
            
            // Abrir modal
            $this->showModal = true;
            
            $this->dispatch('notify',
                title: 'Cita cargada',
                description: 'Puede editar los datos de la cita.',
                type: 'info'
            );

        } catch (\Exception $e) {
            Log::error('Error al cargar cita para editar', ['error' => $e->getMessage()]);
            $this->dispatch('notify',
                title: 'Error',
                description: 'No se pudo cargar la cita para editar: ' . $e->getMessage(),
                type: 'error'
            );
        }
    }

    public function cambiarEstadoCita($citaId, $nuevoEstado)
    {
        try {
            Log::info('Cambiando estado de cita', [
                'cita_id' => $citaId, 
                'nuevo_estado' => $nuevoEstado
            ]);

            // Buscar el estado por nombre (mantener consistencia con la base de datos)
            $estadoModel = EstadoCita::where('nombre_estado_cita', $nuevoEstado)->first();
            
            if (!$estadoModel) {
                throw new \Exception("Estado no encontrado: {$nuevoEstado}");
            }

            DB::transaction(function () use ($citaId, $estadoModel) {
                $cita = Cita::findOrFail($citaId);
                $cita->update([
                    'id_estado_cita' => $estadoModel->id_estado_cita,
                    'fecha_actualizacion' => now()
                ]);
                
                Log::info('Estado de cita actualizado', [
                    'cita_id' => $citaId,
                    'nuevo_estado_id' => $estadoModel->id_estado_cita
                ]);
            });

            $this->dispatch('notify',
                title: '✅ Estado actualizado',
                description: "La cita ha pasado a estado: {$nuevoEstado}",
                type: 'success'
            );

            // Actualizar estadísticas
            $this->actualizarEstadisticas();
            
            // Disparar evento para refrescar la tabla
            $this->dispatch('citasUpdated');

        } catch (\Exception $e) {
            Log::error('Error al cambiar estado de cita', [
                'error' => $e->getMessage(),
                'cita_id' => $citaId,
                'nuevo_estado' => $nuevoEstado
            ]);
            
            $this->dispatch('notify',
                title: '❌ Error',
                description: 'No se pudo actualizar el estado de la cita: ' . $e->getMessage(),
                type: 'error'
            );
        }
    }

    public function confirmarCambiarEstado($citaId, $estado)
    {
        $this->dispatch('confirmar-cambio-estado', [
            'citaId' => $citaId,
            'estado' => $estado
        ]);
    }

    // Nuevo método para obtener los días no laborales del trabajador
    private function obtenerDiasNoLaborales($trabajadorId)
    {
        if (!$trabajadorId) {
            return [];
        }

        $trabajador = Trabajador::with(['turnos.horarios'])->find($trabajadorId);
        if (!$trabajador) {
            return [];
        }

        $diasNoLaborales = [];

        foreach ($trabajador->turnos as $turno) {
            foreach ($turno->horarios as $horario) {
                if ($horario->es_descanso) {
                    // Mapear día español a inglés para Carbon
                    $diaMap = [
                        'lunes' => 'monday',
                        'martes' => 'tuesday',
                        'miércoles' => 'wednesday',
                        'jueves' => 'thursday',
                        'viernes' => 'friday',
                        'sábado' => 'saturday',
                        'domingo' => 'sunday'
                    ];
                    
                    $diaEspanol = strtolower($horario->dia_semana);
                    if (isset($diaMap[$diaEspanol])) {
                        $diasNoLaborales[] = $diaMap[$diaEspanol];
                    }
                }
            }
        }

        return array_unique($diasNoLaborales);
    }


    private function calcularDisponibilidadPorDia($fechaInicio, $fechaFin)
    {
        $periodo = CarbonPeriod::create($fechaInicio, $fechaFin);
        $this->fechasDisponibilidad = [];

        // Asegurar que sea colección
        $citasColeccion = $this->citasVeterinario instanceof \Illuminate\Support\Collection 
            ? $this->citasVeterinario 
            : collect($this->citasVeterinario);

        foreach ($periodo as $fecha) {
            $fechaStr = $fecha->format('Y-m-d');
            $diaSemanaIngles = strtolower($fecha->englishDayOfWeek);
            
            // Verificar si es día no laboral
            $esDiaNoLaboral = in_array($diaSemanaIngles, $this->diasNoLaborales);
            
            // Obtener citas para este día
            $citasDelDia = $citasColeccion->filter(function ($cita) use ($fechaStr) {
                return Carbon::parse($cita->fecha_programada)->format('Y-m-d') === $fechaStr;
            });

            // Calcular horas ocupadas
            $horasOcupadas = $citasDelDia->sum(function ($cita) {
                if (!$cita->serviciosCita) {
                    return 0;
                }
                
                return $cita->serviciosCita->sum(function ($citaServicio) {
                    return $citaServicio->servicio->duracion_estimada ?? 0;
                });
            });

            // Si es día no laboral, marcarlo como no disponible
            if ($esDiaNoLaboral) {
                $this->fechasDisponibilidad[$fechaStr] = [
                    'fecha' => $fechaStr,
                    'fecha_formateada' => $fecha->translatedFormat('d M'),
                    'dia_semana' => $fecha->translatedFormat('l'),
                    'total_citas' => 0,
                    'horas_ocupadas' => 0,
                    'porcentaje_ocupacion' => 0,
                    'disponibilidad' => 'no_laboral',
                    'es_no_laboral' => true,
                    'puede_seleccionar' => false
                ];
                continue;
            }

            // Para días laborales, calcular disponibilidad
            $jornadaLaboral = 480;
            $porcentajeOcupacion = $horasOcupadas > 0 ? ($horasOcupadas / $jornadaLaboral) * 100 : 0;
            
            // Determinar disponibilidad
            if ($porcentajeOcupacion >= 90) {
                $disponibilidad = 'muy_baja';
                $puedeSeleccionar = false;
            } elseif ($porcentajeOcupacion >= 80) {
                $disponibilidad = 'baja';
                $puedeSeleccionar = true;
            } elseif ($porcentajeOcupacion >= 50) {
                $disponibilidad = 'media';
                $puedeSeleccionar = true;
            } else {
                $disponibilidad = 'alta';
                $puedeSeleccionar = true;
            }
            
            $this->fechasDisponibilidad[$fechaStr] = [
                'fecha' => $fechaStr,
                'fecha_formateada' => $fecha->translatedFormat('d M'),
                'dia_semana' => $fecha->translatedFormat('l'),
                'total_citas' => $citasDelDia->count(),
                'horas_ocupadas' => $horasOcupadas,
                'porcentaje_ocupacion' => min(100, $porcentajeOcupacion),
                'disponibilidad' => $disponibilidad,
                'es_no_laboral' => false,
                'puede_seleccionar' => $puedeSeleccionar,
                'citas' => $citasDelDia->map(function($cita) {
                    return [
                        'hora' => Carbon::parse($cita->fecha_programada)->format('H:i'),
                        'duracion' => $cita->serviciosCita->sum(function ($citaServicio) {
                            return $citaServicio->servicio->duracion_estimada ?? 0;
                        }) ?: 60,
                        'estado' => $cita->estadoCita->nombre_estado_cita
                    ];
                })->toArray()
            ];
        }
    }

    
    public function obtenerHorariosDespuesDeCitas($fecha)
    {
        if (!$this->trabajadorSeleccionado || !$fecha) {
            return [];
        }

        // Convertir a colección si es necesario
        $citasColeccion = $this->citasVeterinario instanceof \Illuminate\Support\Collection 
            ? $this->citasVeterinario 
            : collect($this->citasVeterinario);

        $citasDelDia = $citasColeccion->filter(function ($cita) use ($fecha) {
            return Carbon::parse($cita->fecha_programada)->format('Y-m-d') === $fecha;
        })->sortBy('fecha_programada');

        if ($citasDelDia->isEmpty()) {
            return $this->horariosDisponibles;
        }

        // Obtener la última cita del día
        $ultimaCita = $citasDelDia->last();
        $horaFinUltimaCita = Carbon::parse($ultimaCita->fecha_programada)
            ->addMinutes($ultimaCita->serviciosCita->sum(function ($citaServicio) {
                return $citaServicio->servicio->duracion_estimada ?? 0;
            }) ?: 60);

        // Filtrar horarios disponibles que sean después de la última cita
        return collect($this->horariosDisponibles)->filter(function ($slot) use ($horaFinUltimaCita) {
            $horaSlot = Carbon::parse($slot['fecha_completa']);
            return $horaSlot->greaterThanOrEqualTo($horaFinUltimaCita);
        })->values()->toArray();
    }

    public function seleccionarFechaCalendario($fecha)
    {
        if (!isset($this->fechasDisponibilidad[$fecha])) {
            return;
        }

        $disponibilidad = $this->fechasDisponibilidad[$fecha];
        
        // Validar si se puede seleccionar
        if (!$disponibilidad['puede_seleccionar']) {
            $mensaje = $disponibilidad['es_no_laboral'] 
                ? "Este día no es laboral para el veterinario seleccionado." 
                : "Este día tiene demasiadas citas programadas.";
            
            $this->dispatch('notify',
                title: 'Día no disponible',
                description: $mensaje,
                type: 'warning'
            );
            return;
        }

        $this->fechaSeleccionada = $fecha;
        $this->actualizarHorariosDisponibles();
        $this->mostrarCalendario = false;
    }


    public function toggleCalendario()
    {
        $this->mostrarCalendario = !$this->mostrarCalendario;
        
        if ($this->mostrarCalendario && $this->trabajadorSeleccionado) {
            $this->cargarCitasVeterinario();
        }
    }

    public function cargarCitasVeterinario()
    {
        if (!$this->trabajadorSeleccionado) {
            $this->citasVeterinario = collect();
            $this->eventosCalendario = [];
            $this->diasNoLaborales = [];
            return;
        }

        // Obtener el rango de fechas del MES COMPLETO
        $fechaInicio = Carbon::parse($this->rangoFechaCalendario . '-01')->startOfMonth();
        $fechaFin = $fechaInicio->copy()->endOfMonth();

        // Obtener citas del veterinario en el rango de fechas
        $this->citasVeterinario = Cita::with([
            'cliente.persona',
            'mascota',
            'estadoCita',
            'serviciosCita.servicio'
        ])
        ->where('id_trabajador_asignado', $this->trabajadorSeleccionado)
        ->whereBetween('fecha_programada', [$fechaInicio, $fechaFin])
        ->whereIn('id_estado_cita', function($query) {
            $query->select('id_estado_cita')
                ->from('estado_citas')
                ->whereIn('nombre_estado_cita', ['Pendiente', 'En progreso', 'Completada']);
        })
        ->orderBy('fecha_programada')
        ->get();

        // Obtener días no laborales
        $this->diasNoLaborales = $this->obtenerDiasNoLaborales($this->trabajadorSeleccionado);

        // Preparar eventos para el calendario
        $this->eventosCalendario = $this->citasVeterinario->map(function ($cita) {
            $color = $this->getColorByEstado($cita->estadoCita->nombre_estado_cita);
            
            $duracionTotal = $cita->serviciosCita->sum(function ($citaServicio) {
                return $citaServicio->servicio->duracion_estimada ?? 0;
            }) ?: 60;
            
            $fechaFin = Carbon::parse($cita->fecha_programada)->addMinutes($duracionTotal);
            
            return [
                'id' => $cita->id_cita,
                'title' => $cita->mascota ? $cita->mascota->nombre_mascota : 'Sin mascota',
                'start' => $cita->fecha_programada,
                'end' => $fechaFin->format('Y-m-d H:i:s'),
                'color' => $color,
                'extendedProps' => [
                    'cliente' => $cita->cliente && $cita->cliente->persona 
                        ? $cita->cliente->persona->nombre . ' ' . $cita->cliente->persona->apellido_paterno
                        : 'N/A',
                    'mascota' => $cita->mascota ? $cita->mascota->nombre_mascota : 'Sin mascota',
                    'estado' => $cita->estadoCita->nombre_estado_cita,
                    'motivo' => $cita->motivo,
                    'servicios' => $cita->serviciosCita->map(function ($citaServicio) {
                        return [
                            'nombre' => $citaServicio->servicio->nombre_servicio ?? 'Servicio no disponible',
                            'duracion' => $citaServicio->servicio->duracion_estimada ?? 0,
                            'precio' => $citaServicio->precio_aplicado
                        ];
                    })->toArray()
                ]
            ];
        })->toArray();

        // Calcular disponibilidad por día con validación de días no laborales
        $this->calcularDisponibilidadPorDia($fechaInicio, $fechaFin);
    }


     private function getColorByEstado($estado)
    {
        switch ($estado) {
            case 'Pendiente':
                return '#fbbf24'; // amarillo
            case 'En progreso':
                return '#3b82f6'; // azul
            case 'Completada':
                return '#10b981'; // verde
            case 'Cancelada':
                return '#ef4444'; // rojo
            default:
                return '#6b7280'; // gris
        }
    }

    public function calcularEstadisticas()
    {
        $estados = EstadoCita::all();
        $totalGeneral = Cita::count();
        
        // Reiniciar estadísticas
        $this->estadisticas = [
            'Pendiente' => ['total' => 0, 'porcentaje' => 0],
            'En progreso' => ['total' => 0, 'porcentaje' => 0],
            'Cancelada' => ['total' => 0, 'porcentaje' => 0],
            'Completada' => ['total' => 0, 'porcentaje' => 0],
            'No asistio' => ['total' => 0, 'porcentaje' => 0],
        ];
        
        foreach ($estados as $estado) {
            $count = Cita::where('id_estado_cita', $estado->id_estado_cita)->count();
            $porcentaje = $totalGeneral > 0 ? round(($count / $totalGeneral) * 100, 1) : 0;
            
            // Usar el nombre exacto de la base de datos
            $nombreEstado = $estado->nombre_estado_cita;
            
            if (isset($this->estadisticas[$nombreEstado])) {
                $this->estadisticas[$nombreEstado] = [
                    'total' => $count,
                    'porcentaje' => $porcentaje
                ];
            }
        }
        
        Log::info('Estadísticas calculadas', $this->estadisticas);
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
            
            // Si el calendario está visible, cargar las citas
            if ($this->mostrarCalendario) {
                $this->cargarCitasVeterinario();
            }
        } else {
            $this->horariosDisponibles = [];
            $this->diasNoLaborales = [];
        }
    }


    public function estaOcupadoEnHora($fecha, $hora)
    {
        // Convertir a colección si es necesario
        $citasColeccion = $this->citasVeterinario instanceof \Illuminate\Support\Collection 
            ? $this->citasVeterinario 
            : collect($this->citasVeterinario);

        $citasDelDia = $citasColeccion->filter(function ($cita) use ($fecha) {
            return Carbon::parse($cita->fecha_programada)->format('Y-m-d') === $fecha;
        });

        foreach ($citasDelDia as $cita) {
            $horaCita = Carbon::parse($cita->fecha_programada);
            $duracionCita = $cita->serviciosCita->sum(function ($citaServicio) {
                return $citaServicio->servicio->duracion_estimada ?? 0;
            }) ?: 60;
            $horaFinCita = $horaCita->copy()->addMinutes($duracionCita);
            
            $horaSeleccionada = Carbon::parse($fecha . ' ' . $hora);
            
            if ($horaSeleccionada->between($horaCita, $horaFinCita)) {
                return true;
            }
        }

        return false;
    }


    public function updatedRangoFechaCalendario($value)
    {
        if ($this->trabajadorSeleccionado && $this->mostrarCalendario) {
            $this->cargarCitasVeterinario();
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

    public function getInfoTurnosTrabajadorProperty()
    {
        if (!$this->trabajadorSeleccionado) {
            return null;
        }

        $trabajador = Trabajador::with(['turnos.horarios'])->find($this->trabajadorSeleccionado);

        if (!$trabajador) {
            return null;
        }

        // Cargar citas si el calendario está visible
        if ($this->mostrarCalendario) {
            $this->cargarCitasVeterinario();
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

    /* public function cargarEstadosCita()
    {
        $this->estadosCita = EstadoCita::all();
    } */

    public function cargarEstadosCita()
    {
        $this->estadosCita = EstadoCita::orderBy('id_estado_cita')->get();
        Log::info('Estados de cita cargados', [
            'total' => $this->estadosCita->count(),
            'estados' => $this->estadosCita->pluck('nombre_estado_cita')->toArray()
        ]);
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

        // Estado por defecto SIEMPRE será "Pendiente"
        $estadoPendiente = EstadoCita::where('nombre_estado_cita', 'Pendiente')->first();
        if ($estadoPendiente) {
            $this->estadoCitaSeleccionado = $estadoPendiente->id_estado_cita;
        }
    }

    public function guardar()
    {
            $reglas = [
            'clienteSeleccionado' => 'required|exists:clientes,id_cliente',
            'mascotaSeleccionada' => 'required|exists:mascotas,id_mascota',
            'trabajadorSeleccionado' => 'required|exists:trabajadores,id_trabajador',
            'estadoCitaSeleccionado' => 'required|exists:estado_citas,id_estado_cita',
            'cita.motivo' => 'required|string|max:500',
            'cita.observaciones' => 'nullable|string|max:1000',
            'serviciosSeleccionados' => 'required|array|min:1',
            'serviciosSeleccionados.*' => 'exists:servicios,id_servicio',
        ];

        // Para nuevas citas, la fecha debe ser futura
        if (!$this->citaSeleccionada) {
            $reglas['cita.fecha_programada'] = 'required|date|after:now';
        } else {
            $reglas['cita.fecha_programada'] = 'required|date';
        }

        $this->validate($reglas, [
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
                if ($this->citaSeleccionada) {
                    // ACTUALIZAR CITA EXISTENTE
                    $this->citaSeleccionada->update([
                        'id_cliente' => $this->clienteSeleccionado,
                        'id_mascota' => $this->mascotaSeleccionada,
                        'id_trabajador_asignado' => $this->trabajadorSeleccionado,
                        'id_estado_cita' => $this->estadoCitaSeleccionado,
                        'fecha_programada' => $this->cita['fecha_programada'],
                        'motivo' => $this->cita['motivo'],
                        'observaciones' => $this->cita['observaciones'] ?? null,
                        'fecha_actualizacion' => now(),
                    ]);

                    // Eliminar servicios antiguos
                    CitaServicio::where('id_cita', $this->citaSeleccionada->id_cita)->delete();
                } else {
                    // CREAR NUEVA CITA
                    $this->citaSeleccionada = Cita::create([
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
                }

                // Crear servicios de la cita
                foreach ($this->serviciosSeleccionados as $servicioId) {
                    $servicio = Servicio::find($servicioId);
                    CitaServicio::create([
                        'id_cita' => $this->citaSeleccionada->id_cita,
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

            $mensaje = $this->citaSeleccionada ? 'Cita actualizada correctamente ✅' : 'Cita registrada correctamente ✅';
            
            $this->dispatch('notify', 
                title: 'Éxito', 
                description: $mensaje, 
                type: 'success'
            );

            // Actualizar estadísticas después de guardar
            $this->actualizarEstadisticas();
            
            // Disparar evento para refrescar la tabla
            $this->dispatch('citasUpdated');

        } catch (\Exception $e) {
            Log::error('Error al registrar la cita', ['error' => $e->getMessage()]);
            $this->dispatch('notify', 
                title: 'Error', 
                description: 'Error al guardar la cita: ' . $e->getMessage(), 
                type: 'error'
            );
        }
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
        $this->citaSeleccionada = null; 

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