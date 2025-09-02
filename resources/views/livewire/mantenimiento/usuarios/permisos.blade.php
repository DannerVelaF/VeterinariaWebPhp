<x-panel title="Gestión de Permisos">
    <!-- Formulario de creación -->
    @if (session()->has('success'))
        <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
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
                    <td class="p-2 border">{{ $permiso->id }}</td>
                    <td class="p-2 border font-bold">{{ $permiso->name }}</td>
                    <td class="p-2 border">
                        <span
                            class="px-2 py-1 rounded text-white 
                        {{ $permiso->estado === 'activo' ? 'bg-green-600' : 'bg-red-600' }}">
                            {{ ucfirst($permiso->estado) }}
                        </span>
                    </td>
                    <td class="p-2 border text-center">
                        <button wire:click="cambiarEstado({{ $permiso->id }})"
                            class="px-3 py-1 rounded text-white
                            {{ $permiso->estado === 'activo' ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600' }}">
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

</x-panel>
