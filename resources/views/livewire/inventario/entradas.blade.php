<x-panel title="Gestión de entradas de inventario" :breadcrumbs="[
    ['label' => 'Inicio', 'href' => '/', 'icon' => 'home'],
    ['label' => 'Inventario', 'href' => '#'],
    ['label' => 'Entradas'],
]">
    <x-tabs :tabs="['registro' => 'Registar entradas', 'detalle' => 'Listado de entradas']" default="registro">
        <x-tab name="registro">
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
            <div class="max-w-full h-full">
                <x-card>
                    <div class="flex flex-col gap-2">
                        <p class="font-medium text-xl flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="blue" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-plus-icon lucide-plus">
                                <path d="M5 12h14" />
                                <path d="M12 5v14" />
                            </svg>
                            Registrar Entrada
                        </p>
                        <p class="text-md">Añade stock a tus productos existentes.</p>
                    </div>

                    <!-- Buscar Orden de Compra -->
                    <div class="bg-blue-50 p-4 rounded-lg mb-6">
                        <div class="flex gap-2 items-center">
                            <input type="text" wire:model="ordenCompra" placeholder="Ingrese código de OC"
                                class="border rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-300 w-full">
                            <button type="button" wire:click="buscarOrdenCompra"
                                class="bg-blue-500 hover:bg-blue-700 text-white rounded px-4 py-2 font-medium">
                                Buscar OC
                            </button>
                        </div>
                    </div>

                    @if ($proveedorOC)
                        <div class="bg-gray-50 p-4 rounded-lg mb-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="font-medium text-gray-600">Proveedor</label>
                                    <p class="font-semibold">{{ $proveedorOC->nombre_proveedor }}</p>
                                </div>
                                <div>
                                    <label class="font-medium text-gray-600">Orden de Compra</label>
                                    <p class="font-semibold">{{ $ordenCompra }}</p>
                                </div>
                                <div>
                                    <label class="font-medium text-gray-600">Productos Pendientes</label>
                                    {{-- ✅ CORRECCIÓN: Usar la propiedad computada en lugar de array_filter --}}
                                    <p class="font-semibold">{{ $this->productosPendientesCount }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Formulario Individual -->
                        <div class="{{ $showFormularioRapido ? 'lg:col-span-1' : 'lg:col-span-2' }}">
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <h3 class="font-medium text-lg mb-4 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 20h9" />
                                        <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z" />
                                    </svg>
                                    Entrada Individual
                                </h3>

                                <form wire:submit.prevent="registrar" class="grid grid-cols-1 gap-4 text-sm">
                                    <div class="flex flex-col gap-2">
                                        <label for="producto" class="font-medium">Seleccionar un producto <span
                                                class="text-red-500">*</span></label>
                                        <select wire:model.live="id_producto" id="producto" name="producto_id"
                                            class="border rounded px-2 py-2 focus:outline-none focus:ring focus:ring-blue-300">
                                            <option value="">Seleccione un producto</option>
                                            @foreach ($productosOC as $producto)
                                                <option value="{{ $producto['id_detalle_compra'] }}"
                                                    @if (isset($producto['pertenece_proveedor']) && !$producto['pertenece_proveedor']) disabled 
                        style="color: #999; background-color: #f5f5f5;" @endif>
                                                    {{ $producto['nombre'] }}
                                                    @if (isset($producto['pertenece_proveedor']) && !$producto['pertenece_proveedor'])
                                                        (❌ No asociado)
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>

                                        @error('producto_id')
                                            <p class="text-red-500 text-xs italic mt-1">
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>

                                    @if ($productoSeleccionado)
                                        <div
                                            class="bg-gray-50 rounded-md p-3 mb-2 border border-gray-200 text-gray-600">
                                            <p class="font-medium">Stock actual</p>
                                            <div class="grid grid-cols-3 gap-4 mt-2 text-sm">
                                                <div class="flex gap-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="12"
                                                        height="12" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        class="lucide lucide-package-icon lucide-package">
                                                        <path
                                                            d="M11 21.73a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73z" />
                                                        <path d="M12 22V12" />
                                                        <polyline points="3.29 7 12 12 20.71 7" />
                                                        <path d="m7.5 4.27 9 5.15" />
                                                    </svg>
                                                    <span class="font-medium">Total: </span>
                                                    <span>{{ $this->stockActual['total'] }}</span>
                                                </div>
                                                <div class="flex gap-2 items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="12"
                                                        height="12" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        class="lucide lucide-warehouse-icon lucide-warehouse">
                                                        <path d="M18 21V10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1v11" />
                                                        <path
                                                            d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V8a2 2 0 0 1 1.132-1.803l7.95-3.974a2 2 0 0 1 1.837 0l7.948 3.974A2 2 0 0 1 22 8z" />
                                                        <path d="M6 13h12" />
                                                        <path d="M6 17h12" />
                                                    </svg>
                                                    <span class="font-medium">Almacén: </span>
                                                    <span>{{ $this->stockActual['almacen'] }}</span>
                                                </div>
                                                <div class="flex gap-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="12"
                                                        height="12" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        class="lucide lucide-shopping-cart-icon lucide-shopping-cart">
                                                        <circle cx="8" cy="21" r="1" />
                                                        <circle cx="19" cy="21" r="1" />
                                                        <path
                                                            d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12" />
                                                    </svg>
                                                    <span class="font-medium">Mostrador: </span>
                                                    <span>{{ $this->stockActual['mostrador'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="flex flex-col gap-2">
                                            <label for="cantidad" class="font-medium">Cantidad <span
                                                    class="text-red-500">*</span>
                                            </label>
                                            <input type="number" id="cantidad" min="0" step="0.01"
                                                class="border rounded px-2 py-2 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200"
                                                placeholder="0.00" wire:model="lote.cantidad_total">
                                            @if ($productoSeleccionado)
                                                <p class="text-xs text-gray-500">
                                                    Máximo: {{ $productoSeleccionado['cantidad'] }} unidades
                                                </p>
                                            @endif
                                            @error('lote.cantidad_total')
                                                <p class="text-red-500 text-xs italic mt-1">
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                        </div>
                                        <div class="flex flex-col gap-2">
                                            <label for="ubicacion" class="font-medium">Ubicación <span
                                                    class="text-red-500">*</span></label>
                                            <select name="ubicacion" id="ubicacion" wire:model="ubicacion"
                                                class="border rounded px-2 py-2 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200 ">
                                                <option value="almacen">Almacen</option>
                                                <option value="mostrador">Mostrador</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="flex flex-col gap-2">
                                            <label for="fecha_recepcion" class="font-medium">Fecha de recepción <span
                                                    class="text-red-500">*</span></label>
                                            <input type="date" id="fecha_recepcion" name="fecha_recepcion"
                                                class="border rounded px-2 py-2 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200"
                                                wire:model="lote.fecha_recepcion">
                                            @error('lote.fecha_recepcion')
                                                <p class="text-red-500 text-xs italic mt-1">
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                        </div>
                                        <div class="flex flex-col gap-2">
                                            <label for="fecha_vencimiento" class="font-medium">Fecha de
                                                vencimiento</label>
                                            <input type="date" id="fecha_vencimiento" name="fecha_vencimiento"
                                                class="border rounded px-2 py-2 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200"
                                                wire:model="lote.fecha_vencimiento">
                                            @error('lote.fecha_vencimiento')
                                                <p class="text-red-500 text-xs italic mt-1">
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="flex flex-col gap-2">
                                        <label for="observacion" class="font-medium">Observaciones</label>
                                        <textarea name="observacion" id="observacion" rows="3" maxlength="1000"
                                            class="border rounded px-2 py-2 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200 resize-none"
                                            placeholder="Observaciones de entrada..." wire:model.lazy="lote.observacion"></textarea>
                                        @error('lote.observacion')
                                            <p class="text-red-500 text-xs italic mt-1">
                                                {{ $message }}
                                            </p>
                                        @enderror
                                        <div class="text-right text-xs text-gray-500 mt-1">
                                            {{ strlen($lote['observacion']) }}/1000 caracteres
                                        </div>
                                    </div>

                                    <button type="submit"
                                        class="w-full p-3 text-white rounded-md transition bg-green-500 hover:bg-green-600 ease-linear font-medium">
                                        Registrar entrada individual
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Formulario Rápido para Múltiples Productos -->
                        @if ($showFormularioRapido)
                            <div class="lg:col-span-1">
                                <div class="bg-white border border-orange-200 rounded-lg p-4">
                                    <h3 class="font-medium text-lg mb-4 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                            <polyline points="14 2 14 8 20 8" />
                                            <line x1="16" y1="13" x2="8" y2="13" />
                                            <line x1="16" y1="17" x2="8" y2="17" />
                                            <polyline points="10 9 9 9 8 9" />
                                        </svg>
                                        Entradas Rápidas
                                    </h3>

                                    <div class="max-h-96 overflow-y-auto mb-4">
                                        <table class="w-full text-sm">
                                            <thead class="bg-gray-50 sticky top-0">
                                                <tr>
                                                    <th class="px-3 py-2 text-left font-medium text-gray-600">Producto
                                                    </th>
                                                    <th class="px-3 py-2 text-center font-medium text-gray-600">
                                                        Pendiente</th>
                                                    <th class="px-3 py-2 text-center font-medium text-gray-600">
                                                        Cantidad</th>
                                                    <th class="px-3 py-2 text-center font-medium text-gray-600">
                                                        Ubicación</th>
                                                    <th class="px-3 py-2 text-center font-medium text-gray-600">
                                                        Vencimiento</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200">
                                                @foreach ($productosOC as $producto)
                                                    @if ($producto['cantidad'] > 0)
                                                        <tr>
                                                            <td class="px-3 py-2">
                                                                <div class="text-xs font-medium">
                                                                    {{ $producto['nombre'] }}</div>
                                                                <div class="text-xs text-gray-500">S/
                                                                    {{ number_format($producto['precio_compra'], 2) }}
                                                                </div>
                                                            </td>
                                                            <td class="px-3 py-2 text-center">
                                                                <span
                                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                                    {{ $producto['cantidad'] }}
                                                                </span>
                                                            </td>
                                                            <td class="px-3 py-2">
                                                                <input type="number"
                                                                    wire:model="entradasRapidas.{{ $producto['id_detalle_compra'] }}.cantidad"
                                                                    min="0" max="{{ $producto['cantidad'] }}"
                                                                    step="0.01"
                                                                    class="w-20 px-2 py-1 border rounded text-center focus:outline-none focus:ring-1 focus:ring-blue-300">
                                                            </td>
                                                            <td class="px-3 py-2">
                                                                <select
                                                                    wire:model="entradasRapidas.{{ $producto['id_detalle_compra'] }}.ubicacion"
                                                                    class="text-xs border rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-blue-300">
                                                                    <option value="almacen">Almacén</option>
                                                                    <option value="mostrador">Mostrador</option>
                                                                </select>
                                                            </td>
                                                            <td class="px-3 py-2">
                                                                <input type="date"
                                                                    wire:model="entradasRapidas.{{ $producto['id_detalle_compra'] }}.fecha_vencimiento"
                                                                    class="text-xs border rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-blue-300">
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="space-y-3">
                                        <div class="flex flex-col gap-2">
                                            <label class="font-medium text-sm">Fecha de recepción común</label>
                                            <input type="date"
                                                class="border rounded px-2 py-2 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200"
                                                wire:model="lote.fecha_recepcion">
                                        </div>

                                        <div class="flex flex-col gap-2">
                                            <label class="font-medium text-sm">Observaciones comunes (opcional)</label>
                                            <textarea
                                                class="border rounded px-2 py-2 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200 resize-none text-sm"
                                                rows="2" placeholder="Observaciones para todos los productos..." wire:model="lote.observacion"></textarea>
                                        </div>

                                        <button type="button" wire:click="registrarEntradasRapidas"
                                            class="w-full p-3 text-white rounded-md transition bg-orange-500 hover:bg-orange-600 ease-linear font-medium">
                                            Registrar todas las entradas
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </x-card>
            </div>
        </x-tab>

        <x-tab name="detalle">
            <div class=" bg-gray-50 rounded p-4 space-y-4">

                <div class="max-w-full" id="historial_entradas">
                    <x-card class="">
                        <p class="font-medium text-xl">Historial de entradas</p>
                        <p class="text-md">Todas las entradas registradas en el sistema</p>
                        <livewire:entradas-table />
                    </x-card>
                </div>
            </div>
            <div x-data x-init="$watch('$wire.showModalDetalle', value => {
                if (value) {
                    document.body.classList.add('overflow-hidden');
                    document.body.style.paddingRight = '0px';
                } else {
                    document.body.classList.remove('overflow-hidden');
                    document.body.style.paddingRight = '';
                }
            })">
                <div>
                    @if ($showModalDetalle && $selectedEntrada)
                        <!-- Fondo con backdrop -->
                        <div class="fixed inset-0 flex items-center justify-center z-50">
                            <!-- Overlay -->
                            <div class="absolute inset-0 bg-black/60 transition-opacity duration-300"
                                wire:click="$set('showModalDetalle', false)"></div>

                            <!-- Modal -->
                            <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-4xl mx-4 max-h-[90vh] overflow-hidden transform transition-all duration-300 scale-100 opacity-100"
                                x-transition:enter="ease-out duration-300"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="ease-in duration-200"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95">

                                <!-- Header -->
                                <div
                                    class="px-6 py-4 flex items-center justify-between border-b border-gray-200 bg-white">
                                    <div class="flex items-center gap-3">
                                        <div class="bg-green-100 p-2 rounded-lg">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                viewBox="0 0 24 24" fill="none" stroke="green" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M5 12h14" />
                                                <path d="M12 5v14" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-medium text-gray-700">Detalles de Entrada de
                                                Inventario</h3>
                                            <p class="text-gray-500 text-sm">
                                                Movimiento de inventario
                                                #{{ $selectedEntrada->id_inventario_movimiento }} -
                                                {{ $selectedEntrada->fecha_movimiento->format('d/m/Y H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                    <button wire:click="$set('showModalDetalle', false)"
                                        class="p-2 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="18" y1="6" x2="6" y2="18">
                                            </line>
                                            <line x1="6" y1="6" x2="18" y2="18">
                                            </line>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Body -->
                                <div class="overflow-y-auto p-6 bg-gray-50 max-h-[calc(85vh-120px)]">
                                    <!-- Información Principal -->
                                    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200 mb-6">
                                        <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-200">
                                            <div class="bg-blue-100 p-2 rounded-lg">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                    viewBox="0 0 24 24" fill="none" stroke="blue"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path
                                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                </svg>
                                            </div>
                                            <h4 class="font-bold text-gray-800 text-lg">Información del Producto</h4>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <!-- Producto -->
                                            <div class="flex flex-col">
                                                <label
                                                    class="text-sm font-medium text-gray-600 mb-1.5">Producto</label>
                                                <div
                                                    class="p-3 bg-gray-50 rounded-lg border border-gray-200 flex items-center gap-2">
                                                    <p class="text-gray-800 font-medium">
                                                        {{ $selectedEntrada->lote->producto->nombre_producto }}</p>
                                                    <p class="text-gray-500 text-sm">
                                                        ({{ $selectedEntrada->lote->producto->unidad->nombre_unidad ?? 'Sin unidad' }})
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Cantidad -->
                                            <div class="flex flex-col">
                                                <label class="text-sm font-medium text-gray-600 mb-1.5">Cantidad
                                                    Ingresada</label>
                                                <div class="p-3 bg-green-50 rounded-lg border border-green-200">
                                                    <p class="text-green-700 font-bold text-lg">
                                                        +{{ $selectedEntrada->cantidad_movimiento }}</p>
                                                </div>
                                            </div>

                                            <!-- Lote -->
                                            <div class="flex flex-col">
                                                <label class="text-sm font-medium text-gray-600 mb-1.5">Código de
                                                    Lote</label>
                                                <div class="p-3 bg-purple-50 rounded-lg border border-purple-200">
                                                    <p class="text-purple-700 font-mono font-medium">
                                                        {{ $selectedEntrada->lote->codigo_lote }}</p>
                                                </div>
                                            </div>

                                            <!-- Stock Resultante -->
                                            <div class="flex flex-col">
                                                <label class="text-sm font-medium text-gray-600 mb-1.5">Stock
                                                    Resultante</label>
                                                <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                                                    <p class="text-blue-700 font-medium">
                                                        {{ $selectedEntrada->stock_resultante }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Información de la Operación -->
                                    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200 mb-6">
                                        <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-200">
                                            <div class="bg-indigo-100 p-2 rounded-lg">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                    viewBox="0 0 24 24" fill="none" stroke="indigo"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                                    <circle cx="9" cy="7" r="4" />
                                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                                </svg>
                                            </div>
                                            <h4 class="font-bold text-gray-800 text-lg">Información de la Operación
                                            </h4>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <!-- Ubicación -->
                                            <div class="flex flex-col">
                                                <label
                                                    class="text-sm font-medium text-gray-600 mb-1.5">Ubicación</label>
                                                <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                                                    <p class="text-blue-700 font-medium capitalize">
                                                        {{ $selectedEntrada->tipoUbicacion->nombre_tipo_ubicacion ?? $selectedEntrada->ubicacion }}
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Usuario -->
                                            <div class="flex flex-col">
                                                <label class="text-sm font-medium text-gray-600 mb-1.5">Registrado
                                                    por</label>
                                                <div class="p-3 bg-indigo-50 rounded-lg border border-indigo-200">
                                                    <p class="text-indigo-700 font-medium">
                                                        {{ $selectedEntrada->trabajador->persona->user->usuario }}</p>
                                                    <p class="text-indigo-500 text-sm mt-1">
                                                        {{ $selectedEntrada->trabajador->persona->nombre_completo ?? '' }}
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Fecha -->
                                            <div class="flex flex-col">
                                                <label class="text-sm font-medium text-gray-600 mb-1.5">Fecha y
                                                    Hora</label>
                                                <div
                                                    class="p-3 bg-orange-50 rounded-lg border border-orange-200 flex items-center gap-2">
                                                    <p class="text-orange-700 font-medium">
                                                        {{ $selectedEntrada->fecha_movimiento->format('d/m/Y') }}</p>
                                                    <p class="text-orange-600 text-sm">
                                                        {{ $selectedEntrada->fecha_movimiento->format('H:i:s') }}</p>
                                                </div>
                                            </div>

                                            <!-- Tipo de Movimiento -->
                                            <div class="flex flex-col">
                                                <label class="text-sm font-medium text-gray-600 mb-1.5">Tipo de
                                                    Movimiento</label>
                                                <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                                                    <p class="text-gray-700 font-medium">
                                                        {{ $selectedEntrada->tipo_movimiento->nombre_tipo_movimiento ?? 'Entrada' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Información Adicional del Lote -->
                                    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
                                        <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-200">
                                            <div class="bg-yellow-100 p-2 rounded-lg">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                    viewBox="0 0 24 24" fill="none" stroke="orange"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <rect x="3" y="3" width="18" height="18" rx="2"
                                                        ry="2" />
                                                    <line x1="3" y1="9" x2="21"
                                                        y2="9" />
                                                    <line x1="9" y1="21" x2="9"
                                                        y2="9" />
                                                </svg>
                                            </div>
                                            <h4 class="font-bold text-gray-800 text-lg">Información Adicional del Lote
                                            </h4>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <!-- Precio de Compra -->
                                            <div class="flex flex-col">
                                                <label class="text-sm font-medium text-gray-600 mb-1.5">Precio de
                                                    Compra</label>
                                                <div class="p-3 bg-green-50 rounded-lg border border-green-200">
                                                    <p class="text-green-700 font-medium">
                                                        S/
                                                        {{ number_format($selectedEntrada->lote->precio_compra, 2) }}
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Fecha de Recepción -->
                                            <div class="flex flex-col">
                                                <label class="text-sm font-medium text-gray-600 mb-1.5">Fecha de
                                                    Recepción</label>
                                                <div class="p-3 bg-purple-50 rounded-lg border border-purple-200">
                                                    <p class="text-purple-700 font-medium">
                                                        @if ($selectedEntrada->lote->fecha_recepcion)
                                                            {{ \Carbon\Carbon::parse($selectedEntrada->lote->fecha_recepcion)->format('d/m/Y') }}
                                                        @else
                                                            <span class="text-gray-500 italic">No especificada</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Fecha de Vencimiento -->
                                            <div class="flex flex-col">
                                                <label class="text-sm font-medium text-gray-600 mb-1.5">Fecha de
                                                    Vencimiento</label>
                                                <div
                                                    class="p-3 bg-{{ $selectedEntrada->lote->fecha_vencimiento && \Carbon\Carbon::parse($selectedEntrada->lote->fecha_vencimiento)->isPast() ? 'red' : 'blue' }}-50 rounded-lg border border-{{ $selectedEntrada->lote->fecha_vencimiento && \Carbon\Carbon::parse($selectedEntrada->lote->fecha_vencimiento)->isPast() ? 'red' : 'blue' }}-200">
                                                    <p
                                                        class="text-{{ $selectedEntrada->lote->fecha_vencimiento && \Carbon\Carbon::parse($selectedEntrada->lote->fecha_vencimiento)->isPast() ? 'red' : 'blue' }}-700 font-medium">
                                                        @if ($selectedEntrada->lote->fecha_vencimiento)
                                                            {{ \Carbon\Carbon::parse($selectedEntrada->lote->fecha_vencimiento)->format('d/m/Y') }}
                                                            @if (\Carbon\Carbon::parse($selectedEntrada->lote->fecha_vencimiento)->isPast())
                                                                <span
                                                                    class="text-xs text-red-600 ml-2">(Vencido)</span>
                                                            @endif
                                                        @else
                                                            <span class="text-gray-500 italic">No especificada</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Estado del Lote -->
                                            <div class="flex flex-col">
                                                <label class="text-sm font-medium text-gray-600 mb-1.5">Estado del
                                                    Lote</label>
                                                <div
                                                    class="p-3 bg-{{ $selectedEntrada->lote->estado == 'activo' ? 'green' : 'gray' }}-50 rounded-lg border border-{{ $selectedEntrada->lote->estado == 'activo' ? 'green' : 'gray' }}-200">
                                                    <p
                                                        class="text-{{ $selectedEntrada->lote->estado == 'activo' ? 'green' : 'gray' }}-700 font-medium capitalize">
                                                        {{ $selectedEntrada->lote->estado }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Observaciones -->
                                        <div class="mt-4">
                                            <label
                                                class="text-sm font-medium text-gray-600 mb-1.5">Observaciones</label>
                                            <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                                                @if (!empty($selectedEntrada->lote->observacion))
                                                    <p class="text-gray-700 leading-relaxed">
                                                        {{ $selectedEntrada->lote->observacion }}</p>
                                                @else
                                                    <p class="text-gray-500 italic">Sin observaciones</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Footer -->
                                <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
                                    <div class="flex justify-end gap-3">
                                        <button wire:click="$set('showModalDetalle', false)"
                                            class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-100 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2">
                                            Cerrar
                                        </button>

                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            </div>
        </x-tab>
    </x-tabs>
    <x-loader />
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
</x-panel>
