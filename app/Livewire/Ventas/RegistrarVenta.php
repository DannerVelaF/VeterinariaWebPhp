<?php

namespace App\Livewire\Ventas;

use App\Models\Ventas;
use App\Models\DetalleVentas;
use App\Models\EstadoVentas;
use App\Models\Producto;
use App\Models\Servicio;
use App\Models\Clientes;
use App\Models\CategoriaProducto as Categoriaproducto;
use App\Models\CategoriaServicio as CategoriaServicio;
use App\Models\Proveedor;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

use App\Exports\VentaConDetalleExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class RegistrarVenta extends Component
{
    public $IGV = 0.18;
    public $codigoVenta = '';
    public $productos = [];
    public $servicios = [];
    public $clientes = [];
    public $clienteSeleccionado = '';
    
    // Filtros
    public $categoriasProductos = [];
    public $categoriasServicios = [];
    public $proveedores = [];
    public $categoriaProductoSeleccionada = '';
    public $categoriaServicioSeleccionada = '';
    public $proveedorSeleccionado = '';
    
    // Estadísticas
    public $cantVentasPendientes = 0;
    public $cantVentasCompletadas = 0;
    public $cantVentasCanceladas = 0;
    public $totalVentasCompletadas = 0;

    public $detalleVenta = [];
    public $venta = [
        'fecha_venta' => '',
        'observacion' => '',
        'descuento' => 0
    ];

    // Cálculos en tiempo real
    public $subtotal = 0;
    public $totalImpuesto = 0;
    public $totalGeneral = 0;

    public bool $showModal = false;
    public bool $showModalDetalle = false;
    public ?Ventas $ventaSeleccionada = null;

    public function mount()
    {
        $this->generarCodigoVenta();
        $this->clientes = Clientes::all();
        $this->cargarFiltros();
        $this->cargarProductosYServicios();
        $this->inicializarDetalleVenta();
        $this->calcularEstadisticas();
    }

    public function cargarFiltros()
    {
        $this->categoriasProductos = Categoriaproducto::where('estado', 'activo')->get();
        $this->categoriasServicios = CategoriaServicio::where('estado', 'activo')->get();
        $this->proveedores = Proveedor::where('estado', 'activo')->get();


    }

    public function cargarProductosYServicios()
    {
        // Cargar productos activos con stock
        $queryProductos = Producto::where('estado', 'activo');

        // Aplicar filtros a productos
        if ($this->categoriaProductoSeleccionada) {
            $queryProductos->where('id_categoria_producto', $this->categoriaProductoSeleccionada);
        }

        if ($this->proveedorSeleccionado) {
            $queryProductos->where('id_proveedor', $this->proveedorSeleccionado);
        }

        $this->productos = $queryProductos->get()
            ->filter(function($producto) {
                return $producto->stock_actual > 0;
            })
            ->values();

        // Cargar servicios activos
        $queryServicios = Servicio::where('estado', 'activo');

        // Aplicar filtros a servicios si es necesario
        if ($this->categoriaServicioSeleccionada) {
            $queryServicios->where('id_categoria_servicio', $this->categoriaServicioSeleccionada);
        }

        $this->servicios = $queryServicios->get();
    }

    public function updatedCategoriaProductoSeleccionada()
    {
        $this->cargarProductosYServicios();
        $this->actualizarSelectsProductos();
    }

    public function updatedCategoriaServicioSeleccionada()
    {
        $this->cargarProductosYServicios();
        $this->actualizarSelectsServicios();
    }

    public function updatedProveedorSeleccionado()
    {
        $this->cargarProductosYServicios();
        $this->actualizarSelectsProductos();
    }

    public function actualizarSelectsProductos()
    {
        // Si un producto seleccionado ya no está disponible, limpiar el detalle
        foreach ($this->detalleVenta as $index => $detalle) {
            if ($detalle['tipo_item'] === 'producto' && $detalle['id_item']) {
                $producto = Producto::find($detalle['id_item']);
                if (!$producto || $producto->stock_actual <= 0) {
                    $this->detalleVenta[$index]['id_item'] = '';
                    $this->detalleVenta[$index]['precio_unitario'] = 0;
                    $this->detalleVenta[$index]['cantidad'] = 1;
                }
            }
        }
        $this->calcularTotales();
    }

    public function actualizarSelectsServicios()
    {
        // Si un servicio seleccionado ya no está disponible, limpiar el detalle
        foreach ($this->detalleVenta as $index => $detalle) {
            if ($detalle['tipo_item'] === 'servicio' && $detalle['id_item']) {
                $servicio = Servicio::find($detalle['id_item']);
                if (!$servicio) {
                    $this->detalleVenta[$index]['id_item'] = '';
                    $this->detalleVenta[$index]['precio_unitario'] = 0;
                    $this->detalleVenta[$index]['cantidad'] = 1;
                }
            }
        }
        $this->calcularTotales();
    }

    public function inicializarDetalleVenta()
    {
        $this->detalleVenta = [
            ['tipo_item' => 'producto', 'id_item' => '', 'cantidad' => 1, 'precio_unitario' => 0]
        ];
        
        $this->venta = [
            'fecha_venta' => now()->format('Y-m-d'),
            'observacion' => '',
            'descuento' => 0
        ];
    }

    public function generarCodigoVenta()
    {
        $año = Carbon::now()->format('Y');
        $mes = Carbon::now()->format('m');

        $ultimoCodigo = Ventas::where('codigo', 'like', "VTA-{$año}-{$mes}-%")
            ->orderBy('codigo', 'desc')
            ->first();

        $correlativo = 1;
        if ($ultimoCodigo) {
            $partes = explode('-', $ultimoCodigo->codigo);
            $ultimoCorrelativo = end($partes);
            $correlativo = intval($ultimoCorrelativo) + 1;
        }

        $this->codigoVenta = sprintf("VTA-%s-%s-%05d", $año, $mes, $correlativo);
    }

    public function calcularEstadisticas()
    {
        $pendiente = EstadoVentas::where('nombre_estado_venta_fisica', 'pendiente')->first();
        $completado = EstadoVentas::where('nombre_estado_venta_fisica', 'completado')->first();
        $cancelado = EstadoVentas::where('nombre_estado_venta_fisica', 'cancelado')->first();

        if ($pendiente) {
            $this->cantVentasPendientes = Ventas::where("id_estado_venta", $pendiente->id_estado_venta_fisica)->count();
        }
        if ($completado) {
            $this->cantVentasCompletadas = Ventas::where("id_estado_venta", $completado->id_estado_venta_fisica)->count();
            $this->totalVentasCompletadas = Ventas::where("id_estado_venta", $completado->id_estado_venta_fisica)->sum('total');
        }
        if ($cancelado) {
            $this->cantVentasCanceladas = Ventas::where("id_estado_venta", $cancelado->id_estado_venta_fisica)->count();
        }
    }

    public function updated($property)
    {
        // Recalcular totales cuando cambien los detalles o el descuento
        if (str_starts_with($property, 'detalleVenta') || $property === 'venta.descuento') {
            $this->calcularTotales();
        }
        
        // Cargar precio unitario cuando se seleccione un producto o servicio
        if (str_starts_with($property, 'detalleVenta')) {
            $parts = explode('.', $property);
            if (count($parts) === 3) {
                $index = $parts[1];
                $field = $parts[2];
                
                if ($field === 'tipo_item') {
                    // Cuando cambia el tipo, resetear los valores
                    $this->detalleVenta[$index]['id_item'] = '';
                    $this->detalleVenta[$index]['precio_unitario'] = 0;
                    if ($this->detalleVenta[$index]['tipo_item'] === 'servicio') {
                        $this->detalleVenta[$index]['cantidad'] = 1;
                    }
                } elseif ($field === 'id_item') {
                    $this->cargarPrecioUnitario($index);
                }
            }
        }
    }

    public function calcularTotales()
    {
        $this->subtotal = 0;

        foreach ($this->detalleVenta as $detalle) {
            if ($detalle['id_item'] && $detalle['cantidad'] > 0 && $detalle['precio_unitario'] > 0) {
                $this->subtotal += $detalle['precio_unitario'] * $detalle['cantidad'];
            }
        }

        $descuento = $this->venta['descuento'] ?? 0;
        $subtotalConDescuento = $this->subtotal - $descuento;
        $this->totalImpuesto = $subtotalConDescuento * $this->IGV;
        $this->totalGeneral = $subtotalConDescuento + $this->totalImpuesto;
    }

    public function cargarPrecioUnitario($index)
    {
        $detalle = $this->detalleVenta[$index];
        
        if ($detalle['tipo_item'] === 'producto' && $detalle['id_item']) {
            $producto = Producto::find($detalle['id_item']);
            if ($producto) {
                $this->detalleVenta[$index]['precio_unitario'] = $producto->precio_venta;
            }
        } elseif ($detalle['tipo_item'] === 'servicio' && $detalle['id_item']) {
            $servicio = Servicio::find($detalle['id_item']);
            if ($servicio) {
                // Para servicios, cargar el precio pero permitir edición
                $this->detalleVenta[$index]['precio_unitario'] = $servicio->precio;
            }
        }
        
        $this->calcularTotales();
    }

    public function agregarDetalle()
    {
        if (count($this->detalleVenta) < 50) {
            $this->detalleVenta[] = [
                'tipo_item' => 'producto', 
                'id_item' => '', 
                'cantidad' => 1, 
                'precio_unitario' => 0
            ];
        }
    }

    public function eliminarDetalle($index)
    {
        if (count($this->detalleVenta) > 1) {
            unset($this->detalleVenta[$index]);
            $this->detalleVenta = array_values($this->detalleVenta);
            $this->calcularTotales();
        }
    }

    public function guardar()
    {
        $this->validate([
            "clienteSeleccionado" => "required|exists:clientes,id_cliente",
            "venta.fecha_venta" => "required|date|before_or_equal:today",
            "venta.observacion" => "nullable|max:1000",
            "venta.descuento" => "nullable|numeric|min:0",
            "detalleVenta.*.id_item" => "required",
            "detalleVenta.*.cantidad" => "required|numeric|min:1",
            "detalleVenta.*.precio_unitario" => "required|numeric|min:0.01",
        ], [
            "clienteSeleccionado.required" => "El cliente es obligatorio.",
            "clienteSeleccionado.exists" => "El cliente seleccionado no es válido.",
            "venta.fecha_venta.required" => "La fecha de venta es obligatoria.",
            "venta.fecha_venta.date" => "La fecha de venta no es una fecha válida.",
            "venta.fecha_venta.before_or_equal" => "La fecha de venta no puede ser futura.",
            "venta.observacion.max" => "La observación no debe exceder los 1000 caracteres.",
            "venta.descuento.numeric" => "El descuento debe ser un número.",
            "venta.descuento.min" => "El descuento no puede ser negativo.",
            "detalleVenta.*.id_item.required" => "El producto o servicio es obligatorio.",
            "detalleVenta.*.cantidad.required" => "La cantidad es obligatoria.",
            "detalleVenta.*.cantidad.numeric" => "La cantidad debe ser un número.",
            "detalleVenta.*.cantidad.min" => "La cantidad debe ser al menos 1.",
            "detalleVenta.*.precio_unitario.required" => "El precio unitario es obligatorio.",
            "detalleVenta.*.precio_unitario.numeric" => "El precio unitario debe ser un número.",
            "detalleVenta.*.precio_unitario.min" => "El precio unitario debe ser al menos 0.01.",
        ]);

        // Validación adicional para stock de productos
        foreach ($this->detalleVenta as $index => $detalle) {
            if ($detalle['tipo_item'] === 'producto') {
                $producto = Producto::find($detalle['id_item']);
                if ($producto && $producto->stock_actual < $detalle['cantidad']) {
                    $this->addError("detalleVenta.{$index}.cantidad", 
                        "Stock insuficiente para {$producto->nombre_producto}. Stock disponible: {$producto->stock_actual}");
                    return;
                }
            }
        }

        try {
            DB::transaction(function () {
                $estadoVentaPendiente = EstadoVentas::where('nombre_estado_venta_fisica', 'pendiente')->first();

                // Crear la venta
                $venta = Ventas::create([
                    "id_cliente" => $this->clienteSeleccionado,
                    "codigo" => $this->codigoVenta,
                    "id_trabajador" => auth()->user()->persona->trabajador->id_trabajador,
                    "fecha_venta" => $this->venta['fecha_venta'],
                    "subtotal" => $this->subtotal,
                    "descuento" => $this->venta['descuento'] ?? 0,
                    "impuesto" => $this->totalImpuesto,
                    "total" => $this->totalGeneral,
                    "observacion" => $this->venta['observacion'],
                    "id_estado_venta" => $estadoVentaPendiente->id_estado_venta_fisica,
                    "fecha_registro" => now(),
                    "fecha_actualizacion" => now(),
                ]);

                // Crear detalles de venta
                foreach ($this->detalleVenta as $detalle) {
                    $detalleData = [
                        "id_venta" => $venta->id_venta,
                        "cantidad" => $detalle['cantidad'],
                        "precio_unitario" => $detalle['precio_unitario'],
                        "subtotal" => $detalle['precio_unitario'] * $detalle['cantidad'],
                        "tipo_item" => $detalle['tipo_item'],
                        "estado" => 'activo',
                        "fecha_registro" => now(),
                        "fecha_actualizacion" => now(),
                    ];

                    if ($detalle['tipo_item'] === 'producto') {
                        $detalleData['id_producto'] = $detalle['id_item'];
                        // Actualizar stock del producto
                        $producto = Producto::find($detalle['id_item']);
                        if ($producto) {
                            $producto->decrement('stock', $detalle['cantidad']);
                        }
                    } else {
                        $detalleData['id_servicio'] = $detalle['id_item'];
                    }

                    DetalleVentas::create($detalleData);
                }
            });

            $this->resetForm();
            $this->closeModal();
            $this->mount();
            
            $this->dispatch('notify', title: 'Success', description: 'Venta registrada correctamente ✅', type: 'success');
            $this->dispatch('ventasUpdated');
            
        } catch (\Exception $e) {
            Log::error('Error al registrar la venta', ['error' => $e->getMessage()]);
            $this->dispatch('notify', title: 'Error', description: 'Error al registrar la venta: ' . $e->getMessage(), type: 'error');
        }
    }

    public function completarVenta()
    {
        try {
            DB::transaction(function () {
                $estadoVentaCompletado = EstadoVentas::where('nombre_estado_venta_fisica', 'completado')->first();

                $venta = $this->ventaSeleccionada;
                $venta->id_estado_venta = $estadoVentaCompletado->id_estado_venta_fisica;
                $venta->save();
            });

            $this->dispatch('notify', title: 'Success', description: 'Venta completada correctamente ✅', type: 'success');
            $this->closeModalDetalle();
            $this->dispatch('ventasUpdated');
            
        } catch (\Exception $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error al completar la venta: ' . $e->getMessage(), type: 'error');
        }
    }

    public function cancelarVenta()
    {
        try {
            DB::transaction(function () {
                $estadoVentaCancelado = EstadoVentas::where('nombre_estado_venta_fisica', 'cancelado')->first();

                $venta = $this->ventaSeleccionada;
                $venta->id_estado_venta = $estadoVentaCancelado->id_estado_venta_fisica;
                $venta->save();

                // Revertir stock si hay productos
                foreach ($venta->detalleVentas as $detalle) {
                    $detalle->estado = 'cancelado';
                    $detalle->save();

                    if ($detalle->tipo_item === 'producto' && $detalle->producto) {
                        $detalle->producto->increment('stock', $detalle->cantidad);
                    }
                }
            });

            $this->dispatch('notify', title: 'Success', description: 'Venta cancelada correctamente ❌', type: 'success');
            $this->closeModalDetalle();
            $this->dispatch('ventasUpdated');
            
        } catch (\Exception $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error al cancelar la venta: ' . $e->getMessage(), type: 'error');
        }
    }

    public function render()
    {
        return view('livewire.ventas.registro');
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

    public function closeModalDetalle(): void
    {
        $this->showModalDetalle = false;
        $this->ventaSeleccionada = null;
    }

    public function resetForm()
    {
        $this->inicializarDetalleVenta();
        $this->clienteSeleccionado = '';
        $this->categoriaProductoSeleccionada = '';
        $this->categoriaServicioSeleccionada = '';
        $this->proveedorSeleccionado = '';
        $this->generarCodigoVenta();
        $this->subtotal = 0;
        $this->totalImpuesto = 0;
        $this->totalGeneral = 0;
    }

    #[\Livewire\Attributes\On('show-modal-venta')]
    public function showModal(int $rowId): void
    {
        $this->ventaSeleccionada = Ventas::with([
            'detalleVentas.producto', 
            'detalleVentas.servicio', 
            'cliente.persona', 
            'trabajador.persona.user',
            'estadoVenta'
        ])->find($rowId);

        $this->showModalDetalle = true;
    }

    #[\Livewire\Attributes\On('completar-venta')]
    public function completarVentaFn(int $rowId): void
    {
        $this->ventaSeleccionada = Ventas::find($rowId);
        if ($this->ventaSeleccionada) {
            $this->completarVenta();
        }
    }

    #[\Livewire\Attributes\On('cancelar-venta')]
    public function cancelarVentaFn(int $rowId): void
    {
        $this->ventaSeleccionada = Ventas::find($rowId);
        if ($this->ventaSeleccionada) {
            $this->cancelarVenta();
        }
    }

    public function exportarExcel()
    {
        return Excel::download(new VentaConDetalleExport, 'reporteVentas.xlsx');
    }

    public function exportarPdf()
    {
        $ventas = Ventas::with(['cliente.persona', 'detalleVentas.producto', 'detalleVentas.servicio', 'estadoVenta'])->get();

        $pdf = Pdf::loadView('exports.ventas_pdf', compact('ventas'))
            ->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'reporte_ventas.pdf');
    }

    public function exportarPdfVenta()
    {
        $venta = Ventas::with(['cliente.persona', 'detalleVentas.producto', 'detalleVentas.servicio', 'estadoVenta'])->find($this->ventaSeleccionada->id_venta);
        $IGV = $this->IGV;

        $pdf = Pdf::loadView('exports.venta_pdf', compact('venta', 'IGV'))
            ->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        },  'venta_' . $venta->id_venta . '.pdf');
    }
}