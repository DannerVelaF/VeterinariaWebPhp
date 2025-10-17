<div>
    <p>Mantenimiento de tablas</p>
    <x-tabs :tabs="['trabajadores' => 'Trabajadores', 'puestos' => 'Puestos', 'ubigeos' => 'Ubigeos', 'turnos' => 'Turnos']" default="trabajadores">
        <!-- TAB 1: TRABAJAOORES -->
        <x-tab name="trabajadores">
            <livewire:mantenimiento.trabajadores.trabajadores />
        </x-tab>

        <!-- TAB 1: PUESTSOS DE TRABAJO -->
        <x-tab name="puestos">
            <livewire:mantenimiento.trabajadores.puestos />
        </x-tab>
        <x-tab name="turnos">
            <livewire:mantenimiento.trabajadores.turnos />
        </x-tab>
        <x-tab name="ubigeos">
            <livewire:mantenimiento.trabajadores.ubigeos />
        </x-tab>
    </x-tabs>
</div>
