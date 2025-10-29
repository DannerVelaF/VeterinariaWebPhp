<?php

namespace App\Livewire\Inventario;

use App\Models\DetalleVentas;
use App\Models\InventarioMovimiento;
use App\Models\Lotes;
use App\Models\Producto;
use App\Models\TipoMovimiento;
use App\Models\TipoUbicacion;
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

    public $motivosSalida = [];
    public $showMotivoPersonalizado = false;

    public bool $showModal = false;
    public ?InventarioMovimiento $selectedSalida = null;

    public function mount()
    {
        $this->productos = Producto::with(["unidad", "lotes" => function ($query) {
            $query->where('estado', 'activo');
        }])->where("estado", "activo")->get();

        $this->motivosSalida = TipoMovimiento::whereNotIn('nombre_tipo_movimiento', ['Salida', 'Entrada'])->get();
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
            $this->dispatch('stockUpdated');
        } else {
            $this->productoSeleccionado = null;
            $this->cantidad = '';
        }
    }

    public function updatedMotivo($value)
    {
        // Mostrar campo personalizado solo si selecciona "Otro"
        $this->showMotivoPersonalizado = ($value === 'otro');
        if (!$this->showMotivoPersonalizado) {
            $this->motivo_personalizado = '';
        }
    }

    public function registrarSalida()
    {
        // Validación inicial
        $this->validate([
            "id_producto" => "required|exists:productos,id_producto",
            'cantidad' => 'required|numeric|min:0.01|max:999999999.99',
            'ubicacion' => 'required|in:almacen,mostrador',
            "motivo" => "required",
            "motivo_personalizado" => $this->motivo === 'otro' ? 'required|max:1000' : 'nullable',
        ], [
            'motivo.required' => 'Seleccione un motivo de salida.',
            'motivo_personalizado.required' => 'Cuando selecciona "Otro", debe especificar el motivo.',
        ]);

        // Normalizar cantidad
        $this->cantidad = is_string($this->cantidad) ? floatval(str_replace(',', '.', $this->cantidad)) : (float)$this->cantidad;

        // Determinar el motivo final - SIEMPRE usar el texto personalizado si está disponible
        $motivoFinal = $this->motivo_personalizado ?: $this->getNombreMotivo($this->motivo);

        // Verificar stock total disponible (ambas ubicaciones)
        $stockDisponible = $this->getStockActualProperty();
        $stockTotal = $stockDisponible['total'];

        if ($stockTotal < $this->cantidad) {
            $this->dispatch(
                'notify',
                title: 'Error',
                description: "Stock total insuficiente. Stock disponible: {$stockTotal}",
                type: 'error'
            );
            return;
        }

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

                // 5) Consumir primero de la ubicación primaria (la seleccionada)
                $cantidadRestante = $this->consumirDeUbicacion($lotesTotales, $consumoInfo['ubicacion_primaria'], $cantidadRestante, $trabajador, $consumoInfo, $motivoFinal);

                // 6) Si aún queda cantidad, consumir de la ubicación secundaria AUTOMÁTICAMENTE
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
            $mensaje = 'Salida registrada con éxito';

            if ($consumoInfo['secundaria'] > 0) {
                $mensaje .= " ({$consumoInfo['primaria']} de {$consumoInfo['ubicacion_primaria']} + {$consumoInfo['secundaria']} de {$consumoInfo['ubicacion_secundaria']})";
            } else {
                $mensaje .= " ({$consumoInfo['primaria']} de {$consumoInfo['ubicacion_primaria']})";
            }

            $this->dispatch(
                'notify',
                title: 'Éxito',
                description: $mensaje,
                type: 'success'
            );

            $this->resetForm();
            $this->dispatch('salidaRegistrada');
        } catch (Exception $e) {
            $this->dispatch(
                'notify',
                title: 'Error',
                description: 'Error al registrar la salida: ' . $e->getMessage(),
                type: 'error'
            );

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
     * Obtener el nombre del motivo basado en el ID o valor
     */
    private function getNombreMotivo($motivoValue)
    {
        if ($motivoValue === 'otro') {
            return $this->motivo_personalizado;
        }

        // Buscar en los motivos de la base de datos
        $motivo = $this->motivosSalida->firstWhere('id_tipo_movimiento', $motivoValue);
        return $motivo ? $motivo->nombre_tipo_movimiento : 'Motivo no especificado';
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
        })->sortBy('fecha_recepcion');

        $cantidadConsumidaUbicacion = 0;

        foreach ($lotesUbicacion as $lote) {
            if ($cantidadRestante <= 0) break;

            $cantidadDisponible = (float) ($ubicacion === 'almacen'
                ? $lote->cantidad_almacenada
                : $lote->cantidad_mostrada);

            if ($cantidadDisponible <= 0) continue;

            $cantidadAUsar = min($cantidadRestante, $cantidadDisponible);
            $cantidadConsumidaUbicacion += $cantidadAUsar;

            // Reducir de la ubicación seleccionada
            if ($ubicacion === 'almacen') {
                $lote->cantidad_almacenada = round((float)$lote->cantidad_almacenada - $cantidadAUsar, 2);
            } else {
                $lote->cantidad_mostrada = round((float)$lote->cantidad_mostrada - $cantidadAUsar, 2);
            }

            // Incrementar cantidad_vendida si existe
            if (Schema::hasColumn('lotes', 'cantidad_vendida')) {
                $lote->cantidad_vendida = round((float)($lote->cantidad_vendida ?? 0) + $cantidadAUsar, 2);
            }

            // Marcar inactivo si ya no queda stock
            if (($lote->cantidad_almacenada + $lote->cantidad_mostrada) <= 0) {
                $lote->estado = 'vendido';
            }

            $lote->save();

            // Recalcular stock total real del producto
            $stockProductoDespues = Lotes::where('id_producto', $lote->id_producto)
                ->where('estado', 'activo')
                ->get()
                ->sum(function ($l) {
                    return (float)$l->cantidad_almacenada + (float)$l->cantidad_mostrada;
                });

            // Crear o reutilizar una venta dummy
            $ventaDummy = Ventas::firstOrCreate(
                [
                    'fecha_venta' => now()->format('Y-m-d'),
                    'total' => 0,
                    'subtotal' => 0,
                    'descuento' => 0,
                    'impuesto' => 0,
                    'estado' => 'entregado',
                    'id_cliente' => null,
                    'id_trabajador' => $trabajador->id_trabajador,
                    "fecha_registro" => now(),
                ]
            );

            // Crear detalle_venta dummy - USAR EL MOTIVO FINAL (texto del usuario)
            $detalleVenta = DetalleVentas::create([
                'id_venta' => $ventaDummy->id_venta,
                'id_producto' => $this->id_producto,
                'cantidad' => $cantidadAUsar,
                'precio_unitario' => 0,
                'subtotal' => 0,
                'motivo_salida' => $motivoFinal, // Usar el motivo final (texto del usuario)
            ]);

            $salidas = TipoMovimiento::where("nombre_tipo_movimiento", "salida")->first();
            $ubicacionModel = TipoUbicacion::where("nombre_tipo_ubicacion", $ubicacion)->first();

            // Registrar movimiento - USAR EL MOTIVO FINAL (texto del usuario)
            InventarioMovimiento::create([
                "id_tipo_movimiento" => $salidas->id_tipo_movimiento,
                "cantidad_movimiento" => $cantidadAUsar,
                "stock_resultante" => $stockProductoDespues,
                "fecha_movimiento" => now(),
                "fecha_registro" => now(),
                "id_lote" => $lote->id_lote,
                "id_trabajador" => $trabajador->id_trabajador,
                "id_tipo_ubicacion" => $ubicacionModel->id_tipo_ubicacion,
                "motivo" => $motivoFinal, // Usar el motivo final (texto del usuario)
                "tipo_movimiento_asociado" => DetalleVentas::class,
                "id_movimiento_asociado" => $detalleVenta->id_detalle_venta,
            ]);

            $cantidadRestante -= $cantidadAUsar;
        }

        // Actualizar el tracking del consumo
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
            'stockActual' => $this->getStockActualProperty()
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
        $this->showMotivoPersonalizado = false;
        $this->cargarSalidasRecientes();
        $this->dispatch('stockUpdated');
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

        $lotes = Lotes::where('id_producto', $this->productoSeleccionado->id_producto)
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
