<div>
    <p>Compras</p>
    <x-tabs :tabs="['registro' => 'Registrar orden de compra']" default="registro">
        <x-tab name="registro">
            <x-card>
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
                <div class="flex justify-between">
                    <div>
                        <p class="font-medium text-gray-600 text-xl">Órdenes de Compra</p>
                        <p class="font-medium text-gray-600 text-sm">Gestiona las órdenes de compra a proveedores</p>
                    </div>
                    <button wire:click="openModal"
                        class="bg-gray-500 hover:bg-gray-700 transition ease-in-out text-white rounded-md px-4 font-medium flex items-center gap-2 text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-plus-icon lucide-plus">
                            <path d="M5 12h14" />
                            <path d="M12 5v14" />
                        </svg>
                        Nueva orden
                    </button>
                </div>
                <livewire:compras-table />

            </x-card>
        </x-tab>

    </x-tabs>

    <div>
        @if ($showModal)
            <div class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
                <div class="bg-white rounded-xl shadow-xl w-1/3 p-6 space-y-4">
                    <div>
                        <h2 class="text-xl font-medium mb-1">Crear Nueva Orden de Compra</h2>
                        <p class="text-sm font-medium">Completa la información de la orden y agrega los productos</p>
                    </div>
                    <form action="" class="space-y-4 w-full" wire:submit.prevent="guardar">
                        <div class="flex gap-2">
                            <div class="flex gap-2 flex-col w-1/2">
                                <label>Número de orden</label>
                                <input wire:model="codigoOrden" type="text" readonly
                                    class=" border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200">
                            </div>
                            <div class="flex gap-2 flex-col w-1/2">
                                <label>Proveedor</label>
                                <select wire:model.live="proveedorSeleccionado" id="proveedor"
                                    class=" border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200 ">
                                    <option value="">Seleccione un proveedor</option>
                                    @foreach ($proveedores as $proveedor)
                                        <option value="{{ $proveedor->id }}">
                                            {{ $proveedor->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('proveedorSeleccionado')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror

                            </div>
                        </div>
                        <div class="flex gap-2">
                            <div class="flex gap-2 flex-col w-1/2">
                                <label for="fechaCompra">Numero factura</label>
                                <input wire:model="compra.numero_factura" type="text" id="fechaCompra"
                                    name="fechaCompra"
                                    class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200">
                                @error('compra.numero_factura')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror

                            </div>
                            <div class="flex gap-2 flex-col w-1/2">
                                <label for="fechaCompra">Fecha de compra</label>
                                <input wire:model="compra.fecha_compra" type="date" id="fechaCompra"
                                    name="fechaCompra"
                                    class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200">
                                @error('compra.fecha_compra')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror

                            </div>
                        </div>
                        <div class="space-y-4">
                            <p class="font-medium text-xl ">Productos</p>
                            <div class="max-h-[300px] overflow-y-auto space-y-2"> {{-- scroll al superar 4 filas aprox --}}
                                @foreach ($detalleCompra as $index => $detalle)
                                    <div class="grid grid-cols-12 gap-2 items-end">
                                        <!-- Producto -->
                                        <div class="col-span-5 flex flex-col">
                                            <label class="text-sm font-medium text-gray-600">Producto</label>
                                            <select wire:model="detalleCompra.{{ $index }}.producto_id"
                                                class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200">
                                                <option value="">Seleccione un producto</option>
                                                @foreach ($productos as $producto)
                                                    <option value="{{ $producto->id }}">{{ $producto->nombre_producto }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('detalleCompra.' . $index . '.producto_id')
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror

                                        </div>

                                        <!-- Cantidad -->
                                        <div class="col-span-3 flex flex-col">
                                            <label class="text-sm font-medium text-gray-600">Cantidad</label>
                                            <input type="number" min="1" step="0.01"
                                                wire:model="detalleCompra.{{ $index }}.cantidad"
                                                class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200">
                                            @error('detalleCompra.' . $index . '.cantidad')
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror

                                        </div>

                                        <!-- Precio -->
                                        <div class="col-span-3 flex flex-col">
                                            <label class="text-sm font-medium text-gray-600">Precio unitario</label>
                                            <input type="number" min="0.01" step="0.01"
                                                wire:model="detalleCompra.{{ $index }}.precio_unitario"
                                                class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200">
                                            @error('detalleCompra.' . $index . '.precio_unitario')
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror

                                        </div>

                                        <!-- Botón -->
                                        <div class="col-span-1 flex justify-center">
                                            @if ($loop->last)
                                                {{-- Último item -> botón agregar --}}
                                                <button type="button" wire:click="agregarDetalle"
                                                    class="bg-green-500 hover:bg-green-700 text-white rounded-md p-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20"
                                                        height="20" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round" class="lucide lucide-plus">
                                                        <path d="M5 12h14" />
                                                        <path d="M12 5v14" />
                                                    </svg>
                                                </button>
                                            @else
                                                {{-- Filas anteriores -> botón eliminar --}}
                                                <button type="button"
                                                    wire:click="eliminarDetalle({{ $index }})"
                                                    class="bg-red-500 hover:bg-red-700 text-white rounded-md p-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20"
                                                        height="20" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round" class="lucide lucide-trash">
                                                        <path d="M3 6h18" />
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" />
                                                        <path d="M10 11v6" />
                                                        <path d="M14 11v6" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div>

                            </div>
                        </div>
                        <div class="flex gap-2 flex-col">
                            <label for="observacion">Observaciones</label>
                            <textarea wire:model="compra.observacion" name="observacion" id="observacion" rows="4" maxlength="1000"
                                class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200 resize-none"
                                placeholder="Observaciones..."></textarea>
                        </div>
                        <div class="flex justify-end mt-4 gap-4 items-center">

                            <button wire:click="closeModal"
                                class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                                Cancelar
                            </button>
                            <button class="bg-green-500 hover:bg-green-700 text-white px-4 py-2 rounded-md">
                                Crear orden
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        @endif

    </div>
    <div x-data x-init="$watch('$wire.showModal', value => {
        if (value) { document.body.classList.add('overflow-hidden') } else { document.body.classList.remove('overflow-hidden') }
    })">

        <div>
            @if ($showModalDetalle && $compraSeleccionada)
                <div class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
                    <div class="bg-white rounded-xl shadow-xl w-1/2 p-6">
                        <h2 class="text-xl font-bold mb-4">
                            Detalles de compra #{{ $compraSeleccionada->codigo }}
                        </h2>
                        <button wire:click="aprobarCompra"
                            class="bg-green-500 hover:bg-green-700 text-white px-4 py-2 rounded-md">
                            Aprobar
                        </button>
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
</div>
