<div>
    <p>Inventario</p>
    <x-tabs :tabs="['detalle' => 'Detalle Inventario', 'registro' => 'Registar movimiento']" default="detalle">
        <x-tab name="registro">
            <livewire:inventario.movimiento />
        </x-tab>

    </x-tabs>
</div>
