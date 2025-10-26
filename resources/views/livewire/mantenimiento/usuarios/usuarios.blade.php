<x-panel title="Gestión de Usuarios" :breadcrumbs="[
    ['label' => 'Inicio', 'href' => '/', 'icon' => 'home'],
    ['label' => 'Mantenimiento', 'href' => '#'],
    ['label' => 'Usuarios'],
]">
    <x-tabs :tabs="['listado' => '📋 Detalle usuarios registrados', 'registro' => '➕ Registrar nuevo usuario']" default="listado">
        <!-- TAB 1: LISTADO -->
        <x-tab name="listado">
            <div class="p-4">
                <livewire:user-table />
            </div>
        </x-tab>

        <!-- TAB 2: REGISTRO -->
        <x-tab name="registro">
            <!-- Mensajes de éxito y error -->
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

            <div class="p-3 bg-gray-50 rounded">
                <form wire:submit.prevent="guardar" class="grid grid-cols-2 gap-4 text-xs">
                    <!-- Datos del trabajador -->
                    <div class="col-span-2">
                        <p class="font-bold text-gray-700 mb-3">👤 Información del trabajador</p>
                    </div>

                    <div class="flex flex-col">
                        <label>Nombre <span class="text-red-500">*</span></label>
                        <select wire:model.live="trabajadorSeleccionado" name="trabajador" id="trabajador"
                            class="border rounded px-2 py-1">
                            <option value="">Seleccione...</option>
                            @foreach ($trabajadores as $t)
                                <option value="{{ $t->id_trabajador }}">
                                    {{ $t->persona->nombre }} {{ $t->persona->apellido_paterno }}
                                    {{ $t->persona->apellido_materno }}
                                </option>
                            @endforeach
                        </select>

                        @error('trabajadorSeleccionado')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Username -->
                    <div class="flex flex-col">
                        <label>Nombre de usuario <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="username" class="border rounded px-2 py-1">
                        @error('username')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="flex flex-col">
                        <div class="flex flex-col mb-3">
                            <label>Contraseña <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="dniTrabajador"
                                class="border rounded px-2 py-1 bg-gray-100" readonly>
                        </div>

                    </div>

                    <!-- Rol -->
                    <div class="flex flex-col">
                        <label>Rol <span class="text-red-500">*</span></label>
                        <select wire:model="rolSeleccionado" class="border rounded px-2 py-1">
                            <option value="">Seleccione...</option>
                            @foreach ($roles as $rol)
                                <option value="{{ $rol->id_rol }}">{{ $rol->nombre_rol }}</option>
                            @endforeach
                        </select>
                        @error('rolSeleccionado')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Botones -->
                    <div class="col-span-2 flex justify-end mt-6 space-x-2">
                        <button type="reset" class="bg-gray-500 text-white px-4 py-2 rounded">Limpiar</button>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Registrar
                            Usuario</button>
                    </div>
                </form>

            </div>
        </x-tab>
    </x-tabs>
    @if ($modalRol)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data="{ show: true }" x-show="show"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">

            <!-- Overlay difuminado -->
            <div class="absolute inset-0 bg-black opacity-50" @click="$wire.set('modalRol', false)"></div>

            <!-- Contenido del modal -->
            <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md z-50 overflow-hidden">
                <!-- Header del modal -->
                <div class="p-6">
                    <div class="flex justify-between items-center">
                        <h3 class="font-bold text-lg text-gray-700">Editar Usuario</h3>
                        <button wire:click="$set('modalRol', false)"
                            class="text-gray-500 hover:text-gray-700 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                    <p class="text-gray-600 text-sm mt-1">Usuario seleccionado: {{ $usuarioSeleccionado->usuario }}</p>
                </div>

                <!-- Contenido del formulario -->
                <div class="px-6 pb-6">
                    <!-- Username -->
                    <div class="flex flex-col mb-4">
                        <label class="text-sm font-medium text-gray-700 mb-1">Nombre de usuario</label>
                        <input type="text" wire:model="usernameEdit" readonly
                            class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                        @error('usernameEdit')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Reset Password -->
                    <div class="flex flex-col mb-4">
                        <label class="text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                        <div class="flex items-center space-x-2">
                            <button type="button" wire:click="resetContrasena"
                                class="px-4 py-2 text-sm font-medium text-white bg-amber-500 border border-transparent rounded-lg hover:bg-amber-600 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                                Resetear Contraseña
                            </button>
                            <span class="text-xs text-gray-500">Se restablecerá al DNI del trabajador</span>
                        </div>
                    </div>

                    <!-- Rol -->
                    <div class="flex flex-col mb-4">
                        <label class="text-sm font-medium text-gray-700 mb-1">Rol</label>
                        <select wire:model="rolNuevo"
                            class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                            <option value="">Seleccione un rol...</option>
                            @foreach ($roles as $rol)
                                <option value="{{ $rol->id_rol }}">{{ $rol->nombre_rol }}</option>
                            @endforeach
                        </select>
                        @error('rolNuevo')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Estado -->
                    <div class="flex flex-col mb-4">
                        <label class="text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <select wire:model="estadoEdit"
                            class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                        @error('estadoEdit')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Acciones del modal -->
                    <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
                        <button wire:click="$set('modalRol', false)"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancelar
                        </button>
                        <button wire:click="guardarRol"
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
