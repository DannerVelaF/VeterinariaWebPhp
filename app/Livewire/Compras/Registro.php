<?php

namespace App\Livewire\Compras;

use App\Exports\CompraConDetalleExport;
use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\EstadoCompras;
use App\Models\EstadoDetalleCompra;
use App\Models\Producto;
use App\Models\Proveedor;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class Registro extends Component
{
    public $IGV = 0.18;
    public $codigoOrden = '';
    public $productos = [];
    public $proveedores = [];
    public $proveedorSeleccionado = '';
    public $cantOrdenesPendientes = 0;
    public $cantOrdenesAprobadas = 0;
    public $cantOrdenesRecibidas = 0;
    public $precioCompraTotal = 0;
    public $detalleCompra = [];
    public $compra = [
        'numero_factura' => '',
        'fecha_compra' => '',
        'observacion' => ''
    ];

    public bool $showModal = false;
    public bool $showModalDetalle = false;

    public ?Compra $compraSeleccionada = null;
    public $busquedaProveedor = '';
    public $proveedoresFiltrados = [];
    public $mostrarListaProveedores = false;

    // VARIABLES PARA EL BUSCADOR DE PRODUCTOS (Arrays por índice)
    public $busquedaProductos = [];
    public $productosFiltrados = [];
    public $mostrarListaProductos = [];

    public function mount()
    {
        $this->generarCodigoOrden();
        $this->proveedores = Proveedor::where('estado', 'activo')->get();
        $this->detalleCompra = [
            ['id_producto' => '', 'cantidad' => 0, 'precio_unitario' => 0]
        ];
        $this->compra = [
            'numero_factura' => '',
            'fecha_compra' => now()->format('Y-m-d'),
            'observacion' => ''
        ];

        $pendiente = EstadoCompras::where('nombre_estado_compra', 'pendiente')->first();
        $aprobado = EstadoCompras::where('nombre_estado_compra', 'aprobado')->first();
        $recibido = EstadoCompras::where('nombre_estado_compra', 'recibido')->first();
        $cancelado = EstadoCompras::where('nombre_estado_compra', 'cancelado')->first();

        $this->cantOrdenesPendientes = Compra::where("id_estado_compra", $pendiente->id_estado_compra)->count();
        $this->cantOrdenesAprobadas = Compra::where("id_estado_compra", $aprobado->id_estado_compra)->count();
        $this->cantOrdenesRecibidas = Compra::where("id_estado_compra", $recibido->id_estado_compra)->count();

        $this->precioCompraTotal = Compra::where("id_estado_compra", $recibido->id_estado_compra)->sum('total');
        $this->busquedaProductos[0] = '';
        $this->mostrarListaProductos[0] = false;
        $this->productosFiltrados[0] = [];
    }

    public function updatedBusquedaProveedor()
    {
        $this->mostrarListaProveedores = true;

        if (!empty($this->busquedaProveedor)) {
            $this->proveedoresFiltrados = Proveedor::where('estado', 'activo')
                ->where('nombre_proveedor', 'like', '%' . $this->busquedaProveedor . '%')
                ->orWhere('ruc', 'like', '%' . $this->busquedaProveedor . '%')
                ->take(10)
                ->get();
        } else {
            $this->proveedoresFiltrados = [];
        }
    }

    public function updated($propertyName)
    {
        // Detectar cambio en búsqueda de productos: busquedaProductos.0
        if (str_starts_with($propertyName, 'busquedaProductos.')) {
            $parts = explode('.', $propertyName);
            $index = $parts[1];
            $this->buscarProducto($index);
        }
    }

    public function buscarProducto($index)
    {
        $termino = $this->busquedaProductos[$index] ?? '';
        $this->mostrarListaProductos[$index] = true;

        if (!$this->proveedorSeleccionado) {
            $this->productosFiltrados[$index] = [];
            return;
        }

        if (!empty($termino)) {
            // Buscar productos DEL proveedor seleccionado
            $this->productosFiltrados[$index] = Producto::with(['unidad'])
                ->whereHas('proveedores', function ($query) {
                    $query->where('proveedores.id_proveedor', $this->proveedorSeleccionado);
                })
                ->where('estado', 'activo')
                ->where(function ($q) use ($termino) {
                    $q->where('nombre_producto', 'like', '%' . $termino . '%')
                        ->orWhere('codigo_barras', 'like', '%' . $termino . '%');
                })
                ->take(15) // Limitar resultados
                ->get();
        } else {
            // Si está vacío, mostrar los primeros 15 del proveedor
            $this->productosFiltrados[$index] = Producto::with(['unidad'])
                ->whereHas('proveedores', function ($query) {
                    $query->where('proveedores.id_proveedor', $this->proveedorSeleccionado);
                })
                ->where('estado', 'activo')
                ->take(15)
                ->get();
        }
    }

    public function seleccionarProducto($index, $id, $nombre)
    {
        $this->detalleCompra[$index]['id_producto'] = $id;
        $this->busquedaProductos[$index] = $nombre;
        $this->mostrarListaProductos[$index] = false;
    }

    // Asegurarse de inicializar arrays al agregar fila
    public function agregarDetalle()
    {
        if (count($this->detalleCompra) < 50) {
            $nuevoIndex = count($this->detalleCompra);
            $this->detalleCompra[] = ['id_producto' => '', 'cantidad' => 1, 'precio_unitario' => 0];

            // Inicializar variables de búsqueda para la nueva fila
            $this->busquedaProductos[$nuevoIndex] = '';
            $this->mostrarListaProductos[$nuevoIndex] = false;
            $this->productosFiltrados[$nuevoIndex] = [];
        }
    }

    public function eliminarDetalle($index)
    {
        unset($this->detalleCompra[$index]);
        unset($this->busquedaProductos[$index]);
        unset($this->mostrarListaProductos[$index]);
        unset($this->productosFiltrados[$index]);

        // Reindexar todo para evitar huecos en los índices
        $this->detalleCompra = array_values($this->detalleCompra);
        $this->busquedaProductos = array_values($this->busquedaProductos);
        $this->mostrarListaProductos = array_values($this->mostrarListaProductos);
        $this->productosFiltrados = array_values($this->productosFiltrados);
    }

    public function seleccionarProveedor($id, $nombre)
    {
        $this->proveedorSeleccionado = $id;
        $this->busquedaProveedor = $nombre; // Poner el nombre en el input
        $this->mostrarListaProveedores = false;

        // Limpiar productos al cambiar proveedor
        $this->resetDetalleProductos();
    }


    private function resetDetalleProductos()
    {
        // Reiniciar el detalle a una fila vacía
        $this->detalleCompra = [
            ['id_producto' => '', 'cantidad' => 0, 'precio_unitario' => 0]
        ];
        $this->busquedaProductos = [''];
        $this->productosFiltrados = [[]];
        $this->mostrarListaProductos = [false];
    }

    public function generarCodigoOrden()
    {
        $año = Carbon::now()->format('Y');
        $mes = Carbon::now()->format('m');

        // Buscar el último correlativo del mes
        $ultimo = Compra::whereYear('fecha_registro', $año)
            ->whereMonth('fecha_registro', $mes)
            ->orderBy('id_compra', 'desc')
            ->first();

        $correlativo = 1;

        if ($ultimo) {
            // Extraer los últimos 5 dígitos
            $ultimoCodigo = substr($ultimo->codigo, -5);
            $correlativo = intval($ultimoCodigo) + 1;
        }

        $this->codigoOrden = sprintf("OC-%s-%s-%05d", $año, $mes, $correlativo);
    }

    public function updatedProveedorSeleccionado($value)
    {
        $this->productos = [];

        if (!empty($value)) {
            // ✅ CORRECCIÓN: Usar la relación muchos a muchos
            $this->productos = Producto::with(['proveedores', 'unidad'])
                ->whereHas('proveedores', function ($query) use ($value) {
                    $query->where('proveedores.id_proveedor', $value);
                })
                ->where('estado', 'activo')
                ->get();

        }
    }

    public function aprobarCompra()
    {
        try {
            DB::transaction(function () {

                $estadoOrdenAprobado = EstadoCompras::where('nombre_estado_compra', 'aprobado')->first();

                $compra = $this->compraSeleccionada;
                $compra->id_estado_compra = $estadoOrdenAprobado->id_estado_compra;
                $compra->id_usuario_aprobador = auth()->user()->id_usuario;
                $compra->save();
            });

            $this->dispatch('notify', title: 'Success', description: 'Compra aprobada correctamente.', type: 'success');
            $this->reset(["compraSeleccionada", "detalleCompra", "proveedorSeleccionado", "codigoOrden", "proveedores"]);
            $this->showModalDetalle = false;
            $this->dispatch('comprasUpdated');
        } catch (\Exception $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error al aprobar la compra: ' . $e->getMessage(), type: 'error');
            Log::error('Error al registrar la compra', ['error' => $e->getMessage()]);
        }
    }

    public function rechazarCompra()
    {
        try {


            DB::transaction(function () {
                $estadoOrdenCancelado = EstadoCompras::where('nombre_estado_compra', 'cancelado')->first();

                $compra = $this->compraSeleccionada;
                $compra->id_estado_compra = $estadoOrdenCancelado->id_estado_compra;
                $compra->save();
            });

            $this->dispatch('notify', title: 'Success', description: '❌ Compra rechazada correctamente ❌', type: 'success');
            $this->reset(["compraSeleccionada", "detalleCompra", "proveedorSeleccionado", "codigoOrden", "proveedores"]);
            $this->showModalDetalle = false;
            $this->dispatch('comprasUpdated');
        } catch (\Exception $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error al rechazar la compra: ' . $e->getMessage(), type: 'error');
            Log::error('Error al rechazar la compra', ['error' => $e->getMessage()]);
        }
    }

    public function guardar()
    {
        $this->validate(
            [
                "proveedorSeleccionado" => "required|exists:proveedores,id_proveedor",
                "compra.numero_factura" => "required|string|max:255",
                "compra.fecha_compra" => "required|date|before_or_equal:today",
                "compra.observacion" => "max:1000",
                "detalleCompra.*.id_producto" => "required|exists:productos,id_producto",
                "detalleCompra.*.cantidad" => "required|numeric|min:0.01",
                "detalleCompra.*.precio_unitario" => "required|numeric|min:0.01",
            ],
            [
                "proveedorSeleccionado.required" => "El proveedor es obligatorio.",
                "proveedorSeleccionado.exists" => "El proveedor seleccionado no es válido.",
                "compra.numero_factura.required" => "El número de factura es obligatorio.",
                "compra.numero_factura.string" => "El número de factura debe ser una cadena de texto.",
                "compra.numero_factura.max" => "El número de factura no debe exceder los 255 caracteres.",
                "compra.fecha_compra.required" => "La fecha de compra es obligatoria.",
                "compra.fecha_compra.date" => "La fecha de compra no es una fecha válida.",
                "compra.fecha_compra.before_or_equal" => "La fecha de compra no puede ser futura.",
                "compra.observacion.max" => "La observación no debe exceder los 1000 caracteres.",
                "detalleCompra.*.id_producto.required" => "El producto es obligatorio en cada detalle.",
                "detalleCompra.*.id_producto.exists" => "El producto seleccionado no es válido en uno de los detalles.",
                "detalleCompra.*.cantidad.required" => "La cantidad es obligatoria en cada detalle.",
                "detalleCompra.*.cantidad.numeric" => "La cantidad debe ser un número en cada detalle.",
                "detalleCompra.*.cantidad.min" => "La cantidad debe ser al menos 0.01 en cada detalle.",
                "detalleCompra.*.precio_unitario.required" => "El precio unitario es obligatorio en cada detalle.",
                "detalleCompra.*.precio_unitario.numeric" => "El precio unitario debe ser un número en cada detalle.",
                "detalleCompra.*.precio_unitario.min" => "El precio unitario debe ser al menos 0.01 en cada detalle.",
            ]
        );
        try {
            DB::transaction(function () {
                $total = 0;
                $cantidad_total = 0;
                foreach ($this->detalleCompra as $detalle) {
                    $total += $detalle['precio_unitario'] * $detalle['cantidad'];
                    $cantidad_total += $detalle['cantidad'];
                }

                $estadoPendiente = EstadoDetalleCompra::where('nombre_estado_detalle_compra', 'pendiente')->first();
                $estadoOrdenPendiente = EstadoCompras::where('nombre_estado_compra', 'pendiente')->first();
                $compra = Compra::create([
                    "id_proveedor" => $this->proveedorSeleccionado,
                    "id_trabajador" => auth()->user()->persona->trabajador->id_trabajador,
                    "codigo" => $this->codigoOrden,
                    "numero_factura" => $this->compra['numero_factura'],
                    "fecha_compra" => $this->compra['fecha_compra'],
                    "cantidad_total" => $cantidad_total,
                    "fecha_actualizacion" => now(),
                    "id_estado_compra" => $estadoOrdenPendiente->id_estado_compra,
                    'total' => $total,
                    "observacion" => $this->compra['observacion'],
                    "fecha_registro" => now(),
                ]);

                foreach ($this->detalleCompra as $index => $detalle) {
                    DetalleCompra::create([
                        "id_compra" => $compra->id_compra,
                        "id_producto" => $detalle['id_producto'],
                        "cantidad" => $detalle['cantidad'],
                        "precio_unitario" => $detalle['precio_unitario'],
                        "sub_total" => $detalle['precio_unitario'] * $detalle['cantidad'],
                        "id_estado_detalle_compra" => $estadoPendiente->id_estado_detalle_compra,
                    ]);
                }

                $this->reset(["compra", "detalleCompra", "proveedorSeleccionado", "codigoOrden", "proveedores"]);
                $this->detalleCompra = [
                    ['id_producto' => '', 'cantidad' => 0, 'precio_unitario' => 0]
                ];
                $this->closeModal();
            });
            $this->mount();
            $this->dispatch('notify', title: 'Success', description: 'Orden de compra registrada correctamente ✅', type: 'success');
            $this->dispatch('comprasUpdated');
        } catch (\Exception $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error al registrar el proveedor: ' . $e->getMessage(), type: 'error');
            Log::error('Error al registrar la compra', ['error' => $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.compras.registro');
    }

    public function openModal(): void
    {
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->compra = [
            'numero_factura' => '',
            'fecha_compra' => '',
            'observacion' => ''
        ];

        $this->detalleCompra = [
            ['id_producto' => '', 'cantidad' => 0, 'precio_unitario' => 0]
        ];
        $this->busquedaProveedor = '';
        $this->proveedorSeleccionado = '';
        $this->resetDetalleProductos();
    }


    #[\Livewire\Attributes\On('show-modal')]
    public function showModal(int $rowId): void
    {
        $this->compraSeleccionada = Compra::with(['detalleCompra', "proveedor", 'trabajador.persona.user'])
            ->find($rowId);

        $this->showModalDetalle = true;
    }

    #[\Livewire\Attributes\On('rechazar-compra')]
    public function rechazarComprafn(int $rowId): void
    {
        $this->compraSeleccionada = Compra::find($rowId);

        if ($this->compraSeleccionada) {
            $this->rechazarCompra();
        }
    }

    #[\Livewire\Attributes\On('aprobar-compra')]
    public function aprobarComprafn(int $rowId): void
    {
        $this->compraSeleccionada = Compra::find($rowId);

        if ($this->compraSeleccionada) {
            $this->aprobarCompra();
        }
    }

    public function exportarExcel()
    {
        return Excel::download(new CompraConDetalleExport, 'reporteCompras.xlsx');
    }

    public function exportarPdf()
    {
        $compras = Compra::with(['proveedor', 'detalleCompra.producto', 'estadoCompra'])->get();

        $pdf = Pdf::loadView('exports.compras_pdf', compact('compras'))
            ->setPaper('a4', 'landscape'); // opcional

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'reporte_compras.pdf');
    }

    public function exportarPdfOrdenCompra()
    {
        $compra = Compra::with(['proveedor', 'detalleCompra.producto', 'estadoCompra'])->find($this->compraSeleccionada->id_compra);
        $IGV = $this->IGV;

        $pdf = Pdf::loadView('exports.ordenCompra_pdf', compact('compra', 'IGV'))
            ->setPaper('a4', 'portrait'); // opcional

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'orden_compra.pdf');
    }
}
