<x-panel title="Gestión de Ventas" :breadcrumbs="[
    ['label' => 'Inicio', 'href' => '/', 'icon' => 'home'],
    ['label' => 'Ventas', 'href' => '#'],
    ['label' => 'Registro de ventas'],
]">
    <div class="grid grid-cols-4 gap-4 mb-4">
        <x-card>
            <div class="h-[100px] flex flex-col justify-between">
                <div class="flex justify-between items-center">
                    <p>Ventas pendientes</p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-clock-icon lucide-clock">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
                <p class="font-medium text-3xl">{{ $cantVentasPendientes }}</p>
            </div>
        </x-card>
        <x-card>
            <div class="h-[100px] flex flex-col justify-between">
                <div class="flex justify-between items-center">
                    <p>Ventas completadas</p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-check-circle-icon lucide-check-circle">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                </div>
                <p class="font-medium text-3xl">{{ $cantVentasCompletadas }}</p>
            </div>
        </x-card>
        <x-card>
            <div class="h-[100px] flex flex-col justify-between">
                <div class="flex justify-between items-center">
                    <p>Ventas canceladas</p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-x-circle-icon lucide-x-circle">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="m15 9-6 6"/>
                        <path d="m9 9 6 6"/>
                    </svg>
                </div>
                <p class="font-medium text-3xl">{{ $cantVentasCanceladas }}</p>
            </div>
        </x-card>
        <x-card>
            <div class="h-[100px] flex flex-col justify-between">
                <div class="flex justify-between items-center">
                    <p>Total recaudado</p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-dollar-sign-icon lucide-dollar-sign">
                        <line x1="12" x2="12" y1="2" y2="22"/>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                    </svg>
                </div>
                <p class="font-medium text-3xl">S/ {{ number_format($totalVentasCompletadas, 2) }}</p>
            </div>
        </x-card>
    </div>
    <x-card>
        @if (session()->has('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="opacity-0 transform translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-500"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform translate-y-2"
                class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded" x-data="{ show: true }"
                x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="opacity-0 transform translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-500"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform translate-y-2">
                {{ session('error') }}
            </div>
        @endif
        <div class="flex justify-between mb-5">
            <div>
                <p class="font-medium text-gray-600 text-xl">Registro de Ventas</p>
                <p class="font-medium text-gray-600 text-sm">Gestiona las ventas de productos y servicios</p>
            </div>
            <div class="flex gap-2">
                <x-exports />
                <button wire:click="openModal"
                    class="inline-flex items-center gap-2 px-4 py-2 h-10 bg-blue-600 hover:bg-blue-700 transition text-white rounded-lg font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-plus">
                        <path d="M5 12h14"/>
                        <path d="M12 5v14"/>
                    </svg>
                    Nueva venta
                </button>
            </div>
        </div>

        <livewire:ventas-table />

    </x-card>

    <div>
        @if ($showModal)
            <div class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
                <div class="bg-white rounded-xl shadow-xl w-2/3 p-6 space-y-4 max-h-[90vh] overflow-y-auto">
                    <div>
                        <h2 class="text-xl font-medium mb-1">Registrar Nueva Venta</h2>
                        <p class="text-sm font-medium">Completa la información de la venta y agrega los productos/servicios</p>
                    </div>
                    <form action="" class="space-y-4 w-full" wire:submit.prevent="guardar">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex gap-2 flex-col">
                                <label>Código de venta</label>
                                <input wire:model="codigoVenta" type="text" readonly
                                    class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200 bg-gray-50">
                            </div>
                            <div class="flex gap-2 flex-col">
                                <label>Seleccione un cliente / Registre un cliente</label>
                                <button type="button" 
                                        wire:click="redirigirAClientes"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-purple-500 hover:bg-purple-600 text-white rounded transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                    </svg>
                                    Registro Completo de Clientes
                                </button>
                                <select wire:model.live="clienteSeleccionado" id="cliente"
                                    class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200">
                                    <option value="">Seleccione un cliente</option>
                                    @foreach ($clientes as $cliente)
                                        <option value="{{ $cliente->id_cliente }}">
                                            @if($cliente->persona)
                                                {{ $cliente->persona->nombre }} {{ $cliente->persona->apellido_paterno }} (DNI: {{ $cliente->persona->numero_documento }})
                                                <!-- {{ $cliente->persona->nombres }} {{ $cliente->persona->nombre ?? '' }} -->
                                            @else
                                                Cliente #{{ $cliente->id_cliente }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('clienteSeleccionado')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="flex gap-2 flex-col">
                                <label for="fechaVenta">Fecha de venta</label>
                                <input wire:model="venta.fecha_venta" type="date" id="fechaVenta"
                                    name="fechaVenta"
                                    class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200">
                                @error('venta.fecha_venta')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="flex gap-2 flex-col">
                                <label for="descuento">Descuento (S/)</label>
                                <input wire:model.live="venta.descuento" type="number" min="0" step="0.01" id="descuento"
                                    name="descuento"
                                    class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200">
                                @error('venta.descuento')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Filtros para Productos -->
                        <div class="grid grid-cols-3 gap-4 mb-4" id="filtros-productos">
                            <!-- Filtro de Categoría para Productos -->
                            <div class="flex gap-2 flex-col">
                                <label class="text-sm font-medium text-gray-600">Filtrar Productos por Categoría</label>
                                <select wire:model.live="categoriaProductoSeleccionada"
                                    class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200 text-sm">
                                    <option value="">Todas las categorías</option>
                                    @foreach ($categoriasProductos as $categoria)
                                        <option value="{{ $categoria->id_categoria_producto }}">
                                            {{ $categoria->nombre_categoria_producto }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filtro de Categoría para Servicios -->
                            <div class="flex gap-2 flex-col">
                                <label class="text-sm font-medium text-gray-600">Filtrar Servicios por Categoría</label>
                                <select wire:model.live="categoriaServicioSeleccionada"
                                    class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200 text-sm">
                                    <option value="">Todas las categorías</option>
                                    @foreach ($categoriasServicios as $categoria)
                                        <option value="{{ $categoria->id_categoria_servicio }}">
                                            {{ $categoria->nombre_categoria_servicio }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filtro de Proveedor -->
                            <div class="flex gap-2 flex-col">
                                <label class="text-sm font-medium text-gray-600">Filtrar por Proveedor</label>
                                <select wire:model.live="proveedorSeleccionado"
                                    class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200 text-sm">
                                    <option value="">Todos los proveedores</option>
                                    @foreach ($proveedores as $proveedor)
                                        <option value="{{ $proveedor->id_proveedor }}">
                                            {{ $proveedor->nombre_proveedor }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>




                        <!-- Detalles de Productos/Servicios -->
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <p class="font-medium text-xl">Productos y Servicios</p>
                                <button type="button" wire:click="agregarDetalle"
                                    class="inline-flex items-center gap-1 px-3 py-1 bg-green-500 hover:bg-green-600 text-white rounded text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus">
                                        <path d="M5 12h14"/>
                                        <path d="M12 5v14"/>
                                    </svg>
                                    Agregar item
                                </button>
                            </div>

                            <div class="max-h-[300px] overflow-y-auto space-y-2">
                                @foreach ($detalleVenta as $index => $detalle)
                                    <div class="grid grid-cols-12 gap-2 items-end p-2 border rounded-lg bg-gray-50">
                                        <!-- Tipo de Item -->
                                        <div class="col-span-2 flex flex-col">
                                            <label class="text-sm font-medium text-gray-600">Tipo</label>
                                            <select wire:model.live="detalleVenta.{{ $index }}.tipo_item"
                                                class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200 text-sm">
                                                <option value="producto">Producto</option>
                                                <option value="servicio">Servicio</option>
                                            </select>
                                        </div>

                                        <!-- Producto/Servicio -->
                                        <div class="col-span-3 flex flex-col">
                                            <label class="text-sm font-medium text-gray-600">
                                                @if($detalle['tipo_item'] == 'producto')
                                                    Producto
                                                @else
                                                    Servicio
                                                @endif
                                            </label>
                                            <select wire:model.live="detalleVenta.{{ $index }}.id_item"
                                                class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200 text-sm">
                                                <option value="">Seleccione...</option>
                                                @if($detalle['tipo_item'] == 'producto')
                                                    @foreach ($productos as $producto)
                                                        <option value="{{ $producto->id_producto }}">
                                                            {{ $producto->nombre_producto }}
                                                            @if($producto->stock_actual > 0)
                                                                (Stock: {{ $producto->stock_actual }})
                                                            @else
                                                                (Sin stock)
                                                            @endif
                                                        </option>
                                                    @endforeach
                                                @else
                                                    @foreach ($servicios as $servicio)
                                                        <option value="{{ $servicio->id_servicio }}">
                                                            {{ $servicio->nombre_servicio }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            @error('detalleVenta.' . $index . '.id_item')
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <!-- Cantidad -->
                                        <div class="col-span-2 flex flex-col">
                                            <label class="text-sm font-medium text-gray-600">Cantidad</label>
                                            <input type="text"
                                                   wire:model.live="detalleVenta.{{ $index }}.cantidad"
                                                   wire:keydown.debounce.500ms="validarYCast('cantidad', {{ $index }})"
                                                   @if($detalle['tipo_item'] == 'servicio') disabled @endif
                                                   pattern="[0-9]*"
                                                   inputmode="numeric"
                                                   class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200 text-sm @if($detalle['tipo_item'] == 'servicio') bg-gray-100 @endif"
                                                   placeholder="1">
                                            @error('detalleVenta.' . $index . '.cantidad')
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <!-- Precio Unitario con Tooltip -->
                                        <div class="col-span-2 flex flex-col">
                                            <label class="text-sm font-medium text-gray-600">
                                                Precio Unit.
                                                @if($detalle['tipo_item'] == 'servicio' && $detalle['id_item'])
                                                    @php
                                                        $servicioSeleccionado = \App\Models\Servicio::find($detalle['id_item']);
                                                        $precioReferencial = $servicioSeleccionado ? $servicioSeleccionado->precio : 0;
                                                        $precioActual = $detalle['precio_unitario'] ?? 0;
                                                        $esPrecioModificado = $precioReferencial > 0 && $precioActual != $precioReferencial;
                                                    @endphp
                                                    @if($precioReferencial > 0)
                                                        <span class="text-xs font-normal block {{ $esPrecioModificado ? 'text-yellow-600' : 'text-green-600' }}"
                                                            title="{{ $esPrecioModificado ? 'Precio modificado del valor referencial' : 'Precio referencial del servicio' }}">
                                                            Ref: S/ {{ number_format($precioReferencial, 2) }}
                                                            @if($esPrecioModificado)
                                                                • 💰 Modificado
                                                            @endif
                                                        </span>
                                                    @endif
                                                @endif
                                            </label>

                                            <div class="relative">
                                                <input type="text"
                                                       wire:model.live="detalleVenta.{{ $index }}.precio_unitario"
                                                       wire:keydown.debounce.500ms="validarYCast('precio_unitario', {{ $index }})"
                                                       pattern="[0-9.]*"
                                                       inputmode="decimal"
                                                       class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 text-sm transition-all duration-300 w-full
        @if($detalle['tipo_item'] == 'servicio' && $detalle['id_item'])
            @php
                $servicio = \App\Models\Servicio::find($detalle['id_item']);
                $precioRef = $servicio ? $servicio->precio : 0;
                $precioAct = $detalle['precio_unitario'] ?? 0;
                $modificado = $precioRef > 0 && $precioAct != $precioRef;
            @endphp
            @if($modificado)
                bg-yellow-50 border-yellow-400 text-yellow-700 font-medium shadow-sm
            @else
                bg-blue-50 border-blue-300
            @endif
        @else
            border-gray-200
        @endif"
                                                       placeholder="0.00">

                                                @if($detalle['tipo_item'] == 'servicio' && $detalle['id_item'])
                                                    @php
                                                        $servicio = \App\Models\Servicio::find($detalle['id_item']);
                                                        $precioRef = $servicio ? $servicio->precio : 0;
                                                        $precioAct = $detalle['precio_unitario'] ?? 0;
                                                        $modificado = $precioRef > 0 && $precioAct != $precioRef;
                                                    @endphp
                                                    @if($modificado)
                                                        <div class="absolute right-2 top-1/2 transform -translate-y-1/2">
                                                            <span class="text-yellow-500 text-sm" title="Precio modificado del valor referencial">
                                                                💰
                                                            </span>
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>

                                            @error('detalleVenta.' . $index . '.precio_unitario')
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <!-- Subtotal -->
                                        <div class="col-span-2 flex flex-col">
                                            <label class="text-sm font-medium text-gray-600">Subtotal</label>
                                            <input type="text"
                                                readonly
                                                value="S/ {{ number_format(($detalle['cantidad'] ?? 0) * ($detalle['precio_unitario'] ?? 0), 2) }}"
                                                class="border rounded px-2 py-1 bg-gray-100 border-gray-200 text-sm font-medium">
                                        </div>

                                        <!-- Botón Eliminar -->
                                        <div class="col-span-1 flex justify-center">
                                            @if (count($detalleVenta) > 1)
                                                <button type="button"
                                                    wire:click="eliminarDetalle({{ $index }})"
                                                    class="bg-red-500 hover:bg-red-600 text-white rounded-md p-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash">
                                                        <path d="M3 6h18"/>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/>
                                                        <path d="M10 11v6"/>
                                                        <path d="M14 11v6"/>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Resumen de Totales -->
                        <div class="grid grid-cols-4 gap-4 p-4 bg-blue-50 rounded-lg" wire:transition>
                            <div class="text-center">
                                <p class="text-sm font-medium text-gray-600">Subtotal</p>
                                <p class="text-lg font-bold">S/ {{ number_format($subtotal, 2) }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm font-medium text-gray-600">Descuento</p>
                                <p class="text-lg font-bold text-red-600">- S/ {{ number_format($venta['descuento'] ?? 0, 2) }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm font-medium text-gray-600">IGV (18%)</p>
                                <p class="text-lg font-bold">S/ {{ number_format($totalImpuesto, 2) }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm font-medium text-gray-600">Total General</p>
                                <p class="text-lg font-bold text-green-600">S/ {{ number_format($totalGeneral, 2) }}</p>
                            </div>
                        </div>

                        <div class="flex gap-2 flex-col">
                            <label for="observacion">Observaciones</label>
                            <textarea wire:model="venta.observacion" name="observacion" id="observacion" rows="3" maxlength="1000"
                                class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200 resize-none"
                                placeholder="Observaciones adicionales sobre la venta..."></textarea>
                        </div>

                        <div class="flex justify-end mt-4 gap-4 items-center">
                            <button type="button" wire:click="closeModal"
                                class="bg-gray-500 hover:bg-gray-700 text-white px-6 py-2 rounded-md transition">
                                Cancelar
                            </button>
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md transition">
                                Registrar Venta
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>

    <!-- Modal de Detalle de Venta -->
    <div x-data x-init="$watch('$wire.showModalDetalle', value => {
        if (value) { document.body.classList.add('overflow-hidden') } else { document.body.classList.remove('overflow-hidden') }
    })">
        @if ($showModalDetalle && $ventaSeleccionada)
            <div class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
                <div class="bg-white rounded-xl shadow-xl w-1/3 p-6 max-h-[90vh] overflow-y-auto">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <p class="text-xl font-medium">Detalles de Venta</p>
                            <p class="text-sm font-medium">Información completa de la venta {{ $ventaSeleccionada->codigo ?? 'N/A' }}</p>
                        </div>
                        <div class="flex gap-2">
                            <button title="Descargar Comprobante" wire:click="exportarPdfVenta"
                                class="bg-gray-500 hover:bg-gray-700 text-white px-3 py-2 rounded-md">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text">
                                    <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/>
                                    <path d="M14 2v4a2 2 0 0 0 2 2h4"/>
                                    <path d="M10 9H8"/>
                                    <path d="M16 13H8"/>
                                    <path d="M16 17H8"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="font-medium text-sm text-gray-600">Código de venta</label>
                            <p class="text-gray-800">{{ $ventaSeleccionada->codigo ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="font-medium text-sm text-gray-600">Fecha de venta</label>
                            <p class="text-gray-800">{{ $ventaSeleccionada->fecha_venta }}</p>
                        </div>
                        <div>
                            <label class="font-medium text-sm text-gray-600">Cliente</label>
                            <p class="text-gray-800">
                                @if($ventaSeleccionada->cliente && $ventaSeleccionada->cliente->persona)
                                    {{ $ventaSeleccionada->cliente->persona->nombres }}
                                    {{ $ventaSeleccionada->cliente->persona->apellido_paterno ?? '' }}
                                @else
                                    Cliente no disponible
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="font-medium text-sm text-gray-600">Estado</label>
                            <p class="text-gray-800 capitalize">{{ $ventaSeleccionada->estadoVenta->nombre_estado_venta_fisica ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="font-medium text-sm text-gray-600">Vendedor</label>
                            <p class="text-gray-800">
                                @if($ventaSeleccionada->trabajador && $ventaSeleccionada->trabajador->persona)
                                    {{ $ventaSeleccionada->trabajador->persona->nombres }}
                                    {{ $ventaSeleccionada->trabajador->persona->apellido_paterno ?? '' }}
                                @else
                                    Vendedor no disponible
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="font-medium text-sm text-gray-600">Fecha de registro</label>
                            <p class="text-gray-800">{{ $ventaSeleccionada->fecha_registro }}</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <p class="font-medium text-md mb-2">Detalles de Productos/Servicios</p>
                        <div class="border rounded-lg overflow-hidden">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="text-left p-2 font-medium">Item</th>
                                        <th class="text-left p-2 font-medium">Tipo</th>
                                        <th class="text-left p-2 font-medium">Cant.</th>
                                        <th class="text-left p-2 font-medium">P. Unit.</th>
                                        <th class="text-left p-2 font-medium">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ventaSeleccionada->detalleVentas as $detalle)
                                        <tr class="border-t">
                                            <td class="p-2">
                                                @if($detalle->tipo_item == 'producto')
                                                    {{ $detalle->producto->nombre_producto ?? 'N/A' }}
                                                @else
                                                    {{ $detalle->servicio->nombre_servicio ?? 'N/A' }}
                                                @endif
                                            </td>
                                            <td class="p-2 capitalize">{{ $detalle->tipo_item }}</td>
                                            <td class="p-2">{{ $detalle->cantidad }}</td>
                                            <td class="p-2">S/ {{ number_format($detalle->precio_unitario, 2) }}</td>
                                            <td class="p-2">S/ {{ number_format($detalle->subtotal, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="space-y-2 bg-gray-50 p-4 rounded-lg">
                        <div class="flex justify-between">
                            <span class="font-medium">Subtotal:</span>
                            <span>S/ {{ number_format($ventaSeleccionada->subtotal, 2) }}</span>
                        </div>
                        @if($ventaSeleccionada->descuento > 0)
                        <div class="flex justify-between text-red-600">
                            <span class="font-medium">Descuento:</span>
                            <span>- S/ {{ number_format($ventaSeleccionada->descuento, 2) }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="font-medium">IGV (18%):</span>
                            <span>S/ {{ number_format($ventaSeleccionada->impuesto, 2) }}</span>
                        </div>
                        <div class="flex justify-between border-t pt-2 font-bold text-lg">
                            <span>Total:</span>
                            <span>S/ {{ number_format($ventaSeleccionada->total, 2) }}</span>
                        </div>
                    </div>

                    @if($ventaSeleccionada->observacion)
                    <div class="mt-4">
                        <label class="font-medium text-sm text-gray-600">Observaciones</label>
                        <p class="text-gray-800 bg-yellow-50 p-3 rounded-lg mt-1">{{ $ventaSeleccionada->observacion }}</p>
                    </div>
                    @endif

                    <!-- Botones de Acción -->
                    <div class="flex justify-end gap-2 mt-6">
                        @if($ventaSeleccionada->estadoVenta->nombre_estado_venta_fisica == 'pendiente')
                            <button wire:click="completarVenta"
                                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md text-sm">
                                Completar Venta
                            </button>
                            <button wire:click="cancelarVenta"
                                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md text-sm">
                                Cancelar Venta
                            </button>
                        @endif
                        <button wire:click="$set('showModalDetalle', false)"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            Livewire.on('notify', (data) => {
                Swal.fire({
                    title: data.title,
                    text: data.description,
                    icon: data.type,
                    timer: 2500,
                    showConfirmButton: false,
                    customClass: {
                        popup: 'rounded-lg',
                        title: 'text-lg font-semibold',
                        htmlContainer: 'text-sm'
                    }
                });
            });

            Livewire.on('ventasUpdated', () => {
                // Recargar datos si es necesario
            });
        </script>
    @endpush
    <x-loader />
</x-panel>
