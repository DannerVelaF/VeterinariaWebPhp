<x-panel title="Gestión de Productos">
    <x-tabs :tabs="['listado' => '📋 Detalle productos registrados', 'registro' => '➕ Registrar nuevo producto']" default="listado">
        <!-- TAB 1: LISTADO -->
        <x-tab name="listado">
            <div class="p-4">

                <livewire:producto-table />
            </div>

        </x-tab>

        <!-- TAB 2: REGISTRO -->
        <x-tab name="registro">
            <!-- Mensajes de éxito y error -->
            @if (session()->has('success'))
                <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded" x-data="{ show: true }"
                    x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 transform translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-500"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform translate-y-2">
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
                            <label for="unidad" class="font-bold mb-1">Unidad <span
                                    class="text-red-500">*</span></label>
                            <select wire:model="producto.id_unidad" id="unidad"
                                class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 @error('producto.id_unidad') border-red-500 @enderror">
                                <option value="">-- Seleccione una unidad --</option>
                                @foreach ($unidades as $unidad)
                                    <option value="{{ $unidad->id_unidad }}">{{ $unidad->nombre_unidad }}</option>
                                @endforeach
                            </select>
                            @error('producto.id_unidad')
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
                                <option value="{{ $cate->id_categoria_producto }}">{{ $cate->nombre_categoria }}
                                </option>
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
                                <option value="{{ $prov->id_proveedor }}">{{ $prov->nombre_proveedor }}</option>
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
                    <div class="flex flex-col col-span-2">
                        <label class="font-bold mb-1">Imagen del Producto</label>

                        <!-- Contenedor estilizado del input -->
                        <div class="flex items-center space-x-4">
                            <label
                                class="bg-blue-600 text-white px-4 py-2 rounded cursor-pointer hover:bg-blue-700 transition-colors text-xs font-semibold"
                                for="imagen_producto">
                                Seleccionar Imagen
                            </label>
                            <span class="text-gray-600 text-xs" wire:loading.remove wire:target="imagenProducto">
                                {{ $imagenProducto ? $imagenProducto->getClientOriginalName() : 'No se ha seleccionado ninguna' }}
                            </span>

                            <!-- Loader mientras se sube la imagen -->
                            <div wire:loading wire:target="imagenProducto" class="flex items-center space-x-2">
                                <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                <span class="text-gray-600 text-xs">Cargando...</span>
                            </div>
                        </div>

                        <input type="file" id="imagen_producto" wire:model="imagenProducto" accept="image/*"
                            class="hidden">

                        @error('imagenProducto')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror

                        <!-- Previsualización -->
                        @if ($imagenProducto)
                            <img src="{{ $imagenProducto->temporaryUrl() }}" alt="Vista previa"
                                class="mt-2 w-32 h-32 object-cover rounded border border-gray-300 bg-gray-100">
                        @endif
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
    @if ($modalEditar)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <!-- Overlay -->
            <div class="absolute inset-0 bg-black opacity-50" wire:click="$set('modalEditar', false)"></div>

            <!-- Contenido del modal -->
            <div class="relative bg-white rounded-md p-6 w-1/3 z-10 overflow-y-auto max-h-[90vh]">
                <h2 class="text-lg font-bold mb-4 flex gap-2"><svg xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-pencil-icon lucide-pencil">
                        <path
                            d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z" />
                        <path d="m15 5 4 4" />
                    </svg>Editar Producto</h2>

                <form wire:submit.prevent="actualizarProducto" class="grid grid-cols-2 gap-4 text-xs">
                    <div class="flex flex-col col-span-2">
                        <label class="font-bold mb-1">Nombre</label>
                        <input type="text" wire:model="productoEditar.nombre_producto"
                            class="border rounded px-2 py-1 focus:ring focus:ring-blue-300">
                    </div>

                    <div class="flex flex-col">
                        <label class="font-bold mb-1">Unidad</label>
                        <select wire:model="productoEditar.id_unidad"
                            class="border rounded px-2 py-1 focus:ring focus:ring-blue-300">
                            <option value="">-- Seleccione una unidad --</option>
                            @foreach ($unidades as $unidad)
                                <option value="{{ $unidad->id_unidad }}">{{ $unidad->nombre_unidad }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col">
                        <label class="font-bold mb-1">Categoría</label>
                        <select wire:model="productoEditar.id_categoria_producto"
                            class="border rounded px-2 py-1 focus:ring focus:ring-blue-300">
                            <option value="">-- Seleccione categoría --</option>
                            @foreach ($categorias as $cate)
                                <option value="{{ $cate->id_categoria_producto }}">{{ $cate->nombre_categoria }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col">
                        <label class="font-bold mb-1">Proveedor</label>
                        <select wire:model="productoEditar.id_proveedor"
                            class="border rounded px-2 py-1 focus:ring focus:ring-blue-300">
                            <option value="">-- Seleccione proveedor --</option>
                            @foreach ($proveedores as $prov)
                                <option value="{{ $prov->id_proveedor }}">{{ $prov->nombre_proveedor }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col col-span-2">
                        <label class="font-bold mb-1">Descripción</label>
                        <textarea wire:model="productoEditar.descripcion" rows="3"
                            class="border rounded px-2 py-1 focus:ring focus:ring-blue-300"></textarea>
                    </div>

                    <div class="flex flex-col col-span-2">
                        <label class="font-bold mb-1">Imagen del Producto</label>

                        <div class="flex items-center space-x-4">
                            <label
                                class="bg-blue-600 text-white px-4 py-2 rounded cursor-pointer hover:bg-blue-700 transition-colors text-xs font-semibold"
                                for="imagenEditar">
                                Seleccionar Imagen
                            </label>
                            <span class="text-gray-600 text-xs" wire:loading.remove wire:target="imagenEditar">
                                {{ $imagenEditar ? $imagenEditar->getClientOriginalName() : (isset($productoEditar['ruta_imagen']) ? 'Imagen actual' : 'No se ha seleccionado ninguna') }}
                            </span>

                            <!-- Loader mientras se sube la imagen -->
                            <div wire:loading wire:target="imagenEditar" class="flex items-center space-x-2">
                                <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                <span class="text-gray-600 text-xs">Cargando...</span>
                            </div>
                        </div>

                        <input type="file" id="imagenEditar" wire:model="imagenEditar" accept="image/*"
                            class="hidden">

                        <!-- Previsualización -->
                        @if ($imagenEditar)
                            <img src="{{ $imagenEditar->temporaryUrl() }}"
                                class="mt-2 w-32 h-32 object-cover rounded border border-gray-300 bg-gray-100">
                        @elseif(isset($productoEditar['ruta_imagen']))
                            <img src="{{ asset('storage/' . $productoEditar['ruta_imagen']) }}"
                                class="mt-2 w-32 h-32 object-cover rounded border border-gray-300 bg-gray-100">
                        @endif

                        @error('imagenEditar')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-span-2 flex justify-end space-x-2 mt-4">
                        <button type="button" wire:click="$set('modalEditar', false)"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded text-xs font-bold">Cancelar</button>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-xs font-bold">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <div wire:loading wire:target="guardar | actualizarProducto"
        @if ($imagenProducto) style="display:none;" @endif>
        <x-loader />
    </div>
</x-panel>
