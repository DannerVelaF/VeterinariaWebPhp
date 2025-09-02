<x-panel title="Gestión de Roles">
    <!-- Mensajes -->
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

    <!-- Formulario -->
    <form wire:submit.prevent="guardar" class="grid grid-cols-2 gap-4 text-xs mb-6">
        <div class="col-span-2 flex flex-col">
            <label>Nombre del Rol <span class="text-red-500">*</span></label>
            <input type="text" wire:model="nombreRol" class="border p-1 rounded">
            @error('nombreRol')
                <p class="text-red-500 text-xs">{{ $message }}</p>
            @enderror
        </div>

        <div class="col-span-2 flex justify-end space-x-2">
            <button type="reset" class="bg-gray-500 text-white px-4 py-2 rounded">Limpiar</button>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Registrar Rol</button>
        </div>
    </form>

    <!-- Tabla de roles -->
    <table class="w-full text-xs border border-gray-300">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2 text-left border">#</th>
                <th class="p-2 text-left border">Rol</th>
                <th class="p-2 text-left border">Estado</th>
                <th class="p-2 text-left border">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($roles as $rol)
                <tr>
                    <td class="p-2 border">{{ $rol->id }}</td>
                    <td class="p-2 border font-bold">{{ $rol->name }}</td>
                    <td class="p-2 border">
                        <span
                            class="px-2 py-1 rounded text-white 
                            {{ $rol->estado === 'activo' ? 'bg-green-600' : 'bg-red-600' }}">
                            {{ ucfirst($rol->estado) }}
                        </span>
                    </td>
                    <td class="p-2 border text-center">
                        <button wire:click="cambiarEstado({{ $rol->id }})"
                            class="px-3 py-1 rounded text-white
                                {{ $rol->estado === 'activo' ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600' }}">
                            {{ $rol->estado === 'activo' ? 'Desactivar' : 'Activar' }}
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
</x-panel>
