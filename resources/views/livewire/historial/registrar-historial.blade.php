<x-panel title="Registro de Historial Clínico" :breadcrumbs="[
    ['label' => 'Inicio', 'href' => '/', 'icon' => 'home'],
    ['label' => 'Historial Clínico', 'href' => '#'],
    ['label' => 'Registro'],
]">
    <x-card>
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-2">📝 Registrar Historial Clínico</h2>
            <p class="text-gray-600">Complete el historial clínico para los servicios de las citas atendidas</p>
        </div>

        <!-- Filtros de búsqueda -->
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
            <h3 class="font-bold text-gray-700 mb-3">🔍 Buscar Citas para Registrar Historial</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Filtro por DNI -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">DNI del Cliente</label>
                    <input type="text" wire:model.live.debounce.500ms="filtroDni"
                        placeholder="Ingrese DNI..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Filtro por Mascota -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de Mascota</label>
                    <input type="text" wire:model.live.debounce.500ms="filtroMascota"
                        placeholder="Nombre de mascota..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Filtro por Fecha -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Cita</label>
                    <input type="date" wire:model.live.debounce.500ms="filtroFecha"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Filtro por Estado -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado de Cita</label>
                    <select wire:model.live.debounce.500ms="filtroEstado"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Todos los estados</option>
                        @foreach($estadosCita as $estado)
                            <option value="{{ $estado->id_estado_cita }}">{{ $estado->nombre_estado_cita }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Botones de acción filtros -->
            <div class="flex justify-end mt-4">
                <button wire:click="limpiarFiltros"
                    class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors font-medium">
                    🗑️ Limpiar Filtros
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Columna 1: Lista de Citas -->
            <div class="lg:col-span-1">
                <div class="bg-white border border-gray-200 rounded-lg p-4 h-full">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Citas Pendientes de Historial
                    </h3>

                    @if($citas->isEmpty())
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p>No hay citas pendientes de historial</p>
                        </div>
                    @else
                        <div class="space-y-3 max-h-[500px] overflow-y-auto">
                            @foreach($citas as $cita)
                                <div wire:click="seleccionarCita({{ $cita->id_cita }})"
                                    class="border border-gray-200 rounded-lg p-3 cursor-pointer hover:bg-blue-50 hover:border-blue-300 transition-all duration-200 
                                        {{ $citaSeleccionada && $citaSeleccionada->id_cita == $cita->id_cita ? 'bg-blue-100 border-blue-400' : '' }}">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <p class="font-semibold text-gray-800">
                                                #{{ $cita->id_cita }} - {{ $cita->mascota?->nombre_mascota ?? 'N/A' }}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                {{ $cita->cliente?->persona?->nombre ?? 'N/A' }}
                                                {{ $cita->cliente?->persona?->apellido_paterno ?? '' }}
                                            </p>
                                        </div>
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            {{ $cita->estadoCita?->nombre_estado_cita == 'En progreso' ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800' }}">
                                            {{ $cita->estadoCita?->nombre_estado_cita ?? 'N/A' }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        <p>📅 {{ \Carbon\Carbon::parse($cita->fecha_programada)->format('d/m/Y H:i') }}</p>
                                        <p>🛎️ {{ $cita->serviciosCita->count() }} servicio(s)</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Columna 2: Servicios de la Cita -->
            <div class="lg:col-span-2">
                @if($citaSeleccionada)
                    <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold text-gray-800 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Servicios de la Cita #{{ $citaSeleccionada->id_cita }}
                            </h3>
                            <button wire:click="limpiarSeleccion"
                                class="text-sm text-red-600 hover:text-red-800 font-medium">
                                Cambiar cita
                            </button>
                        </div>

                        <!-- Información de la cita -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 mb-4">
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div>
                                    <p class="font-semibold text-gray-600">Cliente:</p>
                                    <p class="text-gray-800">
                                        {{ $citaSeleccionada->cliente?->persona?->nombre ?? 'N/A' }}
                                        {{ $citaSeleccionada->cliente?->persona?->apellido_paterno ?? '' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-600">Mascota:</p>
                                    <p class="text-gray-800">
                                        {{ $citaSeleccionada->mascota?->nombre_mascota ?? 'N/A' }}
                                        ({{ $citaSeleccionada->mascota?->raza?->nombre_raza ?? 'Sin raza' }})
                                    </p>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-600">Fecha:</p>
                                    <p class="text-gray-800">
                                        {{ \Carbon\Carbon::parse($citaSeleccionada->fecha_programada)->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-600">Veterinario:</p>
                                    <p class="text-gray-800">
                                        {{ $citaSeleccionada->trabajadorAsignado?->persona?->nombre ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Lista de servicios -->
                        @if($serviciosCita->isEmpty())
                            <div class="text-center py-8 text-gray-500">
                                <p>No hay servicios en esta cita</p>
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($serviciosCita as $servicioCita)
                                    <div wire:click="seleccionarServicio({{ $servicioCita->id_cita_servicio }})"
                                        class="border rounded-lg p-3 cursor-pointer transition-all duration-200
                                            {{ $servicioSeleccionado && $servicioSeleccionado->id_cita_servicio == $servicioCita->id_cita_servicio 
                                                ? 'border-blue-400 bg-blue-50' 
                                                : 'border-gray-200 hover:bg-gray-50' }}">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="font-semibold text-gray-800">
                                                    {{ $servicioCita->servicio?->nombre_servicio ?? 'Servicio' }}
                                                </p>
                                                <p class="text-sm text-gray-600">
                                                    Precio: S/ {{ number_format($servicioCita->precio_aplicado, 2) }}
                                                </p>
                                            </div>
                                            <div>
                                                @if($servicioCita->diagnostico)
                                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                                        ✅ Completado
                                                    </span>
                                                @else
                                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                                        ⏳ Pendiente
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Formulario de Historial -->
                    @if($servicioSeleccionado)
                        <div class="bg-white border border-gray-200 rounded-lg p-4">
                            <h3 class="font-bold text-gray-800 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Historial Clínico - {{ $servicioSeleccionado->servicio?->nombre_servicio }}
                            </h3>

                            <form wire:submit.prevent="guardarHistorial" class="space-y-4">
                                <!-- Diagnóstico -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        🩺 Diagnóstico
                                    </label>
                                    <textarea wire:model="historial.diagnostico" rows="4"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                        placeholder="Describa el diagnóstico del paciente..."></textarea>
                                    @error('historial.diagnostico')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Medicamentos -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        💊 Medicamentos Recetados
                                    </label>
                                    <textarea wire:model="historial.medicamentos" rows="3"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                        placeholder="Lista de medicamentos, dosis y frecuencia..."></textarea>
                                    @error('historial.medicamentos')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Recomendaciones -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        📋 Recomendaciones
                                    </label>
                                    <textarea wire:model="historial.recomendaciones" rows="3"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                        placeholder="Recomendaciones para el cuidado del paciente..."></textarea>
                                    @error('historial.recomendaciones')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Notas Adicionales -->
                                <!-- <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        📝 Notas Adicionales
                                    </label>
                                    <textarea wire:model="historial.notas_adicionales" rows="2"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                        placeholder="Observaciones adicionales..."></textarea>
                                    @error('historial.notas_adicionales')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div> -->

                                <!-- Botones de acción -->
                                <div class="flex justify-end gap-3 pt-4 border-t">
                                    <button type="button" wire:click="resetearFormulario"
                                        class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors font-medium">
                                        Limpiar Formulario
                                    </button>
                                    <button type="submit"
                                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium">
                                        💾 Guardar Historial
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-8 text-center">
                            <svg class="w-12 h-12 text-yellow-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-yellow-700 font-medium">Seleccione un servicio para registrar el historial clínico</p>
                            <p class="text-yellow-600 text-sm mt-1">Haga clic en uno de los servicios listados arriba</p>
                        </div>
                    @endif
                @else
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-8 text-center">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-gray-600 font-medium">Seleccione una cita para comenzar</p>
                        <p class="text-gray-500 text-sm mt-1">Elija una cita de la lista para registrar el historial clínico</p>
                    </div>
                @endif
            </div>
        </div>
    </x-card>
</x-panel>