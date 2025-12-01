<div>
    <x-panel title="Gestión de Ventas" :breadcrumbs="[
        ['label' => 'Inicio', 'href' => '/', 'icon' => 'home'],
        ['label' => 'Ventas', 'href' => '#'],
        ['label' => 'Reporte de caja'],
    ]">
        <div>
            <livewire:caja-table/>
        </div>
    </x-panel>

    {{-- MODAL --}}
    @if($modalOpen && $cajaSeleccionada)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">

            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

                <div class="fixed inset-0 bg-black/50 bg-opacity-75 transition-opacity"
                     wire:click="closeModal" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                {{-- Contenido del Modal con Alpine Data para Tabs --}}
                <div x-data="{ tab: 'resumen' }"
                     class="relative z-50 inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full">

                    {{-- Header Fijo --}}
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-0 sm:px-6 border-b dark:border-gray-700">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                    Reporte de Caja #{{ $cajaSeleccionada->id_caja }}
                                </h3>
                                <div class="text-sm text-gray-500 mt-1 flex flex-col sm:flex-row sm:gap-4">
                                    <span>📅 Apertura: {{ $cajaSeleccionada->fecha_apertura->format('d/m/Y H:i') }}</span>
                                    <span>👤 Responsable: {{ $cajaSeleccionada->trabajador?->persona?->nombre }}</span>
                                </div>
                            </div>
                            <button wire:click="closeModal"
                                    class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                <span class="text-2xl font-bold">&times;</span>
                            </button>
                        </div>

                        {{-- Navegación de Pestañas (Tabs) --}}
                        <div class="flex space-x-6">
                            <button @click="tab = 'resumen'"
                                    :class="{ 'border-indigo-500 text-indigo-600 dark:text-indigo-400': tab === 'resumen', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'resumen' }"
                                    class="whitespace-nowrap pb-3 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                                📊 Resumen Financiero
                            </button>
                            <button @click="tab = 'ranking'"
                                    :class="{ 'border-indigo-500 text-indigo-600 dark:text-indigo-400': tab === 'ranking', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'ranking' }"
                                    class="whitespace-nowrap pb-3 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                                🏆 Ranking Productos y Servicios
                            </button>
                        </div>
                    </div>

                    {{-- Cuerpo del Modal (Scrollable) --}}
                    <div class="bg-gray-50 dark:bg-gray-900 px-4 py-6 sm:p-6 max-h-[70vh] overflow-y-auto">

                        {{-- TAB 1: RESUMEN FINANCIERO --}}
                        <div x-show="tab === 'resumen'" x-transition.opacity>

                            {{-- KPIs Superiores --}}
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                                {{-- Card 1 --}}
                                <div
                                    class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Monto
                                        Inicial
                                    </div>
                                    <div class="mt-1 text-2xl font-bold text-gray-800 dark:text-white">
                                        S/ {{ number_format($cajaSeleccionada->monto_inicial, 2) }}
                                    </div>
                                </div>
                                {{-- Card 2 --}}
                                <div
                                    class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Ingresos
                                        Totales
                                    </div>
                                    <div class="mt-1 text-2xl font-bold text-indigo-600">
                                        S/ {{ number_format($cajaSeleccionada->total_ventas, 2) }}
                                    </div>
                                </div>
                                {{-- Card 3 --}}
                                <div
                                    class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">En Caja
                                        (Calculado)
                                    </div>
                                    <div class="mt-1 text-2xl font-bold text-gray-800 dark:text-white">
                                        S/ {{ number_format($cajaSeleccionada->total_esperado, 2) }}
                                    </div>
                                </div>
                                {{-- Card 4 --}}
                                <div
                                    class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Cuadre /
                                        Diferencia
                                    </div>
                                    @php $diff = $cajaSeleccionada->diferencia ?? 0; @endphp
                                    <div class="mt-1 flex items-center">
                                        <span
                                            class="text-2xl font-bold {{ $diff < 0 ? 'text-red-500' : ($diff > 0 ? 'text-green-500' : 'text-gray-600') }}">
                                            S/ {{ number_format($diff, 2) }}
                                        </span>
                                        @if($diff != 0)
                                            <span
                                                class="ml-2 text-xs px-2 py-0.5 rounded-full {{ $diff < 0 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                                {{ $diff < 0 ? 'Faltante' : 'Sobrante' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                {{-- Desglose por Método de Pago --}}
                                <div
                                    class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                                    <h4 class="font-bold text-gray-700 dark:text-gray-200 mb-4 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                        </svg>
                                        Métodos de Pago
                                    </h4>
                                    <div class="space-y-4">
                                        @foreach($balancePagos as $pago)
                                            @if($pago['monto'] > 0)
                                                <div class="relative pt-1">
                                                    <div class="flex mb-2 items-center justify-between">
                                                        <div>
                                                            <span
                                                                class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full {{ str_replace('bg-', 'text-', str_replace('500', '600', $pago['color'])) }} bg-opacity-20">
                                                                {{ $pago['label'] }}
                                                            </span>
                                                        </div>
                                                        <div class="text-right">
                                                            <span
                                                                class="text-sm font-semibold inline-block text-gray-700 dark:text-gray-200">
                                                                S/ {{ number_format($pago['monto'], 2) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-gray-200 dark:bg-gray-700">
                                                        @php $pct = ($cajaSeleccionada->total_ventas > 0) ? ($pago['monto'] / $cajaSeleccionada->total_ventas) * 100 : 0; @endphp
                                                        <div style="width:{{ $pct }}%"
                                                             class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center {{ $pago['color'] }}"></div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Desglose Productos vs Servicios --}}
                                <div
                                    class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                                    <h4 class="font-bold text-gray-700 dark:text-gray-200 mb-4 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                        Origen de Ingresos
                                    </h4>

                                    @php
                                        $totalMix = $balanceTipoVenta['total'] > 0 ? $balanceTipoVenta['total'] : 1;
                                        $pctProd = ($balanceTipoVenta['productos'] / $totalMix) * 100;
                                        $pctServ = ($balanceTipoVenta['servicios'] / $totalMix) * 100;
                                    @endphp

                                    <div class="flex items-center justify-center h-32 space-x-2">
                                        {{-- Barra Productos --}}
                                        <div class="flex flex-col items-center justify-end h-full w-16 group relative">
                                            <div
                                                class="w-full bg-indigo-500 rounded-t-lg transition-all duration-500 hover:bg-indigo-600"
                                                style="height: {{ $pctProd == 0 ? 2 : $pctProd }}%"></div>
                                            <span
                                                class="text-xs mt-2 font-bold text-gray-600 dark:text-gray-400">Prod.</span>
                                            {{-- Tooltip simple --}}
                                            <div
                                                class="absolute bottom-full mb-1 opacity-0 group-hover:opacity-100 transition-opacity bg-black text-white text-xs rounded py-1 px-2 whitespace-nowrap">
                                                S/ {{ number_format($balanceTipoVenta['productos'], 2) }}
                                            </div>
                                        </div>

                                        {{-- Barra Servicios --}}
                                        <div class="flex flex-col items-center justify-end h-full w-16 group relative">
                                            <div
                                                class="w-full bg-orange-500 rounded-t-lg transition-all duration-500 hover:bg-orange-600"
                                                style="height: {{ $pctServ == 0 ? 2 : $pctServ }}%"></div>
                                            <span
                                                class="text-xs mt-2 font-bold text-gray-600 dark:text-gray-400">Serv.</span>
                                            <div
                                                class="absolute bottom-full mb-1 opacity-0 group-hover:opacity-100 transition-opacity bg-black text-white text-xs rounded py-1 px-2 whitespace-nowrap">
                                                S/ {{ number_format($balanceTipoVenta['servicios'], 2) }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-4 grid grid-cols-2 gap-4 text-center">
                                        <div class="p-2 bg-indigo-50 dark:bg-indigo-900/20 rounded">
                                            <div
                                                class="text-xs text-indigo-600 dark:text-indigo-400 font-bold uppercase">
                                                Productos
                                            </div>
                                            <div class="font-bold text-gray-800 dark:text-gray-200">
                                                S/ {{ number_format($balanceTipoVenta['productos'], 2) }}</div>
                                        </div>
                                        <div class="p-2 bg-orange-50 dark:bg-orange-900/20 rounded">
                                            <div
                                                class="text-xs text-orange-600 dark:text-orange-400 font-bold uppercase">
                                                Servicios
                                            </div>
                                            <div class="font-bold text-gray-800 dark:text-gray-200">
                                                S/ {{ number_format($balanceTipoVenta['servicios'], 2) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- TAB 2: RANKING --}}
                        <div x-show="tab === 'ranking'" x-transition.opacity style="display: none;">
                            <div
                                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                                <div
                                    class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex justify-between items-center">
                                    <h4 class="font-bold text-gray-700 dark:text-gray-200">Top Movimientos (Productos y
                                        Servicios)</h4>
                                </div>

                                @if(count($topItems) > 0)
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full text-sm">
                                            <thead
                                                class="bg-gray-50 dark:bg-gray-700 text-gray-500 uppercase font-medium text-xs">
                                            <tr>
                                                <th class="px-6 py-3 text-left tracking-wider">Item</th>
                                                <th class="px-6 py-3 text-center tracking-wider">Tipo</th>
                                                <th class="px-6 py-3 text-right tracking-wider">Cantidad</th>
                                                <th class="px-6 py-3 text-right tracking-wider">Total Generado</th>
                                                <th class="px-6 py-3 text-left tracking-wider w-1/4">Participación</th>
                                            </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                            @php $maxVenta = collect($topItems)->max('total_vendido'); @endphp
                                            @foreach($topItems as $item)
                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                                    <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900 dark:text-white">
                                                        {{ $item->nombre }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                                        @if($item->tipo_item === 'producto')
                                                            <span
                                                                class="px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-700 border border-indigo-200">
                                                                    📦 Producto
                                                                </span>
                                                        @else
                                                            <span
                                                                class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-700 border border-orange-200">
                                                                    🛠️ Servicio
                                                                </span>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-right text-gray-600 dark:text-gray-300">
                                                        {{ $item->total_vendido }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-right font-bold text-gray-800 dark:text-white">
                                                        S/ {{ number_format($item->total_dinero, 2) }}
                                                    </td>
                                                    <td class="px-6 py-4 align-middle">
                                                        <div
                                                            class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-600">
                                                            <div
                                                                class="{{ $item->tipo_item === 'producto' ? 'bg-indigo-500' : 'bg-orange-500' }} h-2 rounded-full"
                                                                style="width: {{ ($item->total_vendido / $maxVenta) * 100 }}%"></div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="p-10 text-center text-gray-400">
                                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24"
                                             stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p class="mt-2">No hay movimientos registrados.</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
