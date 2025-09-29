<x-panel title="Gestión de Permisos">
    <!-- Formulario de creación -->
    @if (session()->has('success'))
        <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded" x-data="{ show: true }"
            x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 transform translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-500"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform translate-y-2">
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
            <label>Nombre del Permiso <span class="text-red-500">*</span></label>
            <input type="text" wire:model="nombrePermiso" class="border p-1 rounded">
            @error('nombrePermiso')
                <p class="text-red-500 text-xs">{{ $message }}</p>
            @enderror
        </div>

        <div class="col-span-2 flex justify-end space-x-2">
            <button type="reset" class="bg-gray-500 text-white px-4 py-2 rounded">Limpiar</button>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Registrar Permiso</button>
        </div>
    </form>

    <!-- Tabla de permisos -->
    <table class="w-full text-xs border border-gray-300">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2 text-left border">#</th>
                <th class="p-2 text-left border">Permiso</th>
                <th class="p-2 text-left border">Estado</th>
                <th class="p-2 text-left border">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($permisos as $permiso)
                <tr>
                    <td class="p-2 border">{{ $permiso->id_permiso }}</td>
                    <td class="p-2 border font-bold">{{ $permiso->nombre_permiso }}</td>
                    <td class="p-2 border">
                        <span
                            class="px-2 py-1 rounded text-white {{ $permiso->estado === 'activo' ? 'bg-green-600' : 'bg-red-600' }}">
                            {{ ucfirst($permiso->estado) }}
                        </span>
                    </td>
                    <td class="p-2 border text-center">
                        <button wire:click="cambiarEstado({{ $permiso->id_permiso }})"
                            class="px-3 py-1 rounded text-white {{ $permiso->estado === 'activo' ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600' }}">
                            {{ $permiso->estado === 'activo' ? 'Desactivar' : 'Activar' }}
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="p-4 text-center text-gray-500">No hay permisos registrados</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <x-loader />
</x-panel>
