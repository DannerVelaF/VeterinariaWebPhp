<x-panel title="Gestión de Proveedores">
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
                    <!-- ====== DATOS DE PROVEEDOR ====== -->
                    <div class="col-span-2">
                        <p class="font-bold text-gray-700 mb-3">👤 Información del Proveedor</p>
                    </div>

                    <div class="flex flex-col">
                        <label for="nombre" class="font-bold mb-1">Nombre <span class="text-red-500">*</span></label>
                        <input type="text" id="nombre" name="nombre"
                            class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 @error('proveedor.nombre') border-red-500 @enderror"
                            placeholder="Nombre del proveedor" wire:model="proveedor.nombre">
                        @error('proveedor.nombre')
                            <p class="text-red-500 text-xs italic mt-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label for="ruc" class="font-bold mb-1">RUC <span class="text-red-500">*</span></label>
                        <input type="text" id="ruc" name="ruc" maxlength="11"
                            class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 @error('proveedor.ruc') border-red-500 @enderror"
                            placeholder="RUC del proveedor (11 dígitos)" wire:model="proveedor.ruc">
                        @error('proveedor.ruc')
                            <p class="text-red-500 text-xs italic mt-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label for="telefono" class="font-bold mb-1">Teléfono Principal</label>
                        <input type="text" id="telefono" name="telefono" maxlength="15"
                            class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 @error('proveedor.telefono') border-red-500 @enderror"
                            placeholder="Teléfono principal" wire:model="proveedor.telefono">
                        @error('proveedor.telefono')
                            <p class="text-red-500 text-xs italic mt-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label for="correo_electronico_empresa" class="font-bold mb-1">Correo Empresa</label>
                        <input type="email" id="correo_electronico_empresa" maxlength="255"
                            class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 @error('proveedor.correo_electronico_empresa') border-red-500 @enderror"
                            placeholder="correo@empresa.com" wire:model="proveedor.correo_electronico_empresa">
                        @error('proveedor.correo_electronico_empresa')
                            <p class="text-red-500 text-xs italic mt-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label for="telefono_contacto" class="font-bold mb-1">Teléfono Contacto</label>
                        <input type="text" id="telefono_contacto" maxlength="15"
                            class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 @error('proveedor.telefono_contacto') border-red-500 @enderror"
                            placeholder="Teléfono contacto" wire:model="proveedor.telefono_contacto">
                        @error('proveedor.telefono_contacto')
                            <p class="text-red-500 text-xs italic mt-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label for="telefono_secundario" class="font-bold mb-1">Teléfono Secundario</label>
                        <input type="text" id="telefono_secundario" maxlength="15"
                            class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 @error('proveedor.telefono_secundario') border-red-500 @enderror"
                            placeholder="Teléfono secundario" wire:model="proveedor.telefono_secundario">
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
                            placeholder="encargado@empresa.com" wire:model="proveedor.correo_electronico_encargado">
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
                            wire:model="proveedor.pais">
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
                                <input type="text" id="tipo_calle" name="tipo_calle" maxlength="50"
                                    class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 @error('direccion.tipo_calle') border-red-500 @enderror"
                                    placeholder="Ej: Av, Jr, Calle" wire:model="direccion.tipo_calle">
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
                                    placeholder="Ej: Los Olivos" wire:model="direccion.nombre_calle">
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
                                    placeholder="Ej: 123" wire:model="direccion.numero">
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
                                    placeholder="Urbanización, sector" wire:model="direccion.zona">
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
                                    placeholder="Ej: 15084" wire:model="direccion.codigo_postal">
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
                                    placeholder="Punto de referencia" wire:model="direccion.referencia">
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
                                    class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300">
                                    <option value="">-- Seleccione --</option>
                                    @foreach ($departamentos as $dep)
                                        <option value="{{ $dep }}">{{ $dep }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- PROVINCIA -->
                            <div class="flex flex-col">
                                <label for="provincia" class="font-bold mb-1">Provincia <span
                                        class="text-red-500">*</span></label>
                                <select wire:model.live="provinciaSeleccionada"
                                    class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300"
                                    @if (empty($departamentoSeleccionado)) disabled @endif>
                                    <option value="">-- Seleccione --</option>
                                    @foreach ($provincias as $prov)
                                        <option value="{{ $prov }}">{{ $prov }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- DISTRITO -->
                            <div class="flex flex-col">
                                <label for="distrito" class="font-bold mb-1">Distrito <span
                                        class="text-red-500">*</span></label>
                                <select wire:model="direccion.codigo_ubigeo"
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
</x-panel>
