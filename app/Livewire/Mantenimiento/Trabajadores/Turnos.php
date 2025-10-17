<?php

namespace App\Livewire\Mantenimiento\Trabajadores;

use App\Models\TurnoHorario;
use App\Models\Turnos as ModelsTurnos;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Turnos extends Component
{
    public $nombre_turno, $descripcion;
    public $edit_nombre_turno, $edit_descripcion;
    public $turnos = [];
    public $turnoSeleccionado = null;
    public $horarioTurno = [];

    public $horarios = [];
    public $edit_horarios = [];

    public $idTurnoEditando = null;

    public function mount()
    {
        $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];

        foreach ($dias as $dia) {
            $this->horarios[$dia] = ['inicio' => null, 'fin' => null, 'descanso' => false];
            $this->edit_horarios[$dia] = ['inicio' => null, 'fin' => null, 'descanso' => false];
        }

        $this->turnos = ModelsTurnos::where('estado', 'activo')->orderByDesc('id_turno')->get();
    }

    public function rowSelected($idTurno)
    {
        if ($this->turnoSeleccionado && $this->turnoSeleccionado->id_turno == $idTurno) {
            $this->turnoSeleccionado = null;
            $this->horarioTurno = [];
            return;
        }

        $this->turnoSeleccionado = ModelsTurnos::find($idTurno);
        $this->horarioTurno = TurnoHorario::where('id_turno', $idTurno)
            ->orderByRaw("FIELD(dia_semana, 'Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo')")
            ->get();
    }

    public function guardarTurno()
    {
        try {
            $this->validate([
                'nombre_turno' => 'required|string|max:100',
                'descripcion' => 'nullable|string|max:255',
            ]);

            DB::transaction(function () {
                $turno = ModelsTurnos::create([
                    'nombre_turno' => $this->nombre_turno,
                    'descripcion' => $this->descripcion,
                    'estado' => 'activo',
                    'fecha_registro' => now(),
                    'fecha_actualizacion' => now(),
                ]);

                foreach ($this->horarios as $dia => $data) {
                    TurnoHorario::create([
                        'id_turno' => $turno->id_turno,
                        'dia_semana' => $dia,
                        'hora_inicio' => $data['inicio'],
                        'hora_fin' => $data['fin'],
                        'es_descanso' => $data['descanso'] ?? false,
                        'fecha_registro' => now(),
                        'fecha_actualizacion' => now(),
                    ]);
                }
            });

            $this->reset(['nombre_turno', 'descripcion']);
            $this->dispatch('close-modal', name: 'nuevo-turno');

            $this->mount();

            $this->dispatch('notify', title: 'Turno creado', description: 'El turno fue registrado correctamente.', type: 'success');
        } catch (Exception $e) {
            Log::error('Error al guardar turno', ['error' => $e->getMessage()]);
            $this->dispatch('notify', title: 'Error', description: 'Ocurrió un error al guardar el turno.', type: 'error');
            $this->dispatch('close-modal', name: 'nuevo-turno');
        }
    }



    public function deleteTurno($idTurno)
    {
        DB::transaction(function () use ($idTurno) {
            ModelsTurnos::where('id_turno', $idTurno)
                ->update(['estado' => 'inactivo', 'fecha_actualizacion' => now()]);
        });

        $this->dispatch('notify', title: 'Turno eliminado', description: 'El turno fue eliminado correctamente.', type: 'success');
        $this->mount();
    }

    public function render()
    {
        return view('livewire.mantenimiento.trabajadores.turnos');
    }
}
