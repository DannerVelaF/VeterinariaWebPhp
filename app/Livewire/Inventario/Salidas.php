<?php

namespace App\Livewire\Inventario;

use App\Models\InventarioMovimiento;
use App\Models\Lotes;
use App\Models\Producto;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Livewire\WithPagination;

class Salidas extends Component
{

    public function render()
    {
        $salidas = InventarioMovimiento::where("tipo_movimiento", "salida")
            ->with(['lote.producto', 'trabajador.persona.user'])
            ->orderBy("fecha_movimiento", "desc")
            ->paginate(10);

        return view('livewire.inventario.salidas', [
            'salidas' => $salidas,
            'motivosPredefinidos' => $this->motivosPredefinidos
        ]);
    }

    public function resetForm()
    {
        $this->producto_id = null;
        $this->productoSeleccionado = null;
        $this->ubicacion = "mostrador";
        $this->cantidad = "";
        $this->motivo = "";
        $this->motivo_personalizado = "";
        $this->cargarSalidasRecientes();
    }

    public function getStockActualProperty()
    {
        if (!$this->productoSeleccionado) {
            return [
                'total' => 0,
                'almacen' => 0,
                'mostrador' => 0,
            ];
        }

        $lotes = Lotes::where('producto_id', $this->producto_id)
            ->where('estado', 'activo')
            ->get();

        $almacen = $lotes->sum('cantidad_almacenada');
        $mostrador = $lotes->sum('cantidad_mostrada');
        $total = $almacen + $mostrador;

        return [
            'total' => $total,
            'almacen' => $almacen,
            'mostrador' => $mostrador,
        ];
    }

    #[\Livewire\Attributes\On('show-modal')]
    public function showModal(int $rowId): void
    {
        $this->selectedSalida = InventarioMovimiento::with(['lote.producto', 'trabajador.persona.user'])
            ->find($rowId);

        $this->showModal = true;
    }
}
