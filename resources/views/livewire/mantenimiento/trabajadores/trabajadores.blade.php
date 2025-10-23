<x-panel title="Gestión de trabajadores" :breadcrumbs="[
    ['label' => 'Inicio', 'href' => '/', 'icon' => 'home'],
    ['label' => 'Mantenimiento', 'href' => '#'],
    ['label' => 'Gestión de trabajadores'],
]">
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
                                <option value="{{ $p->id_puesto_trabajo }}">{{ $p->nombre_puesto }}</option>
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
    @if ($modalEditar)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 animate-fadeIn">
            <!-- Overlay -->
            <div class="absolute inset-0 bg-black/60" wire:click="$set('modalEditar', false)"></div>

            <!-- Modal -->
            <div
                class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl z-10 overflow-hidden animate-slideUp">

                <!-- Header -->
                <div class="px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">

                        <div>
                            <h3 class="text-lg font-medium text-gray-700">Editar información de Trabajador</h3>
                            <p class="text-gray-500"> Trabajador:
                                {{ $trabajadorSeleccionado->persona?->nombre }}
                                {{ $trabajadorSeleccionado->persona?->apellido_paterno }}
                                {{ $trabajadorSeleccionado->persona?->apellido_materno }}</p>
                        </div>
                    </div>
                    <button wire:click="cerrarModal"
                        class="text-white/80 hover:text-white hover:bg-blue-600 rounded-lg p-2 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="white" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18" />
                            <line x1="6" y1="6" x2="18" y2="18" />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="overflow-y-auto max-h-[calc(90vh-180px)] p-6 bg-gray-50">
                    <form class="space-y-6">

                        <!-- Información Personal -->
                        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
                            <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-200">
                                <div class="bg-gray-200 p-2 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 24 24" fill="none" stroke="gray" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                                        <circle cx="12" cy="7" r="4" />
                                    </svg>
                                </div>
                                <h4 class="font-bold text-gray-800 text-lg">Información Personal</h4>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex flex-col">
                                    <label class="text-sm font-semibold text-gray-700 mb-1.5">Nombre</label>
                                    <input readonly type="text" wire:model="persona.nombre"
                                        class="border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                </div>

                                <div class="flex flex-col">
                                    <label class="text-sm font-semibold text-gray-700 mb-1.5">Apellido Paterno</label>
                                    <input readonly type="text" wire:model="persona.apellido_paterno"
                                        class="border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                </div>

                                <div class="flex flex-col">
                                    <label class="text-sm font-semibold text-gray-700 mb-1.5">Apellido Materno</label>
                                    <input readonly type="text" wire:model="persona.apellido_materno"
                                        class="border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                </div>

                                <div class="flex flex-col">
                                    <label class="text-sm font-semibold text-gray-700 mb-1.5">Correo Personal</label>
                                    <input type="email" wire:model="persona.correo_electronico_personal"
                                        class="border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                </div>

                                <div class="flex flex-col">
                                    <label class="text-sm font-semibold text-gray-700 mb-1.5">Correo Secundario</label>
                                    <input type="email" wire:model="persona.correo_electronico_secundario"
                                        class="border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                </div>

                                <div class="flex flex-col">
                                    <label class="text-sm font-semibold text-gray-700 mb-1.5">Teléfono Personal</label>
                                    <input type="text" wire:model="persona.numero_telefono_personal"
                                        class="border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                </div>

                                <div class="flex flex-col">
                                    <label class="text-sm font-semibold text-gray-700 mb-1.5">Teléfono
                                        Secundario</label>
                                    <input type="text" wire:model="persona.numero_telefono_secundario"
                                        class="border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                </div>
                            </div>
                        </div>

                        <!-- Información Laboral -->
                        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
                            <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-200">
                                <div class="bg-gray-200 p-2 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 24 24" fill="none" stroke="gray" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <rect width="20" height="14" x="2" y="6" rx="2" />
                                        <path d="M12 12h.01" />
                                        <path d="M16 6V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" />
                                    </svg>
                                </div>
                                <h4 class="font-bold text-gray-800 text-lg">Información Laboral</h4>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex flex-col">
                                    <label class="text-sm font-semibold text-gray-700 mb-1.5">Salario</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">S/</span>
                                        <input type="number" step="0.01" wire:model="trabajador.salario"
                                            class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                    </div>
                                </div>

                                <div class="flex flex-col">
                                    <label class="text-sm font-semibold text-gray-700 mb-1.5">Seguro Social</label>
                                    <input type="text" wire:model="trabajador.numero_seguro_social"
                                        class="border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                </div>

                                <div class="flex flex-col">
                                    <label class="text-sm font-semibold text-gray-700 mb-1.5">Puesto</label>
                                    <select wire:model="puestoNuevo"
                                        class="border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                        <option value="">Seleccione...</option>
                                        @foreach ($puestos as $puesto)
                                            <option value="{{ $puesto->id_puesto_trabajo }}">
                                                {{ $puesto->nombre_puesto }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="flex flex-col">
                                    <label class="text-sm font-semibold text-gray-700 mb-1.5">Estado</label>
                                    <select wire:model="estadoNuevo"
                                        class="border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                        <option value="">Seleccione...</option>
                                        @foreach ($estados as $estado)
                                            <option value="{{ $estado->id_estado_trabajador }}">
                                                {{ $estado->nombre_estado_trabajador }}</option>
                                        @endforeach
                                    </select>
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
                                    <label class="text-sm font-semibold text-gray-700 mb-1.5">Zona</label>
                                    <input type="text" wire:model="direccion.zona"
                                        class="border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                </div>

                                <div class="flex flex-col">
                                    <label class="text-sm font-semibold text-gray-700 mb-1.5">Tipo de Calle</label>
                                    <input type="text" wire:model="direccion.tipo_calle"
                                        class="border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                </div>

                                <div class="flex flex-col">
                                    <label class="text-sm font-semibold text-gray-700 mb-1.5">Nombre de Calle</label>
                                    <input type="text" wire:model="direccion.nombre_calle"
                                        class="border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                </div>

                                <div class="flex flex-col">
                                    <label class="text-sm font-semibold text-gray-700 mb-1.5">Número</label>
                                    <input type="text" wire:model="direccion.numero"
                                        class="border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                    <button wire:click="cerrarModal"
                        class="px-5 py-2.5 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold transition-all hover:shadow-md">
                        Cancelar
                    </button>
                    <button wire:click="guardarEdicion"
                        class="px-5 py-2.5 rounded-lg bg-black hover:bg-black/80' text-white font-semibold transition-all shadow-lg hover:shadow-xl">
                        Guardar Cambios
                    </button>
                </div>
            </div>
        </div>

        <!-- Animaciones -->
        <style>
            @keyframes fadeIn {
                from {
                    opacity: 0;
                }

                to {
                    opacity: 1;
                }
            }

            @keyframes slideUp {
                from {
                    opacity: 0;
                    transform: translateY(20px) scale(0.95);
                }

                to {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }

            .animate-fadeIn {
                animation: fadeIn 0.2s ease-out;
            }

            .animate-slideUp {
                animation: slideUp 0.3s ease-out;
            }
        </style>
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
    <x-loader />
</x-panel>
