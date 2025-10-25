<?php

namespace App\Livewire\Inventario;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\EstadoCompras;
use App\Models\EstadoDetalleCompra;
use App\Models\InventarioMovimiento;
use App\Models\Lotes;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\TipoMovimiento;
use App\Models\TipoUbicacion;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithPagination;

class Entradas extends Component
{
    use WithPagination;

    // Para que la paginación se reinicie al filtrar productos
    protected $updatesQueryString = ['id_producto'];
    public $productos = [];
    public $proveedores = [];
    public $id_producto = null;
    public $observacion = "";
    public $tipoUbicaciones = [];
    public $tipoUbicacionSeleccionada = null;
    public $productoSeleccionado = null; // objeto del producto
    public $ubicacion = "";
    public $lote = [
        "cantidad_total" => "",
        "cantidad_mostrada" => "",
        "cantidad_almacenada" => "",
        "observacion" => "",
        "fecha_recepcion" => "",
        "fecha_vencimiento" => "",
        "id_producto" => "",
        "precio_compra" => "",
        "codigo_lote" => "",
    ];

    public bool $showModal = false;
    public bool $showModalDetalle = false;
    public ?InventarioMovimiento $selectedEntrada = null;

    public $ordenCompra = '';
    public $productosOC = []; // productos de la orden de compra
    public $proveedorOC = null; // proveedor de la OC

    public function mount()
    {
        $this->ubicacion = "almacen";
        $this->productos = Producto::with("unidad")->where("estado", "activo")->get();
        $this->proveedores = Proveedor::where('estado', 'activo')->get();
        if (!$this->lote['fecha_recepcion']) {
            $this->lote['fecha_recepcion'] = now()->format('Y-m-d');
        }
        $tipoEntrad = TipoMovimiento::where("nombre_tipo_movimiento", "entrada")->first();
    }

    public function buscarOrdenCompra()
    {
        $this->productosOC = [];
        $this->proveedorOC = null;
        $this->id_producto = null;
        $this->productoSeleccionado = null;
        $this->lote['cantidad_total'] = '';
        $this->lote['precio_compra'] = '';


        if (!$this->ordenCompra) {
            $this->dispatch('notify', title: 'Error', description: 'No se ha introducido el código de orden de compra', type: 'error');
            return;
        }

        $compra = Compra::with([
            'detalleCompra.producto.unidad',
            'detalleCompra.estadoDetalleCompra',
            'proveedor'
        ])->where('codigo', trim($this->ordenCompra))->first();

        if (!$compra) {
            $this->dispatch('notify', title: 'Error', description: 'No se encontró la orden de compra: ' . $this->ordenCompra, type: 'error');
            return;
        }

        if ($compra->estadoCompra?->nombre_estado_compra === "pendiente") {
            $this->dispatch('notify', title: 'Error', description: 'La orden de compra no ha sido aprobada', type: 'error');
            $this->ordenCompra = "";
            return;
        }

        if ($compra->estadoCompra?->nombre_estado_compra === "recibido") {
            $this->dispatch('notify', title: 'Info', description: 'La orden de compra ya ha sido recibida', type: 'success');
            $this->ordenCompra = "";
            return;
        }

        if ($compra->estadoCompra?->nombre_estado_compra === "cancelado") {
            $this->dispatch('notify', title: 'Error', description: 'La orden se encuentra cancelada', type: 'error');
            $this->ordenCompra = "";
            return;
        }


        if (!$compra) {
            $this->dispatch('notify', title: 'Error', description: 'No se encontró la orden de compra: ' . $this->ordenCompra, type: 'error');
            return;
        }

        $this->proveedorOC = $compra->proveedor;

        $this->productosOC = $compra->detalleCompra
            ->map(function ($detalle) {
                // Sumar todos los movimientos de entrada asociados
                $cantidadRecibida = InventarioMovimiento::where('id_tipo_movimiento', TipoMovimiento::where('nombre_tipo_movimiento', 'entrada')->first()->id_tipo_movimiento)
                    ->where('id_movimiento_asociado', $detalle->id_detalle_compra)
                    ->sum('cantidad_movimiento');


                // Cantidad pendiente
                $cantidadPendiente = $detalle->cantidad - $cantidadRecibida;

                return [
                    'id_producto' => $detalle->producto->id_producto,
                    'id_detalle_compra' => $detalle->id_detalle_compra,
                    'nombre' => $detalle->producto->nombre_producto . ' (' . $detalle->producto->unidad?->nombre_unidad . ')',
                    'precio_compra' => $detalle->precio_unitario,
                    'cantidad' => max($cantidadPendiente, 0), // evita números negativos
                    'codigo_barras' => $detalle->producto->codigo_barras,
                    'estado' => $cantidadPendiente <= 0 ? 'recibido' : 'pendiente',
                ];
            })->values();
    }

