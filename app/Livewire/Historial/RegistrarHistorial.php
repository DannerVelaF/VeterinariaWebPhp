<?php

namespace App\Livewire\Historial;

use App\Models\Cita;
use App\Models\CitaServicio;
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
    ];
    
    // Estados disponibles
    public $estadosCita = ['En progreso', 'Completada'];
    
    // Mensaje de estado
    public $mensaje = '';
    public $tipoMensaje = '';
    
    public function mount()
    {
        $this->cargarEstadosCita();
        $this->cargarCitas();
    }
    
    public function cargarEstadosCita()
    {
        $this->estadosCita = EstadoCita::all();
    }
    
    public function cargarCitas()
    {
        try {
            $query = Cita::with([
                'cliente.persona',
                'mascota',
                'trabajadorAsignado.persona',
                'estadoCita',
                'serviciosCita.servicio'
            ]);
            
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
            } else {
                // Por defecto, mostrar citas "En progreso" o "Completadas"
                $query->whereIn('id_estado_cita', function($q) {
                    $q->select('id_estado_cita')
                      ->from('estado_citas')
                      ->whereIn('nombre_estado_cita', ['En progreso', 'Completada']);
                });
            }
            
            $this->citas = $query->orderBy('fecha_programada', 'desc')->get();
            
        } catch (\Exception $e) {
            Log::error('Error al cargar citas para historial', ['error' => $e->getMessage()]);
            $this->mostrarMensaje('Error al cargar citas: ' . $e->getMessage(), 'error');
        }
    }
    
    public function updatedFiltroDni()
    {
        $this->cargarCitas();
    }
    
    public function updatedFiltroMascota()
    {
        $this->cargarCitas();
    }
    
    public function updatedFiltroFecha()
    {
        $this->cargarCitas();
    }
    
    public function updatedFiltroEstado()
    {
        $this->cargarCitas();
    }
    
    public function seleccionarCita($idCita)
    {
        try {
            $this->citaSeleccionada = Cita::with([
                'cliente.persona',
                'mascota.raza',
                'trabajadorAsignado.persona',
                'serviciosCita.servicio'
            ])->find($idCita);
            
            if ($this->citaSeleccionada) {
                $this->serviciosCita = $this->citaSeleccionada->serviciosCita ?? [];
                $this->servicioSeleccionado = null;
                $this->resetearFormulario();
                
                // Auto-seleccionar el primer servicio sin historial
                foreach ($this->serviciosCita as $servicioCita) {
                    if (empty($servicioCita->diagnostico)) {
                        $this->seleccionarServicio($servicioCita->id_cita_servicio);
                        break;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error al seleccionar cita', ['error' => $e->getMessage()]);
            $this->mostrarMensaje('Error al seleccionar cita: ' . $e->getMessage(), 'error');
        }
    }
    
    public function seleccionarServicio($idCitaServicio)
    {
        try {
            $this->servicioSeleccionado = CitaServicio::with('servicio')->find($idCitaServicio);
            
            if ($this->servicioSeleccionado) {
                $this->historial = [
                    'diagnostico' => $this->servicioSeleccionado->diagnostico ?? '',
                    'medicamentos' => $this->servicioSeleccionado->medicamentos ?? '',
                    'recomendaciones' => $this->servicioSeleccionado->recomendaciones ?? '',
                ];
            } else {
                $this->resetearFormulario();
            }
        } catch (\Exception $e) {
            Log::error('Error al seleccionar servicio', ['error' => $e->getMessage()]);
            $this->mostrarMensaje('Error al seleccionar servicio: ' . $e->getMessage(), 'error');
        }
    }
    
    public function resetearFormulario()
    {
        $this->historial = [
            'diagnostico' => '',
            'medicamentos' => '',
            'recomendaciones' => '',
        ];
        $this->mensaje = '';
        $this->tipoMensaje = '';
    }
    
    public function mostrarMensaje($mensaje, $tipo = 'info')
    {
        $this->mensaje = $mensaje;
        $this->tipoMensaje = $tipo;
        
        // Auto-ocultar mensaje después de 5 segundos
        $this->dispatch('mensaje-temporal');
    }
    
    public function guardarHistorial()
    {
        // Validar que se haya seleccionado un servicio
        if (!$this->servicioSeleccionado) {
            $this->mostrarMensaje('Debe seleccionar un servicio para guardar el historial.', 'error');
            return;
        }
        
        // Validar campos obligatorios
        if (empty(trim($this->historial['diagnostico']))) {
            $this->mostrarMensaje('El diagnóstico es obligatorio.', 'error');
            return;
        }
        
        if (empty(trim($this->historial['recomendaciones']))) {
            $this->mostrarMensaje('Las recomendaciones son obligatorias.', 'error');
            return;
        }
        
        try {
            DB::beginTransaction();
            
            // Guardar el historial en la tabla cita_servicios
            $this->servicioSeleccionado->update([
                'diagnostico' => $this->historial['diagnostico'],
                'medicamentos' => $this->historial['medicamentos'],
                'recomendaciones' => $this->historial['recomendaciones'],
                'fecha_actualizacion' => now()
            ]);
            
            // Verificar si todos los servicios tienen historial
            $todosConHistorial = true;
            foreach ($this->serviciosCita as $servicioCita) {
                if (empty($servicioCita->diagnostico) && $servicioCita->id_cita_servicio != $this->servicioSeleccionado->id_cita_servicio) {
                    $todosConHistorial = false;
                    break;
                }
            }
            
            // Si todos los servicios tienen historial, marcar la cita como completada
            if ($todosConHistorial) {
                $estadoCompletado = EstadoCita::where('nombre_estado_cita', 'Completada')->first();
                if ($estadoCompletado) {
                    $this->citaSeleccionada->update([
                        'id_estado_cita' => $estadoCompletado->id_estado_cita,
                        'fecha_actualizacion' => now()
                    ]);
                }
            }
            
            DB::commit();
            
            // Recargar la cita para mostrar los cambios
            $this->seleccionarCita($this->citaSeleccionada->id_cita);
            
            $this->mostrarMensaje('¡Historial clínico guardado exitosamente!', 'success');
            
            // Despachar evento para notificación
            $this->dispatch('notify', 
                title: '¡Éxito!',
                description: 'Historial clínico registrado correctamente.',
                type: 'success'
            );
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al guardar historial clínico', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->mostrarMensaje('Error al guardar el historial: ' . $e->getMessage(), 'error');
            
            $this->dispatch('notify',
                title: 'Error',
                description: 'Error al guardar el historial clínico.',
                type: 'error'
            );
        }
    }
    
    public function limpiarFiltros()
    {
        $this->filtroDni = '';
        $this->filtroMascota = '';
        $this->filtroFecha = '';
        $this->filtroEstado = '';
        $this->cargarCitas();
    }
    
    public function limpiarSeleccion()
    {
        $this->citaSeleccionada = null;
        $this->serviciosCita = [];
        $this->servicioSeleccionado = null;
        $this->resetearFormulario();
    }
    
    // Calcular progreso del historial
    public function getProgresoHistorialProperty()
    {
        if (!$this->citaSeleccionada || $this->serviciosCita->isEmpty()) {
            return 0;
        }
        
        $totalServicios = $this->serviciosCita->count();
        $serviciosConHistorial = 0;
        
        foreach ($this->serviciosCita as $servicio) {
            if (!empty($servicio->diagnostico)) {
                $serviciosConHistorial++;
            }
        }
        
        return ($serviciosConHistorial / $totalServicios) * 100;
    }
    
    // Verificar si todos los servicios tienen historial
    public function getTodosConHistorialProperty()
    {
        if (!$this->citaSeleccionada || $this->serviciosCita->isEmpty()) {
            return false;
        }
        
        foreach ($this->serviciosCita as $servicio) {
            if (empty($servicio->diagnostico)) {
                return false;
            }
        }
        
        return true;
    }
    
    public function render()
    {
        return view('livewire.historial.registrar-historial');
    }
}