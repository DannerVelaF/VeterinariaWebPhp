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
    public $entradasRapidas = []; // Para entradas rápidas
    public $showFormularioRapido = false;

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

    public function getProductosPendientesCountProperty()
    {
        if (!$this->productosOC instanceof \Illuminate\Support\Collection) {
            $this->productosOC = collect($this->productosOC);
        }

        return $this->productosOC->where('cantidad', '>', 0)->count();
    }

    public function buscarOrdenCompra()
    {
        $this->productosOC = [];
        $this->proveedorOC = null;
        $this->id_producto = null;
        $this->productoSeleccionado = null;
        $this->lote['cantidad_total'] = '';
        $this->lote['precio_compra'] = '';
        $this->entradasRapidas = [];

        if (!$this->ordenCompra) {
            $this->dispatch('notify', title: 'Error', description: 'No se ha introducido el código de orden de compra', type: 'error');
            return;
        }

        // ✅ CORRECCIÓN: Cargar la relación de proveedores en los productos
        $compra = Compra::with([
            'detalleCompra.producto.proveedores', // ← AGREGAR ESTA RELACIÓN
            'detalleCompra.producto.unidad',
            'detalleCompra.estadoDetalleCompra',
            'proveedor',
            'estadoCompra' // ← AGREGAR RELACIÓN DEL ESTADO
        ])->where('codigo', trim($this->ordenCompra))->first();

        if (!$compra) {
            $this->dispatch('notify', title: 'Error', description: 'No se encontró la orden de compra: ' . $this->ordenCompra, type: 'error');
            return;
        }

        // ✅ NUEVO: Mostrar alerta con el estado de la compra y validar si puede continuar
        $puedeContinuar = $this->mostrarAlertaEstadoCompra($compra);

        if (!$puedeContinuar) {
            // Si no puede continuar, limpiar todo y salir
            $this->resetForm();
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

                // ✅ VERIFICAR QUE EL PRODUCTO PERTENEZCA AL PROVEEDOR DE LA OC
                $proveedorOC = $detalle->compra->proveedor;
                $productoPertenece = $detalle->producto->proveedores->contains('id_proveedor', $proveedorOC->id_proveedor);

                if (!$productoPertenece) {
                    Log::warning("Producto {$detalle->producto->nombre_producto} no pertenece al proveedor {$proveedorOC->nombre_proveedor}");
                }

                return [
                    'id_producto' => $detalle->producto->id_producto,
                    'id_detalle_compra' => $detalle->id_detalle_compra,
                    'nombre' => $detalle->producto->nombre_producto . ' (' . $detalle->producto->unidad?->nombre_unidad . ')',
                    'precio_compra' => $detalle->precio_unitario,
                    'cantidad' => max($cantidadPendiente, 0), // evita números negativos
                    'codigo_barras' => $detalle->producto->codigo_barras,
                    'estado' => $cantidadPendiente <= 0 ? 'recibido' : 'pendiente',
                    'pertenece_proveedor' => $productoPertenece, // ✅ NUEVO: Para debug
                    'cantidad_original' => $detalle->cantidad,
                    'cantidad_recibida' => $cantidadRecibida,
                ];
            })
            ->filter(function ($producto) {
                // ✅ FILTRAR SOLO PRODUCTOS QUE PERTENEZCAN AL PROVEEDOR
                return $producto['pertenece_proveedor'] === true;
            })
            ->values();

        // Inicializar entradas rápidas
        foreach ($this->productosOC as $producto) {
            if ($producto['cantidad'] > 0) {
                $this->entradasRapidas[$producto['id_detalle_compra']] = [
                    'cantidad' => $producto['cantidad'],
                    'ubicacion' => 'almacen',
                    'fecha_vencimiento' => null, // ✅ Inicializar como null en lugar de cadena vacía
                    'observacion' => ''
                ];
            }
        }

        $this->showFormularioRapido = count($this->productosOC) > 1;
    }

    // ✅ NUEVO: Método para mostrar alerta del estado de la compra y determinar si puede continuar
    private function mostrarAlertaEstadoCompra(Compra $compra): bool
    {
        $estado = $compra->estadoCompra->nombre_estado_compra ?? 'desconocido';
        $codigoOC = $compra->codigo;

        switch ($estado) {
            case 'aprobado':
                $this->dispatch('notify',
                    title: 'OC Aprobada',
                    description: "La orden de compra {$codigoOC} está APROBADA y lista para recibir productos.",
                    type: 'success'
                );
                return true; // Puede continuar

            case 'pendiente':
                $this->dispatch('notify',
                    title: 'OC Pendiente',
                    description: "La orden de compra {$codigoOC} está PENDIENTE de aprobación. No se pueden registrar entradas hasta que sea aprobada.",
                    type: 'warning'
                );
                return false; // No puede continuar

            case 'recibido':
                $this->dispatch('notify',
                    title: 'OC Completada',
                    description: "La orden de compra {$codigoOC} ya está marcada como RECIBIDA. Verifique si necesita registrar entradas adicionales.",
                    type: 'info'
                );
                return true; // Puede continuar (por si hay ajustes)

            case 'cancelado':
                $this->dispatch('notify',
                    title: 'OC Cancelada',
                    description: "La orden de compra {$codigoOC} está CANCELADA. No se pueden registrar entradas.",
                    type: 'error'
                );
                return false; // No puede continuar

            default:
                $this->dispatch('notify',
                    title: 'Estado Desconocido',
                    description: "La orden de compra {$codigoOC} tiene un estado desconocido: {$estado}. Contacte al administrador.",
                    type: 'warning'
                );
                return false; // No puede continuar
        }
    }


    public function registrar()
    {
        // ✅ AGREGAR VALIDACIÓN PARA VERIFICAR RELACIÓN PRODUCTO-PROVEEDOR
        $this->validate([
            "id_producto" => "required|exists:detalle_compras,id_detalle_compra",
            'lote.cantidad_total' => [
                'required',
                'numeric',
                'min:1',
                'max:999999999.99',
                function ($attribute, $value, $fail) {
                    if ($this->productoSeleccionado && $value > $this->productoSeleccionado['cantidad']) {
                        $fail('La cantidad recibida no puede ser mayor a la cantidad de la orden de compra (' . $this->productoSeleccionado['cantidad'] . ').');
                    }

                    // ✅ VALIDACIÓN ADICIONAL: Verificar que el producto pertenezca al proveedor
                    if ($this->productoSeleccionado && isset($this->productoSeleccionado['pertenece_proveedor']) && !$this->productoSeleccionado['pertenece_proveedor']) {
                        $fail('El producto seleccionado no está asociado al proveedor de esta orden de compra.');
                    }
                },
            ],
            'lote.fecha_recepcion' => 'required|date|before_or_equal:today',
            'lote.fecha_vencimiento' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) {
                    if ($value && $this->lote['fecha_recepcion'] && $value <= $this->lote['fecha_recepcion']) {
                        $fail('La fecha de vencimiento debe ser posterior a la fecha de recepción.');
                    }
                },
            ],
            "lote.observacion" => "max:1000",
            'lote.precio_compra' => 'required|numeric|min:1',
        ], [
            'lote.cantidad_total.required' => 'Debe ingresar la cantidad.',
            'lote.cantidad_total.numeric' => 'La cantidad debe ser un número.',
            'lote.cantidad_total.min' => 'La cantidad debe ser mayor a 0.',
            'lote.cantidad_total.max' => 'La cantidad ingresada es demasiado grande.',
        ]);

        try {
            DB::transaction(function () {
                // ✅ CORRECCIÓN: Obtener el ID del trabajador de forma segura
                $idTrabajador = auth()->user()->persona->trabajador->id_trabajador ?? null;

                if (!$idTrabajador) {
                    throw new \Exception("No se pudo identificar al trabajador. Verifique que el usuario tenga un perfil de trabajador asociado.");
                }

                // ✅ VERIFICACIÓN FINAL ANTES DE CREAR EL LOTE
                $detalleCompra = DetalleCompra::with(['producto.proveedores', 'compra.proveedor'])
                    ->find($this->lote['id_detalle_compra']);

                if (!$detalleCompra) {
                    throw new \Exception("Detalle de compra no encontrado");
                }

                // Verificar que el producto pertenezca al proveedor de la OC
                $proveedorOC = $detalleCompra->compra->proveedor;
                $productoPertenece = $detalleCompra->producto->proveedores->contains('id_proveedor', $proveedorOC->id_proveedor);

                if (!$productoPertenece) {
                    throw new \Exception("El producto '{$detalleCompra->producto->nombre_producto}' no está asociado al proveedor '{$proveedorOC->nombre_proveedor}'");
                }

                $this->lote['codigo_lote'] = $this->generarCodigoLote($detalleCompra->producto->codigo_barras);

                // ✅ CORRECCIÓN: Usar el accessor stock_actual del modelo Producto
                $stockActual = $detalleCompra->producto->stock_actual;

                // Crear el lote
                $lote = Lotes::create([
                    'codigo_lote' => $this->lote['codigo_lote'],
                    'cantidad_total' => $this->lote['cantidad_total'],
                    'cantidad_almacenada' => $this->ubicacion === 'almacen' ? $this->lote['cantidad_total'] : 0,
                    'cantidad_mostrada' => $this->ubicacion === 'mostrador' ? $this->lote['cantidad_total'] : 0,
                    'cantidad_vendida' => 0,
                    'fecha_recepcion' => $this->lote['fecha_recepcion'],
                    'fecha_vencimiento' => $this->lote['fecha_vencimiento'] ?: null,
                    'observacion' => $this->lote['observacion'],
                    'precio_compra' => $this->lote['precio_compra'],
                    'id_producto' => $detalleCompra->producto->id_producto,
                    'estado' => 'activo',
                ]);

                // Obtener tipo de movimiento de entrada
                $tipoEntrada = TipoMovimiento::where("nombre_tipo_movimiento", "entrada")->first();

                if (!$tipoEntrada) {
                    throw new \Exception("No se encontró el tipo de movimiento 'entrada' en el sistema.");
                }

                // ✅ CORRECCIÓN: Calcular stock resultante usando el accessor
                $stockResultante = $stockActual + $this->lote['cantidad_total'];

                // Crear movimiento de inventario
                InventarioMovimiento::create([
                    'cantidad_movimiento' => $this->lote['cantidad_total'],
                    'stock_resultante' => $stockResultante,
                    'fecha_movimiento' => now(),
                    'id_tipo_movimiento' => $tipoEntrada->id_tipo_movimiento,
                    'id_movimiento_asociado' => $detalleCompra->id_detalle_compra,
                    'id_lote' => $lote->id_lote,
                    'id_trabajador' => $idTrabajador,
                    'id_tipo_ubicacion' => $this->ubicacion === 'almacen' ? 1 : 2,
                ]);

                // ✅ NO necesitamos actualizar stock_actual porque se calcula dinámicamente
            });

            $this->dispatch('notify', title: 'Success', description: 'Entrada registrada con éxito. Código de lote: ' . $this->lote['codigo_lote'], type: 'success');
            $this->resetForm();
            $this->dispatch('entradasUpdated');
        } catch (Exception $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error al registrar la entrada: ' . $e->getMessage(), type: 'error');
            Log::error('Error al registrar entrada', ['error' => $e->getMessage()]);
        }
    }

    public function generarCodigoLote($codigoBarras = null)
    {
        if (!$codigoBarras) {
            // ✅ FALLBACK: Generar código alternativo si no hay código de barras
            do {
                $codigo = "LT-" . Str::random(8) . "-" . random_int(100, 999);
            } while (Lotes::where('codigo_lote', $codigo)->exists());
        } else {
            do {
                $codigo = "LT." . $codigoBarras . "-" . random_int(100, 999);
            } while (Lotes::where('codigo_lote', $codigo)->exists());
        }

        return $codigo;
    }

    public function registrarEntradasRapidas()
    {
        try {
            DB::transaction(function () {
                $entradasRegistradas = 0;

                // ✅ CORRECCIÓN: Obtener el ID del trabajador de forma segura
                $idTrabajador = auth()->user()->persona->trabajador->id_trabajador ?? null;

                if (!$idTrabajador) {
                    throw new \Exception("No se pudo identificar al trabajador. Verifique que el usuario tenga un perfil de trabajador asociado.");
                }

                foreach ($this->entradasRapidas as $idDetalleCompra => $entrada) {
                    if (empty($entrada['cantidad']) || $entrada['cantidad'] <= 0) {
                        continue;
                    }

                    $detalleCompra = DetalleCompra::with(['producto.proveedores', 'compra.proveedor'])
                        ->find($idDetalleCompra);

                    if (!$detalleCompra) {
                        continue;
                    }

                    // Verificar cantidad máxima
                    $productoOC = collect($this->productosOC)->firstWhere('id_detalle_compra', $idDetalleCompra);
                    $cantidadMaxima = $productoOC ? $productoOC['cantidad'] : 0;

                    if ($entrada['cantidad'] > $cantidadMaxima) {
                        throw new \Exception("La cantidad para {$detalleCompra->producto->nombre_producto} no puede ser mayor a {$cantidadMaxima}");
                    }

                    // ✅ CORRECCIÓN: Convertir fecha_vencimiento vacía a null
                    $fechaVencimiento = !empty($entrada['fecha_vencimiento']) ? $entrada['fecha_vencimiento'] : null;

                    // ✅ CORRECCIÓN: Usar el accessor stock_actual del modelo Producto
                    $stockActual = $detalleCompra->producto->stock_actual;

                    // Generar código de lote
                    $codigoLote = $this->generarCodigoLote($detalleCompra->producto->codigo_barras);

                    // Crear el lote
                    $lote = Lotes::create([
                        'codigo_lote' => $codigoLote,
                        'cantidad_total' => $entrada['cantidad'],
                        'cantidad_almacenada' => $entrada['ubicacion'] === 'almacen' ? $entrada['cantidad'] : 0,
                        'cantidad_mostrada' => $entrada['ubicacion'] === 'mostrador' ? $entrada['cantidad'] : 0,
                        'cantidad_vendida' => 0,
                        'fecha_recepcion' => $this->lote['fecha_recepcion'],
                        'fecha_vencimiento' => $fechaVencimiento,
                        'observacion' => $entrada['observacion'] ?? '',
                        'precio_compra' => $productoOC['precio_compra'],
                        'id_producto' => $detalleCompra->producto->id_producto,
                        'estado' => 'activo',
                    ]);

                    // Obtener tipo de movimiento de entrada
                    $tipoEntrada = TipoMovimiento::where("nombre_tipo_movimiento", "entrada")->first();

                    if (!$tipoEntrada) {
                        throw new \Exception("No se encontró el tipo de movimiento 'entrada' en el sistema.");
                    }

                    // ✅ CORRECCIÓN: Calcular stock resultante usando el accessor
                    $stockResultante = $stockActual + $entrada['cantidad'];

                    // Crear movimiento de inventario
                    InventarioMovimiento::create([
                        'cantidad_movimiento' => $entrada['cantidad'],
                        'stock_resultante' => $stockResultante,
                        'fecha_movimiento' => now(),
                        'id_tipo_movimiento' => $tipoEntrada->id_tipo_movimiento,
                        'id_movimiento_asociado' => $detalleCompra->id_detalle_compra,
                        'id_lote' => $lote->id_lote,
                        'id_trabajador' => $idTrabajador,
                        'id_tipo_ubicacion' => $entrada['ubicacion'] === 'almacen' ? 1 : 2,
                    ]);

                    $entradasRegistradas++;
                }

                if ($entradasRegistradas === 0) {
                    throw new \Exception("No se registró ninguna entrada. Verifique las cantidades ingresadas.");
                }
            });

            $this->dispatch('notify', title: 'Success', description: "Se registraron {$entradasRegistradas} entradas correctamente.", type: 'success');
            $this->resetForm();
            $this->dispatch('entradasUpdated');
        } catch (Exception $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error al registrar las entradas: ' . $e->getMessage(), type: 'error');
            Log::error('Error al registrar entradas rápidas', ['error' => $e->getMessage()]);
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
        $this->entradasRapidas = [];
        $this->showFormularioRapido = false;

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

        // ✅ CORRECCIÓN: Usar el modelo Producto para obtener el stock
        $producto = Producto::with(['lotes' => function ($query) {
            $query->where('estado', 'activo');
        }])->find($this->productoSeleccionado['id_producto']);

        if (!$producto) {
            return [
                'total' => 0,
                'almacen' => 0,
                'mostrador' => 0,
            ];
        }

        $lotes = $producto->lotes;

        return [
            'total' => $producto->stock_actual, // ✅ Usa el accessor
            'almacen' => $lotes->sum('cantidad_almacenada'),
            'mostrador' => $lotes->sum('cantidad_mostrada'),
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
