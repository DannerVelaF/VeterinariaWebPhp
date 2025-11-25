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
                <p class="font-medium text-gray-600 text-xl">Registro de Ventas</p>
                <p class="font-medium text-gray-600 text-sm">Gestiona las ventas de productos y servicios</p>
            </div>
            <div class="flex gap-2">
                <x-exports/>
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

        <livewire:ventas-table/>

    </x-card>

    <div>
        @if ($showModal)
            <div class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
                <div class="bg-white rounded-xl shadow-xl w-2/3 p-6 space-y-4 max-h-[90vh] overflow-y-auto">
                    <div>
                        <h2 class="text-xl font-medium mb-1">Registrar Nueva Venta</h2>
                        <p class="text-sm font-medium">Completa la información de la venta y agrega los
                            productos/servicios</p>
                    </div>
                    <form action="" class="space-y-4 w-full" wire:submit.prevent="guardar">

                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex gap-2 flex-col">
                                <label>Código de venta</label>
                                <input wire:model="codigoVenta" type="text" readonly
                                       class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200 bg-gray-50">
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

                            <!-- INFORMACIÓN DEL CLIENTE (ocupa las 2 columnas) -->
                            <!-- INFORMACIÓN DEL CLIENTE -->
                            <div class="col-span-2">
                                <h3 class="font-bold text-gray-700 text-base mb-2">👤 Información del Cliente</h3>
                                <p class="text-gray-500 text-xs mb-3">Busca un cliente por su DNI o nombre para
                                    asociarlo a la venta.</p>

                                <!-- BUSCADOR MEJORADO -->
                                <div>
                                    <label class="font-semibold mb-1 block">Buscar Cliente:</label>
                                    <div class="relative">
                                        <!-- Input con lupa -->
                                        <div class="relative">
                                            <input type="text" wire:model.live.debounce.500ms="filtroCliente"
                                                   placeholder="Ingrese DNI, nombre o apellido del cliente..."
                                                   class="border rounded-lg px-4 py-3 w-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pl-10 pr-10">
                                            <!-- Icono de búsqueda a la izquierda -->
                                            <div
                                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                                     viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                </svg>
                                            </div>
                                            <!-- Botón de búsqueda manual a la derecha -->
                                            <button type="button" wire:click="buscarClientes"
                                                    class="absolute inset-y-0 right-0 pr-3 flex items-center hover:text-blue-600 transition-colors"
                                                    title="Buscar cliente">
                                                <svg class="h-5 w-5 text-gray-400 hover:text-blue-600" fill="none"
                                                     stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                </svg>
                                            </button>
                                        </div>

                                        <!-- MENÚ DESPLEGABLE DE RESULTADOS -->
                                        @if ($filtroCliente && $clientes->count() > 0)
                                            <div
                                                class="absolute z-20 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                                <div class="p-2 bg-gray-50 border-b">
                                                    <p class="text-xs font-semibold text-gray-600">
                                                        {{ $clientes->count() }} cliente(s) encontrado(s)
                                                    </p>
                                                </div>
                                                <ul>
                                                    @foreach ($clientes as $cliente)
                                                        <li wire:click="seleccionarCliente({{ $cliente->id_cliente }})"
                                                            class="px-4 py-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors duration-150">
                                                            <div class="flex justify-between items-start">
                                                                <div class="flex-1">
                                                                    <div class="flex items-center mb-1">
                                                                        <span
                                                                            class="font-semibold text-gray-800 text-sm">
                                                                            @if($cliente->persona)
                                                                                {{ $cliente->persona->nombre ?? $cliente->persona->nombre }}
                                                                                {{ $cliente->persona->apellido_paterno }}
                                                                                {{ $cliente->persona->apellido_materno }}
                                                                            @else
                                                                                Cliente #{{ $cliente->id_cliente }}
                                                                            @endif
                                                                        </span>
                                                                        <span
                                                                            class="ml-2 bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                                                            DNI: {{ $cliente->persona->numero_documento ?? 'N/A' }}
                                                                        </span>
                                                                    </div>
                                                                    <div class="text-xs text-gray-600 space-y-1">
                                                                        @if ($cliente->persona && $cliente->persona->numero_telefono_personal)
                                                                            <span class="flex items-center">
                                                                                <svg class="w-3 h-3 mr-1" fill="none"
                                                                                     stroke="currentColor"
                                                                                     viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round"
                                                                                          stroke-linejoin="round"
                                                                                          stroke-width="2"
                                                                                          d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                                                                    </path>
                                                                                </svg>
                                                                                {{ $cliente->persona->numero_telefono_personal }}
                                                                            </span>
                                                                        @endif
                                                                        @if ($cliente->persona && $cliente->persona->correo_electronico_personal)
                                                                            <span class="flex items-center">
                                                                                <svg class="w-3 h-3 mr-1" fill="none"
                                                                                     stroke="currentColor"
                                                                                     viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round"
                                                                                          stroke-linejoin="round"
                                                                                          stroke-width="2"
                                                                                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                                                                    </path>
                                                                                </svg>
                                                                                {{ $cliente->persona->correo_electronico_personal }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="ml-2 flex-shrink-0">
                                                                    <svg class="w-4 h-4 text-green-500" fill="none"
                                                                         stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                              stroke-linejoin="round"
                                                                              stroke-width="2"
                                                                              d="M5 13l4 4L19 7"></path>
                                                                    </svg>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif

                                        <!-- Mensaje cuando no hay resultados -->
                                        @if ($filtroCliente && $clientes->isEmpty())
                                            <div
                                                class="absolute z-20 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg p-4">
                                                <div class="text-center text-gray-500">
                                                    <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none"
                                                         stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                        </path>
                                                    </svg>
                                                    <p class="text-sm font-medium">No se encontraron clientes</p>
                                                    <p class="text-xs mt-1">Intente con otro término de búsqueda</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- CLIENTE SELECCIONADO -->
                                @if ($this->getClienteSeleccionadoFormateado())
                                    @php
                                        $clienteFormateado = $this->getClienteSeleccionadoFormateado();
                                    @endphp
                                    <div class="mt-3">
                                        <div class="bg-green-50 border border-green-200 rounded-lg shadow-sm p-4">
                                            <div class="flex items-center justify-between mb-3">
                                                <div class="flex items-center">
                                                    <svg class="w-5 h-5 text-green-600 mr-2" fill="none"
                                                         stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <h4 class="font-bold text-green-800 text-base">Cliente
                                                        Seleccionado</h4>
                                                </div>
                                                <button type="button" wire:click="limpiarCliente"
                                                        class="text-red-500 text-xs font-bold hover:underline flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                         viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                    Cambiar cliente
                                                </button>
                                            </div>
                                            <div class="grid grid-cols-2 gap-3 text-gray-700 text-sm">
                                                <div>
                                                    <p class="font-semibold text-gray-600">Nombre completo:</p>
                                                    <p class="text-gray-800">
                                                        {{ $clienteFormateado['nombre'] }}
                                                        {{ $clienteFormateado['apellido_paterno'] }}
                                                        {{ $clienteFormateado['apellido_materno'] }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-gray-600">DNI:</p>
                                                    <p class="text-gray-800">
                                                        {{ $clienteFormateado['dni'] }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-gray-600">Teléfono:</p>
                                                    <p class="text-gray-800">
                                                        {{ $clienteFormateado['telefono'] ?: 'No registrado' }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-gray-600">Correo:</p>
                                                    <p class="text-gray-800">
                                                        {{ $clienteFormateado['correo'] ?: 'No registrado' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <button type="button"
                                        wire:click="redirigirAClientes"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-purple-500 hover:bg-purple-600 text-white rounded transition mt-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                         stroke-linejoin="round" class="lucide lucide-users">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                    </svg>
                                    Registro Completo de Clientes
                                </button>
                            </div>

                            <div class="flex gap-2 flex-col">

                            </div>
                        </div>

                        <!-- Detalles de Productos/Servicios -->
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <p class="font-medium text-xl">Productos / Servicios</p>
                                <button type="button" wire:click="agregarDetalle"
                                        class="inline-flex items-center gap-1 px-3 py-1 bg-green-500 hover:bg-green-600 text-white rounded text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                         fill="none"
                                         stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                         stroke-linejoin="round" class="lucide lucide-plus">
                                        <path d="M5 12h14"/>
                                        <path d="M12 5v14"/>
                                    </svg>
                                    Agregar item
                                </button>
                            </div>

                            <!-- MODIFICACIÓN: Eliminamos max-height y overflow-y-auto -->
                            <div class="space-y-2">
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

                                        <!-- Producto/Servicio con Búsqueda Mejorada -->
                                        <div class="col-span-3 flex flex-col relative">
                                            <label class="text-sm font-medium text-gray-600 mb-1">
                                                @if($detalle['tipo_item'] == 'producto')
                                                    🔍 Buscar Producto
                                                @else
                                                    🔍 Buscar Servicio
                                                @endif
                                            </label>

                                            <div class="relative">
                                                <!-- Input de búsqueda -->
                                                <div class="flex gap-1">
                                                    <input type="text"
                                                           wire:model.live="busquedaItems.{{ $index }}"
                                                           wire:keydown.escape="limpiarBusqueda({{ $index }})"
                                                           wire:click="mostrarLista({{ $index }})"
                                                           wire:focus="mostrarLista({{ $index }})"
                                                           placeholder="@if($detalle['tipo_item'] == 'producto')Haz clic para buscar producto...@elseHaz clic para buscar servicio...@endif"
                                                           class="border rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-300 border-gray-200 text-sm flex-1 cursor-pointer"
                                                           wire:key="search-{{ $index }}-{{ $detalle['tipo_item'] }}">

                                                    <!-- Botón limpiar -->
                                                    @if($busquedaItems[$index] ?? '')
                                                        <button type="button"
                                                                wire:click="limpiarBusqueda({{ $index }})"
                                                                class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-2 rounded transition">
                                                            ✕
                                                        </button>
                                                    @endif
                                                </div>

                                                <!-- Lista de resultados - Solo visible cuando se hace clic o focus -->
                                                @if(($mostrarListaItems[$index] ?? false) && count($itemsFiltrados[$index] ?? []) > 0)
                                                    <div
                                                        class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto search-dropdown">
                                                        <div
                                                            class="p-2 bg-gray-50 border-b flex justify-between items-center">
                                                            <p class="text-xs font-semibold text-gray-600">
                                                                {{ count($itemsFiltrados[$index]) }} resultado(s)
                                                            </p>
                                                            @if($busquedaItems[$index] ?? '')
                                                                <button type="button"
                                                                        wire:click="limpiarBusqueda({{ $index }})"
                                                                        class="text-xs text-blue-600 hover:text-blue-800">
                                                                    Limpiar
                                                                </button>
                                                            @endif
                                                        </div>
                                                        <ul>
                                                            @foreach($itemsFiltrados[$index] as $item)
                                                                <li wire:click="seleccionarItem({{ $index }}, {{ $item['id'] }}, '{{ $item['tipo'] }}')"
                                                                    class="px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors duration-150">
                                                                    <div class="flex justify-between items-start">
                                                                        <div class="flex-1">
                                                                            <div class="flex items-center mb-1">
                                        <span class="font-semibold text-gray-800 text-sm">
                                            {{ $item['nombre'] }}
                                        </span>
                                                                                <span class="ml-2 text-xs px-2 py-1 rounded-full
                                            @if($item['tipo'] == 'producto') bg-green-100 text-green-800 @else bg-purple-100 text-purple-800 @endif">
                                            {{ $item['tipo'] == 'producto' ? 'Producto' : 'Servicio' }}
                                        </span>
                                                                            </div>

                                                                            <div
                                                                                class="text-xs text-gray-600 space-y-1">
                                                                                @if($item['tipo'] == 'producto')
                                                                                    <div class="flex justify-between">
                                                                                        <span>Stock: {{ $item['stock'] }}</span>
                                                                                        <span
                                                                                            class="font-semibold">S/ {{ number_format($item['precio'], 2) }}</span>
                                                                                    </div>
                                                                                    @if($item['codigo_barras'])
                                                                                        <span class="text-gray-500">Código: {{ $item['codigo_barras'] }}</span>
                                                                                    @endif
                                                                                @else
                                                                                    <div class="flex justify-between">
                                                                                        <span>Duración: {{ $item['duracion'] ?? 'N/A' }}</span>
                                                                                        <span
                                                                                            class="font-semibold">S/ {{ number_format($item['precio'], 2) }}</span>
                                                                                    </div>
                                                                                @endif

                                                                            </div>
                                                                        </div>
                                                                        <div class="ml-2 flex-shrink-0">
                                                                            <svg class="w-4 h-4 text-green-500"
                                                                                 fill="none" stroke="currentColor"
                                                                                 viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round"
                                                                                      stroke-linejoin="round"
                                                                                      stroke-width="2"
                                                                                      d="M5 13l4 4L19 7"></path>
                                                                            </svg>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif

                                                <!-- Mensaje cuando no hay resultados -->
                                                @if(($mostrarListaItems[$index] ?? false) && count($itemsFiltrados[$index] ?? []) === 0)
                                                    <div
                                                        class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg p-4">
                                                        <div class="text-center text-gray-500">
                                                            <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none"
                                                                 stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                      stroke-width="2"
                                                                      d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                            <p class="text-sm font-medium">
                                                                @if($busquedaItems[$index] ?? '')
                                                                    No se
                                                                    encontraron {{ $detalle['tipo_item'] == 'producto' ? 'productos' : 'servicios' }}
                                                                @else
                                                                    No
                                                                    hay {{ $detalle['tipo_item'] == 'producto' ? 'productos' : 'servicios' }}
                                                                    disponibles
                                                                @endif
                                                            </p>
                                                            <p class="text-xs mt-1">
                                                                @if($busquedaItems[$index] ?? '')
                                                                    Intente con otro término de búsqueda
                                                                @else
                                                                    Todos
                                                                    los {{ $detalle['tipo_item'] == 'producto' ? 'productos' : 'servicios' }}
                                                                    están agotados o inactivos
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>

                                            @error('detalleVenta.' . $index . '.id_item')
                                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <!-- Cantidad con validación de stock -->
                                        <div class="col-span-2 flex flex-col">
                                            <label class="text-sm font-medium text-gray-600">
                                                Cantidad
                                                @if($detalle['tipo_item'] == 'producto' && $detalle['id_item'])
                                                    @php
                                                        $productoInfo = $this->getInfoStockProducto($detalle['id_item']);
                                                    @endphp
                                                    @if($productoInfo)
                                                        <span
                                                            class="text-xs font-normal block {{ $detalle['cantidad'] > $productoInfo['stock_actual'] ? 'text-red-600' : 'text-green-600' }}">
                    Stock: {{ $productoInfo['stock_actual'] }}
                                                            @if($detalle['cantidad'] > $productoInfo['stock_actual'])
                                                                • ❌ Excede stock
                                                            @endif
                </span>
                                                    @endif
                                                @endif
                                            </label>
                                            <input type="text"
                                                   wire:model.live="detalleVenta.{{ $index }}.cantidad"
                                                   wire:keydown.debounce.500ms="validarYCast('cantidad', {{ $index }})"
                                                   @if($detalle['tipo_item'] == 'servicio') disabled @endif
                                                   pattern="[0-9]*"
                                                   inputmode="numeric"
                                                   class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200 text-sm @if($detalle['tipo_item'] == 'servicio') bg-gray-100 @endif @error('detalleVenta.' . $index . '.cantidad') border-red-500 @enderror"
                                                   placeholder="1">
                                            @error('detalleVenta.' . $index . '.cantidad')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <!-- Precio Unitario con Tooltip -->
                                        <div class="col-span-2 flex flex-col">
                                            <label class="text-sm font-medium text-gray-600">
                                                Precio Unit. Venta
                                                @if($detalle['tipo_item'] == 'servicio' && $detalle['id_item'])
                                                    @php
                                                        $servicioSeleccionado = \App\Models\Servicio::find($detalle['id_item']);
                                                        $precioReferencial = $servicioSeleccionado ? $servicioSeleccionado->precio : 0;
                                                        $precioActual = $detalle['precio_unitario'] ?? 0;
                                                        $esPrecioModificado = $precioReferencial > 0 && $precioActual != $precioReferencial;
                                                    @endphp
                                                    @if($precioReferencial > 0)
                                                        <span
                                                            class="text-xs font-normal block {{ $esPrecioModificado ? 'text-yellow-600' : 'text-green-600' }}"
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
                                                        <div
                                                            class="absolute right-2 top-1/2 transform -translate-y-1/2">
                                    <span class="text-yellow-500 text-sm"
                                          title="Precio modificado del valor referencial">
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
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                         viewBox="0 0 24 24" fill="none"
                                                         stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                         stroke-linejoin="round" class="lucide lucide-trash">
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

                        <!-- Sección de Aplicar Descuento -->
                        <div
                            class="mt-6 p-4 bg-gradient-to-r from-purple-50 to-blue-50 rounded-lg border border-purple-200">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-purple-600 mr-2" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                                    </svg>
                                    <h3 class="font-bold text-gray-800 text-lg">🎁 Aplicar Descuento</h3>
                                </div>

                                @if($descuentoSeleccionado > 0)
                                    <button type="button" wire:click="quitarDescuento"
                                            class="text-red-600 hover:text-red-800 font-medium text-sm flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Quitar descuento
                                    </button>
                                @endif
                            </div>

                            <!-- Botón para mostrar/ocultar opciones -->
                            <div class="flex gap-3 items-center">
                                <button type="button"
                                        wire:click="$toggle('mostrarOpcionesDescuento')"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-all duration-200 font-medium shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $descuentoSeleccionado > 0 ? "Descuento {$descuentoSeleccionado}% aplicado" : 'Seleccionar Descuento' }}
                                </button>

                                @if($descuentoSeleccionado > 0)
                                    <div class="flex items-center gap-2 text-green-600 font-semibold">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span>Ahorro: S/ {{ number_format($venta['descuento'], 2) }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Opciones de Descuento (se muestra al hacer clic) -->
                            @if($mostrarOpcionesDescuento)
                                <div class="mt-4 p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                                    <h4 class="font-semibold text-gray-700 mb-3 text-center">Selecciona el porcentaje de
                                        descuento</h4>

                                    <div class="grid grid-cols-5 gap-3">
                                        @foreach($opcionesDescuento as $porcentaje)
                                            @php
                                                $montoDescuento = $subtotal * ($porcentaje / 100);
                                                $subtotalConDescuento = $subtotal - $montoDescuento;
                                            @endphp
                                            <button type="button"
                                                    wire:click="aplicarDescuento({{ $porcentaje }})"
                                                    class="p-4 border-2 rounded-xl text-center transition-all duration-200 hover:scale-105 hover:shadow-md
                                                        {{ $descuentoSeleccionado == $porcentaje
                                                            ? 'border-green-500 bg-green-50 shadow-sm'
                                                            : 'border-gray-200 bg-white hover:border-purple-300' }}">
                                                <div class="flex flex-col items-center">
                                                    <!-- Porcentaje grande -->
                                                    <span class="text-2xl font-bold text-gray-800 mb-1">{{ $porcentaje }}%</span>

                                                    <!-- Monto de descuento -->
                                                    <div class="text-sm text-green-600 font-semibold mb-1">
                                                        - S/ {{ number_format($montoDescuento, 2) }}
                                                    </div>

                                                    <!-- Línea divisoria -->
                                                    <div class="w-full border-t border-gray-200 my-1"></div>

                                                    <!-- Subtotal después del descuento -->
                                                    <div class="text-xs text-gray-600">
                                                        <div>Subtotal:</div>
                                                        <div class="font-semibold text-gray-800">
                                                            S/ {{ number_format($subtotalConDescuento, 2) }}</div>
                                                    </div>
                                                </div>
                                            </button>
                                        @endforeach
                                    </div>

                                    <!-- Información adicional -->
                                    <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                        <div class="flex items-start">
                                            <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="none"
                                                 stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <div class="text-sm text-blue-700">
                                                <p class="font-semibold">💡 Información sobre descuentos:</p>
                                                <p class="mt-1">El descuento se aplica sobre el <strong>subtotal actual
                                                        (S/ {{ number_format($subtotal, 2) }})</strong> antes de
                                                    impuestos.</p>
                                                <p>Puedes cambiar productos y el descuento se recalculará
                                                    automáticamente.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Resumen del Descuento Aplicado -->
                            @if($descuentoSeleccionado > 0)
                                <div
                                    class="mt-4 p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border border-green-200 shadow-sm">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <div>
                                                <h4 class="font-bold text-green-800 text-lg">¡Descuento Aplicado!</h4>
                                                <p class="text-green-600 text-sm">Descuento
                                                    del {{ $descuentoSeleccionado }}% sobre el subtotal</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-2xl font-bold text-green-800">-
                                                S/ {{ number_format($venta['descuento'], 2) }}</div>
                                            <div class="text-sm text-green-600">Ahorro del {{ $descuentoSeleccionado }}
                                                %
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Desglose del cálculo -->
                                    <div class="mt-3 grid grid-cols-2 gap-4 text-sm">
                                        <div class="bg-white p-3 rounded-lg border">
                                            <p class="text-gray-600">Subtotal original:</p>
                                            <p class="font-semibold text-lg">S/ {{ number_format($subtotal, 2) }}</p>
                                        </div>
                                        <div class="bg-white p-3 rounded-lg border">
                                            <p class="text-gray-600">Subtotal con descuento:</p>
                                            <p class="font-semibold text-lg text-green-600">
                                                S/ {{ number_format($subtotal - $venta['descuento'], 2) }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Resumen de Totales -->
                        <div class="grid grid-cols-7 gap-4 p-4 bg-blue-50 rounded-lg" wire:transition>
                            <div class="text-center">
                                <p class="text-sm font-medium text-gray-600">Subtotal</p>
                                <p class="text-lg font-bold">S/ {{ number_format($subtotal, 2) }}</p>
                            </div>
                            <div class="flex items-center justify-center"> <!-- Added flex for vertical alignment -->
                                <p class="text-3xl font-bold">+</p> <!-- Changed to text-3xl -->
                            </div>
                            <div class="text-center">
                                <p class="text-sm font-medium text-gray-600">Descuento</p>
                                <p class="text-lg font-bold text-red-600">-
                                    S/ {{ number_format($venta['descuento'] ?? 0, 2) }}</p>
                            </div>
                            <div class="flex items-center justify-center"> <!-- Added flex for vertical alignment -->
                                <p class="text-3xl font-bold">+</p> <!-- Changed to text-3xl -->
                            </div>
                            <div class="text-center">
                                <p class="text-sm font-medium text-gray-600">IGV (18%)</p>
                                <p class="text-lg font-bold">S/ {{ number_format($totalImpuesto, 2) }}</p>
                            </div>
                            <div class="flex items-center justify-center"> <!-- Added flex for vertical alignment -->
                                <p class="text-3xl font-bold">=</p> <!-- Changed to text-3xl -->
                            </div>
                            <div class="text-center">
                                <p class="text-sm font-medium text-gray-600">Total General</p>
                                <p class="text-lg font-bold text-green-600">S/ {{ number_format($totalGeneral, 2) }}</p>
                            </div>
                        </div>

                        <div class="flex gap-2 flex-col">
                            <label for="observacion">Observaciones</label>
                            <textarea wire:model="venta.observacion" name="observacion" id="observacion" rows="3"
                                      maxlength="1000"
                                      class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200 resize-none"
                                      placeholder="Observaciones adicionales sobre la venta..."></textarea>
                        </div>

                        <!-- Información de Pago -->
                        <div class="space-y-4">
                            <div class="flex items-center gap-2">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <h3 class="font-bold text-gray-700 text-lg">💳 Información de Pago</h3>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <!-- Método de Pago -->
                                <div class="flex gap-2 flex-col">
                                    <label class="font-medium text-gray-600">Método de Pago</label>
                                    <select wire:model="transaccionPago.id_metodo_pago"
                                            class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200">
                                        <option value="">Seleccionar método de pago</option>
                                        @foreach($metodosPago as $metodo)
                                            <option
                                                value="{{ $metodo->id_metodo_pago }}">{{ $metodo->nombre_metodo }}</option>
                                        @endforeach
                                    </select>
                                    @error('transaccionPago.id_metodo_pago')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Referencia de Pago (Opcional) -->
                                <div class="flex gap-2 flex-col">
                                    <label class="font-medium text-gray-600">Referencia/Número de Operación
                                        (Opcional)</label>
                                    <input type="text" wire:model="transaccionPago.referencia"
                                           class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200"
                                           placeholder="Número de operación, voucher, etc.">
                                    @error('transaccionPago.referencia')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Monto Pagado -->
                            <div class="grid grid-cols-2 gap-4">
                                <div class="flex gap-2 flex-col">
                                    <label class="font-medium text-gray-600">Monto Pagado</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">S/</span>
                                        <input type="text" wire:model="transaccionPago.monto"
                                               class="border rounded px-2 py-1 pl-8 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200 w-full"
                                               placeholder="0.00"
                                               wire:keydown.debounce.500ms="validarMontoPago">
                                    </div>
                                    @error('transaccionPago.monto')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror

                                    <!-- Indicador de diferencia -->
                                    @if($transaccionPago['monto'] > 0 && $transaccionPago['monto'] != $totalGeneral)
                                        @php
                                            $diferencia = $transaccionPago['monto'] - $totalGeneral;
                                        @endphp
                                        <div
                                            class="mt-1 text-sm {{ $diferencia >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            @if($diferencia > 0)
                                                💰 Cambio: S/ {{ number_format($diferencia, 2) }}
                                            @else
                                                ❌ Faltante: S/ {{ number_format(abs($diferencia), 2) }}
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <!-- Comprobante de Pago (Input File) -->
                                <div class="flex gap-2 flex-col">
                                    <label class="font-medium text-gray-600">Comprobante de Pago (Opcional)</label>
                                    <input type="file" wire:model="comprobanteTemporal"
                                           accept=".jpg,.jpeg,.png,.pdf"
                                           class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">

                                    @error('comprobanteTemporal')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror

                                    <!-- Información del archivo seleccionado -->
                                    @if ($comprobanteTemporal)
                                        <div class="mt-2 p-2 bg-green-50 border border-green-200 rounded text-sm">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <span
                                                        class="font-medium text-green-700">Archivo seleccionado:</span>
                                                    <span
                                                        class="text-green-600 ml-2">{{ $comprobanteTemporal->getClientOriginalName() }}</span>
                                                </div>
                                                <button type="button" wire:click="eliminarComprobante"
                                                        class="text-red-500 hover:text-red-700 text-sm">
                                                    ✕ Eliminar
                                                </button>
                                            </div>
                                            <div class="text-xs text-green-600 mt-1">
                                                Tamaño: {{ number_format($comprobanteTemporal->getSize() / 1024, 2) }}
                                                KB
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Información de formatos aceptados -->
                                    <div class="text-xs text-gray-500 mt-1">
                                        Formatos aceptados: JPG, JPEG, PNG, PDF (Máx. 5MB)
                                    </div>
                                </div>
                            </div>

                            <!-- Resumen de Pago -->
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <h4 class="font-semibold text-gray-700 mb-3">📊 Resumen de Pago</h4>
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <p class="text-gray-600">Total a Pagar:</p>
                                        <p class="font-bold text-lg">S/ {{ number_format($totalGeneral, 2) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600">Monto Ingresado:</p>
                                        <p class="font-bold text-lg {{ $transaccionPago['monto'] >= $totalGeneral ? 'text-green-600' : 'text-red-600' }}">
                                            S/ {{ number_format($transaccionPago['monto'] ?? 0, 2) }}
                                        </p>
                                    </div>
                                </div>

                                @if($transaccionPago['monto'] > 0)
                                    <div
                                        class="mt-3 p-3 rounded-lg {{ $transaccionPago['monto'] >= $totalGeneral ? 'bg-green-50 border border-green-200' : 'bg-yellow-50 border border-yellow-200' }}">
                                        <div class="flex items-center gap-2">
                                            @if($transaccionPago['monto'] == $totalGeneral)
                                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                                     viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                <span class="text-green-700 font-medium">✅ Pago exacto</span>
                                            @elseif($transaccionPago['monto'] > $totalGeneral)
                                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                                     viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span
                                                    class="text-green-700 font-medium">💰 Cambio: S/ {{ number_format($transaccionPago['monto'] - $totalGeneral, 2) }}</span>
                                            @else
                                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor"
                                                     viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                </svg>
                                                <span
                                                    class="text-yellow-700 font-medium">⚠️ Faltante: S/ {{ number_format($totalGeneral - $transaccionPago['monto'], 2) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
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
    <!-- Modal de Detalle de Venta -->
    <div x-data x-init="$watch('$wire.showModalDetalle', value => {
    if (value) { document.body.classList.add('overflow-hidden') } else { document.body.classList.remove('overflow-hidden') }
})">
        @if ($showModalDetalle && $ventaSeleccionada)
            <div class="fixed inset-0 flex items-center justify-center bg-black/50 z-50 p-4">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl h-[600px] overflow-y-auto">
                    <!-- Header -->
                    <div class="bg-white border-b border-gray-200 p-6 rounded-t-lg">
                        <div class="flex justify-between items-start">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-800 mb-2">📋 Detalles de Venta</h2>
                                <p class="text-gray-600">Información completa de la
                                    venta {{ $ventaSeleccionada->codigo ?? 'N/A' }}</p>
                            </div>
                            <div class="flex gap-2">
                                <button title="Descargar Comprobante" wire:click="exportarPdfVenta"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                         stroke-linejoin="round" class="lucide lucide-file-text">
                                        <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/>
                                        <path d="M14 2v4a2 2 0 0 0 2 2h4"/>
                                        <path d="M10 9H8"/>
                                        <path d="M16 13H8"/>
                                        <path d="M16 17H8"/>
                                    </svg>
                                    Exportar PDF
                                </button>
                                <button wire:click="closeModalDetalle"
                                        class="text-gray-500 hover:text-gray-700 transition-colors p-2">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Información General -->
                        <div class="grid grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <!-- Información de la Venta -->
                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                    <h3 class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Información de la Venta
                                    </h3>
                                    <div class="space-y-3 text-sm">
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600 font-medium">Código:</span>
                                            <span
                                                class="font-semibold text-gray-800">{{ $ventaSeleccionada->codigo ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600 font-medium">Fecha de Venta:</span>
                                            <span
                                                class="text-gray-800">{{ \Carbon\Carbon::parse($ventaSeleccionada->fecha_venta)->format('d/m/Y H:i') }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600 font-medium">Fecha de Registro:</span>
                                            <span
                                                class="text-gray-800">{{ \Carbon\Carbon::parse($ventaSeleccionada->fecha_registro)->format('d/m/Y H:i') }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600 font-medium">Total:</span>
                                            <span
                                                class="font-bold text-green-600 text-lg">S/ {{ number_format($ventaSeleccionada->total, 2) }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600 font-medium">Estado:</span>
                                            <span class="px-3 py-1 rounded-full text-xs font-medium
                                            {{ $ventaSeleccionada->estadoVenta->nombre_estado_venta_fisica === 'pendiente' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' :
                                               ($ventaSeleccionada->estadoVenta->nombre_estado_venta_fisica === 'completado' ? 'bg-green-100 text-green-800 border border-green-200' :
                                               'bg-red-100 text-red-800 border border-red-200') }}">
                                            {{ ucfirst($ventaSeleccionada->estadoVenta->nombre_estado_venta_fisica) }}
                                        </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Información del Cliente -->
                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                    <h3 class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        Información del Cliente
                                    </h3>
                                    <div class="space-y-3 text-sm">
                                        @if($ventaSeleccionada->cliente && $ventaSeleccionada->cliente->persona)
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-600 font-medium">Nombre:</span>
                                                <span class="font-semibold text-gray-800">
                                                {{ $ventaSeleccionada->cliente->persona->nombre }}
                                                    {{ $ventaSeleccionada->cliente->persona->apellido_paterno }}
                                                    {{ $ventaSeleccionada->cliente->persona->apellido_materno }}
                                            </span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-600 font-medium">DNI:</span>
                                                <span
                                                    class="text-gray-800">{{ $ventaSeleccionada->cliente->persona->numero_documento }}</span>
                                            </div>
                                            @if($ventaSeleccionada->cliente->persona->numero_telefono_personal)
                                                <div class="flex justify-between items-center">
                                                    <span class="text-gray-600 font-medium">Teléfono:</span>
                                                    <span
                                                        class="text-gray-800">{{ $ventaSeleccionada->cliente->persona->numero_telefono_personal }}</span>
                                                </div>
                                            @endif
                                            @if($ventaSeleccionada->cliente->persona->correo_electronico_personal)
                                                <div class="flex justify-between items-center">
                                                    <span class="text-gray-600 font-medium">Email:</span>
                                                    <span
                                                        class="text-gray-800">{{ $ventaSeleccionada->cliente->persona->correo_electronico_personal }}</span>
                                                </div>
                                            @endif
                                        @else
                                            <div class="text-center text-gray-500 py-2">
                                                <p>Información del cliente no disponible</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <!-- Información del Vendedor -->
                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                    <h3 class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        Información del Vendedor
                                    </h3>
                                    <div class="space-y-3 text-sm">
                                        @if($ventaSeleccionada->trabajador && $ventaSeleccionada->trabajador->persona)
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-600 font-medium">Nombre:</span>
                                                <span class="font-semibold text-gray-800">
                                                {{ $ventaSeleccionada->trabajador->persona->nombre }}
                                                    {{ $ventaSeleccionada->trabajador->persona->apellido_paterno }}
                                            </span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-600 font-medium">DNI:</span>
                                                <span
                                                    class="text-gray-800">{{ $ventaSeleccionada->trabajador->persona->numero_documento }}</span>
                                            </div>
                                        @else
                                            <div class="text-center text-gray-500 py-2">
                                                <p>Información del vendedor no disponible</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Resumen Financiero -->
                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                    <h3 class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Resumen Financiero
                                    </h3>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600">Subtotal:</span>
                                            <span
                                                class="font-medium">S/ {{ number_format($ventaSeleccionada->subtotal, 2) }}</span>
                                        </div>
                                        @if($ventaSeleccionada->descuento > 0)
                                            <div class="flex justify-between items-center text-red-600">
                                                <span class="text-gray-600">Descuento:</span>
                                                <span
                                                    class="font-medium">- S/ {{ number_format($ventaSeleccionada->descuento, 2) }}</span>
                                            </div>
                                        @endif
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600">IGV (18%):</span>
                                            <span
                                                class="font-medium">S/ {{ number_format($ventaSeleccionada->impuesto, 2) }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600">Comprobante:</span>
                                            @if($ventaSeleccionada->transaccionPago && $ventaSeleccionada->transaccionPago->comprobante_url)
                                                <button
                                                    wire:click="descargarComprobanteVenta({{ $ventaSeleccionada->id_venta }})"
                                                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs flex items-center gap-1 transition-colors">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                         viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                    </svg>
                                                    Descargar
                                                </button>
                                            @else
                                                <span class="text-gray-400 text-xs">No disponible</span>
                                            @endif
                                        </div>
                                        <div
                                            class="flex justify-between items-center border-t border-gray-200 pt-2 mt-2">
                                            <span class="text-gray-800 font-bold">Total General:</span>
                                            <span
                                                class="text-green-600 font-bold text-lg">S/ {{ number_format($ventaSeleccionada->total, 2) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Información de Pago -->
                                @if($ventaSeleccionada->transaccionPago)
                                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                        <h3 class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                            </svg>
                                            Información de Pago
                                        </h3>
                                        <div class="space-y-3 text-sm">
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-600 font-medium">Método:</span>
                                                <span
                                                    class="font-semibold text-gray-800">{{ $ventaSeleccionada->transaccionPago->metodoPago->nombre_metodo }}</span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-600 font-medium">Monto Pagado:</span>
                                                <span
                                                    class="font-bold text-green-600">S/ {{ number_format($ventaSeleccionada->transaccionPago->monto, 2) }}</span>
                                            </div>
                                            @if($ventaSeleccionada->transaccionPago->referencia)
                                                <div class="flex justify-between items-center">
                                                    <span class="text-gray-600 font-medium">Referencia:</span>
                                                    <span
                                                        class="font-mono text-gray-800">{{ $ventaSeleccionada->transaccionPago->referencia }}</span>
                                                </div>
                                            @endif
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-600 font-medium">Estado Pago:</span>
                                                <span class="px-2 py-1 rounded text-xs font-medium
                                                {{ $ventaSeleccionada->transaccionPago->estado === 'completado' ? 'bg-green-100 text-green-800' :
                                                   ($ventaSeleccionada->transaccionPago->estado === 'pendiente' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                {{ ucfirst($ventaSeleccionada->transaccionPago->estado) }}
                                            </span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Productos de la Venta -->
                        <div class="bg-white border border-gray-200 rounded-lg">
                            <h3 class="font-semibold text-gray-700 p-4 border-b border-gray-200 flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                Productos y Servicios de la Venta
                            </h3>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-50">
                                    <tr>
                                        <th class="text-left p-3 font-medium text-gray-700 border-b border-gray-200">
                                            Item
                                        </th>
                                        <th class="text-left p-3 font-medium text-gray-700 border-b border-gray-200">
                                            Tipo
                                        </th>
                                        <th class="text-center p-3 font-medium text-gray-700 border-b border-gray-200">
                                            Cantidad
                                        </th>
                                        <th class="text-right p-3 font-medium text-gray-700 border-b border-gray-200">
                                            Precio Unit.
                                        </th>
                                        <th class="text-right p-3 font-medium text-gray-700 border-b border-gray-200">
                                            Subtotal
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                    @foreach($ventaSeleccionada->detalleVentas as $detalle)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="p-3 border-b border-gray-100">
                                                @if($detalle->tipo_item === 'producto')
                                                    {{ $detalle->producto->nombre_producto ?? 'N/A' }}
                                                @else
                                                    {{ $detalle->servicio->nombre_servicio ?? 'N/A' }}
                                                @endif
                                            </td>
                                            <td class="p-3 border-b border-gray-100 capitalize">
                                            <span class="px-2 py-1 rounded text-xs font-medium
                                                {{ $detalle->tipo_item === 'producto' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                                {{ $detalle->tipo_item }}
                                            </span>
                                            </td>
                                            <td class="p-3 text-center border-b border-gray-100">{{ $detalle->cantidad }}</td>
                                            <td class="p-3 text-right border-b border-gray-100">
                                                S/ {{ number_format($detalle->precio_unitario, 2) }}</td>
                                            <td class="p-3 text-right border-b border-gray-100 font-medium">
                                                S/ {{ number_format($detalle->subtotal, 2) }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="4"
                                            class="p-3 text-right font-semibold text-gray-700 border-t border-gray-200">
                                            Total:
                                        </td>
                                        <td class="p-3 text-right font-bold text-green-600 border-t border-gray-200">
                                            S/ {{ number_format($ventaSeleccionada->total, 2) }}
                                        </td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- Observaciones -->
                        @if($ventaSeleccionada->observacion)
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <h3 class="font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    Observaciones
                                </h3>
                                <p class="text-gray-700 text-sm">{{ $ventaSeleccionada->observacion }}</p>
                            </div>
                        @endif

                        <!-- Botones de Acción -->
                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                            @if($ventaSeleccionada->estadoVenta->nombre_estado_venta_fisica == 'pendiente')
                                <button wire:click="completarVenta"
                                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Completar Venta
                                </button>
                                <button wire:click="cancelarVenta"
                                        class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Cancelar Venta
                                </button>
                            @endif
                            <button wire:click="closeModalDetalle"
                                    class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                                Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Modal para Transacciones Web -->
    <div x-data x-init="$watch('$wire.showModalTransaccion', value => {
    if (value) { document.body.classList.add('overflow-hidden') } else { document.body.classList.remove('overflow-hidden') }
})">
        @if ($showModalTransaccion && $ventaWebSeleccionada && $transaccionPago)
            <div class="fixed inset-0 flex items-center justify-center bg-black/50 z-50 p-4">
                <div class="bg-white rounded-xl shadow-xl w-full max-w-4xl max-h-[95vh] overflow-y-auto">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-6 text-white rounded-t-xl">
                        <div class="flex justify-between items-start">
                            <div>
                                <h2 class="text-2xl font-bold mb-2">📋 Revisión de Venta Web</h2>
                                <p class="text-blue-100">Verificación de comprobante y aprobación de venta</p>
                            </div>
                            <button wire:click="cerrarModalTransaccion"
                                    class="text-white hover:text-blue-200 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Información General -->
                        <div class="grid grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h3 class="font-semibold text-gray-700 mb-3">📦 Información de la Venta</h3>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Código:</span>
                                            <span class="font-semibold">{{ $ventaWebSeleccionada->codigo }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Fecha:</span>
                                            <span>{{ \Carbon\Carbon::parse($ventaWebSeleccionada->fecha_venta)->format('d/m/Y H:i') }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Total:</span>
                                            <span
                                                class="font-bold text-green-600">S/ {{ number_format($ventaWebSeleccionada->total, 2) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Estado:</span>
                                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                            {{ $ventaWebSeleccionada->estadoVenta->nombre_estado_venta_fisica === 'pendiente' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                            {{ $ventaWebSeleccionada->estadoVenta->nombre_estado_venta_fisica }}
                                        </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h3 class="font-semibold text-gray-700 mb-3">👤 Información del Cliente</h3>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Nombre:</span>
                                            <span class="font-semibold">
                                            {{ $ventaWebSeleccionada->cliente->persona->nombre }}
                                                {{ $ventaWebSeleccionada->cliente->persona->apellido_paterno }}
                                        </span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">DNI:</span>
                                            <span>{{ $ventaWebSeleccionada->cliente->persona->numero_documento }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Email:</span>
                                            <span>{{ $ventaWebSeleccionada->cliente->persona->correo_electronico_personal }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Teléfono:</span>
                                            <span>{{ $ventaWebSeleccionada->cliente->persona->numero_telefono_personal ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h3 class="font-semibold text-gray-700 mb-3">💳 Información de Pago</h3>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Método:</span>
                                            <span
                                                class="font-semibold">{{ $transaccionPago->metodoPago->nombre_metodo }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Estado:</span>
                                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                            {{ $transaccionPago->estado === 'pendiente' ? 'bg-yellow-100 text-yellow-800' :
                                               ($transaccionPago->estado === 'confirmado' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                            {{ $transaccionPago->estado }}
                                        </span>
                                        </div>
                                        @if($transaccionPago->referencia)
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Referencia:</span>
                                                <span class="font-mono">{{ $transaccionPago->referencia }}</span>
                                            </div>
                                        @endif
                                        @if($transaccionPago->fecha_pago)
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Fecha Pago:</span>
                                                <span>{{ $transaccionPago->fecha_pago->format('d/m/Y H:i') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Comprobante -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h3 class="font-semibold text-gray-700 mb-3">📎 Comprobante de Pago</h3>
                                    @if($transaccionPago->comprobante_url)
                                        <div class="text-center">
                                            @if(pathinfo($transaccionPago->comprobante_url, PATHINFO_EXTENSION) === 'pdf')
                                                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-3">
                                                    <svg class="w-12 h-12 text-red-500 mx-auto mb-2" fill="none"
                                                         stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                    <p class="text-red-700 font-medium">Documento PDF</p>
                                                </div>
                                            @else
                                                <img src="{{ $transaccionPago->comprobante_url }}"
                                                     alt="Comprobante de pago"
                                                     class="max-w-full h-48 object-contain mx-auto rounded-lg border border-gray-200 mb-3">
                                            @endif

                                            <a href="{{ $transaccionPago->comprobante_url }}"
                                               target="_blank"
                                               class="inline-flex items-center gap-2 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                     viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                </svg>
                                                Ver Comprobante Completo
                                            </a>
                                        </div>
                                    @else
                                        <div class="text-center text-gray-500 py-4">
                                            <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                                            </svg>
                                            <p>No hay comprobante disponible</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Productos de la Venta -->
                        <div class="bg-white border border-gray-200 rounded-lg">
                            <h3 class="font-semibold text-gray-700 p-4 border-b">🛒 Productos de la Venta</h3>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-50">
                                    <tr>
                                        <th class="text-left p-3 font-medium">Producto/Servicio</th>
                                        <th class="text-left p-3 font-medium">Tipo</th>
                                        <th class="text-center p-3 font-medium">Cantidad</th>
                                        <th class="text-right p-3 font-medium">Precio Unit.</th>
                                        <th class="text-right p-3 font-medium">Subtotal</th>
                                    </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                    @foreach($ventaWebSeleccionada->detalleVentas as $detalle)
                                        <tr>
                                            <td class="p-3">
                                                @if($detalle->tipo_item === 'producto')
                                                    {{ $detalle->producto->nombre_producto ?? 'N/A' }}
                                                @else
                                                    {{ $detalle->servicio->nombre_servicio ?? 'N/A' }}
                                                @endif
                                            </td>
                                            <td class="p-3 capitalize">{{ $detalle->tipo_item }}</td>
                                            <td class="p-3 text-center">{{ $detalle->cantidad }}</td>
                                            <td class="p-3 text-right">
                                                S/ {{ number_format($detalle->precio_unitario, 2) }}</td>
                                            <td class="p-3 text-right">
                                                S/ {{ number_format($detalle->subtotal, 2) }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="4" class="p-3 text-right font-semibold">Total:</td>
                                        <td class="p-3 text-right font-bold text-green-600">
                                            S/ {{ number_format($ventaWebSeleccionada->total, 2) }}
                                        </td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        @if($transaccionPago->estado === 'pendiente')
                            <div class="flex justify-end gap-4 pt-6 border-t">
                                <button wire:click="rechazarVentaWeb"
                                        wire:confirm="¿Estás seguro de rechazar esta venta? El stock será revertido."
                                        class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg font-semibold transition-colors flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Rechazar Venta
                                </button>

                                <button wire:click="aprobarVentaWeb"
                                        wire:confirm="¿Estás seguro de aprobar esta venta? El pago será confirmado."
                                        class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-semibold transition-colors flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Aprobar Venta
                                </button>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <p class="text-gray-600">
                                    Esta venta ya ha sido
                                    <span
                                        class="font-semibold {{ $transaccionPago->estado === 'confirmado' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $transaccionPago->estado === 'confirmado' ? 'APROBADA' : 'RECHAZADA' }}
                            </span>
                                </p>
                            </div>
                        @endif
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
        <style>
            .search-dropdown {
                scrollbar-width: thin;
                scrollbar-color: #cbd5e0 #f7fafc;
            }

            .search-dropdown::-webkit-scrollbar {
                width: 6px;
            }

            .search-dropdown::-webkit-scrollbar-track {
                background: #f7fafc;
                border-radius: 3px;
            }

            .search-dropdown::-webkit-scrollbar-thumb {
                background: #cbd5e0;
                border-radius: 3px;
            }

            .search-dropdown::-webkit-scrollbar-thumb:hover {
                background: #a0aec0;
            }
        </style>
    @endpush
    <x-loader target="guardar, completarVenta, cancelarVenta"/>
</x-panel>
