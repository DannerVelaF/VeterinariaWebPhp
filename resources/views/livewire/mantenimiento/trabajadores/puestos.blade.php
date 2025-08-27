<x-panel title="Gestión de puestos">
    <x-tabs :tabs="['listado' => '📋 Detalle trabajadores registrados', 'registro' => '➕ Registrar nuevo trabajador']" default="listado">
        <x-tab name="listado">
            <div class="p-4">
                <livewire:puestos-table />
            </div>
        </x-tab>

        <x-tab name="registro">
            <div class="p-4">
                @if (session()->has('success'))
                    <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-3">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-3">
                        {{ session('error') }}
                    </div>
                @endif

                <form wire:submit.prevent="guardar" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium">Nombre del puesto <span
                                class="text-red-500">*</span></label>
                        <input type="text" wire:model="puesto.nombre"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 border px-2 py-1" />
                        @error('puesto.nombre')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Descripción (opcional)</label>
                        <textarea wire:model="puesto.descripcion"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 border  px-2 py-1"></textarea>
                        @error('puesto.descripcion')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
                            Guardar Puesto
                        </button>
                    </div>
                </form>
            </div>
        </x-tab>
    </x-tabs>
</x-panel>
