<?php

namespace App\Livewire\Mantenimiento\Trabajadores;

use App\Models\Trabajador;
use App\Models\TrabajadorTurno;
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
    public $horarioTurnoAsignar = [];

    public $horarios = [];
    public $edit_horarios = [];
    public $modalVisible = false;
    public $idTurnoEditando = null;

    public $turnoSeleccionadoAsignar = null;
    public $trabajadoresSinTurno = [];
    public $trabajadoresConTurno = [];

    public $selectedSinTurno = [];
    public $selectedConTurno = [];

    public function mount()
    {
        $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];

        foreach ($dias as $dia) {
            $this->horarios[$dia] = ['inicio' => null, 'fin' => null, 'descanso' => false];
            $this->edit_horarios[$dia] = ['inicio' => null, 'fin' => null, 'descanso' => false];
        }

        // 🔹 Cargar turnos activos
        $this->turnos = ModelsTurnos::where('estado', 'activo')
            ->orderByDesc('id_turno')
            ->get();

        // 🔹 Cargar trabajadores sin turno desde el inicio
        $trabajadoresConTurnoIds = TrabajadorTurno::pluck('id_trabajador')->toArray();

        $this->trabajadoresSinTurno = Trabajador::with('persona')
            ->whereNotIn('id_trabajador', $trabajadoresConTurnoIds)
            ->get();

        // 🔹 Vacío hasta seleccionar turno
        $this->trabajadoresConTurno = collect();
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
            $this->modalVisible = false;
        } catch (Exception $e) {
            Log::error('Error al guardar turno', ['error' => $e->getMessage()]);
            $this->dispatch('notify', title: 'Error', description: 'Ocurrió un error al guardar el turno.', type: 'error');
            $this->dispatch('close-modal', name: 'nuevo-turno');
        }
    }

    public function abrirModal()
    {
        $this->modalVisible = true;
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


    public function updatedTurnoSeleccionadoAsignar($idTurno)
    {
        $this->selectedSinTurno = [];
        $this->selectedConTurno = [];

        if (!$idTurno) {
            $trabajadoresConTurnoIds = TrabajadorTurno::pluck('id_trabajador')->toArray();
            $this->trabajadoresSinTurno = Trabajador::with('persona')
                ->whereNotIn('id_trabajador', $trabajadoresConTurnoIds)
                ->get();

            $this->trabajadoresConTurno = collect();
            $this->horarioTurnoAsignar = [];
            return;
        }

        $this->cargarListas($idTurno);
        $this->cargarHorarioTurno($idTurno);
    }

    public function cargarHorarioTurno($idTurno)
    {
        $this->horarioTurnoAsignar = TurnoHorario::where('id_turno', $idTurno)
            ->orderByRaw("FIELD(dia_semana, 'Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo')")
            ->get()
            ->mapWithKeys(function ($item) {
                $clave = ucfirst(strtolower($item->dia_semana));
                return [$clave => $item];
            });
    }


    private function cargarListas($idTurno)
    {
        // 🔹 IDs de trabajadores con el turno seleccionado
        $trabajadoresConTurnoIds = TrabajadorTurno::where('id_turno', $idTurno)
            ->pluck('id_trabajador')
            ->toArray();

        // 🔹 Todos los trabajadores que tienen algún turno asignado
        $trabajadoresConCualquierTurnoIds = TrabajadorTurno::pluck('id_trabajador')->toArray();

        // 🔹 Trabajadores con el turno actual
        $this->trabajadoresConTurno = Trabajador::with('persona')
            ->whereIn('id_trabajador', $trabajadoresConTurnoIds)
            ->get();

        // 🔹 Trabajadores sin turno asignado (ningún turno)
        $this->trabajadoresSinTurno = Trabajador::with('persona')
            ->whereNotIn('id_trabajador', $trabajadoresConCualquierTurnoIds)
            ->get();
    }


    public function asignarTrabajadores()
    {
        try {
            DB::transaction(function () {
                foreach ($this->selectedSinTurno as $idTrabajador) {
                    TrabajadorTurno::updateOrCreate(
                        ['id_trabajador' => $idTrabajador],
                        [
                            'id_turno' => $this->turnoSeleccionadoAsignar,
                            'fecha_inicio' => now(),
                            'fecha_fin' => null,
                            'fecha_registro' => now(),
                            'fecha_actualizacion' => now(),
                        ]
                    );
                }
            });

            $this->selectedSinTurno = [];
            $this->cargarListas($this->turnoSeleccionadoAsignar);

            $this->dispatch('notify', title: 'Asignación completada', description: 'Los turnos fueron asignados correctamente.', type: 'success');
        } catch (Exception $e) {
            Log::error('Error al asignar turnos', ['error' => $e->getMessage()]);
            $this->dispatch('notify', title: 'Error', description: 'Ocurrió un error al asignar los turnos.', type: 'error');
        }
    }

    public function quitarTrabajadores()
    {
        if (!$this->turnoSeleccionadoAsignar || empty($this->selectedConTurno)) return;

        DB::transaction(function () {
            TrabajadorTurno::whereIn('id_trabajador', $this->selectedConTurno)->delete();
        });

        $this->selectedConTurno = [];
        $this->cargarListas($this->turnoSeleccionadoAsignar);

        $this->dispatch('notify', title: 'Remoción completada', description: 'Trabajadores quitados del turno.', type: 'success');
    }

    public function render()
    {
        return view('livewire.mantenimiento.trabajadores.turnos');
    }
}
