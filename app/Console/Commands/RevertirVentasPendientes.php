<?php

namespace App\Console\Commands;

use App\Models\DetalleVentas;
use App\Models\EstadoVentas;
use App\Models\InventarioMovimiento;
use App\Models\Lotes;
use App\Models\TipoMovimiento;
use App\Models\TransaccionPago;
use App\Models\Ventas;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RevertirVentasPendientes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ventas:revertir-pendientes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Revertir ventas pendientes que exceden el tiempo límite y liberar stock';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Buscando ventas pendientes para revertir...');

        $limiteHoras = 2; // Configurable
        $fechaLimite = Carbon::now()->subHours($limiteHoras);

        // Buscar ventas pendientes que superen el tiempo límite
        $ventasPendientes = Ventas::with(['detalleVentas.producto.lotes', 'transaccionPago', 'estadoVenta'])
            ->whereHas('estadoVenta', function ($query) {
                $query->where('nombre_estado_venta_fisica', 'pendiente');
            })
            ->where('fecha_registro', '<=', $fechaLimite)
            ->get();

        $this->info("Encontradas {$ventasPendientes->count()} ventas para revertir");

        foreach ($ventasPendientes as $venta) {
            try {
                DB::beginTransaction();

                $this->revertirVenta($venta);

                DB::commit();
                $this->info("Venta {$venta->codigo} revertida exitosamente");

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Error al revertir venta {$venta->codigo}: " . $e->getMessage());
                $this->error("Error al revertir venta {$venta->codigo}: " . $e->getMessage());
            }
        }

        $this->info('Proceso de reversión completado');
        return Command::SUCCESS;
    }

    private function revertirVenta(Ventas $venta)
    {
        // 1. Revertir stock de cada producto
        foreach ($venta->detalleVentas as $detalle) {
            $this->revertirStockProducto($detalle, $venta->id_venta);
        }

        // 2. Cambiar estado de la venta a "anulada"
        $estadoAnulado = EstadoVentas::where('nombre_estado_venta_fisica', 'cancelado')->first();
        if ($estadoAnulado) {
            $venta->id_estado_venta = $estadoAnulado->id_estado_venta_fisica;
            $venta->save();
        }

        // 3. Cambiar estado de la transacción de pago a "expirada"
        if ($venta->transaccionPago) {
            $venta->transaccionPago->estado = TransaccionPago::ESTADO_FALLIDO;
            $venta->transaccionPago->save();
        }

        // 4. Registrar motivo de anulación
        $venta->observacion = ($venta->observacion ?? '') .
            " | ANULADA: Tiempo límite excedido (" . Carbon::now()->format('Y-m-d H:i:s') . ")";
        $venta->id_estado_venta = $estadoAnulado->id_estado_venta_fisica;
        $venta->save();

        Log::info("Venta {$venta->codigo} revertida por tiempo límite excedido");
    }

    private function revertirStockProducto(DetalleVentas $detalle, $idVenta)
    {
        $producto = $detalle->producto;
        $cantidadARevertir = $detalle->cantidad;

        // Buscar movimientos de inventario asociados a esta venta Y al producto específico
        $movimientos = InventarioMovimiento::where([
            'tipo_movimiento_asociado' => Ventas::class,
            'id_movimiento_asociado' => $idVenta,
        ])
            ->whereHas('lote', function ($query) use ($detalle) {
                $query->where('id_producto', $detalle->id_producto);
            })
            ->get();

        foreach ($movimientos as $movimiento) {
            if ($cantidadARevertir <= 0) break;

            $lote = $movimiento->lote;
            if (!$lote) continue;

            // La cantidad en el movimiento es negativa (salida), revertir significa sumar
            $cantidadMovimiento = abs($movimiento->cantidad_movimiento);
            $cantidadARevertirEnLote = min($cantidadMovimiento, $cantidadARevertir);

            if ($cantidadARevertirEnLote > 0) {
                // Revertir al lote (priorizar cantidad_almacenada)
                $lote->cantidad_almacenada += $cantidadARevertirEnLote;
                $lote->cantidad_vendida = max(0, $lote->cantidad_vendida - $cantidadARevertirEnLote);
                $lote->save();

                // Registrar movimiento de reversión
                $this->registrarMovimientoReversion($lote, $cantidadARevertirEnLote, $idVenta);

                $cantidadARevertir -= $cantidadARevertirEnLote;
            }
        }

        if ($cantidadARevertir > 0) {
            Log::warning("No se pudo revertir completamente el stock para el producto {$producto->nombre_producto}. Faltan: {$cantidadARevertir} unidades. Venta: {$idVenta}");
        }
    }

    private function registrarMovimientoReversion(Lotes $lote, $cantidad, $idVenta)
    {
        $tipoMovimientoEntrada = TipoMovimiento::where('nombre_tipo_movimiento', 'Entrada')->first();

        if (!$tipoMovimientoEntrada) {
            throw new \Exception("Tipo de movimiento 'Entrada' no encontrado");
        }

        $stockResultante = $lote->cantidad_almacenada + $lote->cantidad_mostrada;

        InventarioMovimiento::create([
            "id_tipo_movimiento" => $tipoMovimientoEntrada->id_tipo_movimiento,
            "cantidad_movimiento" => $cantidad, // Positivo porque es entrada (reversión)
            "stock_resultante" => $stockResultante,
            "id_tipo_ubicacion" => 1, // Ajustar según tu configuración
            "motivo" => "Reversión por tiempo límite excedido - Venta #" . $idVenta,
            "id_lote" => $lote->id_lote,
            "id_trabajador" => null,
            "tipo_movimiento_asociado" => Ventas::class,
            "id_movimiento_asociado" => $idVenta,
            "fecha_movimiento" => now(),
        ]);
    }
}
