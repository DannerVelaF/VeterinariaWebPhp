<x-panel title="Gestión de Roles">
    <!-- Formulario de creación de rol -->
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
                    <td class="p-2 border">{{ $rol->id_rol }}</td>
                    <td class="p-2 border font-bold">{{ $rol->nombre_rol }}</td>
                    <td class="p-2 border">
                        <span
                            class="px-2 py-1 rounded text-white {{ $rol->estado === 'activo' ? 'bg-green-600' : 'bg-red-600' }}">
                            {{ ucfirst($rol->estado) }}
                        </span>
                    </td>
                    <td class="p-2 border text-center space-x-1">
                        <button wire:click="cambiarEstado({{ $rol->id_rol }})"
                            class="px-3 py-1 rounded text-white {{ $rol->estado === 'activo' ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600' }}">
                            {{ $rol->estado === 'activo' ? 'Desactivar' : 'Activar' }}
                        </button>

                        <button wire:click="editarPermisos({{ $rol->id_rol }})"
                            class="px-3 py-1 rounded bg-blue-500 hover:bg-blue-600 text-white">
                            Permisos
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

    <!-- Modal de permisos -->
    @if ($modalPermisos)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <!-- Overlay difuminado -->
            <div class="absolute inset-0 bg-black opacity-50"></div>

            <!-- Contenido del modal -->
            <div class="relative bg-white rounded-lg shadow-lg w-96 p-6 z-50">
                <h3 class="font-bold mb-4 text-lg">Permisos para: {{ $rolSeleccionado->nombre_rol }}</h3>

                <div class="grid grid-cols-1 gap-2 max-h-64 overflow-y-auto mb-4">
                    @foreach ($permisos as $permiso)
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" wire:model="permisosSeleccionados"
                                value="{{ $permiso->id_permiso }}"
                                {{ $permiso->estado !== 'activo' ? 'disabled' : '' }}>
                            <span class="{{ $permiso->estado !== 'activo' ? 'text-gray-400 line-through' : '' }}">
                                {{ $permiso->nombre_permiso }}
                            </span>
                        </label>
                    @endforeach
                </div>

                <div class="flex justify-end space-x-2">
                    <button wire:click="$set('modalPermisos', false)"
                        class="px-3 py-1 rounded bg-gray-500 text-white hover:bg-gray-600">Cerrar</button>
                    <button wire:click="guardarPermisos"
                        class="px-3 py-1 rounded bg-blue-600 text-white hover:bg-blue-700">Guardar</button>
                </div>
            </div>
        </div>
    @endif
    <x-loader />
</x-panel>
