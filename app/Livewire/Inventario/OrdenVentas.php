<?php

namespace App\Livewire\Inventario;

use App\Models\InventarioMovimiento;
use App\Models\Lotes;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\DetalleVenta;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class Salidas extends Component
{
    use WithPagination;

    public $productos = [];
    public $producto_id = null;
    public $productoSeleccionado = null;
    public $ubicacion = "mostrador";
    public $motivo = "";
    public $cantidad = "";
    public $ordenVenta = '';
    public $productosOV = [];
    public $clienteOV = null;
    public $top10salidas = [];

    public bool $showModal = false;
    public ?InventarioMovimiento $selectedSalida = null;

    public function mount()
    {
        $this->productos = Producto::with("unidad")->where("estado", "activo")->get();
        $this->top10salidas = InventarioMovimiento::where("tipo_movimiento", "salida")
            ->orderBy("fecha_movimiento", "desc")
            ->limit(10)
            ->get();
    }

    public function buscarOrdenVenta()
    {
        $this->productosOV = [];
        $this->clienteOV = null;
        $this->producto_id = null;
        $this->productoSeleccionado = null;
        $this->cantidad = '';

        if (!$this->ordenVenta) {
            session()->flash('error', 'No se ha introducido el código de orden de venta');
            return;
        }

        $venta = Venta::with('detalleVenta.producto.unidad', 'cliente')
            ->where('codigo', trim($this->ordenVenta))
            ->first();

        if (!$venta) {
            session()->flash('error', 'No se encontró la orden de venta: ' . $this->ordenVenta);
            return;
        }

        if ($venta->estado == "pendiente") {
            session()->flash('error', 'La orden de venta no ha sido aprobada');
            $this->ordenVenta = "";
            return;
        } elseif ($venta->estado == "entregado") {
            session()->flash('error', 'La orden de venta ya ha sido entregada');
            $this->ordenVenta = "";
            return;
        } elseif ($venta->estado == "cancelado") {
            session()->flash('error', 'La orden se encuentra cancelada');
            $this->ordenVenta = "";
            return;
        }

        $this->clienteOV = $venta->cliente;

        $this->productosOV = $venta->detalleVenta
            ->where('estado', 'pendiente')
            ->map(function ($detalle) {
                return [
                    'id' => $detalle->producto->id,
                    'detalle_venta_id' => $detalle->id,
                    'nombre' => $detalle->producto->nombre_producto . ' (' . $detalle->producto->unidad->nombre . ')',
                    'precio_venta' => $detalle->precio_unitario,
                    'cantidad' => $detalle->cantidad,
                    'codigo_barras' => $detalle->producto->codigo_barras,
                    'estado' => $detalle->estado,
                ];
            })->values();
    }

    public function updatedProductoId($value)
    {
        $this->productoSeleccionado = collect($this->productosOV)
            ->firstWhere('detalle_venta_id', $value);

        if ($this->productoSeleccionado) {
            $this->cantidad = $this->productoSeleccionado['cantidad'];
        } else {
            $this->cantidad = '';
        }
    }

    public function registrarSalida()
    {
        $this->validate([
            "producto_id" => "required|exists:detalle_ventas,id",
            'cantidad' => 'required|numeric|min:0.01|max:999999999.99',
            'ubicacion' => 'required|in:almacen,mostrador',
            "motivo" => "required|max:1000",
        ]);

        try {
            DB::transaction(function () {
                // Obtener el producto
                $producto = Producto::find($this->productoSeleccionado['id']);
                
                // Verificar stock disponible
                if ($producto->stock_actual < $this->cantidad) {
                    throw new Exception('Stock insuficiente. Stock disponible: ' . $producto->stock_actual);
                }

                // Obtener el trabajador del usuario autenticado
                $trabajador = auth()->user()->persona->trabajador;

                if (!$trabajador) {
                    throw new Exception("El usuario autenticado no tiene un trabajador asociado");
                }

                // Buscar lotes disponibles (FIFO - Primero en entrar, primero en salir)
                $lotesDisponibles = Lotes::where('producto_id', $this->productoSeleccionado['id'])
                    ->where('estado', 'activo')
                    ->where(function($query) {
                        if ($this->ubicacion === 'almacen') {
                            $query->where('cantidad_almacenada', '>', 0);
                        } else {
                            $query->where('cantidad_mostrada', '>', 0);
                        }
                    })
                    ->orderBy('fecha_recepcion', 'asc')
                    ->get();

                $cantidadRestante = $this->cantidad;
                $lotesUtilizados = [];

                foreach ($lotesDisponibles as $lote) {
                    if ($cantidadRestante <= 0) break;

                    $cantidadDisponible = $this->ubicacion === 'almacen' 
                        ? $lote->cantidad_almacenada 
                        : $lote->cantidad_mostrada;

                    $cantidadAUsar = min($cantidadRestante, $cantidadDisponible);

                    // Registrar movimiento de salida para este lote
                    InventarioMovimiento::create([
                        "tipo_movimiento" => "salida",
                        "cantidad_movimiento" => $cantidadAUsar,
                        "stock_resultante" => $this->ubicacion === 'almacen' 
                            ? $lote->cantidad_almacenada - $cantidadAUsar
                            : $lote->cantidad_mostrada - $cantidadAUsar,
                        "fecha_movimiento" => now(),
                        "fecha_registro" => now(),
                        "id_lote" => $lote->id,
                        "id_trabajador" => $trabajador->id,
                        "ubicacion" => $this->ubicacion,
                        "motivo" => $this->motivo,
                        "movimentable_type" => DetalleVenta::class,
                        "movimentable_id" => $this->productoSeleccionado['detalle_venta_id'],
                    ]);

                    // Actualizar el lote
                    if ($this->ubicacion === 'almacen') {
                        $lote->decrement('cantidad_almacenada', $cantidadAUsar);
                    } else {
                        $lote->decrement('cantidad_mostrada', $cantidadAUsar);
                    }

                    // Actualizar cantidad total del lote
                    $lote->decrement('cantidad_total', $cantidadAUsar);
                    
                    // Si el lote se agota, marcarlo como inactivo
                    if ($lote->cantidad_total <= 0) {
                        $lote->update(['estado' => 'inactivo']);
                    }

                    $cantidadRestante -= $cantidadAUsar;
                    $lotesUtilizados[] = $lote->codigo_lote;
                }

                if ($cantidadRestante > 0) {
                    throw new Exception('No hay suficiente stock disponible en los lotes');
                }

                // Actualizar el detalle de venta
                $detalle = DetalleVenta::find($this->productoSeleccionado['detalle_venta_id']);
                $detalle->estado = 'entregado';
                $detalle->save();

                // Verificar si todos los detalles de la orden de venta están entregados
                $ordenVenta = $detalle->venta;
                $todosEntregados = $ordenVenta->detalleVenta()->where('estado', 'pendiente')->count() === 0;
                if ($todosEntregados) {
                    $ordenVenta->estado = 'entregado';
                    $ordenVenta->save();
                }

                // Actualizar stock del producto
                $producto->decrement('stock_actual', $this->cantidad);
            });

            session()->flash('success', '✅ Salida registrada con éxito. Lotes utilizados: ' . implode(', ', $lotesUtilizados));
            $this->resetForm();
            $this->dispatch('salidaRegistrada');
        } catch (Exception $e) {
            session()->flash('error', 'Error al registrar la salida: ' . $e->getMessage());
            Log::error('Error al registrar salida', ['error' => $e->getMessage()]);
        }
    }

    public function render()
    {
        $salidas = InventarioMovimiento::where("tipo_movimiento", "salida")
            ->orderBy("fecha_movimiento", "desc")
            ->paginate(10);

        return view('livewire.inventario.salidas', [
            'salidas' => $salidas
        ]);
    }

    public function resetForm()
    {
        $this->ordenVenta = "";
        $this->producto_id = null;
        $this->productoSeleccionado = null;
        $this->ubicacion = "mostrador";
        $this->cantidad = "";
        $this->motivo = "";

        $this->mount();
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

        $lotes = Lotes::where('producto_id', $this->productoSeleccionado['id'])
            ->where('estado', 'activo')
            ->get();

        $total = $lotes->sum('cantidad_total');
        $almacen = $lotes->sum('cantidad_almacenada');
        $mostrador = $lotes->sum('cantidad_mostrada');

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