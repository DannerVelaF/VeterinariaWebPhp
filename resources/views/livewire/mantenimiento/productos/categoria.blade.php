<x-panel title="Gestión de Productos">
    <x-tabs :tabs="['listado' => '📋 Detalle productos registrados', 'registro' => '➕ Registrar nuevo producto']" default="listado">
        <!-- TAB 1: LISTADO -->
        <x-tab name="listado">
            <div class="p-4">
                <livewire:categoria-table />
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
                    <!-- ====== INFORMACIÓN DEL PRODUCTO ====== -->
                    <div class="col-span-2">
                        <p class="font-bold text-gray-700 mb-3">📦 Información de la categoria</p>
                    </div>

                    <div class="flex flex-col">
                        <label for="nombre_producto" class="font-bold mb-1">Nombre de la categoria <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="nombre_categoria" name="nombre_categoria" maxlength="255"
                            class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 @error('cateogoria.nombre') border-red-500 @enderror"
                            placeholder="Categoria de productos" wire:model="categoria.nombre">
                        @error('categoria.nombre')
                            <p class="text-red-500 text-xs italic mt-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="flex flex-col col-span-2">
                        <label for="descripcion" class="font-bold mb-1">Descripción de la categoria <span
                                class="text-red-500">*</span></label>
                        <textarea id="descripcion" name="descripcion" rows="4" maxlength="1000"
                            class="border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300 @error('cate.descripcion') border-red-500 @enderror"
                            placeholder="Descripción detallada del producto..." wire:model="categoria.descripcion"></textarea>
                        @error('categoria.descripcion')
                            <p class="text-red-500 text-xs italic mt-1">
                                {{ $message }}
                            </p>
                        @enderror
                        <div class="text-right text-xs text-gray-500 mt-1">
                            {{ strlen($categoria['descripcion']) }}/1000 caracteres
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
                            Registrar Producto
                        </button>
                    </div>
                </form>
            </div>
        </x-tab>
    </x-tabs>
</x-panel>
