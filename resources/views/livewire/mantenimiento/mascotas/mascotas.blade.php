<x-panel title="Gestión de Mascotas">
    <x-tabs :tabs="['listado' => '📋 Detalle de mascotas registradas', 'registro' => '➕ Registrar nueva mascota']" default="listado">
        <!-- TAB 1: LISTADO -->
        <x-tab name="listado">
            <div class="p-4">
                <livewire:mascotas-table />
            </div>
        </x-tab>

        <!-- TAB 2: REGISTRO -->
        <x-tab name="registro">
            <!-- MENSAJES DE ALERTA -->
            @if (session()->has('success'))
                <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded" x-data="{ show: true }"
                    x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 transform translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0">
                    {{ session('success') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded" x-data="{ show: true }"
                    x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 transform translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0">
                    {{ session('error') }}
                </div>
            @endif

            <div class="p-3 bg-gray-50 rounded">
                <form wire:submit.prevent="guardar" class="grid grid-cols-2 gap-4 text-xs">

                    <!-- SECCIÓN DE DATOS DE LA MASCOTA -->
                    <div class="col-span-2">
                        <p class="font-bold text-gray-700 mb-3">🐶 Información de la Mascota</p>
                    </div>

                    <div class="flex flex-col">
                        <label for="nombre_mascota" class="font-bold mb-1">Nombre de la Mascota <span class="text-red-500">*</span></label>
                        <input type="text" id="nombre_mascota" wire:model="mascota.nombre_mascota"
                            class="border rounded px-2 py-1 focus:ring focus:ring-blue-300 @error('mascota.nombre_mascota') border-red-500 @enderror"
                            placeholder="Ej. Firulais">
                        @error('mascota.nombre_mascota') 
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> 
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label for="cliente" class="font-bold mb-1">Propietario <span class="text-red-500">*</span></label>
                        <select id="cliente" wire:model="mascota.id_cliente"
                            class="border rounded px-2 py-1 focus:ring focus:ring-blue-300 @error('mascota.id_cliente') border-red-500 @enderror">
                            <option value="">-- Seleccione propietario --</option>
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente->id_cliente }}">{{ $cliente->nombre_cliente }}</option>
                            @endforeach
                        </select>
                        @error('mascota.id_cliente')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label for="raza" class="font-bold mb-1">Raza <span class="text-red-500">*</span></label>
                        <select id="raza" wire:model="mascota.id_raza"
                            class="border rounded px-2 py-1 focus:ring focus:ring-blue-300 @error('mascota.id_raza') border-red-500 @enderror">
                            <option value="">-- Seleccione raza --</option>
                            @foreach ($razas as $raza)
                                <option value="{{ $raza->id_raza }}">{{ $raza->nombre_raza }}</option>
                            @endforeach
                        </select>
                        @error('mascota.id_raza')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label for="sexo" class="font-bold mb-1">Sexo <span class="text-red-500">*</span></label>
                        <select id="sexo" wire:model="mascota.sexo"
                            class="border rounded px-2 py-1 focus:ring focus:ring-blue-300 @error('mascota.sexo') border-red-500 @enderror">
                            <option value="">-- Seleccione sexo --</option>
                            <option value="Macho">Macho</option>
                            <option value="Hembra">Hembra</option>
                        </select>
                        @error('mascota.sexo')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label for="fecha_nacimiento" class="font-bold mb-1">Fecha de Nacimiento</label>
                        <input type="date" id="fecha_nacimiento" wire:model="mascota.fecha_nacimiento"
                            class="border rounded px-2 py-1 focus:ring focus:ring-blue-300">
                    </div>

                    <div class="flex flex-col">
                        <label for="color_primario" class="font-bold mb-1">Color Primario</label>
                        <input type="text" id="color_primario" wire:model="mascota.color_primario"
                            class="border rounded px-2 py-1 focus:ring focus:ring-blue-300"
                            placeholder="Ej. Blanco, marrón, negro...">
                    </div>

                    <div class="flex flex-col">
                        <label for="peso_actual" class="font-bold mb-1">Peso Actual (kg)</label>
                        <input type="number" step="0.1" id="peso_actual" wire:model="mascota.peso_actual"
                            class="border rounded px-2 py-1 focus:ring focus:ring-blue-300"
                            placeholder="Ej. 5.2">
                    </div>

                    <div class="flex flex-col col-span-2">
                        <label for="observacion" class="font-bold mb-1">Observaciones</label>
                        <textarea id="observacion" wire:model="mascota.observacion" rows="3"
                            class="border rounded px-2 py-1 focus:ring focus:ring-blue-300"
                            placeholder="Ej. Mascota tímida, necesita atención especial..."></textarea>
                    </div>

                    <!-- Botones -->
                    <div class="col-span-2 flex justify-end mt-6 space-x-2">
                        <button type="button" wire:click="resetForm"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded text-xs font-bold transition-colors">
                            Limpiar Formulario
                        </button>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-xs font-bold transition-colors">
                            Registrar Mascota
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
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-pencil">
                        <path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z" />
                        <path d="m15 5 4 4" />
                    </svg>
                    Editar Mascota
                </h2>

                <form wire:submit.prevent="actualizarMascota" class="grid grid-cols-2 gap-4 text-xs">
                    <div class="flex flex-col col-span-2">
                        <label class="font-bold mb-1">Nombre</label>
                        <input type="text" wire:model="mascotaEditar.nombre_mascota"
                            class="border rounded px-2 py-1 focus:ring focus:ring-blue-300">
                    </div>

                    <div class="flex flex-col col-span-2">
                        <label class="font-bold mb-1">Observaciones</label>
                        <textarea wire:model="mascotaEditar.observacion" rows="3"
                            class="border rounded px-2 py-1 focus:ring focus:ring-blue-300"></textarea>
                    </div>

                    <div class="col-span-2 flex justify-end space-x-2 mt-4">
                        <button type="button" wire:click="$set('modalEditar', false)"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded text-xs font-bold">Cancelar</button>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-xs font-bold">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <div wire:loading wire:target="guardar | actualizarMascota">
        <x-loader />
    </div>
</x-panel>
