<x-panel title="Gestión de proveedores" :breadcrumbs="[
    ['label' => 'Inicio', 'href' => '/', 'icon' => 'home'],
    ['label' => 'Productos', 'href' => route('mantenimiento.productos'), 'icon' => 'ellipsis-horizontal'],
    ['label' => 'Gestión de proveedores', 'href' => route('mantenimiento.productos.proveedores')],
]">
    <x-tabs :tabs="['listado' => '📋 Detalle proveedores registrados', 'registro' => '➕ Registrar nuevo proveedor']" default="listado">
        <!-- TAB 1: LISTADO -->
        <x-tab name="listado">
            <div class="p-4">
                <livewire:proveedor-table />
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

            <div class="p-4 bg-gray-50 rounded-lg">
                <form wire:submit.prevent="guardar" class="grid grid-cols-1 lg:grid-cols-2 gap-6 text-sm">

                    <!-- ====== COLUMNA IZQUIERDA ====== -->
                    <div>
                        <!-- INFORMACIÓN DEL PROVEEDOR -->
                        <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm">
                            <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-200">
                                <div class="bg-blue-100 p-2 rounded-lg">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <h3 class="font-bold text-gray-800 text-lg">Información del Proveedor</h3>
                            </div>

                            <div class="space-y-4">
                                <!-- RUC -->
                                <div class="grid grid-cols-1 gap-4">
                                    <div class="flex flex-col">
                                        <label for="ruc" class="font-semibold text-gray-700 mb-2">RUC <span
                                                class="text-red-500">*</span></label>
                                        <div class="flex gap-2">
                                            <input type="text" id="ruc" name="ruc" maxlength="11"
                                                class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('proveedor.ruc') border-red-500 @enderror"
                                                placeholder="RUC (11 dígitos)" wire:model.change="proveedor.ruc">
                                            <button type="button" wire:click="buscarRuc"
                                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors whitespace-nowrap">
                                                Buscar RUC
                                            </button>
                                        </div>
                                        @error('proveedor.ruc')
                                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Nombre -->
                                <div class="flex flex-col">
                                    <label for="nombre" class="font-semibold text-gray-700 mb-2">Nombre <span
                                            class="text-red-500">*</span></label>
                                    <input @readonly($proveedorEncontrado) type="text" id="nombre" name="nombre"
                                        class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('proveedor.nombre_proveedor') border-red-500 @enderror"
                                        placeholder="Nombre del proveedor"
                                        wire:model.change="proveedor.nombre_proveedor">
                                    @error('proveedor.nombre_proveedor')
                                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Teléfonos -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="flex flex-col">
                                        <label for="telefono_contacto" class="font-semibold text-gray-700 mb-2">Teléfono
                                            Principal</label>
                                        <input type="number" id="telefono_contacto" name="telefono_contacto"
                                            maxlength="9"
                                            class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('proveedor.telefono_contacto') border-red-500 @enderror"
                                            placeholder="9 dígitos" wire:model.change="proveedor.telefono_contacto">
                                        @error('proveedor.telefono_contacto')
                                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="flex flex-col">
                                        <label for="telefono_secundario"
                                            class="font-semibold text-gray-700 mb-2">Teléfono Secundario</label>
                                        <input type="number" id="telefono_secundario" maxlength="9"
                                            class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('proveedor.telefono_secundario') border-red-500 @enderror"
                                            placeholder="9 dígitos" wire:model.change="proveedor.telefono_secundario">
                                        @error('proveedor.telefono_secundario')
                                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Correos -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="flex flex-col">
                                        <label for="correo_electronico_empresa"
                                            class="font-semibold text-gray-700 mb-2">Correo Empresa</label>
                                        <input type="email" id="correo_electronico_empresa" maxlength="255"
                                            class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('proveedor.correo_electronico_empresa') border-red-500 @enderror"
                                            placeholder="correo@empresa.com"
                                            wire:model.change="proveedor.correo_electronico_empresa">
                                        @error('proveedor.correo_electronico_empresa')
                                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="flex flex-col">
                                        <label for="correo_electronico_encargado"
                                            class="font-semibold text-gray-700 mb-2">Correo Encargado</label>
                                        <input type="email" id="correo_electronico_encargado" maxlength="255"
                                            class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('proveedor.correo_electronico_encargado') border-red-500 @enderror"
                                            placeholder="encargado@empresa.com"
                                            wire:model.change="proveedor.correo_electronico_encargado">
                                        @error('proveedor.correo_electronico_encargado')
                                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- País -->
                                <div class="flex flex-col">
                                    <label for="pais" class="font-semibold text-gray-700 mb-2">País <span
                                            class="text-red-500">*</span></label>
                                    <select id="pais" name="pais"
                                        class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('proveedor.pais') border-red-500 @enderror"
                                        wire:model.change="proveedor.pais">
                                        <option value="">Seleccione un país...</option>
                                        <option value="peru">Perú</option>
                                        <option value="colombia">Colombia</option>
                                    </select>
                                    @error('proveedor.pais')
                                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- ====== COLUMNA DERECHA ====== -->
                    <div class="space-y-6">
                        <!-- PRODUCTOS ASOCIADOS -->
                        <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm">
                            <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-200">
                                <div class="bg-green-100 p-2 rounded-lg">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                                <h3 class="font-bold text-gray-800 text-lg">Productos que Provee</h3>
                            </div>

                            <div class="flex flex-col">
                                <label class="font-semibold text-gray-700 mb-2">Productos <span
                                        class="text-red-500">*</span></label>

                                <div x-data="productosSelect(@js($productos_seleccionados))" x-init="init()" class="relative">

                                    <!-- Input de búsqueda y selección -->
                                    <div class="border border-gray-300 rounded-lg px-3 py-2 focus-within:ring-2 focus-within:ring-green-500 focus-within:border-transparent transition-colors min-h-[44px] cursor-pointer bg-white"
                                        @click="open = !open">

                                        <!-- Chips de productos seleccionados -->
                                        <div class="flex flex-wrap gap-1 max-h-20 overflow-y-auto">
                                            <template x-for="productoId in selectedIds" :key="productoId">
                                                <div
                                                    class="bg-green-600 text-white px-3 py-1 rounded-full text-xs flex items-center gap-1">
                                                    <span x-text="getProductoName(productoId)"
                                                        class="max-w-32 truncate"></span>
                                                    <button type="button" @click.stop="removeProducto(productoId)"
                                                        class="hover:bg-green-700 rounded-full w-4 h-4 flex items-center justify-center text-xs">
                                                        ×
                                                    </button>
                                                </div>
                                            </template>

                                            <!-- Placeholder cuando no hay selección -->
                                            <span x-show="selectedIds.length === 0" class="text-gray-500 text-sm">
                                                Seleccione uno o varios productos...
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Dropdown con posición mejorada -->
                                    <div x-show="open" x-transition @click.outside="open = false"
                                        class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-xl max-h-48 overflow-y-auto"
                                        x-cloak>

                                        <!-- Búsqueda -->
                                        <div class="p-3 border-b border-gray-200 sticky top-0 bg-white">
                                            <input type="text" x-model="search" @input="filterProductos()"
                                                placeholder="Buscar productos..."
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                                                x-ref="searchInput">
                                        </div>

                                        <!-- Lista de productos -->
                                        <div class="py-1">
                                            <template x-for="producto in filteredProductos"
                                                :key="producto.id_producto">
                                                <div class="flex items-center px-3 py-2 hover:bg-green-50 cursor-pointer border-b border-gray-100 last:border-b-0"
                                                    @click="toggleProducto(producto.id_producto)">

                                                    <input type="checkbox"
                                                        :checked="selectedIds.includes(producto.id_producto)"
                                                        class="rounded border-gray-300 text-green-600 focus:ring-green-500 mr-3">

                                                    <span x-text="producto.nombre_producto"
                                                        :class="{
                                                            'font-semibold text-green-700': selectedIds.includes(
                                                                producto
                                                                .id_producto)
                                                        }"
                                                        class="text-sm">
                                                    </span>
                                                </div>
                                            </template>

                                            <!-- Mensaje cuando no hay resultados -->
                                            <div x-show="filteredProductos.length === 0"
                                                class="px-3 py-4 text-gray-500 text-sm text-center">
                                                No se encontraron productos
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Input oculto para Livewire -->
                                    <input type="hidden" x-model="selectedIdsString"
                                        wire:model="productos_seleccionados">
                                </div>

                                @error('productos_seleccionados')
                                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <!-- DIRECCIÓN -->
                        <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm">
                            <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-200">
                                <div class="bg-purple-100 p-2 rounded-lg">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <h3 class="font-bold text-gray-800 text-lg">Dirección</h3>
                            </div>

                            <div class="grid grid-cols-1 gap-4">
                                <!-- Tipo de Calle y Nombre -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="flex flex-col">
                                        <label for="tipo_calle" class="font-semibold text-gray-700 mb-2">Tipo de
                                            Calle</label>
                                        <select id="tipo_calle" name="tipo_calle"
                                            class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('direccion.tipo_calle') border-red-500 @enderror"
                                            wire:model.change="direccion.tipo_calle">
                                            <option value="">Seleccione tipo...</option>
                                            @foreach ($tipos_calle as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                        @error('direccion.tipo_calle')
                                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="flex flex-col">
                                        <label for="nombre_calle" class="font-semibold text-gray-700 mb-2">Nombre de
                                            Calle</label>
                                        <input type="text" id="nombre_calle" name="nombre_calle" maxlength="255"
                                            class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('direccion.nombre_calle') border-red-500 @enderror"
                                            placeholder="Ej: Los Olivos" wire:model.change="direccion.nombre_calle">
                                        @error('direccion.nombre_calle')
                                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Número y Zona -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="flex flex-col">
                                        <label for="numero" class="font-semibold text-gray-700 mb-2">Número</label>
                                        <input type="text" id="numero" name="numero" maxlength="10"
                                            class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('direccion.numero') border-red-500 @enderror"
                                            placeholder="Ej: 123" wire:model.change="direccion.numero">
                                        @error('direccion.numero')
                                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="flex flex-col">
                                        <label for="zona" class="font-semibold text-gray-700 mb-2">Zona</label>
                                        <input type="text" id="zona" name="zona" maxlength="255"
                                            class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('direccion.zona') border-red-500 @enderror"
                                            placeholder="Urbanización, sector" wire:model.change="direccion.zona">
                                        @error('direccion.zona')
                                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Código Postal y Referencia -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="flex flex-col">
                                        <label for="codigo_postal" class="font-semibold text-gray-700 mb-2">Código
                                            Postal</label>
                                        <input type="text" id="codigo_postal" name="codigo_postal" maxlength="10"
                                            class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('direccion.codigo_postal') border-red-500 @enderror"
                                            placeholder="Ej: 15084" wire:model.change="direccion.codigo_postal">
                                        @error('direccion.codigo_postal')
                                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="flex flex-col">
                                        <label for="referencia"
                                            class="font-semibold text-gray-700 mb-2">Referencia</label>
                                        <input type="text" id="referencia" name="referencia" maxlength="255"
                                            class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('direccion.referencia') border-red-500 @enderror"
                                            placeholder="Punto de referencia"
                                            wire:model.change="direccion.referencia">
                                        @error('direccion.referencia')
                                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- UBIGEO -->
                    <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm col-span-2">
                        <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-200">
                            <div class="bg-orange-100 p-2 rounded-lg">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-gray-800 text-lg">Ubicación Geográfica</h3>
                        </div>

                        <div class="flex justify-between gap-4">
                            <!-- Departamento -->
                            <div class="flex flex-col">
                                <label for="departamento" class="font-semibold text-gray-700 mb-2">Departamento
                                    <span class="text-red-500">*</span></label>
                                <select wire:model.live="departamentoSeleccionado"
                                    class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('direccion.codigo_ubigeo') border-red-500 @enderror">
                                    <option value="">Seleccione departamento...</option>
                                    @foreach ($departamentos as $dep)
                                        <option value="{{ $dep }}">{{ $dep }}</option>
                                    @endforeach
                                </select>
                                @error('direccion.codigo_ubigeo')
                                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Provincia -->
                            <div class="flex flex-col">
                                <label for="provincia" class="font-semibold text-gray-700 mb-2">Provincia <span
                                        class="text-red-500">*</span></label>
                                <select wire:model.live="provinciaSeleccionada"
                                    class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('direccion.codigo_ubigeo') border-red-500 @enderror"
                                    @if (empty($departamentoSeleccionado)) disabled @endif>
                                    <option value="">Seleccione provincia...</option>
                                    @foreach ($provincias as $prov)
                                        <option value="{{ $prov }}">{{ $prov }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Distrito -->
                            <div class="flex flex-col">
                                <label for="distrito" class="font-semibold text-gray-700 mb-2">Distrito <span
                                        class="text-red-500">*</span></label>
                                <select wire:model.change="direccion.codigo_ubigeo"
                                    class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('direccion.codigo_ubigeo') border-red-500 @enderror"
                                    @if (empty($provinciaSeleccionada)) disabled @endif>
                                    <option value="">Seleccione distrito...</option>
                                    @foreach ($distritos as $dis)
                                        <option value="{{ $dis->codigo_ubigeo }}">{{ $dis->distrito }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- BOTONES -->
                    <div class="col-span-1 lg:col-span-2 flex justify-end gap-3 pt-6 border-t border-gray-200">
                        <button type="button" wire:click="resetForm"
                            class="px-6 py-3 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Limpiar Formulario
                        </button>
                        <button type="submit"
                            class="px-8 py-3 text-sm font-semibold text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Registrar Proveedor
                        </button>
                    </div>
                </form>
            </div>
        </x-tab>
    </x-tabs>

    <!-- Modal de edición mejorado -->
    <flux:modal name="editarProveedor" class="w-full max-w-4xl h-[80vh]" wire:model="modalEditar">
        <!-- Header -->
        <div class="px-6 py-4 flex items-center justify-between border-b border-gray-200">
            <div class="flex items-center gap-3">
                <div>
                    <h3 class="text-lg font-medium text-gray-700">Editar información de Proveedor</h3>
                    <p class="text-gray-500">
                        Proveedor:
                        @if ($proveedorSeleccionado)
                            {{ $proveedorEditar['nombre_proveedor'] ?? '' }}
                        @else
                            Cargando...
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Body -->
        <div class="overflow-y-auto p-6 bg-gray-50">
            @if ($proveedorSeleccionado)
                <form class="space-y-6" wire:submit.prevent="actualizarProveedor">

                    <!-- Información del Proveedor -->
                    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
                        <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-200">
                            <div class="bg-gray-200 p-2 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24" fill="none" stroke="gray" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 2L2 7l10 5 10-5-10-5z" />
                                    <path d="M2 17l10 5 10-5" />
                                    <path d="M2 12l10 5 10-5" />
                                </svg>
                            </div>
                            <h4 class="font-bold text-gray-800 text-lg">Información del Proveedor</h4>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col">
                                <flux:label class="mb-1.5">Nombre</flux:label>
                                <flux:input readonly wire:model.change="proveedorEditar.nombre_proveedor" />
                            </div>

                            <div class="flex flex-col">
                                <flux:label class="mb-1.5">RUC</flux:label>
                                <flux:input readonly wire:model.change="proveedorEditar.ruc" />
                            </div>

                            <div class="flex flex-col">
                                <flux:label class="mb-1.5">Teléfono Principal</flux:label>
                                <flux:input type="text" wire:model.change="proveedorEditar.telefono_contacto"
                                    class="@error('proveedorEditar.telefono_contacto') border-red-500 @enderror" />
                                @error('proveedorEditar.telefono_contacto')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="flex flex-col">
                                <flux:label class="mb-1.5">Teléfono Secundario</flux:label>
                                <flux:input type="text" wire:model.change="proveedorEditar.telefono_secundario"
                                    class="@error('proveedorEditar.telefono_secundario') border-red-500 @enderror" />
                                @error('proveedorEditar.telefono_secundario')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="flex flex-col">
                                <flux:label class="mb-1.5">Correo Empresa</flux:label>
                                <flux:input type="email"
                                    wire:model.change="proveedorEditar.correo_electronico_empresa"
                                    class="@error('proveedorEditar.correo_electronico_empresa') border-red-500 @enderror" />
                                @error('proveedorEditar.correo_electronico_empresa')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="flex flex-col">
                                <flux:label class="mb-1.5">Correo Encargado</flux:label>
                                <flux:input type="email"
                                    wire:model.change="proveedorEditar.correo_electronico_encargado"
                                    class="@error('proveedorEditar.correo_electronico_encargado') border-red-500 @enderror" />
                                @error('proveedorEditar.correo_electronico_encargado')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="flex flex-col">
                                <flux:label class="mb-1.5">País</flux:label>
                                <flux:select wire:model.change="proveedorEditar.pais"
                                    class="@error('proveedorEditar.pais') border-red-500 @enderror">
                                    <option value="">Seleccione...</option>
                                    <option value="peru">Perú</option>
                                    <option value="colombia">Colombia</option>
                                </flux:select>
                                @error('proveedorEditar.pais')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="flex flex-col">
                                <flux:label class="mb-1.5">Estado</flux:label>
                                <flux:select wire:model.change="proveedorEditar.estado"
                                    class="@error('proveedorEditar.estado') border-red-500 @enderror">
                                    <option value="activo">Activo</option>
                                    <option value="inactivo">Inactivo</option>
                                </flux:select>
                                @error('proveedorEditar.pais')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Dirección -->
                    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
                        <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-200">
                            <div class="bg-gray-200 p-2 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24" fill="none" stroke="gray" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                                    <circle cx="12" cy="10" r="3" />
                                </svg>
                            </div>
                            <h4 class="font-bold text-gray-800 text-lg">Dirección</h4>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col">
                                <flux:label class="mb-1.5">Zona</flux:label>
                                <flux:input type="text" wire:model.change="direccionEditar.zona"
                                    class="@error('direccionEditar.zona') border-red-500 @enderror" />
                                @error('direccionEditar.zona')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="flex flex-col">
                                <flux:label class="mb-1.5">Tipo de Calle</flux:label>
                                <flux:select wire:model.change="direccionEditar.tipo_calle"
                                    class="@error('direccionEditar.tipo_calle') border-red-500 @enderror">
                                    <option value="">-- Seleccione --</option>
                                    @foreach ($tipos_calle as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </flux:select>
                                @error('direccionEditar.tipo_calle')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="flex flex-col">
                                <flux:label class="mb-1.5">Nombre de Calle</flux:label>
                                <flux:input type="text" wire:model.change="direccionEditar.nombre_calle"
                                    class="@error('direccionEditar.nombre_calle') border-red-500 @enderror" />
                                @error('direccionEditar.nombre_calle')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="flex flex-col">
                                <flux:label class="mb-1.5">Número</flux:label>
                                <flux:input type="text" wire:model.change="direccionEditar.numero"
                                    class="@error('direccionEditar.numero') border-red-500 @enderror" />
                                @error('direccionEditar.numero')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="flex flex-col">
                                <flux:label class="mb-1.5">Código Postal</flux:label>
                                <flux:input type="text" wire:model.change="direccionEditar.codigo_postal"
                                    class="@error('direccionEditar.codigo_postal') border-red-500 @enderror" />
                                @error('direccionEditar.codigo_postal')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="flex flex-col">
                                <flux:label class="mb-1.5">Referencia</flux:label>
                                <flux:input type="text" wire:model.change="direccionEditar.referencia"
                                    class="@error('direccionEditar.referencia') border-red-500 @enderror" />
                                @error('direccionEditar.referencia')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Productos Asociados -->
                    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
                        <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-200">
                            <div class="bg-gray-200 p-2 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24" fill="none" stroke="gray" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                                    <line x1="3" y1="6" x2="21" y2="6"></line>
                                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                                </svg>
                            </div>
                            <h4 class="font-bold text-gray-800 text-lg">Productos que Provee</h4>
                        </div>

                        <div class="flex flex-col">
                            <flux:label class="mb-1.5">
                                Productos <span class="text-red-500">*</span>
                            </flux:label>

                            <!-- Componente Alpine.js para selección de productos en edición -->
                            <div x-data="productosModalSelect(@js($productos_seleccionados_editar ?? []), @js($productos))" x-init="init()" class="relative max-h-[400px]">

                                <!-- Input de búsqueda y selección -->
                                <div class="border border-gray-300 rounded-lg px-3 py-2 focus-within:ring-2 focus-within:ring-green-500 focus-within:border-transparent transition-colors min-h-[42px] cursor-pointer"
                                    @click="open = !open">

                                    <!-- Chips de productos seleccionados -->
                                    <div class="flex flex-wrap gap-1 max-h-24 overflow-y-auto">
                                        <template x-for="productoId in selectedIds" :key="productoId">
                                            <div
                                                class="bg-green-600 text-white px-2 py-1 rounded text-xs flex items-center gap-1">
                                                <span x-text="getProductoName(productoId)"></span>
                                                <button type="button" @click.stop="removeProducto(productoId)"
                                                    class="hover:bg-green-700 rounded-full w-4 h-4 flex items-center justify-center">
                                                    ×
                                                </button>
                                            </div>
                                        </template>

                                        <!-- Placeholder cuando no hay selección -->
                                        <span x-show="selectedIds.length === 0" class="text-gray-500">
                                            -- Seleccione uno o varios productos --
                                        </span>
                                    </div>
                                </div>

                                <!-- Dropdown CON ALTURA MÁXIMA -->
                                <div x-show="open" x-transition @click.outside="open = false"
                                    class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-40 overflow-y-auto"
                                    style="max-height: 160px;">

                                    <!-- Búsqueda -->
                                    <div class="p-2 border-b border-gray-200 sticky top-0 bg-white">
                                        <input type="text" x-model="search" @input="filterProductos()"
                                            placeholder="Buscar productos..."
                                            class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                                    </div>

                                    <!-- Lista de productos -->
                                    <div class="py-1">
                                        <template x-for="producto in filteredProductos" :key="producto.id_producto">
                                            <div class="flex items-center px-3 py-2 hover:bg-green-50 cursor-pointer"
                                                @click="toggleProducto(producto.id_producto)">

                                                <input type="checkbox"
                                                    :checked="selectedIds.includes(producto.id_producto)"
                                                    class="rounded border-gray-300 text-green-600 focus:ring-green-500 mr-2">

                                                <span x-text="producto.nombre_producto"
                                                    :class="{
                                                        'font-medium': selectedIds.includes(producto.id_producto)
                                                    }">
                                                </span>
                                            </div>
                                        </template>

                                        <!-- Mensaje cuando no hay resultados -->
                                        <div x-show="filteredProductos.length === 0"
                                            class="px-3 py-2 text-gray-500 text-sm">
                                            No se encontraron productos
                                        </div>
                                    </div>
                                </div>

                                <!-- Input oculto para Livewire -->
                                <input type="hidden" x-model="selectedIdsString"
                                    wire:model="productos_seleccionados_editar">
                            </div>

                            @error('productos_seleccionados_editar')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </form>
            @else
                <!-- Loading state -->
                <div class="flex items-center justify-center py-12">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                        <p class="mt-4 text-gray-600">Cargando datos del proveedor...</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Footer -->
        @if ($proveedorSeleccionado)
            <div slot="footer" class="flex justify-end gap-3">
                <flux:button wire:click.prevent="cerrarModal">
                    Cancelar
                </flux:button>
                <flux:button wire:click="actualizarProveedor" :disabled="$loading" variant="primary">
                    @if ($loading)
                        <span class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Guardando...
                        </span>
                    @else
                        Guardar Cambios
                    @endif
                </flux:button>
            </div>
        @endif
    </flux:modal>

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

            document.addEventListener('alpine:init', () => {
                // Componente para el formulario principal - PRODUCTOS
                Alpine.data('productosSelect', (initialSelected = []) => ({
                    open: false,
                    search: '',
                    selectedIds: [],
                    allProductos: @json($productos),
                    filteredProductos: [],

                    init() {
                        this.initializeSelectedIds(initialSelected);
                        this.filteredProductos = this.allProductos;

                        this.$watch('selectedIds', (value) => {
                            this.syncWithLivewire();
                        });

                        this.syncWithLivewire();
                    },

                    initializeSelectedIds(initialValue) {
                        if (Array.isArray(initialValue)) {
                            this.selectedIds = initialValue;
                        } else if (typeof initialValue === 'string') {
                            try {
                                const parsed = JSON.parse(initialValue);
                                this.selectedIds = Array.isArray(parsed) ? parsed : [];
                            } catch (e) {
                                this.selectedIds = [];
                            }
                        } else {
                            this.selectedIds = [];
                        }
                    },

                    syncWithLivewire() {
                        const hiddenInput = this.$el.querySelector('input[type="hidden"]');
                        if (hiddenInput) {
                            const arrayValue = Array.isArray(this.selectedIds) ? this.selectedIds : [];
                            hiddenInput.value = JSON.stringify(arrayValue);
                            hiddenInput.dispatchEvent(new Event('input', {
                                bubbles: true
                            }));
                        }
                    },

                    filterProductos() {
                        if (!this.search.trim()) {
                            this.filteredProductos = this.allProductos;
                            return;
                        }
                        const searchLower = this.search.toLowerCase();
                        this.filteredProductos = this.allProductos.filter(producto =>
                            producto.nombre_producto.toLowerCase().includes(searchLower)
                        );
                    },

                    toggleProducto(productoId) {
                        if (this.selectedIds.includes(productoId)) {
                            this.removeProducto(productoId);
                        } else {
                            this.addProducto(productoId);
                        }
                    },

                    addProducto(productoId) {
                        if (!this.selectedIds.includes(productoId)) {
                            this.selectedIds.push(productoId);
                            this.syncWithLivewire();
                        }
                    },

                    removeProducto(productoId) {
                        this.selectedIds = this.selectedIds.filter(id => id != productoId);
                        this.syncWithLivewire();
                    },

                    getProductoName(productoId) {
                        const producto = this.allProductos.find(p => p.id_producto == productoId);
                        return producto ? producto.nombre_producto : '';
                    },

                    get selectedIdsString() {
                        return JSON.stringify(this.selectedIds);
                    }
                }));

                // Componente para el modal - PRODUCTOS
                Alpine.data('productosModalSelect', (initialSelected = [], productosData) => ({
                    open: false,
                    search: '',
                    selectedIds: [],
                    allProductos: productosData,
                    filteredProductos: [],
                    initialized: false,

                    init() {
                        setTimeout(() => {
                            this.initializeSelectedIds(initialSelected);
                            this.filteredProductos = this.allProductos;
                            this.initialized = true;
                            this.syncWithLivewire();
                        }, 100);

                        this.$watch('selectedIds', (value) => {
                            if (this.initialized) {
                                this.syncWithLivewire();
                            }
                        });
                    },

                    initializeSelectedIds(initialValue) {
                        if (Array.isArray(initialValue)) {
                            this.selectedIds = initialValue.map(id => id.toString());
                        } else if (typeof initialValue === 'string') {
                            try {
                                const parsed = JSON.parse(initialValue);
                                this.selectedIds = Array.isArray(parsed) ? parsed.map(id => id.toString()) :
                                    [];
                            } catch (e) {
                                this.selectedIds = [];
                            }
                        } else {
                            this.selectedIds = [];
                        }
                    },

                    syncWithLivewire() {
                        const hiddenInput = this.$el.querySelector('input[type="hidden"]');
                        if (hiddenInput) {
                            const arrayValue = Array.isArray(this.selectedIds) ? this.selectedIds : [];
                            hiddenInput.value = JSON.stringify(arrayValue);
                            hiddenInput.dispatchEvent(new Event('input', {
                                bubbles: true
                            }));
                        }
                    },

                    filterProductos() {
                        if (!this.search.trim()) {
                            this.filteredProductos = this.allProductos;
                            return;
                        }
                        const searchLower = this.search.toLowerCase();
                        this.filteredProductos = this.allProductos.filter(producto =>
                            producto.nombre_producto.toLowerCase().includes(searchLower)
                        );
                    },

                    toggleProducto(productoId) {
                        if (this.selectedIds.includes(productoId.toString())) {
                            this.removeProducto(productoId);
                        } else {
                            this.addProducto(productoId);
                        }
                    },

                    addProducto(productoId) {
                        const idStr = productoId.toString();
                        if (!this.selectedIds.includes(idStr)) {
                            this.selectedIds.push(idStr);
                            this.syncWithLivewire();
                        }
                    },

                    removeProducto(productoId) {
                        const idStr = productoId.toString();
                        this.selectedIds = this.selectedIds.filter(id => id !== idStr);
                        this.syncWithLivewire();
                    },

                    getProductoName(productoId) {
                        const producto = this.allProductos.find(p => p.id_producto == productoId);
                        return producto ? producto.nombre_producto : '';
                    },

                    get selectedIdsString() {
                        return JSON.stringify(this.selectedIds);
                    }
                }));
            });
        </script>
    @endpush

    <x-loader target="guardar, actualizarProveedor, buscarRuc, cerrarModal, cargarUbigeoSincrono" />
</x-panel>
