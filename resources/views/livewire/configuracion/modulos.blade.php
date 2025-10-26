<x-panel title="Gestión de Módulos" :breadcrumbs="[
    ['label' => 'Inicio', 'href' => '/', 'icon' => 'home'],
    ['label' => 'Configuración', 'href' => '#'],
    ['label' => 'Módulos del sistema'],
]">

    <div class="p-6 space-y-6">
        <!-- Header con botón de acción -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Módulos del Sistema</h2>
                <p class="text-sm text-gray-600 mt-1">Administra los módulos y sus permisos de acceso por rol</p>
            </div>
            <flux:button wire:click="abrirModal" variant="primary">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Crear Módulo
            </flux:button>
        </div>

        <!-- Modal Crear/Editar -->
        <flux:modal name="crearModulo" class="md:w-[500px]" wire:model="modalVisible">
            <div class="p-2">
                <h3 class="text-xl font-bold text-gray-800 mb-1">
                    {{ $modulo_id ? 'Editar Módulo' : 'Crear Nuevo Módulo' }}
                </h3>
                <p class="text-sm text-gray-600 mb-6">
                    {{ $modulo_id ? 'Modifica la información del módulo' : 'Completa los datos para crear un nuevo módulo' }}
                </p>

                <form wire:submit.prevent="guardar" class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Nombre del módulo
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="nombre_modulo"
                            class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            placeholder="Ej: Gestión de Ventas">
                        @error('nombre_modulo')
                            <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            Roles con acceso
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach ($roles as $rol)
                                <label
                                    class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition">
                                    <input type="checkbox" wire:model="rolesSeleccionados" value="{{ $rol->id_rol }}"
                                        class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                                    <span class="ml-3 text-sm text-gray-700">{{ $rol->nombre_rol }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('rolesSeleccionados')
                            <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <flux:button wire:click="$set('modalVisible', false)" type="button"
                            class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700">
                            Cancelar
                        </flux:button>
                        <flux:button type="submit" variant="primary" class="px-5 py-2 ">
                            {{ $modulo_id ? 'Actualizar Módulo' : 'Guardar Módulo' }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        <!-- Tabla de módulos -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Módulos Registrados</h3>
                <p class="text-sm text-gray-600 mt-1">Total: {{ count($modulos) }} módulo(s)</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                #</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Módulo</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Roles Asignados</th>
                            <th
                                class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($modulos as $index => $mod)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">

                                        <span class="text-sm font-medium text-gray-800">{{ $mod->nombre_modulo }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-2">
                                        @forelse ($mod->roles as $rol)
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $rol->nombre_rol }}
                                            </span>
                                        @empty
                                            <span class="text-sm text-gray-400 italic">Sin roles asignados</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center space-x-2">
                                        <flux:button wire:click="abrirModal({{ $mod->id_modulo }})"
                                            class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-sm rounded-lg transition"
                                            title="Editar módulo">
                                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                            Editar
                                        </flux:button>
                                        <flux:button wire:click="abrirModalRoles({{ $mod->id_modulo }})"
                                            class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition"
                                            title="Gestionar roles">
                                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                                </path>
                                            </svg>
                                            Roles
                                        </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                        </path>
                                    </svg>
                                    <p class="mt-4 text-sm text-gray-600">No hay módulos registrados</p>
                                    <p class="text-sm text-gray-500">Comienza creando tu primer módulo</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Asignar Roles -->
        <flux:modal name="asignarRoles" class="md:w-[500px]" wire:model="modalRolesVisible">
            <div class="p-2">
                <h3 class="text-xl font-bold text-gray-800 mb-1">
                    Gestionar Roles
                </h3>
                <p class="text-sm text-gray-600 mb-6">
                    Asignar roles a: <span class="font-semibold">{{ $moduloSeleccionado->nombre_modulo ?? '' }}</span>
                </p>

                <div class="mb-6">
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Roles disponibles para asignar</h4>
                    <div class="grid grid-cols-2 gap-3 max-h-64 overflow-y-auto">
                        @php
                            $rolesAsignados = $moduloSeleccionado?->roles->pluck('id_rol')->toArray() ?? [];
                            $rolesDisponibles = false;
                        @endphp

                        @foreach ($roles as $rol)
                            @if (!in_array($rol->id_rol, $rolesAsignados))
                                @php $rolesDisponibles = true; @endphp
                                <label
                                    class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition">
                                    <input type="checkbox" wire:model="rolesNuevos" value="{{ $rol->id_rol }}"
                                        class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                                    <span class="ml-3 text-sm text-gray-700">{{ $rol->nombre_rol }}</span>
                                </label>
                            @endif
                        @endforeach

                        @if (!$rolesDisponibles)
                            <div class="col-span-2 text-center py-8">
                                <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="mt-2 text-sm text-gray-600">Todos los roles están asignados</p>
                            </div>
                        @endif
                    </div>
                    @error('rolesNuevos')
                        <span class="text-red-600 text-sm mt-2 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <flux:button wire:click="$set('modalRolesVisible', false)"
                        class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700">
                        Cancelar
                    </flux:button>
                    <flux:button variant="primary" wire:click="asignarRoles" class="px-5 py-2 ">
                        Asignar Roles
                    </flux:button>
                </div>
            </div>
        </flux:modal>
    </div>

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

</x-panel>
