<x-panel title="Gestión de clientes">
    <x-tabs :tabs="['listado' => '📋 Detalle clientes registrados', 'registro' => '➕ Registrar nuevo cliente']" default="listado">
        <!-- TAB 1: LISTADO -->
        <x-tab name="listado">
            <div class="p-4">
                <livewire:clientes-table />
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
                    <!-- Datos de Persona -->
                    <div class="col-span-2">
                        <p class="font-bold text-gray-700 mb-3">👤 Información Personal</p>
                    </div>

                    <div class="flex flex-col">
                        <label>Tipo de documento <span class="text-red-500">*</span></label>
                        <select wire:model.live="persona.id_tipo_documento" class="border rounded px-2 py-1">
                            <option value="">-- Seleccione --</option>
                            @foreach ($tipos_documentos as $t)
                                <option value="{{ $t->id_tipo_documento }}">{{ $t->nombre_tipo_documento }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col">
                        <label>Numero documento <span class="text-red-500">*</span></label>
                        <div class="flex items-center">
                            <input type="text" wire:model="persona.numero_documento"
                                maxlength="{{ $persona['id_tipo_documento'] == 1 ? 8 : 11 }}"
                                class="border rounded px-2 py-1" @disabled(!$persona['id_tipo_documento'])>
                            <button type="button" wire:click="buscarDni" @disabled($persona['id_tipo_documento'] != 1)
                                class="ml-2 bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs font-bold disabled:opacity-50 disabled:cursor-not-allowed">
                                Buscar DNI
                            </button>
                        </div>
                        @error('persona.numero_documento')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label>Nombre <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="persona.nombre" class="border rounded px-2 py-1"
                            @readonly($persona['id_tipo_documento'] == 1)>
                        @error('persona.nombre')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label>Apellido Paterno <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="persona.apellido_paterno" class="border rounded px-2 py-1"
                            @readonly($persona['id_tipo_documento'] == 1)>
                        @error('persona.apellido_paterno')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label>Apellido Materno</label>
                        <input type="text" wire:model="persona.apellido_materno" class="border rounded px-2 py-1"
                            @readonly($persona['id_tipo_documento'] == 1)>
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
                        <label>Nacionalidad <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="persona.nacionalidad" class="border rounded px-2 py-1">
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
    @if ($modalEditar)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <!-- Overlay -->
            <div class="absolute inset-0 bg-black opacity-50" wire:click="$set('modalEditar', false)"></div>

            <!-- Modal -->
            <div class="relative bg-white rounded-md p-6 w-1/2 z-10 overflow-y-auto max-h-[90vh]">
                <h3 class="font-bold mb-4 text-lg">
                    Editar trabajador: {{ $trabajadorSeleccionado->persona?->nombre }}
                </h3>

                <div class="grid grid-cols-2 gap-4">

                    <!-- Información Personal -->
                    <div class="col-span-2 font-bold text-gray-700 mt-2 flex gap-2"><svg
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-user-icon lucide-user">
                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg> Información Personal</div>

                    <div class="flex flex-col">
                        <label>Nombre</label>
                        <input type="text" wire:model="persona.nombre" class="border rounded px-2 py-1">
                    </div>

                    <div class="flex flex-col">
                        <label>Apellido Paterno</label>
                        <input type="text" wire:model="persona.apellido_paterno" class="border rounded px-2 py-1">
                    </div>

                    <div class="flex flex-col">
                        <label>Apellido Materno</label>
                        <input type="text" wire:model="persona.apellido_materno" class="border rounded px-2 py-1">
                    </div>

                    <div class="flex flex-col">
                        <label>Correo personal</label>
                        <input type="email" wire:model="persona.correo_electronico_personal"
                            class="border rounded px-2 py-1">
                    </div>
                    <div class="flex flex-col">
                        <label>Correo secundario</label>
                        <input type="email" wire:model="persona.correo_electronico_secundario"
                            class="border rounded px-2 py-1">
                    </div>
                    <div class="flex flex-col">
                        <label>Teléfono personal</label>
                        <input type="text" wire:model="persona.numero_telefono_personal"
                            class="border rounded px-2 py-1">
                    </div>
                    <div class="flex flex-col">
                        <label>Teléfono Secundario</label>
                        <input type="text" wire:model="persona.numero_telefono_secundario"
                            class="border rounded px-2 py-1">
                    </div>
                    <!-- Información Laboral -->
                    <div class="col-span-2 font-bold text-gray-700 mt-4 flex gap-2"><svg
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"
                            class="lucide lucide-briefcase-business-icon lucide-briefcase-business">
                            <path d="M12 12h.01" />
                            <path d="M16 6V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" />
                            <path d="M22 13a18.15 18.15 0 0 1-20 0" />
                            <rect width="20" height="14" x="2" y="6" rx="2" />
                        </svg> Información Laboral</div>

                    <div class="flex flex-col">
                        <label>Salario</label>
                        <input type="number" step="0.01" wire:model="trabajador.salario"
                            class="border rounded px-2 py-1">
                    </div>

                    <div class="flex flex-col">
                        <label>Seguro Social</label>
                        <input type="text" wire:model="trabajador.numero_seguro_social"
                            class="border rounded px-2 py-1">
                    </div>

                    <div class="flex flex-col">
                        <label>Puesto</label>
                        <select wire:model="puestoNuevo" class="border rounded px-2 py-1">
                            <option value="">Seleccione...</option>
                            @foreach ($puestos as $puesto)
                                <option value="{{ $puesto->id_puesto_trabajo }}">{{ $puesto->nombre_puesto }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col">
                        <label>Estado</label>
                        <select wire:model="estadoNuevo" class="border rounded px-2 py-1">
                            <option value="">Seleccione...</option>
                            @foreach ($estados as $estado)
                                <option value="{{ $estado->id_estado_trabajador }}">
                                    {{ $estado->nombre_estado_trabajador }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Dirección -->
                    <div class="col-span-2 font-bold text-gray-700 mt-4 flex gap-2"><svg
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"
                            class="lucide lucide-briefcase-business-icon lucide-briefcase-business">
                            <path d="M12 12h.01" />
                            <path d="M16 6V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" />
                            <path d="M22 13a18.15 18.15 0 0 1-20 0" />
                            <rect width="20" height="14" x="2" y="6" rx="2" />
                        </svg> Dirección</div>

                    <div class="flex flex-col">
                        <label>Zona</label>
                        <input type="text" wire:model="direccion.zona" class="border rounded px-2 py-1">
                    </div>

                    <div class="flex flex-col">
                        <label>Tipo de Calle</label>
                        <input type="text" wire:model="direccion.tipo_calle" class="border rounded px-2 py-1">
                    </div>

                    <div class="flex flex-col">
                        <label>Nombre de Calle</label>
                        <input type="text" wire:model="direccion.nombre_calle" class="border rounded px-2 py-1">
                    </div>

                    <div class="flex flex-col">
                        <label>Número</label>
                        <input type="text" wire:model="direccion.numero" class="border rounded px-2 py-1">
                    </div>

                </div>

                <div class="flex justify-end space-x-2 mt-6">
                    <button wire:click="cerrarModal" class="px-3 py-1 rounded bg-gray-500 text-white">Cerrar</button>
                    <button wire:click="guardarEdicion"
                        class="px-3 py-1 rounded bg-blue-600 text-white">Guardar</button>
                </div>
            </div>
        </div>
    @endif

    <x-loader />
</x-panel>
