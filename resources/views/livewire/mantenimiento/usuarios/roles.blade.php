<x-panel title="Gestión de Roles" :breadcrumbs="[
    ['label' => 'Inicio', 'href' => '/', 'icon' => 'home'],
    ['label' => 'Mantenimiento', 'href' => '#'],
    ['label' => 'Roles'],
]">

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

    <!-- Modal de permisos mejorado -->
    @if ($modalPermisos)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data="{ show: true }" x-show="show"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">

            <!-- Overlay difuminado -->
            <div class="absolute inset-0 bg-black opacity-50" @click="$wire.set('modalPermisos', false)"></div>

            <!-- Contenido del modal -->
            <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md z-50 overflow-hidden">
                <!-- Header del modal -->
                <div class="p-6">
                    <div class="flex justify-between items-center">
                        <h3 class="font-bold text-lg text-gray-700">Permisos del Rol</h3>
                        <button wire:click="$set('modalPermisos', false)"
                            class="text-white hover:text-gray-200 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                    <p class="text-gray-600 text-sm mt-1">{{ $rolSeleccionado->nombre_rol }}</p>
                </div>

                <!-- Contador de permisos seleccionados -->
                <div class="px-4 ">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-blue-800">Permisos seleccionados:</span>
                            <span
                                class="bg-blue-600 text-white text-xs font-bold rounded-full h-6 w-6 flex items-center justify-center">
                                {{ count($permisosSeleccionados) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Lista de permisos -->
                <div class="px-4 pb-4">
                    <div class="max-h-64 overflow-y-auto border border-gray-200 rounded-lg">
                        @if ($permisos->count() > 0)
                            <div class="divide-y divide-gray-100">
                                @foreach ($permisos as $permiso)
                                    <label
                                        class="flex items-center p-3 hover:bg-gray-50 transition-colors cursor-pointer {{ $permiso->estado !== 'activo' ? 'opacity-60' : '' }}">
                                        <div class="flex items-center h-5">
                                            <input type="checkbox" wire:model="permisosSeleccionados"
                                                value="{{ $permiso->id_permiso }}"
                                                {{ $permiso->estado !== 'activo' ? 'disabled' : '' }}
                                                class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        </div>
                                        <div class="ml-3 flex flex-col">
                                            <span
                                                class="text-sm font-medium {{ $permiso->estado !== 'activo' ? 'text-gray-500' : 'text-gray-900' }}">
                                                {{ $permiso->nombre_permiso }}
                                            </span>
                                            @if ($permiso->estado !== 'activo')
                                                <span class="text-xs text-gray-400">Permiso desactivado</span>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @else
                            <div class="p-4 text-center text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto text-gray-400 mb-2"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <p>No hay permisos disponibles</p>
                            </div>
                        @endif
                    </div>

                    <!-- Acciones del modal -->
                    <div class="flex justify-end space-x-3 mt-4 pt-3 border-t border-gray-200">
                        <button wire:click="$set('modalPermisos', false)"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancelar
                        </button>
                        <button wire:click="guardarPermisos"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Guardar Cambios
                        </button>
                    </div>
                </div>
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
