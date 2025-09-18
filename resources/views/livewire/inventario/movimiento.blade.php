<x-panel title="Gestión de inventario">

    <x-tabs :tabs="['lotes' => 'Lotes', 'entradas' => 'Entradas', 'Salidas' => 'Salidas']" default="lotes">
        <x-tab name="lotes">
            <livewire:inventario.lotes />
        </x-tab>

        <x-tab name="entradas">
            <livewire:inventario.entradas />
        </x-tab>
    </x-tabs>

</x-panel>
