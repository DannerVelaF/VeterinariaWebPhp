<x-panel title="Gestión de clientes" :breadcrumbs="[
    ['label' => 'Inicio', 'href' => '/', 'icon' => 'home'],
    ['label' => 'Mantenimiento', 'href' => '#'],
    ['label' => 'Gestión de clientes'],
]">
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
                        @error('persona.id_tipo_documento')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex flex-col">
                        <label>Numero documento <span class="text-red-500">*</span></label>
                        <div class="flex items-center">
                            <input type="text" wire:model.change="persona.numero_documento"
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
                        <input type="text" wire:model.change="persona.nombre" class="border rounded px-2 py-1"
                            @readonly($persona['id_tipo_documento'] == 1)>
                        @error('persona.nombre')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label>Apellido Paterno <span class="text-red-500">*</span></label>
                        <input type="text" wire:model.change="persona.apellido_paterno"
                            class="border rounded px-2 py-1" @readonly($persona['id_tipo_documento'] == 1)>
                        @error('persona.apellido_paterno')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label>Apellido Materno</label>
                        <input type="text" wire:model.change="persona.apellido_materno"
                            class="border rounded px-2 py-1" @readonly($persona['id_tipo_documento'] == 1)>
                        @error('persona.apellido_materno')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label>Fecha Nacimiento <span class="text-red-500">*</span></label>
                        <input type="date" wire:model.change="persona.fecha_nacimiento"
                            class="border rounded px-2 py-1">
                        @error('persona.fecha_nacimiento')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label>Sexo <span class="text-red-500">*</span></label>
                        <select wire:model.change="persona.sexo" class="border rounded px-2 py-1">
                            <option value="">-- Seleccione --</option>
                            <option value="M">Masculino</option>
                            <option value="F">Femenino</option>
                            <option value="O">Otro</option>
                        </select>
                        @error('persona.sexo')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex flex-col">
                        <label>Nacionalidad <span class="text-red-500">*</span></label>
                        <input type="text" wire:model.change="persona.nacionalidad" class="border rounded px-2 py-1">
                        @error('persona.nacionalidad')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex flex-col">
                        <label>Correo electronico personal <span class="text-red-500">*</span></label>
                        <input type="text" wire:model.change="persona.correo_electronico_personal"
                            class="border rounded px-2 py-1">
                        @error('persona.correo_electronico_personal')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label>Numero telefono personal <span class="text-red-500">*</span></label>
                        <input type="text" wire:model.change="persona.numero_telefono_personal"
                            class="border rounded px-2 py-1">
                        @error('persona.numero_telefono_personal')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label>Correo electronico secundario</label>
                        <input type="text" wire:model.change="persona.correo_electronico_secundario"
                            class="border rounded px-2 py-1">
                        @error('persona.correo_electronico_secundario')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label>Numero telefono secundario</label>
                        <input type="text" wire:model.change="persona.numero_telefono_secundario"
                            class="border rounded px-2 py-1">
                        @error('persona.numero_telefono_secundario')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- ====== DATOS DE DIRECCIÓN ====== -->
                    <div class="col-span-2 mt-4">
                        <p class="font-bold text-gray-700 mb-3">📍 Dirección <span class="text-red-500">*</span></p>
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
                                <label for="nombre_calle" class="font-bold mb-1">Nombre de Calle <span
                                        class="text-red-500">*</span></label>
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
                                <label for="numero" class="font-bold mb-1">Número <span
                                        class="text-red-500">*</span></label>
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
                                <label for="zona" class="font-bold mb-1">Zona <span
                                        class="text-red-500">*</span></label>
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
                                <label for="codigo_postal" class="font-bold mb-1">Código Postal <span
                                        class="text-red-500">*</span></label>
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
                                    class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300">
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
                                    class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300"
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
    <flux:modal name="editarCliente" class="w-full max-w-4xl h-[80vh]" wire:model="modalEditar">
        <!-- Header -->
        <div class="px-6 py-4 flex items-center justify-between border-b border-gray-200">
            <div class="flex items-center gap-3">
                <div>
                    <h3 class="text-lg font-medium text-gray-700">Editar información de Cliente</h3>
                    <p class="text-gray-500">
                        Cliente:
                        @if ($clienteSeleccionado && $clienteSeleccionado->persona)
                            {{ $clienteSeleccionado->persona->nombre }}
                            {{ $clienteSeleccionado->persona->apellido_paterno }}
                            {{ $clienteSeleccionado->persona->apellido_materno }}
                        @else
                            Cargando...
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Body -->
        <div class="overflow-y-auto p-6 bg-gray-50">
            @if ($clienteSeleccionado && $clienteSeleccionado->persona)
                <form class="space-y-6" wire:submit.prevent="guardarEdicion">

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
                            <!-- Campos de solo lectura -->
                            <div class="flex flex-col">
                                <flux:label class="mb-1.5">Tipo de documento</flux:label>
                                <flux:select wire:model="persona.id_tipo_documento" disabled>
                                    <option value="">-- Seleccione --</option>
                                    @foreach ($tipos_documentos as $t)
                                        <option value="{{ $t->id_tipo_documento }}">{{ $t->nombre_tipo_documento }}
                                        </option>
                                    @endforeach
                                </flux:select>
                                <p class="text-xs text-gray-500 mt-1">No editable</p>
                            </div>

                            <div class="flex flex-col">
                                <flux:label class="mb-1.5">Número documento</flux:label>
                                <flux:input readonly wire:model="persona.numero_documento" />
                                <p class="text-xs text-gray-500 mt-1">No editable</p>
                            </div>

                            <div class="flex flex-col">
                                <flux:label class="mb-1.5">Nombre</flux:label>
                                <flux:input readonly wire:model="persona.nombre" />
                                <p class="text-xs text-gray-500 mt-1">No editable</p>
                            </div>

                            <div class="flex flex-col">
                                <flux:label class="mb-1.5">Apellido Paterno</flux:label>
                                <flux:input readonly wire:model="persona.apellido_paterno" />
                                <p class="text-xs text-gray-500 mt-1">No editable</p>
                            </div>

                            <div class="flex flex-col">
                                <flux:label class="mb-1.5">Apellido Materno</flux:label>
                                <flux:input readonly wire:model="persona.apellido_materno" />
                                <p class="text-xs text-gray-500 mt-1">No editable</p>
                            </div>

                            <!-- Campos editables -->
                            <div class="flex flex-col">
                                <flux:label class="mb-1.5">Fecha de nacimiento</flux:label>
                                <flux:input type="date" wire:model.change="persona.fecha_nacimiento" />
                                @error('persona.fecha_nacimiento')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="flex flex-col">
                                <flux:label class="mb-1.5">Sexo</flux:label>
                                <flux:select wire:model.change="persona.sexo">
                                    <option value="">-- Seleccione --</option>
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                    <option value="O">Otro</option>
                                </flux:select>
                                @error('persona.sexo')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="flex flex-col">
                                <flux:label class="mb-1.5">Nacionalidad</flux:label>
                                <flux:input type="text" wire:model.change="persona.nacionalidad" />
                                @error('persona.nacionalidad')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="flex flex-col">
                                <flux:label class="mb-1.5">Correo electrónico personal</flux:label>
                                <flux:input type="email" wire:model.change="persona.correo_electronico_personal" />
                                @error('persona.correo_electronico_personal')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="flex flex-col">
                                <flux:label class="mb-1.5">Correo electrónico secundario</flux:label>
                                <flux:input type="email"
                                    wire:model.change="persona.correo_electronico_secundario" />
                                @error('persona.correo_electronico_secundario')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="flex flex-col">
                                <flux:label class="mb-1.5">Teléfono personal</flux:label>
                                <flux:input type="text" wire:model.change="persona.numero_telefono_personal" />
                                @error('persona.numero_telefono_personal')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="flex flex-col">
                                <flux:label class="mb-1.5">Teléfono secundario</flux:label>
                                <flux:input type="text" wire:model.change="persona.numero_telefono_secundario" />
                                @error('persona.numero_telefono_secundario')
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
                                <flux:label class="mb-1.5">Tipo de Calle</flux:label>
                                <flux:select wire:model.change="direccion.tipo_calle">
                                    <option value="">-- Seleccione --</option>
                                    @foreach ($tipos_calle as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </flux:select>
                                @error('direccion.tipo_calle')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="flex flex-col">
                                <flux:label class="mb-1.5">Zona</flux:label>
                                <flux:input type="text" wire:model.change="direccion.zona" />
                                @error('direccion.zona')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="flex flex-col">
                                <flux:label class="mb-1.5">Nombre de Calle</flux:label>
                                <flux:input type="text" wire:model.change="direccion.nombre_calle" />
                                @error('direccion.nombre_calle')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="flex flex-col">
                                <flux:label class="mb-1.5">Número</flux:label>
                                <flux:input type="text" wire:model.change="direccion.numero" />
                                @error('direccion.numero')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="flex flex-col">
                                <flux:label class="mb-1.5">Código Postal</flux:label>
                                <flux:input type="text" wire:model.change="direccion.codigo_postal" />
                                @error('direccion.codigo_postal')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="flex flex-col">
                                <flux:label class="mb-1.5">Referencia</flux:label>
                                <flux:input type="text" wire:model.change="direccion.referencia" />
                                @error('direccion.referencia')
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
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="flex flex-col">
                                <flux:label class="mb-1.5">Departamento</flux:label>
                                <flux:select wire:model.live="departamentoSeleccionado">
                                    <option value="">-- Seleccione --</option>
                                    @foreach ($departamentos as $dep)
                                        <option value="{{ $dep }}">{{ $dep }}</option>
                                    @endforeach
                                </flux:select>
                            </div>

                            <div class="flex flex-col">
                                <flux:label class="mb-1.5">Provincia</flux:label>
                                <flux:select wire:model.live="provinciaSeleccionada"
                                    :disabled="empty($departamentoSeleccionado)">
                                    <option value="">-- Seleccione --</option>
                                    @foreach ($provincias as $prov)
                                        <option value="{{ $prov }}">{{ $prov }}</option>
                                    @endforeach
                                </flux:select>
                            </div>

                            <div class="flex flex-col">
                                <flux:label class="mb-1.5">Distrito</flux:label>
                                <flux:select wire:model.change="direccion.codigo_ubigeo"
                                    :disabled="empty($provinciaSeleccionada)">
                                    <option value="">-- Seleccione --</option>
                                    @foreach ($distritos as $dis)
                                        <option value="{{ $dis->codigo_ubigeo }}">{{ $dis->distrito }}</option>
                                    @endforeach
                                </flux:select>
                                @error('direccion.codigo_ubigeo')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </form>
            @else
                <!-- Loading state -->
                <div class="flex items-center justify-center py-12">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                        <p class="mt-4 text-gray-600">Cargando datos del cliente...</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Footer -->
        @if ($clienteSeleccionado && $clienteSeleccionado->persona)
            <div slot="footer" class="flex justify-end gap-3">
                <flux:button wire:click.prevent="cerrarModal">
                    Cancelar
                </flux:button>
                <flux:button wire:click="guardarEdicion" :disabled="$loading" variant="primary">
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
    <x-loader target="guardar, guardarEdicion, buscarDni, cerrarModal" />
</x-panel>
