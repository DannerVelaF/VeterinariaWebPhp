<div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-2 md:p-4"> {{-- Padding reducido en móvil --}}
    <x-panel title="Gestión de Logística y Rutas" :breadcrumbs="[
        ['label' => 'Inicio', 'href' => '/', 'icon' => 'home'],
        ['label' => 'Inventario', 'href' => '#'],
        ['label' => 'Entrega de pedidos'],
    ]">

        <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
            <div class="max-w-7xl mx-auto">

                {{-- Cabecera --}}
                <div
                    class="flex flex-col md:flex-row items-center justify-between mb-6 bg-white dark:bg-gray-800 p-4 rounded-lg shadow gap-4">
                    {{-- Título y Fecha --}}
                    <h2 class="text-xl md:text-2xl font-bold text-gray-800 dark:text-white capitalize flex items-center gap-2 w-full md:w-auto justify-center md:justify-start">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="text-indigo-600">
                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                            <line x1="16" x2="16" y1="2" y2="6"/>
                            <line x1="8" x2="8" y1="2" y2="6"/>
                            <line x1="3" x2="21" y1="10" y2="10"/>
                        </svg>
                        <span>{{ $nombreMes }} <span class="text-gray-400">{{ $anioActual }}</span></span>
                    </h2>

                    {{-- Controles de Navegación --}}
                    <div class="flex items-center justify-center space-x-2 w-full md:w-auto">
                        <button wire:click="irHoy"
                                class="flex-1 md:flex-none justify-center px-4 py-2 bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition flex items-center gap-1 shadow-sm font-medium text-sm md:text-base">
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

                        <div class="h-6 w-px bg-gray-300 dark:bg-gray-600 mx-2 hidden md:block"></div>

                        <div class="flex gap-1">
                            <button wire:click="cambiarMes('ant')"
                                    class="p-2 md:px-3 md:py-2 bg-white border border-gray-300 dark:bg-gray-700 dark:border-gray-600 text-gray-700 dark:text-white rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition flex items-center shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <path d="m15 18-6-6 6-6"/>
                                </svg>
                            </button>
                            <button wire:click="cambiarMes('sig')"
                                    class="p-2 md:px-3 md:py-2 bg-white border border-gray-300 dark:bg-gray-700 dark:border-gray-600 text-gray-700 dark:text-white rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition flex items-center shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <path d="m9 18 6-6-6-6"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- RESPONSIVE: Wrapper con overflow-x-auto para permitir scroll horizontal en móviles --}}
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col">
                    <div class="overflow-x-auto custom-scrollbar">
                        {{-- RESPONSIVE: min-w-[800px] fuerza a que el calendario mantenga su forma y aparezca el scroll --}}
                        <div class="min-w-[800px] md:min-w-full">
                            {{-- Encabezados Días --}}
                            <div
                                class="grid grid-cols-7 border-b dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-center font-bold text-gray-500 dark:text-gray-400 py-3 uppercase text-xs tracking-wider">
                                <div>Lunes</div>
                                <div>Martes</div>
                                <div>Miércoles</div>
                                <div>Jueves</div>
                                <div>Viernes</div>
                                <div>Sábado</div>
                                <div>Domingo</div>
                            </div>

                            {{-- Días --}}
                            <div class="grid grid-cols-7 auto-rows-fr bg-gray-200 dark:bg-gray-700 gap-[1px]">
                                {{-- Espacios vacíos --}}
                                @for($i = 0; $i < $espaciosVacios; $i++)
                                    <div class="min-h-[8rem] bg-white dark:bg-gray-900/50"></div>
                                @endfor

                                {{-- Días del mes --}}
                                @for($dia = 1; $dia <= $diasEnMes; $dia++)
                                    @php
                                        $fecha = \Carbon\Carbon::create($anioActual, $mesActual, $dia)->format('Y-m-d');
                                        $esHoy = $fecha == date('Y-m-d');
                                        $pedidosDia = $pedidosMes->filter(fn($p) => $p->fecha_programada->format('Y-m-d') == $fecha);
                                        $count = $pedidosDia->count();
                                        $todosEntregados = $count > 0 && $pedidosDia->every(fn($p) => strtolower($p->estadoEnvio->nombre_estado_envio_pedido) === 'entregado');
                                        $iconClass = $todosEntregados ? 'text-gray-400 dark:text-gray-600' : 'text-indigo-600 dark:text-indigo-400 drop-shadow-sm';
                                        $bgClass = $esHoy ? 'bg-indigo-50/40 dark:bg-indigo-900/20' : 'bg-white dark:bg-gray-800';
                                        $interactionClass = $count > 0 ? 'cursor-pointer hover:bg-indigo-50 dark:hover:bg-gray-700' : 'cursor-default';
                                    @endphp

                                    <div @if($count > 0) wire:click="seleccionarDia({{ $dia }})" @endif
                                    class="min-h-[8rem] p-2 relative group transition-colors duration-200 {{ $bgClass }} {{ $interactionClass }}">

                                        {{-- Número del día --}}
                                        <span
                                            class="absolute top-2 left-2 text-sm font-semibold {{ $esHoy ? 'bg-indigo-600 text-white w-7 h-7 flex items-center justify-center rounded-full shadow-sm' : 'text-gray-700 dark:text-gray-300' }}">
                                            {{ $dia }}
                                        </span>

                                        @if($count > 0)
                                            <div
                                                class="flex flex-col items-center justify-center h-full mt-2 group-hover:scale-105 transition-transform">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                     class="w-8 h-8 md:w-10 md:h-10 {{ $iconClass }}"
                                                     viewBox="0 0 24 24" fill="currentColor">
                                                    <path
                                                        d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.8 0-1.6.4-2.1 1.2l-2.6 4C.1 12.5 0 12.9 0 13.4V16c0 .6.4 1 1 1h1c0 1.7 1.3 3 3 3s3-1.3 3-3h8c0 1.7 1.3 3 3 3s3-1.3 3-3zM5 18c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1zm14 0c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1z"/>
                                                </svg>

                                                <span
                                                    class="text-[10px] md:text-xs font-bold mt-1 px-2 py-0.5 rounded-full whitespace-nowrap {{ $todosEntregados ? 'bg-gray-100 text-gray-500' : 'bg-indigo-100 text-indigo-700' }}">
                                                    {{ $count }} <span class="hidden sm:inline">Envíos</span>
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- MODAL DETALLE DEL DÍA --}}
            @if($modalDiaOpen)
                <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
                     aria-modal="true">
                    {{-- RESPONSIVE: Padding reducido (px-4 -> px-2) en móvil para ganar espacio --}}
                    <div
                        class="flex items-center justify-center min-h-screen px-2 pt-4 pb-20 text-center sm:block sm:p-0">

                        <div class="fixed inset-0 transition-opacity bg-black/50 bg-opacity-75"
                             wire:click="$set('modalDiaOpen', false)" aria-hidden="true"></div>

                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"
                              aria-hidden="true">&#8203;</span>

                        {{-- RESPONSIVE: w-full, align-bottom en móvil (estilo sheet) o centrado --}}
                        <div
                            class="relative z-50 inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl w-full border border-gray-100 dark:border-gray-700">

                            {{-- Header Modal --}}
                            <div
                                class="bg-white dark:bg-gray-800 px-4 md:px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center sticky top-0 z-10">
                                <div>
                                    <h3 class="text-lg md:text-xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                                        <span class="bg-indigo-100 text-indigo-600 p-1.5 md:p-2 rounded-lg">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6"
                                                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </span>
                                        Hoja de Ruta
                                    </h3>
                                    <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400 mt-1 ml-1">
                                        {{ $diaSeleccionado ? $diaSeleccionado->isoFormat('dddd D [de] MMMM') : '' }}
                                    </p>
                                </div>
                                <button wire:click="$set('modalDiaOpen', false)"
                                        class="text-gray-400 hover:text-gray-500 p-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            <div class="px-4 md:px-6 py-4 md:py-6 bg-gray-50 dark:bg-gray-900/50 min-h-[300px]">
                                @if(count($pedidosDelDia) > 0)
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                        <div
                                            class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-row md:flex-col justify-between md:justify-center items-center md:items-start">
                                            <div
                                                class="text-sm text-gray-500 dark:text-gray-400 uppercase tracking-wide font-semibold">
                                                Total Entregas
                                            </div>
                                            <div class="flex items-baseline gap-2">
                                                <span
                                                    class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ count($pedidosDelDia) }}</span>
                                                <span
                                                    class="text-sm text-gray-500 hidden md:inline">pedidos asignados</span>
                                            </div>
                                        </div>

                                        <div class="flex items-center justify-end">
                                            <button wire:click="descargarHojaRuta"
                                                    class="w-full md:w-auto bg-gray-900 hover:bg-black dark:bg-indigo-600 dark:hover:bg-indigo-700 text-white font-medium py-3 px-6 rounded-xl shadow-lg transition-all flex items-center justify-center gap-3">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                     viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                <span>Imprimir PDF</span>
                                            </button>
                                        </div>
                                    </div>

                                    <div
                                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                                        <div
                                            class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex justify-between items-center">
                                            <h4 class="font-semibold text-gray-700 dark:text-gray-300 text-sm">Detalle
                                                de Ruta</h4>
                                        </div>

                                        {{-- RESPONSIVE: Tabla con scroll horizontal en móvil --}}
                                        <div class="overflow-x-auto max-h-[400px] overflow-y-auto">
                                            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                                                <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0 z-10">
                                                <tr>
                                                    <th scope="col"
                                                        class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider w-16">
                                                        Hora
                                                    </th>
                                                    <th scope="col"
                                                        class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider min-w-[140px]">
                                                        Cliente
                                                    </th>
                                                    <th scope="col"
                                                        class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider min-w-[160px]">
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
                                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-bold text-gray-700 dark:text-gray-300 align-top">
                                                            {{ $pedido->fecha_programada->format('H:i') }}
                                                        </td>
                                                        <td class="px-4 py-4 align-top">
                                                            <div
                                                                class="text-sm font-medium text-gray-900 dark:text-white line-clamp-2">
                                                                {{ $pedido->venta->cliente->persona->nombre }}
                                                            </div>
                                                            <div
                                                                class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 flex items-center gap-1">
                                                                {{ $pedido->venta->cliente->persona->numero_telefono_personal ?? '-' }}
                                                            </div>
                                                        </td>
                                                        <td class="px-4 py-4 align-top">
                                                            <div class="text-sm text-gray-800 dark:text-gray-200">
                                                                {{ $pedido->direccion->nombre_calle }}
                                                                #{{ $pedido->direccion->numero }}
                                                            </div>
                                                            <div
                                                                class="text-xs text-gray-500 mt-1 flex items-start gap-1">
                                                                <span
                                                                    class="truncate max-w-[150px]">{{ $pedido->direccion->referencia ?? '' }}</span>
                                                            </div>
                                                            <div class="mt-2">
                                                                @if(strtolower($pedido->estadoEnvio->nombre_estado_envio_pedido) !== 'entregado')
                                                                    <button
                                                                        wire:click="abrirModalEvidencia({{ $pedido->id_envio_pedido }})"
                                                                        class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                                        📷 Registrar
                                                                    </button>
                                                                @else
                                                                    <span
                                                                        class="text-green-600 text-xs flex items-center gap-1">
                                                                        <svg class="w-3 h-3" fill="none"
                                                                             stroke="currentColor" viewBox="0 0 24 24"><path
                                                                                stroke-linecap="round"
                                                                                stroke-linejoin="round" stroke-width="2"
                                                                                d="M5 13l4 4L19 7"/></svg>
                                                                        {{ $pedido->fecha_entrega_real?->format('H:i') }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td class="px-4 py-4 text-center align-top">
                                                            @php
                                                                $est = strtolower($pedido->estadoEnvio->nombre_estado_envio_pedido);
                                                                $isEntregado = $est === 'entregado';
                                                                $badgeClass = $isEntregado
                                                                    ? 'bg-gray-100 text-gray-600 border-gray-200'
                                                                    : 'bg-emerald-50 text-emerald-700 border-emerald-100';
                                                            @endphp
                                                            <span
                                                                class="inline-flex flex-col items-center justify-center px-2 py-1 rounded text-[10px] font-medium border {{ $badgeClass }}">
                                                                <span>{{ $isEntregado ? 'ENTREGADO' : 'PENDIENTE' }}</span>
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
                                        {{-- SVG y Texto de vacío sin cambios --}}
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
                                            asignadas.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- MODAL EVIDENCIA --}}
        @if($modalEvidenciaOpen)
            <div class="fixed inset-0 z-[60] overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen px-4 pb-4 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-black/50 transition-opacity" wire:click="cerrarModalEvidencia"></div>

                    {{-- Centrado vertical en móvil --}}
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                    <div
                        class="inline-block align-middle bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-md w-full p-6">
                        <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-white flex items-center gap-2">
                            <span>📸</span> Confirmar Entrega
                        </h3>

                        {{-- Input Foto --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Evidencia
                                Fotográfica</label>

                            {{-- Input de archivo mejorado para móvil --}}
                            <div
                                class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-md relative hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <div class="space-y-1 text-center">
                                    @if (!$fotoEvidencia)
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                             viewBox="0 0 48 48">
                                            <path
                                                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 005.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                            <label for="file-upload"
                                                   class="relative cursor-pointer bg-white dark:bg-transparent rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none">
                                                <span>Subir un archivo</span>
                                                <input id="file-upload" wire:model="fotoEvidencia" type="file"
                                                       accept="image/*" class="sr-only">
                                            </label>
                                            <p class="pl-1">o arrastrar y soltar</p>
                                        </div>
                                    @else
                                        <img src="{{ $fotoEvidencia->temporaryUrl() }}"
                                             class="max-h-40 mx-auto rounded shadow-sm">
                                        <button wire:click="$set('fotoEvidencia', null)"
                                                class="text-xs text-red-500 mt-2 underline">Cambiar foto
                                        </button>
                                    @endif
                                </div>
                            </div>
                            @error('fotoEvidencia') <span
                                class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Input Observación --}}
                        <div class="mb-6">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Observaciones</label>
                            <textarea wire:model="observacionEntrega"
                                      class="w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                                      rows="3" placeholder="Opcional..."></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <button wire:click="cerrarModalEvidencia"
                                    class="w-full px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 font-medium">
                                Cancelar
                            </button>
                            <button wire:click="guardarEvidencia"
                                    class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center justify-center gap-2 font-medium shadow-md shadow-indigo-200 dark:shadow-none">
                                <span>Guardar</span>
                                <div wire:loading wire:target="guardarEvidencia"
                                     class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </x-panel>
</div>
