<div>
    {{-- In work, do what you enjoy. --}}
    <p>Mantenimiento de tablas</p>
    <x-tabs :tabs="['proveedores' => 'Proveedores', 'productos' => 'Productos', 'categorias' => 'Categorias']" default="proveedores">
        <!-- TAB 1: PROVEEDORES -->
        <x-tab name="proveedores">
            <livewire:mantenimiento.productos.proveedores />
        </x-tab>
        <!-- TAB 2: PRODUCTOS -->
        <x-tab name="productos">
            <livewire:mantenimiento.productos.productos />
        </x-tab>
        <x-tab name="categorias">
            <livewire:mantenimiento.productos.categoria />
        </x-tab>
    </x-tabs>
</div>
