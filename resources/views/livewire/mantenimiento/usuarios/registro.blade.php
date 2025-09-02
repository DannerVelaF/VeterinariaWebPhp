<div>
    <p>Mantenimiento de tablas</p>
    <x-tabs :tabs="['usuarios' => 'Usuarios', 'roles' => 'Roles', 'permisos' => 'Permisos']" default="usuarios">
        <!-- TAB 1: Usuarios -->
        <x-tab name="usuarios">
            <livewire:mantenimiento.usuarios.usuarios />
        </x-tab>

        <!-- TAB 1: PUESTSOS DE TRABAJO -->
        <x-tab name="roles">
            <livewire:mantenimiento.usuarios.roles />
        </x-tab>
        <x-tab name="permisos">
            <livewire:mantenimiento.usuarios.permisos />
        </x-tab>
    </x-tabs>
</div>
