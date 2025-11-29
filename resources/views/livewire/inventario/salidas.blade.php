<div class="">

    <x-tabs :tabs="['registro' => 'Registar salida', 'detalle' => 'Listado de salidas']" default="registro"
            :breadcrumbs="[
        ['label' => 'Inicio', 'href' => '/', 'icon' => 'home'],
        ['label' => 'Inventario', 'href' => '#'],
        ['label' => 'Salidas'],
    ]">
        <x-tab name="registro">
            {{-- ELIMINAR LOS MENSAJES DE SESSION FLASH --}}
            {{-- @if (session()->has('success')) y @if (session()->has('error')) --}}

            <div class="bg-gray-50 rounded p-4 space-y-4 ">

                <x-card class="h-auto max-w-full ">
                    <div class="flex flex-col gap-2">
                        <p class="font-medium text-xl flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="red" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="lucide lucide-minus">
                                <path d="M5 12h14"/>
                            </svg>
                            Registrar Salida
                        </p>
                        <p class="text-md">Registra la salida de stock de tus productos.</p>
                    </div>

                    <form wire:submit.prevent="registrarSalida" class="grid grid-cols-1 gap-4 text-sm mt-5">
                        <div class="flex flex-col gap-2">
                            <label for="producto" class="font-medium">Seleccionar Producto <span
                                    class="text-red-500">*</span></label>
                            <select wire:model.live="id_producto" id="producto"
                                    class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-red-300">
                                <option value="">Seleccione un producto...</option>
                                @foreach ($productos as $producto)
                                    <option value="{{ $producto->id_producto }}">
                                        {{ $producto->nombre_producto }} ({{ $producto->unidad->nombre_unidad }}) -
                                        Stock: {{ $producto->stock_actual }}
                                    </option>
                                @endforeach
                            </select>

                            @error('id_producto')
                            <p class="text-red-500 text-xs italic mt-1">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        @if ($productoSeleccionado)
                            <div class="bg-gray-50 rounded-md p-3 mb-2 border-gray-100 text-gray-600">
                                <p class="font-medium ">Stock actual del producto</p>
                                <div class="grid grid-cols-3 gap-4 mt-2 text-sm">
                                    <div class="flex gap-2">
                                        <!-- icono -->
                                        <span class="font-medium">Total: </span>
                                        <span>{{ $stockActual['total'] ?? 0 }}</span>
                                    </div>
                                    <div class="flex gap-2 items-center">
                                        <!-- icono -->
                                        <span class="font-medium">Almacén: </span>
                                        <span>{{ $stockActual['almacen'] ?? 0 }}</span>
                                    </div>
                                    <div class="flex gap-2">
                                        <!-- icono -->
                                        <span class="font-medium">Mostrador: </span>
                                        <span>{{ $stockActual['mostrador'] ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="flex gap-5 justify-between">
                            <div class="flex flex-col gap-2 w-full">
                                <label for="cantidad" class="font-medium">Cantidad <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="cantidad" min="1" step="1"
                                       class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-red-300 border-gray-200"
                                       placeholder="Cantidad de producto a salir" wire:model="cantidad">
                                @error('cantidad')
                                <p class="text-red-500 text-xs italic mt-1">
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>
                            <div class="flex flex-col gap-2 w-full">
                                <label for="ubicacion" class="font-medium">Ubicación <span
                                        class="text-red-500">*</span></label>
                                <select name="ubicacion" id="ubicacion" wire:model="ubicacion"
                                        class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-red-300 border-gray-200 ">
                                    <option value="almacen">Almacén</option>
                                    <option value="mostrador">Mostrador</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="motivo" class="font-medium">Motivo de salida <span
                                    class="text-red-500">*</span></label>

                            <select wire:model.live="motivo" id="motivo"
                                    class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-red-300 border-gray-200">
                                <option value="">Seleccione un motivo...</option>
                                @foreach ($motivosSalida as $motivoOption)
                                    <option value="{{ $motivoOption->id_tipo_movimiento }}">
                                        {{ $motivoOption->nombre_tipo_movimiento }}
                                    </option>
                                @endforeach
                                <option value="otro">Otro (Especifique)</option>
                            </select>

                            @error('motivo')
                            <p class="text-red-500 text-xs italic mt-1">
                                {{ $message }}
                            </p>
                            @enderror

                            @if ($showMotivoPersonalizado)
                                <div class="mt-2">
                                    <label for="motivo_personalizado" class="font-medium">Especifique el motivo
                                        <span class="text-red-500">*</span></label>
                                    <textarea wire:model.lazy="motivo_personalizado" id="motivo_personalizado" rows="3"
                                              maxlength="1000"
                                              class="w-full border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-red-300 border-gray-200 resize-none"
                                              placeholder="Describa el motivo de la salida..."></textarea>
                                    @error('motivo_personalizado')
                                    <p class="text-red-500 text-xs italic mt-1">
                                        {{ $message }}
                                    </p>
                                    @enderror
                                    <div class="text-right text-xs text-gray-500 mt-1">
                                        {{ strlen($motivo_personalizado) }}/1000 caracteres
                                    </div>
                                </div>
                            @endif
                        </div>

                        <button type="submit"
                                class="w-full p-2 text-white rounded-md transition bg-red-500 hover:bg-red-600 ease-linear">
                            Registrar salida
                        </button>
                    </form>
                </x-card>
            </div>
        </x-tab>
        <x-tab name="detalle">
            <x-card class="">
                <p class="font-medium text-xl">Historial de salidas</p>
                <p class="text-md">Todas las salidas registradas en el sistema</p>
                <livewire:salidas-table/>
            </x-card>
        </x-tab>
    </x-tabs>

    <div x-data x-init="$watch('$wire.showModal', value => {
        if (value) {
            document.body.classList.add('overflow-hidden');
            document.body.style.paddingRight = '0px';
        } else {
            document.body.classList.remove('overflow-hidden');
            document.body.style.paddingRight = '';
        }
    })">
        <div>
            @if ($showModal && $selectedSalida)
                <!-- Fondo con backdrop -->
                <div class="fixed inset-0 flex items-center justify-center z-50">
                    <!-- Overlay -->
                    <div class="absolute inset-0 bg-black/60 transition-opacity duration-300"
                         wire:click="$set('showModal', false)"></div>

                    <!-- Modal -->
                    <div
                        class="relative bg-white rounded-xl shadow-2xl w-full max-w-4xl mx-4 max-h-[90vh] overflow-hidden transform transition-all duration-300 scale-100 opacity-100"
                        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">

                        <!-- Header -->
                        <div class="px-6 py-4 flex items-center justify-between border-b border-gray-200 bg-white">
                            <div class="flex items-center gap-3">
                                <div class="bg-red-100 p-2 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                         viewBox="0 0 24 24" fill="none" stroke="red" stroke-width="2"
                                         stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M5 12h14"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-medium text-gray-700">Detalles de Salida de Inventario</h3>
                                    <p class="text-gray-500 text-sm">
                                        Movimiento de inventario #{{ $selectedSalida->id_inventario_movimiento }} -
                                        {{ $selectedSalida->fecha_movimiento->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                            </div>
                            <button wire:click="$set('showModal', false)"
                                    class="p-2 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
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
                                             viewBox="0 0 24 24" fill="none" stroke="blue" stroke-width="2"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    </div>
                                    <h4 class="font-bold text-gray-800 text-lg">Información del Producto</h4>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Producto -->
                                    <div class="flex flex-col">
                                        <label class="text-sm font-medium text-gray-600 mb-1.5">Producto</label>
                                        <div
                                            class="p-3 bg-gray-50 rounded-lg border border-gray-200 flex items-center gap-2">
                                            <p class="text-gray-800 font-medium">
                                                {{ $selectedSalida->lote->producto->nombre_producto }}</p>
                                            <p class="text-gray-500 text-sm mt-1">
                                                ({{ $selectedSalida->lote->producto->unidad->nombre_unidad ?? 'Sin unidad' }}
                                                )
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Cantidad -->
                                    <div class="flex flex-col">
                                        <label class="text-sm font-medium text-gray-600 mb-1.5">Cantidad
                                            Retirada</label>
                                        <div class="p-3 bg-red-50 rounded-lg border border-red-200">
                                            <p class="text-red-700 font-bold text-lg">
                                                -{{ $selectedSalida->cantidad_movimiento }}</p>
                                        </div>
                                    </div>

                                    <!-- Lote -->
                                    <div class="flex flex-col">
                                        <label class="text-sm font-medium text-gray-600 mb-1.5">Código de Lote</label>
                                        <div class="p-3 bg-purple-50 rounded-lg border border-purple-200">
                                            <p class="text-purple-700 font-mono font-medium">
                                                {{ $selectedSalida->lote->codigo_lote }}</p>
                                        </div>
                                    </div>

                                    <!-- Stock Resultante -->
                                    <div class="flex flex-col">
                                        <label class="text-sm font-medium text-gray-600 mb-1.5">Stock
                                            Resultante</label>
                                        <div class="p-3 bg-green-50 rounded-lg border border-green-200">
                                            <p class="text-green-700 font-medium">
                                                {{ $selectedSalida->stock_resultante }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Información de la Operación -->
                            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200 mb-6">
                                <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-200">
                                    <div class="bg-green-100 p-2 rounded-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                             viewBox="0 0 24 24" fill="none" stroke="green" stroke-width="2"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                            <circle cx="9" cy="7" r="4"/>
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                        </svg>
                                    </div>
                                    <h4 class="font-bold text-gray-800 text-lg">Información de la Operación</h4>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Ubicación -->
                                    <div class="flex flex-col">
                                        <label class="text-sm font-medium text-gray-600 mb-1.5">Ubicación</label>
                                        <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                                            <p class="text-blue-700 font-medium capitalize">
                                                {{ $selectedSalida->tipoUbicacion->nombre_tipo_ubicacion }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Usuario -->
                                    <div class="flex flex-col">
                                        <label class="text-sm font-medium text-gray-600 mb-1.5">Registrado por</label>
                                        <div class="p-3 bg-indigo-50 rounded-lg border border-indigo-200">
                                            <p class="text-indigo-700 font-medium">
                                                {{ $selectedEntrada->trabajador?->persona?->user?->usuario ?? 'Automático' }}
                                            </p>
                                            <p class="text-indigo-500 text-sm mt-1">
                                                {{ $selectedSalida->trabajador->persona->nombre_completo ?? '' }}</p>
                                        </div>
                                    </div>

                                    <!-- Fecha -->
                                    <div class="flex flex-col">
                                        <label class="text-sm font-medium text-gray-600 mb-1.5">Fecha y Hora</label>
                                        <div
                                            class="p-3 bg-orange-50 rounded-lg border border-orange-200 flex items-center gap-2">
                                            <p class="text-orange-700 font-medium">
                                                {{ $selectedSalida->fecha_movimiento->format('d/m/Y') }}</p>
                                            <p class="text-orange-600 text-sm">
                                                {{ $selectedSalida->fecha_movimiento->format('H:i:s') }}</p>
                                        </div>
                                    </div>

                                    <!-- Tipo de Movimiento -->
                                    <div class="flex flex-col">
                                        <label class="text-sm font-medium text-gray-600 mb-1.5">Tipo de
                                            Movimiento</label>
                                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                                            <p class="text-gray-700 font-medium">
                                                {{ $selectedSalida->tipo_movimiento->nombre_tipo_movimiento ?? 'Salida' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Motivo de Salida -->
                            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
                                <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-200">
                                    <div class="bg-yellow-100 p-2 rounded-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                             viewBox="0 0 24 24" fill="none" stroke="orange" stroke-width="2"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10"/>
                                            <line x1="12" y1="8" x2="12" y2="12"/>
                                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                                        </svg>
                                    </div>
                                    <h4 class="font-bold text-gray-800 text-lg">Motivo de Salida</h4>
                                </div>

                                <div class="flex flex-col">
                                    @if (!empty($selectedSalida->motivo))
                                        <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                                            <p class="text-gray-700 leading-relaxed">{{ $selectedSalida->motivo }}</p>
                                        </div>
                                    @else
                                        <div
                                            class="p-4 bg-gray-50 rounded-lg border border-gray-200 flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                 viewBox="0 0 24 24" fill="none" stroke="gray" stroke-width="2"
                                                 stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                                                <circle cx="12" cy="12" r="10"/>
                                                <line x1="12" y1="8" x2="12" y2="12"/>
                                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                                            </svg>
                                            <span class="text-gray-500 italic">Sin motivo especificado</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
                            <div class="flex justify-end gap-3">
                                <button wire:click="$set('showModal', false)"
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

    <x-loader target="registrarSalida, updatedIdProducto"/>

    {{-- AGREGAR ESTE SCRIPT PARA MANEJAR EL STOCK --}}
    @script
    <script>
        $wire.on('stockUpdated', () => {
            // Forzar actualización del stock cuando se cambia de producto
            console.log('Stock actualizado');
        });
    </script>
    @endscript

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

            // AGREGAR ESTE LISTENER PARA ACTUALIZAR EL STOCK EN TIEMPO REAL
            document.addEventListener('livewire:init', () => {
                Livewire.on('stockUpdated', () => {
                    // El stock se actualiza automáticamente gracias a Livewire
                    console.log('Stock actualizado automáticamente');
                });
            });
        </script>
    @endpush
</div>
