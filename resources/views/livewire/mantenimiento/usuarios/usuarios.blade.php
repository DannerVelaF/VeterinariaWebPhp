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
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <!-- Overlay -->
            <div class="absolute inset-0 bg-black opacity-50" wire:click="$set('modalRol', false)"></div>

            <!-- Modal -->
            <div class="relative bg-white rounded-md p-6 w-96 z-10">
                <h3 class="font-bold mb-4 flex gap-2"><svg xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil-icon lucide-pencil">
                        <path
                            d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z" />
                        <path d="m15 5 4 4" />
                    </svg> Editar usuario: {{ $usuarioSeleccionado->usuario }}</h3>

                <!-- Username -->
                <div class="flex flex-col mb-3">
                    <label>Nombre de usuario</label>
                    <input type="text" wire:model="usernameEdit" class="border rounded px-2 py-1">
                    @error('usernameEdit')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Reset Password -->
                <div class="flex flex-col mb-3">
                    <label>Nueva contraseña (opcional)</label>
                    <input type="password" wire:model="passwordEdit" class="border rounded px-2 py-1">
                    <small class="text-gray-500">Si no deseas cambiarla, deja este campo vacío</small>
                    @error('passwordEdit')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Rol -->
                <div class="flex flex-col mb-3">
                    <label>Rol</label>
                    <select wire:model="rolNuevo" class="border rounded px-2 py-1">
                        <option value="">Seleccione...</option>
                        @foreach ($roles as $rol)
                            <option value="{{ $rol->id_rol }}">{{ $rol->nombre_rol }}</option>
                        @endforeach
                        @error('rolNuevo')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                        @enderror
                    </select>
                </div>

                <!-- Estado -->
                <div class="flex flex-col mb-3">
                    <label>Estado</label>
                    <select wire:model="estadoEdit" class="border rounded px-2 py-1">
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                    @error('estadoEdit')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Botones -->
                <div class="flex justify-end space-x-2 mt-4">
                    <button wire:click="$set('modalRol', false)"
                        class="px-3 py-1 rounded bg-gray-500 text-white">Cerrar</button>
                    <button wire:click="guardarRol" class="px-3 py-1 rounded bg-blue-600 text-white">Guardar</button>
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
