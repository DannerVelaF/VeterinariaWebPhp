<div>
    {{-- In work, do what you enjoy. --}}
    <p>Mantenimiento de Tablas</p>
    <x-tabs :tabs="[
        'servicios' => 'Servicios',
        'categorias' => 'Categorias',
    ]" default="servicios">
        <!-- TAB 1: SERVICIOS -->
        <x-tab name="servicios">
            <livewire:mantenimiento.servicios.servicios />
        </x-tab>
        <!-- TAB 2: CATEGORIAS -->
        <x-tab name="categorias">
            <livewire:mantenimiento.servicios.categoria />
        </x-tab>
    </x-tabs>
</div>
