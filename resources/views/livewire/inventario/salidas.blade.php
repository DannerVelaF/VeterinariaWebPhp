<div class="">

    <x-tabs :tabs="['registro' => 'Registar salida', 'detalle' => 'Listado de salidas']" default="registro">
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
            <div class="bg-gray-50 rounded p-4 space-y-4 ">

                <x-card class="h-auto max-w-full ">
                    <div class="flex flex-col gap-2">
                        <p class="font-medium text-xl flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="red" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-minus">
                                <path d="M5 12h14" />
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
                                        Stock:
                                        {{ $producto->stock_actual }}
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
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-package">
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
                                            class="lucide lucide-warehouse">
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
                                            class="lucide lucide-shopping-cart">
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
                                <label for="cantidad" class="font-medium">Cantidad <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="cantidad" min="0" step="0.01"
                                    class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-red-300 border-gray-200"
                                    placeholder="0.00" wire:model="cantidad">
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
                                @foreach ($motivosPredefinidos as $motivoOption)
                                    <option value="{{ $motivoOption }}">{{ $motivoOption }}</option>
                                @endforeach
                            </select>

                            @error('motivo')
                                <p class="text-red-500 text-xs italic mt-1">
                                    {{ $message }}
                                </p>
                            @enderror

                            {{-- Campo para motivo personalizado --}}
                            @if ($motivo === 'Otro')
                                <div class="mt-2">
                                    <label for="motivo_personalizado" class="font-medium">Especifique el motivo
                                        <span class="text-red-500">*</span></label>
                                    <textarea wire:model.lazy="motivo_personalizado" id="motivo_personalizado" rows="3" maxlength="1000"
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
                <livewire:salidas-table />
            </x-card>
        </x-tab>
    </x-tabs>

    <div x-data x-init="$watch('$wire.showModal', value => {
        if (value) { document.body.classList.add('overflow-hidden') } else { document.body.classList.remove('overflow-hidden') }
    })">
        <div>
            @if ($showModal && $selectedSalida)
                <div class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
                    <div class="bg-white rounded-xl shadow-xl w-1/2 p-6 max-h-[90vh] overflow-y-auto">
                        <h2 class="text-xl font-bold mb-4">
                            Detalles de salida #{{ $selectedSalida->id_movimiento }}
                        </h2>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p><strong>Producto:</strong></p>
                                <p class="text-gray-700">{{ $selectedSalida->lote->producto->nombre_producto }}
                                </p>
                            </div>
                            <div>
                                <p><strong>Cantidad:</strong></p>
                                <p class="text-red-600 font-medium">-{{ $selectedSalida->cantidad_movimiento }}
                                </p>
                            </div>
                            <div>
                                <p><strong>Ubicación:</strong></p>
                                <p class="capitalize">{{ $selectedSalida->ubicacion }}</p>
                            </div>
                            <div>
                                <p><strong>Usuario:</strong></p>
                                <p>{{ $selectedSalida->trabajador->persona->user->usuario }}</p>
                            </div>
                            <div class="col-span-2">
                                <p><strong>Fecha:</strong></p>
                                <p>{{ $selectedSalida->fecha_movimiento->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="col-span-2">
                                <p><strong>Lote:</strong></p>
                                <p>{{ $selectedSalida->lote->codigo_lote }}</p>
                            </div>
                            <div class="col-span-2">
                                <p><strong>Motivo:</strong></p>
                                <p class="text-gray-700 bg-gray-50 p-3 rounded-md mt-1">
                                    @if (!empty($selectedSalida->motivo))
                                        {{ $selectedSalida->motivo }}
                                    @else
                                        <span class="text-gray-400 italic">Sin motivo especificado</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="flex justify-end mt-4">
                            <button wire:click="$set('showModal', false)"
                                class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                                Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <x-loader />
</div>
