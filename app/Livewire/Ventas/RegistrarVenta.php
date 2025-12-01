<?php

namespace App\Livewire\Ventas;

use App\Models\Caja;
use App\Models\Cita;
use App\Models\EstadoCita;
use App\Models\InventarioMovimiento;
use App\Models\TipoMovimiento;
use App\Models\TransaccionPago;
use App\Models\User;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

use App\Exports\VentaConDetalleExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class RegistrarVenta extends Component
{

    use WithFileUploads;

    protected $listeners = [
        'cajaAbierta' => 'actualizarEstadoCaja',
        'cajaCerrada' => 'actualizarEstadoCaja',
        'cajaActualizada' => 'actualizarEstadoCaja'
    ];

    public $cajaContraida = false;

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
    public $citasCompletadasCliente = [];
    public $mostrarCitasCompletadas = false;
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

    public $busquedaItems = [];
    public $itemsFiltrados = [];
    public $mostrarListaItems = [];
    public $indiceActivo = null;

    public $metodosPago = [];
    public $transaccionPago = [
        'id_metodo_pago' => '',
        'monto' => 0,
        'referencia' => '',
        'estado' => 'completado',
        'fecha_pago' => null,
        'comprobante_url' => '',
        'datos_adicionales' => []
    ];

    public $cajaActual = null;
    public $bloquearVentas = false;

    public $comprobanteTemporal = null;
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

    // Manejo de compras web
    public bool $showModalTransaccion = false;
    public ?Ventas $ventaWebSeleccionada = null;

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

    public function actualizarEstadoCaja()
    {
        $this->verificarCaja();
        $this->dispatch('caja-actualizada'); // Opcional: para notificar a otros componentes
    }

    public function toggleCajaPanel()
    {
        $this->cajaContraida = !$this->cajaContraida;
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
        $this->verificarCaja();
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

        if (!isset($this->venta['descuento'])) {
            $this->venta['descuento'] = 0;
        }

        $this->opcionesDescuento = [10, 20, 30, 40, 50];
        $this->inicializarBusquedas();
        $this->cargarMetodosPago();
        $this->inicializarTransaccionPago();
        $this->citasCompletadasCliente = [];
        $this->mostrarCitasCompletadas = false;
    }

    public function cargarMetodosPago()
    {
        $this->metodosPago = \App\Models\MetodoPago::where('estado', 'activo')->get();
    }

    public function inicializarTransaccionPago()
    {
        $this->transaccionPago = [
            'id_metodo_pago' => '',
            'monto' => $this->totalGeneral,
            'referencia' => '',
            'estado' => 'completado', // Siempre completado
            'fecha_pago' => now()->format('Y-m-d\TH:i'),
            'comprobante_url' => '',
            'datos_adicionales' => []
        ];
        $this->comprobanteTemporal = null;
    }

    public function verificarCaja()
    {
        $this->cajaActual = Caja::where('id_trabajador', Auth::user()->persona->trabajador->id_trabajador)
            ->abierta()
            ->first();

        $this->bloquearVentas = !$this->cajaActual;

        // Forzar actualización de la vista
        $this->dispatch('$refresh');
    }

    public function eliminarComprobante()
    {
        $this->comprobanteTemporal = null;
    }

    public function updatedComprobanteTemporal()
    {
        $this->validate([
            'comprobanteTemporal' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:5120' // 5MB
            ]
        ], [
            'comprobanteTemporal.file' => 'El comprobante debe ser un archivo válido.',
            'comprobanteTemporal.mimes' => 'El comprobante debe ser una imagen (JPG, JPEG, PNG) o PDF.',
            'comprobanteTemporal.max' => 'El comprobante no debe pesar más de 5MB.'
        ]);
    }

    public function validarMontoPago()
    {
        $this->transaccionPago['monto'] = $this->asegurarNumero($this->transaccionPago['monto']);

        // Si el monto está vacío, establecerlo como el total general
        if (empty($this->transaccionPago['monto']) || $this->transaccionPago['monto'] == 0) {
            $this->transaccionPago['monto'] = $this->totalGeneral;
        }
    }

    public function mostrarLista($index)
    {
        // Ocultar todas las otras listas
        foreach ($this->mostrarListaItems as $i => $value) {
            if ($i != $index) {
                $this->mostrarListaItems[$i] = false;
            }
        }

        // Mostrar la lista actual y buscar items
        $this->mostrarListaItems[$index] = true;
        $this->buscarItems($index);
    }

    public function inicializarBusquedas()
    {
        foreach ($this->detalleVenta as $index => $detalle) {
            $this->busquedaItems[$index] = '';
            $this->mostrarListaItems[$index] = false; // Cambiar a false por defecto
            $this->itemsFiltrados[$index] = [];
        }
    }

    public function buscarItems($index)
    {
        $busqueda = $this->busquedaItems[$index] ?? '';
        $tipoItem = $this->detalleVenta[$index]['tipo_item'] ?? 'producto';

        if ($tipoItem === 'producto') {
            $query = Producto::where('estado', 'activo')
                ->with(['lotes' => function ($query) {
                    $query->where('estado', 'activo');
                }]);

            if (!empty($busqueda)) {
                $query->where(function ($q) use ($busqueda) {
                    $q->where('nombre_producto', 'like', '%' . $busqueda . '%')
                        ->orWhere('codigo_barras', 'like', '%' . $busqueda . '%')
                        ->orWhere('descripcion', 'like', '%' . $busqueda . '%');
                });
            }

            $productos = $query->limit(15)->get();

            $this->itemsFiltrados[$index] = $productos->filter(function ($producto) {
                return $producto->stock_actual > 0;
            })->map(function ($producto) {
                return [
                    'id' => $producto->id_producto,
                    'nombre' => $producto->nombre_producto,
                    'tipo' => 'producto',
                    'precio' => $producto->precio_unitario,
                    'stock' => $producto->stock_actual,
                    'descripcion' => $producto->descripcion,
                    'codigo_barras' => $producto->codigo_barras
                ];
            })->values()->toArray();

        } else {
            $query = Servicio::where('estado', 'activo');

            if (!empty($busqueda)) {
                $query->where(function ($q) use ($busqueda) {
                    $q->where('nombre_servicio', 'like', '%' . $busqueda . '%')
                        ->orWhere('descripcion', 'like', '%' . $busqueda . '%');
                });
            }

            $this->itemsFiltrados[$index] = $query->limit(15)->get()
                ->map(function ($servicio) {
                    return [
                        'id' => $servicio->id_servicio,
                        'nombre' => $servicio->nombre_servicio,
                        'tipo' => 'servicio',
                        'precio' => $servicio->precio_unitario,
                        'descripcion' => $servicio->descripcion,
                        'duracion' => $servicio->duracion_estimada
                    ];
                })
                ->toArray();
        }

        // NO mostrar la lista automáticamente aquí - solo actualizar los resultados
        // La lista se mostrará cuando el usuario haga clic/focus
    }


