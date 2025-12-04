<?php

namespace App\Livewire\Historial;

use App\Models\CitaServicio;
use App\Models\EstadoCita;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Contracts\View\View;

class VerHistorial extends Component
{
    use WithPagination;
    
    // Propiedades para filtros
    public $filtroDni = '';
    public $filtroCliente = '';
    public $filtroMascota = '';
    public $filtroServicio = '';
    public $filtroFechaDesde = '';
    public $filtroFechaHasta = '';
    public $filtroEstado = '';
    
    // Propiedad para el modal de detalle
    public $showModalDetalle = false;
    public $historialSeleccionado = null;
    
    // Ordenamiento
    public $sortField = 'fecha_registro';
    public $sortDirection = 'desc';
    
    protected $queryString = [
        'filtroDni' => ['except' => ''],
        'filtroCliente' => ['except' => ''],
        'filtroMascota' => ['except' => ''],
        'filtroServicio' => ['except' => ''],
        'filtroFechaDesde' => ['except' => ''],
        'filtroFechaHasta' => ['except' => ''],
        'filtroEstado' => ['except' => ''],
    ];
    
    public function mount()
    {
        // Inicializar fechas por defecto (último mes)
        $this->filtroFechaDesde = now()->subMonth()->format('Y-m-d');
        $this->filtroFechaHasta = now()->format('Y-m-d');
    }
    
    public function getEstadosProperty()
    {
        return EstadoCita::all();
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
            'filtroServicio',
            'filtroFechaDesde',
            'filtroFechaHasta',
            'filtroEstado'
        ]);
        
        $this->filtroFechaDesde = now()->subMonth()->format('Y-m-d');
        $this->filtroFechaHasta = now()->format('Y-m-d');
        
        $this->resetPage();
    }
    
    public function verDetalle($id)
    {
        $this->historialSeleccionado = CitaServicio::with([
            'cita.cliente.persona',
            'cita.mascota',
            'cita.trabajadorAsignado.persona',
            'cita.estadoCita',
            'servicio'
        ])->find($id);
        
        $this->showModalDetalle = true;
    }
    
    public function closeModalDetalle()
    {
        $this->showModalDetalle = false;
        $this->historialSeleccionado = null;
    }
    
    public function generarReceta($id)
    {
        $historial = CitaServicio::with([
            'cita.cliente.persona',
            'cita.mascota',
            'servicio'
        ])->find($id);
        
        if ($historial && $historial->medicamentos) {
            // Aquí puedes implementar la generación de PDF
            $this->dispatch('notify', 
                title: 'Receta Generada',
                description: 'La receta se ha generado correctamente para ' . $historial->servicio->nombre_servicio,
                type: 'success'
            );
        } else {
            $this->dispatch('notify',
                title: 'Información',
                description: 'No hay medicamentos registrados para generar receta.',
                type: 'info'
            );
        }
    }
    
    public function render(): View
    {
        // Construir la consulta
        $query = CitaServicio::with([
            'cita.cliente.persona',
            'cita.mascota',
            'cita.trabajadorAsignado.persona',
            'cita.estadoCita',
            'servicio'
        ])
        ->whereNotNull('diagnostico') // Solo historiales registrados
        ->whereHas('cita', function($q) {
            // Filtros relacionados con la cita
            if ($this->filtroEstado) {
                $q->where('id_estado_cita', $this->filtroEstado);
            }
            
            if ($this->filtroFechaDesde) {
                $q->whereDate('fecha_programada', '>=', $this->filtroFechaDesde);
            }
            
            if ($this->filtroFechaHasta) {
                $q->whereDate('fecha_programada', '<=', $this->filtroFechaHasta);
            }
            
            // Filtro por DNI del cliente
            if ($this->filtroDni) {
                $q->whereHas('cliente.persona', function($q2) {
                    $q2->where('numero_documento', 'like', '%' . $this->filtroDni . '%');
                });
            }
            
            // Filtro por nombre del cliente
            if ($this->filtroCliente) {
                $q->whereHas('cliente.persona', function($q2) {
                    $q2->where('nombre', 'like', '%' . $this->filtroCliente . '%')
                       ->orWhere('apellido_paterno', 'like', '%' . $this->filtroCliente . '%')
                       ->orWhere('apellido_materno', 'like', '%' . $this->filtroCliente . '%');
                });
            }
            
            // Filtro por mascota
            if ($this->filtroMascota) {
                $q->whereHas('mascota', function($q2) {
                    $q2->where('nombre_mascota', 'like', '%' . $this->filtroMascota . '%');
                });
            }
        })
        ->whereHas('servicio', function($q) {
            // Filtro por servicio
            if ($this->filtroServicio) {
                $q->where('nombre_servicio', 'like', '%' . $this->filtroServicio . '%');
            }
        });
        
        // Aplicar ordenamiento
        if ($this->sortField === 'fecha_cita') {
            $query->join('citas', 'cita_servicios.id_cita', '=', 'citas.id_cita')
                  ->orderBy('citas.fecha_programada', $this->sortDirection)
                  ->select('cita_servicios.*');
        } elseif ($this->sortField === 'cliente_nombre') {
            $query->join('citas', 'cita_servicios.id_cita', '=', 'citas.id_cita')
                  ->join('clientes', 'citas.id_cliente', '=', 'clientes.id_cliente')
                  ->join('personas', 'clientes.id_persona', '=', 'personas.id_persona')
                  ->orderBy('personas.nombre', $this->sortDirection)
                  ->select('cita_servicios.*');
        } else {
            $query->orderBy($this->sortField, $this->sortDirection);
        }
        
        $historiales = $query->paginate(10);
        
        return view('livewire.historial.ver-historial', [
            'historiales' => $historiales
        ]);
    }
}