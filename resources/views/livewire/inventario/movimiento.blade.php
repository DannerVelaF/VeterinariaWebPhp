<x-panel title="Gestión de inventario">

    <x-tabs :tabs="['inventario' => 'Inventario', 'entradas' => 'Entradas', 'salidas' => 'Salidas']" default="inventario">

        <x-tab name="inventario">
            <livewire:inventario.lotes />
        </x-tab>

        <x-tab name="entradas">
            <livewire:inventario.entradas />
        </x-tab>

        <x-tab name="salidas">
            <livewire:inventario.salidas />
        </x-tab>
    </x-tabs>

</x-panel>