// Método para seleccionar item
    public function seleccionarItem($index, $idItem, $tipoItem)
    {
        if ($tipoItem === 'producto') {
            $producto = Producto::with(['lotes' => function ($query) {
                $query->where('estado', 'activo');
            }])->find($idItem);

            if ($producto) {
                $this->detalleVenta[$index]['id_item'] = $producto->id_producto;
                $this->detalleVenta[$index]['precio_unitario'] = $producto->precio_unitario;
                $this->busquedaItems[$index] = $producto->nombre_producto . " (Stock: {$producto->stock_actual})";

                // Validar stock inicial
                $this->validarStockProducto($index);
            }
        } else {
            $servicio = Servicio::find($idItem);
            if ($servicio) {
                $this->detalleVenta[$index]['id_item'] = $servicio->id_servicio;
                $this->detalleVenta[$index]['precio_unitario'] = $servicio->precio_unitario;
                $this->busquedaItems[$index] = $servicio->nombre_servicio;
            }
        }

        $this->mostrarListaItems[$index] = false;
        $this->calcularTotales();
    }

// Método para limpiar búsqueda
    public function limpiarBusqueda($index)
    {
        $this->busquedaItems[$index] = '';
        $this->itemsFiltrados[$index] = [];
        $this->mostrarListaItems[$index] = false;
        $this->detalleVenta[$index]['id_item'] = '';
        $this->detalleVenta[$index]['precio_unitario'] = 0;
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
        $this->verificarCitasCompletadas($idCliente);
    }

    public function verificarCitasCompletadas($idCliente)
    {
        // Primero, necesitamos saber el ID del estado "completada"
        $estadoCompletada = EstadoCita::where('nombre_estado_cita', 'completada')
            ->orWhere('nombre_estado_cita', 'finalizada')
            ->orWhere('nombre_estado_cita', 'like', '%complet%')
            ->first();

        if (!$estadoCompletada) {
            $this->citasCompletadasCliente = [];
            $this->mostrarCitasCompletadas = false;
            return;
        }

        // Buscar citas completadas del cliente
        $this->citasCompletadasCliente = Cita::where('id_cliente', $idCliente)
            ->where('id_estado_cita', $estadoCompletada->id_estado_cita)
            ->with(['serviciosCita.servicio', 'mascota'])
            ->get()
            ->filter(function ($cita) {
                // Filtrar solo las citas que tienen servicios no facturados
                return $cita->serviciosCita->filter(function ($citaServicio) {
                        return !$this->servicioYaFacturado($citaServicio->id_servicio, $citaServicio->id_cita);
                    })->count() > 0;
            });

        $this->mostrarCitasCompletadas = $this->citasCompletadasCliente->count() > 0;

        // Si hay citas completadas, agregarlas automáticamente al detalle de venta
        if ($this->mostrarCitasCompletadas) {
            $this->agregarCitasCompletadasAlDetalle();
        }
    }


    private function servicioYaFacturado($idServicio, $idCita)
    {
        // Buscar en las observaciones de las ventas si ya se menciona esta cita
        return Ventas::where('id_cliente', $this->clienteSeleccionado)
            ->whereHas('detalleVentas', function ($query) use ($idServicio) {
                $query->where('id_servicio', $idServicio)
                    ->where('tipo_item', 'servicio');
            })
            ->where('observacion', 'like', "%Cita #{$idCita}%")
            ->exists();
    }


    public function agregarCitasCompletadasAlDetalle()
    {
        foreach ($this->citasCompletadasCliente as $cita) {
            foreach ($cita->serviciosCita as $citaServicio) {
                if ($citaServicio->servicio && !$this->servicioYaFacturado($citaServicio->id_servicio, $cita->id_cita)) {

                    // Verificar si ya existe este servicio en el detalle actual
                    $existeEnDetalle = collect($this->detalleVenta)->contains(function ($detalle) use ($citaServicio, $cita) {
                        return isset($detalle['id_item']) &&
                            $detalle['id_item'] == $citaServicio->id_servicio &&
                            isset($detalle['id_cita']) &&
                            $detalle['id_cita'] == $cita->id_cita;
                    });

                    if (!$existeEnDetalle) {
                        $this->detalleVenta[] = [
                            'tipo_item' => 'servicio',
                            'id_item' => $citaServicio->id_servicio,
                            'cantidad' => $citaServicio->cantidad ?? 1,
                            'precio_unitario' => $citaServicio->precio_aplicado ?? $citaServicio->servicio->precio_unitario,
                            'precio_referencial' => $citaServicio->servicio->precio_unitario,
                            'id_cita' => $cita->id_cita,
                            'es_cita_completada' => true,
                            'mascota_nombre' => $cita->mascota->nombre_mascota ?? 'N/A',
                            'fecha_cita' => $cita->fecha_programada,
                            'descripcion_servicio' => $citaServicio->servicio->nombre_servicio,
                            'diagnostico' => $citaServicio->diagnostico,
                            'observaciones_cita' => "Cita #{$cita->id_cita} - " . ($cita->mascota->nombre_mascota ?? 'Mascota')
                        ];
                    }
                }
            }
        }

        $this->calcularTotales();

        // Notificar al usuario
        if ($this->mostrarCitasCompletadas) {
            $totalServicios = collect($this->citasCompletadasCliente)->sum(function ($cita) {
                return $cita->serviciosCita->filter(function ($cs) use ($cita) {
                    return !$this->servicioYaFacturado($cs->id_servicio, $cita->id_cita);
                })->count();
            });

            $this->dispatch('notify',
                title: 'Citas Completadas Encontradas',
                description: "Se han agregado {$totalServicios} servicio(s) de citas completadas a la venta.",
                type: 'info'
            );
        }
    }


    // Método para limpiar cliente seleccionado
    public function limpiarCliente()
    {
        $this->clienteSeleccionado = '';
        $this->filtroCliente = '';
        $this->clienteSeleccionadoFormateado = null;
        $this->citasCompletadasCliente = [];
        $this->mostrarCitasCompletadas = false;

        // Remover items que fueron agregados por citas completadas
        $this->detalleVenta = array_filter($this->detalleVenta, function ($detalle) {
            return !isset($detalle['es_cita_completada']) || !$detalle['es_cita_completada'];
        });

        // Reindexar el array
        $this->detalleVenta = array_values($this->detalleVenta);
        $this->calcularTotales();
    }

    // Modifica el método cargarClientes para que retorne los datos formateados
    public function cargarClientes()
    {
        $query = Clientes::with('persona');

        if ($this->filtroCliente) {
            $query->whereHas('persona', function ($q) {
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
        // Cargar productos activos con stock usando el accessor
        $this->productos = Producto::where('estado', 'activo')
            ->with(['lotes' => function ($query) {
                $query->where('estado', 'activo');
            }])
            ->get()
            ->filter(function ($producto) {
                return $producto->stock_actual > 0;
            })
            ->values();

        // Cargar servicios activos
        $this->servicios = Servicio::where('estado', 'activo')->get();
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
                $producto = Producto::with(['lotes' => function ($query) {
                    $query->where('estado', 'activo');
                }])->find($detalle['id_item']);

                if (!$producto || $producto->stock_actual <= 0) {
                    $this->detalleVenta[$index]['id_item'] = '';
                    $this->detalleVenta[$index]['precio_unitario'] = 0;
                    $this->detalleVenta[$index]['cantidad'] = 1;
                    $this->limpiarBusqueda($index);
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

    public function updatedDetalleVenta($value, $key)
    {
        $parts = explode('.', $key);

        // Si se cambia el tipo de item
        if (count($parts) === 3 && $parts[2] === 'tipo_item') {
            $index = $parts[1];
            $this->limpiarBusqueda($index);

            // Si cambia a servicio, establecer cantidad a 1
            if ($this->detalleVenta[$index]['tipo_item'] === 'servicio') {
                $this->detalleVenta[$index]['cantidad'] = 1;
            }
        }

        // Si se cambia el id_item manualmente (aunque no debería pasar con el nuevo sistema)
        if (count($parts) === 3 && $parts[2] === 'id_item') {
            $index = $parts[1];
            $this->cargarPrecioUnitario($index);
            $this->validarStockProducto($index); // Validar stock cuando se selecciona producto
        }

        // Si se cambia la cantidad, validar stock
        if (count($parts) === 3 && $parts[2] === 'cantidad') {
            $index = $parts[1];
            $this->validarStockProducto($index);
        }

        // Recalcular totales cuando cambie cualquier campo del detalle
        $this->calcularTotales();
    }

    public function validarStockProducto($index)
    {
        $detalle = $this->detalleVenta[$index];

        if ($detalle['tipo_item'] === 'producto' && $detalle['id_item']) {
            $producto = Producto::with(['lotes' => function ($query) {
                $query->where('estado', 'activo');
            }])->find($detalle['id_item']);

            $cantidadSolicitada = floatval($detalle['cantidad'] ?? 0);

            if ($producto && $cantidadSolicitada > 0) {
                if ($producto->stock_actual < $cantidadSolicitada) {
                    // Agregar error visual
                    $this->addError("detalleVenta.{$index}.cantidad",
                        "Stock insuficiente. Disponible: {$producto->stock_actual}");
                } else {
                    // Limpiar error si ya es válido
                    $this->resetErrorBag("detalleVenta.{$index}.cantidad");
                }
            }
        }
    }

    public function getInfoStockProducto($productoId)
    {
        $producto = Producto::with(['lotes' => function ($query) {
            $query->where('estado', 'activo');
        }])->find($productoId);

        if ($producto) {
            return [
                'stock_actual' => $producto->stock_actual,
                'nombre' => $producto->nombre_producto
            ];
        }

        return null;
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

                // La lógica de tipo_item e id_item ahora está en updatedDetalleVenta
            }
        }

        // Buscar items automáticamente cuando cambie la búsqueda o el tipo
        if (str_starts_with($property, 'busquedaItems') || str_starts_with($property, 'detalleVenta')) {
            $parts = explode('.', $property);
            if (count($parts) >= 2) {
                $index = $parts[1];
                // Si es cambio de tipo, buscar inmediatamente
                if (isset($parts[2]) && $parts[2] === 'tipo_item') {
                    $this->buscarItems($index);
                }
                // Si es cambio en búsqueda, buscar inmediatamente
                if (str_starts_with($property, 'busquedaItems')) {
                    $this->buscarItems($index);
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
            $nuevoIndex = count($this->detalleVenta);
            $this->detalleVenta[] = [
                'tipo_item' => 'producto',
                'id_item' => '',
                'cantidad' => 1,
                'precio_unitario' => 0,
                'precio_referencial' => 0
            ];

            // Inicializar arrays de búsqueda para el nuevo índice
            $this->busquedaItems[$nuevoIndex] = '';
            $this->mostrarListaItems[$nuevoIndex] = false;
            $this->itemsFiltrados[$nuevoIndex] = [];
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

        // Validar stock si es cantidad
        if ($campo === 'cantidad') {
            $this->validarStockProducto($index);
        }

        $this->calcularTotales();
    }

    public function verificarEstadoCaja()
    {
        $this->verificarCaja();
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

        if ($this->bloquearVentas) {
            $this->dispatch('notify', title: 'Caja Cerrada', description: 'No puedes registrar ventas sin una caja abierta.', type: 'error');
            return;
        }

        $this->validate([
            "clienteSeleccionado" => "required|exists:clientes,id_cliente",
            "venta.fecha_venta" => "required|date|before_or_equal:today",
            "venta.observacion" => "nullable|max:1000",
            "venta.descuento" => "nullable|numeric|min:0",
            "detalleVenta.*.id_item" => "required",
            "detalleVenta.*.cantidad" => "required|numeric|min:1",
            "detalleVenta.*.precio_unitario" => "required|numeric|min:0.01",
            "transaccionPago.id_metodo_pago" => "required|exists:metodo_pagos,id_metodo_pago",
            "transaccionPago.monto" => "required|numeric|min:0.01",
            "transaccionPago.referencia" => "nullable|string|max:100",
            "comprobanteTemporal" => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:5120'
            ],

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
            "transaccionPago.id_metodo_pago.required" => "El método de pago es obligatorio.",
            "transaccionPago.id_metodo_pago.exists" => "El método de pago seleccionado no es válido.",
            "transaccionPago.monto.required" => "El monto del pago es obligatorio.",
            "transaccionPago.monto.numeric" => "El monto debe ser un número válido.",
            "transaccionPago.monto.min" => "El monto debe ser al menos 0.01.",
            "transaccionPago.estado.required" => "El estado del pago es obligatorio.",
            "transaccionPago.estado.in" => "El estado del pago no es válido.",
            "transaccionPago.fecha_pago.date" => "La fecha de pago no es válida.",
            "transaccionPago.comprobante_url.url" => "La URL del comprobante debe ser una URL válida.",
            "comprobanteTemporal.file" => "El comprobante debe ser un archivo válido.",
            "comprobanteTemporal.mimes" => "El comprobante debe ser una imagen (JPG, JPEG, PNG) o PDF.",
            "comprobanteTemporal.max" => "El comprobante no debe pesar más de 5MB.",
        ]);
        if ($this->transaccionPago['monto'] < $this->totalGeneral) {
            $this->addError('transaccionPago.monto',
                "El monto pagado no puede ser menor al total de la venta.");
            return;
        }
        // Validación adicional para stock de productos
        foreach ($this->detalleVenta as $index => $detalle) {
            if ($detalle['tipo_item'] === 'producto') {
                $producto = Producto::with(['lotes' => function ($query) {
                    $query->where('estado', 'activo');
                }])->find($detalle['id_item']);

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
                    "id_caja" => $this->cajaActual->id_caja,
                ]);
                $comprobanteUrl = '';
                $datosAdicionales = [];
                if ($this->comprobanteTemporal) {
                    $file = $this->comprobanteTemporal;
                    $fileName = 'comprobante_' . time() . '_' . $venta->id_venta . '.' . $file->getClientOriginalExtension();

                    $filePath = $file->storeAs('comprobantes_pago', $fileName, 'public');

                    $comprobanteUrl = '/storage/' . $filePath;

                    $datosAdicionales = [
                        'archivo' => [
                            'nombre_original' => $file->getClientOriginalName(),
                            'tipo' => $file->getClientOriginalExtension(),
                            'tamaño' => $file->getSize(),
                            'ruta_almacenamiento' => $filePath,
                            'fecha_subida' => now()->toISOString(),
                        ]
                    ];
                }

                // Crear transacción de pago
                TransaccionPago::create([
                    "id_venta" => $venta->id_venta,
                    "id_metodo_pago" => $this->transaccionPago['id_metodo_pago'],
                    "monto" => floatval($this->transaccionPago['monto']),
                    "referencia" => $this->transaccionPago['referencia'],
                    "estado" => 'completado', // Siempre completado
                    "fecha_pago" => now(),
                    "comprobante_url" => $comprobanteUrl,
                    "datos_adicionales" => $datosAdicionales,
                    "fecha_registro" => now(),
                    "fecha_actualizacion" => now(),
                ]);

                if ($this->transaccionPago['estado'] == 'completado' &&
                    $this->transaccionPago['monto'] >= $this->totalGeneral) {
                    $estadoVentaCompletado = EstadoVentas::where('nombre_estado_venta_fisica', 'completado')->first();
                    if ($estadoVentaCompletado) {
                        $venta->update([
                            'id_estado_venta' => $estadoVentaCompletado->id_estado_venta_fisica
                        ]);
                    }
                }
                $observacionesConCitas = $this->venta['observacion'] ?? '';
                $citasIncluidas = [];

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
                        $this->actualizarStockProducto($detalle['id_item'], $cantidad, $venta->id_venta);
                    } else {
                        $detalleData['id_servicio'] = $detalle['id_item'];

                        // Si es una cita completada, registrar la información en observaciones
                        if (isset($detalle['es_cita_completada']) && $detalle['es_cita_completada']) {
                            $citaId = $detalle['id_cita'];
                            if (!in_array($citaId, $citasIncluidas)) {
                                $citasIncluidas[] = $citaId;

                                $infoCita = "\n• Cita #{$citaId}";
                                if (isset($detalle['mascota_nombre'])) {
                                    $infoCita .= " - Mascota: " . $detalle['mascota_nombre'];
                                }
                                if (isset($detalle['fecha_cita'])) {
                                    $infoCita .= " (" . \Carbon\Carbon::parse($detalle['fecha_cita'])->format('d/m/Y') . ")";
                                }

                                $observacionesConCitas .= $infoCita;
                            }
                        }
                    }

                    DetalleVentas::create($detalleData);
                }

                // Actualizar observaciones de la venta con información de citas
                if (!empty($citasIncluidas)) {
                    $observacionFinal = trim($observacionesConCitas);
                    if (!empty($this->venta['observacion'])) {
                        $observacionFinal = $this->venta['observacion'] . "\n\nServicios de citas:" . $observacionFinal;
                    } else {
                        $observacionFinal = "Servicios de citas:" . $observacionFinal;
                    }

                    $venta->update([
                        'observacion' => $observacionFinal
                    ]);
                }
            });

            $this->cajaActual->calcularTotales();
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

    private function registrarMovimientoInventario($lote, $cantidad, $idVenta)
    {
        // Obtener tipo de movimiento "Salida"
        // Asegúrate que en tu DB el nombre sea exacto, a veces es "Salida", "Venta" o "Egreso"
        $tipoMovimientoSalida = TipoMovimiento::where('nombre_tipo_movimiento', 'Salida')->first();

        if (!$tipoMovimientoSalida) {
            Log::error("Tipo de movimiento 'Salida' no encontrado al registrar venta.");
            return; // O lanzar excepción si es crítico
        }

        // Calcular stock resultante (lo que queda en el lote)
        $stockResultante = $lote->cantidad_almacenada + $lote->cantidad_mostrada;

        InventarioMovimiento::create([
            "id_tipo_movimiento" => $tipoMovimientoSalida->id_tipo_movimiento,
            "cantidad_movimiento" => -$cantidad, // Negativo porque es salida
            "stock_resultante" => $stockResultante,
            "id_tipo_ubicacion" => 1, // Ajustar si manejas múltiples ubicaciones (Tienda/Almacén)
            "motivo" => "Venta Presencial #" . $this->codigoVenta,
            "id_lote" => $lote->id_lote,
            // En Livewire usamos el usuario autenticado
            "id_trabajador" => Auth::user()->persona->trabajador->id_trabajador,
            "tipo_movimiento_asociado" => Ventas::class,
            "id_movimiento_asociado" => $idVenta,
            "fecha_movimiento" => now(),
        ]);
    }

    public function updatedTotalGeneral($value)
    {
        // Actualizar el monto del pago cuando cambie el total general
        if (empty($this->transaccionPago['monto']) || $this->transaccionPago['monto'] == 0) {
            $this->transaccionPago['monto'] = $this->totalGeneral;
        }
    }

    private function actualizarStockProducto($productoId, $cantidadVendida, $idVenta)
    {
        $producto = Producto::with(['lotes' => function ($query) {
            $query->where('estado', 'activo')
                ->orderBy('fecha_vencimiento', 'asc');
        }])->find($productoId);

        if (!$producto) {
            throw new \Exception("Producto no encontrado");
        }

        $cantidadRestante = $cantidadVendida;

        foreach ($producto->lotes as $lote) {
            if ($cantidadRestante <= 0) break;

            $cantidadADescontarTotalLote = 0; // Para saber cuánto descontamos en total de este lote

            // 1. Descontar de cantidad_mostrada
            if ($lote->cantidad_mostrada > 0) {
                $cantidadADescontar = min($lote->cantidad_mostrada, $cantidadRestante);
                $lote->cantidad_mostrada -= $cantidadADescontar;
                $lote->cantidad_vendida += $cantidadADescontar;
                $cantidadRestante -= $cantidadADescontar;
                $cantidadADescontarTotalLote += $cantidadADescontar;
            }

            // 2. Descontar de cantidad_almacenada
            if ($cantidadRestante > 0 && $lote->cantidad_almacenada > 0) {
                $cantidadADescontar = min($lote->cantidad_almacenada, $cantidadRestante);
                $lote->cantidad_almacenada -= $cantidadADescontar;
                $lote->cantidad_vendida += $cantidadADescontar;
                $cantidadRestante -= $cantidadADescontar;
                $cantidadADescontarTotalLote += $cantidadADescontar;
            }

            if ($cantidadADescontarTotalLote > 0) {
                $lote->save();

                $this->registrarMovimientoInventario($lote, $cantidadADescontarTotalLote, $idVenta);
            }
        }

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
                $usuarioAutenticado = User::findOrFail(Auth::id());
                $venta->id_trabajador = $usuarioAutenticado->persona->trabajador->id_trabajador;
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
        $producto = Producto::with(['lotes' => function ($query) {
            $query->where('estado', 'activo')
                ->orderBy('fecha_vencimiento', 'desc')
                ->orderBy('created_at', 'desc');
        }])->find($productoId);

        if (!$producto) return;

        $cantidadRestante = $cantidadARevertir;

        foreach ($producto->lotes as $lote) {
            if ($cantidadRestante <= 0) break;

            // Calcular cuánto podemos revertir en este lote
            $cantidadMaximaRevertir = $lote->cantidad_vendida;
            $cantidadARevertirLote = min($cantidadMaximaRevertir, $cantidadRestante);

            if ($cantidadARevertirLote > 0) {
                // Revertir primero a cantidad_almacenada
                $lote->cantidad_vendida -= $cantidadARevertirLote;
                $lote->cantidad_almacenada += $cantidadARevertirLote;
                $cantidadRestante -= $cantidadARevertirLote;

                $lote->save();
            }
        }

        // Si después de revertir en todos los lotes aún queda cantidad, es un error
        if ($cantidadRestante > 0) {
            Log::error("No se pudo revertir completamente el stock para producto {$productoId}. Faltan: {$cantidadRestante}");
        }
    }

    #[\Livewire\Attributes\On('revisar-venta-web')]
    public function revisarVentaWeb(int $rowId): void
    {
        $this->abrirModalTransaccion($rowId);
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
        $this->inicializarTransaccionPago();
        $this->comprobanteTemporal = null;
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
        try {
            $ventas = Ventas::with([
                'cliente.persona',
                'detalleVentas.producto',
                'detalleVentas.servicio',
                'estadoVenta',
                'transaccionPago.metodoPago',
                'trabajador.persona'
            ])->orderBy('fecha_venta', 'desc')->get();

            // Verificar si hay ventas
            if ($ventas->isEmpty()) {
                $this->dispatch('notify',
                    title: 'Información',
                    description: 'No hay ventas para exportar',
                    type: 'info'
                );
                return;
            }

            $data = [
                'ventas' => $ventas,
                'IGV' => $this->IGV,
                'fecha_reporte' => now()->format('d/m/Y H:i:s')
            ];

            $pdf = Pdf::loadView('exports.ventas_pdf', $data)
                ->setPaper('a4', 'landscape')
                ->setOptions([
                    'defaultFont' => 'sans-serif',
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true
                ]);

            return response()->streamDownload(
                fn() => print($pdf->output()),
                'reporte_general_ventas_' . now()->format('Y_m_d_His') . '.pdf'
            );

        } catch (\Exception $e) {
            Log::error('Error al exportar PDF: ' . $e->getMessage());
            $this->dispatch('notify',
                title: 'Error',
                description: 'Error al generar el PDF: ' . $e->getMessage(),
                type: 'error'
            );
        }
    }

    public function exportarPdfVenta()
    {
        if (!$this->ventaSeleccionada) {
            $this->dispatch('notify',
                title: 'Error',
                description: 'No se ha seleccionado ninguna venta',
                type: 'error'
            );
            return;
        }

        try {
            $venta = Ventas::with([
                'cliente.persona',
                'detalleVentas.producto',
                'detalleVentas.servicio',
                'estadoVenta',
                'transaccionPago.metodoPago',
                'trabajador.persona'
            ])->find($this->ventaSeleccionada->id_venta);

            if (!$venta) {
                $this->dispatch('notify',
                    title: 'Error',
                    description: 'Venta no encontrada',
                    type: 'error'
                );
                return;
            }

            $data = [
                'venta' => $venta, // ✅ Variable SINGULAR para venta individual
                'IGV' => $this->IGV,
                'fecha_emision' => now()->format('d/m/Y H:i:s')
            ];

            $pdf = Pdf::loadView('exports.venta_individual_pdf', $data) // ✅ Nueva vista
            ->setPaper('a4', 'portrait')
                ->setOptions([
                    'defaultFont' => 'sans-serif',
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true
                ]);

            return response()->streamDownload(
                fn() => print($pdf->output()),
                'comprobante_venta_' . ($venta->codigo ?? $venta->id_venta) . '_' . now()->format('Y_m_d') . '.pdf'
            );

        } catch (\Exception $e) {
            Log::error('Error al exportar PDF de venta: ' . $e->getMessage());
            $this->dispatch('notify',
                title: 'Error',
                description: 'Error al generar el PDF: ' . $e->getMessage(),
                type: 'error'
            );
        }
    }

    public function descargarComprobanteVenta($idVenta)
    {
        $venta = Ventas::with('transaccionPago')->find($idVenta);

        if ($venta && $venta->transaccionPago && $venta->transaccionPago->comprobante_url) {
            // Extraer el path del storage
            $path = str_replace('/storage/', '', $venta->transaccionPago->comprobante_url);

            if (Storage::disk('public')->exists($path)) {
                return Storage::disk('public')->download($path);
            }
        }

        $this->dispatch('notify', title: 'Error', description: 'Comprobante no disponible', type: 'error');
    }

    // Método para abrir el modal de transacción web
    public function abrirModalTransaccion($idVenta)
    {
        $this->ventaWebSeleccionada = Ventas::with([
            'cliente.persona',
            'transaccionPago.metodoPago',
            'detalleVentas.producto',
            'detalleVentas.servicio',
            'estadoVenta'
        ])->find($idVenta);

        if ($this->ventaWebSeleccionada && $this->ventaWebSeleccionada->tipo_venta === 'web') {
            $this->transaccionPago = $this->ventaWebSeleccionada->transaccionPago;
            $this->showModalTransaccion = true;
        }
    }

// Método para aprobar venta web
    public function aprobarVentaWeb()
    {
        try {
            DB::transaction(function () {
                $venta = $this->ventaWebSeleccionada;
                $transaccion = $this->transaccionPago;

                // Cambiar estado de la transacción a confirmado
                $transaccion->update([
                    'estado' => 'completado',
                    'fecha_pago' => now(),
                ]);

                // Cambiar estado de la venta a completado
                $estadoCompletado = EstadoVentas::where('nombre_estado_venta_fisica', 'completado')->first();
                $usuarioAutenticado = User::findOrFail(Auth::id());

                if ($estadoCompletado) {
                    $venta->update([
                        'id_estado_venta' => $estadoCompletado->id_estado_venta_fisica,
                        "id_trabajador" => $usuarioAutenticado->persona->trabajador->id_trabajador
                    ]);
                }

                // El stock ya fue reducido al crear la venta, no es necesario hacer nada más
            });

            $this->actualizarEstadisticas();
            $this->dispatch('notify', title: 'Success', description: 'Venta web aprobada correctamente ✅', type: 'success');
            $this->cerrarModalTransaccion();
            $this->dispatch('ventasUpdated');

        } catch (\Exception $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error al aprobar la venta: ' . $e->getMessage(), type: 'error');
        }
    }

// Método para rechazar venta web (anular)
    public function rechazarVentaWeb()
    {
        try {
            DB::transaction(function () {
                $venta = $this->ventaWebSeleccionada;
                $transaccion = $this->transaccionPago;

                // Cambiar estado de la transacción a rechazado
                $transaccion->update([
                    'estado' => 'refundido',
                ]);

                // Cambiar estado de la venta a cancelado
                $estadoCancelado = EstadoVentas::where('nombre_estado_venta_fisica', 'cancelado')->first();
                if ($estadoCancelado) {
                    $venta->update([
                        'id_estado_venta' => $estadoCancelado->id_estado_venta_fisica
                    ]);
                }

                // REVERTIR STOCK - importante!
                foreach ($venta->detalleVentas as $detalle) {
                    if ($detalle->tipo_item === 'producto' && $detalle->producto) {
                        $this->revertirStockProducto($detalle->producto->id_producto, $detalle->cantidad);
                    }
                }
            });

            $this->actualizarEstadisticas();
            $this->dispatch('notify', title: 'Success', description: 'Venta web rechazada y stock revertido ✅', type: 'success');
            $this->cerrarModalTransaccion();
            $this->dispatch('ventasUpdated');

        } catch (\Exception $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error al rechazar la venta: ' . $e->getMessage(), type: 'error');
        }
    }

// Método para cerrar el modal
    public function cerrarModalTransaccion()
    {
        $this->showModalTransaccion = false;
        $this->ventaWebSeleccionada = null;
        $this->transaccionPago = null;
    }

// Método para descargar comprobante
    public function descargarComprobante($idTransaccion)
    {
        $transaccion = TransaccionPago::find($idTransaccion);

        if ($transaccion && $transaccion->comprobante_url) {
            // Extraer el path del storage
            $path = str_replace('/storage/', '', $transaccion->comprobante_url);

            if (Storage::disk('public')->exists($path)) {
                return Storage::disk('public')->download($path);
            }
        }

        $this->dispatch('notify', title: 'Error', description: 'Comprobante no disponible', type: 'error');
    }

    public function getProductosProperty()
    {
        return \App\Models\Producto::where('estado', 'activo')
            ->select('id_producto', 'nombre_producto', 'stock_actual', 'precio_unitario')
            ->get();
    }

    public function getServiciosProperty()
    {
        return \App\Models\Servicio::where('estado', 'activo')
            ->select('id_servicio', 'nombre_servicio', 'precio_unitario')
            ->get();
    }

    public function getNombreProducto($idProducto)
    {
        $producto = Producto::find($idProducto);
        return $producto ? $producto->nombre_producto : 'Producto no encontrado';
    }

    public function getNombreServicio($idServicio)
    {
        $servicio = Servicio::find($idServicio);
        return $servicio ? $servicio->nombre_servicio : 'Servicio no encontrado';
    }

    public function updatedTransaccionPagoIdMetodoPago($value)
    {
        if ($value) {
            $metodoPago = \App\Models\MetodoPago::find($value);
            if ($metodoPago) {
                $esEfectivo = strtolower($metodoPago->nombre_metodo) === 'efectivo' ||
                    str_contains(strtolower($metodoPago->nombre_metodo), 'contra entrega');

                if (!$esEfectivo) {
                    // Para métodos no efectivo, establecer el monto automáticamente
                    $this->transaccionPago['monto'] = $this->totalGeneral;
                } else {
                    // Para efectivo, resetear el monto si estaba previamente establecido
                    if (isset($this->transaccionPago['monto']) && $this->transaccionPago['monto'] == $this->totalGeneral) {
                        $this->transaccionPago['monto'] = 0;
                    }
                }
            }
        }
    }

}
