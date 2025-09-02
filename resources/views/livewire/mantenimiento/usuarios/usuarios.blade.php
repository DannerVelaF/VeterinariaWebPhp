<x-panel title="Gestión de Usuarios">
    <x-tabs :tabs="['listado' => '📋 Detalle usuarios registrados', 'registro' => '➕ Registrar nuevo usuario']" default="listado">
        <!-- TAB 1: LISTADO -->
        <x-tab name="listado">
            <div class="p-4">
                <livewire:user-table/>
            </div>
        </x-tab>

        <!-- TAB 2: REGISTRO -->
        <x-tab name="registro">
            <!-- Mensajes de éxito y error -->
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

            <div class="p-3 bg-gray-50 rounded">
                <form wire:submit.prevent="guardar" class="grid grid-cols-2 gap-4 text-xs">
                    <!-- Datos del trabajador -->
                    <div class="col-span-2">
                        <p class="font-bold text-gray-700 mb-3">👤 Información del trabajador</p>
                    </div>

                    <div class="flex flex-col">
                        <label>Nombre <span class="text-red-500">*</span></label>
                        <select wire:model.live='trabajadorSeleccionado' name="trabajador" id="trabajador"
                            class="border rounded px-2 py-1">
                            <option value="">Seleccione...</option>
                            @foreach ($trabajadores as $t)
                                <option value="{{ $t->id }}">
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
                        <label>Contraseña <span class="text-red-500">*</span></label>
                        <input type="password" wire:model="password" class="border rounded px-2 py-1">
                        @error('password')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Rol -->
                    <div class="flex flex-col">
                        <label>Rol <span class="text-red-500">*</span></label>
                        <select wire:model="rolSeleccionado" class="border rounded px-2 py-1">
                            <option value="">Seleccione...</option>
                            @foreach ($roles as $rol)
                                <option value="{{ $rol->name }}">{{ $rol->name }}</option>
                            @endforeach
                        </select>
                        @error('rolSeleccionado')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Permisos -->
                    <div class="col-span-2">
                        <label class="font-bold">Permisos adicionales</label>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach ($permisos as $permiso)
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" wire:model="permisosSeleccionados"
                                        value="{{ $permiso->name }}" class="border rounded px-2 py-1">
                                    <span>{{ $permiso->name }}</span>
                                </label>
                            @endforeach
                        </div>
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

</x-panel>
