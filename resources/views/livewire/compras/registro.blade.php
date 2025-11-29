<x-panel title="Gestión de Compras" :breadcrumbs="[
    ['label' => 'Inicio', 'href' => '/', 'icon' => 'home'],
    ['label' => 'Compras', 'href' => '#'],
    ['label' => 'Registro de compras'],
]">
    <div class="grid grid-cols-4 gap-4 mb-4">
        <x-card>
            <div class="h-[100px] flex flex-col justify-between">
                <div class="flex justify-between items-center">
                    <p>Órdenes pendientes</p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="lucide lucide-clipboard-clock-icon lucide-clipboard-clock">
                        <path d="M16 14v2.2l1.6 1"/>
                        <path d="M16 4h2a2 2 0 0 1 2 2v.832"/>
                        <path d="M8 4H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h2"/>
                        <circle cx="16" cy="16" r="6"/>
                        <rect x="8" y="2" width="8" height="4" rx="1"/>
                    </svg>
                </div>
                <p class="font-medium text-3xl">{{ $cantOrdenesPendientes }}</p>
            </div>
        </x-card>
        <x-card>
            <div class="h-[100px] flex flex-col justify-between">
                <div class="flex justify-between items-center">
                    <p>Órdenes aprobadas</p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round" class="lucide lucide-check-icon lucide-check">
                        <path d="M20 6 9 17l-5-5"/>
                    </svg>
                </div>
                <p class="font-medium text-3xl">{{ $cantOrdenesAprobadas }}</p>
            </div>
        </x-card>
        <x-card>
            <div class="h-[100px] flex flex-col justify-between">
                <div class="flex justify-between items-center">
                    <p>Órdenes recibidas</p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round" class="lucide lucide-package-icon lucide-package">
                        <path
                            d="M11 21.73a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73z"/>
                        <path d="M12 22V12"/>
                        <polyline points="3.29 7 12 12 20.71 7"/>
                        <path d="m7.5 4.27 9 5.15"/>
                    </svg>
                </div>
                <p class="font-medium text-3xl">{{ $cantOrdenesRecibidas }}</p>
            </div>
        </x-card>
        <x-card>
            <div class="h-[100px] flex flex-col justify-between">
                <div class="flex justify-between items-center">
                    <p>Precio total compras recibidas</p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round" class="lucide lucide-dollar-sign-icon lucide-dollar-sign">
                        <line x1="12" x2="12" y1="2" y2="22"/>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                    </svg>
                </div>
                <p class="font-medium text-3xl">PEN{{ $precioCompraTotal }}</p>
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
                 x-show="show" x-init="setTimeout(() => show = false, 4000)"
                 x-transition:enter="transition ease-out duration-500"
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
                <p class="font-medium text-gray-600 text-xl">Órdenes de Compra</p>
                <p class="font-medium text-gray-600 text-sm">Gestiona las órdenes de compra a proveedores</p>
            </div>
            <div class="flex gap-2">
                <x-exports/>
                <button wire:click="openModal"
                        class="inline-flex items-center gap-2 px-4 py-2 h-10 bg-gray-500 hover:bg-gray-700 transition text-white rounded-lg font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round" class="lucide lucide-plus">
                        <path d="M5 12h14"/>
                        <path d="M12 5v14"/>
                    </svg>
                    Nueva orden
                </button>
            </div>
        </div>

        <livewire:compras-table/>

    </x-card>

    <div>
        @if ($showModal)
            <div class="fixed inset-0 flex items-center justify-center bg-black/50 z-50 p-4">
                <div class="bg-white rounded-xl shadow-xl w-full max-w-5xl max-h-[90vh] flex flex-col">

                    <!-- Header -->
                    <div
                        class="p-5 border-b border-gray-100 flex justify-between items-center bg-white rounded-t-xl flex-shrink-0">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">Crear Nueva Orden de Compra</h2>
                            <p class="text-sm text-gray-500">Complete los datos generales y detalle de productos</p>
                        </div>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 p-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="guardar" class="flex flex-col flex-1 min-h-0">

                        <!-- Datos Generales (Fijo) -->
                        <div class="p-6 pb-4 border-b border-dashed border-gray-200 bg-gray-50/50 flex-shrink-0">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Columna Izquierda -->
                                <div class="space-y-4">
                                    <div class="flex flex-col gap-1">
                                        <label class="text-xs font-bold text-gray-500 uppercase">Número de Orden</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2.5 text-gray-400">#</span>
                                            <input wire:model="codigoOrden" type="text" readonly
                                                   class="w-full pl-7 border rounded-lg px-3 py-2 bg-white text-gray-700 font-mono text-sm focus:outline-none border-gray-300 shadow-sm">
                                        </div>
                                    </div>

                                    <div class="flex flex-col gap-1 relative">
                                        <label class="text-xs font-bold text-gray-500 uppercase">Proveedor</label>
                                        <div class="relative">
                                            <input type="text"
                                                   wire:model.live.debounce.300ms="busquedaProveedor"
                                                   placeholder="Buscar proveedor..."
                                                   class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300 text-sm shadow-sm"
                                                   autocomplete="off">
                                            @if($proveedorSeleccionado)
                                                <button type="button" wire:click="limpiarProveedor"
                                                        class="absolute right-2 top-2 text-gray-400 hover:text-red-500 p-1">
                                                    ✕
                                                </button>
                                            @endif
                                        </div>
                                        @if($mostrarListaProveedores && !empty($busquedaProveedor))
                                            <div
                                                class="absolute z-50 w-full bg-white border border-gray-200 rounded-lg shadow-xl max-h-48 overflow-y-auto mt-1 top-full">
                                                @if(count($proveedoresFiltrados) > 0)
                                                    <ul>
                                                        @foreach($proveedoresFiltrados as $prov)
                                                            <li wire:click="seleccionarProveedor({{ $prov->id_proveedor }}, '{{ $prov->nombre_proveedor }}')"
                                                                class="px-4 py-2.5 hover:bg-blue-50 cursor-pointer text-sm text-gray-700 border-b border-gray-50 last:border-b-0">
                                                                <div
                                                                    class="font-bold">{{ $prov->nombre_proveedor }}</div>
                                                                <div class="text-xs text-gray-500">
                                                                    RUC: {{ $prov->ruc ?? 'S/N' }}</div>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <div class="p-3 text-sm text-gray-500 text-center">No se encontraron
                                                        proveedores
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                        @error('proveedorSeleccionado') <span
                                            class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <!-- Columna Derecha -->
                                <div class="space-y-4">
                                    <div class="flex flex-col gap-1">
                                        <label class="text-xs font-bold text-gray-500 uppercase">N° Factura /
                                            Boleta</label>
                                        <input wire:model="compra.numero_factura" type="text"
                                               class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300 text-sm shadow-sm">
                                        @error('compra.numero_factura') <span
                                            class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="flex flex-col gap-1">
                                        <label class="text-xs font-bold text-gray-500 uppercase">Fecha de
                                            Emisión</label>
                                        <input wire:model="compra.fecha_compra" type="date"
                                               class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300 text-sm shadow-sm">
                                        @error('compra.fecha_compra') <span
                                            class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección de Productos (SCROLLABLE CON ALTURA MÁXIMA) -->
                        <div class="flex-1 overflow-y-auto p-6 custom-scrollbar bg-white relative">
                            <div
                                class="flex justify-between items-center mb-4 sticky top-0 bg-white z-20 pb-2 border-b border-gray-100">
                                <h3 class="font-bold text-lg text-gray-800 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24"
                                         stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                    Detalle de Productos
                                </h3>

                                <button type="button" wire:click="agregarDetalle"
                                        class="bg-green-50 text-green-700 hover:bg-green-100 border border-green-200 rounded-lg px-3 py-1.5 text-sm font-semibold shadow-sm flex items-center gap-1.5 transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                         stroke-linejoin="round">
                                        <path d="M5 12h14"/>
                                        <path d="M12 5v14"/>
                                    </svg>
                                    Agregar Fila
                                </button>
                            </div>

                            @if(!$proveedorSeleccionado)
                                <div
                                    class="bg-amber-50 border border-amber-200 p-4 rounded-lg flex items-start gap-3 mb-4">
                                    <svg class="w-5 h-5 text-amber-600 mt-0.5 flex-shrink-0" fill="none"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    <p class="text-sm text-amber-800 font-medium">Por favor seleccione un proveedor
                                        arriba para habilitar la búsqueda de productos.</p>
                                </div>
                            @endif

                            {{--
                                AQUÍ ESTÁ EL TRUCO: 'pb-40'
                                Esto agrega un espacio vacío de ~160px al final de la lista.
                                Así, si abres el dropdown del último producto, hay espacio para mostrarlo sin que se corte el scroll.
                            --}}
                            <div class="space-y-3 min-h-[150px] pb-40">
                                @foreach ($detalleCompra as $index => $detalle)
                                    <div
                                        class="grid grid-cols-12 gap-3 items-start p-3 border border-gray-200 rounded-lg bg-white hover:border-blue-300 transition-colors shadow-sm relative"
                                        wire:key="row-{{ $index }}"
                                        style="z-index: {{ 100 - $index }};"> {{-- Z-Index alto para que tape a los de abajo --}}

                                        {{-- Buscador Producto --}}
                                        <div class="col-span-5 flex flex-col gap-1 relative">
                                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Producto</label>
                                            <div class="relative">
                                                <input type="text"
                                                       wire:model.live.debounce.300ms="busquedaProductos.{{ $index }}"
                                                       wire:focus="buscarProducto({{ $index }})"
                                                       placeholder="Buscar..."
                                                       class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300 text-sm"
                                                       @if(!$proveedorSeleccionado) disabled @endif
                                                       autocomplete="off">

                                                @if(!empty($busquedaProductos[$index]))
                                                    <button type="button"
                                                            wire:click="limpiarBusquedaProducto({{ $index }})"
                                                            class="absolute right-2 top-2.5 text-gray-400 hover:text-red-500 z-10">
                                                        ✕
                                                    </button>
                                                @endif

                                                @if($mostrarListaProductos[$index] ?? false)
                                                    {{-- El dropdown tiene posición absoluta y un z-index muy alto --}}
                                                    <div
                                                        class="absolute left-0 w-full bg-white border border-gray-300 rounded-lg shadow-2xl max-h-56 overflow-y-auto mt-1"
                                                        style="z-index: 9999; min-width: 300px;">

                                                        @if(isset($productosFiltrados[$index]) && count($productosFiltrados[$index]) > 0)
                                                            <ul class="divide-y divide-gray-100">
                                                                @foreach($productosFiltrados[$index] as $prod)
                                                                    <li wire:click="seleccionarProducto({{ $index }}, {{ $prod->id_producto }}, '{{ $prod->nombre_producto }}')"
                                                                        class="px-4 py-2 hover:bg-blue-50 cursor-pointer text-sm text-gray-700 transition-colors group">
                                                                        <div
                                                                            class="font-medium group-hover:text-blue-700">{{ $prod->nombre_producto }}</div>
                                                                        <div class="text-xs text-gray-500">
                                                                            Und: {{ $prod->unidad->nombre_unidad ?? 'U' }}</div>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            <div class="p-3 text-xs text-gray-500 text-center italic">
                                                                {{ !$proveedorSeleccionado ? 'Seleccione proveedor' : 'Sin resultados' }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                            @error('detalleCompra.' . $index . '.id_producto') <span
                                                class="text-red-500 text-[10px] font-medium">{{ $message }}</span> @enderror
                                        </div>

                                        {{-- Cantidad --}}
                                        <div class="col-span-2 flex flex-col gap-1">
                                            <label
                                                class="text-[10px] font-bold text-gray-400 uppercase tracking-wider text-center">Cant.</label>
                                            <input type="number" min="1" step="1"
                                                   wire:model.change="detalleCompra.{{ $index }}.cantidad"
                                                   class="border rounded-md px-2 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300 text-sm text-center font-semibold">
                                        </div>

                                        {{-- Precio --}}
                                        <div class="col-span-2 flex flex-col gap-1">
                                            <label
                                                class="text-[10px] font-bold text-gray-400 uppercase tracking-wider text-right">Precio</label>
                                            <div class="relative">
                                                <span class="absolute left-2 top-2 text-gray-400 text-xs">S/</span>
                                                <input type="number" min="0.01" step="0.01"
                                                       wire:model.change="detalleCompra.{{ $index }}.precio_unitario"
                                                       class="border rounded-md px-2 py-2 pl-5 w-full focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300 text-sm text-right">
                                            </div>
                                        </div>

                                        {{-- Subtotal --}}
                                        <div class="col-span-2 flex flex-col gap-1 items-end justify-center pt-6">
                    <span class="text-sm font-bold text-gray-700">
                        S/ {{ number_format(($detalle['cantidad'] ?? 0) * ($detalle['precio_unitario'] ?? 0), 2) }}
                    </span>
                                        </div>

                                        {{-- Botón Eliminar --}}
                                        <div class="col-span-1 flex items-center justify-center pt-5">
                                            <button type="button" wire:click="eliminarDetalle({{ $index }})"
                                                    class="text-gray-400 hover:text-red-600 hover:bg-red-50 p-2 rounded-lg transition-all"
                                                    title="Eliminar fila">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M3 6h18"/>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/>
                                                    <path d="M10 11v6"/>
                                                    <path d="M14 11v6"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Footer (Fijo) -->
                        <div class="p-6 bg-white border-t border-gray-200 shadow-lg flex-shrink-0 rounded-b-xl">
                            <div class="flex flex-col md:flex-row gap-6 justify-between items-end">

                                <div class="w-full md:w-2/3">
                                    <label
                                        class="text-xs font-bold text-gray-500 uppercase mb-1 block">Observaciones</label>
                                    <textarea wire:model="compra.observacion" rows="2"
                                              class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300 text-sm resize-none bg-gray-50"
                                              placeholder="Notas adicionales sobre la orden..."></textarea>
                                </div>

                                <div class="w-full md:w-1/3 flex flex-col gap-4 items-end">

                                    <div class="text-right w-full">
                                        <div class="flex justify-between items-center mb-1">
                                            <span class="text-sm text-gray-500">Subtotal:</span>
                                            @php
                                                $subtotal = collect($detalleCompra)->sum(fn($d) => ($d['cantidad']??0) * ($d['precio_unitario']??0));
                                            @endphp
                                            <span
                                                class="font-medium text-gray-800">S/ {{ number_format($subtotal, 2) }}</span>
                                        </div>
                                        <div
                                            class="flex justify-between items-center border-t border-gray-100 pt-2 mt-1">
                                            <span class="text-base font-bold text-gray-700">Total General:</span>
                                            <span class="text-2xl font-bold text-blue-700">
                                        S/ {{ number_format($subtotal * (1 + $IGV), 2) }}
                                    </span>
                                        </div>
                                        <p class="text-[10px] text-gray-400 mt-1 text-right">Incluye IGV
                                            ({{ $IGV * 100 }}%)</p>
                                    </div>

                                    <div class="flex gap-3 w-full">
                                        <button type="button" wire:click="closeModal"
                                                class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors text-sm">
                                            Cancelar
                                        </button>
                                        <button type="submit"
                                                class="flex-[2] px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold shadow-md transition-colors flex justify-center items-center gap-2 text-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                 stroke-linecap="round" stroke-linejoin="round">
                                                <path
                                                    d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                                                <polyline points="17 21 17 13 7 13 7 21"/>
                                                <polyline points="7 3 7 8 15 8"/>
                                            </svg>
                                            Generar Orden
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        @endif
    </div>
    <div x-data x-init="$watch('$wire.showModal', value => {
        if (value) { document.body.classList.add('overflow-hidden') } else { document.body.classList.remove('overflow-hidden') }
    })">

        <div>
            @if ($showModalDetalle && $compraSeleccionada)
                <div class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
                    <div class="bg-white rounded-xl shadow-xl w-1/4 p-6">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-xl font-medium ">
                                    Detalles de orden de compra
                                </p>
                                <p class="text-sm font-medium ">
                                    Información completa de la orden {{ $compraSeleccionada->codigo }}
                                </p>
                            </div>
                            <button title="Descargar Orden de compra" wire:click="exportarPdfOrdenCompra"
                                    class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round"
                                     class="lucide lucide-file-text-icon lucide-file-text">
                                    <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/>
                                    <path d="M14 2v4a2 2 0 0 0 2 2h4"/>
                                    <path d="M10 9H8"/>
                                    <path d="M16 13H8"/>
                                    <path d="M16 17H8"/>
                                </svg>
                            </button>
                        </div>
                        <div class="grid grid-cols-2 gap-5 mt-5">
                            <div>
                                <label class="font-medium">Numero de orden</label>
                                <p class="text-gray-700">{{ $compraSeleccionada->codigo }}</p>
                            </div>
                            <div>
                                <label class="font-medium">Fecha de orden</label>
                                <p class="text-gray-700">{{ $compraSeleccionada->fecha_compra }}</p>
                            </div>
                            <div>
                                <label class="font-medium">Proveedor</label>
                                <p class="text-gray-700">{{ $compraSeleccionada->proveedor->nombre_proveedor }}</p>
                            </div>
                            <div>
                                <label class="font-medium">Nro Factura</label>
                                <p class="text-gray-700">{{ $compraSeleccionada->numero_factura }}</p>
                            </div>
                            <div>
                                <label class="font-medium">Estado</label>
                                <p class="text-gray-700 capitalize">
                                    {{ $compraSeleccionada->estadoCompra->nombre_estado_compra }}
                                </p>
                            </div>
                            <div>
                                <label class="font-medium">Fecha de compra</label>
                                <p class="text-gray-700">{{ $compraSeleccionada->fecha_compra }}</p>
                            </div>
                            <div>
                                <label class="font-medium">Fecha de registro</label>
                                <p class="text-gray-700">{{ $compraSeleccionada->fecha_registro }}</p>
                            </div>
                        </div>

                        <div class="mt-5">
                            <p class="font-medium text-md">Detalles productos</p>
                            <table>
                                <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-start font-medium">Producto</th>
                                    <th class="text-start font-medium">Cantidad</th>
                                    <th class="text-start font-medium">Precio</th>
                                    <th class="text-start font-medium">Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($compraSeleccionada->detalleCompra as $detalle)
                                    <tr class="border-b border-gray-200 text-sm">
                                        <td class="pb-3 pt-2 text-gray-700 font-medium">
                                            {{ $detalle->producto->nombre_producto }}
                                        </td>
                                        <td class="text-gray-700 font-medium">{{ $detalle->cantidad }}</td>
                                        <td class="text-gray-700 font-medium">s/{{ $detalle->precio_unitario }}
                                        </td>
                                        <td class="text-gray-700 font-medium">s/{{ $detalle->sub_total }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                        <div class="mt-5 space-y-1">
                            <p class="text-sm text-end">
                                Subtotal: S/ {{ number_format($compraSeleccionada->total, 2) }}
                            </p>
                            <p class="text-sm text-end">
                                IGV ({{ $IGV * 100 }}%):
                                S/ {{ number_format($compraSeleccionada->total * $IGV, 2) }}
                            </p>
                            <p class="font-medium text-xl text-end">
                                Total:
                                S/
                                {{ number_format($compraSeleccionada->total + $compraSeleccionada->total * $IGV, 2) }}
                            </p>
                        </div>
                        <div>
                            <label>Observaciones</label>
                            <p class="text-gray-700">{{ $compraSeleccionada->observacion }}</p>
                        </div>

                        <div class="flex justify-end mt-5">
                            <button wire:click="$set('showModalDetalle', false)"
                                    class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                                Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
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
        </script>
    @endpush
    <x-loader target="guardar"/>
</x-panel>
