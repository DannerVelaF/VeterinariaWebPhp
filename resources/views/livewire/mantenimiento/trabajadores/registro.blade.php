<div>
    {{-- In work, do what you enjoy. --}}
    <span>{{ $userId ?? 'No hay usuario' }}</span>
    <p>Mantenimiento de tablas</p>
    <x-tabs :tabs="['trabajadores' => 'Trabajadores', 'puestos' => 'Puestos']" default="trabajadores">
        <!-- TAB 1: TRABAJAOORES -->
        <x-tab name="trabajadores">
            <livewire:mantenimiento.trabajadores.trabajadores />
        </x-tab>

        <!-- TAB 1: PUESTSOS DE TRABAJO -->
        <x-tab name="puestos">
            <livewire:mantenimiento.trabajadores.puestos />
        </x-tab>
    </x-tabs>
</div>
