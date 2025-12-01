<?php

namespace App\Livewire\Inventario;

use App\Models\EnvioPedido;
use App\Models\EstadoEnvioPedido;
use App\Models\Trabajador;
use App\Models\Ubigeo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

// Importante para verificar archivos
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class Despacho extends Component
{
    use WithPagination;

    // --- Filtros ---
    public $search = '';
    public $departamentoSeleccionado = '';
    public $provinciaSeleccionada = '';
    public $distritoSeleccionado = '';

    // --- Selección Masiva ---
    public $selectedOrders = [];
    public $selectAll = false;

    // --- Modal Masivo ---
    public $modalMasivoOpen = false;
    public $countSeleccionados = 0;
    public $id_trabajador_masivo = '';
    public $fecha_programada_masiva = '';

    // --- Modal Reprogramación Individual ---
    public $modalReprogramarOpen = false;
    public $pedidoReprogramarId = null;
    public $reprogramar_transportista = '';
    public $reprogramar_fecha = '';

    // --- NUEVAS VARIABLES PARA EVIDENCIA ---
    public $modalEvidenciaOpen = false;
    public $evidenciaPedido = null; // Aquí guardaremos el objeto pedido completo para mostrarlo

    // --- Listas ---
    public $departamentos = [];
    public $provincias = [];
    public $distritos = [];

    public function mount()
    {
        $this->departamentos = Ubigeo::select('departamento')
            ->distinct()
            ->orderBy('departamento')
            ->pluck('departamento');
    }

    // ... (Mantén tus métodos de filtros updatedDepartamentoSeleccionado, etc.) ...
    public function updatedDepartamentoSeleccionado($value)
    {
        $this->provinciaSeleccionada = '';
        $this->distritoSeleccionado = '';
        $this->distritos = [];
        if (!empty($value)) {
            $this->provincias = Ubigeo::where('departamento', $value)->select('provincia')->distinct()->orderBy('provincia')->pluck('provincia');
        } else {
            $this->provincias = [];
        }
        $this->resetPage();
    }

    public function updatedProvinciaSeleccionado($value)
    {
        $this->distritoSeleccionado = '';
        if (!empty($value)) {
            $this->distritos = Ubigeo::where('departamento', $this->departamentoSeleccionado)->where('provincia', $value)->select('distrito')->distinct()->orderBy('distrito')->pluck('distrito');
        } else {
            $this->distritos = [];
        }
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedOrders = $this->getPedidosQuery()->pluck('id_envio_pedido')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedOrders = [];
        }
    }

    // --- Métodos Masivos (abrirModalMasivo, guardarAsignacionMasiva, etc.) ---
    public function abrirModalMasivo()
    {
        $ids = array_filter($this->selectedOrders);
        $this->countSeleccionados = count($ids);

        if ($this->countSeleccionados > 0) {
            $this->modalMasivoOpen = true;
            $this->fecha_programada_masiva = now()->format('Y-m-d\TH:i');
        } else {
            $this->dispatch('notify', title: 'Atención', description: 'Selecciona al menos un pedido.', type: 'warning');
        }
    }

    public function guardarAsignacionMasiva()
    {
        $this->validate([
            'id_trabajador_masivo' => 'required|exists:trabajadores,id_trabajador',
            'fecha_programada_masiva' => 'required|date',
        ]);

        $ids = array_filter($this->selectedOrders);

        try {
            DB::transaction(function () use ($ids) {
                $estadoAsignado = EstadoEnvioPedido::where('nombre_estado_envio_pedido', 'asignado')->first();
                if (!$estadoAsignado) throw new \Exception("Estado 'asignado' no encontrado.");

                EnvioPedido::whereIn('id_envio_pedido', $ids)
                    ->update([
                        'id_trabajador' => $this->id_trabajador_masivo,
                        'fecha_programada' => $this->fecha_programada_masiva,
                        'id_estado_envio_pedido' => $estadoAsignado->id_estado_envio_pedido,
                        'fecha_actualizacion' => now()
                    ]);
            });

            $this->modalMasivoOpen = false;
            $this->selectedOrders = [];
            $this->selectAll = false;
            $this->dispatch('notify', title: 'Éxito', description: 'Ruta asignada correctamente.', type: 'success');

        } catch (\Exception $e) {
            Log::error("Error masivo: " . $e->getMessage());
            $this->dispatch('notify', title: 'Error', description: $e->getMessage(), type: 'error');
        }
    }

    // --- Lógica Reprogramación ---
    public function abrirModalReprogramar($id)
    {
        $pedido = EnvioPedido::find($id);
        if ($pedido) {
            $this->pedidoReprogramarId = $id;
            $this->reprogramar_transportista = $pedido->id_trabajador;
            $this->reprogramar_fecha = $pedido->fecha_programada ? $pedido->fecha_programada->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i');
            $this->modalReprogramarOpen = true;
        }
    }

    public function guardarReprogramacion()
    {
        $this->validate([
            'reprogramar_transportista' => 'nullable|exists:trabajadores,id_trabajador',
            'reprogramar_fecha' => 'required|date',
        ]);

        try {
            $pedido = EnvioPedido::find($this->pedidoReprogramarId);
            if ($pedido) {
                $datosUpdate = [
                    'id_trabajador' => $this->reprogramar_transportista,
                    'fecha_programada' => $this->reprogramar_fecha,
                    'fecha_actualizacion' => now()
                ];

                // Si estaba pendiente y asignamos chofer, pasarlo a 'asignado'
                // Ojo: Si ya estaba entregado, NO deberíamos cambiar el estado automáticamente aquí si es solo reprogramación,
                // pero si el admin está reprogramando un fallido o pendiente, sí.
                if ($pedido->estadoEnvio->nombre_estado_envio_pedido === 'pendiente' && $this->reprogramar_transportista) {
                    $estadoAsignado = EstadoEnvioPedido::where('nombre_estado_envio_pedido', 'asignado')->first();
                    if ($estadoAsignado) {
                        $datosUpdate['id_estado_envio_pedido'] = $estadoAsignado->id_estado_envio_pedido;
                    }
                }

                $pedido->update($datosUpdate);
                $this->modalReprogramarOpen = false;
                $this->dispatch('notify', title: 'Actualizado', description: 'Pedido reprogramado con éxito.', type: 'success');
            }
        } catch (\Exception $e) {
            $this->dispatch('notify', title: 'Error', description: 'No se pudo reprogramar.', type: 'error');
        }
    }

    // --- NUEVO MÉTODO: Ver Evidencia ---
    public function verEvidencia($id)
    {
        // Cargamos el pedido con sus relaciones para mostrar info completa en el modal
        $pedido = EnvioPedido::with(['venta.cliente.persona', 'trabajador.persona', 'estadoEnvio'])->find($id);

        if ($pedido) {
            $this->evidenciaPedido = $pedido;
            $this->modalEvidenciaOpen = true;
        }
    }

    public function cerrarModalEvidencia()
    {
        $this->modalEvidenciaOpen = false;
        $this->evidenciaPedido = null;
    }

    // --- Query Principal ---
    private function getPedidosQuery()
    {
        return EnvioPedido::query()
            ->with([
                'venta.cliente.persona',
                'direccion.ubigeo',
                'estadoEnvio',
                'trabajador.persona'
            ])
            ->whereHas('venta', fn($q) => $q->where('tipo_venta', 'web'))
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('venta.cliente.persona', function ($q2) {
                        $q2->where('nombre', 'like', '%' . $this->search . '%')
                            ->orWhere('apellido_paterno', 'like', '%' . $this->search . '%')
                            ->orWhere('numero_documento', 'like', '%' . $this->search . '%');
                    })
                        ->orWhereHas('direccion', function ($q3) {
                            $q3->where('nombre_calle', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->departamentoSeleccionado, fn($q) => $q->whereHas('direccion.ubigeo', fn($u) => $u->where('departamento', $this->departamentoSeleccionado)))
            ->when($this->provinciaSeleccionada, fn($q) => $q->whereHas('direccion.ubigeo', fn($u) => $u->where('provincia', $this->provinciaSeleccionada)))
            ->when($this->distritoSeleccionado, fn($q) => $q->whereHas('direccion.ubigeo', fn($u) => $u->where('distrito', $this->distritoSeleccionado)))
            ->orderBy('id_envio_pedido', 'desc');
    }

    public function render()
    {
        $pedidos = $this->getPedidosQuery()->paginate(10);
        $transportistas = Trabajador::with('persona')
            ->whereHas('puestoTrabajo', function ($query) {
                $query->where('nombre_puesto', 'like', '%Transportista%')
                    ->orWhere('nombre_puesto', 'like', '%Chofer%');
            })->get();

        return view('livewire.inventario.despacho', [
            'pedidos' => $pedidos,
            'transportistas' => $transportistas
        ]);
    }
}
