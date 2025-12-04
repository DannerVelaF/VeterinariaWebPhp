<?php

namespace App\Livewire\Historial;

use App\Models\Cita;
use App\Models\CitaServicio;
use App\Models\Servicio;
use App\Models\Clientes;
use App\Models\Mascota;
use App\Models\Trabajador;
use App\Models\EstadoCita;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class RegistrarHistorial extends Component
{
    public $citas = [];
    public $citaSeleccionada = null;
    public $serviciosCita = [];
    public $servicioSeleccionado = null;
    public $citaServicioData = [];
    
    // Filtros para búsqueda
    public $filtroDni = '';
    public $filtroMascota = '';
    public $filtroFecha = '';
    public $filtroEstado = '';
    
    // Datos del formulario
    public $historial = [
        'diagnostico' => '',
        'medicamentos' => '',
        'recomendaciones' => '',
        'notas_adicionales' => ''
    ];
    
    // Estados disponibles
    public $estadosCita = [];
    
    public function mount()
    {
        $this->cargarEstadosCita();
        $this->cargarCitasPendientes();
    }
    
    public function cargarEstadosCita()
    {
        $this->estadosCita = EstadoCita::all();
    }
    
    public function cargarCitasPendientes()
    {
        $query = Cita::with([
            'cliente.persona',
            'mascota',
            'trabajadorAsignado.persona',
            'estadoCita',
            'serviciosCita.servicio'
        ])
        ->whereIn('id_estado_cita', function($q) {
            $q->select('id_estado_cita')
              ->from('estado_citas')
              ->whereIn('nombre_estado_cita', ['En progreso', 'Completada']);
        });
        
        // Aplicar filtros
        if ($this->filtroDni) {
            $query->whereHas('cliente.persona', function($q) {
                $q->where('numero_documento', 'like', '%' . $this->filtroDni . '%');
            });
        }
        
        if ($this->filtroMascota) {
            $query->whereHas('mascota', function($q) {
                $q->where('nombre_mascota', 'like', '%' . $this->filtroMascota . '%');
            });
        }
        
        if ($this->filtroFecha) {
            $query->whereDate('fecha_programada', $this->filtroFecha);
        }
        
        if ($this->filtroEstado) {
            $query->where('id_estado_cita', $this->filtroEstado);
        }
        
        $this->citas = $query->orderBy('fecha_programada', 'desc')->get();
    }
    
    public function updatedFiltroDni()
    {
        $this->cargarCitasPendientes();
    }
    
    public function updatedFiltroMascota()
    {
        $this->cargarCitasPendientes();
    }
    
    public function updatedFiltroFecha()
    {
        $this->cargarCitasPendientes();
    }
    
    public function updatedFiltroEstado()
    {
        $this->cargarCitasPendientes();
    }
    
    public function seleccionarCita($idCita)
    {
        $this->citaSeleccionada = Cita::with([
            'cliente.persona',
            'mascota',
            'trabajadorAsignado.persona',
            'serviciosCita.servicio'
        ])->find($idCita);
        
        $this->serviciosCita = $this->citaSeleccionada->serviciosCita ?? [];
        $this->servicioSeleccionado = null;
        $this->citaServicioData = [];
        $this->resetearFormulario();
    }
    
    public function seleccionarServicio($idCitaServicio)
    {
        $this->servicioSeleccionado = CitaServicio::with('servicio')->find($idCitaServicio);
        
        if ($this->servicioSeleccionado) {
            $this->historial = [
                'diagnostico' => $this->servicioSeleccionado->diagnostico ?? '',
                'medicamentos' => $this->servicioSeleccionado->medicamentos ?? '',
                'recomendaciones' => $this->servicioSeleccionado->recomendaciones ?? '',
                'notas_adicionales' => $this->servicioSeleccionado->notas_adicionales ?? ''
            ];
        } else {
            $this->resetearFormulario();
        }
    }
    
    public function resetearFormulario()
    {
        $this->historial = [
            'diagnostico' => '',
            'medicamentos' => '',
            'recomendaciones' => '',
            'notas_adicionales' => ''
        ];
    }
    
    public function guardarHistorial()
    {
        $this->validate([
            'servicioSeleccionado' => 'required|exists:cita_servicios,id_cita_servicio',
            'historial.diagnostico' => 'required|string|min:10|max:2000',
            'historial.medicamentos' => 'nullable|string|max:1000',
            'historial.recomendaciones' => 'required|string|min:10|max:1000',
            //'historial.notas_adicionales' => 'nullable|string|max:500'
        ], [
            'servicioSeleccionado.required' => 'Debe seleccionar un servicio de la cita.',
            'historial.diagnostico.required' => 'El diagnóstico es obligatorio.',
            'historial.diagnostico.min' => 'El diagnóstico debe tener al menos 10 caracteres.',
            'historial.recomendaciones.required' => 'Las recomendaciones son obligatorias.',
            'historial.recomendaciones.min' => 'Las recomendaciones deben tener al menos 10 caracteres.'
        ]);
        
        try {
            DB::transaction(function () {
                $this->servicioSeleccionado->update([
                    'diagnostico' => $this->historial['diagnostico'],
                    'medicamentos' => $this->historial['medicamentos'],
                    'recomendaciones' => $this->historial['recomendaciones'],
                    //'notas_adicionales' => $this->historial['notas_adicionales'],
                    'fecha_actualizacion' => now()
                ]);
                
                // Si todos los servicios de la cita tienen historial, marcar cita como completada
                $cita = $this->citaSeleccionada;
                $serviciosSinHistorial = $cita->serviciosCita()
                    ->whereNull('diagnostico')
                    ->count();
                
                if ($serviciosSinHistorial === 0) {
                    $estadoCompletado = EstadoCita::where('nombre_estado_cita', 'Completada')->first();
                    if ($estadoCompletado) {
                        $cita->update([
                            'id_estado_cita' => $estadoCompletado->id_estado_cita,
                            'fecha_actualizacion' => now()
                        ]);
                    }
                }
            });
            
            // Recargar datos
            $this->seleccionarCita($this->citaSeleccionada->id_cita);
            
            $this->dispatch('notify', [
                'title' => '¡Éxito!',
                'description' => 'Historial clínico registrado correctamente.',
                'type' => 'success'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al guardar historial clínico', ['error' => $e->getMessage()]);
            
            $this->dispatch('notify', [
                'title' => 'Error',
                'description' => 'Error al guardar el historial clínico: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
    
    public function limpiarFiltros()
    {
        $this->filtroDni = '';
        $this->filtroMascota = '';
        $this->filtroFecha = '';
        $this->filtroEstado = '';
        $this->cargarCitasPendientes();
    }
    
    public function limpiarSeleccion()
    {
        $this->citaSeleccionada = null;
        $this->serviciosCita = [];
        $this->servicioSeleccionado = null;
        $this->resetearFormulario();
    }
    
    public function render()
    {
        return view('livewire.historial.registrar-historial');
    }
}