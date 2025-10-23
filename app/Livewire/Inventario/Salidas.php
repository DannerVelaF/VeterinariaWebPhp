<?php

namespace App\Livewire\Inventario;

use App\Models\DetalleVentas;
use App\Models\InventarioMovimiento;
use App\Models\Lotes;
use App\Models\Producto;
use App\Models\TipoMovimiento;
use App\Models\Ventas;
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
    public $id_producto = null;
    public $productoSeleccionado = null;
    public $ubicacion = "mostrador";
    public $motivo = "";
    public $motivo_personalizado = "";
    public $cantidad = "";
    public $top10salidas = [];

    // Motivos predefinidos
    public $motivosPredefinidos = [
        'Venta',
        'Producto defectuoso',
        'Cambio',
        'Devolución',
        'Merma',
        'Ajuste de inventario',
        'Donación',
        'Uso interno',
        'Promoción',
        'Otro' // Este permitirá texto personalizado
    ];

    public bool $showModal = false;
    public ?InventarioMovimiento $selectedSalida = null;

    public function mount()
    {
        $this->productos = Producto::with("unidad")->where("estado", "activo")->get();
        $this->cargarSalidasRecientes();
    }

    public function cargarSalidasRecientes()
    {
        $salidas = TipoMovimiento::where("nombre_tipo_movimiento", "salida")->first();

        $this->top10salidas = InventarioMovimiento::where("id_tipo_movimiento", $salidas->id_tipo_movimiento)
            ->with(['lote.producto', 'trabajador.persona.user'])
            ->orderBy("fecha_movimiento", "desc")
            ->limit(10)
            ->get();
    }

    public function updatedIdProducto($value)
    {
        if ($value) {
            $this->productoSeleccionado = Producto::with('unidad')->find($value);
            $this->cantidad = '';
        } else {
            $this->productoSeleccionado = null;
            $this->cantidad = '';
        }
    }

    public function updatedMotivo($value)
    {
        // Si selecciona "Otro", limpiar el motivo personalizado para que pueda escribir
        if ($value === 'Otro') {
            $this->motivo_personalizado = '';
        } else {
            // Si selecciona cualquier otro motivo, limpiar el personalizado
            $this->motivo_personalizado = '';
        }
    }

    public function registrarSalida()
    {
        // Normalizar cantidad
        $this->cantidad = is_string($this->cantidad) ? floatval(str_replace(',', '.', $this->cantidad)) : (float)$this->cantidad;

        // Determinar el motivo final
        $motivoFinal = $this->motivo;
        if ($this->motivo === 'Otro' && !empty($this->motivo_personalizado)) {
            $motivoFinal = $this->motivo_personalizado;
        }

        $this->validate([
            "id_producto" => "required|exists:productos,id_producto",
            'cantidad' => 'required|numeric|min:0.01|max:999999999.99',
            'ubicacion' => 'required|in:almacen,mostrador',
            "motivo" => "required|in:" . implode(',', $this->motivosPredefinidos),
            "motivo_personalizado" => $this->motivo === 'Otro' ? 'required|max:1000' : 'nullable',
        ], [
            'motivo.required' => 'Seleccione un motivo de salida.',
            'motivo_personalizado.required' => 'Cuando selecciona "Otro", debe especificar el motivo.',
        ]);

        // Variables para tracking del consumo
        $consumoInfo = [
            'primaria' => 0,
            'secundaria' => 0,
            'ubicacion_primaria' => $this->ubicacion,
            'ubicacion_secundaria' => ($this->ubicacion === 'almacen') ? 'mostrador' : 'almacen',
            'stock_primaria' => 0,
            'stock_secundaria' => 0
        ];

        try {
            DB::transaction(function () use (&$consumoInfo, $motivoFinal) {
                // 1) Lock del producto
                $producto = Producto::where('id_producto', $this->id_producto)->lockForUpdate()->first();
                if (!$producto) {
                    throw new Exception("Producto no encontrado.");
                }

                // 2) Calcular stock total disponible (ambas ubicaciones)
                $lotesTotales = Lotes::where('id_producto', $this->id_producto)
                    ->where('estado', 'activo')
                    ->lockForUpdate()
                    ->get();

                // Calcular stocks disponibles
                $consumoInfo['stock_primaria'] = $lotesTotales->sum(
                    $consumoInfo['ubicacion_primaria'] === 'almacen' ? 'cantidad_almacenada' : 'cantidad_mostrada'
                );

                $consumoInfo['stock_secundaria'] = $lotesTotales->sum(
                    $consumoInfo['ubicacion_secundaria'] === 'almacen' ? 'cantidad_almacenada' : 'cantidad_mostrada'
                );

                $stockTotal = $consumoInfo['stock_primaria'] + $consumoInfo['stock_secundaria'];

                if ($stockTotal < $this->cantidad) {
                    throw new Exception("Stock total insuficiente. Stock disponible: {$stockTotal}");
                }

                // 3) Obtener trabajador
                $trabajador = auth()->user()->persona->trabajador;
                if (!$trabajador) {
                    throw new Exception("El usuario autenticado no tiene un trabajador asociado.");
                }

                // 4) Determinar estrategia de consumo
                $cantidadRestante = (float) $this->cantidad;

                // 5) Consumir primero de la ubicación primaria
                $cantidadRestante = $this->consumirDeUbicacion($lotesTotales, $consumoInfo['ubicacion_primaria'], $cantidadRestante, $trabajador, $consumoInfo, $motivoFinal);

                // 6) Si aún queda cantidad, consumir de la ubicación secundaria
                if ($cantidadRestante > 0) {
                    $cantidadRestante = $this->consumirDeUbicacion($lotesTotales, $consumoInfo['ubicacion_secundaria'], $cantidadRestante, $trabajador, $consumoInfo, $motivoFinal);
                }

                if ($cantidadRestante > 0) {
                    throw new Exception('Error inesperado: No se pudo consumir toda la cantidad solicitada.');
                }

                // 7) Actualizar stock_actual del producto
                if (Schema::hasColumn('productos', 'stock_actual')) {
                    $stockActualReal = (float) Lotes::where('id_producto', $this->id_producto)
                        ->where('estado', 'activo')
                        ->get()
                        ->sum(function ($lote) {
                            return (float)$lote->cantidad_almacenada + (float)$lote->cantidad_mostrada;
                        });

                    $producto->stock_actual = $stockActualReal;
                    $producto->save();
                }
            });

            // Mensaje personalizado según el consumo
            $mensaje = '✅ Salida registrada con éxito';

            if ($consumoInfo['secundaria'] > 0) {
                // Se consumió de ambas ubicaciones
                $mensaje .= " ({$consumoInfo['primaria']} de {$consumoInfo['ubicacion_primaria']} + {$consumoInfo['secundaria']} de {$consumoInfo['ubicacion_secundaria']})";
            } else {
                // Solo se consumió de la ubicación primaria
                $mensaje .= " ({$consumoInfo['primaria']} de {$consumoInfo['ubicacion_primaria']})";
            }

            session()->flash('success', $mensaje);
            $this->resetForm();
            $this->dispatch('salidaRegistrada');
        } catch (Exception $e) {
            session()->flash('error', 'Error al registrar la salida: ' . $e->getMessage());
            Log::error('Error al registrar salida', [
                'error' => $e->getMessage(),
                'id_producto' => $this->id_producto,
                'cantidad' => $this->cantidad,
                'ubicacion' => $this->ubicacion,
                'motivo' => $motivoFinal
            ]);
        }
    }

    /**
     * Consume cantidad de una ubicación específica usando lotes FIFO
     */
    private function consumirDeUbicacion($lotes, $ubicacion, $cantidadRestante, $trabajador, &$consumoInfo, $motivoFinal)
    {
        // Filtrar lotes que tienen stock en la ubicación específica
        $lotesUbicacion = $lotes->filter(function ($lote) use ($ubicacion) {
            return $ubicacion === 'almacen'
                ? (float)$lote->cantidad_almacenada > 0
                : (float)$lote->cantidad_mostrada > 0;
        })->sortBy('fecha_recepcion'); // FIFO

        $cantidadConsumidaUbicacion = 0;

        foreach ($lotesUbicacion as $lote) {
            if ($cantidadRestante <= 0) break;

            $cantidadDisponible = (float) ($ubicacion === 'almacen'
                ? $lote->cantidad_almacenada
                : $lote->cantidad_mostrada);

            if ($cantidadDisponible <= 0) continue;

            $cantidadAUsar = min($cantidadRestante, $cantidadDisponible);
            $cantidadConsumidaUbicacion += $cantidadAUsar;

            // 🔹 Reducir de la ubicación seleccionada
            if ($ubicacion === 'almacen') {
                $lote->cantidad_almacenada = round((float)$lote->cantidad_almacenada - $cantidadAUsar, 2);
            } else {
                $lote->cantidad_mostrada = round((float)$lote->cantidad_mostrada - $cantidadAUsar, 2);
            }

            // 🔹 Incrementar cantidad_vendida si existe
            if (Schema::hasColumn('lotes', 'cantidad_vendida')) {
                $lote->cantidad_vendida = round((float)($lote->cantidad_vendida ?? 0) + $cantidadAUsar, 2);
            }

            // 🔹 Marcar inactivo si ya no queda stock en ninguna ubicación
            if (($lote->cantidad_almacenada + $lote->cantidad_mostrada) <= 0) {
                $lote->estado = 'vendido';
            }

            $lote->save();

            // 🔹 Recalcular stock total real del producto
            $stockProductoDespues = Lotes::where('id_producto', $lote->id_producto)
                ->where('estado', 'activo')
                ->get()
                ->sum(function ($l) {
                    return (float)$l->cantidad_almacenada + (float)$l->cantidad_mostrada;
                });

            // DEBUG
            Log::info('Creando movimiento con motivo:', [
                'motivoFinal' => $motivoFinal,
                'cantidad' => $cantidadAUsar,
                'ubicacion' => $ubicacion
            ]);

            // 🔹 Crear o reutilizar una venta dummy
            $ventaDummy = Ventas::firstOrCreate(
                [
                    'fecha_venta' => now(),
                    'total' => 0,
                    'subtotal' => 0,
                    'descuento' => 0,
                    'impuesto' => 0,
                    'estado' => 'entregado',
                    'id_cliente' => 1, // o un cliente genérico
                    'id_trabajador' => $trabajador->id_trabajador,
                    "fecha_registro" => now(),
                ]
            );

            // 🔹 Crear detalle_venta dummy
            $detalleVenta = DetalleVentas::create([
                'id_venta' => $ventaDummy->id_venta,
                'id_producto' => $this->id_producto,
                'cantidad' => $cantidadAUsar,   // la misma que consumiste del lote
                'precio_unitario' => 0,
                'subtotal' => 0,
                'motivo_salida' => $motivoFinal,
            ]);

            $salidas = TipoMovimiento::where("nombre_tipo_movimiento", "salida")->first();


            // 🔹 Registrar movimiento
            InventarioMovimiento::create([
                "id_tipo_movimiento" => $salidas->id_tipo_movimiento,
                "cantidad_movimiento" => $cantidadAUsar,
                "stock_resultante" => $stockProductoDespues,
                "fecha_movimiento" => now(),
                "fecha_registro" => now(),
                "id_lote" => $lote->id_lote,
                "id_trabajador" => $trabajador->id_trabajador,
                "ubicacion" => $ubicacion,
                "motivo_salida" => $motivoFinal ?? 'Sin motivo especificado',
                "tipo_movimiento_asociado"    => DetalleVentas::class,
                "id_movimiento_asociado"      => $detalleVenta->id_detalle_venta,
            ]);

            $cantidadRestante -= $cantidadAUsar;
        }

        // 🔹 Actualizar el tracking del consumo
        if ($ubicacion === $consumoInfo['ubicacion_primaria']) {
            $consumoInfo['primaria'] = $cantidadConsumidaUbicacion;
        } else {
            $consumoInfo['secundaria'] = $cantidadConsumidaUbicacion;
        }

        return $cantidadRestante;
    }

    public function render()
    {
        $salidaID = TipoMovimiento::where("nombre_tipo_movimiento", "salida")->first();

        $salidas = InventarioMovimiento::where("id_tipo_movimiento", $salidaID->id_tipo_movimiento)
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
        $this->id_producto = null;
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

        $lotes = Lotes::where('id_producto', $this->productoSeleccionado['id_producto'])
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
