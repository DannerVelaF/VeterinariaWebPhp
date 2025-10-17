<?php

namespace App\Livewire\Compras;

use App\Exports\CompraConDetalleExport;
use App\Models\Compra;
use App\Models\DetalleCompra;
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
        $this->cantOrdenesPendientes = Compra::where('estado', 'pendiente')->count();
        $this->cantOrdenesAprobadas = Compra::where('estado', 'aprobado')->count();
        $this->cantOrdenesRecibidas = Compra::where('estado', 'recibido')->count();
        $this->precioCompraTotal = Compra::where("estado", "recibido")->sum('total');
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
            $this->productos = Producto::with(['proveedor', 'unidad'])
                ->where('id_proveedor', $value)
                ->where('estado', 'activo')
                ->get();
        }
    }

    public function aprobarCompra()
    {
        try {
            DB::transaction(function () {
                $compra = $this->compraSeleccionada;
                $compra->estado = "aprobado";
                $compra->id_usuario_aprobador = auth()->user()->persona->id_persona;
                $compra->save();
            });

            session()->flash('success', '✅ Compra aprobada correctamente ✅');
            $this->reset(["compraSeleccionada", "detalleCompra", "proveedorSeleccionado", "codigoOrden", "proveedores"]);
            $this->showModalDetalle = false;
            $this->dispatch('comprasUpdated');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al registrar la compra: ' . $e->getMessage());
            Log::error('Error al registrar la compra', ['error' => $e->getMessage()]);
        }
    }

    public function rechazarCompra()
    {
        try {
            DB::transaction(function () {
                $compra = $this->compraSeleccionada;
                $compra->estado = "cancelado";
                $compra->save();
            });

            session()->flash('success', '❌ Compra rechazada correctamente ❌');
            $this->reset(["compraSeleccionada", "detalleCompra", "proveedorSeleccionado", "codigoOrden", "proveedores"]);
            $this->showModalDetalle = false;
            $this->dispatch('comprasUpdated');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al rechazar la compra: ' . $e->getMessage());
            Log::error('Error al rechazar la compra', ['error' => $e->getMessage()]);
        }
    }

    public function guardar()
    {
        $this->validate([
            "proveedorSeleccionado" => "required|exists:proveedores,id_proveedor",
            "compra.numero_factura" => "required|string|max:255",
            "compra.fecha_compra" => "required|date",
            "compra.fecha_compra" => "required|date|before_or_equal:today",
            "compra.observacion" => "max:1000",
            "detalleCompra.*.id_producto" => "required|exists:productos,id_producto",
            "detalleCompra.*.cantidad" => "required|numeric|min:0.01",
            "detalleCompra.*.precio_unitario" => "required|numeric|min:0.01",
        ]);
        try {
            DB::transaction(function () {
                $total = 0;
                $cantidad_total = 0;
                foreach ($this->detalleCompra as $detalle) {
                    $total += $detalle['precio_unitario'] * $detalle['cantidad'];
                    $cantidad_total += $detalle['cantidad'];
                }

                $estadoPendiente = EstadoDetalleCompra::where('nombre_estado_detalle_compra', 'pendiente')->first();

                $compra = Compra::create([
                    "id_proveedor" => $this->proveedorSeleccionado,
                    "id_trabajador" => auth()->user()->persona->trabajador->id_trabajador,
                    "codigo" => $this->codigoOrden,
                    "numero_factura" => $this->compra['numero_factura'],
                    "fecha_compra" => $this->compra['fecha_compra'],
                    "cantidad_total" => $cantidad_total,
                    "fecha_actualizacion" => now(),
                    "estado" => "pendiente",
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
            session()->flash('success', 'Orden de compra registrada correctamente ✅');
            $this->dispatch('comprasUpdated');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al registrar el proveedor: ' . $e->getMessage());
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
    }
    public function agregarDetalle()
    {
        if (count($this->detalleCompra) < 50) { // límite por seguridad
            $this->detalleCompra[] = ['id_producto' => '', 'cantidad' => 1, 'precio' => 0];
        }
    }

    public function eliminarDetalle($index)
    {
        unset($this->detalleCompra[$index]);
        $this->detalleCompra = array_values($this->detalleCompra); // reindexar
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
        $compras = Compra::with(['proveedor', 'detalleCompra.producto'])->get();

        $pdf = Pdf::loadView('exports.compras_pdf', compact('compras'))
            ->setPaper('a4', 'landscape'); // opcional

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'reporte_compras.pdf');
    }

    public function exportarPdfOrdenCompra()
    {
        $compra = Compra::with(['proveedor', 'detalleCompra.producto'])->find($this->compraSeleccionada->id_compra);
        $IGV = $this->IGV;

        $pdf = Pdf::loadView('exports.ordenCompra_pdf', compact('compra', 'IGV'))
            ->setPaper('a4', 'portrait'); // opcional

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        },  'orden_compra.pdf');
    }
}
