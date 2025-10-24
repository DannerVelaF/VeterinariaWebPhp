<x-panel title="Gestión de productos" :breadcrumbs="[
    ['label' => 'Inicio', 'href' => '/', 'icon' => 'home'],
    ['label' => 'Mantenimiento', 'href' => '#'],
    ['label' => 'Gestión de productos'],
]">
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
                        <div class="flex flex-col">
                            <label for="unidad" class="font-bold mb-1">Precio unitario <span
                                    class="text-red-500">*</span></label>
                            <input type="text" id="precio_unitario" name="precio_unitario" maxlength="255"
                                class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 @error('producto.precio_unitario') border-red-500 @enderror"
                                placeholder="Precio unitario" wire:model="producto.precio_unitario">
                            @error('producto.precio_unitario')
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
                                <option value="{{ $cate->id_categoria_producto }}">
                                    {{ $cate->nombre_categoria_producto }}</option>
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
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z">
                                    </path>
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
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data="{ show: true }" x-show="show"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">

            <!-- Overlay -->
            <div class="absolute inset-0 bg-black opacity-50" wire:click="$set('modalEditar', false)"></div>

            <!-- Contenido del modal mejorado -->
            <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-2xl z-10 overflow-y-auto max-h-[95vh]">
                <!-- Header del modal -->
                <div class=" p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div>
                                <h2 class="text-xl font-bold">Editar Producto</h2>
                                <p class="text-sm opacity-90">Actualiza la información del producto</p>
                            </div>
                        </div>
                        <button wire:click="$set('modalEditar', false)"
                            class=" transition-colors p-1 rounded-full  hover:bg-opacity-10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Formulario de edición -->
                <form wire:submit.prevent="actualizarProducto" class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Columna Izquierda -->
                        <div class="space-y-4">
                            <!-- Información Básica -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="font-bold text-gray-700 mb-3 flex items-center text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-blue-600"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Información Básica
                                </h3>

                                <div class="space-y-4">
                                    <!-- Nombre del Producto -->
                                    <div class="flex flex-col">
                                        <label class="font-semibold text-gray-700 mb-2 text-sm">
                                            Nombre del Producto <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" wire:model="productoEditar.nombre_producto"
                                            class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                                        @error('productoEditar.nombre_producto')
                                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Precio Unitario -->
                                    <div class="flex flex-col">
                                        <label class="font-semibold text-gray-700 mb-2 text-sm">
                                            Precio Unitario <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <span
                                                class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">S/</span>
                                            <input type="text" wire:model="productoEditar.precio_unitario"
                                                class="border border-gray-300 rounded-lg px-3 py-2 pl-8 w-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                                                placeholder="0.00">
                                        </div>
                                        @error('productoEditar.precio_unitario')
                                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Estado -->
                                    <div class="flex flex-col">
                                        <label class="font-semibold text-gray-700 mb-2 text-sm">
                                            Estado <span class="text-red-500">*</span>
                                        </label>
                                        <select wire:model="productoEditar.estado"
                                            class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                                            <option value="activo">Activo</option>
                                            <option value="inactivo">Inactivo</option>
                                        </select>
                                        @error('productoEditar.estado')
                                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Categorías y Unidades -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="font-bold text-gray-700 mb-3 flex items-center text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-green-600"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path
                                            d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
                                    </svg>
                                    Clasificación
                                </h3>

                                <div class="space-y-4">
                                    <!-- Categoría -->
                                    <div class="flex flex-col">
                                        <label class="font-semibold text-gray-700 mb-2 text-sm">Categoría</label>
                                        <select wire:model="productoEditar.id_categoria_producto"
                                            class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                                            <option value="">-- Seleccione categoría --</option>
                                            @foreach ($categorias as $cate)
                                                <option value="{{ $cate->id_categoria_producto }}">
                                                    {{ $cate->nombre_categoria_producto }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('productoEditar.id_categoria_producto')
                                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Unidad -->
                                    <div class="flex flex-col">
                                        <label class="font-semibold text-gray-700 mb-2 text-sm">Unidad</label>
                                        <select wire:model="productoEditar.id_unidad"
                                            class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                                            <option value="">-- Seleccione una unidad --</option>
                                            @foreach ($unidades as $unidad)
                                                <option value="{{ $unidad->id_unidad }}">{{ $unidad->nombre_unidad }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('productoEditar.id_unidad')
                                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Columna Derecha -->
                        <div class="space-y-4">
                            <!-- Proveedor -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="font-bold text-gray-700 mb-3 flex items-center text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-purple-600"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                        <path fill-rule="evenodd"
                                            d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Proveedor
                                </h3>

                                <div class="flex flex-col">
                                    <select wire:model="productoEditar.id_proveedor"
                                        class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                                        <option value="">-- Seleccione proveedor --</option>
                                        @foreach ($proveedores as $prov)
                                            <option value="{{ $prov->id_proveedor }}">{{ $prov->nombre_proveedor }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('productoEditar.id_proveedor')
                                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Descripción -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="font-bold text-gray-700 mb-3 flex items-center text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-orange-600"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Descripción
                                </h3>

                                <div class="flex flex-col">
                                    <textarea wire:model="productoEditar.descripcion" rows="4"
                                        class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors resize-none"
                                        placeholder="Describe las características del producto..."></textarea>
                                    <div class="text-right text-xs text-gray-500 mt-1">
                                        {{ strlen($productoEditar['descripcion'] ?? '') }}/1000 caracteres
                                    </div>
                                </div>
                            </div>

                            <!-- Imagen del Producto -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="font-bold text-gray-700 mb-3 flex items-center text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-pink-600"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Imagen del Producto
                                </h3>

                                <div class="space-y-4">
                                    <!-- Selector de archivo -->
                                    <div class="flex items-center justify-between">
                                        <label
                                            class="bg-blue-600 text-white px-4 py-2 rounded-lg cursor-pointer hover:bg-blue-700 transition-colors text-xs font-semibold flex items-center"
                                            for="imagenEditar">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Seleccionar Imagen
                                        </label>

                                        <span class="text-gray-600 text-xs flex-1 ml-4 truncate" wire:loading.remove
                                            wire:target="imagenEditar">
                                            {{ $imagenEditar ? $imagenEditar->getClientOriginalName() : (isset($productoEditar['ruta_imagen']) ? 'Imagen actual seleccionada' : 'No se ha seleccionado ninguna') }}
                                        </span>

                                        <!-- Loader -->
                                        <div wire:loading wire:target="imagenEditar"
                                            class="flex items-center space-x-2">
                                            <svg class="animate-spin h-4 w-4 text-blue-600"
                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                    stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8v8H4z"></path>
                                            </svg>
                                            <span class="text-gray-600 text-xs">Cargando...</span>
                                        </div>
                                    </div>

                                    <input type="file" id="imagenEditar" wire:model="imagenEditar"
                                        accept="image/*" class="hidden">

                                    <!-- Previsualización -->
                                    <div class="flex justify-center">
                                        @if ($imagenEditar)
                                            <img src="{{ $imagenEditar->temporaryUrl() }}"
                                                class="w-32 h-32 object-cover rounded-lg border-2 border-blue-300 shadow-sm">
                                        @elseif(isset($productoEditar['ruta_imagen']))
                                            <img src="{{ asset('storage/' . $productoEditar['ruta_imagen']) }}"
                                                class="w-32 h-32 object-cover rounded-lg border-2 border-gray-300 shadow-sm">
                                        @else
                                            <div
                                                class="w-32 h-32 bg-gray-200 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>

                                    @error('imagenEditar')
                                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                        <button type="button" wire:click="$set('modalEditar', false)"
                            class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            Actualizar Producto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

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

    <div wire:loading wire:target="guardar | actualizarProducto"
        @if ($imagenProducto) style="display:none;" @endif>
        <x-loader />
    </div>
</x-panel>
