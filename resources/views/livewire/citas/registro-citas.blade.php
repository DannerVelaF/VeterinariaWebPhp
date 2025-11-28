<x-panel title="Gestión de Citas" :breadcrumbs="[
    ['label' => 'Inicio', 'href' => '/', 'icon' => 'home'],
    ['label' => 'Citas', 'href' => '#'],
    ['label' => 'Registro de citas'],
]">
    <div class="grid grid-cols-5 gap-4 mb-4">
        <!-- Agregar esta tarjeta en el grid de estadísticas -->
        <x-card>
            <div class="h-[100px] flex flex-col justify-between">
                <div class="flex justify-between items-center">
                    <p>No asistieron</p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-user-x text-gray-500">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="m17 8 5 5"/>
                        <path d="m22 8-5 5"/>
                    </svg>
                </div>
                <p class="font-medium text-3xl">{{ $cantCitasNoAsistio }}</p>
            </div>
        </x-card>
        <!-- Citas Pendientes -->
        <x-card>
            <div class="h-[100px] flex flex-col justify-between">
                <div class="flex justify-between items-center">
                    <p>Citas pendientes</p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-clock text-orange-500">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
                <p class="font-medium text-3xl">{{ $cantCitasPendientes }}</p>
            </div>
        </x-card>

        <!-- Citas Confirmadas -->
        <x-card>
            <div class="h-[100px] flex flex-col justify-between">
                <div class="flex justify-between items-center">
                    <p>Citas confirmadas</p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-check-circle text-blue-500">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                </div>
                <p class="font-medium text-3xl">{{ $cantCitasConfirmadas }}</p>
            </div>
        </x-card>

        <!-- Citas Canceladas -->
        <x-card>
            <div class="h-[100px] flex flex-col justify-between">
                <div class="flex justify-between items-center">
                    <p>Citas canceladas</p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-x-circle text-red-500">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="m15 9-6 6"/>
                        <path d="m9 9 6 6"/>
                    </svg>
                </div>
                <p class="font-medium text-3xl">{{ $cantCitasCanceladas }}</p>
            </div>
        </x-card>

        <!-- Citas Completadas -->
        <x-card>
            <div class="h-[100px] flex flex-col justify-between">
                <div class="flex justify-between items-center">
                    <p>Citas completadas</p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-calendar-check text-green-500">
                        <path d="M8 2v4"/>
                        <path d="M16 2v4"/>
                        <rect width="18" height="18" x="3" y="4" rx="2"/>
                        <path d="M3 10h18"/>
                        <path d="m9 16 2 2 4-4"/>
                    </svg>
                </div>
                <p class="font-medium text-3xl">{{ $cantCitasCompletadas }}</p>
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
                <p class="font-medium text-gray-600 text-xl">Registro de Citas</p>
                <p class="font-medium text-gray-600 text-sm">Gestiona las citas programadas para las mascotas</p>
            </div>
            <div class="flex gap-2">
                <button wire:click="openModal"
                    class="inline-flex items-center gap-2 px-4 py-2 h-10 bg-blue-600 hover:bg-blue-700 transition text-white rounded-lg font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-plus">
                        <path d="M5 12h14"/>
                        <path d="M12 5v14"/>
                    </svg>
                    Nueva Cita
                </button>
            </div>
        </div>

        <livewire:cita-table />

    </x-card>

    <!-- Modal para Nueva Cita -->
    @if ($showModal)
        <div class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
            <div class="bg-white rounded-xl shadow-xl w-2/3 p-6 space-y-4 max-h-[90vh] overflow-y-auto">
                <div>
                    <h2 class="text-xl font-medium mb-1">Registrar Nueva Cita</h2>
                    <p class="text-sm font-medium">Programa una nueva cita para una mascota</p>
                </div>

                <form class="space-y-4 w-full" wire:submit.prevent="guardar">
                    
                <!-- Información del Cliente -->
                <div class="col-span-2">
                    <h3 class="font-bold text-gray-700 text-base mb-2">👤 Información del Cliente</h3>
                    <p class="text-gray-500 text-xs mb-3">Busca un cliente por su DNI o nombre para asociarlo a la cita.</p>

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
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <!-- Botón de búsqueda manual a la derecha -->
                                <button type="button" wire:click="buscarClientes"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center hover:text-blue-600 transition-colors"
                                    title="Buscar cliente">
                                    <svg class="h-5 w-5 text-gray-400 hover:text-blue-600" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
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
                                                            <span class="font-semibold text-gray-800 text-sm">
                                                                @if($cliente->persona)
                                                                    {{ $cliente->persona->nombre }} 
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
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                                                        </path>
                                                                    </svg>
                                                                    {{ $cliente->persona->numero_telefono_personal }}
                                                                </span>
                                                            @endif
                                                            @if ($cliente->persona && $cliente->persona->correo_electronico_personal)
                                                                <span class="flex items-center">
                                                                    <svg class="w-3 h-3 mr-1" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
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
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7"></path>
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
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
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
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <h4 class="font-bold text-green-800 text-base">Cliente Seleccionado</h4>
                                    </div>
                                    <button type="button" wire:click="limpiarCliente"
                                        class="text-red-500 text-xs font-bold hover:underline flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
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

                    <!-- Botones de redirección -->
                    <div class="flex gap-2 mt-4">
                        <button type="button" 
                                wire:click="redirigirAClientes"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-purple-500 hover:bg-purple-600 text-white rounded transition">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            Registro de Clientes
                        </button>

                        @if ($clienteSeleccionado)
                        <button type="button" 
                                wire:click="redirigirAMascotas"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded transition">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus">
                                <path d="M12 5v14"/>
                                <path d="M5 12h14"/>
                            </svg>
                            Registrar Mascota
                        </button>
                        @endif
                    </div>
                </div>

                    <!-- Selección de Mascota -->
                    <div class="col-span-2">
                        <h3 class="font-bold text-gray-700 text-base mb-2">🐾 Mascota</h3>
                        
                        @if (!$clienteSeleccionado)
                            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 text-center">
                                <svg class="w-8 h-8 text-orange-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                                <p class="text-orange-600 font-medium">Primero seleccione un cliente para cargar sus mascotas</p>
                            </div>
                        @else
                            <div class="flex gap-2 flex-col">
                                <label class="font-semibold">Seleccionar Mascota:</label>
                                <select wire:model="mascotaSeleccionada" 
                                    class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200">
                                    <option value="">Seleccione una mascota</option>
                                    @foreach ($mascotas as $mascota)
                                        <option value="{{ $mascota->id_mascota }}">
                                            {{ $mascota->nombre_mascota }} 
                                            ({{ $mascota->raza?->nombre_raza ?? 'Sin raza' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('mascotaSeleccionada')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                                
                                @if($mascotas->count() === 0)
                                    <div class="bg-red-50 border border-red-200 rounded-lg p-3 mt-2">
                                        <p class="text-red-600 text-sm">
                                            ❌ Este cliente no tiene mascotas registradas. 
                                            <button type="button" wire:click="redirigirAMascotas" class="text-red-700 font-semibold underline hover:no-underline">
                                                Click aquí para registrar una mascota
                                            </button>
                                        </p>
                                    </div>
                                @else
                                    <p class="text-green-600 text-sm mt-1">
                                        ✅ {{ $mascotas->count() }} mascota(s) encontrada(s)
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- En resources/views/livewire/citas/registro-citas.blade.php -->
                      <!-- SECCIÓN 2: SERVICIOS -->
                <div class="border-t pt-6">
                    <h3 class="font-bold text-gray-700 text-lg mb-4">🛎️ Servicios Solicitados</h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        @foreach($serviciosDisponibles as $servicio)
                            <label class="flex items-start space-x-3 p-4 border border-gray-200 rounded-lg hover:border-blue-300 hover:bg-blue-50 cursor-pointer transition-all duration-200">
                                <input type="checkbox" 
                                       wire:model.live="serviciosSeleccionados" 
                                       value="{{ $servicio->id_servicio }}"
                                       class="mt-1 h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $servicio->nombre_servicio }}</p>
                                            <p class="text-sm text-gray-600 mt-1">
                                                ⏱️ {{ $servicio->duracion_estimada }} min • 
                                                💰 S/ {{ number_format($servicio->precio_unitario, 2) }}
                                            </p>
                                        </div>
                                    </div>
                                    @if($servicio->descripcion)
                                        <p class="text-xs text-gray-500 mt-2">{{ $servicio->descripcion }}</p>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>

                    @error('serviciosSeleccionados')
                        <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        </div>
                    @enderror

                    @if(count($serviciosSeleccionados) > 0)
                        <div class="mt-4 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-blue-800 font-semibold">
                                        📋 {{ count($serviciosSeleccionados) }} servicio(s) seleccionado(s)
                                    </p>
                                    <p class="text-blue-600 text-sm mt-1">
                                        @php
                                            $horas = floor($duracionTotal / 60);
                                            $minutos = $duracionTotal % 60;
                                            $duracionFormateada = $horas > 0 ? "{$horas}h {$minutos}min" : "{$minutos} min";
                                        @endphp
                                        ⏱️ Duración total estimada: {{ $duracionFormateada }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-blue-800 font-bold text-lg">
                                        S/ {{ number_format(collect($serviciosDisponibles)->whereIn('id_servicio', $serviciosSeleccionados)->sum('precio_unitario'), 2) }}
                                    </p>
                                    <p class="text-blue-600 text-xs">Costo total</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- SECCIÓN 3: TRABAJADOR -->
                <div class="border-t pt-6">
                    <h3 class="font-bold text-gray-700 text-lg mb-4">👨‍⚕️ Seleccionar Trabajador</h3>
                    
                    <div class="grid grid-cols-2 gap-6">
                        <!-- Selector de Trabajador -->
                        <div class="space-y-3">
                            <label class="font-semibold text-gray-700">Trabajador Asignado:</label>
                            <select wire:model.live="trabajadorSeleccionado"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Seleccione un trabajador</option>
                                @foreach ($trabajadores as $trabajador)
                                    <option value="{{ $trabajador->id_trabajador }}">
                                        @if($trabajador->persona)
                                            {{ $trabajador->persona->nombre }} {{ $trabajador->persona->apellido_paterno }}
                                            ({{ $trabajador->puestoTrabajo?->nombre_puesto ?? 'Sin puesto' }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('trabajadorSeleccionado')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Horarios del Trabajador -->
                        @if($trabajadorSeleccionado && $this->infoTurnosTrabajador)
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <h4 class="font-semibold text-blue-800 mb-3 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Horarios del Trabajador
                                </h4>
                                <div class="grid grid-cols-7 gap-1 text-xs">
                                    @foreach (['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'] as $index => $diaCorto)
                                        @php
                                            $diasCompletos = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                                            $diaCompleto = $diasCompletos[$index];
                                            $horarioDia = collect($this->infoTurnosTrabajador)
                                                ->pluck('horarios')
                                                ->flatten(1)
                                                ->where('dia', $diaCompleto)
                                                ->first();
                                        @endphp
                                        <div class="text-center p-2 rounded {{ $horarioDia && !$horarioDia['descanso'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            <div class="font-semibold">{{ $diaCorto }}</div>
                                            <div class="text-xs mt-1">
                                                @if($horarioDia && !$horarioDia['descanso'])
                                                    {{ \Carbon\Carbon::parse($horarioDia['inicio'])->format('H:i') }}-{{ \Carbon\Carbon::parse($horarioDia['fin'])->format('H:i') }}
                                                @else
                                                    <span class="text-red-600">✗</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- SECCIÓN 4: FECHA Y HORA -->
                @if($trabajadorSeleccionado && count($serviciosSeleccionados) > 0)
                <div class="border-t pt-6">
                    <h3 class="font-bold text-gray-700 text-lg mb-4">📅 Seleccionar Fecha y Hora</h3>
                    
                    <div class="grid grid-cols-2 gap-6">
                        <!-- Selector de Fecha -->
                        <div class="space-y-3">
                            <label class="font-semibold text-gray-700">Fecha de la Cita:</label>
                            <input wire:model.live="fechaSeleccionada" type="date" 
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                min="{{ now()->format('Y-m-d') }}">
                            @error('fechaSeleccionada')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                            
                            @if($fechaSeleccionada)
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                    <p class="text-sm text-gray-600">
                                        📅 {{ \Carbon\Carbon::parse($fechaSeleccionada)->translatedFormat('l, d \\d\\e F \\d\\e Y') }}
                                    </p>
                                </div>
                            @endif
                        </div>

                        <!-- Horarios Disponibles -->
                        <div class="space-y-3">
                            <label class="font-semibold text-gray-700">Horarios Disponibles:</label>
                            
                            @if(count($horariosDisponibles) > 0)
                                <div class="grid grid-cols-3 gap-2 max-h-48 overflow-y-auto p-3 border border-gray-200 rounded-lg bg-white">
                                    @foreach ($horariosDisponibles as $slot)
                                        <button type="button" 
                                            wire:click="seleccionarHora('{{ $slot['formato'] }}', '{{ $slot['fecha_completa'] }}')"
                                            class="p-3 text-sm border rounded-lg transition-all duration-200 
                                                   {{ $horaSeleccionada === $slot['formato'] 
                                                       ? 'bg-green-500 text-white border-green-600 shadow-md transform scale-105' 
                                                       : 'bg-white border-gray-300 hover:bg-green-50 hover:border-green-300 hover:shadow-sm' }}">
                                            <div class="font-medium">{{ $slot['formato'] }}</div>
                                            <div class="text-xs opacity-75 mt-1">
                                                @php
                                                    $horas = floor($duracionTotal / 60);
                                                    $minutos = $duracionTotal % 60;
                                                    $duracionFormateada = $horas > 0 ? "{$horas}h {$minutos}min" : "{$minutos} min";
                                                @endphp
                                                {{ $duracionFormateada }}
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                                
                                @if($horaSeleccionada)
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                        <p class="text-green-700 font-semibold flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Hora seleccionada: {{ $horaSeleccionada }}
                                        </p>
                                        <p class="text-green-600 text-sm mt-1">
                                            Duración: 
                                            @php
                                                $horas = floor($duracionTotal / 60);
                                                $minutos = $duracionTotal % 60;
                                                $duracionFormateada = $horas > 0 ? "{$horas}h {$minutos}min" : "{$minutos} min";
                                            @endphp
                                            {{ $duracionFormateada }}
                                        </p>
                                    </div>
                                @endif
                            @else
                                <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                                    <svg class="w-8 h-8 text-red-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                    </svg>
                                    <p class="text-red-700 font-medium">No hay horarios disponibles</p>
                                    <p class="text-red-600 text-sm mt-1">
                                        El trabajador no tiene disponibilidad para esta fecha
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Campo oculto para la fecha completa -->
                    <input type="hidden" wire:model="cita.fecha_programada">
                    @error('cita.fecha_programada')
                        <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        </div>
                    @enderror
                </div>
                @endif

                <!-- SECCIÓN 5: INFORMACIÓN ADICIONAL -->
                @if($horaSeleccionada)
                <div class="border-t pt-6">
                    <h3 class="font-bold text-gray-700 text-lg mb-4">📝 Información Adicional</h3>
                    
                    <div class="grid grid-cols-2 gap-6">
                        <!-- Motivo -->
                        <div class="space-y-3">
                            <label for="motivo" class="font-semibold text-gray-700">💬 Motivo de la Cita</label>
                            <textarea wire:model="cita.motivo" id="motivo" rows="4"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                placeholder="Describa el motivo de la cita..."></textarea>
                            @error('cita.motivo')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Observaciones y Estado -->
                        <div class="space-y-4">
                            <div class="space-y-3">
                                <label for="observaciones" class="font-semibold text-gray-700">📋 Observaciones</label>
                                <textarea wire:model="cita.observaciones" id="observaciones" rows="2"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                    placeholder="Observaciones adicionales (opcional)..."></textarea>
                                @error('cita.observaciones')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="space-y-3">
                                <label for="estado" class="font-semibold text-gray-700">📊 Estado de la Cita</label>
                                <select wire:model="estadoCitaSeleccionado" id="estado"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Seleccione estado</option>
                                    @foreach ($estadosCita as $estado)
                                        <option value="{{ $estado->id_estado_cita }}">
                                            {{ $estado->nombre_estado_cita }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('estadoCitaSeleccionado')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- BOTONES DE ACCIÓN -->
                <div class="border-t pt-6 flex justify-between items-center">
                    <div class="text-sm text-gray-500">
                        @if($horaSeleccionada)
                            <p>✅ Cita programada para: {{ \Carbon\Carbon::parse($cita['fecha_programada'])->translatedFormat('l, d \\d\\e F \\d\\e Y \\a \\l\\a\\s H:i') }}</p>
                        @else
                            <p>Complete todos los pasos para programar la cita</p>
                        @endif
                    </div>
                    
                    <div class="flex gap-3">
                        <button type="button" wire:click="closeModal"
                            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium shadow-sm hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
                            {{ !$horaSeleccionada ? 'disabled' : '' }}>
                            🗓️ Programar Cita
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endif


    <!-- Modal de Detalle de Cita -->
    <!-- Modal de Detalle de Cita - MEJORADO -->
<div x-data x-init="$watch('$wire.showModalDetalle', value => {
    if (value) { document.body.classList.add('overflow-hidden') } else { document.body.classList.remove('overflow-hidden') }
})">
    @if ($showModalDetalle && $citaSeleccionada)
        <div class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
            <div class="bg-white rounded-xl shadow-xl w-3/4 max-w-4xl p-6 max-h-[90vh] overflow-y-auto">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6 pb-4 border-b">
                    <div>
                        <p class="text-2xl font-bold text-gray-800">Detalles de la Cita</p>
                        <p class="text-sm text-gray-600 mt-1">
                            Información completa de la cita #{{ $citaSeleccionada->id_cita }}
                        </p>
                    </div>
                    <button wire:click="closeModalDetalle"
                        class="text-gray-500 hover:text-gray-700 p-2 rounded-full hover:bg-gray-100 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18"/>
                            <path d="m6 6 12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Información Principal -->
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <!-- Fecha y Hora -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="font-semibold text-blue-800">Fecha y Hora</p>
                        </div>
                        <p class="text-lg font-bold text-blue-900">
                            {{ \Carbon\Carbon::parse($citaSeleccionada->fecha_programada)->format('d/m/Y') }}
                        </p>
                        <p class="text-sm text-blue-700">
                            {{ \Carbon\Carbon::parse($citaSeleccionada->fecha_programada)->format('H:i') }} horas
                        </p>
                    </div>

                    <!-- Estado -->
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="font-semibold text-purple-800">Estado</p>
                        </div>
                        @php
                            $estadoColors = [
                                'pendiente' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                'confirmada' => 'bg-blue-100 text-blue-800 border-blue-200',
                                'en progreso' => 'bg-orange-100 text-orange-800 border-orange-200',
                                'completada' => 'bg-green-100 text-green-800 border-green-200',
                                'cancelada' => 'bg-red-100 text-red-800 border-red-200',
                                'no asistio' => 'bg-gray-100 text-gray-800 border-gray-200'
                            ];
                            $estadoClass = $estadoColors[strtolower($citaSeleccionada->estadoCita?->nombre_estado_cita)] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                        @endphp
                        <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full border {{ $estadoClass }}">
                            {{ $citaSeleccionada->estadoCita?->nombre_estado_cita ?? 'N/A' }}
                        </span>
                    </div>

                    <!-- Duración -->
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="font-semibold text-green-800">Duración</p>
                        </div>
                        <p class="text-lg font-bold text-green-900">
                            @php
                                $servicios = $citaSeleccionada->servicios ?? collect();
                                $duracionTotal = $servicios->sum('duracion_estimada') ?: 60;
                                $horas = floor($duracionTotal / 60);
                                $minutos = $duracionTotal % 60;
                                $duracionFormateada = $horas > 0 ? "{$horas}h {$minutos}min" : "{$minutos} min";
                            @endphp
                            {{ $duracionFormateada }}
                        </p>
                        <p class="text-sm text-green-700">
                            {{ $servicios->count() }} servicio(s)
                        </p>
                    </div>
                </div>

                <!-- Información de Participantes -->
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <!-- Cliente -->
                    <div class="border border-gray-200 rounded-lg p-4 bg-white">
                        <div class="flex items-center mb-3">
                            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <h4 class="font-bold text-gray-800">Cliente</h4>
                        </div>
                        @if($citaSeleccionada->cliente && $citaSeleccionada->cliente->persona)
                            <div class="space-y-2">
                                <div>
                                    <p class="font-semibold text-gray-900 text-lg">
                                        {{ $citaSeleccionada->cliente->persona->nombre }} 
                                        {{ $citaSeleccionada->cliente->persona->apellido_paterno }}
                                        {{ $citaSeleccionada->cliente->persona->apellido_materno }}
                                    </p>
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    DNI: {{ $citaSeleccionada->cliente->persona->numero_documento }}
                                </div>
                                @if($citaSeleccionada->cliente->persona->numero_telefono_personal)
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    {{ $citaSeleccionada->cliente->persona->numero_telefono_personal }}
                                </div>
                                @endif
                                @if($citaSeleccionada->cliente->persona->correo_electronico_personal)
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $citaSeleccionada->cliente->persona->correo_electronico_personal }}
                                </div>
                                @endif
                            </div>
                        @else
                            <p class="text-gray-500 italic">Cliente no disponible</p>
                        @endif
                    </div>

                    <!-- Mascota -->
                    <div class="border border-gray-200 rounded-lg p-4 bg-white">
                        <div class="flex items-center mb-3">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                            </svg>
                            <h4 class="font-bold text-gray-800">Mascota</h4>
                        </div>
                        @if($citaSeleccionada->mascota)
                            <div class="space-y-2">
                                <div>
                                    <p class="font-semibold text-gray-900 text-lg">{{ $citaSeleccionada->mascota->nombre_mascota }}</p>
                                </div>
                                <div class="text-sm text-gray-600">
                                    <span class="font-medium">Raza:</span> 
                                    {{ $citaSeleccionada->mascota->raza?->nombre_raza ?? 'No especificada' }}
                                </div>
                                <div class="text-sm text-gray-600">
                                    <span class="font-medium">Sexo:</span> 
                                    {{ $citaSeleccionada->mascota->sexo == 'M' ? 'Macho' : 'Hembra' }}
                                </div>
                                @if($citaSeleccionada->mascota->fecha_nacimiento)
                                <div class="text-sm text-gray-600">
                                    <span class="font-medium">Edad:</span> 
                                    {{ \Carbon\Carbon::parse($citaSeleccionada->mascota->fecha_nacimiento)->age }} años
                                </div>
                                @endif
                            </div>
                        @else
                            <p class="text-gray-500 italic">Mascota no disponible</p>
                        @endif
                    </div>

                    <!-- Veterinario -->
                    <div class="border border-gray-200 rounded-lg p-4 bg-white">
                        <div class="flex items-center mb-3">
                            <svg class="w-5 h-5 text-orange-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <h4 class="font-bold text-gray-800">Veterinario</h4>
                        </div>
                        @if($citaSeleccionada->trabajadorAsignado && $citaSeleccionada->trabajadorAsignado->persona)
                            <div class="space-y-2">
                                <div>
                                    <p class="font-semibold text-gray-900 text-lg">
                                        {{ $citaSeleccionada->trabajadorAsignado->persona->nombre }} 
                                        {{ $citaSeleccionada->trabajadorAsignado->persona->apellido_paterno }}
                                    </p>
                                </div>
                                <div class="text-sm text-gray-600">
                                    <span class="font-medium">Puesto:</span> 
                                    {{ $citaSeleccionada->trabajadorAsignado->puestoTrabajo?->nombre_puesto ?? 'No especificado' }}
                                </div>
                                @if($citaSeleccionada->trabajadorAsignado->persona->numero_telefono_personal)
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    {{ $citaSeleccionada->trabajadorAsignado->persona->numero_telefono_personal }}
                                </div>
                                @endif
                            </div>
                        @else
                            <p class="text-gray-500 italic">Veterinario no asignado</p>
                        @endif
                    </div>
                </div>

                <!-- Servicios y Detalles -->
                <div class="grid grid-cols-2 gap-6 mb-6">
                    <!-- Servicios -->
                    <div class="border border-gray-200 rounded-lg p-4 bg-white">
                        <h4 class="font-bold text-gray-800 mb-3 flex items-center">
                            <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Servicios Solicitados
                        </h4>
                        @if($citaSeleccionada->servicios && $citaSeleccionada->servicios->count() > 0)
                            <div class="space-y-2">
                                @foreach($citaSeleccionada->servicios as $servicio)
                                    <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                                        <span class="font-medium text-gray-700">{{ $servicio->nombre_servicio }}</span>
                                        <span class="text-sm text-gray-600">
                                            S/ {{ number_format($servicio->pivot->precio_aplicado, 2) }}
                                        </span>
                                    </div>
                                @endforeach
                                <div class="border-t pt-2 mt-2">
                                    <div class="flex justify-between items-center font-semibold">
                                        <span>Total:</span>
                                        <span class="text-green-600">
                                            S/ {{ number_format($citaSeleccionada->servicios->sum('pivot.precio_aplicado'), 2) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <p class="text-gray-500 italic">No hay servicios registrados</p>
                        @endif
                    </div>

                    <!-- Motivo y Observaciones -->
                    <div class="space-y-4">
                        <!-- Motivo -->
                        <div class="border border-gray-200 rounded-lg p-4 bg-white">
                            <h4 class="font-bold text-gray-800 mb-2 flex items-center">
                                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                </svg>
                                Motivo de la Cita
                            </h4>
                            <p class="text-gray-700 whitespace-pre-line">{{ $citaSeleccionada->motivo ?: 'No especificado' }}</p>
                        </div>

                        <!-- Observaciones -->
                        @if($citaSeleccionada->observaciones)
                        <div class="border border-gray-200 rounded-lg p-4 bg-white">
                            <h4 class="font-bold text-gray-800 mb-2 flex items-center">
                                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Observaciones
                            </h4>
                            <p class="text-gray-700 whitespace-pre-line">{{ $citaSeleccionada->observaciones }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Información de Registro -->
                <div class="border-t pt-4">
                    <div class="grid grid-cols-2 gap-4 text-sm text-gray-600">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <div>
                                <p class="font-semibold">Fecha de Registro</p>
                                <p>{{ \Carbon\Carbon::parse($citaSeleccionada->fecha_registro)->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <div>
                                <p class="font-semibold">Última Actualización</p>
                                <p>{{ \Carbon\Carbon::parse($citaSeleccionada->fecha_actualizacion)->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="flex justify-end gap-3 mt-6 pt-6 border-t">
                    @if($citaSeleccionada->estadoCita?->nombre_estado_cita == 'pendiente')
                        <button wire:click="confirmarCita"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Confirmar Cita
                        </button>
                        <button wire:click="cancelarCita"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Cancelar Cita
                        </button>
                    @elseif($citaSeleccionada->estadoCita?->nombre_estado_cita == 'confirmada')
                        <button wire:click="completarCita"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Marcar como Completada
                        </button>
                    @elseif($citaSeleccionada->estadoCita?->nombre_estado_cita == 'en progreso')
                        <button wire:click="completarCita"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Marcar como Completada
                        </button>
                    @endif
                    
                    <button wire:click="closeModalDetalle"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
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

            Livewire.on('citasUpdated', () => {
                // Recargar datos si es necesario
            });
        </script>
    @endpush
    <x-loader />
</x-panel>