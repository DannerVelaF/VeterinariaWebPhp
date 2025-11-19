<x-panel title="Gestión de Compras" :breadcrumbs="[
    ['label' => 'Inicio', 'href' => '/', 'icon' => 'home'],
    ['label' => 'Compras', 'href' => '#'],
    ['label' => 'Registro de compras'],
]">
    <div class="grid grid-cols-4 gap-4 mb-4">
        <x-card>
            <div class="h-[100px] flex flex-col justify-between">
                <div class="flex justify-between items-center">
                    <p>Órdenes pendientes</p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="lucide lucide-clipboard-clock-icon lucide-clipboard-clock">
                        <path d="M16 14v2.2l1.6 1"/>
                        <path d="M16 4h2a2 2 0 0 1 2 2v.832"/>
                        <path d="M8 4H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h2"/>
                        <circle cx="16" cy="16" r="6"/>
                        <rect x="8" y="2" width="8" height="4" rx="1"/>
                    </svg>
                </div>
                <p class="font-medium text-3xl">{{ $cantOrdenesPendientes }}</p>
            </div>
        </x-card>
        <x-card>
            <div class="h-[100px] flex flex-col justify-between">
                <div class="flex justify-between items-center">
                    <p>Órdenes aprobadas</p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round" class="lucide lucide-check-icon lucide-check">
                        <path d="M20 6 9 17l-5-5"/>
                    </svg>
                </div>
                <p class="font-medium text-3xl">{{ $cantOrdenesAprobadas }}</p>
            </div>
        </x-card>
        <x-card>
            <div class="h-[100px] flex flex-col justify-between">
                <div class="flex justify-between items-center">
                    <p>Órdenes recibidas</p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round" class="lucide lucide-package-icon lucide-package">
                        <path
                            d="M11 21.73a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73z"/>
                        <path d="M12 22V12"/>
                        <polyline points="3.29 7 12 12 20.71 7"/>
                        <path d="m7.5 4.27 9 5.15"/>
                    </svg>
                </div>
                <p class="font-medium text-3xl">{{ $cantOrdenesRecibidas }}</p>
            </div>
        </x-card>
        <x-card>
            <div class="h-[100px] flex flex-col justify-between">
                <div class="flex justify-between items-center">
                    <p>Precio total compras recibidas</p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round" class="lucide lucide-dollar-sign-icon lucide-dollar-sign">
                        <line x1="12" x2="12" y1="2" y2="22"/>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                    </svg>
                </div>
                <p class="font-medium text-3xl">PEN{{ $precioCompraTotal }}</p>
            </div>
        </x-card>
    </div>
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
                 x-show="show" x-init="setTimeout(() => show = false, 4000)"
                 x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="opacity-0 transform translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-500"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform translate-y-2">
                {{ session('error') }}
            </div>
        @endif
        <div class="flex justify-between mb-5">
            <div>
                <p class="font-medium text-gray-600 text-xl">Órdenes de Compra</p>
                <p class="font-medium text-gray-600 text-sm">Gestiona las órdenes de compra a proveedores</p>
            </div>
            <div class="flex gap-2">
                <x-exports/>
                <button wire:click="openModal"
                        class="inline-flex items-center gap-2 px-4 py-2 h-10 bg-gray-500 hover:bg-gray-700 transition text-white rounded-lg font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round" class="lucide lucide-plus">
                        <path d="M5 12h14"/>
                        <path d="M12 5v14"/>
                    </svg>
                    Nueva orden
                </button>
            </div>
        </div>

        <livewire:compras-table/>

    </x-card>

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
                                        <option value="{{ $proveedor->id_proveedor }}">
                                            {{ $proveedor->nombre_proveedor }}
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
                            <div
                                class="max-h-[300px] overflow-y-auto space-y-2"> {{-- scroll al superar 4 filas aprox --}}
                                @foreach ($detalleCompra as $index => $detalle)
                                    <div class="grid grid-cols-12 gap-2 items-end">
                                        <!-- Producto -->
                                        <div class="col-span-5 flex flex-col">
                                            <label class="text-sm font-medium text-gray-600">Producto</label>
                                            <select wire:model="detalleCompra.{{ $index }}.id_producto"
                                                    class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200">
                                                <option value="">Seleccione un producto</option>
                                                @foreach ($productos as $producto)
                                                    <option value="{{ $producto->id_producto }}">
                                                        {{ $producto->nombre_producto }}
                                                        @if ($producto->unidad)
                                                            - {{ $producto->unidad->nombre_unidad }}
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('detalleCompra.' . $index . '.id_producto')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror

                                        </div>

                                        <!-- Cantidad -->
                                        <div class="col-span-3 flex flex-col">
                                            <label class="text-sm font-medium text-gray-600">Cantidad</label>
                                            <input type="number" min="1" step="1"
                                                   wire:model="detalleCompra.{{ $index }}.cantidad"
                                                   class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 border-gray-200">
                                            @error('detalleCompra.' . $index . '.cantidad')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror

                                        </div>

                                        <!-- Precio -->
                                        <div class="col-span-3 flex flex-col">
                                            <label class="text-sm font-medium text-gray-600">Precio compra</label>
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
                                                        <path d="M5 12h14"/>
                                                        <path d="M12 5v14"/>
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
                                                        <path d="M3 6h18"/>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/>
                                                        <path d="M10 11v6"/>
                                                        <path d="M14 11v6"/>
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
                            <textarea wire:model="compra.observacion" name="observacion" id="observacion" rows="4"
                                      maxlength="1000"
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
                    <div class="bg-white rounded-xl shadow-xl w-1/4 p-6">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-xl font-medium ">
                                    Detalles de orden de compra
                                </p>
                                <p class="text-sm font-medium ">
                                    Información completa de la orden {{ $compraSeleccionada->codigo }}
                                </p>
                            </div>
                            <button title="Descargar Orden de compra" wire:click="exportarPdfOrdenCompra"
                                    class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round"
                                     class="lucide lucide-file-text-icon lucide-file-text">
                                    <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/>
                                    <path d="M14 2v4a2 2 0 0 0 2 2h4"/>
                                    <path d="M10 9H8"/>
                                    <path d="M16 13H8"/>
                                    <path d="M16 17H8"/>
                                </svg>
                            </button>
                        </div>
                        <div class="grid grid-cols-2 gap-5 mt-5">
                            <div>
                                <label class="font-medium">Numero de orden</label>
                                <p class="text-gray-700">{{ $compraSeleccionada->codigo }}</p>
                            </div>
                            <div>
                                <label class="font-medium">Fecha de orden</label>
                                <p class="text-gray-700">{{ $compraSeleccionada->fecha_compra }}</p>
                            </div>
                            <div>
                                <label class="font-medium">Proveedor</label>
                                <p class="text-gray-700">{{ $compraSeleccionada->proveedor->nombre_proveedor }}</p>
                            </div>
                            <div>
                                <label class="font-medium">Nro Factura</label>
                                <p class="text-gray-700">{{ $compraSeleccionada->numero_factura }}</p>
                            </div>
                            <div>
                                <label class="font-medium">Estado</label>
                                <p class="text-gray-700 capitalize">
                                    {{ $compraSeleccionada->estadoCompra->nombre_estado_compra }}
                                </p>
                            </div>
                            <div>
                                <label class="font-medium">Fecha de compra</label>
                                <p class="text-gray-700">{{ $compraSeleccionada->fecha_compra }}</p>
                            </div>
                            <div>
                                <label class="font-medium">Fecha de registro</label>
                                <p class="text-gray-700">{{ $compraSeleccionada->fecha_registro }}</p>
                            </div>
                        </div>

                        <div class="mt-5">
                            <p class="font-medium text-md">Detalles productos</p>
                            <table>
                                <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-start font-medium">Producto</th>
                                    <th class="text-start font-medium">Cantidad</th>
                                    <th class="text-start font-medium">Precio</th>
                                    <th class="text-start font-medium">Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($compraSeleccionada->detalleCompra as $detalle)
                                    <tr class="border-b border-gray-200 text-sm">
                                        <td class="pb-3 pt-2 text-gray-700 font-medium">
                                            {{ $detalle->producto->nombre_producto }}
                                        </td>
                                        <td class="text-gray-700 font-medium">{{ $detalle->cantidad }}</td>
                                        <td class="text-gray-700 font-medium">s/{{ $detalle->precio_unitario }}
                                        </td>
                                        <td class="text-gray-700 font-medium">s/{{ $detalle->sub_total }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                        <div class="mt-5 space-y-1">
                            <p class="text-sm text-end">
                                Subtotal: S/ {{ number_format($compraSeleccionada->total, 2) }}
                            </p>
                            <p class="text-sm text-end">
                                IGV ({{ $IGV * 100 }}%):
                                S/ {{ number_format($compraSeleccionada->total * $IGV, 2) }}
                            </p>
                            <p class="font-medium text-xl text-end">
                                Total:
                                S/
                                {{ number_format($compraSeleccionada->total + $compraSeleccionada->total * $IGV, 2) }}
                            </p>
                        </div>
                        <div>
                            <label>Observaciones</label>
                            <p class="text-gray-700">{{ $compraSeleccionada->observacion }}</p>
                        </div>

                        <div class="flex justify-end mt-5">
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
    <x-loader/>
</x-panel>
