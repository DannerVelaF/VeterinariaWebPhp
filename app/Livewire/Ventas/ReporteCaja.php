<?php

namespace App\Livewire\Ventas;

use Livewire\Component;
use App\Models\Caja;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;

class ReporteCaja extends Component
{
    public $modalOpen = false;
    public $cajaSeleccionada = null;

    // Estadísticas
    public $topItems = [];      // Productos y Servicios mezclados
    public $balancePagos = [];
    public $balanceTipoVenta = []; // Comparativa: $$ Productos vs $$ Servicios

    #[On('verDetalleCaja')]
    public function cargarDetalle($id_caja)
    {
        $this->cajaSeleccionada = Caja::with(['trabajador.persona'])->find($id_caja);

        if ($this->cajaSeleccionada) {
            $this->calcularEstadisticas();
            $this->modalOpen = true;
        }
    }

    public function calcularEstadisticas()
    {
        // 1. Balance de Métodos de Pago
        $this->balancePagos = [
            ['label' => 'Efectivo', 'monto' => $this->cajaSeleccionada->ventas_efectivo, 'color' => 'bg-green-500'],
            ['label' => 'Tarjeta', 'monto' => $this->cajaSeleccionada->ventas_tarjeta, 'color' => 'bg-blue-500'],
            ['label' => 'Transferencia', 'monto' => $this->cajaSeleccionada->ventas_transferencia, 'color' => 'bg-purple-500'],
            ['label' => 'Digital', 'monto' => $this->cajaSeleccionada->ventas_digital, 'color' => 'bg-pink-500'],
        ];

        // 2. Top Items (Productos Y Servicios unificados)
        $this->topItems = DB::table('detalle_ventas')
            ->join('ventas', 'ventas.id_venta', '=', 'detalle_ventas.id_venta')
            ->leftJoin('productos', 'detalle_ventas.id_producto', '=', 'productos.id_producto')
            ->leftJoin('servicios', 'detalle_ventas.id_servicio', '=', 'servicios.id_servicio')
            ->where('ventas.id_caja', $this->cajaSeleccionada->id_caja)
            ->selectRaw('
                COALESCE(productos.nombre_producto, servicios.nombre_servicio, "Item Eliminado") as nombre,
                detalle_ventas.tipo_item,
                sum(detalle_ventas.cantidad) as total_vendido,
                sum(detalle_ventas.subtotal) as total_dinero
            ')
            ->groupBy('detalle_ventas.tipo_item', 'detalle_ventas.id_producto', 'detalle_ventas.id_servicio', 'productos.nombre_producto', 'servicios.nombre_servicio')
            ->orderByDesc('total_vendido')
            ->limit(10)
            ->get();

        // 3. Balance: Total Dinero Productos vs Total Dinero Servicios
        // CORRECCIÓN AQUÍ: Especificamos 'detalle_ventas.subtotal' y 'detalle_ventas.tipo_item'
        $totalesGlobales = DB::table('detalle_ventas')
            ->join('ventas', 'ventas.id_venta', '=', 'detalle_ventas.id_venta')
            ->where('ventas.id_caja', $this->cajaSeleccionada->id_caja)
            ->selectRaw('detalle_ventas.tipo_item, sum(detalle_ventas.subtotal) as total')
            ->groupBy('detalle_ventas.tipo_item')
            ->pluck('total', 'tipo_item');

        $tProd = $totalesGlobales['producto'] ?? 0;
        $tServ = $totalesGlobales['servicio'] ?? 0;

        $this->balanceTipoVenta = [
            'productos' => $tProd,
            'servicios' => $tServ,
            'total' => $tProd + $tServ
        ];
    }

    public function closeModal()
    {
        $this->modalOpen = false;
        $this->cajaSeleccionada = null;
    }

    public function render()
    {
        return view('livewire.ventas.reporte-caja');
    }
}
