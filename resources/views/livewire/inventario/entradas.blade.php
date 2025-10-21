<x-panel title="Gestión de entradas de inventario" :breadcrumbs="[
    ['label' => 'Inicio', 'href' => '/', 'icon' => 'home'],
    ['label' => 'Inventario', 'href' => '#'],
    ['label' => 'Entradas'],
]">
    <x-tabs :tabs="['registro' => 'Registar entradas', 'detalle' => 'Listado de entradas']" default="registro">
        <x-tab name="registro">
            @if (session()->has('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                    x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 transform translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-500"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform translate-y-2"
                    class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif
            @if (session()->has('error'))
                <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded" x-data="{ show: true }"
                    x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 transform translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-500"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform translate-y-2">
                    {{ session('error') }}
                </div>
            @endif
            <div class="max-w-full h-full">
                <x-card>
                    <div class="flex flex-col gap-2">
                        <p class="font-medium text-xl flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="blue" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-plus-icon lucide-plus">
                                <path d="M5 12h14" />
                                <path d="M12 5v14" />
                            </svg>
                            Registrar Entrada
                        </p>
                        <p class="text-md">Añade stock a tus productos existentes.</p>
                    </div>

                    <form wire:submit.prevent="registrar" class="grid grid-cols-1 gap-4 text-sm mt-5">
                        <div class="flex gap-2 items-center">
                            <input type="text" wire:model="ordenCompra" placeholder="Ingrese código de OC"
                                class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 w-full">
                            <button type="button" wire:click="buscarOrdenCompra"
                                class="bg-blue-500 hover:bg-blue-700 text-white rounded px-4 py-1">
                                Buscar
                            </button>
                        </div>
                        <div class="flex gap-5 ">
                            <div class="flex flex-col gap-2">
                                <label for="producto" class="font-medium">Seleccionar un producto <span
                                        class="text-red-500">*</span></label>
                                <select wire:model.live="id_producto" id="producto" name="producto_id"
                                    class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300">
                                    <option value="">Seleccione un producto</option>
                                    @foreach ($productosOC as $producto)
                                        <option value="{{ $producto['id_detalle_compra'] }}">
                                            {{ $producto['nombre'] }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('producto_id')
                                    <p class="text-red-500 text-xs italic mt-1">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="flex flex-col gap-2 w-full">
                                <label for="proveedor" class="font-medium">Proveedor</label>
                                <input type="text" id="proveedor"
                                    class="border rounded px-2 py-1 border-gray-200 w-full"
                                    value="{{ $proveedorOC?->nombre_proveedor }}" readonly>
                            </div>
                        </div>

                        @if ($productoSeleccionado)
                            <div class="bg-gray-50 rounded-md p-3 mb-2 border-gray-100 text-gray-600">
                                <p class="font-medium ">Stock actual</p>
                                <div class="grid grid-cols-3 gap-4 mt-2 text-sm">
                                    <div class="flex gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-package-icon lucide-package">
                                            <path
                                                d="M11 21.73a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73z" />
                                            <path d="M12 22V12" />
                                            <polyline points="3.29 7 12 12 20.71 7" />
                                            <path d="m7.5 4.27 9 5.15" />
                                        </svg>
                                        <span class="font-medium">Total: </span>
                                        <span>{{ $this->stockActual['total'] }}</span>
                                    </div>
                                    <div class="flex gap-2 items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-warehouse-icon lucide-warehouse">
                                            <path d="M18 21V10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1v11" />
                                            <path
                                                d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V8a2 2 0 0 1 1.132-1.803l7.95-3.974a2 2 0 0 1 1.837 0l7.948 3.974A2 2 0 0 1 22 8z" />
                                            <path d="M6 13h12" />
                                            <path d="M6 17h12" />
                                        </svg>
                                        <span class="font-medium">Almacén: </span>
                                        <span>{{ $this->stockActual['almacen'] }}</span>
                                    </div>
                                    <div class="flex gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-shopping-cart-icon lucide-shopping-cart">
                                            <circle cx="8" cy="21" r="1" />
                                            <circle cx="19" cy="21" r="1" />
                                            <path
                                                d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12" />
                                        </svg>
                                        <span class="font-medium">Mostrador: </span>
                                        <span>{{ $this->stockActual['mostrador'] }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="flex gap-5 justify-between">
                            <div class="flex flex-col gap-2 w-full">
                                <label for="cantidad" class="font-medium">Cantidad <span
                                        class="text-red-500">*</span>
                                </label>
                                <input type="number" id="cantidad" min="0" step="0.01"
                                    class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200"
                                    placeholder="0.00" wire:model="lote.cantidad_total">
                                @error('lote.cantidad_total')
                                    <p class="text-red-500 text-xs italic mt-1">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                            <div class="flex flex-col gap-2 w-full">
                                <label for="ubicacion" class="font-medium">Ubicacion <span
                                        class="text-red-500">*</span></label>
                                <select name="ubicacion" id="ubicacion" wire:model="ubicacion"
                                    class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200 ">
                                    <option value="almacen">Almacen</option>
                                    <option value="mostrador">Mostrador</option>
                                </select>
                            </div>

                        </div>
                        <div class="flex gap-5 justify-between">
                            <div class="flex flex-col gap-2 w-full">
                                <label for="fecha_recepcion" class="font-medium">Fecha de recepción <span
                                        class="text-red-500">*</span></label>
                                <input type="date" id="fecha_recepcion" name="fecha_recepcion"
                                    class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200"
                                    wire:model="lote.fecha_recepcion">
                                @error('lote.fecha_recepcion')
                                    <p class="text-red-500 text-xs italic mt-1">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                            <div class="flex flex-col gap-2 w-full">
                                <label for="fecha_vencimiento" class="font-medium">Fecha de vencimiento</label>
                                <input type="date" id="fecha_vencimiento" name="fecha_vencimiento"
                                    class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200"
                                    wire:model="lote.fecha_vencimiento">
                                @error('lote.fecha_vencimiento')
                                    <p class="text-red-500 text-xs italic mt-1">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label for="observacion" class="font-medium">Motivo de entrada</label>
                            <textarea name="observacion" id="observacion" rows="4" maxlength="1000"
                                class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200 resize-none"
                                placeholder="Observaciones de entrada..." wire:model.lazy="lote.observacion"></textarea>
                            @error('lote.observacion')
                                <p class="text-red-500 text-xs italic mt-1">
                                    {{ $message }}
                                </p>
                            @enderror
                            <div class="text-right text-xs text-gray-500 mt-1">
                                {{ strlen($lote['observacion']) }}/1000 caracteres
                            </div>
                        </div>
                        <button type="submit"
                            class="w-full p-2 text-white rounded-md transition bg-green-500 hover:bg-green-600 ease-linear">
                            Registrar entrada
                        </button>
                    </form>
                </x-card>

            </div>
        </x-tab>
        <x-tab name="detalle">
            <div class=" bg-gray-50 rounded p-4 space-y-4">

                <div class="max-w-full" id="historial_entradas">
                    <x-card class="">
                        <p class="font-medium text-xl">Historial de entradas</p>
                        <p class="text-md">Todas las entradas registradas en el sistema</p>
                        <livewire:entradas-table />
                    </x-card>
                </div>
            </div>
            <div x-data x-init="$watch('$wire.showModal', value => {
                if (value) { document.body.classList.add('overflow-hidden') } else { document.body.classList.remove('overflow-hidden') }
            })">

                <div>
                    @if ($showModalDetalle && $selectedEntrada)
                        <div class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
                            <div class="bg-white rounded-xl shadow-xl w-1/2 p-6">
                                <h2 class="text-xl font-bold mb-4">
                                    Detalles de entrada #{{ $selectedEntrada->id }}
                                </h2>

                                <p><strong>Producto:</strong> {{ $selectedEntrada->lote->producto->nombre_producto }}
                                </p>
                                <p><strong>Cantidad:</strong> {{ $selectedEntrada->cantidad_movimiento }}</p>
                                <p><strong>Ubicación:</strong> {{ $selectedEntrada->ubicacion }}</p>
                                <p><strong>Usuario:</strong>
                                    {{ $selectedEntrada->trabajador->persona->user->username }}</p>
                                <p><strong>Fecha:</strong> {{ $selectedEntrada->fecha_movimiento }}</p>
                                <p><strong>Lote:</strong> {{ $selectedEntrada->lote->codigo_lote }}</p>
                                <p><strong>Precio de compra:</strong> {{ $selectedEntrada->lote->precio_compra }}</p>
                                <p><strong>Observaciones:</strong> {{ $selectedEntrada->lote->observacion }}</p>
                                <div class="flex justify-end mt-4">
                                    <button wire:click="$set('showModalDetalle', false)"
                                        class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                                        Cerrar
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </x-tab>
    </x-tabs>
    <x-loader />
</x-panel>
