<div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-4">
    <x-panel title="Gestión de Logística y Rutas" :breadcrumbs="[
        ['label' => 'Inicio', 'href' => '/', 'icon' => 'home'],
        ['label' => 'Inventario', 'href' => '#'],
        ['label' => 'Enterga de pedidos'],
    ]">

        <div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-4">
            <div class="max-w-7xl mx-auto">

                {{-- Cabecera --}}
                <div
                    class="flex flex-col md:flex-row items-center justify-between mb-6 bg-white dark:bg-gray-800 p-4 rounded-lg shadow gap-4">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white capitalize flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="text-indigo-600">
                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                            <line x1="16" x2="16" y1="2" y2="6"/>
                            <line x1="8" x2="8" y1="2" y2="6"/>
                            <line x1="3" x2="21" y1="10" y2="10"/>
                        </svg>
                        {{ $nombreMes }} {{ $anioActual }}
                    </h2>

                    <div class="flex items-center space-x-2">
                        {{-- Botón Hoy --}}
                        <button wire:click="irHoy"
                                class="px-4 py-2 bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition flex items-center gap-1 shadow-sm font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                 fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M8 2v4"/>
                                <path d="M16 2v4"/>
                                <rect width="18" height="18" x="3" y="4" rx="2"/>
                                <path d="M3 10h18"/>
                                <path d="m9 16 2 2 4-4"/>
                            </svg>
                            Hoy
                        </button>

                        <div class="h-6 w-px bg-gray-300 dark:bg-gray-600 mx-2"></div>

                        {{-- Navegación --}}
                        <button wire:click="cambiarMes('ant')"
                                class="px-3 py-2 bg-white border border-gray-300 dark:bg-gray-700 dark:border-gray-600 text-gray-700 dark:text-white rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition flex items-center gap-1 shadow-sm"
                                title="Mes Anterior">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                 fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m15 18-6-6 6-6"/>
                            </svg>
                        </button>
                        <button wire:click="cambiarMes('sig')"
                                class="px-3 py-2 bg-white border border-gray-300 dark:bg-gray-700 dark:border-gray-600 text-gray-700 dark:text-white rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition flex items-center gap-1 shadow-sm"
                                title="Mes Siguiente">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                 fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m9 18 6-6-6-6"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Grid Calendario --}}
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden border border-gray-200 dark:border-gray-700">
                    {{-- Encabezados Días --}}
                    <div
                        class="grid grid-cols-7 border-b dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-center font-bold text-gray-500 dark:text-gray-400 py-3 uppercase text-xs tracking-wider">
                        <div>Lun</div>
                        <div>Mar</div>
                        <div>Mié</div>
                        <div>Jue</div>
                        <div>Vie</div>
                        <div>Sáb</div>
                        <div>Dom</div>
                    </div>

                    {{-- Días --}}
                    <div class="grid grid-cols-7 auto-rows-fr bg-gray-200 dark:bg-gray-700 gap-[1px]">
                        {{-- Espacios vacíos --}}
                        @for($i = 0; $i < $espaciosVacios; $i++)
                            <div class="h-32 bg-white dark:bg-gray-900/50"></div>
                        @endfor

                        {{-- Días del mes --}}
                        @for($dia = 1; $dia <= $diasEnMes; $dia++)
                            @php
                                $fecha = \Carbon\Carbon::create($anioActual, $mesActual, $dia)->format('Y-m-d');
                                $esHoy = $fecha == date('Y-m-d');

                                // Filtrar pedidos del día actual del loop
                                $pedidosDia = $pedidosMes->filter(fn($p) => $p->fecha_programada->format('Y-m-d') == $fecha);
                                $count = $pedidosDia->count();

                                // Verificar si todos están entregados para cambiar el color del icono
                                $todosEntregados = $count > 0 && $pedidosDia->every(fn($p) => strtolower($p->estadoEnvio->nombre_estado_envio_pedido) === 'entregado');

                                // Clases condicionales
                                $iconClass = $todosEntregados ? 'text-gray-400 dark:text-gray-600' : 'text-indigo-600 dark:text-indigo-400 drop-shadow-sm';
                                $bgClass = $esHoy ? 'bg-indigo-50/40 dark:bg-indigo-900/20' : 'bg-white dark:bg-gray-800';

                                // Solo habilitar puntero y hover si hay pedidos
                                $interactionClass = $count > 0 ? 'cursor-pointer hover:bg-indigo-50 dark:hover:bg-gray-700' : 'cursor-default';
                            @endphp

                            {{-- LÓGICA DE CLICK: Solo asignamos wire:click si $count > 0 --}}
                            <div @if($count > 0) wire:click="seleccionarDia({{ $dia }})" @endif
                            class="h-32 p-2 relative group transition-colors duration-200 {{ $bgClass }} {{ $interactionClass }}">

                                {{-- Número del día --}}
                                <span
                                    class="absolute top-2 left-2 text-sm font-semibold {{ $esHoy ? 'bg-indigo-600 text-white w-7 h-7 flex items-center justify-center rounded-full shadow-sm' : 'text-gray-700 dark:text-gray-300' }}">
                            {{ $dia }}
                        </span>

                                {{-- Contenido (Camión y Contador) --}}
                                @if($count > 0)
                                    <div
                                        class="flex flex-col items-center justify-center h-full mt-2 group-hover:scale-105 transition-transform">
                                        {{-- Icono de Camión SVG --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40"
                                             viewBox="0 0 24 24"
                                             fill="currentColor" class="{{ $iconClass }}">
                                            <path
                                                d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.8 0-1.6.4-2.1 1.2l-2.6 4C.1 12.5 0 12.9 0 13.4V16c0 .6.4 1 1 1h1c0 1.7 1.3 3 3 3s3-1.3 3-3h8c0 1.7 1.3 3 3 3s3-1.3 3-3zM5 18c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1zm14 0c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1z"/>
                                        </svg>

                                        <span
                                            class="text-xs font-bold mt-1 px-2 py-0.5 rounded-full {{ $todosEntregados ? 'bg-gray-100 text-gray-500' : 'bg-indigo-100 text-indigo-700' }}">
                                    {{ $count }} Envíos
                                </span>
                                    </div>
                                @endif
                            </div>
                        @endfor
                    </div>
                </div>
            </div>

            {{-- MODAL DETALLE DEL DÍA --}}
            @if($modalDiaOpen)
                <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
                     aria-modal="true">
                    <div
                        class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">

                        {{-- Overlay --}}
                        <div class="fixed inset-0 transition-opacity bg-black/50 bg-opacity-75"
                             wire:click="$set('modalDiaOpen', false)" aria-hidden="true"></div>

                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"
                              aria-hidden="true">&#8203;</span>

                        {{-- Contenido Modal --}}
                        <div
                            class="relative z-50 inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full border border-gray-100 dark:border-gray-700">

                            {{-- Header Modal --}}
                            <div
                                class="bg-white dark:bg-gray-800 px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center sticky top-0 z-10">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                                <span class="bg-indigo-100 text-indigo-600 p-2 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </span>
                                        Hoja de Ruta
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 ml-1">
                                        Programación para el
                                        <strong>{{ $diaSeleccionado ? $diaSeleccionado->isoFormat('dddd D [de] MMMM') : '' }}</strong>
                                    </p>
                                </div>
                                <button wire:click="$set('modalDiaOpen', false)"
                                        class="text-gray-400 hover:text-gray-500 transition focus:outline-none p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                         viewBox="0 0 24 24"
                                         stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            <div class="px-6 py-6 bg-gray-50 dark:bg-gray-900/50 min-h-[300px]">
                                @if(count($pedidosDelDia) > 0)

                                    {{-- Resumen y Acción --}}
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                        <div
                                            class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col justify-center">
                                            <div
                                                class="text-sm text-gray-500 dark:text-gray-400 uppercase tracking-wide font-semibold">
                                                Total Entregas
                                            </div>
                                            <div class="flex items-baseline gap-2">
                                        <span
                                            class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ count($pedidosDelDia) }}</span>
                                                <span class="text-sm text-gray-500">pedidos asignados</span>
                                            </div>
                                        </div>

                                        <div class="flex items-center justify-end">
                                            <button wire:click="descargarHojaRuta"
                                                    class="w-full md:w-auto bg-gray-900 hover:bg-black dark:bg-indigo-600 dark:hover:bg-indigo-700 text-white font-medium py-3 px-6 rounded-xl shadow-lg transition-all transform hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-3">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                     viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                <span>Descargar PDF para Imprimir</span>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Lista de Pedidos --}}
                                    <div
                                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                                        <div
                                            class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex justify-between items-center">
                                            <h4 class="font-semibold text-gray-700 dark:text-gray-300 text-sm">Detalle
                                                de
                                                Ruta</h4>
                                            <span
                                                class="text-xs text-gray-500 bg-white dark:bg-gray-700 px-2 py-1 rounded border border-gray-200 dark:border-gray-600">Ordenado por hora</span>
                                        </div>
                                        <div class="overflow-x-auto max-h-[400px] overflow-y-auto">
                                            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                                                <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0 z-10">
                                                <tr>
                                                    <th scope="col"
                                                        class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider w-16">
                                                        Hora
                                                    </th>
                                                    <th scope="col"
                                                        class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                        Cliente / Contacto
                                                    </th>
                                                    <th scope="col"
                                                        class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                        Destino
                                                    </th>
                                                    <th scope="col"
                                                        class="px-4 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider w-28">
                                                        Estado
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody
                                                    class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                                                @foreach($pedidosDelDia as $pedido)
                                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors group">
                                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-bold text-gray-700 dark:text-gray-300">
                                                            {{ $pedido->fecha_programada->format('H:i') }}
                                                        </td>
                                                        <td class="px-4 py-4">
                                                            <div
                                                                class="text-sm font-medium text-gray-900 dark:text-white">{{ $pedido->venta->cliente->persona->nombre }}</div>
                                                            <div
                                                                class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 flex items-center gap-1">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                                     viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                          stroke-width="2"
                                                                          d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                                                </svg>
                                                                {{ $pedido->venta->cliente->persona->numero_telefono_personal ?? 'Sin teléfono' }}
                                                            </div>
                                                        </td>
                                                        <td class="px-4 py-4">
                                                            <div class="text-sm text-gray-800 dark:text-gray-200">
                                                                {{ $pedido->direccion->nombre_calle }}
                                                                #{{ $pedido->direccion->numero }}
                                                            </div>
                                                            <div
                                                                class="text-xs text-gray-500 mt-1 flex items-start gap-1">
                                                                <svg class="w-3 h-3 mt-0.5 flex-shrink-0" fill="none"
                                                                     stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                          stroke-width="2"
                                                                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                          stroke-width="2"
                                                                          d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                                </svg>
                                                                <span
                                                                    class="truncate max-w-[200px]">{{ $pedido->direccion->referencia ?? 'Sin referencia' }}</span>
                                                            </div>
                                                            @if(strtolower($pedido->estadoEnvio->nombre_estado_envio_pedido) !== 'entregado')
                                                                <button
                                                                    wire:click="abrirModalEvidencia({{ $pedido->id_envio_pedido }})"
                                                                    class="text-blue-600 hover:underline text-xs">
                                                                    📷 Registrar Entrega
                                                                </button>
                                                            @else
                                                                <span
                                                                    class="text-green-600 text-xs">Entregado el {{ $pedido->fecha_entrega_real?->format('H:i') }}</span>
                                                            @endif
                                                        </td>
                                                        <td class="px-4 py-4 text-center">
                                                            @php
                                                                $est = strtolower($pedido->estadoEnvio->nombre_estado_envio_pedido);
                                                                $isEntregado = $est === 'entregado';
                                                                $badgeClass = $isEntregado
                                                                    ? 'bg-gray-100 text-gray-600 border-gray-200 ring-gray-500/10'
                                                                    : 'bg-emerald-50 text-emerald-700 border-emerald-100 ring-emerald-600/20';
                                                                $icon = $isEntregado ? '✓' : '🚚';
                                                            @endphp
                                                            <span
                                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border ring-1 ring-inset {{ $badgeClass }}">
                                                            <span>{{ $icon }}</span> {{ ucfirst($est) }}
                                                        </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex flex-col items-center justify-center py-12 text-center">
                                        <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded-full mb-3">
                                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Sin
                                            programación</h3>
                                        <p class="text-gray-500 dark:text-gray-400 mt-1 max-w-sm">No tienes entregas
                                            asignadas
                                            para este día.</p>
                                    </div>
                                @endif
                            </div>

                            {{-- Footer Modal --}}
                            @if(count($pedidosDelDia) > 0)
                                <div
                                    class="bg-gray-50 dark:bg-gray-800 px-6 py-4 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center text-xs text-gray-500">
                                    <div>
                                        Generado el {{ now()->format('d/m/Y H:i') }}
                                    </div>

                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
        {{-- MODAL EVIDENCIA --}}
        @if($modalEvidenciaOpen)
            <div class="fixed inset-0 z-[60] overflow-y-auto"> {{-- z-60 para estar encima del otro modal --}}
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="fixed inset-0 bg-black/50" wire:click="cerrarModalEvidencia"></div>

                    <div class="relative bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full shadow-2xl">
                        <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">📸 Confirmar Entrega</h3>

                        {{-- Input Foto --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Foto de
                                Evidencia</label>
                            <input type="file" wire:model="fotoEvidencia" accept="image/*"
                                   class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            @error('fotoEvidencia') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

                            {{-- Previsualización --}}
                            @if ($fotoEvidencia)
                                <img src="{{ $fotoEvidencia->temporaryUrl() }}"
                                     class="mt-2 w-full h-40 object-cover rounded border">
                            @endif
                        </div>

                        {{-- Input Observación --}}
                        <div class="mb-4">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Observaciones</label>
                            <textarea wire:model="observacionEntrega"
                                      class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                      rows="3" placeholder="Ej: Recibido por el vigilante..."></textarea>
                        </div>

                        <div class="flex justify-end gap-2">
                            <button wire:click="cerrarModalEvidencia"
                                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Cancelar
                            </button>
                            <button wire:click="guardarEvidencia"
                                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 flex items-center gap-2">
                                <span>Guardar</span>
                                <div wire:loading wire:target="guardarEvidencia, fotoEvidencia"
                                     class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </x-panel>
</div>
