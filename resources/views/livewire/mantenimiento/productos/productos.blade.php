<x-panel title="Gestión de Productos">
    <x-tabs :tabs="['listado' => '📋 Detalle productos registrados', 'registro' => '➕ Registrar nuevo producto']" default="listado">
        <!-- TAB 1: LISTADO -->
        <x-tab name="listado">
            <div class="p-4">

                <livewire:producto-table />
            </div>
            <div x-data="{ open: false, descripcion: '' }" x-on:open-modal.window="open = true; descripcion = $event.detail.descripcion">

                <!-- Modal -->
                <div x-show="open" class="fixed inset-0 flex items-center justify-center">
                    <div class="bg-white p-6 rounded-lg w-1/2">
                        <h2 class="text-lg font-bold mb-4">Descripción completa</h2>
                        <p x-text="descripcion"></p>
                        <button class="mt-4 bg-red-500 text-white px-3 py-1 rounded"
                            @click="open = false">Cerrar</button>
                    </div>
                </div>
            </div>

        </x-tab>

        <!-- TAB 2: REGISTRO -->
        <x-tab name="registro">
            <!-- Mensajes de éxito y error -->
            @if (session()->has('success'))
                <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="p-3 bg-gray-50 rounded">
                <form wire:submit.prevent="guardar" class="grid grid-cols-2 gap-4 text-xs">
                    <!-- ====== INFORMACIÓN DEL PRODUCTO ====== -->
                    <div class="col-span-2">
                        <p class="font-bold text-gray-700 mb-3">📦 Información del Producto</p>
                    </div>

                    <div class="flex flex-col">
                        <label for="nombre_producto" class="font-bold mb-1">Nombre del Producto <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="nombre_producto" name="nombre_producto" maxlength="255"
                            class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 @error('producto.nombre_producto') border-red-500 @enderror"
                            placeholder="Nombre del producto" wire:model="producto.nombre_producto">
                        @error('producto.nombre_producto')
                            <p class="text-red-500 text-xs italic mt-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="flex  justify-between">
                        <div class="flex flex-col">
                            <label for="precio_unitario" class="font-bold mb-1">Precio Unitario <span
                                    class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-2 top-1 text-gray-500">S/</span>
                                <input type="number" id="precio_unitario" name="precio_unitario" step="0.01"
                                    min="0.01" max="999999999.99"
                                    class="border rounded px-7 py-1 focus:outline-none focus:ring focus:ring-blue-300 @error('producto.precio_unitario') border-red-500 @enderror"
                                    placeholder="0.00" wire:model="producto.precio_unitario">
                            </div>
                            @error('producto.precio_unitario')
                                <p class="text-red-500 text-xs italic mt-1">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="flex flex-col">
                            <label for="stock" class="font-bold mb-1">Stock inicial</label>
                            <div class="relative">
                                <span class="absolute right-2 top-1 text-gray-500">unidades</span>
                                <input type="text" id="stock" name="stock" min="0" max="9999"
                                    class="border rounded px-4 py-1 focus:outline-none focus:ring focus:ring-blue-300 @error('producto.stock') border-red-500 @enderror"
                                    placeholder="0" wire:model.live="producto.stock">
                            </div>
                            @error('producto.stock')
                                <p class="text-red-500 text-xs italic mt-1">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex flex-col">
                        <label for="categoria" class="font-bold mb-1">Categoría <span
                                class="text-red-500">*</span></label>
                        <select wire:model="producto.id_categoria_producto" id="categoria"
                            class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 @error('producto.id_categoria_producto') border-red-500 @enderror">
                            <option value="">-- Seleccione una categoría --</option>
                            @foreach ($categorias as $cate)
                                <option value="{{ $cate->id }}">{{ $cate->nombre }}</option>
                            @endforeach
                        </select>
                        @error('producto.id_categoria_producto')
                            <p class="text-red-500 text-xs italic mt-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label for="proveedor" class="font-bold mb-1">Proveedor <span
                                class="text-red-500">*</span></label>
                        <select wire:model="producto.id_proveedor" id="proveedor"
                            class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 @error('producto.id_proveedor') border-red-500 @enderror">
                            <option value="">-- Seleccione un proveedor --</option>
                            @foreach ($proveedores as $prov)
                                <option value="{{ $prov->id }}">{{ $prov->nombre }}</option>
                            @endforeach
                        </select>
                        @error('producto.id_proveedor')
                            <p class="text-red-500 text-xs italic mt-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="flex flex-col col-span-2">
                        <label for="descripcion" class="font-bold mb-1">Descripción del Producto</label>
                        <textarea id="descripcion" name="descripcion" rows="4" maxlength="1000"
                            class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 @error('producto.descripcion') border-red-500 @enderror"
                            placeholder="Descripción detallada del producto..." wire:model.live="producto.descripcion"></textarea>
                        @error('producto.descripcion')
                            <p class="text-red-500 text-xs italic mt-1">
                                {{ $message }}
                            </p>
                        @enderror
                        <div class="text-right text-xs text-gray-500 mt-1">
                            {{ strlen($producto['descripcion']) }}/1000 caracteres
                        </div>
                    </div>

                    <!-- Información adicional -->
                    <div class="col-span-2 mt-4">
                        <div class="bg-blue-50 border border-blue-200 rounded p-3">
                            <p class="font-bold text-blue-800 text-xs mb-2">ℹ️ Información Adicional</p>
                            <div class="grid grid-cols-2 gap-4 text-xs">
                                <div>
                                    <span class="font-semibold">Stock inicial:</span> {{ $producto['stock'] ?? 0 }}
                                    unidades
                                </div>
                                <div>
                                    <span class="font-semibold">Código de barras: </span>Se generarà automáticamente
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="col-span-2 flex justify-end mt-6 space-x-2">
                        <button type="button" wire:click="resetForm"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded text-xs font-bold transition-colors">
                            Limpiar Formulario
                        </button>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-xs font-bold transition-colors">
                            Registrar Producto
                        </button>
                    </div>
                </form>
            </div>
        </x-tab>
    </x-tabs>

</x-panel>
