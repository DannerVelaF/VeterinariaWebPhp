<x-panel title="Gestión de entradas de inventario" :breadcrumbs="[
    ['label' => 'Inicio', 'href' => '/', 'icon' => 'home'],
    ['label' => 'Inventario', 'href' => '#'],
    ['label' => 'Entradas'],
]">
    <x-tabs :tabs="['registro' => 'Registar entradas', 'detalle' => 'Listado de entradas']" default="registro">
        <x-tab name="registro">
            {{-- Mensajes de feedback (Session) --}}
            @if (session()->has('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                     class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif
            @if (session()->has('error'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                     class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif

            {{-- 🔴 AQUÍ MOSTRAMOS ERRORES GENERALES DE VALIDACIÓN SI EXISTEN --}}
            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded text-red-600 text-sm">
                    <p class="font-bold">Por favor corrija los siguientes errores:</p>
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="max-w-full h-full">
                <x-card>
                    <div class="flex flex-col gap-2 mb-6">
                        <p class="font-medium text-xl flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="blue" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="lucide lucide-package-check">
                                <path d="m16 16 2 2 4-4"/>
                                <path
                                    d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14"/>
                                <path d="m16.5 9.4-9 5.19"/>
                                <polyline points="3.29 7 12 12 20.71 7"/>
                                <line x1="12" x2="12" y1="22" y2="12"/>
                            </svg>
                            Recepción de Mercadería (OC)
                        </p>
                        <p class="text-md text-gray-500">Procesar entrada de productos desde una Orden de Compra.</p>
                    </div>

                    {{-- Buscador de OC --}}
                    <div class="bg-blue-50 p-4 rounded-lg mb-6 border border-blue-100">
                        <div class="flex flex-col md:flex-row gap-4 items-end">
                            <div class="w-full md:w-1/3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Código Orden de
                                    Compra</label>
                                <div class="flex gap-2">
                                    <input type="text" wire:model="ordenCompra" placeholder="Ej: OC-2025-..."
                                           wire:keydown.enter="buscarOrdenCompra"
                                           class="border rounded-l px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-300 w-full uppercase">
                                    <button type="button" wire:click="buscarOrdenCompra"
                                            class="bg-blue-600 hover:bg-blue-700 text-white rounded-r px-4 py-2 font-medium transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="11" cy="11" r="8"/>
                                            <path d="m21 21-4.3-4.3"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            @if ($proveedorOC)
                                <div
                                    class="w-full md:w-2/3 grid grid-cols-1 md:grid-cols-2 gap-4 bg-white p-3 rounded border border-gray-200">
                                    <div>
                                        <label class="text-xs font-bold text-gray-500 uppercase">Proveedor</label>
                                        <p class="font-semibold text-gray-800">{{ $proveedorOC->nombre_proveedor }}</p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-bold text-gray-500 uppercase">Estado /
                                            Pendientes</label>
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="px-2 py-0.5 rounded text-xs font-bold bg-green-100 text-green-700 border border-green-200">APROBADO</span>
                                            <span class="text-sm font-medium text-gray-600">{{ $this->productosPendientesCount }} productos por recibir</span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if (count($productosOC) > 0)
                        <div class="animate-fade-in-down">
                            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm">
                                <div class="p-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                                    <h3 class="font-bold text-gray-700">Detalle de Productos</h3>
                                    <span
                                        class="text-xs text-gray-500">* Verifique cantidades y fechas antes de guardar</span>
                                </div>

                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm text-left">
                                        <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                                        <tr>
                                            <th class="px-4 py-3">Producto</th>
                                            <th class="px-4 py-3 text-center">Cant. OC</th>
                                            <th class="px-4 py-3 text-center">Recibido</th>
                                            <th class="px-4 py-3 text-center w-32">Cant. Entrada</th>
                                            <th class="px-4 py-3 text-center w-32">Ubicación</th>
                                            <th class="px-4 py-3 text-center w-32">Vencimiento</th>
                                        </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                        @foreach ($productosOC as $producto)
                                            <tr class="hover:bg-gray-50 transition-colors {{ $producto['cantidad'] == 0 ? 'bg-gray-50 opacity-60' : '' }}">
                                                <td class="px-4 py-3 align-top">
                                                    <div
                                                        class="font-medium text-gray-800">{{ $producto['nombre'] }}</div>
                                                    <div class="text-xs text-gray-500">
                                                        Cód: {{ $producto['codigo_barras'] ?? 'S/C' }} |
                                                        Costo: S/ {{ number_format($producto['precio_compra'], 2) }}
                                                    </div>
                                                    @if (!$producto['pertenece_proveedor'])
                                                        <span class="text-xs text-red-500 font-bold">⚠️ No asociado al proveedor</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-center font-medium align-top">
                                                    {{ $producto['cantidad_original'] }}
                                                </td>
                                                <td class="px-4 py-3 text-center align-top">
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $producto['cantidad'] == 0 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                        {{ $producto['cantidad_recibida'] }}
                                                    </span>
                                                </td>

                                                {{-- COLUMNA CANTIDAD --}}
                                                <td class="px-4 py-3 align-top">
                                                    @if ($producto['cantidad'] > 0)
                                                        <input type="number"
                                                               wire:model="entradasRapidas.{{ $producto['id_detalle_compra'] }}.cantidad"
                                                               min="0"
                                                               max="{{ $producto['cantidad'] }}"
                                                               step="0.01"
                                                               class="w-full px-2 py-1.5 border rounded text-center focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-bold text-gray-700 @error('entradasRapidas.'.$producto['id_detalle_compra'].'.cantidad') border-red-500 @enderror"
                                                               placeholder="0">

                                                        {{-- 🔴 ERROR CANTIDAD --}}
                                                        @error('entradasRapidas.'.$producto['id_detalle_compra'].'.cantidad')
                                                        <span
                                                            class="text-[10px] text-red-500 leading-tight block mt-1">{{ $message }}</span>
                                                        @enderror
                                                    @else
                                                        <span
                                                            class="text-green-600 text-xs font-bold flex justify-center items-center gap-1">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                 viewBox="0 0 24 24"><path stroke-linecap="round"
                                                                                           stroke-linejoin="round"
                                                                                           stroke-width="2"
                                                                                           d="M5 13l4 4L19 7"/></svg>
                                                            Completado
                                                        </span>
                                                    @endif
                                                </td>

                                                {{-- COLUMNA UBICACIÓN --}}
                                                <td class="px-4 py-3 align-top">
                                                    @if ($producto['cantidad'] > 0)
                                                        <select
                                                            wire:model="entradasRapidas.{{ $producto['id_detalle_compra'] }}.ubicacion"
                                                            class="w-full text-xs border rounded px-2 py-1.5 focus:ring-2 focus:ring-blue-500 @error('entradasRapidas.'.$producto['id_detalle_compra'].'.ubicacion') border-red-500 @enderror">
                                                            <option value="almacen">Almacén</option>
                                                            <option value="mostrador">Mostrador</option>
                                                        </select>
                                                        {{-- 🔴 ERROR UBICACIÓN --}}
                                                        @error('entradasRapidas.'.$producto['id_detalle_compra'].'.ubicacion')
                                                        <span
                                                            class="text-[10px] text-red-500 leading-tight block mt-1">{{ $message }}</span>
                                                        @enderror
                                                    @endif
                                                </td>

                                                {{-- COLUMNA VENCIMIENTO --}}
                                                <td class="px-4 py-3 align-top">
                                                    @if ($producto['cantidad'] > 0)
                                                        {{-- ✅ LIMITACIÓN HTML: min="mañana" --}}
                                                        <input type="date"
                                                               wire:model="entradasRapidas.{{ $producto['id_detalle_compra'] }}.fecha_vencimiento"
                                                               min="{{ now()->addDay()->format('Y-m-d') }}"
                                                               class="w-full text-xs border rounded px-2 py-1.5 focus:ring-2 focus:ring-blue-500 @error('entradasRapidas.'.$producto['id_detalle_compra'].'.fecha_vencimiento') border-red-500 @enderror">

                                                        {{-- 🔴 ERROR VENCIMIENTO --}}
                                                        @error('entradasRapidas.'.$producto['id_detalle_compra'].'.fecha_vencimiento')
                                                        <span
                                                            class="text-[10px] text-red-500 leading-tight block mt-1">{{ $message }}</span>
                                                        @enderror
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="bg-blue-50/50 p-6 border-t border-gray-200">
                                    <h4 class="font-bold text-gray-700 mb-4 text-sm uppercase tracking-wide">Datos
                                        Generales de la Recepción</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de
                                                Recepción Global</label>

                                            {{-- ✅ LIMITACIÓN HTML: max="hoy" --}}
                                            <input type="date"
                                                   wire:model="lote.fecha_recepcion"
                                                   max="{{ now()->format('Y-m-d') }}"
                                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 @error('lote.fecha_recepcion') border-red-500 @enderror">

                                            {{-- 🔴 ERROR FECHA RECEPCIÓN --}}
                                            @error('lote.fecha_recepcion')
                                            <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones
                                                (Opcional)</label>
                                            <textarea
                                                wire:model="lote.observacion"
                                                rows="1"
                                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                                placeholder="Ej: Entrega conforme, Guía de Remisión N°..."></textarea>
                                        </div>
                                    </div>

                                    <div class="mt-6 flex justify-end">
                                        <button type="button" wire:click="registrarEntradasRapidas"
                                                class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-bold shadow-lg transform active:scale-95 transition-all flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                 stroke-linecap="round" stroke-linejoin="round">
                                                <path
                                                    d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                                                <polyline points="17 21 17 13 7 13 7 21"/>
                                                <polyline points="7 3 7 8 15 8"/>
                                            </svg>
                                            Confirmar Entrada al Inventario
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div
                            class="min-h-[500px] flex flex-col items-center justify-center p-8 text-center animate-fade-in-up">

                            {{-- Ilustración Principal (Camión) --}}
                            <div class="relative mb-6 group cursor-default">
                                {{-- Círculos decorativos de fondo --}}
                                <div
                                    class="absolute inset-0 bg-blue-100 rounded-full opacity-50 blur-xl group-hover:scale-110 transition-transform duration-700"></div>
                                <div
                                    class="relative bg-white p-6 rounded-full shadow-lg border border-blue-50 group-hover:-translate-y-1 transition-transform duration-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                         stroke-linejoin="round" class="text-blue-600">
                                        <path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/>
                                        <path d="M15 18H9"/>
                                        <path
                                            d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/>
                                        <circle cx="17" cy="18" r="2"/>
                                        <circle cx="7" cy="18" r="2"/>
                                        {{-- Caja pequeña encima --}}
                                        <rect x="6" y="8" width="6" height="4" rx="1"
                                              class="text-blue-400 fill-blue-50"/>
                                    </svg>
                                </div>

                                {{-- Badge flotante --}}
                                <div
                                    class="absolute -right-2 -top-2 bg-green-500 text-white p-1.5 rounded-full shadow-md animate-bounce">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                                         stroke-linejoin="round">
                                        <path d="M12 5v14"/>
                                        <path d="M5 12h14"/>
                                    </svg>
                                </div>
                            </div>

                            <h3 class="text-2xl font-bold text-slate-800 mb-3">
                                Esperando Orden de Compra
                            </h3>

                            <p class="text-slate-500 max-w-lg mx-auto mb-10 text-lg leading-relaxed">
                                Para registrar una entrada de inventario, primero debes seleccionar una
                                Orden de Compra aprobada desde el módulo de gestión.
                            </p>

                            {{-- Pasos Visuales --}}
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl w-full mb-10">
                                <div
                                    class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow flex flex-col items-center relative overflow-hidden">
                                    <div class="absolute top-0 left-0 w-1 h-full bg-blue-500"></div>
                                    <div class="mb-3 text-blue-500 bg-blue-50 p-2 rounded-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/>
                                            <path d="M3 6h18"/>
                                            <path d="M16 10a4 4 0 0 1-8 0"/>
                                        </svg>
                                    </div>
                                    <h4 class="font-bold text-slate-700">1. Módulo Compras</h4>
                                    <p class="text-xs text-slate-500 mt-1">Busca tu orden</p>
                                </div>

                                <div class="hidden md:flex items-center justify-center text-slate-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                         stroke-linejoin="round">
                                        <path d="M5 12h14"/>
                                        <path d="m12 5 7 7-7 7"/>
                                    </svg>
                                </div>

                                <div
                                    class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow flex flex-col items-center relative overflow-hidden">
                                    <div class="absolute top-0 left-0 w-1 h-full bg-green-500"></div>
                                    <div class="mb-3 text-green-500 bg-green-50 p-2 rounded-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                            <polyline points="22 4 12 14.01 9 11.01"/>
                                        </svg>
                                    </div>
                                    <h4 class="font-bold text-slate-700">2. Aprobar OC</h4>
                                    <p class="text-xs text-slate-500 mt-1">Valida la compra</p>
                                </div>

                                <div class="hidden md:flex items-center justify-center text-slate-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                         stroke-linejoin="round">
                                        <path d="M5 12h14"/>
                                        <path d="m12 5 7 7-7 7"/>
                                    </svg>
                                </div>

                                <div
                                    class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow flex flex-col items-center relative overflow-hidden">
                                    <div class="absolute top-0 left-0 w-1 h-full bg-orange-500"></div>
                                    <div class="mb-3 text-orange-500 bg-orange-50 p-2 rounded-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M16 16h6"/>
                                            <path d="M19 13v6"/>
                                            <path
                                                d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14"/>
                                            <path d="M16.5 9.4 7.55 4.24"/>
                                            <polyline points="3.29 7 12 12 20.71 7"/>
                                            <line x1="12" x2="12" y1="22" y2="12"/>
                                        </svg>
                                    </div>
                                    <h4 class="font-bold text-slate-700">3. Clic Recepcionar</h4>
                                    <p class="text-xs text-slate-500 mt-1">En la columna acciones</p>
                                </div>
                            </div>

                            {{-- Botón de Acción Principal --}}
                            <a href="{{ route('compras') }}" {{-- ASEGÚRATE QUE ESTA RUTA SEA CORRECTA --}}
                            class="inline-flex items-center justify-center px-8 py-4 text-base font-bold text-white transition-all duration-200 bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600 hover:shadow-lg hover:-translate-y-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 -ml-1" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Ir al listado de Compras
                            </a>
                        </div>
                    @endif
                </x-card>
            </div>
        </x-tab>

        <x-tab name="detalle">
            <div class=" bg-gray-50 rounded p-4 space-y-4">

                <div class="max-w-full" id="historial_entradas">
                    <x-card class="">
                        <p class="font-medium text-xl">Historial de entradas</p>
                        <p class="text-md">Todas las entradas registradas en el sistema</p>
                        <livewire:entradas-table/>
                    </x-card>
                </div>
            </div>
            <div x-data x-init="$watch('$wire.showModalDetalle', value => {
                if (value) {
                    document.body.classList.add('overflow-hidden');
                    document.body.style.paddingRight = '0px';
                } else {
                    document.body.classList.remove('overflow-hidden');
                    document.body.style.paddingRight = '';
                }
            })">
                <div>
                    @if ($showModalDetalle && $selectedEntrada)
                        <!-- Fondo con backdrop -->
                        <div class="fixed inset-0 flex items-center justify-center z-50">
                            <!-- Overlay -->
                            <div class="absolute inset-0 bg-black/60 transition-opacity duration-300"
                                 wire:click="$set('showModalDetalle', false)"></div>

                            <!-- Modal -->
                            <div
                                class="relative bg-white rounded-xl shadow-2xl w-full max-w-4xl mx-4 max-h-[90vh] overflow-hidden transform transition-all duration-300 scale-100 opacity-100"
                                x-transition:enter="ease-out duration-300"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="ease-in duration-200"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95">

                                <!-- Header -->
                                <div
                                    class="px-6 py-4 flex items-center justify-between border-b border-gray-200 bg-white">
                                    <div class="flex items-center gap-3">
                                        <div class="bg-green-100 p-2 rounded-lg">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                 viewBox="0 0 24 24" fill="none" stroke="green" stroke-width="2"
                                                 stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M5 12h14"/>
                                                <path d="M12 5v14"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-medium text-gray-700">Detalles de Entrada de
                                                Inventario</h3>
                                            <p class="text-gray-500 text-sm">
                                                Movimiento de inventario
                                                #{{ $selectedEntrada->id_inventario_movimiento }} -
                                                {{ $selectedEntrada->fecha_movimiento->format('d/m/Y H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                    <button wire:click="$set('showModalDetalle', false)"
                                            class="p-2 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                             viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="18" y1="6" x2="6" y2="18">
                                            </line>
                                            <line x1="6" y1="6" x2="18" y2="18">
                                            </line>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Body -->
                                <div class="overflow-y-auto p-6 bg-gray-50 max-h-[calc(85vh-120px)]">
                                    <!-- Información Principal -->
                                    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200 mb-6">
                                        <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-200">
                                            <div class="bg-blue-100 p-2 rounded-lg">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                     viewBox="0 0 24 24" fill="none" stroke="blue"
                                                     stroke-width="2" stroke-linecap="round"
                                                     stroke-linejoin="round">
                                                    <path
                                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                </svg>
                                            </div>
                                            <h4 class="font-bold text-gray-800 text-lg">Información del
                                                Producto</h4>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <!-- Producto -->
                                            <div class="flex flex-col">
                                                <label
                                                    class="text-sm font-medium text-gray-600 mb-1.5">Producto</label>
                                                <div
                                                    class="p-3 bg-gray-50 rounded-lg border border-gray-200 flex items-center gap-2">
                                                    <p class="text-gray-800 font-medium">
                                                        {{ $selectedEntrada->lote->producto->nombre_producto }}</p>
                                                    <p class="text-gray-500 text-sm">
                                                        ({{ $selectedEntrada->lote->producto->unidad->nombre_unidad ?? 'Sin unidad' }}
                                                        )
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Cantidad -->
                                            <div class="flex flex-col">
                                                <label class="text-sm font-medium text-gray-600 mb-1.5">Cantidad
                                                    Ingresada</label>
                                                <div class="p-3 bg-green-50 rounded-lg border border-green-200">
                                                    <p class="text-green-700 font-bold text-lg">
                                                        +{{ $selectedEntrada->cantidad_movimiento }}</p>
                                                </div>
                                            </div>

                                            <!-- Lote -->
                                            <div class="flex flex-col">
                                                <label class="text-sm font-medium text-gray-600 mb-1.5">Código
                                                    de
                                                    Lote</label>
                                                <div
                                                    class="p-3 bg-purple-50 rounded-lg border border-purple-200">
                                                    <p class="text-purple-700 font-mono font-medium">
                                                        {{ $selectedEntrada->lote->codigo_lote }}</p>
                                                </div>
                                            </div>

                                            <!-- Stock Resultante -->
                                            <div class="flex flex-col">
                                                <label class="text-sm font-medium text-gray-600 mb-1.5">Stock
                                                    Resultante</label>
                                                <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                                                    <p class="text-blue-700 font-medium">
                                                        {{ $selectedEntrada->stock_resultante }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Información de la Operación -->
                                    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200 mb-6">
                                        <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-200">
                                            <div class="bg-indigo-100 p-2 rounded-lg">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                     viewBox="0 0 24 24" fill="none" stroke="indigo"
                                                     stroke-width="2" stroke-linecap="round"
                                                     stroke-linejoin="round">
                                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                                    <circle cx="9" cy="7" r="4"/>
                                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                                </svg>
                                            </div>
                                            <h4 class="font-bold text-gray-800 text-lg">Información de la
                                                Operación
                                            </h4>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <!-- Ubicación -->
                                            <div class="flex flex-col">
                                                <label
                                                    class="text-sm font-medium text-gray-600 mb-1.5">Ubicación</label>
                                                <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                                                    <p class="text-blue-700 font-medium capitalize">
                                                        {{ $selectedEntrada->tipoUbicacion->nombre_tipo_ubicacion ?? $selectedEntrada->ubicacion }}
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Usuario -->
                                            <div class="flex flex-col">
                                                <label class="text-sm font-medium text-gray-600 mb-1.5">Registrado
                                                    por</label>
                                                <div
                                                    class="p-3 bg-indigo-50 rounded-lg border border-indigo-200">
                                                    <p class="text-indigo-700 font-medium">
                                                        {{ $selectedEntrada->trabajador?->persona?->user?->usuario ?? 'Automático' }}
                                                    </p>
                                                    <p class="text-indigo-500 text-sm mt-1">
                                                        {{ $selectedEntrada->trabajador?->persona?->nombre_completo ?? 'No disponible' }}
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Fecha -->
                                            <div class="flex flex-col">
                                                <label class="text-sm font-medium text-gray-600 mb-1.5">Fecha y
                                                    Hora</label>
                                                <div
                                                    class="p-3 bg-orange-50 rounded-lg border border-orange-200 flex items-center gap-2">
                                                    <p class="text-orange-700 font-medium">
                                                        {{ $selectedEntrada->fecha_movimiento->format('d/m/Y') }}</p>
                                                    <p class="text-orange-600 text-sm">
                                                        {{ $selectedEntrada->fecha_movimiento->format('H:i:s') }}</p>
                                                </div>
                                            </div>

                                            <!-- Tipo de Movimiento -->
                                            <div class="flex flex-col">
                                                <label class="text-sm font-medium text-gray-600 mb-1.5">Tipo de
                                                    Movimiento</label>
                                                <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                                                    <p class="text-gray-700 font-medium">
                                                        {{ $selectedEntrada->tipo_movimiento->nombre_tipo_movimiento ?? 'Entrada' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Información Adicional del Lote -->
                                    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
                                        <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-200">
                                            <div class="bg-yellow-100 p-2 rounded-lg">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                     viewBox="0 0 24 24" fill="none" stroke="orange"
                                                     stroke-width="2" stroke-linecap="round"
                                                     stroke-linejoin="round">
                                                    <rect x="3" y="3" width="18" height="18" rx="2"
                                                          ry="2"/>
                                                    <line x1="3" y1="9" x2="21"
                                                          y2="9"/>
                                                    <line x1="9" y1="21" x2="9"
                                                          y2="9"/>
                                                </svg>
                                            </div>
                                            <h4 class="font-bold text-gray-800 text-lg">Información Adicional
                                                del Lote
                                            </h4>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <!-- Precio de Compra -->
                                            <div class="flex flex-col">
                                                <label class="text-sm font-medium text-gray-600 mb-1.5">Precio
                                                    de
                                                    Compra</label>
                                                <div class="p-3 bg-green-50 rounded-lg border border-green-200">
                                                    <p class="text-green-700 font-medium">
                                                        S/
                                                        {{ number_format($selectedEntrada->lote->precio_compra, 2) }}
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Fecha de Recepción -->
                                            <div class="flex flex-col">
                                                <label class="text-sm font-medium text-gray-600 mb-1.5">Fecha de
                                                    Recepción</label>
                                                <div
                                                    class="p-3 bg-purple-50 rounded-lg border border-purple-200">
                                                    <p class="text-purple-700 font-medium">
                                                        @if ($selectedEntrada->lote->fecha_recepcion)
                                                            {{ \Carbon\Carbon::parse($selectedEntrada->lote->fecha_recepcion)->format('d/m/Y') }}
                                                        @else
                                                            <span
                                                                class="text-gray-500 italic">No especificada</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Fecha de Vencimiento -->
                                            <div class="flex flex-col">
                                                <label class="text-sm font-medium text-gray-600 mb-1.5">Fecha de
                                                    Vencimiento</label>
                                                <div
                                                    class="p-3 bg-{{ $selectedEntrada->lote->fecha_vencimiento && \Carbon\Carbon::parse($selectedEntrada->lote->fecha_vencimiento)->isPast() ? 'red' : 'blue' }}-50 rounded-lg border border-{{ $selectedEntrada->lote->fecha_vencimiento && \Carbon\Carbon::parse($selectedEntrada->lote->fecha_vencimiento)->isPast() ? 'red' : 'blue' }}-200">
                                                    <p
                                                        class="text-{{ $selectedEntrada->lote->fecha_vencimiento && \Carbon\Carbon::parse($selectedEntrada->lote->fecha_vencimiento)->isPast() ? 'red' : 'blue' }}-700 font-medium">
                                                        @if ($selectedEntrada->lote->fecha_vencimiento)
                                                            {{ \Carbon\Carbon::parse($selectedEntrada->lote->fecha_vencimiento)->format('d/m/Y') }}
                                                            @if (\Carbon\Carbon::parse($selectedEntrada->lote->fecha_vencimiento)->isPast())
                                                                <span
                                                                    class="text-xs text-red-600 ml-2">(Vencido)</span>
                                                            @endif
                                                        @else
                                                            <span
                                                                class="text-gray-500 italic">No especificada</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Estado del Lote -->
                                            <div class="flex flex-col">
                                                <label class="text-sm font-medium text-gray-600 mb-1.5">Estado
                                                    del
                                                    Lote</label>
                                                <div
                                                    class="p-3 bg-{{ $selectedEntrada->lote->estado == 'activo' ? 'green' : 'gray' }}-50 rounded-lg border border-{{ $selectedEntrada->lote->estado == 'activo' ? 'green' : 'gray' }}-200">
                                                    <p
                                                        class="text-{{ $selectedEntrada->lote->estado == 'activo' ? 'green' : 'gray' }}-700 font-medium capitalize">
                                                        {{ $selectedEntrada->lote->estado }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Observaciones -->
                                        <div class="mt-4">
                                            <label
                                                class="text-sm font-medium text-gray-600 mb-1.5">Observaciones</label>
                                            <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                                                @if (!empty($selectedEntrada->lote->observacion))
                                                    <p class="text-gray-700 leading-relaxed">
                                                        {{ $selectedEntrada->lote->observacion }}</p>
                                                @else
                                                    <p class="text-gray-500 italic">Sin observaciones</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Footer -->
                                <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
                                    <div class="flex justify-end gap-3">
                                        <button wire:click="$set('showModalDetalle', false)"
                                                class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-100 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2">
                                            Cerrar
                                        </button>

                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            </div>
        </x-tab>
    </x-tabs>
    <x-loader/>
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
        </script>
    @endpush
</x-panel>
