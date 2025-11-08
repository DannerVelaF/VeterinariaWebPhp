<x-panel title="Gestión de Unidades de producto">
    <x-tabs :tabs="['registro' => '➕ Registrar nueva unidad']" default="registro">

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

            <form wire:submit.prevent="guardar" class="grid grid-cols-2 gap-4 text-xs mb-6">
                <div class="col-span-2 flex flex-col">
                    <label>Nombre de la unidad<span class="text-red-500">*</span></label>
                    <input type="text" wire:model="nombre" class="border p-1 rounded">
                    @error('nombre')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                <!-- ✅ CHECKBOX -->
                <div class="col-span-2 flex items-center space-x-2">
                    <input type="checkbox" wire:model="contieneUnidades" id="contieneUnidades"
                        class="rounded border-gray-300">
                    <label for="contieneUnidades" class="text-sm font-medium text-gray-700 cursor-pointer">
                        ¿Esta unidad contiene múltiples unidades individuales?
                    </label>
                </div>
                <div class="col-span-2 text-xs text-gray-500 mb-2">
                    <p>✔ Marcar esta opción para unidades como: Caja, Paquete, Bolsa, etc.</p>
                    <p>✘ No marcar para unidades individuales como: Unidad, Frasco, Tarro, etc.</p>
                </div>

                <div class="col-span-2 flex justify-end space-x-2">
                    <button type="reset" class="bg-gray-500 text-white px-4 py-2 rounded">Limpiar</button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Registrar Unidad</button>
                </div>
            </form>

            <table class="w-full text-xs border border-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 text-left border">Unidad</th>
                        <th class="p-2 text-left border">Contiene Unidades</th>
                        <th class="p-2 text-left border">Estado</th>
                        <th class="p-2 text-left border">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($unidades as $unidad)
                        <tr>
                            <td class="p-2 border font-bold">{{ $unidad->nombre_unidad }}</td>
                            <td class="p-2 border text-center">
                                <!-- ✅ CHECKBOX EDITABLE -->
                                <input type="checkbox"
                                    wire:change="actualizarContieneUnidades({{ $unidad->id_unidad }}, $event.target.checked)"
                                    {{ $unidad->contiene_unidades ? 'checked' : '' }}
                                    class="rounded border-gray-300 cursor-pointer" title="Click para cambiar">
                            </td>
                            <td class="p-2 border text-center capitalize">{{ $unidad->estado }}</td>
                            <td class="p-2 border text-center">
                                @if ($unidad->estado == 'activo')
                                    <button wire:click="cambiarEstado({{ $unidad->id_unidad }})"
                                        class="px-3 py-1 rounded text-white bg-red-500 hover:bg-red-600">
                                        Anular
                                    </button>
                                @else
                                    <button wire:click="cambiarEstado({{ $unidad->id_unidad }})"
                                        class="px-3 py-1 rounded text-white bg-green-500 hover:bg-green-600">
                                        Activar
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-4 text-center text-gray-500">No hay unidades registradas</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </x-tab>
    </x-tabs>
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
