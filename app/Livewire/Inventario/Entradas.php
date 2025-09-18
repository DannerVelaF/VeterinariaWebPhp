<?php

namespace App\Livewire\Inventario;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\InventarioMovimiento;
use App\Models\Lotes;
use App\Models\Producto;
use App\Models\Proveedor;
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
    protected $updatesQueryString = ['producto_id'];
    public $productos = [];
    public $proveedores = [];
    public $producto_id = null;
    public $observacion = "";
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
    public $top10entradas = [];

    public bool $showModal = false;

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
        $this->top10entradas = InventarioMovimiento::where("tipo_movimiento", "entrada")->orderBy("fecha_movimiento", "desc")->limit(10)->get();
    }

    public function buscarOrdenCompra()
    {
        $this->productosOC = [];
        $this->proveedorOC = null;
        $this->producto_id = null;
        $this->productoSeleccionado = null;
        $this->lote['cantidad_total'] = '';
        $this->lote['precio_compra'] = '';


        if (!$this->ordenCompra) {
            session()->flash('error', 'No se ha introducido el código de orden de compra');
            return;
        }

        $compra = Compra::with('detalleCompra.producto.unidad', 'proveedor')
            ->where('codigo', trim($this->ordenCompra))
            ->first();

        if (!$compra) {
            session()->flash('error', 'No se encontró la orden de compra: ' . $this->ordenCompra);
            return;
        }

        if ($compra->estado == "pendiente") {
            session()->flash('error', 'La orden de compra no ha sido aprobada');
            $this->ordenCompra = "";
        }
        if ($compra->estado == "recibido") {
            session()->flash('error', 'La orden de compra ya ha sido recibida');
            $this->ordenCompra = "";
            return;
        } elseif ($compra->estado == "cancelado") {
            session()->flash('error', 'La orden se encuentra cancelada');
            $this->ordenCompra = "";
            return;
        }

        if (!$compra) {
            session()->flash('error', 'No se encontró la orden de compra: ' . $this->ordenCompra);
            return;
        }

        $this->proveedorOC = $compra->proveedor;

        $this->productosOC = $compra->detalleCompra
            ->where('estado', 'pendiente')
            ->map(function ($detalle) {
                return [
                    'id' => $detalle->producto->id,
                    'detalle_compra_id' => $detalle->id,
                    'nombre' => $detalle->producto->nombre_producto . ' (' . $detalle->producto->unidad->nombre . ')',
                    'precio_compra' => $detalle->precio_unitario,
                    'cantidad' => $detalle->cantidad,
                    'codigo_barras' => $detalle->producto->codigo_barras,
                    'estado' => $detalle->estado,
                ];
            })->values();
    }

    public function updatedProductoId($value)
    {
        $this->productoSeleccionado = collect($this->productosOC)
            ->firstWhere('detalle_compra_id', $value);

        if ($this->productoSeleccionado) {
            $this->lote['precio_compra'] = $this->productoSeleccionado['precio_compra'];
            $this->lote['cantidad_total'] = $this->productoSeleccionado['cantidad'];
            $this->lote['detalle_compra_id'] = $this->productoSeleccionado['detalle_compra_id'];
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
            "producto_id"             => "required|exists:detalle_compras,id",
            'lote.cantidad_total'     => 'required|numeric|min:0.01|max:999999999.99',
            'lote.fecha_recepcion'    => 'required|date',
            'lote.fecha_vencimiento' => 'nullable|date|after:lote.fecha_recepcion',
            "lote.observacion"        => "max:1000",
            'lote.precio_compra'      => 'required|numeric|min:0.01',
        ]);

        try {
            DB::transaction(function () {
                $this->generarCodigoLote();

                $fechaVencimiento = $this->lote['fecha_vencimiento'] ?: null;

                $lote = Lotes::create([
                    "producto_id"       => $this->productoSeleccionado['id'], // <-- CORRECTO
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
                InventarioMovimiento::create([
                    "tipo_movimiento"   => "entrada",
                    "cantidad_movimiento"          => $this->lote['cantidad_total'],
                    "stock_resultante"  => $lote->cantidad_total,
                    "fecha_movimiento"  => now(),
                    "fecha_registro"    => now(),
                    "id_lote"           => $lote->id,
                    "id_trabajador"     => $trabajador->id,
                    "ubicacion"         => $this->ubicacion,
                    "movimentable_type" => DetalleCompra::class,
                    "movimentable_id"    => $this->lote['detalle_compra_id'],
                ]);

                $detalle = DetalleCompra::find($this->lote['detalle_compra_id']);
                $detalle->estado = 'recibido';
                $detalle->save();

                // Verificar si todos los detalles de la orden de compra están recibidos
                $ordenCompra = $detalle->compra;
                $todosRecibidos = $ordenCompra->detalleCompra()->where('estado', 'pendiente')->count() === 0;
                if ($todosRecibidos) {
                    $ordenCompra->estado = 'recibido';
                    $ordenCompra->save();
                }
            });

            session()->flash('success', '✅ Entrada registrada con éxito. Código de lote: ' . $this->lote['codigo_lote']);
            $this->resetForm();
            $this->dispatch('entradaRegistrada');
        } catch (Exception $e) {
            session()->flash('error', 'Error al registrar la entrada: ' . $e->getMessage());
            Log::error('Error al registrar entrada', ['error' => $e->getMessage()]);
        }
    }

    public function render()
    {
        $entradas = InventarioMovimiento::where("tipo_movimiento", "entrada")
            ->orderBy("fecha_movimiento", "desc")
            ->paginate(10); // <- Paginación aquí

        return view('livewire.inventario.entradas', [
            'entradas' => $entradas
        ]);
    }

    public function resetForm()
    {
        $this->ordenCompra = "";
        $this->producto_id = null;
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
        $this->selectedEntrada = InventarioMovimiento::with(['lote.producto', 'trabajador.persona.user'])
            ->find($rowId);

        $this->showModalDetalle = true;
    }
}
