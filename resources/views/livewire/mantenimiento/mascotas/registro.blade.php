<div>
    {{-- In work, do what you enjoy. --}}
    <p>Mantenimiento de Mascotas</p>
    <x-tabs :tabs="[
        'razas' => 'Razas',
        'especies' => 'Especies'
    ]" default="especies">

         <!-- TAB 2: RAZAS -->
         <x-tab name="razas">
            <livewire:mantenimiento.mascotas.razas />
        </x-tab>

        <!-- TAB 3: ESPECIES -->
         <x-tab name="especies">
            <livewire:mantenimiento.mascotas.especies />
        </x-tab>
        
    </x-tabs>
</div>
