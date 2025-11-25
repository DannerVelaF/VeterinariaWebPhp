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

                    <!-- Información de la Cita -->
                    <div class="grid grid-cols-3 gap-4">
                        <!-- Fecha y Hora Programada -->
                        <div class="flex gap-2 flex-col">
                            <label for="fecha_programada" class="font-semibold">📅 Fecha y Hora Programada</label>
                            <input wire:model="cita.fecha_programada" type="datetime-local" id="fecha_programada"
                                class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200">
                            @error('cita.fecha_programada')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Trabajador Asignado -->
                        <div class="flex gap-2 flex-col">
                            <label for="trabajador" class="font-semibold">👨‍⚕️ Trabajador Asignado</label>
                            <select wire:model="trabajadorSeleccionado" id="trabajador"
                                class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200">
                                <option value="">Seleccione un trabajador</option>
                                @foreach ($trabajadores as $trabajador)
                                    <option value="{{ $trabajador->id_trabajador }}">
                                        <!-- En el trabajador seleccionado -->
                                        @if($trabajador->persona)
                                            {{ $trabajador->persona->nombre }} 
                                            {{ $trabajador->persona->apellido_paterno }}
                                            ({{ $trabajador->puestoTrabajo?->nombre_puesto ?? 'Sin puesto' }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('trabajadorSeleccionado')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Estado de la Cita -->
                        <div class="flex gap-2 flex-col">
                            <label for="estado" class="font-semibold">📊 Estado de la Cita</label>
                            <select wire:model="estadoCitaSeleccionado" id="estado"
                                class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200">
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

                    <!-- Motivo y Observaciones -->
                    <div class="grid grid-cols-1 gap-4">
                        <div class="flex gap-2 flex-col">
                            <label for="motivo" class="font-semibold">💬 Motivo de la Cita</label>
                            <textarea wire:model="cita.motivo" id="motivo" rows="3" maxlength="500"
                                class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200 resize-none"
                                placeholder="Describa el motivo de la cita..."></textarea>
                            @error('cita.motivo')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex gap-2 flex-col">
                            <label for="observaciones" class="font-semibold">📝 Observaciones Adicionales</label>
                            <textarea wire:model="cita.observaciones" id="observaciones" rows="2" maxlength="1000"
                                class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200 resize-none"
                                placeholder="Observaciones adicionales (opcional)..."></textarea>
                            @error('cita.observaciones')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="flex justify-end mt-6 gap-4 items-center pt-4 border-t">
                        <button type="button" wire:click="closeModal"
                            class="bg-gray-500 hover:bg-gray-700 text-white px-6 py-2 rounded-md transition">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md transition">
                            Programar Cita
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal de Detalle de Cita -->
    <div x-data x-init="$watch('$wire.showModalDetalle', value => {
        if (value) { document.body.classList.add('overflow-hidden') } else { document.body.classList.remove('overflow-hidden') }
    })">
        @if ($showModalDetalle && $citaSeleccionada)
            <div class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
                <div class="bg-white rounded-xl shadow-xl w-1/2 p-6 max-h-[90vh] overflow-y-auto">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <p class="text-xl font-medium">Detalles de la Cita</p>
                            <p class="text-sm font-medium">Información completa de la cita #{{ $citaSeleccionada->id_cita }}</p>
                        </div>
                        <button wire:click="closeModalDetalle"
                            class="text-gray-500 hover:text-gray-700">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 6 6 18"/>
                                <path d="m6 6 12 12"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Información Principal -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <p class="font-semibold text-blue-800 text-sm mb-1">📅 Fecha Programada</p>
                            <p class="text-lg font-bold text-blue-900">
                                {{ \Carbon\Carbon::parse($citaSeleccionada->fecha_programada)->format('d/m/Y H:i') }}
                            </p>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <p class="font-semibold text-purple-800 text-sm mb-1">📊 Estado</p>
                            <p class="text-lg font-bold text-purple-900 capitalize">
                                {{ $citaSeleccionada->estadoCita?->nombre_estado_cita ?? 'N/A' }}
                            </p>
                        </div>
                    </div>

                    <!-- Información de Participantes -->
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <!-- Cliente -->
                        <div class="border rounded-lg p-4">
                            <h4 class="font-bold text-gray-700 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Cliente
                            </h4>
                            @if($citaSeleccionada->cliente && $citaSeleccionada->cliente->persona)
                                <p class="font-semibold text-gray-800">
                                    {{ $citaSeleccionada->cliente->persona->nombre }} 
                                    {{ $citaSeleccionada->cliente->persona->apellido_paterno }}
                                </p>
                                <p class="text-sm text-gray-600 mt-1">
                                    DNI: {{ $citaSeleccionada->cliente->persona->numero_documento }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    Tel: {{ $citaSeleccionada->cliente->persona->numero_telefono_personal ?: 'No registrado' }}
                                </p>
                            @else
                                <p class="text-gray-500">Cliente no disponible</p>
                            @endif
                        </div>

                        <!-- Mascota -->
                        <div class="border rounded-lg p-4">
                            <h4 class="font-bold text-gray-700 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                </svg>
                                Mascota
                            </h4>
                            @if($citaSeleccionada->mascota)
                                <p class="font-semibold text-gray-800">{{ $citaSeleccionada->mascota->nombre_mascota }}</p>
                                <p class="text-sm text-gray-600 mt-1">
                                    Raza: {{ $citaSeleccionada->mascota->raza?->nombre_raza ?? 'No especificada' }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    Sexo: {{ $citaSeleccionada->mascota->sexo == 'M' ? 'Macho' : 'Hembra' }}
                                </p>
                            @else
                                <p class="text-gray-500">Mascota no disponible</p>
                            @endif
                        </div>

                        <!-- Trabajador -->
                        <div class="border rounded-lg p-4">
                            <h4 class="font-bold text-gray-700 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Veterinario
                            </h4>
                            @if($citaSeleccionada->trabajadorAsignado && $citaSeleccionada->trabajadorAsignado->persona)
                                <p class="font-semibold text-gray-800">
                                    {{ $citaSeleccionada->trabajadorAsignado->persona->nombre }} 
                                    {{ $citaSeleccionada->trabajadorAsignado->persona->apellido_paterno }}
                                </p>
                                <p class="text-sm text-gray-600 mt-1">
                                    Puesto: {{ $citaSeleccionada->trabajadorAsignado->puestoTrabajo?->nombre_puesto ?? 'No especificado' }}
                                </p>
                            @else
                                <p class="text-gray-500">Veterinario no asignado</p>
                            @endif
                        </div>
                    </div>

                    <!-- Motivo y Observaciones -->
                    <div class="space-y-4">
                        <div class="border rounded-lg p-4">
                            <h4 class="font-bold text-gray-700 mb-2">💬 Motivo de la Cita</h4>
                            <p class="text-gray-800">{{ $citaSeleccionada->motivo ?: 'No especificado' }}</p>
                        </div>

                        @if($citaSeleccionada->observaciones)
                            <div class="border rounded-lg p-4">
                                <h4 class="font-bold text-gray-700 mb-2">📝 Observaciones</h4>
                                <p class="text-gray-800">{{ $citaSeleccionada->observaciones }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Fechas de Registro -->
                    <div class="grid grid-cols-2 gap-4 mt-6 text-sm text-gray-600">
                        <div>
                            <p class="font-semibold">Fecha de Registro:</p>
                            <p>{{ \Carbon\Carbon::parse($citaSeleccionada->fecha_registro)->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="font-semibold">Última Actualización:</p>
                            <p>{{ \Carbon\Carbon::parse($citaSeleccionada->fecha_actualizacion)->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="flex justify-end gap-2 mt-6 pt-4 border-t">
                        @if($citaSeleccionada->estadoCita?->nombre_estado_cita == 'pendiente')
                            <button wire:click="confirmarCita"
                                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md text-sm">
                                Confirmar Cita
                            </button>
                            <button wire:click="cancelarCita"
                                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md text-sm">
                                Cancelar Cita
                            </button>
                        @elseif($citaSeleccionada->estadoCita?->nombre_estado_cita == 'confirmada')
                            <button wire:click="completarCita"
                                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm">
                                Marcar como Completada
                            </button>
                        @endif
                        <button wire:click="closeModalDetalle"
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

            Livewire.on('citasUpdated', () => {
                // Recargar datos si es necesario
            });
        </script>
    @endpush
    <x-loader />
</x-panel>