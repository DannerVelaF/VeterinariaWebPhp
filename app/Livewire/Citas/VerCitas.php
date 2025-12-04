<?php

namespace App\Livewire\Citas;

use App\Models\Cita;
use App\Models\EstadoCita;
use App\Models\Trabajador;
use App\Models\Cliente;
use App\Models\Mascota;
use App\Models\Servicio;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VerCitas extends Component
{
    use WithPagination;
    
    // Propiedades para filtros
    public $filtroDni = '';
    public $filtroCliente = '';
    public $filtroMascota = '';
    public $filtroTrabajador = '';
    public $filtroEstado = '';
    public $filtroFechaDesde = '';
    public $filtroFechaHasta = '';
    public $filtroServicio = '';
    
    // Propiedad para el modal de detalle
    public $showModalDetalle = false;
    public $citaSeleccionada = null;
    
    // Ordenamiento
    public $sortField = 'fecha_programada';
    public $sortDirection = 'desc';
    
    // Para estadísticas
    public $estadisticas = [];
    
    protected $queryString = [
        'filtroDni' => ['except' => ''],
        'filtroCliente' => ['except' => ''],
        'filtroMascota' => ['except' => ''],
        'filtroTrabajador' => ['except' => ''],
        'filtroEstado' => ['except' => ''],
        'filtroFechaDesde' => ['except' => ''],
        'filtroFechaHasta' => ['except' => ''],
        'filtroServicio' => ['except' => ''],
    ];
    
    public function mount()
    {
        // Inicializar fechas por defecto (último mes)
        $this->filtroFechaDesde = now()->subMonth()->format('Y-m-d');
        $this->filtroFechaHasta = now()->format('Y-m-d');
        
        // Calcular estadísticas iniciales
        $this->calcularEstadisticas();
    }
    
    public function calcularEstadisticas()
    {
        $query = Cita::query();
        
        // Aplicar filtros a las estadísticas
        $this->aplicarFiltrosEstadisticas($query);
        
        $total = $query->count();
        
        // Obtener todos los estados
        $estados = EstadoCita::all();
        $estadisticas = [];
        
        foreach ($estados as $estado) {
            $queryEstado = Cita::where('id_estado_cita', $estado->id_estado_cita);
            $this->aplicarFiltrosEstadisticas($queryEstado);
            
            $count = $queryEstado->count();
            $porcentaje = $total > 0 ? round(($count / $total) * 100, 1) : 0;
            
            $estadisticas[$estado->nombre_estado_cita] = [
                'total' => $count,
                'porcentaje' => $porcentaje,
                'color' => $this->getColorEstado($estado->nombre_estado_cita),
                'icono' => $this->getIconoEstado($estado->nombre_estado_cita)
            ];
        }
        
        $this->estadisticas = $estadisticas;
    }
    
    private function aplicarFiltrosEstadisticas($query)
    {
        if ($this->filtroFechaDesde) {
            $query->whereDate('fecha_programada', '>=', $this->filtroFechaDesde);
        }
        
        if ($this->filtroFechaHasta) {
            $query->whereDate('fecha_programada', '<=', $this->filtroFechaHasta);
        }
        
        if ($this->filtroEstado) {
            $query->where('id_estado_cita', $this->filtroEstado);
        }
        
        if ($this->filtroCliente) {
            $query->whereHas('cliente.persona', function($q) {
                $q->where('nombre', 'like', '%' . $this->filtroCliente . '%')
                  ->orWhere('apellido_paterno', 'like', '%' . $this->filtroCliente . '%')
                  ->orWhere('apellido_materno', 'like', '%' . $this->filtroCliente . '%');
            });
        }
        
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
        
        if ($this->filtroTrabajador) {
            $query->where('id_trabajador_asignado', $this->filtroTrabajador);
        }
    }
    
    public function getEstadosProperty()
    {
        return EstadoCita::all();
    }
    
    public function getTrabajadoresProperty()
    {
        return Trabajador::with('persona')->get();
    }
    
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        
        $this->sortField = $field;
    }
    
    public function limpiarFiltros()
    {
        $this->reset([
            'filtroDni',
            'filtroCliente',
            'filtroMascota',
            'filtroTrabajador',
            'filtroEstado',
            'filtroFechaDesde',
            'filtroFechaHasta',
            'filtroServicio'
        ]);
        
        $this->filtroFechaDesde = now()->subMonth()->format('Y-m-d');
        $this->filtroFechaHasta = now()->format('Y-m-d');
        
        $this->calcularEstadisticas();
        $this->resetPage();
    }
    
    public function verDetalle($id)
    {
        $this->citaSeleccionada = Cita::with([
            'cliente.persona',
            'mascota.raza',
            'trabajadorAsignado.persona',
            'estadoCita',
            'serviciosCita.servicio'
        ])->find($id);
        
        $this->showModalDetalle = true;
    }
    
    public function closeModalDetalle()
    {
        $this->showModalDetalle = false;
        $this->citaSeleccionada = null;
    }
    
    public function cambiarEstado($id, $nuevoEstado)
    {
        try {
            $estado = EstadoCita::where('nombre_estado_cita', $nuevoEstado)->first();
            
            if ($estado) {
                $cita = Cita::find($id);
                $cita->update([
                    'id_estado_cita' => $estado->id_estado_cita,
                    'fecha_actualizacion' => now()
                ]);
                
                $this->calcularEstadisticas();
                
                $this->dispatch('notify', 
                    title: 'Éxito', 
                    description: 'Estado de cita actualizado correctamente', 
                    type: 'success'
                );
            }
        } catch (\Exception $e) {
            $this->dispatch('notify',
                title: 'Error',
                description: 'Error al cambiar estado: ' . $e->getMessage(),
                type: 'error'
            );
        }
    }
    
    public function cancelarCita($id)
    {
        $this->cambiarEstado($id, 'Cancelada');
    }
    
    public function completarCita($id)
    {
        $this->cambiarEstado($id, 'Completada');
    }
    
    public function enProgresoCita($id)
    {
        $this->cambiarEstado($id, 'En progreso');
    }
    
    public function noAsistioCita($id)
    {
        $this->cambiarEstado($id, 'No asistio');
    }
    
    private function getColorEstado($estado)
    {
        $colores = [
            'Pendiente' => 'bg-yellow-100 text-yellow-800',
            'En progreso' => 'bg-blue-100 text-blue-800',
            'Completada' => 'bg-green-100 text-green-800',
            'Cancelada' => 'bg-red-100 text-red-800',
            'No asistio' => 'bg-gray-100 text-gray-800',
            'Confirmada' => 'bg-indigo-100 text-indigo-800',
        ];
        return $colores[$estado] ?? 'bg-gray-100 text-gray-800';
    }
    
    private function getIconoEstado($estado)
    {
        $iconos = [
            'Pendiente' => 'clock',
            'En progreso' => 'play',
            'Completada' => 'check-circle',
            'Cancelada' => 'x-circle',
            'No asistio' => 'user-x',
            'Confirmada' => 'check',
        ];
        return $iconos[$estado] ?? 'circle';
    }
    
    public function render()
    {
        // Construir la consulta
        $query = Cita::with([
            'cliente.persona',
            'mascota',
            'trabajadorAsignado.persona',
            'estadoCita',
            'serviciosCita.servicio'
        ]);
        
        // Aplicar filtros
        if ($this->filtroFechaDesde) {
            $query->whereDate('fecha_programada', '>=', $this->filtroFechaDesde);
        }
        
        if ($this->filtroFechaHasta) {
            $query->whereDate('fecha_programada', '<=', $this->filtroFechaHasta);
        }
        
        if ($this->filtroEstado) {
            $query->where('id_estado_cita', $this->filtroEstado);
        }
        
        if ($this->filtroDni) {
            $query->whereHas('cliente.persona', function($q) {
                $q->where('numero_documento', 'like', '%' . $this->filtroDni . '%');
            });
        }
        
        if ($this->filtroCliente) {
            $query->whereHas('cliente.persona', function($q) {
                $q->where('nombre', 'like', '%' . $this->filtroCliente . '%')
                  ->orWhere('apellido_paterno', 'like', '%' . $this->filtroCliente . '%')
                  ->orWhere('apellido_materno', 'like', '%' . $this->filtroCliente . '%');
            });
        }
        
        if ($this->filtroMascota) {
            $query->whereHas('mascota', function($q) {
                $q->where('nombre_mascota', 'like', '%' . $this->filtroMascota . '%');
            });
        }
        
        if ($this->filtroTrabajador) {
            $query->where('id_trabajador_asignado', $this->filtroTrabajador);
        }
        
        if ($this->filtroServicio) {
            $query->whereHas('serviciosCita.servicio', function($q) {
                $q->where('nombre_servicio', 'like', '%' . $this->filtroServicio . '%');
            });
        }
        
        // Aplicar ordenamiento
        if ($this->sortField === 'cliente_nombre') {
            $query->join('clientes', 'citas.id_cliente', '=', 'clientes.id_cliente')
                  ->join('personas', 'clientes.id_persona', '=', 'personas.id_persona')
                  ->orderBy('personas.nombre', $this->sortDirection)
                  ->select('citas.*');
        } elseif ($this->sortField === 'trabajador_nombre') {
            $query->join('trabajadores', 'citas.id_trabajador_asignado', '=', 'trabajadores.id_trabajador')
                  ->join('personas', 'trabajadores.id_persona', '=', 'personas.id_persona')
                  ->orderBy('personas.nombre', $this->sortDirection)
                  ->select('citas.*');
        } else {
            $query->orderBy($this->sortField, $this->sortDirection);
        }
        
        $citas = $query->paginate(15);
        
        return view('livewire.citas.ver-citas', [
            'citas' => $citas
        ]);
    }
}