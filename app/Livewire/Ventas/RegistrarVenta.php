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
use App\Models\Persona;
use App\Models\Tipo_documento;
use App\Models\Direccion;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
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
    public $filtroCliente = '';
    public $clienteSeleccionadoFormateado = null;

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

    public $opcionesDescuento = [10, 20, 30, 40, 50];
    public $descuentoSeleccionado = 0;
    public $mostrarOpcionesDescuento = false;

    public bool $showModal = false;
    public bool $showModalDetalle = false;
    public bool $showModalCliente = false;
    public ?Ventas $ventaSeleccionada = null;

    // Para el registro de nuevo cliente
    public $tiposDocumentos = [];
    public $nuevoCliente = [
        'id_tipo_documento' => '',
        'numero_documento' => '',
        'nombres' => '',
        'apellido_paterno' => '',
        'apellido_materno' => '',
        'fecha_nacimiento' => '',
        'sexo' => '',
        'nacionalidad' => 'Peruana',
        'correo_electronico_personal' => '',
        'numero_telefono_personal' => '',
    ];

    // Método para aplicar descuento
    public function aplicarDescuento($porcentaje)
    {
        $this->descuentoSeleccionado = $porcentaje;
        $this->calcularDescuento();
        $this->mostrarOpcionesDescuento = false;
    }

    // Método para quitar descuento
    public function quitarDescuento()
    {
        $this->descuentoSeleccionado = 0;
        $this->venta['descuento'] = 0;
        $this->calcularTotales();
        $this->mostrarOpcionesDescuento = false;
    }

    // Método para calcular el descuento
    public function calcularDescuento()
    {
        if ($this->descuentoSeleccionado > 0 && $this->subtotal > 0) {
            $this->venta['descuento'] = $this->subtotal * ($this->descuentoSeleccionado / 100);
        } else {
            $this->venta['descuento'] = 0;
        }
        $this->calcularTotales();
    }


    public function mount()
    {
        $this->generarCodigoVenta();
        $this->cargarClientes(); 
        //$this->clientes = Clientes::all();
        $this->cargarFiltros();
        $this->cargarProductosYServicios();
        $this->inicializarDetalleVenta();
        $this->calcularEstadisticas();
        $this->cargarTiposDocumento();

        // Inicializar descuentos
        /* $this->descuentoSeleccionado = 0;
        $this->mostrarOpcionesDescuento = false; */

        $this->subtotal = $this->subtotal ?? 0;
        $this->totalImpuesto = $this->totalImpuesto ?? 0;
        $this->totalGeneral = $this->totalGeneral ?? 0;

        $this->mostrarOpcionesDescuento = false;

        if (! isset($this->venta['descuento'])) {
            $this->venta['descuento'] = 0;
        }

        $this->opcionesDescuento = [10, 20, 30, 40, 50];
    }

    public function cargarTiposDocumento()
    {
        $this->tiposDocumentos = Tipo_documento::all();
    }


    public function updatedFiltroCliente()
    {
        $this->cargarClientes();
    }

    public function cargarFiltros()
    {
        $this->categoriasProductos = Categoriaproducto::where('estado', 'activo')->get();
        $this->categoriasServicios = CategoriaServicio::where('estado', 'activo')->get();
        $this->proveedores = Proveedor::where('estado', 'activo')->get();
    }

    // Método para seleccionar cliente
    public function seleccionarCliente($idCliente)
    {
        $this->clienteSeleccionado = $idCliente;
        $this->filtroCliente = ''; // Limpiar la búsqueda después de seleccionar
        $this->actualizarClienteFormateado();
    }

    // Método para limpiar cliente seleccionado
    public function limpiarCliente()
    {
        $this->clienteSeleccionado = '';
        $this->filtroCliente = '';
        $this->clienteSeleccionadoFormateado = null;
    }

    // Modifica el método cargarClientes para que retorne los datos formateados
    public function cargarClientes()
    {
        $query = Clientes::with('persona');
        
        if ($this->filtroCliente) {
            $query->whereHas('persona', function($q) {
                $q->where('numero_documento', 'like', '%' . $this->filtroCliente . '%')
                ->orWhere('nombre', 'like', '%' . $this->filtroCliente . '%')
                ->orWhere('apellido_paterno', 'like', '%' . $this->filtroCliente . '%')
                ->orWhere('apellido_materno', 'like', '%' . $this->filtroCliente . '%');
            });
        }
        
        $this->clientes = $query->get();
    }

    public function getClienteSeleccionadoFormateado()
    {
        if (!$this->clienteSeleccionado) {
            return null;
        }
        
        $cliente = Clientes::with('persona')->find($this->clienteSeleccionado);
        
        if (!$cliente || !$cliente->persona) {
            return null;
        }
        
        return [
            'id_cliente' => $cliente->id_cliente,
            'nombre' => $cliente->persona->nombre ?? $cliente->persona->nombre ?? '',
            'apellido_paterno' => $cliente->persona->apellido_paterno ?? '',
            'apellido_materno' => $cliente->persona->apellido_materno ?? '',
            'dni' => $cliente->persona->numero_documento ?? '',
            'telefono' => $cliente->persona->numero_telefono_personal ?? '',
            'correo' => $cliente->persona->correo_electronico_personal ?? ''
        ];
    }

    // Método para actualizar el cliente formateado
    public function actualizarClienteFormateado()
    {
        if (!$this->clienteSeleccionado) {
            $this->clienteSeleccionadoFormateado = null;
            return;
        }
        
        $cliente = Clientes::with('persona')->find($this->clienteSeleccionado);
        
        if (!$cliente || !$cliente->persona) {
            $this->clienteSeleccionadoFormateado = null;
            return;
        }
        
        $this->clienteSeleccionadoFormateado = [
            'id_cliente' => $cliente->id_cliente,
            'nombre' => $cliente->persona->nombre ?? $cliente->persona->nombre ?? '',
            'apellido_paterno' => $cliente->persona->apellido_paterno ?? '',
            'apellido_materno' => $cliente->persona->apellido_materno ?? '',
            'dni' => $cliente->persona->numero_documento ?? '',
            'telefono' => $cliente->persona->numero_telefono_personal ?? '',
            'correo' => $cliente->persona->correo_electronico_personal ?? ''
        ];
    }

    public function updatedClienteSeleccionado($value)
    {
        if ($value) {
            $this->actualizarClienteFormateado();
        } else {
            $this->clienteSeleccionadoFormateado = null;
        }
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
            [
                'tipo_item' => 'producto',
                'id_item' => '',
                'cantidad' => 1,
                'precio_unitario' => 0,
                'precio_referencial' => 0 // Para comparación de servicios
            ]
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
        /* $pendiente = EstadoVentas::where('nombre_estado_venta_fisica', 'pendiente')->first();
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
        } */

        $this->actualizarEstadisticas();
    }

    public function updated($property)
    {
        // Validar automáticamente cuando cambien campos numéricos
        if (str_starts_with($property, 'detalleVenta')) {
            $parts = explode('.', $property);
            if (count($parts) === 3) {
                $index = $parts[1];
                $field = $parts[2];

                if (in_array($field, ['cantidad', 'precio_unitario'])) {
                    $this->validarYCast($field, $index);
                }

                if ($field === 'tipo_item') {
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

        if ($property === 'venta.descuento') {
            $this->venta['descuento'] = $this->asegurarNumero($this->venta['descuento']);
            $this->calcularTotales();
        }
    }

    public function calcularTotales()
    {
        $this->subtotal = 0;

        foreach ($this->detalleVenta as $detalle) {
            // Asegurar que los valores sean numéricos
            $cantidad = $this->asegurarNumero($detalle['cantidad'] ?? 0);
            $precio = $this->asegurarNumero($detalle['precio_unitario'] ?? 0);

            if ($detalle['id_item'] && $cantidad > 0 && $precio > 0) {
                $this->subtotal += $precio * $cantidad;
            }
        }

        // Calcular descuento si hay uno seleccionado
        if ($this->descuentoSeleccionado > 0) {
            $this->venta['descuento'] = $this->subtotal * ($this->descuentoSeleccionado / 100);
        } else {
            $this->venta['descuento'] = 0;
        }

        $descuento = $this->asegurarNumero($this->venta['descuento'] ?? 0);
        $subtotalConDescuento = $this->subtotal - $descuento;
        $this->totalImpuesto = $subtotalConDescuento * $this->IGV;
        $this->totalGeneral = $subtotalConDescuento + $this->totalImpuesto;
    }

    private function asegurarNumero($valor)
    {
        if (is_numeric($valor)) {
            return floatval($valor);
        }

        if (is_string($valor)) {
            $limpio = preg_replace('/[^0-9.]/', '', $valor);
            return $limpio === '' ? 0 : floatval($limpio);
        }

        return 0;
    }

    // Método auxiliar para limpiar valores
    private function limpiarValorNumerico($valor)
    {
        if (is_string($valor)) {
            // Remover caracteres no numéricos excepto punto decimal
            $valor = preg_replace('/[^0-9.]/', '', $valor);
        }

        // Si está vacío o no es numérico, retornar 0
        if ($valor === '' || !is_numeric($valor)) {
            return 0;
        }

        return floatval($valor);
    }

    public function cargarPrecioUnitario($index)
    {
        $detalle = $this->detalleVenta[$index];

        if ($detalle['tipo_item'] === 'producto' && $detalle['id_item']) {
            $producto = Producto::find($detalle['id_item']);
            if ($producto) {
                $this->detalleVenta[$index]['precio_unitario'] = $producto->precio_unitario;
            }
        } elseif ($detalle['tipo_item'] === 'servicio' && $detalle['id_item']) {
            $servicio = Servicio::find($detalle['id_item']);
            if ($servicio) {
                // Guardar el precio referencial para comparación
                $this->detalleVenta[$index]['precio_referencial'] = $servicio->precio;

                // Solo cargar el precio referencial si el precio actual es 0 (valor inicial)
                // Esto permite que si el usuario ya modificó el precio, no se sobreescriba
                if (empty($this->detalleVenta[$index]['precio_unitario']) || $this->detalleVenta[$index]['precio_unitario'] == 0) {
                    $this->detalleVenta[$index]['precio_unitario'] = $servicio->precio;
                }
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
                'precio_unitario' => 0,
                'precio_referencial' => 0 // Para comparación de servicios
            ];
        }
    }

    public function validarYCast($campo, $index)
    {
        if (!isset($this->detalleVenta[$index][$campo])) {
            return;
        }

        $valor = $this->detalleVenta[$index][$campo];

        // Si es null o vacío, establecer valor por defecto
        if ($valor === null || $valor === '') {
            $this->detalleVenta[$index][$campo] = $campo === 'cantidad' ? 1 : 0;
            $this->calcularTotales();
            return;
        }

        // Remover todo excepto números y punto decimal (para precios)
        if ($campo === 'precio_unitario') {
            $valorLimpio = preg_replace('/[^0-9.]/', '', $valor);
            // Remover puntos decimales extras, dejar solo uno
            $partes = explode('.', $valorLimpio);
            if (count($partes) > 2) {
                $valorLimpio = $partes[0] . '.' . $partes[1];
            }
        } else {
            // Para cantidad, solo números enteros
            $valorLimpio = preg_replace('/[^0-9]/', '', $valor);
        }

        // Si después de limpiar está vacío, poner valor por defecto
        if ($valorLimpio === '') {
            $valorLimpio = $campo === 'cantidad' ? '1' : '0';
        }

        // Convertir a número
        $valorNumerico = $campo === 'cantidad' ? intval($valorLimpio) : floatval($valorLimpio);

        // Validar rangos mínimos
        if ($campo === 'cantidad' && $valorNumerico < 1) {
            $valorNumerico = 1;
        }

        if ($campo === 'precio_unitario' && $valorNumerico < 0) {
            $valorNumerico = 0;
        }

        // Actualizar el valor en el array
        $this->detalleVenta[$index][$campo] = $valorNumerico;

        $this->calcularTotales();
    }
    public function eliminarDetalle($index)
    {
        if (count($this->detalleVenta) > 1) {
            unset($this->detalleVenta[$index]);
            $this->detalleVenta = array_values($this->detalleVenta);
            $this->calcularTotales();
        }
    }

    // Método para actualizar estadísticas en tiempo real
    public function actualizarEstadisticas()
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
            "venta.descuento.numeric" => "El descuento seleccionado no es el adecuado",
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
                $cantidadSolicitada = floatval($detalle['cantidad']);

                if ($producto && $producto->stock_actual < $cantidadSolicitada) {
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
                    "subtotal" => floatval($this->subtotal),
                    "descuento" => floatval($this->venta['descuento'] ?? 0),
                    "impuesto" => floatval($this->totalImpuesto),
                    "total" => floatval($this->totalGeneral),
                    "observacion" => $this->venta['observacion'],
                    "id_estado_venta" => $estadoVentaPendiente->id_estado_venta_fisica,
                    "fecha_registro" => now(),
                    "fecha_actualizacion" => now(),
                ]);

                // Crear detalles de venta
                foreach ($this->detalleVenta as $detalle) {
                    $precio = floatval($detalle['precio_unitario']);
                    $cantidad = floatval($detalle['cantidad']);

                    $detalleData = [
                        "id_venta" => $venta->id_venta,
                        "cantidad" => $cantidad,
                        "precio_unitario" => $precio,
                        "subtotal" => $precio * $cantidad,
                        "tipo_item" => $detalle['tipo_item'],
                        "estado" => 'activo',
                        "fecha_registro" => now(),
                        "fecha_actualizacion" => now(),
                    ];

                    if ($detalle['tipo_item'] === 'producto') {
                        $detalleData['id_producto'] = $detalle['id_item'];

                        // ACTUALIZAR STOCK A TRAVÉS DE LOTES
                        $this->actualizarStockProducto($detalle['id_item'], $cantidad);
                    } else {
                        $detalleData['id_servicio'] = $detalle['id_item'];
                    }

                    DetalleVentas::create($detalleData);
                }
            });

            // Actualizar estadísticas después de guardar
            $this->actualizarEstadisticas();

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

    private function actualizarStockProducto($productoId, $cantidadVendida)
    {
        $producto = Producto::with(['lotes' => function($query) {
            $query->where('estado', 'activo')
                ->orderBy('fecha_vencimiento', 'asc'); // Primero los que vencen antes
        }])->find($productoId);

        if (!$producto) {
            throw new \Exception("Producto no encontrado");
        }

        $cantidadRestante = $cantidadVendida;

        foreach ($producto->lotes as $lote) {
            if ($cantidadRestante <= 0) break;

            // Primero descontar de cantidad_mostrada
            if ($lote->cantidad_mostrada > 0) {
                $cantidadADescontar = min($lote->cantidad_mostrada, $cantidadRestante);
                $lote->cantidad_mostrada -= $cantidadADescontar;
                $lote->cantidad_vendida += $cantidadADescontar;
                $cantidadRestante -= $cantidadADescontar;
            }

            // Si aún queda cantidad, descontar de cantidad_almacenada
            if ($cantidadRestante > 0 && $lote->cantidad_almacenada > 0) {
                $cantidadADescontar = min($lote->cantidad_almacenada, $cantidadRestante);
                $lote->cantidad_almacenada -= $cantidadADescontar;
                $lote->cantidad_vendida += $cantidadADescontar;
                $cantidadRestante -= $cantidadADescontar;
            }

            $lote->save();
        }

        // Si no hay suficiente stock en todos los lotes
        if ($cantidadRestante > 0) {
            throw new \Exception("Stock insuficiente para el producto {$producto->nombre_producto}. Faltan: {$cantidadRestante} unidades");
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

            // Actualizar estadísticas después de completar
            $this->actualizarEstadisticas();

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
                        $this->revertirStockProducto($detalle->producto->id_producto, $detalle->cantidad);
                    }
                }
            });

            // Actualizar estadísticas después de completar
            $this->actualizarEstadisticas();

            $this->dispatch('notify', title: 'Success', description: 'Venta cancelada correctamente ❌', type: 'success');
            $this->closeModalDetalle();
            $this->dispatch('ventasUpdated');

        } catch (\Exception $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error al cancelar la venta: ' . $e->getMessage(), type: 'error');
        }
    }

    private function revertirStockProducto($productoId, $cantidadARevertir)
    {
        $producto = Producto::with(['lotes' => function($query) {
            $query->where('estado', 'activo')
                ->orderBy('fecha_vencimiento', 'desc'); // Revertir en los lotes más recientes
        }])->find($productoId);

        if (!$producto) return;

        $cantidadRestante = $cantidadARevertir;

        foreach ($producto->lotes as $lote) {
            if ($cantidadRestante <= 0) break;

            // Revertir primero a cantidad_almacenada
            if ($lote->cantidad_vendida > 0) {
                $cantidadARevertirLote = min($lote->cantidad_vendida, $cantidadRestante);
                $lote->cantidad_vendida -= $cantidadARevertirLote;
                $lote->cantidad_almacenada += $cantidadARevertirLote;
                $cantidadRestante -= $cantidadARevertirLote;
            }

            $lote->save();
        }
    }

    public function abrirModalCliente()
    {
        $this->showModalCliente = true;
        $this->resetErrorBag();
        $this->reset('nuevoCliente');
        $this->nuevoCliente['nacionalidad'] = 'Peruana';
    }

    public function cerrarModalCliente()
    {
        $this->showModalCliente = false;
    }

    
    public function redirigirAClientes()
    {
        $this->cerrarModalCliente();
        return redirect()->route('mantenimiento.clientes'); // Ajusta la ruta según tu configuración
    }

    public function render()
    {
        // Ordenar por fecha de venta descendente (las más recientes primero)
        $ventas = Ventas::with(['cliente.persona', 'estadoVenta'])
                        ->orderBy('fecha_venta', 'desc')
                        ->orderBy('id_venta', 'desc') // Para desempatar
                        ->get();

        return view('livewire.ventas.registro', [
            'ventas' => $ventas
        ]);
        //return view('livewire.ventas.registro');
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
        $this->descuentoSeleccionado = 0;
        $this->mostrarOpcionesDescuento = false;
        $this->generarCodigoVenta();
        $this->subtotal = 0;
        $this->totalImpuesto = 0;
        $this->totalGeneral = 0;
        $this->venta['descuento'] = 0;
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