    public function updatedIdProducto($value)
    {
        $this->productoSeleccionado = collect($this->productosOC)
            ->firstWhere('id_detalle_compra', $value);

        if ($this->productoSeleccionado) {
            $this->lote['precio_compra'] = $this->productoSeleccionado['precio_compra'];
            $this->lote['cantidad_total'] = $this->productoSeleccionado['cantidad'];
            $this->lote['id_detalle_compra'] = $this->productoSeleccionado['id_detalle_compra'];
        } else {
            $this->lote['precio_compra'] = '';
            $this->lote['cantidad_total'] = '';
        }
    }

    public function generarCodigoLote()
    {
        do {
            $codigo = "LT." . $this->productoSeleccionado['codigo_barras'] . "-" . random_int(100, 999);
        } while (Lotes::where('codigo_lote', $codigo)->exists());

        $this->lote['codigo_lote'] = $codigo;
    }

    public function registrar()
    {
        $this->validate([
            "id_producto"             => "required|exists:detalle_compras,id_detalle_compra",
            'lote.cantidad_total'     => [
                'required',
                'numeric',
                'min:0.01',
                'max:999999999.99',
                function ($attribute, $value, $fail) {
                    if ($this->productoSeleccionado && $value > $this->productoSeleccionado['cantidad']) {
                        $fail('La cantidad recibida no puede ser mayor a la cantidad de la orden de compra (' . $this->productoSeleccionado['cantidad'] . ').');
                    }
                },
            ],
            'lote.fecha_recepcion'    => 'required|date|before_or_equal:today',
            'lote.fecha_vencimiento' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) {
                    if ($value && $this->lote['fecha_recepcion'] && $value <= $this->lote['fecha_recepcion']) {
                        $fail('La fecha de vencimiento debe ser posterior a la fecha de recepción.');
                    }
                },
            ],
            "lote.observacion"        => "max:1000",
            'lote.precio_compra'      => 'required|numeric|min:0.01',
        ], [
            'lote.cantidad_total.required' => 'Debe ingresar la cantidad.',
            'lote.cantidad_total.numeric' => 'La cantidad debe ser un número.',
            'lote.cantidad_total.min' => 'La cantidad debe ser mayor a 0.',
            'lote.cantidad_total.max' => 'La cantidad ingresada es demasiado grande.',
        ]);



        try {
            DB::transaction(function () {
                $this->generarCodigoLote();

                $fechaVencimiento = $this->lote['fecha_vencimiento'] ?: null;

                $lote = Lotes::create([
                    "id_producto"       => $this->productoSeleccionado['id_producto'],
                    "cantidad_total"    => $this->lote['cantidad_total'],
                    "precio_compra"     => $this->lote['precio_compra'],
                    "fecha_recepcion"   => $this->lote['fecha_recepcion'],
                    "fecha_vencimiento" => $fechaVencimiento,
                    "estado"            => "activo",
                    "observacion"       => $this->lote['observacion'],
                    "codigo_lote"       => $this->lote['codigo_lote'],
                    "cantidad_almacenada" => $this->ubicacion === 'almacen' ? $this->lote['cantidad_total'] : 0,
                    "cantidad_mostrada"  => $this->ubicacion === 'mostrador' ? $this->lote['cantidad_total'] : 0,
                ]);
                // Obtener el trabajador del usuario autenticado
                $trabajador = auth()->user()->persona->trabajador;

                if (!$trabajador) {
                    throw new \Exception("El usuario autenticado no tiene un trabajador asociado");
                }
                $tipoUbicacion = TipoUbicacion::where('nombre_tipo_ubicacion', $this->ubicacion)->first();

                if (!$tipoUbicacion) {
                    throw new \Exception("Tipo de ubicación no encontrado: " . $this->ubicacion);
                }
                $tipoEntrada = TipoMovimiento::where('nombre_tipo_movimiento', 'entrada')->first();


                InventarioMovimiento::create([
                    "cantidad_movimiento"       => $this->lote['cantidad_total'],
                    "stock_resultante"          => $lote->cantidad_total,
                    "fecha_movimiento"          => now(),
                    "fecha_registro"            => now(),
                    "id_lote"                   => $lote->id_lote,
                    "id_trabajador"             => $trabajador->id_trabajador,
                    "id_tipo_movimiento"        => TipoMovimiento::where("nombre_tipo_movimiento", "entrada")->first()->id_tipo_movimiento,
                    "id_tipo_ubicacion"         => $tipoUbicacion->id_tipo_ubicacion, // <--- Aquí va
                    "tipo_movimiento_asociado"  => DetalleCompra::class,
                    "id_movimiento_asociado"    => $this->lote['id_detalle_compra'],
                ]);


                $detalle = DetalleCompra::find($this->lote['id_detalle_compra']);

                // Cantidad total pedida en la OC
                $cantidadOC = $detalle->cantidad;

                $tipoEntrada = TipoMovimiento::where("nombre_tipo_movimiento", "entrada")->first();

                $cantidadRecibida = InventarioMovimiento::where('id_tipo_movimiento', $tipoEntrada->id_tipo_movimiento)
                    ->where('id_movimiento_asociado', $detalle->id_detalle_compra)
                    ->sum('cantidad_movimiento');

                // Cantidad nueva que vamos a registrar
                $cantidadNueva = $this->lote['cantidad_total'];

                // Solo cambiar a "recibido" si se ha alcanzado o superado la cantidad de la OC
                if ($cantidadRecibida + $cantidadNueva >= $cantidadOC) {
                    $estadoRecibido = EstadoDetalleCompra::where('nombre_estado_detalle_compra', 'recibido')->first();
                    $detalle->id_estado_detalle_compra = $estadoRecibido->id_estado_detalle_compra;
                    $detalle->save();
                }


                // Verificar si todos los detalles de la orden de compra están recibidos
                $ordenCompra = $detalle->compra;
                $pendientes = $ordenCompra->detalleCompra()
                    ->whereHas('estadoDetalleCompra', function ($q) {
                        $q->where('nombre_estado_detalle_compra', 'pendiente');
                    })->count();

                if ($pendientes === 0) {
                    $estadoRecibido = EstadoCompras::where('nombre_estado_compra', 'recibido')->first();
                    $ordenCompra->id_estado_compra = $estadoRecibido->id_estado_compra;
                    $ordenCompra->save();
                }
            });

            $this->dispatch('notify', title: 'Success', description: 'Entrada registrada con éxito. Código de lote: ' . $this->lote['codigo_lote'], type: 'success');
            $this->resetForm();
            $this->dispatch('entradasUpdated');
        } catch (Exception $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error al registrar la entrada: ' . $e->getMessage(), type: 'error');
            Log::error('Error al registrar entrada', ['error' => $e->getMessage()]);
        }
    }

    public function render()
    {

        return view('livewire.inventario.entradas');
    }

    public function resetForm()
    {
        $this->ordenCompra = "";
        $this->id_producto = null;
        $this->productoSeleccionado = null;
        $this->ubicacion = "";
        $this->lote = [
            "cantidad_total" => "",
            "cantidad_mostrada" => "",
            "cantidad_almacenada" => "",
            "observacion" => "",
            "fecha_recepcion" => "",
            "fecha_vencimiento" => "",
            "id_producto" => "",
            "precio_compra" => "",
            "codigo_lote" => "",
        ];

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

        // Sumar los lotes activos del producto
        $lotes = Lotes::where('id_producto', $this->productoSeleccionado['id_producto'])
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
        $this->selectedEntrada = InventarioMovimiento::with(['lote.producto', 'trabajador.persona.user'])
            ->find($rowId);

        $this->showModalDetalle = true;
    }
}
