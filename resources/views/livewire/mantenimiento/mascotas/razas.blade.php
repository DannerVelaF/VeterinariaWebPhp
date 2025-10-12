<x-panel title="Gestión de Razas">
    <x-tabs :tabs="['listado' => '📋 Detalle de razas registradas', 'registro' => '➕ Registrar nueva raza']" default="listado">
        
        <!-- TAB 1: LISTADO -->
        <x-tab name="listado">
            <div class="p-4">
                <livewire:razas-table />
            </div>
        </x-tab>

        <!-- TAB 2: REGISTRO -->
        <x-tab name="registro">
            <!-- Mensajes de éxito -->
            @if (session()->has('success'))
                <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded"
                    x-data="{ show: true }" x-show="show"
                    x-init="setTimeout(() => show = false, 4000)"
                    x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-500"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-2">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Mensajes de error -->
            @if (session()->has('error'))
                <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded"
                    x-data="{ show: true }" x-show="show"
                    x-init="setTimeout(() => show = false, 4000)"
                    x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-500"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-2">
                    {{ session('error') }}
                </div>
            @endif

            <!-- FORMULARIO DE REGISTRO -->
            <div class="p-3 bg-gray-50 rounded">
                <form wire:submit.prevent="guardar" class="grid grid-cols-2 gap-4 text-xs">
                    <div class="col-span-2">
                        <p class="font-bold text-gray-700 mb-3">🐶 Información de la Raza</p>
                    </div>

                    <div class="flex flex-col col-span-2">
                        <label for="nombre_raza" class="font-bold mb-1">Nombre de la raza <span class="text-red-500">*</span></label>
                        <input type="text" id="nombre_raza" wire:model="raza.nombre_raza"
                            placeholder="Ejemplo: Labrador Retriever"
                            class="border rounded px-2 py-1 focus:ring focus:ring-blue-300 @error('raza.nombre_raza') border-red-500 @enderror">
                        @error('raza.nombre_raza')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col col-span-2">
                        <label for="id_especie" class="font-bold mb-1">Especie <span class="text-red-500">*</span></label>
                        <select id="id_especie" wire:model="raza.id_especie"
                            class="border rounded px-2 py-1 focus:ring focus:ring-blue-300 @error('raza.id_especie') border-red-500 @enderror">
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
                        <textarea id="descripcion" wire:model="raza.descripcion" rows="4" maxlength="500"
                            placeholder="Descripción breve de la raza..."
                            class="border rounded px-2 py-1 focus:ring focus:ring-blue-300 @error('raza.descripcion') border-red-500 @enderror"></textarea>
                        @error('raza.descripcion')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                        <div class="text-right text-xs text-gray-500 mt-1">
                            {{ strlen($raza['descripcion']) }}/500 caracteres
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="col-span-2 flex justify-end mt-6 space-x-2">
                        <button type="button" wire:click="resetForm"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded text-xs font-bold">
                            Limpiar
                        </button>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-xs font-bold">
                            Registrar Raza
                        </button>
                    </div>
                </form>
            </div>
        </x-tab>
    </x-tabs>

    <!-- MODAL DE EDICIÓN -->
    @if ($modalEditar)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black opacity-50" wire:click="$set('modalEditar', false)"></div>

            <div class="relative bg-white rounded-md p-6 w-1/3 z-10 overflow-y-auto max-h-[90vh]">
                <h2 class="text-lg font-bold mb-4 flex gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-pencil">
                        <path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/>
                        <path d="m15 5 4 4"/>
                    </svg>
                    Editar Raza
                </h2>

                <form wire:submit.prevent="actualizarRaza" class="grid grid-cols-2 gap-4 text-xs">
                    <div class="flex flex-col col-span-2">
                        <label class="font-bold mb-1">Nombre</label>
                        <input type="text" wire:model="razaEditar.nombre_raza"
                            class="border rounded px-2 py-1 focus:ring focus:ring-blue-300">
                    </div>

                    <div class="flex flex-col col-span-2">
                        <label class="font-bold mb-1">Especie</label>
                        <select wire:model="razaEditar.id_especie"
                            class="border rounded px-2 py-1 focus:ring focus:ring-blue-300">
                            <option value="">-- Seleccione especie --</option>
                            @foreach ($especies as $especie)
                                <option value="{{ $especie->id_especie }}">{{ $especie->nombre_especie }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col col-span-2">
                        <label class="font-bold mb-1">Descripción</label>
                        <textarea wire:model="razaEditar.descripcion" rows="3"
                            class="border rounded px-2 py-1 focus:ring focus:ring-blue-300"></textarea>
                    </div>

                    <div class="col-span-2 flex justify-end space-x-2 mt-4">
                        <button type="button" wire:click="$set('modalEditar', false)"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded text-xs font-bold">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-xs font-bold">
                            Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- LOADER -->
    <div wire:loading wire:target="guardar,actualizarRaza">
        <x-loader />
    </div>
</x-panel>
