<?php

namespace App\Livewire\Compras;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Producto;
use App\Models\Proveedor;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Registro extends Component
{
    public $codigoOrden = '';
    public $productos = [];
    public $proveedores = [];
    public $proveedorSeleccionado = '';

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
            ['producto_id' => '', 'cantidad' => 0, 'precio_unitario' => 0]
        ];
        $this->compra = [
            'numero_factura' => '',
            'fecha_compra' => now()->format('Y-m-d'),
            'observacion' => ''
        ];
    }

    public function generarCodigoOrden()
    {
        $año = Carbon::now()->format('Y');
        $mes = Carbon::now()->format('m');

        // Buscar el último correlativo del mes
        $ultimo = Compra::whereYear('created_at', $año)
            ->whereMonth('created_at', $mes)
            ->orderBy('id', 'desc')
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
                $compra->save();
            });

            session()->flash('success', '✅ Compra aprobada correctamente ✅');
            $this->reset(["compraSeleccionada", "detalleCompra", "proveedorSeleccionado", "codigoOrden", "proveedores"]);
            $this->showModalDetalle = false;
        } catch (\Exception $e) {
            session()->flash('error', 'Error al registrar la compra: ' . $e->getMessage());
            Log::error('Error al registrar la compra', ['error' => $e->getMessage()]);
        }
    }

    public function guardar()
    {
        $this->validate([
            "proveedorSeleccionado" => "required|exists:proveedores,id",
            "compra.numero_factura" => "required|string|max:255",
            "compra.fecha_compra" => "required|date",
            "compra.observacion" => "max:1000",
            "detalleCompra.*.producto_id" => "required|exists:productos,id",
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

                $compra = Compra::create([
                    "id_proveedor" => $this->proveedorSeleccionado,
                    "id_trabajador" => auth()->user()->persona->trabajador->id,
                    "codigo" => $this->codigoOrden,
                    "numero_factura" => $this->compra['numero_factura'],
                    "fecha_compra" => $this->compra['fecha_compra'],
                    "cantidad_total" => $cantidad_total,
                    "fecha_actualizacion" => now(),
                    "estado" => "pendiente",
                    'total' => $total,
                    "observacion" => $this->compra['observacion'],
                    "created_at" => now(),
                    "updated_at" => now(),
                ]);

                foreach ($this->detalleCompra as $index => $detalle) {
                    DetalleCompra::create([
                        "id_compra" => $compra->id,
                        "id_producto" => $detalle['producto_id'],
                        "cantidad" => $detalle['cantidad'],
                        "precio_unitario" => $detalle['precio_unitario'],
                        "sub_total" => $detalle['precio_unitario'] * $detalle['cantidad'],
                    ]);
                }

                $this->reset(["compra", "detalleCompra", "proveedorSeleccionado", "codigoOrden", "proveedores"]);
                $this->detalleCompra = [
                    ['producto_id' => '', 'cantidad' => 0, 'precio_unitario' => 0]
                ];
                $this->closeModal();
            });
            session()->flash('success', 'Orden de compra registrada correctamente ✅');
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
            $this->detalleCompra[] = ['producto_id' => '', 'cantidad' => 1, 'precio' => 0];
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
}
