<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegistrarVentaRequest;
use App\Http\Requests\ConfirmarPagoRequest;
use App\Livewire\Mantenimiento\Usuarios\Usuarios;
use App\Models\Clientes;
use App\Models\DetalleVentas;
use App\Models\EstadoVentas;
use App\Models\MetodoPago;
use App\Models\Persona;
use App\Models\TipoMovimiento;
use App\Models\User;
use App\Models\Ventas;
use App\Models\Producto;
use App\Models\Lotes;
use App\Models\InventarioMovimiento;
use App\Traits\ApiResponse;
use App\Models\TransaccionPago;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VentasController extends Controller
{
    use ApiResponse;

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

        return sprintf("VTA-%s-%s-%05d", $año, $mes, $correlativo);
    }

    public function registrarVenta(RegistrarVentaRequest $request)
    {
        DB::beginTransaction();

        try {
            // Buscar el cliente por persona
            $cliente = Clientes::where('id_persona', $request->id_persona)->first();

            if (!$cliente) {
                DB::rollBack();
                return $this->errorResponse('La persona especificada no está registrada como cliente.', 404);
            }

            $codigo = $this->generarCodigoVenta();
            $IGV = 0.18;

            $estadoVentaPendiente = EstadoVentas::where("nombre_estado_venta_fisica", "pendiente")->first();
            if (!$estadoVentaPendiente) {
                DB::rollBack();
                throw new \Exception("Estado de venta 'pendiente' no encontrado");
            }

            // 🔹 CREAR LA VENTA (sin id_metodo_pago)
            $venta = Ventas::create([
                "fecha_venta" => now(),
                "codigo" => $codigo,
                "subtotal" => $request->subtotal,
                "total" => $request->total,
                "descuento" => $request->descuento,
                "impuesto" => $request->subtotal * $IGV,
                "observacion" => $request->observacion ?? "",
                "id_estado_venta" => $estadoVentaPendiente->id_estado_venta_fisica,
                "id_cliente" => $cliente->id_cliente,
                "id_trabajador" => null,
                "tipo_venta" => "web"
                // 🔹 NO incluir id_metodo_pago aquí
            ]);

            // 🔹 CREAR TRANSACCIÓN DE PAGO
            $transaccionPago = TransaccionPago::create([
                "id_venta" => $venta->id_venta,
                "id_metodo_pago" => $request->id_metodo_pago,
                "monto" => $request->total,
                "referencia" => null, // Se llenará cuando se confirme el pago
                "estado" => TransaccionPago::ESTADO_PENDIENTE,
                "fecha_pago" => null, // Se llenará cuando se confirme
                "comprobante_url" => null,
                "datos_adicionales" => null,
            ]);

            // Procesar items del carrito
            foreach ($request->items as $item) {
                $this->procesarItemVenta($venta->id_venta, $item);
            }

            DB::commit();

            // 🔹 CARGAR RELACIONES CORRECTAS
            $venta->load([
                'detalleVentas.producto',
                'estadoVenta',
                'transaccionPago.metodoPago',
                'cliente.persona'
            ]);

            return $this->successResponse([
                'id_venta' => $venta->id_venta,
                'codigo' => $venta->codigo,
                'fecha_venta' => $venta->fecha_venta,
                'subtotal' => $venta->subtotal,
                'total' => $venta->total,
                'impuesto' => $venta->impuesto,
                'descuento' => $venta->descuento,
                'estado' => $venta->estadoVenta->nombre_estado_venta_fisica,
                'transaccion_pago' => [
                    'id_transaccion_pago' => $transaccionPago->id_transaccion_pago,
                    'estado' => $transaccionPago->estado,
                    'metodo_pago' => $venta->transaccionPago->metodoPago,
                ],
                'cliente' => [
                    'id_persona' => $venta->cliente->persona->id_persona,
                    'nombre' => $venta->cliente->persona->nombre,
                    'apellido_paterno' => $venta->cliente->persona->apellido_paterno,
                    'apellido_materno' => $venta->cliente->persona->apellido_materno,
                    'correo_electronico_personal' => $venta->cliente->persona->correo_electronico_personal,
                ],
                'items' => $venta->detalleVentas->map(function ($detalle) {
                    return [
                        'id_detalle_venta' => $detalle->id_detalle_venta,
                        'id_producto' => $detalle->id_producto,
                        'cantidad' => $detalle->cantidad,
                        'precio_unitario' => $detalle->precio_unitario,
                        'subtotal' => $detalle->subtotal,
                        'producto' => [
                            'nombre_producto' => $detalle->producto->nombre_producto,
                            'ruta_imagen' => $detalle->producto->ruta_imagen,
                        ]
                    ];
                })
            ], 'Venta registrada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en registrar venta: ' . $e->getMessage());

            // Mensajes específicos para errores comunes
            $mensajeError = 'Error al registrar la venta';
            if (str_contains($e->getMessage(), 'Stock insuficiente')) {
                $mensajeError = $e->getMessage();
            } elseif (str_contains($e->getMessage(), 'SQLSTATE')) {
                $mensajeError = 'Error de base de datos al procesar la venta: ' . $e->getMessage();
            }

            return $this->serverErrorResponse($mensajeError);
        }
    }

    private function procesarItemVenta($idVenta, $item)
    {
        DB::beginTransaction();

        try {
            // Verificar stock disponible - incluir lotes sin fecha de vencimiento
            $producto = Producto::with(['lotes' => function ($query) {
                $query->where('estado', 'activo')
                    ->where(function ($q) {
                        $q->where('fecha_vencimiento', '>', now())
                            ->orWhereNull('fecha_vencimiento'); // Incluir lotes sin fecha de vencimiento
                    })
                    ->where(function ($q) {
                        $q->where('cantidad_almacenada', '>', 0)
                            ->orWhere('cantidad_mostrada', '>', 0);
                    })
                    ->orderBy('fecha_vencimiento') // Los null irán primero
                    ->lockForUpdate();
            }])->findOrFail($item['id_producto']);

            // Calcular stock disponible real
            $stockDisponible = $this->calcularStockDisponible($producto);
            Log::info("Stock disponible para {$producto->nombre_producto}: {$stockDisponible}, Cantidad solicitada: {$item['cantidad']}");

            if ($stockDisponible < $item['cantidad']) {
                throw new \Exception("Stock insuficiente para el producto: {$producto->nombre_producto}. Stock disponible: {$stockDisponible}");
            }

            $cantidadRequerida = $item['cantidad'];
            $lotesUtilizados = [];

            // Descontar de lotes (método FIFO)
            foreach ($producto->lotes as $lote) {
                if ($cantidadRequerida <= 0) break;

                $cantidadDisponibleLote = $lote->cantidad_almacenada + $lote->cantidad_mostrada;
                $cantidadADescontar = min($cantidadDisponibleLote, $cantidadRequerida);

                if ($cantidadADescontar > 0) {
                    Log::info("Descontando {$cantidadADescontar} del lote {$lote->codigo_lote}. Disponible: {$cantidadDisponibleLote}");

                    // Priorizar cantidad mostrada
                    if ($lote->cantidad_mostrada >= $cantidadADescontar) {
                        $lote->cantidad_mostrada -= $cantidadADescontar;
                    } else {
                        $restante = $cantidadADescontar - $lote->cantidad_mostrada;
                        $lote->cantidad_mostrada = 0;
                        $lote->cantidad_almacenada -= $restante;
                    }

                    $lote->cantidad_vendida += $cantidadADescontar;
                    $lote->save();

                    // Registrar movimiento de inventario
                    $this->registrarMovimientoInventario($lote, $cantidadADescontar, $idVenta);

                    $cantidadRequerida -= $cantidadADescontar;
                    $lotesUtilizados[] = [
                        'lote' => $lote,
                        'cantidad' => $cantidadADescontar
                    ];
                }
            }

            if ($cantidadRequerida > 0) {
                throw new \Exception("Error al descontar stock para el producto: {$producto->nombre_producto}. Faltan: {$cantidadRequerida} unidades");
            }

            // Crear detalle de venta
            $subtotal = $item['precio_unitario'] * $item['cantidad'];

            DetalleVentas::create([
                "cantidad" => $item['cantidad'],
                "precio_unitario" => $item['precio_unitario'],
                "subtotal" => $subtotal,
                "tipo_item" => "producto",
                "id_venta" => $idVenta,
                "id_producto" => $item['id_producto'],
                "estado" => "activo"
            ]);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Calcular stock disponible real considerando lotes no vencidos O sin fecha de vencimiento
     */
    private function calcularStockDisponible(Producto $producto): int
    {
        $stock = 0;

        foreach ($producto->lotes as $lote) {
            // Considerar lotes activos que no estén vencidos O no tengan fecha de vencimiento
            $estaVigente = $lote->fecha_vencimiento === null || $lote->fecha_vencimiento > now();

            if ($lote->estado === 'activo' && $estaVigente) {
                $stock += ($lote->cantidad_almacenada + $lote->cantidad_mostrada);
            }
        }

        return $stock;
    }

    private function registrarMovimientoInventario($lote, $cantidad, $idVenta)
    {
        // Obtener tipo de movimiento "Venta"
        $tipoMovimientoVenta = TipoMovimiento::where('nombre_tipo_movimiento', 'Salida')->first();

        if (!$tipoMovimientoVenta) {
            throw new \Exception("Tipo de movimiento 'Venta' no encontrado");
        }

        // Calcular stock resultante
        $stockResultante = $lote->cantidad_almacenada + $lote->cantidad_mostrada;

        InventarioMovimiento::create([
            "id_tipo_movimiento" => $tipoMovimientoVenta->id_tipo_movimiento,
            "cantidad_movimiento" => -$cantidad, // Negativo porque es salida
            "stock_resultante" => $stockResultante,
            "id_tipo_ubicacion" => 1, // Ajustar según tu configuración
            "motivo" => "Venta #" . $idVenta,
            "id_lote" => $lote->id_lote,
            "id_trabajador" => null,
            "tipo_movimiento_asociado" => Ventas::class,
            "id_movimiento_asociado" => $idVenta,
            "fecha_movimiento" => now(),
        ]);
    }

    public function confirmarPago(ConfirmarPagoRequest $request, $idVenta)
    {
        DB::beginTransaction();

        try {
            $venta = Ventas::with(['transaccionPago', 'estadoVenta'])->findOrFail($idVenta);

            // Verificar que la venta no haya expirado (2 horas límite)
            $limiteHoras = 2;
            if ($venta->created_at->lte(now()->subHours($limiteHoras))) {
                DB::rollBack();
                return $this->errorResponse('El tiempo para completar el pago ha expirado (máximo 2 horas). Por favor, realiza un nuevo pedido.', 400);
            }

            // Verificar que la venta esté en estado pendiente
            if ($venta->estadoVenta->nombre_estado_venta_fisica !== 'pendiente') {
                return $this->errorResponse('Esta venta ya ha sido procesada anteriormente.', 400);
            }

            // Verificar que la transacción esté pendiente
            if (!$venta->transaccionPago || $venta->transaccionPago->estado !== TransaccionPago::ESTADO_PENDIENTE) {
                return $this->errorResponse('La transacción de pago no está disponible o ya fue procesada.', 400);
            }

            // Guardar comprobante
            $comprobantePath = null;
            if ($request->hasFile('comprobante')) {
                $comprobantePath = $request->file('comprobante')->store('comprobantes', 'public');
            }

            // Cambiar estado de la venta a "completado"
            $estadoCompletado = EstadoVentas::where("nombre_estado_venta_fisica", "completado")->first();
            if ($estadoCompletado) {
                $venta->id_estado_venta = $estadoCompletado->id_estado_venta_fisica;
                $venta->save();
            }

            // Actualizar transacción de pago
            $venta->transaccionPago->marcarComoCompletado(
                $request->referencia ?? 'PAGO-' . $venta->codigo,
                $comprobantePath
            );

            DB::commit();

            // Recargar relaciones
            $venta->load(['estadoVenta', 'transaccionPago.metodoPago']);

            return $this->successResponse([
                'id_venta' => $venta->id_venta,
                'codigo' => $venta->codigo,
                'estado_venta' => $venta->estadoVenta->nombre_estado_venta_fisica,
                'transaccion_pago' => [
                    'id_transaccion_pago' => $venta->transaccionPago->id_transaccion_pago,
                    'estado' => $venta->transaccionPago->estado,
                    'fecha_pago' => $venta->transaccionPago->fecha_pago,
                    'referencia' => $venta->transaccionPago->referencia,
                    'comprobante_url' => $venta->transaccionPago->comprobante_url,
                    'metodo_pago' => $venta->transaccionPago->metodoPago,
                ],
                'mensaje' => 'Pago confirmado exitosamente'
            ], 'Pago confirmado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al confirmar pago: ' . $e->getMessage());
            return $this->serverErrorResponse('Error al confirmar el pago: ' . $e->getMessage());
        }
    }

    public function obtenerMetodosPago()
    {
        try {
            $metodos = MetodoPago::where("estado", "activo")
                ->orderBy('orden', 'asc')
                ->get();

            return $this->successResponse($metodos, 'Métodos de pago obtenidos exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al obtener métodos de pago: ' . $e->getMessage());
            return $this->serverErrorResponse('Error al obtener métodos de pago');
        }
    }

    public function pedidosCliente($id_usuario)
    {
        try {
            $usuario = User::find($id_usuario);

            if (!$usuario) {
                return $this->errorResponse('Usuario no encontrado', 404);
            }

            if (!$usuario->persona?->cliente) {
                return $this->errorResponse('El cliente no existe para este usuario', 400);
            }

            $pedidosCliente = Ventas::where("id_cliente", $usuario->persona->cliente->id_cliente)
                ->where("tipo_venta", "web")
                ->with(["detalleVentas", "transaccionPago.metodoPago", 'estadoVenta'])
                ->get();

            return $this->successResponse($pedidosCliente, "Pedidos del cliente obtenidos exitosamente");

        } catch (\Exception $e) {
            Log::error('Error al pedidosCliente: ' . $e->getMessage());
            return $this->serverErrorResponse('Error al pedidosCliente');
        }
    }
}
