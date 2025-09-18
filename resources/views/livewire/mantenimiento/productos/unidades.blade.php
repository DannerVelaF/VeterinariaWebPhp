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

                <div class="col-span-2 flex justify-end space-x-2">
                    <button type="reset" class="bg-gray-500 text-white px-4 py-2 rounded">Limpiar</button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Registrar Unidad</button>
                </div>
            </form>
            <table class="w-full text-xs border border-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 text-left border">Unidad</th>
                        <th class="p-2 text-left border">Accion</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($unidades as $unidad)
                        <tr>
                            <td class="p-2 border font-bold">{{ $unidad->nombre }}</td>
                            <td class="p-2 border text-center">
                                <button wire:click="eliminar({{ $unidad->id }})"
                                    class="px-3 py-1 rounded text-white bg-red-500 hover:bg-red-600">
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-4 text-center text-gray-500">No hay roles registrados</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </x-tab>
    </x-tabs>
</x-panel>
