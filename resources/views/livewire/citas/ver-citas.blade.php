<div>
    <x-panel title="Visualización de Citas" :breadcrumbs="[
        ['label' => 'Inicio', 'href' => '/', 'icon' => 'home'],
        ['label' => 'Citas', 'href' => '#'],
        ['label' => 'Visualización'],
    ]">
        <x-card>
            <div class="mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800 mb-2">📋 Citas Programadas</h2>
                        <p class="text-gray-600">Consulte y gestione todas las citas registradas en el sistema</p>
                    </div>
                    <div class="flex gap-2">
                        <button wire:click="toggleFiltrosAvanzados"
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors font-medium flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                            </svg>
                            {{ $mostrarFiltrosAvanzados ? 'Ocultar' : 'Mostrar' }} Filtros Avanzados
                        </button>
                    </div>
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="grid grid-cols-5 gap-4 mb-6">
                @foreach($estadisticas as $estado => $data)
                    @if(isset($data['total']) && $data['total'] > 0)
                        <x-card>
                            <div class="h-[100px] flex flex-col justify-between">
                                <div class="flex justify-between items-center">
                                    <p class="font-medium">{{ $estado }}</p>
                                    <span class="text-lg">
                                        @switch($estado)
                                            @case('Pendiente')
                                                ⏰
                                                @break
                                            @case('En progreso')
                                                ▶️
                                                @break
                                            @case('Confirmada')
                                                ☑️
                                                @break
                                            @case('Completada')
                                                ✅
                                                @break
                                            @case('Cancelada')
                                                ❌
                                                @break
                                            @case('No asistio')
                                                👤❌
                                                @break
                                            @default
                                                ⭕
                                        @endswitch
                                    </span>
                                </div>
                                <div>
                                    <p class="font-medium text-3xl">{{ $data['total'] }}</p>
                                    <p class="text-sm opacity-75">{{ $data['porcentaje'] ?? 0 }}%</p>
                                </div>
                            </div>
                        </x-card>
                    @endif
                @endforeach
            </div>

            <!-- Filtros de búsqueda principales -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4">
                <h3 class="font-bold text-gray-700 mb-3">🔍 Búsqueda Principal</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    <!-- Filtro unificado por Cliente (DNI o Nombre) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cliente (DNI o Nombre)</label>
                        <input type="text" wire:model.live.debounce.500ms="filtroClienteDni"
                            placeholder="Buscar por DNI, nombre o apellido..."
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Filtro por Mascota -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de Mascota</label>
                        <input type="text" wire:model.live.debounce.500ms="filtroMascota"
                            placeholder="Nombre de mascota..."
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Filtro por Estado -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado de Cita</label>
                        <select wire:model.live.debounce.500ms="filtroEstado"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Todos los estados</option>
                            @foreach($this->estados as $estado)
                                <option value="{{ $estado->id_estado_cita }}">{{ $estado->nombre_estado_cita }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Botón Limpiar Filtros -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1 invisible">Acción</label>
                        <button wire:click="limpiarFiltros"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-500 hover:bg-gray-600 text-white transition-colors font-medium flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Limpiar Filtros
                        </button>
                    </div>
                </div>

                <!-- Filtros Avanzados -->
                @if($mostrarFiltrosAvanzados)
                <div class="border-t pt-4 mt-4">
                    <h4 class="font-semibold text-gray-700 mb-3">⚙️ Filtros Avanzados</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Filtro por Trabajador -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Trabajador</label>
                            <select wire:model.live.debounce.500ms="filtroTrabajador"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Todos los trabajadores</option>
                                @foreach($this->trabajadores as $trabajador)
                                    <option value="{{ $trabajador->id_trabajador }}">
                                        {{ $trabajador->persona->nombre }} {{ $trabajador->persona->apellido_paterno }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filtro por Servicio -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Servicio</label>
                            <input type="text" wire:model.live.debounce.500ms="filtroServicio"
                                placeholder="Nombre del servicio..."
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Filtro por Fecha Desde -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Desde</label>
                            <input type="date" wire:model.live.debounce.500ms="filtroFechaDesde"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Filtro por Fecha Hasta -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Hasta</label>
                            <input type="date" wire:model.live.debounce.500ms="filtroFechaHasta"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>
                @endif

                <!-- Resumen de filtros activos -->
                @php
                    $filtrosActivos = 0;
                    if($filtroClienteDni) $filtrosActivos++;
                    if($filtroMascota) $filtrosActivos++;
                    if($filtroEstado) $filtrosActivos++;
                    if($filtroTrabajador) $filtrosActivos++;
                    if($filtroServicio) $filtrosActivos++;
                    if($filtroFechaDesde != now()->subMonth()->format('Y-m-d')) $filtrosActivos++;
                    if($filtroFechaHasta != now()->format('Y-m-d')) $filtrosActivos++;
                @endphp
                
                @if($filtrosActivos > 0)
                <div class="mt-4 pt-3 border-t">
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        <span class="font-medium">{{ $filtrosActivos }}</span>
                        <span class="ml-1">filtro(s) activo(s)</span>
                    </div>
                </div>
                @endif
            </div>

            <!-- Tabla de Citas -->
            <div class="overflow-x-auto bg-white border border-gray-200 rounded-lg shadow-sm">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                wire:click="sortBy('fecha_programada')">
                                <div class="flex items-center">
                                    Fecha Programada
                                    @if($sortField === 'fecha_programada')
                                        @if($sortDirection === 'asc')
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        @endif
                                    @endif
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                wire:click="sortBy('cliente_nombre')">
                                <div class="flex items-center">
                                    Cliente
                                    @if($sortField === 'cliente_nombre')
                                        @if($sortDirection === 'asc')
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        @endif
                                    @endif
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Mascota
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Veterinario
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Servicios
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado
                            </th>
                            
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($citas as $cita)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ \Carbon\Carbon::parse($cita->fecha_programada)->format('d/m/Y') }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($cita->fecha_programada)->format('H:i') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $cita->cliente->persona->nombre }}
                                        {{ $cita->cliente->persona->apellido_paterno }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        DNI: {{ $cita->cliente->persona->numero_documento }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $cita->mascota->nombre_mascota }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $cita->trabajadorAsignado->persona->nombre ?? 'No asignado' }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        @if($cita->serviciosCita->count() > 0)
                                            {{ $cita->serviciosCita->first()->servicio->nombre_servicio }}
                                            @if($cita->serviciosCita->count() > 1)
                                                <span class="text-xs text-gray-500">+{{ $cita->serviciosCita->count() - 1 }} más</span>
                                            @endif
                                        @else
                                            <span class="text-gray-400">Sin servicios</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $color = $this->getColorEstado($cita->estadoCita->nombre_estado_cita);
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $color }}">
                                        {{ $cita->estadoCita->nombre_estado_cita }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex gap-2">
                                        <!-- Botón Ver (siempre visible) -->
                                        <button wire:click="verDetalle({{ $cita->id_cita }})"
                                            class="inline-flex items-center p-2 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition-colors"
                                            title="Ver detalles">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                            </svg>
                                        </button>

                                        @if($cita->estadoCita->nombre_estado_cita === 'En progreso')
                                            <!-- Botón Completar (solo para citas en progreso) -->
                                            <button wire:click="completarCita({{ $cita->id_cita }})"
                                                class="inline-flex items-center p-2 bg-green-100 text-green-700 rounded hover:bg-green-200 transition-colors"
                                                title="Completar cita">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                            d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="text-lg font-medium text-gray-600">No se encontraron citas</p>
                                    <p class="text-sm text-gray-500 mt-1">Intente ajustar los filtros de búsqueda</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($citas->hasPages())
                <div class="mt-4">
                    {{ $citas->links() }}
                </div>
            @endif


            <!-- Modal de Detalle -->
            @if($showModalDetalle && $citaSeleccionada)
                <div class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
                    <div class="bg-white rounded-xl shadow-xl w-3/4 max-w-4xl p-6 max-h-[90vh] overflow-y-auto">
                        <!-- Header -->
                        <div class="flex justify-between items-center mb-6 pb-4 border-b">
                            <div>
                                <p class="text-2xl font-bold text-gray-800">Detalles de Cita #{{ $citaSeleccionada->id_cita }}</p>
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ \Carbon\Carbon::parse($citaSeleccionada->fecha_programada)->translatedFormat('l, d \\d\\e F \\d\\e Y \\a \\l\\a\\s H:i') }}
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
                                    $estadoClass = $this->getColorEstado($citaSeleccionada->estadoCita->nombre_estado_cita);
                                @endphp
                                <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full {{ $estadoClass }}">
                                    {{ $citaSeleccionada->estadoCita->nombre_estado_cita }}
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
                                @php
                                    $duracionTotal = $citaSeleccionada->serviciosCita->sum('servicio.duracion_estimada') ?: 60;
                                    $horas = floor($duracionTotal / 60);
                                    $minutos = $duracionTotal % 60;
                                    $duracionFormateada = $horas > 0 ? "{$horas}h {$minutos}min" : "{$minutos} min";
                                @endphp
                                <p class="text-lg font-bold text-green-900">{{ $duracionFormateada }}</p>
                                <p class="text-sm text-green-700">
                                    {{ $citaSeleccionada->serviciosCita->count() }} servicio(s)
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
                                    </div>
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
                                    </div>
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
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Servicios y Motivo -->
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
                                @if($citaSeleccionada->serviciosCita && $citaSeleccionada->serviciosCita->count() > 0)
                                    <div class="space-y-2">
                                        @foreach($citaSeleccionada->serviciosCita as $servicioCita)
                                            <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                                                <span class="font-medium text-gray-700">{{ $servicioCita->servicio->nombre_servicio }}</span>
                                                <span class="text-sm text-gray-600">
                                                    S/ {{ number_format($servicioCita->precio_aplicado, 2) }}
                                                </span>
                                            </div>
                                        @endforeach
                                        <div class="border-t pt-2 mt-2">
                                            <div class="flex justify-between items-center font-semibold">
                                                <span>Total:</span>
                                                <span class="text-green-600">
                                                    S/ {{ number_format($citaSeleccionada->serviciosCita->sum('precio_aplicado'), 2) }}
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

                        <!-- Botones de Acción -->
                        <!-- Botones de Acción -->
<div class="flex justify-end gap-3 pt-6 border-t">
    @if($citaSeleccionada->estadoCita->nombre_estado_cita === 'En progreso')
        <button wire:click="completarCita({{ $citaSeleccionada->id_cita }})"
            class="inline-flex items-center gap-2 px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
            </svg>
            Completar Cita
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
        </x-card>
    </x-panel>
</div>