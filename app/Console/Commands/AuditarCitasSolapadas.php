<?php
// app/Console/Commands/AuditarCitasSolapadas.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DisponibilidadService;
use App\Models\Cita;
use App\Models\CitaServicio;

class AuditarCitasSolapadas extends Command
{
    protected $signature = 'citas:auditar-solapadas';
    protected $description = 'Audita citas que se solapan en horarios';

    public function handle()
    {
        $disponibilidadService = new DisponibilidadService();
        $citas = Cita::with(['trabajadorAsignado.persona', 'citaServicios.servicio'])
            ->whereIn('id_estado_cita', [1, 2]) // pendiente y confirmada
            ->where('fecha_programada', '>', now())
            ->get();

        $this->info("Auditando {$citas->count()} citas...");

        $problemas = [];
        
        foreach ($citas as $cita) {
            $serviciosIds = $cita->citaServicios->pluck('id_servicio')->toArray();
            
            $disponible = $disponibilidadService->verificarDisponibilidadTrabajador(
                $cita->id_trabajador_asignado,
                $cita->fecha_programada,
                $serviciosIds,
                $cita->id_cita // Excluir la cita actual
            );

            if (!$disponible['disponible']) {
                $problemas[] = [
                    'cita_id' => $cita->id_cita,
                    'trabajador' => $cita->trabajadorAsignado->persona->nombre ?? 'N/A',
                    'fecha' => $cita->fecha_programada,
                    'problema' => $disponible['mensaje']
                ];
            }
        }

        if (count($problemas) > 0) {
            $this->error("Se encontraron " . count($problemas) . " problemas de solapamiento:");
            
            $this->table(
                ['Cita ID', 'Trabajador', 'Fecha', 'Problema'],
                $problemas
            );
        } else {
            $this->info("✅ Todas las citas tienen horarios válidos");
        }
    }
}