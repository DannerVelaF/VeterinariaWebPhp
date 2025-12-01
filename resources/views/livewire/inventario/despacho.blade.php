<div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-4">

    <x-panel title="Gestión de Logística y Rutas" :breadcrumbs="[
        ['label' => 'Inicio', 'href' => '/', 'icon' => 'home'],
        ['label' => 'Inventario', 'href' => '#'],
        ['label' => 'Despacho Web'],
    ]">

        {{-- SECCIÓN DE FILTROS Y ACCIONES (Se mantiene igual) --}}
        <div
            class="mb-6 bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                {{-- Buscador General --}}
                <div class="md:col-span-3">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Buscar Cliente /
                        DNI</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input wire:model.live.debounce.300ms="search" type="text"
                               class="block w-full pl-10 text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                               placeholder="Nombre, DNI o Calle...">
                    </div>
                </div>

                {{-- Filtros Ubigeo --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Departamento</label>
                    <select wire:model.live="departamentoSeleccionado"
                            class="block w-full text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Todos</option>
                        @foreach($departamentos as $dep)
                            <option value="{{ $dep }}">{{ $dep }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Provincia</label>
                    <select wire:model.live="provinciaSeleccionada"
                            class="block w-full text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" {{ empty($departamentoSeleccionado) ? 'disabled' : '' }}>
                        <option value="">Todas</option>
                        @foreach($provincias as $prov)
                            <option value="{{ $prov }}">{{ $prov }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Distrito</label>
                    <select wire:model.live="distritoSeleccionado"
                            class="block w-full text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" {{ empty($provinciaSeleccionada) ? 'disabled' : '' }}>
                        <option value="">Todos</option>
                        @foreach($distritos as $dist)
                            <option value="{{ $dist }}">{{ $dist }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Botón de Acción Masiva --}}
                <div class="md:col-span-3 flex justify-end">
                    @if(count(array_filter($selectedOrders)) > 0)
                        <button wire:click="abrirModalMasivo"
                                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded shadow flex items-center justify-center transition-all duration-300 transform hover:scale-105">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Asignar Ruta ({{ count(array_filter($selectedOrders)) }})
                        </button>
                    @else
                        <div class="text-sm text-gray-400 italic self-center text-right w-full">
                            Selecciona pedidos para asignar
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- TABLA NATIVA --}}
        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden border border-gray-200 dark:border-gray-700">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left"><input type="checkbox" wire:model.live="selectAll"
                                                                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            ID
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Cliente
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Dirección / Ubigeo
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Estado
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Transportista
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Fecha Prog.
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($pedidos as $pedido)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap"><input type="checkbox"
                                                                           value="{{ $pedido->id_envio_pedido }}"
                                                                           wire:model.live="selectedOrders"
                                                                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-bold">
                                #{{ $pedido->id_envio_pedido }}</td>
                            <td class="px-6 py-4">
                                <div
                                    class="text-sm font-medium text-gray-900 dark:text-white">{{ $pedido->venta->cliente->persona->nombre ?? 'N/A' }} {{ $pedido->venta->cliente->persona->apellido_paterno ?? '' }}</div>
                                <div class="text-xs text-gray-500">
                                    DNI: {{ $pedido->venta->cliente->persona->numero_documento ?? '--' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-white truncate max-w-xs"
                                     title="{{ $pedido->direccion->nombre_calle ?? '' }} #{{ $pedido->direccion->numero ?? '' }}">{{ $pedido->direccion->nombre_calle ?? '' }}
                                    #{{ $pedido->direccion->numero ?? '' }}</div>
                                <div class="text-xs text-gray-500 mt-1"><span
                                        class="bg-gray-100 dark:bg-gray-600 px-1 rounded">{{ $pedido->direccion->ubigeo->departamento ?? '-' }} / {{ $pedido->direccion->ubigeo->distrito ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $estado = strtolower($pedido->estadoEnvio->nombre_estado_envio_pedido ?? 'pendiente');
                                    $class = match($estado) {
                                        'entregado' => 'bg-green-100 text-green-800',
                                        'pendiente' => 'bg-yellow-100 text-yellow-800',
                                        'en_ruta' => 'bg-blue-100 text-blue-800',
                                        'fallido' => 'bg-red-100 text-red-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    };
                                @endphp
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $class }}">{{ ucfirst($estado) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                @if($pedido->trabajador)
                                    {{ $pedido->trabajador->persona->nombre }}
                                @else
                                    <span class="text-red-400 italic">-- Sin Asignar --</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $pedido->fecha_programada ? $pedido->fecha_programada->format('d/m/Y H:i') : '--' }}</td>

                            {{-- 1. MODIFICACIÓN AQUÍ: Columna Acciones con lógica condicional --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @if($estado === 'entregado')
                                    <button wire:click="verEvidencia({{ $pedido->id_envio_pedido }})"
                                            class="inline-flex items-center text-emerald-600 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 px-3 py-1 rounded-md transition-colors border border-emerald-200 shadow-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                             viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 5 8.268 7.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Ver Evidencia
                                    </button>
                                @else
                                    <button wire:click="abrirModalReprogramar({{ $pedido->id_envio_pedido }})"
                                            class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-md transition-colors">
                                        Reprogramar
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p class="mt-2 text-sm font-medium">No se encontraron pedidos con estos filtros.</p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                {{ $pedidos->links() }}
            </div>
        </div>

    </x-panel>

    {{-- MODAL MASIVO (Sin cambios) --}}
    @if($modalMasivoOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-black/50 bg-opacity-75"
                     wire:click="$set('modalMasivoOpen', false)"></div>
                <div
                    class="relative z-50 inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-indigo-600 px-4 py-3 sm:px-6">
                        <h3 class="text-lg leading-6 font-bold text-white">📅 Asignar Ruta Masiva</h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6 space-y-4">
                        <div class="bg-blue-50 border border-blue-200 text-blue-800 p-3 rounded text-sm text-center">Se
                            actualizarán <b>{{ $countSeleccionados }}</b> pedidos seleccionados.
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Conductor</label>
                            <select wire:model="id_trabajador_masivo"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- Seleccionar --</option>
                                @foreach($transportistas as $chofer)
                                    <option
                                        value="{{ $chofer->id_trabajador }}">{{ $chofer->persona->nombre }} {{ $chofer->persona->apellido_paterno }}</option>
                                @endforeach
                            </select>
                            @error('id_trabajador_masivo') <span
                                class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de
                                Salida</label>
                            <input type="datetime-local" wire:model="fecha_programada_masiva"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('fecha_programada_masiva') <span
                                class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:flex sm:flex-row-reverse">
                        <button wire:click="guardarAsignacionMasiva"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:ml-3 sm:w-auto sm:text-sm">
                            Confirmar
                        </button>
                        <button wire:click="$set('modalMasivoOpen', false)"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL REPROGRAMACIÓN INDIVIDUAL --}}
    @if($modalReprogramarOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-black/50 bg-opacity-75"
                     wire:click="$set('modalReprogramarOpen', false)"></div>
                <div
                    class="relative z-50 inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                    <div class="bg-green-600 px-4 py-3 sm:px-6">
                        <h3 class="text-lg leading-6 font-bold text-white">🔄 Reprogramar Pedido
                            #{{ $pedidoReprogramarId }}</h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cambiar
                                Conductor</label>
                            <select wire:model="reprogramar_transportista"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                <option value="">-- Sin Asignar --</option>
                                @foreach($transportistas as $chofer)
                                    <option
                                        value="{{ $chofer->id_trabajador }}">{{ $chofer->persona->nombre }} {{ $chofer->persona->apellido_paterno }}</option>
                                @endforeach
                            </select>
                            @error('reprogramar_transportista') <span
                                class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nueva Fecha de
                                Salida</label>
                            <input type="datetime-local" wire:model="reprogramar_fecha"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            @error('reprogramar_fecha') <span
                                class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:flex sm:flex-row-reverse">
                        <button wire:click="guardarReprogramacion"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 sm:ml-3 sm:w-auto sm:text-sm">
                            Guardar Cambios
                        </button>
                        <button wire:click="$set('modalReprogramarOpen', false)"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- 2. MODIFICACIÓN AQUÍ: NUEVO MODAL DE EVIDENCIA --}}
    @if($modalEvidenciaOpen && $evidenciaPedido)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-black/75 backdrop-blur-sm"
                     wire:click="cerrarModalEvidencia"></div>

                <div
                    class="relative z-50 inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-200 dark:border-gray-700">

                    {{-- Header Modal --}}
                    <div class="bg-emerald-600 px-4 py-3 sm:px-6 flex justify-between items-center">
                        <h3 class="text-lg leading-6 font-bold text-white flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Entrega Confirmada #{{ $evidenciaPedido->id_envio_pedido }}
                        </h3>
                        <button wire:click="cerrarModalEvidencia"
                                class="text-white hover:text-gray-200 text-2xl font-bold">&times;
                        </button>
                    </div>

                    <div class="px-6 py-6 space-y-6">

                        {{-- Información Rápida --}}
                        <div
                            class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm bg-gray-50 dark:bg-gray-900/50 p-4 rounded-lg border border-gray-100 dark:border-gray-700">
                            <div>
                                <p class="text-gray-500 dark:text-gray-400 uppercase text-xs font-bold">Fecha de
                                    Entrega:</p>
                                <p class="font-medium text-gray-900 dark:text-white text-base">
                                    {{ $evidenciaPedido->fecha_entrega_real ? $evidenciaPedido->fecha_entrega_real->format('d/m/Y h:i A') : 'Sin fecha registrada' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-500 dark:text-gray-400 uppercase text-xs font-bold">
                                    Transportista:</p>
                                <p class="font-medium text-gray-900 dark:text-white text-base">
                                    {{ $evidenciaPedido->trabajador->persona->nombre ?? 'Desconocido' }}
                                    {{ $evidenciaPedido->trabajador->persona->apellido_paterno ?? '' }}
                                </p>
                            </div>
                        </div>

                        {{-- Foto de Evidencia --}}
                        <div class="border rounded-lg p-3 bg-white dark:bg-gray-800 shadow-sm">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-3 flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Fotografía de Entrega
                            </label>

                            @if($evidenciaPedido->foto_evidencia)
                                <div
                                    class="relative group flex justify-center bg-gray-100 dark:bg-gray-900 rounded-lg overflow-hidden">
                                    {{-- Enlace para abrir en pestaña nueva --}}
                                    <a href="{{ asset('storage/' . $evidenciaPedido->foto_evidencia) }}" target="_blank"
                                       class="block w-full">
                                        <img src="{{ asset('storage/' . $evidenciaPedido->foto_evidencia) }}"
                                             alt="Evidencia de entrega"
                                             class="w-full h-auto max-h-[350px] object-contain mx-auto transition-transform duration-300 group-hover:scale-105">

                                        <div
                                            class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors flex items-center justify-center">
                                            <span
                                                class="bg-black/70 text-white text-xs px-3 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none"
                                                     viewBox="0 0 24 24" stroke="currentColor"><path
                                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                                Clic para ampliar
                                            </span>
                                        </div>
                                    </a>
                                </div>
                            @else
                                <div
                                    class="flex flex-col items-center justify-center h-32 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded bg-gray-50 dark:bg-gray-900 text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mb-1 opacity-50" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-sm">Sin fotografía disponible</span>
                                </div>
                            @endif
                        </div>

                        {{-- Observaciones --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1 flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                </svg>
                                Observaciones del Transportista
                            </label>
                            <div
                                class="bg-yellow-50 dark:bg-yellow-900/10 border border-yellow-200 dark:border-yellow-800 rounded-md p-3 text-gray-800 dark:text-gray-200 text-sm min-h-[50px]">
                                {{ $evidenciaPedido->observaciones_entrega ?: 'Sin observaciones registradas.' }}
                            </div>
                        </div>

                    </div>

                    <div
                        class="bg-gray-50 dark:bg-gray-800 px-4 py-3 sm:flex sm:flex-row-reverse border-t border-gray-100 dark:border-gray-700">
                        <button wire:click="cerrarModalEvidencia"
                                class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:ml-3 sm:w-auto sm:text-sm transition">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Script para Alertas SweetAlert --}}
    @push('scripts')
        <script>
            Livewire.on('notify', (data) => {
                const payload = Array.isArray(data) ? data[0] : data;
                Swal.fire({
                    title: payload.title,
                    text: payload.description,
                    icon: payload.type,
                    timer: 2500,
                    showConfirmButton: false,
                    customClass: {popup: 'rounded-lg', title: 'text-lg font-semibold'}
                });
            });
        </script>
    @endpush
</div>
