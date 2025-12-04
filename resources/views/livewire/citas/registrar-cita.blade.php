<x-panel title="Registro de Citas" :breadcrumbs="[
    ['label' => 'Inicio', 'href' => '/', 'icon' => 'home'],
    ['label' => 'Citas', 'href' => route('citas.ver')],
    ['label' => 'Registro'],
]">
    <div class="grid grid-cols-5 gap-4 mb-4">
        
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
                                            <button type="button" 
                                                    wire:click="redirigirAMascotas"
                                                    class="inline-flex items-center gap-2 px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus">
                                                    <path d="M12 5v14"/>
                                                    <path d="M5 12h14"/>
                                                </svg>
                                                Registrar Mascota
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

        <!-- Horarios del Trabajador - MEJORADO -->
        @if($trabajadorSeleccionado && $this->infoTurnosTrabajador)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="font-semibold text-blue-800 mb-3 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Horario Semanal del Trabajador
                </h4>
                
                <!-- Información del trabajador -->
                <div class="mb-3 p-2 bg-white rounded border">
                    <p class="font-semibold text-gray-800">{{ $this->infoTurnosTrabajador['nombre_trabajador'] }}</p>
                    <p class="text-sm text-gray-600">{{ $this->infoTurnosTrabajador['puesto'] }}</p>
                </div>

                <!-- Grid de días de la semana -->
                <div class="grid grid-cols-7 gap-2 text-xs">
                    @foreach ($this->infoTurnosTrabajador['dias_semana'] as $diaKey => $diaInfo)
                        @php
                            $bgColor = $diaInfo['descanso'] ? 'bg-red-100' : 'bg-green-100';
                            $textColor = $diaInfo['descanso'] ? 'text-red-800' : 'text-green-800';
                            $borderColor = $diaInfo['descanso'] ? 'border-red-300' : 'border-green-300';
                        @endphp
                        
                        <div class="text-center p-2 rounded border {{ $bgColor }} {{ $textColor }} {{ $borderColor }}">
                            <div class="font-semibold">{{ substr($diaInfo['nombre'], 0, 3) }}</div>
                            
                            @if($diaInfo['descanso'])
                                <div class="mt-1 text-red-600 font-bold">✗</div>
                                <div class="text-[10px] mt-1">Descanso</div>
                            @elseif(count($diaInfo['horarios']) > 0)
                                @foreach($diaInfo['horarios'] as $horario)
                                    <div class="mt-1 text-[10px] font-medium">
                                        {{ $horario['inicio'] }}-{{ $horario['fin'] }}
                                    </div>
                                @endforeach
                            @else
                                <div class="mt-1 text-gray-500 text-[10px]">No asignado</div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Leyenda -->
                <div class="flex justify-center gap-4 mt-3 text-xs">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-100 border border-green-300 rounded mr-1"></div>
                        <span class="text-gray-600">Días laborales</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-red-100 border border-red-300 rounded mr-1"></div>
                        <span class="text-gray-600">Días de descanso</span>
                    </div>
                </div>
            </div>
        @elseif($trabajadorSeleccionado)
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                <svg class="w-8 h-8 text-yellow-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <p class="text-yellow-700 font-medium">No se encontró información de horarios</p>
                <p class="text-yellow-600 text-sm mt-1">El trabajador no tiene turnos asignados</p>
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
                            <textarea wire:model="cita.motivo" id="motivo" rows="3"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                placeholder="Describa el motivo de la cita..."></textarea>
                            @error('cita.motivo')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Observaciones y Estado -->
                        <div class="space-y-3">
                            <label for="observaciones" class="font-semibold text-gray-700">📋 Observaciones</label>
                            <textarea wire:model="cita.observaciones" id="observaciones" rows="3"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                placeholder="Observaciones adicionales (opcional)..."></textarea>
                            @error('cita.observaciones')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
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