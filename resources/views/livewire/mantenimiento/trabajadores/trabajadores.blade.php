<x-panel title="Gestión de trabajadores">
    <x-tabs :tabs="['listado' => '📋 Detalle trabajadores registrados', 'registro' => '➕ Registrar nuevo trabajador']" default="listado">
        <!-- TAB 1: LISTADO -->
        <x-tab name="listado">
            <div class="p-4">
                <livewire:trabajador-table />
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
                    <!-- Datos de Persona -->
                    <div class="col-span-2">
                        <p class="font-bold text-gray-700 mb-3">👤 Información Personal</p>
                    </div>

                    <div class="flex flex-col">
                        <label>Nombre <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="persona.nombre" class="border rounded px-2 py-1">
                        @error('persona.nombre')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label>Apellido Paterno <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="persona.apellido_paterno" class="border rounded px-2 py-1">
                        @error('persona.apellido_paterno')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label>Apellido Materno</label>
                        <input type="text" wire:model="persona.apellido_materno" class="border rounded px-2 py-1">
                    </div>

                    <div class="flex flex-col">
                        <label>Tipo de documento <span class="text-red-500">*</span></label>
                        <select wire:model.live="persona.id_tipo_documento" class="border rounded px-2 py-1">
                            <option value="">-- Seleccione --</option>
                            @foreach ($tipos_documentos as $t)
                                <option value="{{ $t->id }}">{{ $t->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col">
                        <label>Numero documento <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="persona.numero_documento" class="border rounded px-2 py-1"
                            @disabled(!$persona['id_tipo_documento'])>
                        @error('persona.numero_documento')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label>Fecha Nacimiento <span class="text-red-500">*</span></label>
                        <input type="date" wire:model="persona.fecha_nacimiento" class="border rounded px-2 py-1">
                    </div>

                    <div class="flex flex-col">
                        <label>Sexo <span class="text-red-500">*</span></label>
                        <select wire:model="persona.sexo" class="border rounded px-2 py-1">
                            <option value="">-- Seleccione --</option>
                            <option value="M">Masculino</option>
                            <option value="F">Femenino</option>
                            <option value="O">Otro</option>
                        </select>
                    </div>

                    <div class="flex flex-col">
                        <label>Correo electronico personal <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="persona.correo_electronico_personal"
                            class="border rounded px-2 py-1">
                    </div>

                    <div class="flex flex-col">
                        <label>Numero telefono personal <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="persona.numero_telefono_personal"
                            class="border rounded px-2 py-1">
                    </div>

                    <div class="flex flex-col">
                        <label>Correo electronico secundario</label>
                        <input type="text" wire:model="persona.correo_electronico_secundario"
                            class="border rounded px-2 py-1">
                    </div>

                    <div class="flex flex-col">
                        <label>Numero telefono secundario</label>
                        <input type="text" wire:model="persona.numero_telefono_secundario"
                            class="border rounded px-2 py-1">
                    </div>

                    <!-- Datos de Trabajador -->
                    <div class="col-span-2 mt-4">
                        <p class="font-bold text-gray-700 mb-3">💼 Información Laboral</p>
                    </div>

                    <div class="flex flex-col">
                        <label>Fecha de Ingreso</label>
                        <input type="date" wire:model="trabajador.fecha_ingreso" class="border rounded px-2 py-1"
                            disabled>
                    </div>

                    <div class="flex flex-col">
                        <label>Numero seguro social <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" wire:model="trabajador.numero_seguro_social"
                            class="border rounded px-2 py-1">
                    </div>

                    <div class="flex flex-col">
                        <label>Salario <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" wire:model="trabajador.salario"
                            class="border rounded px-2 py-1">
                    </div>

                    <div class="flex flex-col">
                        <label>Puesto <span class="text-red-500">*</span></label>
                        <select wire:model="trabajador.id_puesto_trabajo" class="border rounded px-2 py-1">
                            <option value="">-- Seleccione --</option>
                            @foreach ($puestos as $p)
                                <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- ====== DATOS DE DIRECCIÓN ====== -->
                    <div class="col-span-2 mt-4">
                        <p class="font-bold text-gray-700 mb-3">📍 Dirección <span class="text-red-500">*</span></p>
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
                                <label for="nombre_calle" class="font-bold mb-1">Nombre de Calle <span
                                        class="text-red-500">*</span></label>
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
                                <label for="numero" class="font-bold mb-1">Número <span
                                        class="text-red-500">*</span></label>
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
                                <label for="zona" class="font-bold mb-1">Zona <span
                                        class="text-red-500">*</span></label>
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
                                <label for="codigo_postal" class="font-bold mb-1">Código Postal <span
                                        class="text-red-500">*</span></label>
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
                    <!-- Botones -->
                    <div class="col-span-2 flex justify-end mt-6 space-x-2">
                        <button type="reset" class="bg-gray-500 text-white px-4 py-2 rounded">Limpiar</button>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Registrar
                            Trabajador</button>
                    </div>
                </form>

            </div>
        </x-tab>
    </x-tabs>

</x-panel>
