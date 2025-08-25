<div>
    {{-- In work, do what you enjoy. --}}
    <p>Mantenimiento de tablas</p>
    <x-tabs :tabs="['proveedores' => 'Proveedores', 'productos' => 'Productos', 'usuarios' => 'Usuarios']" default="proveedores">
        <!-- TAB 1: PROVEEDORES -->
        <x-tab name="proveedores">
            <livewire:mantenimiento.proveedores />
        </x-tab>
        <!-- TAB 2: PRODUCTOS -->
        <x-tab name="productos">
        </x-tab>
    </x-tabs>
</div>
