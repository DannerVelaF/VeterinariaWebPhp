<x-panel title="Gestión de Opciones de Módulos" :breadcrumbs="[
    ['label' => 'Inicio', 'href' => '/', 'icon' => 'home'],
    ['label' => 'Configuración', 'href' => '#'],
    ['label' => 'Opciones de Módulos'],
]">

    <div class="p-6 space-y-6">
        <!-- Header -->

        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Opciones de Módulos</h2>
                <p class="text-sm text-gray-600 mt-1">
                    Administra las opciones y subopciones del menú de navegación
                </p>
            </div>
            <flux:button wire:click="abrirModal" class="bg-blue-600 hover:bg-blue-700 text-white">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Crear Opción
            </flux:button>
        </div>

        <!-- Modal Crear/Editar Opción -->
        <flux:modal name="crearOpcion" class="md:w-[600px]" wire:model="modalVisible">
            <div class="p-6">
                <div class="flex items-center mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">
                            {{ $opcion_id ? 'Editar Opción' : 'Crear Nueva Opción' }}
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">
                            {{ $opcion_id ? 'Modifica la información de la opción' : 'Completa los datos de la nueva opción de menú' }}
                        </p>
                    </div>
                </div>

                <form wire:submit.prevent="guardar" class="space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Módulo -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Módulo
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select wire:model.live="id_modulo"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition appearance-none bg-white">
                                    <option value="">Seleccione un módulo</option>
                                    @foreach ($modulos as $modulo)
                                        <option value="{{ $modulo->id_modulo }}">{{ $modulo->nombre_modulo }}</option>
                                    @endforeach
                                </select>
                                <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7">
                                    </path>
                                </svg>
                            </div>
                            @error('id_modulo')
                                <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Opción padre -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Opción padre
                                <span class="text-xs text-gray-500 font-normal">(opcional - para crear
                                    subopciones)</span>
                            </label>
                            <div class="relative">
                                <select wire:model="id_opcion_padre"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition appearance-none bg-white">
                                    <option value="">Sin padre - Opción principal</option>
                                    @foreach ($opcionesPadre as $op)
                                        <option value="{{ $op->id_modulo_opcion }}">{{ $op->nombre_opcion }}</option>
                                    @endforeach
                                </select>
                                <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7">
                                    </path>
                                </svg>
                            </div>
                        </div>

                        <!-- Nombre opción -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Nombre de la opción
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model="nombre_opcion"
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                placeholder="Ej: Dashboard">
                            @error('nombre_opcion')
                                <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Orden -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Orden
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="number" wire:model="orden"
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                placeholder="1" min="0">
                            @error('orden')
                                <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Ruta Laravel -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Ruta Laravel
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model="ruta_laravel"
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition font-mono text-sm"
                                placeholder="Ej: admin.dashboard">
                            @error('ruta_laravel')
                                <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Permiso -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Permiso requerido
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select wire:model="id_permiso"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition appearance-none bg-white">
                                    <option value="">Seleccione un permiso</option>
                                    @foreach ($permisos as $perm)
                                        <option value="{{ $perm->id_permiso }}">{{ $perm->nombre_permiso }}</option>
                                    @endforeach
                                </select>
                                <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7">
                                    </path>
                                </svg>
                            </div>
                            @error('id_permiso')
                                <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-5 border-t">
                        <flux:button wire:click="$set('modalVisible', false)" type="button"
                            class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                            Cancelar
                        </flux:button>
                        <flux:button type="submit"
                            class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2 ">

                            {{ $opcion_id ? 'Actualizar' : 'Guardar' }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        <!-- Tabla de opciones con scroll -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Opciones Registradas</h3>
                    <p class="text-sm text-gray-600 mt-1">Estructura jerárquica del menú - Total:
                        {{ count($opciones) }}
                        opción(es)</p>
                </div>
                <button onclick="expandirTodo()"
                    class="text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                        </path>
                    </svg>
                    Expandir/Contraer todo
                </button>
            </div>

            <!-- Contenedor de tabla con scroll -->
            <div class="overflow-x-auto">
                <div class="min-w-full max-h-[500px] overflow-y-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200 sticky top-0 z-10">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider bg-gray-50">
                                    #
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider bg-gray-50">
                                    Opción
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider bg-gray-50">
                                    Módulo
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider bg-gray-50">
                                    Ruta
                                </th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider bg-gray-50">
                                    Orden
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider bg-gray-50">
                                    Permiso
                                </th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider bg-gray-50">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($opciones as $index => $op)
                                <!-- Opción Principal -->
                                <tr class="hover:bg-gray-50 transition-colors"
                                    data-parent-row="{{ $op->id_modulo_opcion }}">
                                    <td class="px-6 py-4 text-sm text-gray-700 font-medium">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            @if ($op->subopciones->count() > 0)
                                                <button onclick="toggleSubopciones({{ $op->id_modulo_opcion }})"
                                                    class="toggle-btn w-6 h-6 rounded hover:bg-gray-200 flex items-center justify-center transition"
                                                    data-target="{{ $op->id_modulo_opcion }}">
                                                    <svg class="w-4 h-4 text-gray-600 transform transition-transform"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                    </svg>
                                                </button>
                                            @else
                                                <div class="w-6"></div>
                                            @endif
                                            <div class="flex items-center gap-2">
                                                <div
                                                    class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-4 h-4 text-blue-600" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z">
                                                        </path>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <span
                                                        class="text-sm font-semibold text-gray-800">{{ $op->nombre_opcion }}</span>
                                                    @if ($op->subopciones->count() > 0)
                                                        <span
                                                            class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                            {{ $op->subopciones->count() }} sub
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-md bg-purple-100 text-purple-800 text-xs font-medium">
                                            {{ $op->modulo->nombre_modulo }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <code
                                            class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-mono">{{ $op->ruta_laravel }}</code>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 text-center">
                                        <span
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-700 font-semibold">
                                            {{ $op->orden }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ $op->permiso->nombre_permiso ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <flux:button wire:click="abrirModal({{ $op->id_modulo_opcion }})"
                                                class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs rounded-lg transition">
                                                <svg class="w-3.5 h-3.5 inline-block mr-1" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                    </path>
                                                </svg>
                                                Editar
                                            </flux:button>
                                            <flux:button wire:click="anular({{ $op->id_modulo_opcion }})"
                                                class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs rounded-lg transition">
                                                <svg class="w-3.5 h-3.5 inline-block mr-1" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                                Anular
                                            </flux:button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Subopciones -->
                                @foreach ($op->subopciones as $subIndex => $sub)
                                    <tr class="subopcion-row hover:bg-blue-50/50 transition-colors hidden bg-blue-50/30"
                                        data-parent="{{ $op->id_modulo_opcion }}">
                                        <td class="px-6 py-3 text-sm text-gray-500"></td>
                                        <td class="px-6 py-3">
                                            <div class="flex items-center gap-2 pl-8">
                                                <svg class="w-4 h-4 text-gray-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                                <div class="flex items-center gap-2">
                                                    <div
                                                        class="w-7 h-7 rounded-lg bg-blue-200 flex items-center justify-center flex-shrink-0">
                                                        <svg class="w-3.5 h-3.5 text-blue-700" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                    <span
                                                        class="text-sm text-gray-700">{{ $sub->nombre_opcion }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-3 text-sm text-gray-700">
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-md bg-purple-100 text-purple-800 text-xs font-medium">
                                                {{ $sub->modulo->nombre_modulo }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3 text-sm">
                                            <code
                                                class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-mono">{{ $sub->ruta_laravel }}</code>
                                        </td>
                                        <td class="px-6 py-3 text-sm text-gray-700 text-center">
                                            <span
                                                class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-100 text-gray-700 font-semibold text-xs">
                                                {{ $sub->orden }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3 text-sm text-gray-700">
                                            {{ $sub->permiso->nombre_permiso ?? '-' }}
                                        </td>
                                        <td class="px-6 py-3">
                                            <div class="flex items-center justify-center gap-2">
                                                <flux:button wire:click="abrirModal({{ $sub->id_modulo_opcion }})"
                                                    class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs rounded-lg transition">
                                                    <svg class="w-3.5 h-3.5 inline-block mr-1" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                        </path>
                                                    </svg>
                                                    Editar
                                                </flux:button>
                                                <flux:button wire:click="anular({{ $sub->id_modulo_opcion }})"
                                                    class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs rounded-lg transition">
                                                    <svg class="w-3.5 h-3.5 inline-block mr-1" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                    Anular
                                                </flux:button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-16 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                            </path>
                                        </svg>
                                        <p class="mt-4 text-sm font-medium text-gray-600">No hay opciones registradas
                                        </p>
                                        <p class="text-sm text-gray-500 mt-1">Comienza creando tu primera opción de
                                            menú
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Toggle subopciones individuales
            function toggleSubopciones(parentId) {
                const subopciones = document.querySelectorAll(`[data-parent="${parentId}"]`);
                const toggleBtn = document.querySelector(`[data-target="${parentId}"] svg`);

                subopciones.forEach(sub => {
                    sub.classList.toggle('hidden');
                });

                if (toggleBtn) {
                    toggleBtn.classList.toggle('rotate-90');
                }
            }

            // Expandir o contraer todas las subopciones
            let todosExpandidos = false;

            function expandirTodo() {
                const subopciones = document.querySelectorAll('.subopcion-row');
                const toggleBtns = document.querySelectorAll('.toggle-btn svg');

                todosExpandidos = !todosExpandidos;

                subopciones.forEach(sub => {
                    if (todosExpandidos) {
                        sub.classList.remove('hidden');
                    } else {
                        sub.classList.add('hidden');
                    }
                });

                toggleBtns.forEach(btn => {
                    if (todosExpandidos) {
                        btn.classList.add('rotate-90');
                    } else {
                        btn.classList.remove('rotate-90');
                    }
                });
            }

            // Notificaciones
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
