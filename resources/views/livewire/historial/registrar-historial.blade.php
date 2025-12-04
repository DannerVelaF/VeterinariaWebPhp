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

        <!-- Mensajes de estado -->
        @if($mensaje)
            <div class="mb-4 p-4 rounded-lg {{ $tipoMensaje == 'error' ? 'bg-red-50 border border-red-200 text-red-800' : 'bg-green-50 border border-green-200 text-green-800' }}">
                <div class="flex items-center">
                    @if($tipoMensaje == 'error')
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    @else
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    @endif
                    <span>{{ $mensaje }}</span>
                </div>
            </div>
        @endif

        <!-- Filtros de búsqueda -->
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
            <h3 class="font-bold text-gray-700 mb-3">🔍 Buscar Citas para Registrar Historial</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Filtro por Cliente (nombre completo o DNI) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cliente (Nombre o DNI)</label>
                    <input type="text" wire:model.live.debounce.500ms="filtroCliente"
                        placeholder="Nombre completo o DNI..."
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

                <!-- Filtro por Estado (solo dos opciones) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado de Cita</label>
                    <select wire:model.live.debounce.500ms="filtroEstado"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Todos</option>
                        <option value="En progreso">En progreso</option>
                        <option value="Completada">Completada</option>
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
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Citas Disponibles
                        </h3>
                        <span class="text-sm bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                            {{ $citas->count() }}
                        </span>
                    </div>

                    @if($citas->isEmpty())
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p>No hay citas disponibles</p>
                            <p class="text-sm mt-1">Ajuste los filtros o verifique que existan citas</p>
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
                                            <p class="text-xs text-gray-500 mt-1">
                                                DNI: {{ $cita->cliente?->persona?->numero_documento ?? 'N/A' }}
                                            </p>
                                        </div>
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            {{ $cita->estadoCita?->nombre_estado_cita == 'En progreso' ? 'bg-orange-100 text-orange-800' : 
                                               ($cita->estadoCita?->nombre_estado_cita == 'Completada' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800') }}">
                                            {{ $cita->estadoCita?->nombre_estado_cita ?? 'N/A' }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        <p>📅 {{ \Carbon\Carbon::parse($cita->fecha_programada)->format('d/m/Y H:i') }}</p>
                                        <p>🛎️ {{ $cita->serviciosCita->count() }} servicio(s)</p>
                                        <!-- Indicador de progreso de historial -->
                                        @php
                                            $totalServicios = $cita->serviciosCita->count();
                                            $serviciosConHistorial = $cita->serviciosCita->whereNotNull('diagnostico')->count();
                                            $progreso = $totalServicios > 0 ? ($serviciosConHistorial / $totalServicios) * 100 : 0;
                                        @endphp
                                        <div class="mt-1 flex items-center gap-1">
                                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                                <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $progreso }}%"></div>
                                            </div>
                                            <span class="text-xs">{{ number_format($progreso, 0) }}%</span>
                                        </div>
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
                            <div>
                                <h3 class="font-bold text-gray-800 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Cita #{{ $citaSeleccionada->id_cita }}
                                </h3>
                                
                                <!-- Barra de progreso -->
                                @if($serviciosCita->count() > 0)
                                    <div class="mt-2 flex items-center gap-2">
                                        <div class="text-sm text-gray-600">
                                            <span class="font-medium">Progreso del historial:</span>
                                            <span class="ml-1 font-bold {{ $this->progresoHistorial == 100 ? 'text-green-600' : 'text-blue-600' }}">
                                                {{ number_format($this->progresoHistorial, 0) }}%
                                            </span>
                                            <span class="ml-2 text-xs text-gray-500">
                                                ({{ $serviciosCita->whereNotNull('diagnostico')->count() }}/{{ $serviciosCita->count() }} servicios)
                                            </span>
                                        </div>
                                        <div class="w-32 bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full" 
                                                 style="width: {{ $this->progresoHistorial }}%"></div>
                                        </div>
                                        @if($this->todosConHistorial)
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                                ✅ Historial Completo
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            <button wire:click="limpiarSeleccion"
                                class="text-sm text-red-600 hover:text-red-800 font-medium flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Cambiar cita
                            </button>
                        </div>

                        <!-- Información de la cita -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div class="bg-blue-50 border border-blue-100 rounded-lg p-3">
                                <h4 class="font-semibold text-blue-800 text-sm mb-2">👤 Información del Cliente</h4>
                                <div class="space-y-1 text-sm">
                                    <p class="text-gray-700">
                                        <span class="font-medium">Nombre:</span>
                                        {{ $citaSeleccionada->cliente?->persona?->nombre ?? 'N/A' }}
                                        {{ $citaSeleccionada->cliente?->persona?->apellido_paterno ?? '' }}
                                        {{ $citaSeleccionada->cliente?->persona?->apellido_materno ?? '' }}
                                    </p>
                                    <p class="text-gray-700">
                                        <span class="font-medium">DNI:</span>
                                        {{ $citaSeleccionada->cliente?->persona?->numero_documento ?? 'N/A' }}
                                    </p>
                                    @if($citaSeleccionada->cliente?->persona?->numero_telefono_personal)
                                    <p class="text-gray-700">
                                        <span class="font-medium">Teléfono:</span>
                                        {{ $citaSeleccionada->cliente?->persona?->numero_telefono_personal }}
                                    </p>
                                    @endif
                                </div>
                            </div>

                            <div class="bg-green-50 border border-green-100 rounded-lg p-3">
                                <h4 class="font-semibold text-green-800 text-sm mb-2">🐾 Información de la Mascota</h4>
                                <div class="space-y-1 text-sm">
                                    <p class="text-gray-700">
                                        <span class="font-medium">Nombre:</span>
                                        {{ $citaSeleccionada->mascota?->nombre_mascota ?? 'N/A' }}
                                    </p>
                                    <p class="text-gray-700">
                                        <span class="font-medium">Raza:</span>
                                        {{ $citaSeleccionada->mascota?->raza?->nombre_raza ?? 'Sin raza' }}
                                    </p>
                                    <p class="text-gray-700">
                                        <span class="font-medium">Sexo:</span>
                                        {{ $citaSeleccionada->mascota?->sexo == 'M' ? 'Macho' : 'Hembra' }}
                                    </p>
                                    @if($citaSeleccionada->mascota?->fecha_nacimiento)
                                    <p class="text-gray-700">
                                        <span class="font-medium">Edad:</span>
                                        {{ \Carbon\Carbon::parse($citaSeleccionada->mascota->fecha_nacimiento)->age }} años
                                    </p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Lista de servicios -->
                        <div>
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="font-semibold text-gray-700 flex items-center">
                                    🛎️ Servicios de la Cita
                                    <span class="ml-2 text-sm text-gray-500">
                                        ({{ $serviciosCita->count() }} servicios)
                                    </span>
                                </h4>
                                <div class="text-sm text-gray-500">
                                    <span class="text-green-600">● Completado</span>
                                    <span class="ml-3 text-yellow-600">● Pendiente</span>
                                </div>
                            </div>
                            @if($serviciosCita->isEmpty())
                                <div class="text-center py-8 text-gray-500 border border-gray-200 rounded-lg">
                                    <p>No hay servicios en esta cita</p>
                                </div>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    @foreach($serviciosCita as $servicioCita)
                                        <div wire:click="seleccionarServicio({{ $servicioCita->id_cita_servicio }})"
                                            class="border rounded-lg p-3 cursor-pointer transition-all duration-200 hover:shadow-md
                                                {{ $servicioSeleccionado && $servicioSeleccionado->id_cita_servicio == $servicioCita->id_cita_servicio 
                                                    ? 'border-blue-400 bg-blue-50 shadow-sm ring-2 ring-blue-200' 
                                                    : 'border-gray-200 hover:bg-gray-50' }}">
                                            <div class="flex justify-between items-start">
                                                <div class="flex-1">
                                                    <p class="font-semibold text-gray-800 flex items-center">
                                                        {{ $servicioCita->servicio?->nombre_servicio ?? 'Servicio' }}
                                                        @if($servicioCita->diagnostico)
                                                            <span class="ml-2 text-green-600">●</span>
                                                        @else
                                                            <span class="ml-2 text-yellow-600">●</span>
                                                        @endif
                                                    </p>
                                                    <p class="text-sm text-gray-600 mt-1">
                                                        💰 S/ {{ number_format($servicioCita->precio_aplicado, 2) }}
                                                    </p>
                                                    @if($servicioCita->diagnostico)
                                                        <div class="mt-2 text-xs text-gray-500">
                                                            <p class="font-medium">Última actualización:</p>
                                                            <p>{{ \Carbon\Carbon::parse($servicioCita->fecha_actualizacion)->format('d/m/Y H:i') }}</p>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    @if($servicioCita->diagnostico)
                                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 flex items-center gap-1">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                            </svg>
                                                            Completado
                                                        </span>
                                                    @else
                                                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 flex items-center gap-1">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                            </svg>
                                                            Pendiente
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            @if($servicioCita->diagnostico)
                                                <div class="mt-2 pt-2 border-t border-gray-100">
                                                    <p class="text-xs text-gray-600 line-clamp-2">
                                                        <span class="font-medium">Diagnóstico:</span>
                                                        {{ Str::limit($servicioCita->diagnostico, 80) }}
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Formulario de Historial -->
                    @if($servicioSeleccionado)
                        <div class="bg-white border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="font-bold text-gray-800 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Historial Clínico - {{ $servicioSeleccionado->servicio?->nombre_servicio }}
                                </h3>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm px-3 py-1 rounded-full 
                                        {{ $servicioSeleccionado->diagnostico ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $servicioSeleccionado->diagnostico ? 'Editando historial' : 'Nuevo historial' }}
                                    </span>
                                    @if($servicioSeleccionado->fecha_actualizacion)
                                        <span class="text-xs text-gray-500">
                                            Editado: {{ \Carbon\Carbon::parse($servicioSeleccionado->fecha_actualizacion)->format('d/m/Y H:i') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <form wire:submit.prevent="guardarHistorial" class="space-y-4">
                                <!-- Diagnóstico -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        🩺 Diagnóstico (requerido)
                                    </label>
                                    <textarea wire:model="historial.diagnostico" rows="4"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                        placeholder="Describa detalladamente el diagnóstico del paciente..."></textarea>
                                    @error('historial.diagnostico')
                                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Medicamentos -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                        </svg>
                                        💊 Medicamentos Recetados (opcional)
                                    </label>
                                    <textarea wire:model="historial.medicamentos" rows="4"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                        placeholder="Ejemplo: 
- Amoxicilina: 250mg cada 12h por 7 días
- Antiinflamatorio: 1 comprimido al día por 5 días
- Vitamina C: 500mg diarios..."></textarea>
                                    @error('historial.medicamentos')
                                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Recomendaciones -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        📋 Recomendaciones (requerido)
                                    </label>
                                    <textarea wire:model="historial.recomendaciones" rows="4"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                        placeholder="Ejemplo: 
- Reposo absoluto por 48 horas
- Dieta blanda durante 3 días
- Controlar la temperatura 2 veces al día
- Regresar en una semana para control..."></textarea>
                                    @error('historial.recomendaciones')
                                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Botones de acción -->
                                <div class="flex justify-between items-center pt-4 border-t">
                                    <div class="text-sm text-gray-500">
                                        @if($servicioSeleccionado->fecha_actualizacion)
                                            <p>Última actualización: 
                                                {{ \Carbon\Carbon::parse($servicioSeleccionado->fecha_actualizacion)->format('d/m/Y H:i') }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="flex gap-3">
                                        <button type="button" wire:click="resetearFormulario"
                                            class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors font-medium">
                                            Limpiar Formulario
                                        </button>
                                        <button type="submit"
                                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium shadow-sm hover:shadow-md flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                    d="M5 13l4 4L19 7"/>
                                            </svg>
                                            💾 {{ $servicioSeleccionado->diagnostico ? 'Actualizar' : 'Guardar' }} Historial
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-8 text-center">
                            <svg class="w-12 h-12 text-yellow-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <h3 class="text-yellow-700 font-medium text-lg mb-2">Seleccione un servicio</h3>
                            <p class="text-yellow-600">Haga clic en uno de los servicios listados arriba para registrar o editar el historial clínico.</p>
                            <p class="text-yellow-600 text-sm mt-1">Los servicios marcados en verde ya tienen historial registrado y pueden ser editados.</p>
                        </div>
                    @endif
                @else
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-8 text-center">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <h3 class="text-gray-600 font-medium text-lg mb-2">Seleccione una cita</h3>
                        <p class="text-gray-500">Elija una cita de la lista para registrar el historial clínico.</p>
                        <p class="text-gray-500 text-sm mt-1">Use los filtros de búsqueda para encontrar las citas.</p>
                    </div>
                @endif
            </div>
        </div>
    </x-card>
</x-panel>

@push('scripts')
<script>
    // Auto-ocultar mensajes después de 5 segundos
    Livewire.on('mensaje-temporal', () => {
        setTimeout(() => {
            const mensajeDiv = document.querySelector('[x-data] .bg-red-50, [x-data] .bg-green-50');
            if (mensajeDiv) {
                mensajeDiv.style.transition = 'opacity 0.5s';
                mensajeDiv.style.opacity = '0';
                setTimeout(() => mensajeDiv.remove(), 500);
            }
        }, 5000);
    });
</script>
@endpush