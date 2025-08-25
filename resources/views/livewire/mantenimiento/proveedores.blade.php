<x-panel title="Gestión de Proveedores">
    <x-tabs :tabs="['listado' => '📋 Detalle proveedores regsitrados', 'registro' => '➕ Registrar nuevo proveedor']" default="listado">
        <!-- TAB 1: LISTADO -->
        <x-tab name="listado">
            <livewire:proveedor-table />

            <script>
                function contextMenu() {
                    return {
                        x: 0,
                        y: 0,
                        openMenu: false,
                        rowId: null,
                        open(event, id) {
                            this.x = event.pageX;
                            this.y = event.pageY;
                            this.rowId = id;
                            this.openMenu = true;
                        },
                        close() {
                            this.openMenu = false;
                        },
                        accion(name) {
                            if (name === 'editar') {
                                Livewire.emit('edit', this.rowId);
                            } else if (name === 'toggleEstado') {
                                Livewire.emit('toggleEstado', this.rowId);
                            } else if (name === 'eliminar') {
                                Livewire.emit('delete', this.rowId);
                            }
                            this.close();
                        }
                    }
                }
            </script>
        </x-tab>
        <!-- TAB 2: REGISTRO -->
        <x-tab name="registro">
            <div class="p-3 bg-gray-50 rounded">
                <form wire:submit.prevent ="guardar" class="grid grid-cols-2 gap-4 text-xs">
                    <!-- ====== DATOS DE PROVEEDOR ====== -->
                    <div class="flex flex-col">
                        <label for="nombre" class="font-bold mb-1">Nombre</label>
                        <input type="text" id="nombre" name="nombre"
                            class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300"
                            placeholder="Nombre del proveedor" wire:model="proveedor.nombre">
                        @error('proveedor.nombre')
                            <p class="text-red-500 text-xs italic">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label for="ruc" class="font-bold mb-1">RUC</label>
                        <input type="text" id="ruc" name="ruc"
                            class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300"
                            placeholder="RUC del proveedor" wire:model="proveedor.ruc">
                        @error('proveedor.ruc')
                            <p class="text-red-500 text-xs italic">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label for="telefono" class="font-bold mb-1">Teléfono</label>
                        <input type="text" id="telefono" name="telefono"
                            class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300"
                            placeholder="Teléfono" wire:model="proveedor.telefono">
                        @error('proveedor.telefono')
                            <p class="text-red-500 text-xs italic">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label for="correo" class="font-bold mb-1">Correo</label>
                        <input type="email" id="correo" name="correo"
                            class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300"
                            placeholder="Correo electrónico" wire:model="proveedor.correo">
                        @error('proveedor.correo')
                            <p class="text-red-500 text-xs italic">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label for="distrito" class="font-bold mb-1">País</label>
                        <select id="distrito" name="distrito" class="border rounded px-2 py-1"
                            wire:model="proveedor.pais">
                            <option>Seleccione...</option>
                            <option value="peru">Perú</option>
                            <option value="colombia">Colombia</option>
                        </select>
                        @error('proveedor.pais')
                            <p class="text-red-500 text-xs italic">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- ====== DATOS DE DIRECCIÓN ====== -->
                    <div class="col-span-2 mt-4">
                        <p class="font-bold text-gray-700">📍 Dirección</p>
                        <div class="grid grid-cols-3 gap-4 mt-2">
                            <div class="flex flex-col">
                                <label for="tipo_calle" class="font-bold mb-1">Tipo de calle</label>
                                <input type="text" id="tipo_calle" name="tipo_calle" class="border rounded px-2 py-1"
                                    placeholder="Ej: Av, Jr, Calle" wire:model="direccion.tipo_calle">
                            </div>
                            <div class="flex flex-col">
                                <label for="nombre_calle" class="font-bold mb-1">Nombre de calle</label>
                                <input type="text" id="nombre_calle" name="nombre_calle"
                                    class="border rounded px-2 py-1" placeholder="Ej: Los Olivos"
                                    wire:model="direccion.nombre_calle">
                            </div>
                            <div class="flex flex-col">
                                <label for="numero" class="font-bold mb-1">Número</label>
                                <input type="text" id="numero" name="numero" class="border rounded px-2 py-1"
                                    placeholder="Ej: 123" wire:model="direccion.numero">
                            </div>
                            <div class="flex flex-col">
                                <label for="zona" class="font-bold mb-1">Zona</label>
                                <input type="text" id="zona" name="zona" class="border rounded px-2 py-1"
                                    placeholder="Urbanización, sector" wire:model="direccion.zona">
                            </div>
                            <div class="flex flex-col">
                                <label for="codigo_postal" class="font-bold mb-1">Código Postal</label>
                                <input type="text" id="codigo_postal" name="codigo_postal"
                                    class="border rounded px-2 py-1" placeholder="Ej: 15084"
                                    wire:model="direccion.codigo_postal">
                            </div>
                            <div class="flex flex-col">
                                <label for="referencia" class="font-bold mb-1">Referencia</label>
                                <input type="text" id="referencia" name="referencia"
                                    class="border rounded px-2 py-1" placeholder="Referencia"
                                    wire:model="direccion.referencia">
                            </div>
                        </div>
                    </div>

                    <!-- ====== UBIGEO ====== -->
                    <div class="col-span-2 mt-4">
                        <p class="font-bold text-gray-700">🌍 Ubigeo</p>
                        <div class="grid grid-cols-3 gap-4 mt-2">
                            <div class="flex flex-col">
                                <label for="departamento" class="font-bold mb-1">Departamento</label>
                                <select wire:model.live="departamentoSeleccionado" class="border rounded px-2 py-1">
                                    <option value="">-- Seleccione --</option>
                                    @foreach ($departamentos as $dep)
                                        <option value="{{ $dep }}">{{ $dep }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- PROVINCIA -->
                            <div class="flex flex-col">
                                <label for="provincia" class="font-bold mb-1">Provincia</label>
                                <select wire:model.live="provinciaSeleccionada" class="border rounded px-2 py-1">
                                    <option value="">-- Seleccione --</option>
                                    @foreach ($provincias as $prov)
                                        <option value="{{ $prov }}">{{ $prov }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- DISTRITO -->
                            <div class="flex flex-col">
                                <label for="distrito" class="font-bold mb-1">Distrito</label>
                                <select wire:model.live="direccion.codigo_ubigeo" class="border rounded px-2 py-1">
                                    <option value="">-- Seleccione --</option>
                                    @foreach ($distritos as $dis)
                                        <option value="{{ $dis->codigo_ubigeo }}">{{ $dis->distrito }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Botón -->
                    <div class="col-span-2 flex justify-end mt-4">
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-xs font-bold">
                            Registrar proveedor
                        </button>
                    </div>
                </form>
            </div>
        </x-tab>
    </x-tabs>
</x-panel>
