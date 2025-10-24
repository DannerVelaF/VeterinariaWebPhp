<div>
    <x-panel title="Gestión de Mascotas" class="max-w-7xl mx-auto">
        <x-tabs :tabs="['listado' => '📋 Listado de mascotas registradas', 'registro' => '➕ Registrar nueva mascota']" default="listado">

            <!-- TAB 1: LISTADO -->
            <x-tab name="listado">
                <div class="p-4">
                    <livewire:mascotas-table />
                </div>
            </x-tab>

            <!-- TAB 2: REGISTRO -->
            <x-tab name="registro">
                <!-- MENSAJES DE ESTADO -->
                @if (session()->has('success'))
                    <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded">
                        ✅ {{ session('success') }}
                    </div>
                @endif
                @if (session()->has('error'))
                    <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                        ⚠️ {{ session('error') }}
                    </div>
                @endif

                <!-- FORMULARIO PRINCIPAL -->
                <div class="p-5 bg-gray-50 rounded shadow-sm">
                    <form wire:submit.prevent="guardarMascota" class="grid grid-cols-2 gap-5 text-sm">

                        <!-- INFORMACIÓN DEL CLIENTE -->
                        <div class="col-span-2">
                            <h3 class="font-bold text-gray-700 text-base mb-2">👤 Información del Cliente</h3>
                            <p class="text-gray-500 text-xs mb-3">Busca un cliente por su DNI o nombre para asociarlo a
                                la
                                mascota.</p>
                        </div>

                        <!-- BUSCADOR MEJORADO -->
                        <div class="col-span-2">
                            <label class="font-semibold mb-1 block">Buscar Cliente:</label>
                            <div class="relative">
                                <!-- Input con lupa -->
                                <div class="relative">
                                    <input type="text" wire:model.live.debounce.500ms="buscarCliente"
                                        placeholder="Ingrese DNI, nombre o apellido del cliente..."
                                        class="border rounded-lg px-4 py-3 w-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pl-10 pr-10">
                                    <!-- Icono de búsqueda a la izquierda -->
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <!-- Botón de búsqueda manual a la derecha -->
                                    <button type="button" wire:click="buscarClientes"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center hover:text-blue-600 transition-colors"
                                        title="Buscar cliente">
                                        <svg class="h-5 w-5 text-gray-400 hover:text-blue-600" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </button>
                                </div>

                                <!-- MENÚ DESPLEGABLE DE RESULTADOS -->
                                @if (!empty($resultadosClientes) && count($resultadosClientes) > 0)
                                    <div
                                        class="absolute z-20 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                        <div class="p-2 bg-gray-50 border-b">
                                            <p class="text-xs font-semibold text-gray-600">
                                                {{ count($resultadosClientes) }} cliente(s) encontrado(s)
                                            </p>
                                        </div>
                                        <ul>
                                            @foreach ($resultadosClientes as $cliente)
                                                <li wire:click="seleccionarCliente({{ $cliente->id_cliente }})"
                                                    class="px-4 py-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors duration-150">
                                                    <div class="flex justify-between items-start">
                                                        <div class="flex-1">
                                                            <div class="flex items-center mb-1">
                                                                <span class="font-semibold text-gray-800 text-sm">
                                                                    {{ $cliente->persona->nombre ?? $cliente->nombre }}
                                                                    {{ $cliente->persona->apellido_paterno ?? '' }}
                                                                    {{ $cliente->persona->apellido_materno ?? ($cliente->apellido ?? '') }}
                                                                </span>
                                                                <span
                                                                    class="ml-2 bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                                                    DNI: {{ $cliente->persona->dni ?? $cliente->dni }}
                                                                </span>
                                                            </div>
                                                            <div class="text-xs text-gray-600 space-y-1">
                                                                @if ($cliente->persona->telefono ?? $cliente->telefono)
                                                                    <span class="flex items-center">
                                                                        <svg class="w-3 h-3 mr-1" fill="none"
                                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round" stroke-width="2"
                                                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                                                            </path>
                                                                        </svg>
                                                                        {{ $cliente->persona->telefono ?? $cliente->telefono }}
                                                                    </span>
                                                                @endif
                                                                @if ($cliente->persona->correo ?? $cliente->correo)
                                                                    <span class="flex items-center">
                                                                        <svg class="w-3 h-3 mr-1" fill="none"
                                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round" stroke-width="2"
                                                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                                                            </path>
                                                                        </svg>
                                                                        {{ $cliente->persona->correo ?? $cliente->correo }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="ml-2 flex-shrink-0">
                                                            <svg class="w-4 h-4 text-green-500" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <!-- Mensaje cuando no hay resultados -->
                                @if ($buscarCliente && empty($resultadosClientes))
                                    <div
                                        class="absolute z-20 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg p-4">
                                        <div class="text-center text-gray-500">
                                            <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                </path>
                                            </svg>
                                            <p class="text-sm font-medium">No se encontraron clientes</p>
                                            <p class="text-xs mt-1">Intente con otro término de búsqueda</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- CLIENTE SELECCIONADO -->
                        @if ($clienteSeleccionado)
                            <div class="col-span-2 mt-3">
                                <div class="bg-green-50 border border-green-200 rounded-lg shadow-sm p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-green-600 mr-2" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <h4 class="font-bold text-green-800 text-base">Cliente Seleccionado</h4>
                                        </div>
                                        <button type="button" wire:click="limpiarCliente"
                                            class="text-red-500 text-xs font-bold hover:underline flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            Cambiar cliente
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3 text-gray-700 text-sm">
                                        <div>
                                            <p class="font-semibold text-gray-600">Nombre completo:</p>
                                            <p class="text-gray-800">
                                                {{ $clienteSeleccionado->persona->nombre ?? $clienteSeleccionado->nombre }}
                                                {{ $clienteSeleccionado->persona->apellido_paterno ?? '' }}
                                                {{ $clienteSeleccionado->persona->apellido_materno ?? ($clienteSeleccionado->apellido ?? '') }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-600">DNI:</p>
                                            <p class="text-gray-800">
                                                {{ $clienteSeleccionado->persona->dni ?? $clienteSeleccionado->dni }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-600">Teléfono:</p>
                                            <p class="text-gray-800">
                                                {{ $clienteSeleccionado->persona->telefono ?? ($clienteSeleccionado->telefono ?? 'No registrado') }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-600">Correo:</p>
                                            <p class="text-gray-800">
                                                {{ $clienteSeleccionado->persona->correo ?? ($clienteSeleccionado->correo ?? 'No registrado') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <hr class="col-span-2 my-3 border-gray-300">

                        <!-- ... (el resto del formulario de mascotas se mantiene igual) ... -->
                        <!-- INFORMACIÓN DE LA MASCOTA -->
                        <div class="col-span-2">
                            <h3 class="font-bold text-gray-700 text-base mb-2">🐾 Información de la Mascota</h3>
                            <p class="text-gray-500 text-xs mb-3">Complete los siguientes datos para registrar una
                                nueva
                                mascota.</p>
                        </div>

                        <!-- NOMBRE -->
                        <div class="flex flex-col">
                            <label class="font-semibold mb-1">Nombre de la Mascota <span
                                    class="text-red-500">*</span></label>
                            <input type="text" wire:model="mascota.nombre_mascota"
                                placeholder="Ej. Firulais, Luna, Max..."
                                class="border rounded px-3 py-2 focus:ring focus:ring-blue-300 @error('mascota.nombre_mascota') border-red-500 @enderror">
                            @error('mascota.nombre_mascota')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- RAZA -->
                        <div class="flex flex-col">
                            <label class="font-semibold mb-1">Raza <span class="text-red-500">*</span></label>
                            <select wire:model="mascota.id_raza"
                                class="border rounded px-3 py-2 focus:ring focus:ring-blue-300 @error('mascota.id_raza') border-red-500 @enderror">
                                <option value="">Seleccione raza</option>
                                @foreach ($razas as $raza)
                                    @if ($raza->estado === 'activo')
                                        <option value="{{ $raza->id_raza }}">{{ $raza->nombre_raza }}</option>
                                    @endif
                                @endforeach
                            </select>
                            @error('mascota.id_raza')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- SEXO -->
                        <div class="flex flex-col">
                            <label class="font-semibold mb-1">Sexo</label>
                            <select wire:model="mascota.sexo"
                                class="border rounded px-3 py-2 focus:ring focus:ring-blue-300">
                                <option value="">Seleccione</option>
                                <option value="Macho">Macho</option>
                                <option value="Hembra">Hembra</option>
                            </select>
                        </div>

                        <!-- FECHA DE NACIMIENTO -->
                        <div class="flex flex-col">
                            <label class="font-semibold mb-1">Fecha de Nacimiento</label>
                            <input type="date" wire:model="mascota.fecha_nacimiento"
                                class="border rounded px-3 py-2 focus:ring focus:ring-blue-300">
                        </div>

                        <!-- PESO ACTUAL -->
                        <div class="flex flex-col">
                            <label class="font-semibold mb-1">Peso actual (kg)</label>
                            <input type="number" step="0.01" min="0" wire:model="mascota.peso_actual"
                                placeholder="Ej. 5.20"
                                class="border rounded px-3 py-2 focus:ring focus:ring-blue-300">
                        </div>

                        <!-- NOMBRE -->
                        <div class="flex flex-col">
                            <label class="font-semibold mb-1">Color Primario</span></label>
                            <input type="text" wire:model="mascota.color_primario"
                                placeholder="Ej. Negro, Blanco, Marrón..."
                                class="border rounded px-3 py-2 focus:ring focus:ring-blue-300 @error('mascota.color_primario') border-red-500 @enderror">
                            @error('mascota.color_primario')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- OBSERVACIONES -->
                        <div class="flex flex-col col-span-2">
                            <label class="font-semibold mb-1">Observaciones</label>
                            <textarea wire:model="mascota.observacion" rows="3"
                                placeholder="Notas adicionales o características de la mascota..."
                                class="border rounded px-3 py-2 focus:ring focus:ring-blue-300"></textarea>
                        </div>

                        <!-- BOTONES -->
                        <div class="col-span-2 flex justify-end mt-6 space-x-3">
                            <button type="button" wire:click="resetForm"
                                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded text-xs font-bold">
                                Limpiar Formulario
                            </button>
                            <button type="button" wire:click="agregarOtraMascota"
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-xs font-bold">
                                Agregar Otra Mascota
                            </button>
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-xs font-bold">
                                Registrar Mascota
                            </button>
                        </div>
                    </form>
                </div>
            </x-tab>
        </x-tabs>

        @if ($modalEditar)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data="{ show: true }"
                x-show="show" x-transition.opacity.duration.200ms>
                <div class="absolute inset-0 bg-black opacity-50" @click="$wire.cerrarModal()"></div>

                <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-2xl z-50 overflow-hidden">
                    <!-- Cabecera -->
                    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-blue-100 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path
                                        d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z" />
                                    <path d="m15 5 4 4" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg text-gray-800">Editar Mascota</h3>
                                <p class="text-gray-600 text-sm mt-1">Actualiza la información de la mascota</p>
                            </div>
                        </div>
                        <button @click="$wire.cerrarModal()" class="text-gray-500 hover:text-gray-700">✕</button>
                    </div>

                    <!-- Formulario -->
                    <form wire:submit.prevent="guardarEdicion" class="p-6 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <!-- Raza -->
                            <div>
                                <label class="text-sm font-medium text-gray-700 mb-2">Raza</label>
                                <select wire:model="mascotaEditar.id_raza"
                                    class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                    <option value="">Seleccione una raza</option>
                                    @foreach ($razas as $raza)
                                        <option value="{{ $raza->id_raza }}">{{ $raza->nombre_raza }}</option>
                                    @endforeach
                                </select>
                                @error('mascotaEditar.id_raza')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Nombre -->
                            <div>
                                <label class="text-sm font-medium text-gray-700 mb-2">Nombre</label>
                                <input type="text" wire:model="mascotaEditar.nombre_mascota"
                                    class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                @error('mascotaEditar.nombre_mascota')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Fecha de nacimiento -->
                            <div>
                                <label class="text-sm font-medium text-gray-700 mb-2">Fecha de nacimiento</label>
                                <input type="date" wire:model="mascotaEditar.fecha_nacimiento"
                                    class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                            </div>

                            <!-- Color primario -->
                            <div>
                                <label class="text-sm font-medium text-gray-700 mb-2">Color primario</label>
                                <input type="text" wire:model="mascotaEditar.color_primario"
                                    class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                            </div>

                            <!-- Sexo -->
                            <div>
                                <label class="text-sm font-medium text-gray-700 mb-2">Sexo</label>
                                <select wire:model="mascotaEditar.sexo"
                                    class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                    <option value="">Seleccione</option>
                                    <option value="macho">Macho</option>
                                    <option value="hembra">Hembra</option>
                                </select>
                                @error('mascotaEditar.sexo')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Estado -->
                            <div>
                                <label class="text-sm font-medium text-gray-700 mb-2">Estado</label>
                                <select wire:model="mascotaEditar.estado"
                                    class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="flex justify-end space-x-2 mt-6 pt-4 border-t border-gray-200">
                            <button type="button" @click="$wire.cerrarModal()"
                                class="px-4 py-2 text-sm text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                                Cancelar
                            </button>
                            <button type="submit"
                                class="px-4 py-2 text-sm text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                                Guardar cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

    </x-panel>
    <x-loader />
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
    <script>
        Livewire.on('scrollToTop', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    </script>
</div>
