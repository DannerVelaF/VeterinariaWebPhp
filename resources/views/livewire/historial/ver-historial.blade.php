<div>
    <x-panel title="Visualización de Historial Clínico" :breadcrumbs="[
        ['label' => 'Inicio', 'href' => '/', 'icon' => 'home'],
        ['label' => 'Historial Clínico', 'href' => '#'],
        ['label' => 'Visualización'],
    ]">
        <x-card>
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-2">📋 Historial Clínico Registrado</h2>
                <p class="text-gray-600">Consulte y visualice todos los historiales clínicos registrados en el sistema</p>
            </div>

            <!-- Filtros de búsqueda -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                <h3 class="font-bold text-gray-700 mb-3">🔍 Filtros de Búsqueda</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                    <!-- Filtro por DNI -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">DNI del Cliente</label>
                        <input type="text" wire:model.live.debounce.500ms="filtroDni"
                            placeholder="Ingrese DNI..."
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Filtro por Nombre del Cliente -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Cliente</label>
                        <input type="text" wire:model.live.debounce.500ms="filtroCliente"
                            placeholder="Nombre o apellido..."
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Filtro por Mascota -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de Mascota</label>
                        <input type="text" wire:model.live.debounce.500ms="filtroMascota"
                            placeholder="Nombre de mascota..."
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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
                </div>

                <!-- Botones de acción filtros -->
                <div class="flex justify-end gap-2">
                    <button wire:click="limpiarFiltros"
                        class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors font-medium flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Limpiar Filtros
                    </button>
                </div>
            </div>

            <!-- Tabla de Historial -->
            <div class="overflow-x-auto bg-white border border-gray-200 rounded-lg shadow-sm">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                wire:click="sortBy('fecha_registro')">
                                <div class="flex items-center">
                                    Fecha Registro
                                    @if($sortField === 'fecha_registro')
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
                                wire:click="sortBy('fecha_cita')">
                                <div class="flex items-center">
                                    Fecha Cita
                                    @if($sortField === 'fecha_cita')
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
                                Servicio
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Diagnóstico
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($historiales as $historial)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($historial->fecha_registro)->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($historial->cita->fecha_programada)->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $historial->cita->cliente->persona->nombre }}
                                        {{ $historial->cita->cliente->persona->apellido_paterno }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        DNI: {{ $historial->cita->cliente->persona->numero_documento }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $historial->cita->mascota->nombre_mascota }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $historial->cita->mascota->raza?->nombre_raza ?? 'Sin raza' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $historial->servicio->nombre_servicio }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 max-w-xs truncate">
                                        {{ Str::limit($historial->diagnostico, 60) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex gap-2">
                                        <button wire:click="verDetalle({{ $historial->id_cita_servicio }})"
                                            class="inline-flex items-center gap-1 px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded text-sm transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                            </svg>
                                            Ver
                                        </button>
                                        
                                        @if($historial->medicamentos)
                                        <button wire:click="generarReceta({{ $historial->id_cita_servicio }})"
                                            class="inline-flex items-center gap-1 px-3 py-1 bg-green-500 hover:bg-green-600 text-white rounded text-sm transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                                                <polyline points="14 2 14 8 20 8"/>
                                                <line x1="16" x2="8" y1="13" y2="13"/>
                                                <line x1="16" x2="8" y1="17" y2="17"/>
                                                <line x1="10" x2="8" y1="9" y2="9"/>
                                            </svg>
                                            Receta
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
                                    <p class="text-lg font-medium text-gray-600">No se encontraron historiales clínicos</p>
                                    <p class="text-sm text-gray-500 mt-1">Intente ajustar los filtros de búsqueda</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($historiales->hasPages())
                <div class="mt-4">
                    {{ $historiales->links() }}
                </div>
            @endif

            <!-- Modal de Detalle -->
            @if($showModalDetalle && $historialSeleccionado)
                <!-- El contenido del modal se mantiene igual que en la versión anterior -->
                <div class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
                    <div class="bg-white rounded-xl shadow-xl w-3/4 max-w-4xl p-6 max-h-[90vh] overflow-y-auto">
                        <!-- Header -->
                        <div class="flex justify-between items-center mb-6 pb-4 border-b">
                            <div>
                                <p class="text-2xl font-bold text-gray-800">Detalles del Historial Clínico</p>
                                <p class="text-sm text-gray-600 mt-1">
                                    Historial #{{ $historialSeleccionado->id_cita_servicio }}
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

                        <!-- Contenido del modal -->
                        <!-- ... (mantener el contenido del modal que ya tenías) ... -->
                        <!-- Puedes copiar el contenido del modal de la versión anterior aquí -->

                        <div class="mb-6">
                            <!-- Información del Cliente -->
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                <h4 class="font-bold text-blue-800 mb-2">👤 Información del Cliente</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-600">Nombre Completo</p>
                                        <p class="text-gray-800">
                                            {{ $historialSeleccionado->cita->cliente->persona->nombre }}
                                            {{ $historialSeleccionado->cita->cliente->persona->apellido_paterno }}
                                            {{ $historialSeleccionado->cita->cliente->persona->apellido_materno }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-600">DNI</p>
                                        <p class="text-gray-800">{{ $historialSeleccionado->cita->cliente->persona->numero_documento }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Información de la Mascota -->
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                                <h4 class="font-bold text-green-800 mb-2">🐾 Información de la Mascota</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-600">Nombre</p>
                                        <p class="text-gray-800">{{ $historialSeleccionado->cita->mascota->nombre_mascota }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-600">Raza</p>
                                        <p class="text-gray-800">{{ $historialSeleccionado->cita->mascota->raza?->nombre_raza ?? 'No especificada' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Diagnóstico -->
                            <div class="mb-4">
                                <h4 class="font-bold text-gray-800 mb-2">🩺 Diagnóstico</h4>
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                    <p class="text-gray-700 whitespace-pre-line">{{ $historialSeleccionado->diagnostico }}</p>
                                </div>
                            </div>

                            <!-- Medicamentos si existen -->
                            @if($historialSeleccionado->medicamentos)
                            <div class="mb-4">
                                <h4 class="font-bold text-gray-800 mb-2">💊 Medicamentos Recetados</h4>
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                    <p class="text-gray-700 whitespace-pre-line">{{ $historialSeleccionado->medicamentos }}</p>
                                </div>
                            </div>
                            @endif

                            <!-- Recomendaciones -->
                            <div class="mb-4">
                                <h4 class="font-bold text-gray-800 mb-2">📋 Recomendaciones</h4>
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                    <p class="text-gray-700 whitespace-pre-line">{{ $historialSeleccionado->recomendaciones }}</p>
                                </div>
                            </div>

                            <!-- Notas adicionales si existen -->
                            @if($historialSeleccionado->notas_adicionales)
                            <div class="mb-4">
                                <h4 class="font-bold text-gray-800 mb-2">📝 Notas Adicionales</h4>
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                    <p class="text-gray-700 whitespace-pre-line">{{ $historialSeleccionado->notas_adicionales }}</p>
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Botones de acción -->
                        <div class="flex justify-end gap-3 pt-4 border-t">
                            <button onclick="window.print()"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                </svg>
                                Imprimir
                            </button>
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