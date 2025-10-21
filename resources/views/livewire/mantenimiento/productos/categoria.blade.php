<x-panel title="Gestión de proveedores" :breadcrumbs="[
    ['label' => 'Inicio', 'href' => '/', 'icon' => 'home'],
    ['label' => 'Productos', 'href' => route('mantenimiento.productos'), 'icon' => 'ellipsis-horizontal'],
    ['label' => 'Gestión de categorias', 'href' => route('mantenimiento.productos.proveedores')],
]">
    <x-tabs :tabs="['listado' => '📋 Detalle categorias registrados', 'registro' => '➕ Registrar nueva categoría']" default="listado">
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
                            placeholder="Categoria de productos" wire:model="categoria.nombre_categoria">
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
                            {{ strlen($categoria['descripcion']) }}/300 caracteres
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
                            Registrar Categoria
                        </button>
                    </div>
                </form>
            </div>
        </x-tab>
    </x-tabs>
    @if ($modalEditar)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <!-- Overlay -->
            <div class="absolute inset-0 bg-black opacity-50" wire:click="$set('modalEditar', false)"></div>

            <!-- Contenido del modal -->
            <div class="relative bg-white rounded-md p-6 w-1/3 z-10 overflow-y-auto max-h-[90vh]">
                <h2 class="text-lg font-bold mb-4 flex gap-2"><svg xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil-icon lucide-pencil">
                        <path
                            d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z" />
                        <path d="m15 5 4 4" />
                    </svg>Editar Categoría</h2>

                <!-- Mensajes de error en edición -->
                @if (session()->has('error'))
                    <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded text-xs">
                        {{ session('error') }}
                    </div>
                @endif

                <form wire:submit.prevent="guardarEdicion" class="grid grid-cols-2 gap-4 text-xs">
                    <div class="flex flex-col col-span-2">
                        <label class="font-bold mb-1">Nombre categoría</label>
                        <input type="text" wire:model="categoriaEditar.nombre_categoria_producto"
                            class="border rounded px-2 py-1 focus:ring focus:ring-blue-300">
                    </div>

                    <div class="flex flex-col col-span-2">
                        <label class="font-bold mb-1">Descripción</label>
                        <textarea wire:model="categoriaEditar.descripcion" rows="3"
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
