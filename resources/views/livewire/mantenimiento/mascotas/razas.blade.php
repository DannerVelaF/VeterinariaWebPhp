<x-panel title="Gestión de Razas de Mascotas" class="max-w-7xl mx-auto" :breadcrumbs="[
    ['label' => 'Inicio', 'href' => '/', 'icon' => 'home'],
    ['label' => 'Clientes', 'href' => route('mantenimiento.clientes'), 'icon' => 'ellipsis-horizontal'],
    ['label' => 'Gestión de Razas', 'href' => route('mantenimiento.clientes.razas')],
]">
    <x-tabs :tabs="['listado' => '📋 Detalle de razas registradas', 'registro' => '➕ Registrar nueva raza']" default="listado">

        <!-- TAB 1: LISTADO -->
        <x-tab name="listado">
            <div class="p-4">
                <livewire:raza-table />
            </div>
        </x-tab>

        <!-- TAB 2: REGISTRO -->
        <x-tab name="registro">
            <!-- Mensajes de éxito -->
            @if (session()->has('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                    x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 transform translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-500"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform translate-y-2"
                    class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Mensajes de error -->
            @if (session()->has('error'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                    x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 transform translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-500"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform translate-y-2"
                    class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="p-3 bg-gray-50 rounded">
                <form wire:submit.prevent="guardarRaza" class="grid grid-cols-2 gap-4 text-xs">
                    <!-- ====== INFORMACIÓN DE LA RAZA ====== -->
                    <div class="col-span-2">
                        <p class="font-bold text-gray-700 mb-3">🐕 Información de la raza</p>
                    </div>

                    <div class="flex flex-col">
                        <label for="nombre_raza" class="font-bold mb-1">Nombre de la raza <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="nombre_raza" name="nombre_raza" maxlength="255"
                            class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 @error('raza.nombre_raza') border-red-500 @enderror"
                            placeholder="Ej. Labrador, Persa, Pastor Alemán..." wire:model.change="raza.nombre_raza">
                        @error('raza.nombre_raza')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label for="id_especie" class="font-bold mb-1">
                            Especie <span class="text-red-500">*</span>
                        </label>
                        <select id="id_especie" name="id_especie"
                            class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300
                            @error('raza.id_especie') border-red-500 @enderror"
                            wire:model="raza.id_especie">
                            <option value="">-- Seleccione una especie --</option>
                            @foreach ($especies as $especie)
                                <option value="{{ $especie->id_especie }}">{{ $especie->nombre_especie }}</option>
                            @endforeach
                        </select>
                        @error('raza.id_especie')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col col-span-2">
                        <label for="descripcion" class="font-bold mb-1">Descripción</label>
                        <textarea id="descripcion" name="descripcion" rows="4" maxlength="1000"
                            class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 @error('raza.descripcion') border-red-500 @enderror"
                            placeholder="Describe brevemente esta raza..." wire:model.change="raza.descripcion"></textarea>
                        @error('raza.descripcion')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                        <div class="text-right text-xs text-gray-500 mt-1">
                            {{ strlen($raza['descripcion']) }}/1000 caracteres
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="col-span-2 flex justify-end mt-6 space-x-2">
                        <button type="button" wire:click="resetForm"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded text-xs font-bold transition-colors">
                            Limpiar Formulario
                        </button>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-xs font-bold transition-colors">
                            Registrar Raza
                        </button>
                    </div>
                </form>
            </div>
        </x-tab>
    </x-tabs>

    <!-- MODAL EDITAR -->
    @if ($modalEditar)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data="{ show: true }" x-show="show"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">

            <!-- Overlay difuminado -->
            <div class="absolute inset-0 bg-black opacity-50" @click="$wire.cerrarModal()"></div>

            <!-- Contenido del modal -->
            <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md z-50 overflow-hidden">
                <!-- Header del modal -->
                <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                    <div class="flex justify-between items-center">
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
                                <h3 class="font-bold text-lg text-gray-800">Editar Raza</h3>
                                <p class="text-gray-600 text-sm mt-1">Actualiza la información de la raza</p>
                            </div>
                        </div>
                        <button @click="$wire.cerrarModal()"
                            class="text-gray-500 hover:text-gray-700 transition-colors p-1 rounded-full hover:bg-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Mensajes de error -->
                @if (session()->has('error'))
                    <div class="mx-6 mt-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm"
                        x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ session('error') }}
                        </div>
                    </div>
                @endif

                <!-- Formulario -->
                <form wire:submit.prevent="guardarEdicion" class="p-6">
                    <div class="space-y-4">
                        <!-- Campo Nombre -->
                        <div class="flex flex-col">
                            <label class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                                <span>Nombre de la raza</span>
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <input readonly type="text" wire:model.change="razaEditar.nombre_raza"
                                class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors placeholder-gray-400"
                                placeholder="Ingresa el nombre de la raza">
                            @error('razaEditar.nombre_raza')
                                <p class="text-red-500 text-xs mt-1 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Campo Descripción -->
                        <div class="flex flex-col">
                            <label class="text-sm font-medium text-gray-700 mb-2">Descripción</label>
                            <textarea wire:model.change="razaEditar.descripcion" rows="4"
                                class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors placeholder-gray-400 resize-none"
                                placeholder="Describe las características de la raza..."></textarea>
                            @error('razaEditar.descripcion')
                                <p class="text-red-500 text-xs mt-1 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <!-- Acciones del modal -->
                    <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
                        <button type="button" @click="$wire.cerrarModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span>Actualizar</span>
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
    <x-loader target="guardarRaza, guardarEdicion" />
</x-panel>
