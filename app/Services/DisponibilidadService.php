<?php
// app/Services/DisponibilidadService.php

namespace App\Services;

use App\Models\Trabajador;
use App\Models\Cita;
use App\Models\Servicio;
use App\Models\CitaServicio;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DisponibilidadService
{
    public function verificarDisponibilidadTrabajador($trabajadorId, $fechaHora, $serviciosIds = [], $citaActualId = null)
    {
        $trabajador = Trabajador::with(['turnos.horarios'])->find($trabajadorId);
        
        if (!$trabajador) {
            return [
                'disponible' => false,
                'mensaje' => 'Trabajador no encontrado'
            ];
        }

        $fechaCita = Carbon::parse($fechaHora);
        $diaSemana = $this->obtenerDiaSemana($fechaCita->dayOfWeek);
        $horaCita = $fechaCita->format('H:i:s');

        // 1. Calcular duración total de servicios
        $duracionTotal = $this->calcularDuracionTotal($serviciosIds);
        
        if ($duracionTotal <= 0) {
            return [
                'disponible' => false,
                'mensaje' => 'No se ha seleccionado ningún servicio válido'
            ];
        }

        // 2. Verificar si el trabajador tiene turno en esa fecha
        $turnoValido = $this->verificarTurnoTrabajador($trabajador, $fechaCita, $diaSemana, $horaCita, $duracionTotal);
        
        if (!$turnoValido['disponible']) {
            return $turnoValido;
        }

        // 3. Verificar si no hay citas existentes en ese horario
        $citaExistente = $this->verificarCitasExistentes($trabajadorId, $fechaCita, $duracionTotal, $citaActualId);
        
        if (!$citaExistente['disponible']) {
            return $citaExistente;
        }

        return [
            'disponible' => true,
            'mensaje' => 'Trabajador disponible',
            'duracion_minutos' => $duracionTotal
        ];
    }

    private function calcularDuracionTotal($serviciosIds)
    {
        if (empty($serviciosIds)) {
            return 30;
        }

        $duracionTotal = Servicio::whereIn('id_servicio', $serviciosIds)
            ->where('estado', 'activo')
            ->sum('duracion_estimada');

        return (int) $duracionTotal ?: 30; // Default 30 minutos si no hay duración
    }

    private function verificarTurnoTrabajador($trabajador, $fechaCita, $diaSemana, $horaCita, $duracionTotal)
    {
        $turnoActivo = false;
        $mensaje = '';
        
        foreach ($trabajador->turnos as $turno) {
            // Verificar si el turno está activo en la fecha de la cita
            if ($this->esTurnoActivo($turno, $fechaCita)) {
                // Verificar horarios del turno
                foreach ($turno->horarios as $horario) {
                    if ($horario->dia_semana === $diaSemana) {
                        if ($horario->es_descanso) {
                            $mensaje = 'El trabajador tiene descanso en este horario';
                            continue;
                        }
                        
                        $horaFinCita = Carbon::parse($horaCita)->addMinutes($duracionTotal)->format('H:i:s');
                        
                        if ($this->estaEnHorarioLaboral($horario, $horaCita, $horaFinCita)) {
                            $turnoActivo = true;
                            break 2;
                        } else {
                            $mensaje = "Horario no laboral. Turno: {$horario->hora_inicio} - {$horario->hora_fin}";
                        }
                    }
                }
            }
        }

        if (!$turnoActivo) {
            return [
                'disponible' => false,
                'mensaje' => $mensaje ?: 'El trabajador no tiene turno asignado para esta fecha y hora'
            ];
        }

        return ['disponible' => true];
    }

    private function estaEnHorarioLaboral($horario, $horaInicioCita, $horaFinCita)
    {
        return $horaInicioCita >= $horario->hora_inicio && $horaFinCita <= $horario->hora_fin;
    }

    private function esTurnoActivo($turno, $fechaCita)
    {
        /* if (!$turno->pivot) {
            return false;
        }*/

        if (!isset($turno->pivot) || !$turno->pivot) {
        return true; // Asumir activo si no hay información de fechas
        }

        // Solo si tienes fechas en el pivot, verificar
        if ($turno->pivot->fecha_inicio) {
            $fechaInicio = Carbon::parse($turno->pivot->fecha_inicio);
            $fechaFin = $turno->pivot->fecha_fin ? Carbon::parse($turno->pivot->fecha_fin) : null;
            
            if ($fechaFin) {
                return $fechaCita->between($fechaInicio, $fechaFin);
            }
            
            return $fechaCita->gte($fechaInicio);
        }

        return true; // Si no hay fechas, asumir activo
    }

    private function verificarCitasExistentes($trabajadorId, $fechaCita, $duracionTotal, $citaActualId = null)
    {
        $finCita = $fechaCita->copy()->addMinutes($duracionTotal);
        
        $query = Cita::where('id_trabajador_asignado', $trabajadorId)
            ->where(function($query) use ($fechaCita, $finCita) {
                $query->whereBetween('fecha_programada', [$fechaCita, $finCita])
                      ->orWhere(function($q) use ($fechaCita, $finCita) {
                          $q->where('fecha_programada', '<', $fechaCita)
                            ->whereRaw("DATE_ADD(fecha_programada, INTERVAL 60 MINUTE) > ?", [$fechaCita]);
                      })
                      ->orWhereBetween('fecha_programada', [$fechaCita->copy()->subMinutes(59), $finCita]);
            })
            ->whereIn('id_estado_cita', function($query) {
                $query->select('id_estado_cita')
                      ->from('estado_citas')
                      ->whereIn('nombre_estado_cita', ['pendiente', 'confirmada', 'en progreso']);
            });

        // Excluir la cita actual si estamos editando
        if ($citaActualId) {
            $query->where('id_cita', '!=', $citaActualId);
        }

        $citaExistente = $query->first();

        if ($citaExistente) {
            $horaExistente = Carbon::parse($citaExistente->fecha_programada)->format('H:i');
            return [
                'disponible' => false,
                'mensaje' => "El trabajador ya tiene una cita programada a las {$horaExistente}"
            ];
        }

        return ['disponible' => true];
    }

    private function obtenerDiaSemana($dayOfWeek)
    {
        $dias = [
            1 => 'lunes',
            2 => 'martes',
            3 => 'miércoles', 
            4 => 'jueves',
            5 => 'viernes',
            6 => 'sábado',
            0 => 'domingo'
        ];
        
        return $dias[$dayOfWeek];
    }

    public function obtenerHorariosDisponibles($trabajadorId, $fecha, $serviciosIds = [])
    {
        $fechaConsulta = Carbon::parse($fecha);
        $trabajador = Trabajador::with(['turnos.horarios'])->find($trabajadorId);
        
         if (!$trabajador) {
        logger("Trabajador no encontrado: " . $trabajadorId);
        return [];
        }

        // DEBUG: Verificar día de la semana
        logger("=== DEBUG DISPONIBILIDAD ===");
        logger("Fecha: " . $fechaConsulta->format('Y-m-d'));
        logger("Carbon dayOfWeek: " . $fechaConsulta->dayOfWeek);
        logger("Día calculado: " . $this->obtenerDiaSemana($fechaConsulta->dayOfWeek));

        logger("Trabajador: " . $trabajador->id_trabajador);
        logger("Turnos count: " . $trabajador->turnos->count());
        
        foreach ($trabajador->turnos as $turno) {
            logger("Turno: " . $turno->id_turno . " - " . $turno->nombre_turno);
            logger("Horarios count: " . $turno->horarios->count());
            
            foreach ($turno->horarios as $horario) {
                logger("Horario: " . $horario->dia_semana . " - " . $horario->hora_inicio . " a " . $horario->hora_fin . " - Descanso: " . $horario->es_descanso);
            }
        }

        $duracionTotal = $this->calcularDuracionTotal($serviciosIds);
        $horariosDisponibles = [];
        $diaSemana = $this->obtenerDiaSemana($fechaConsulta->dayOfWeek);

        logger("Fecha consulta: " . $fechaConsulta->format('Y-m-d'));
        logger("Día de la semana: " . $diaSemana);
        logger("Duración total: " . $duracionTotal . " minutos");

        foreach ($trabajador->turnos as $turno) {
            if ($this->esTurnoActivo($turno, $fechaConsulta)) {
                logger("Turno activo: " . $turno->id_turno);
                foreach ($turno->horarios as $horario) {
                    logger("Verificando horario: " . $horario->dia_semana . " vs " . $diaSemana . " - Descanso: " . $horario->es_descanso);
                    if ($horario->dia_semana === $diaSemana && !$horario->es_descanso) {
                    logger("✅ Horario válido encontrado!");
                    $slots = $this->generarSlotsDisponibles($horario, $fechaConsulta, $trabajadorId, $duracionTotal);
                    logger("Slots generados: " . count($slots));
                    $horariosDisponibles = array_merge($horariosDisponibles, $slots);
                }
                }
            }
        }

        logger("Horarios disponibles count: " . count($horariosDisponibles));
        
        // Ordenar por hora
        usort($horariosDisponibles, function($a, $b) {
            return $a['inicio']->gt($b['inicio']);
        });

        return $horariosDisponibles;
    }

    private function generarSlotsDisponibles($horario, $fecha, $trabajadorId, $duracionTotal)
    {
        $slots = [];
        $horaInicio = Carbon::parse($horario->hora_inicio);
        $horaFin = Carbon::parse($horario->hora_fin);
        
        $slotActual = $fecha->copy()->setTime($horaInicio->hour, $horaInicio->minute);
        
        while ($slotActual->lt($fecha->copy()->setTime($horaFin->hour, $horaFin->minute))) {
            $slotFin = $slotActual->copy()->addMinutes($duracionTotal);
            
            if ($slotFin->gt($fecha->copy()->setTime($horaFin->hour, $horaFin->minute))) {
                break;
            }

            // Verificar si no hay citas en este slot
            $citaExistente = Cita::where('id_trabajador_asignado', $trabajadorId)
                ->whereBetween('fecha_programada', [$slotActual, $slotFin])
                ->whereIn('id_estado_cita', function($query) {
                    $query->select('id_estado_cita')
                          ->from('estado_citas')
                          ->whereIn('nombre_estado_cita', ['pendiente', 'confirmada', 'en progreso']);
                })
                ->exists();
            
            if (!$citaExistente && $slotActual->gt(now())) {
                $slots[] = [
                    'inicio' => $slotActual->copy(),
                    'fin' => $slotFin,
                    'formato' => $slotActual->format('H:i'),
                    'fecha_completa' => $slotActual->format('Y-m-d H:i:s'),
                    'duracion_minutos' => $duracionTotal
                ];
            }
            
            $slotActual->addMinutes(30); // Slots cada 30 minutos
        }
        
        return $slots;
    }

    // Método para obtener trabajadores disponibles en una fecha/hora específica
    public function obtenerTrabajadoresDisponibles($fechaHora, $serviciosIds = [])
    {
        $fechaCita = Carbon::parse($fechaHora);
        $diaSemana = $this->obtenerDiaSemana($fechaCita->dayOfWeek);
        $horaCita = $fechaCita->format('H:i:s');
        $duracionTotal = $this->calcularDuracionTotal($serviciosIds);

        $trabajadoresDisponibles = Trabajador::with(['turnos.horarios', 'persona', 'puestoTrabajo'])
            ->whereHas('estadoTrabajador', function($q) {
                $q->where('nombre_estado_trabajador', 'activo');
            })
            ->get()
            ->filter(function($trabajador) use ($fechaCita, $diaSemana, $horaCita, $duracionTotal) {
                foreach ($trabajador->turnos as $turno) {
                    if ($this->esTurnoActivo($turno, $fechaCita)) {
                        foreach ($turno->horarios as $horario) {
                            if ($horario->dia_semana === $diaSemana && 
                                !$horario->es_descanso) {
                                
                                $horaFinCita = Carbon::parse($horaCita)->addMinutes($duracionTotal)->format('H:i:s');
                                
                                if ($this->estaEnHorarioLaboral($horario, $horaCita, $horaFinCita)) {
                                    // Verificar si no tiene citas en ese horario
                                    $citaExistente = Cita::where('id_trabajador_asignado', $trabajador->id_trabajador)
                                        ->whereBetween('fecha_programada', [
                                            $fechaCita->copy()->subMinutes(59),
                                            $fechaCita->copy()->addMinutes($duracionTotal)
                                        ])
                                        ->whereIn('id_estado_cita', function($query) {
                                            $query->select('id_estado_cita')
                                                  ->from('estado_citas')
                                                  ->whereIn('nombre_estado_cita', ['pendiente', 'confirmada', 'en progreso']);
                                        })
                                        ->exists();
                                    
                                    return !$citaExistente;
                                }
                            }
                        }
                    }
                }
                return false;
            })
            ->values();

        return $trabajadoresDisponibles;
    }
}