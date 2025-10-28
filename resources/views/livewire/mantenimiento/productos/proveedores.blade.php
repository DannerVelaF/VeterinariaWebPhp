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

            <div class="p-3 bg-gray-50 rounded">
                <form wire:submit.prevent="guardar" class="grid grid-cols-2 gap-4 text-xs">
                    <!-- ====== DATOS DE PROVEEDOR ====== -->
                    <div class="col-span-2">
                        <p class="font-bold text-gray-700 mb-3">👤 Información del Proveedor</p>
                    </div>

                    <div class="flex flex-col">
                        <label for="ruc" class="font-bold mb-1">RUC <span class="text-red-500">*</span></label>
                        <div class="flex items-center">
                            <input type="text" id="ruc" name="ruc" maxlength="11"
                                class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 @error('proveedor.ruc') border-red-500 @enderror"
                                placeholder="RUC del proveedor (11 dígitos)" wire:model.change="proveedor.ruc">
                            <button type="button" wire:click="buscarRuc"
                                class="ml-2 bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs font-bold">
                                Buscar RUC
                            </button>
                        </div>
                        @error('proveedor.ruc')
                            <p class="text-red-500 text-xs italic mt-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label for="nombre" class="font-bold mb-1">Nombre <span class="text-red-500">*</span></label>
                        <input @readonly($proveedorEncontrado) type="text" id="nombre" name="nombre"
                            class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 @error('proveedor.nombre_proveedor') border-red-500 @enderror"
                            placeholder="Nombre del proveedor" wire:model.change="proveedor.nombre_proveedor">
                        @error('proveedor.nombre_proveedor')
                            <p class="text-red-500 text-xs italic mt-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label for="telefono_contacto" class="font-bold mb-1">Teléfono Principal</label>
                        <input type="number" id="telefono_contacto" name="telefono_contacto" maxlength="9"
                            class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 @error('proveedor.telefono_contacto') border-red-500 @enderror"
                            placeholder="Teléfono principal (9 dígitos)"
                            wire:model.change="proveedor.telefono_contacto">
                        @error('proveedor.telefono_contacto')
                            <p class="text-red-500 text-xs italic mt-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label for="correo_electronico_empresa" class="font-bold mb-1">Correo Empresa</label>
                        <input type="email" id="correo_electronico_empresa" maxlength="255"
                            class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 @error('proveedor.correo_electronico_empresa') border-red-500 @enderror"
                            placeholder="correo@empresa.com" wire:model.change="proveedor.correo_electronico_empresa">
                        @error('proveedor.correo_electronico_empresa')
                            <p class="text-red-500 text-xs italic mt-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label for="telefono_secundario" class="font-bold mb-1">Teléfono Secundario</label>
                        <input type="number" id="telefono_secundario" maxlength="9"
                            class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 @error('proveedor.telefono_secundario') border-red-500 @enderror"
                            placeholder="Teléfono secundario (9 dígitos)"
                            wire:model.change="proveedor.telefono_secundario">
                        @error('proveedor.telefono_secundario')
                            <p class="text-red-500 text-xs italic mt-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label for="correo_electronico_encargado" class="font-bold mb-1">Correo Encargado</label>
                        <input type="email" id="correo_electronico_encargado" maxlength="255"
                            class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 @error('proveedor.correo_electronico_encargado') border-red-500 @enderror"
                            placeholder="encargado@empresa.com"
                            wire:model.change="proveedor.correo_electronico_encargado">
                        @error('proveedor.correo_electronico_encargado')
                            <p class="text-red-500 text-xs italic mt-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label for="pais" class="font-bold mb-1">País <span class="text-red-500">*</span></label>
                        <select id="pais" name="pais"
                            class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 @error('proveedor.pais') border-red-500 @enderror"
                            wire:model.change="proveedor.pais">
                            <option value="">Seleccione...</option>
                            <option value="peru">Perú</option>
                            <option value="colombia">Colombia</option>
                        </select>
                        @error('proveedor.pais')
                            <p class="text-red-500 text-xs italic mt-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- ====== DATOS DE DIRECCIÓN ====== -->
                    <div class="col-span-2 mt-4">
                        <p class="font-bold text-gray-700 mb-3">📍 Dirección</p>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="flex flex-col">
                                <label for="tipo_calle" class="font-bold mb-1">Tipo de Calle</label>
                                <select id="tipo_calle" name="tipo_calle"
                                    class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 @error('direccion.tipo_calle') border-red-500 @enderror"
                                    wire:model.change="direccion.tipo_calle">
                                    <option value="">-- Seleccione --</option>
                                    @foreach ($tipos_calle as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                @error('direccion.tipo_calle')
                                    <p class="text-red-500 text-xs italic mt-1">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="flex flex-col">
                                <label for="nombre_calle" class="font-bold mb-1">Nombre de Calle</label>
                                <input type="text" id="nombre_calle" name="nombre_calle" maxlength="255"
                                    class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 @error('direccion.nombre_calle') border-red-500 @enderror"
                                    placeholder="Ej: Los Olivos" wire:model.change="direccion.nombre_calle">
                                @error('direccion.nombre_calle')
                                    <p class="text-red-500 text-xs italic mt-1">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="flex flex-col">
                                <label for="numero" class="font-bold mb-1">Número</label>
                                <input type="text" id="numero" name="numero" maxlength="10"
                                    class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 @error('direccion.numero') border-red-500 @enderror"
                                    placeholder="Ej: 123" wire:model.change="direccion.numero">
                                @error('direccion.numero')
                                    <p class="text-red-500 text-xs italic mt-1">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="flex flex-col">
                                <label for="zona" class="font-bold mb-1">Zona</label>
                                <input type="text" id="zona" name="zona" maxlength="255"
                                    class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 @error('direccion.zona') border-red-500 @enderror"
                                    placeholder="Urbanización, sector" wire:model.change="direccion.zona">
                                @error('direccion.zona')
                                    <p class="text-red-500 text-xs italic mt-1">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="flex flex-col">
                                <label for="codigo_postal" class="font-bold mb-1">Código Postal</label>
                                <input type="text" id="codigo_postal" name="codigo_postal" maxlength="10"
                                    class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 @error('direccion.codigo_postal') border-red-500 @enderror"
                                    placeholder="Ej: 15084" wire:model.change="direccion.codigo_postal">
                                @error('direccion.codigo_postal')
                                    <p class="text-red-500 text-xs italic mt-1">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="flex flex-col">
                                <label for="referencia" class="font-bold mb-1">Referencia</label>
                                <input type="text" id="referencia" name="referencia" maxlength="255"
                                    class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 @error('direccion.referencia') border-red-500 @enderror"
                                    placeholder="Punto de referencia" wire:model.change="direccion.referencia">
                                @error('direccion.referencia')
                                    <p class="text-red-500 text-xs italic mt-1">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- ====== UBIGEO ====== -->
                    <div class="col-span-2 mt-4">
                        <p class="font-bold text-gray-700 mb-3">🌍 Ubicación Geográfica</p>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="flex flex-col">
                                <label for="departamento" class="font-bold mb-1">Departamento <span
                                        class="text-red-500">*</span></label>
                                <select wire:model.live="departamentoSeleccionado"
                                    class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 @error('direccion.codigo_ubigeo') border-red-500 @enderror">
                                    <option value="">-- Seleccione --</option>
                                    @foreach ($departamentos as $dep)
                                        <option value="{{ $dep }}">{{ $dep }}</option>
                                    @endforeach
                                </select>
                                @error('direccion.codigo_ubigeo')
                                    <p class="text-red-500 text-xs italic mt-1">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- PROVINCIA -->
                            <div class="flex flex-col">
                                <label for="provincia" class="font-bold mb-1">Provincia <span
                                        class="text-red-500">*</span></label>
                                <select wire:model.live="provinciaSeleccionada"
                                    class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 @error('direccion.codigo_ubigeo') border-red-500 @enderror"
                                    @if (empty($departamentoSeleccionado)) disabled @endif>
                                    <option value="">-- Seleccione --</option>
                                    @foreach ($provincias as $prov)
                                        <option value="{{ $prov }}">{{ $prov }}</option>
                                    @endforeach
                                </select>
                                @error('direccion.codigo_ubigeo')
                                    <p class="text-red-500 text-xs italic mt-1">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- DISTRITO -->
                            <div class="flex flex-col">
                                <label for="distrito" class="font-bold mb-1">Distrito <span
                                        class="text-red-500">*</span></label>
                                <select wire:model.change="direccion.codigo_ubigeo"
                                    class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 @error('direccion.codigo_ubigeo') border-red-500 @enderror"
                                    @if (empty($provinciaSeleccionada)) disabled @endif>
                                    <option value="">-- Seleccione --</option>
                                    @foreach ($distritos as $dis)
                                        <option value="{{ $dis->codigo_ubigeo }}">{{ $dis->distrito }}</option>
                                    @endforeach
                                </select>
                                @error('direccion.codigo_ubigeo')
                                    <p class="text-red-500 text-xs italic mt-1">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Botón -->
                    <div class="col-span-2 flex justify-end mt-6">
                        <button type="button" wire:click="resetForm"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded text-xs font-bold mr-2">
                            Limpiar
                        </button>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-xs font-bold">
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

                    <!-- Ubicación Geográfica -->
                    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
                        <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-200">
                            <div class="bg-gray-200 p-2 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24" fill="none" stroke="gray" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="2" y1="12" x2="22" y2="12" />
                                    <path
                                        d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
                                </svg>
                            </div>
                            <h4 class="font-bold text-gray-800 text-lg">Ubicación Geográfica</h4>
                            @if ($loadingUbigeo)
                                <div class="ml-2">
                                    <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-600"></div>
                                </div>
                            @endif
                        </div>

                        @if ($loadingUbigeo)
                            <div class="flex items-center justify-center py-8">
                                <div class="text-center">
                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto">
                                    </div>
                                    <p class="mt-2 text-gray-600 text-sm">Cargando ubicación...</p>
                                </div>
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Departamento -->
                                <div class="flex flex-col">
                                    <flux:label class="mb-1.5">Departamento</flux:label>
                                    <flux:select wire:model.live="departamentoSeleccionadoEditar">
                                        <option value="">-- Seleccione --</option>
                                        @foreach ($departamentos as $dep)
                                            <option value="{{ $dep }}" @selected($dep == $departamentoSeleccionadoEditar)>
                                                {{ $dep }}
                                            </option>
                                        @endforeach
                                    </flux:select>
                                </div>

                                <!-- Provincia con loading -->
                                <div class="flex flex-col items-center">
                                    <flux:label class="mb-1.5">Provincia</flux:label>
                                    <div class="relative">
                                        <flux:select wire:model.live="provinciaSeleccionadaEditar"
                                            :disabled="empty($departamentoSeleccionadoEditar) || $loadingUbigeo">
                                            <option value="">-- Seleccione --</option>
                                            @foreach ($provinciasEditar as $prov)
                                                <option value="{{ $prov }}" @selected($prov == $provinciaSeleccionadaEditar)>
                                                    {{ $prov }}
                                                </option>
                                            @endforeach
                                        </flux:select>

                                        <!-- Loading state para provincias -->
                                        <div wire:loading wire:target="departamentoSeleccionadoEditar"
                                            class="absolute inset-0 bg-white bg-opacity-80 flex items-center justify-center rounded border">
                                            <div class="flex items-center gap-2 text-blue-600 text-xs font-medium">
                                                <div
                                                    class="animate-spin rounded-full h-3 w-3 border-b-2 border-blue-600">
                                                </div>
                                                <span>Cargando provincias...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Distrito con loading -->
                                <div class="flex flex-col items-center">
                                    <flux:label class="mb-1.5">Distrito</flux:label>
                                    <div class="relative">
                                        <flux:select wire:model.change="direccionEditar.codigo_ubigeo"
                                            :disabled="empty($provinciaSeleccionadaEditar) || $loadingUbigeo"
                                            class="@error('direccionEditar.codigo_ubigeo') border-red-500 @enderror">
                                            <option value="">-- Seleccione --</option>
                                            @foreach ($distritosEditar as $dis)
                                                <option value="{{ $dis->codigo_ubigeo }}"
                                                    @selected($dis->codigo_ubigeo == ($direccionEditar['codigo_ubigeo'] ?? ''))>
                                                    {{ $dis->distrito }}
                                                </option>
                                            @endforeach
                                        </flux:select>

                                        <!-- Loading state para distritos -->
                                        <div wire:loading wire:target="provinciaSeleccionadaEditar"
                                            class="absolute inset-0 bg-white bg-opacity-80 flex items-center justify-center rounded border">
                                            <div class="flex items-center gap-2 text-blue-600 text-xs font-medium">
                                                <div
                                                    class="animate-spin rounded-full h-3 w-3 border-b-2 border-blue-600">
                                                </div>
                                                <span>Cargando distritos...</span>
                                            </div>
                                        </div>

                                        @error('direccionEditar.codigo_ubigeo')
                                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endif
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
        </script>
    @endpush
    <x-loader target="guardar, actualizarProveedor, buscarRuc, cerrarModal, cargarUbigeoSincrono" />
</x-panel>
