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
    use WithPagination;

    public $productos = [];
    public $producto_id = null;
    public $productoSeleccionado = null;
    public $ubicacion = "mostrador";
    public $motivo = "";
    public $cantidad = "";
    public $top10salidas = [];

    public bool $showModal = false;
    public ?InventarioMovimiento $selectedSalida = null;

    public function mount()
    {
        $this->productos = Producto::with("unidad")->where("estado", "activo")->get();
        $this->cargarSalidasRecientes();
    }

    public function cargarSalidasRecientes()
    {
        $this->top10salidas = InventarioMovimiento::where("tipo_movimiento", "salida")
            ->with(['lote.producto', 'trabajador.persona.user'])
            ->orderBy("fecha_movimiento", "desc")
            ->limit(10)
            ->get();
    }

    public function updatedProductoId($value)
    {
        if ($value) {
            $this->productoSeleccionado = Producto::with('unidad')->find($value);
            $this->cantidad = '';
        } else {
            $this->productoSeleccionado = null;
            $this->cantidad = '';
        }
    }

    public function registrarSalida()
    {
        // Normalizar cantidad (acepta coma o punto)
        $this->cantidad = is_string($this->cantidad) ? floatval(str_replace(',', '.', $this->cantidad)) : (float)$this->cantidad;

        $this->validate([
            "producto_id" => "required|exists:productos,id",
            'cantidad' => 'required|numeric|min:0.01|max:999999999.99',
            'ubicacion' => 'required|in:almacen,mostrador',
            "motivo" => "required|max:1000",
        ]);

        try {
            DB::transaction(function () {
                // 1) Lock del producto para evitar lectura concurrente de stock
                $producto = Producto::where('id', $this->producto_id)->lockForUpdate()->first();
                if (!$producto) {
                    throw new Exception("Producto no encontrado.");
                }

                // 2) Obtener y bloquear lotes disponibles en la ubicación (FIFO)
                $lotesQuery = Lotes::where('producto_id', $this->producto_id)
                    ->where('estado', 'activo')
                    ->where(function ($q) {
                        if ($this->ubicacion === 'almacen') {
                            $q->where('cantidad_almacenada', '>', 0);
                        } else {
                            $q->where('cantidad_mostrada', '>', 0);
                        }
                    })
                    ->orderBy('fecha_recepcion', 'asc');

                // lockForUpdate para bloquear filas hasta commit
                $lotesDisponibles = $lotesQuery->lockForUpdate()->get();

                if ($lotesDisponibles->isEmpty()) {
                    throw new Exception("No hay lotes disponibles en {$this->ubicacion}.");
                }

                // Calcular stock disponible en la ubicación usando lotes bloqueados
                $stockDisponibleUbicacion = $lotesDisponibles->sum($this->ubicacion === 'almacen' ? 'cantidad_almacenada' : 'cantidad_mostrada');

                if ($stockDisponibleUbicacion < $this->cantidad) {
                    throw new Exception("Stock insuficiente en {$this->ubicacion}. Stock disponible: {$stockDisponibleUbicacion}");
                }

                // Obtener trabajador del usuario autenticado
                $trabajador = auth()->user()->persona->trabajador;
                if (!$trabajador) {
                    throw new Exception("El usuario autenticado no tiene un trabajador asociado.");
                }

                // 3) Consumir lotes FIFO
                $cantidadRestante = (float) $this->cantidad;
                $lotesUtilizados = [];

                foreach ($lotesDisponibles as $lote) {
                    if ($cantidadRestante <= 0) break;

                    $cantidadDisponible = (float) ($this->ubicacion === 'almacen' ? $lote->cantidad_almacenada : $lote->cantidad_mostrada);
                    if ($cantidadDisponible <= 0) continue;

                    $cantidadAUsar = (float) min($cantidadRestante, $cantidadDisponible);

                    // Actualizar cantidades en el lote (en memoria)
                    if ($this->ubicacion === 'almacen') {
                        $lote->cantidad_almacenada = round((float)$lote->cantidad_almacenada - $cantidadAUsar, 2);
                    } else {
                        $lote->cantidad_mostrada = round((float)$lote->cantidad_mostrada - $cantidadAUsar, 2);
                    }

                    // Actualizar cantidad_total si existe
                    if (isset($lote->cantidad_total)) {
                        $lote->cantidad_total = round((float)$lote->cantidad_total - $cantidadAUsar, 2);
                    }

                    // Incrementar cantidad_vendida solo si la columna existe
                    if (Schema::hasColumn('lotes', 'cantidad_vendida')) {
                        $lote->cantidad_vendida = round((float)($lote->cantidad_vendida ?? 0) + $cantidadAUsar, 2);
                    }

                    // Si se agota el lote, marcar inactivo
                    if (isset($lote->cantidad_total) && $lote->cantidad_total <= 0) {
                        $lote->estado = 'inactivo';
                    }

                    // Guardar cambios en el lote (filas ya bloqueadas por lockForUpdate)
                    $lote->save();

                    // Recalcular stock total real del producto (suma cantidad_total de lotes activos)
                    $stockProductoDespues = Lotes::where('producto_id', $this->producto_id)
                        ->where('estado', 'activo')
                        ->sum('cantidad_total');

                    // Registrar movimiento por esta porción
                    InventarioMovimiento::create([
                        "tipo_movimiento" => "salida",
                        "cantidad_movimiento" => $cantidadAUsar,
                        "stock_resultante" => $stockProductoDespues,
                        "fecha_movimiento" => now(),
                        "fecha_registro" => now(),
                        "id_lote" => $lote->id,
                        "id_trabajador" => $trabajador->id,
                        "ubicacion" => $this->ubicacion,
                        "motivo" => $this->motivo,
                        "movimentable_type" => 'App\Models\Lotes',
                        "movimentable_id" => $lote->id,
                    ]);

                    $cantidadRestante -= $cantidadAUsar;
                    $lotesUtilizados[] = $lote->codigo_lote;
                }

                if ($cantidadRestante > 0) {
                    // Debería ser raro porque verificamos el stock antes; si ocurre, rollback
                    throw new Exception('No se pudo consumir la cantidad solicitada. Quedan: ' . $cantidadRestante);
                }

                // 4) Sincronizar stock_actual en productos (si existe la columna)
                /* if (Schema::hasColumn('productos', 'stock_actual')) {
                    $stockActualReal = (float) Lotes::where('producto_id', $this->producto_id)
                        ->where('estado', 'activo')
                        ->sum('cantidad_total');

                    $producto->stock_actual = $stockActualReal;
                    $producto->save();
                } */

                if (Schema::hasColumn('productos', 'stock_actual')) {
                $stockActualReal = (float) Lotes::where('producto_id', $this->producto_id)
                    ->where('estado', 'activo')
                    ->get()
                    ->sum(function($lote) {
                        return (float)$lote->cantidad_almacenada + (float)$lote->cantidad_mostrada;
                    });

                $producto->stock_actual = $stockActualReal;
                $producto->save();
            }
            }); // fin transaction

            // Si todo OK
            session()->flash('success', '✅ Salida registrada con éxito');
            $this->resetForm();
            $this->dispatch('salidaRegistrada'); // Mantengo tu patrón (PowerGrid escucha este evento)
        } catch (Exception $e) {
            session()->flash('error', 'Error al registrar la salida: ' . $e->getMessage());
            Log::error('Error al registrar salida', [
                'error' => $e->getMessage(),
                'producto_id' => $this->producto_id,
                'cantidad' => $this->cantidad,
                'ubicacion' => $this->ubicacion
            ]);
        }
    }

    public function render()
    {
        $salidas = InventarioMovimiento::where("tipo_movimiento", "salida")
            ->with(['lote.producto', 'trabajador.persona.user'])
            ->orderBy("fecha_movimiento", "desc")
            ->paginate(10);

        return view('livewire.inventario.salidas', [
            'salidas' => $salidas
        ]);
    }

    public function resetForm()
    {
        $this->producto_id = null;
        $this->productoSeleccionado = null;
        $this->ubicacion = "mostrador";
        $this->cantidad = "";
        $this->motivo = "";
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
