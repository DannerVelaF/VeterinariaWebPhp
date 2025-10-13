<x-panel title="Gestión de Servicios">
    <x-tabs :tabs="['listado' => '📋 Detalle de servicios registrados', 'registro' => '➕ Registrar nuevo servicio']" default="listado">

        <!-- TAB 1: LISTADO -->
        <x-tab name="listado">
            <div class="p-4">
                <livewire:servicio-table />
            </div>
        </x-tab>

        <!-- TAB 2: REGISTRO -->
        <x-tab name="registro">
            <!-- Mensajes de éxito y error -->
            @if (session()->has('success'))
                <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded" 
                    x-data="{ show: true }" x-show="show" 
                    x-init="setTimeout(() => show = false, 4000)" 
                    x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 transform translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-500"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform translate-y-2">
                    {{ session('success') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded" 
                    x-data="{ show: true }" x-show="show" 
                    x-init="setTimeout(() => show = false, 4000)" 
                    x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 transform translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-500"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform translate-y-2">
                    {{ session('error') }}
                </div>
            @endif

            <!-- FORMULARIO DE REGISTRO -->
            <div class="p-3 bg-gray-50 rounded">
                <form wire:submit.prevent="guardar" class="grid grid-cols-2 gap-4 text-xs">
                    <div class="col-span-2">
                        <p class="font-bold text-gray-700 mb-3">💼 Información del Servicio</p>
                    </div>

                    <!-- Nombre -->
                    <div class="flex flex-col">
                        <label for="nombre_servicio" class="font-bold mb-1">
                            Nombre del Servicio <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nombre_servicio" maxlength="255"
                            class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 @error('servicio.nombre_servicio') border-red-500 @enderror"
                            placeholder="Nombre del servicio" wire:model="servicio.nombre_servicio">
                        @error('servicio.nombre_servicio')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Categoría -->
                    <div class="flex flex-col">
                        <label for="categoria" class="font-bold mb-1">
                            Categoría del Servicio <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="servicio.id_categoria_servicio" id="categoria"
                            class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 @error('servicio.id_categoria_servicio') border-red-500 @enderror">
                            <option value="">-- Seleccione una categoría --</option>
                            @foreach ($categorias as $cate)
                                <option value="{{ $cate->id_categoria_servicio }}">
                                    {{ $cate->nombre_categoria_servicio }}
                                </option>
                            @endforeach
                        </select>
                        @error('servicio.id_categoria_servicio')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Duración estimada -->
                    <div class="flex flex-col">
                        <label for="duracion_estimada" class="font-bold mb-1">
                            Duración Estimada (minutos) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="duracion_estimada" min="1"
                            class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 @error('servicio.duracion_estimada') border-red-500 @enderror"
                            placeholder="Ejemplo: 60" wire:model="servicio.duracion_estimada">
                        @error('servicio.duracion_estimada')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Precio -->
                    <div class="flex flex-col">
                        <label for="precio_unitario" class="font-bold mb-1">
                            Precio del Servicio (S/.) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" step="0.01" id="precio_unitario" min="0"
                            class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 @error('servicio.precio_unitario') border-red-500 @enderror"
                            placeholder="Ejemplo: 50.00" wire:model="servicio.precio_unitario">
                        @error('servicio.precio_unitario')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Descripción -->
                    <div class="flex flex-col col-span-2">
                        <label for="descripcion" class="font-bold mb-1">Descripción del Servicio</label>
                        <textarea id="descripcion" rows="4" maxlength="500"
                            class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 @error('servicio.descripcion') border-red-500 @enderror"
                            placeholder="Descripción detallada del servicio..." wire:model.live="servicio.descripcion"></textarea>
                        @error('servicio.descripcion')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                        <div class="text-right text-xs text-gray-500 mt-1">
                            {{ strlen($servicio['descripcion']) }}/500 caracteres
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
                            Registrar Servicio
                        </button>
                    </div>
                </form>
            </div>
        </x-tab>
    </x-tabs>

    <!-- MODAL DE EDICIÓN -->
    @if ($modalEditar)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <!-- Overlay -->
            <div class="absolute inset-0 bg-black opacity-50" wire:click="$set('modalEditar', false)"></div>

            <!-- Contenido del modal -->
            <div class="relative bg-white rounded-md p-6 w-1/3 z-10 overflow-y-auto max-h-[90vh]">
                <h2 class="text-lg font-bold mb-4 flex gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-pencil">
                        <path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z" />
                        <path d="m15 5 4 4" />
                    </svg>
                    Editar Servicio
                </h2>

                <form wire:submit.prevent="actualizarServicio" class="grid grid-cols-2 gap-4 text-xs">
                    <div class="flex flex-col col-span-2">
                        <label class="font-bold mb-1">Nombre</label>
                        <input type="text" wire:model="servicioEditar.nombre_servicio"
                            class="border rounded px-2 py-1 focus:ring focus:ring-blue-300">
                    </div>

                    <div class="flex flex-col">
                        <label class="font-bold mb-1">Duración Estimada</label>
                        <input type="number" min="1" wire:model="servicioEditar.duracion_estimada"
                            class="border rounded px-2 py-1 focus:ring focus:ring-blue-300">
                    </div>

                    <div class="flex flex-col">
                        <label class="font-bold mb-1">Precio (S/.)</label>
                        <input type="number" step="0.01" wire:model="servicioEditar.precio_unitario"
                            class="border rounded px-2 py-1 focus:ring focus:ring-blue-300">
                    </div>

                    <div class="flex flex-col col-span-2">
                        <label class="font-bold mb-1">Categoría</label>
                        <select wire:model="servicioEditar.id_categoria_servicio"
                            class="border rounded px-2 py-1 focus:ring focus:ring-blue-300">
                            <option value="">-- Seleccione categoría --</option>
                            @foreach ($categorias as $cate)
                                <option value="{{ $cate->id_categoria_servicio }}">
                                    {{ $cate->nombre_categoria_servicio }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col col-span-2">
                        <label class="font-bold mb-1">Descripción</label>
                        <textarea wire:model="servicioEditar.descripcion" rows="3"
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

    <div wire:loading wire:target="guardar | actualizarServicio">
        <x-loader />
    </div>
</x-panel>
