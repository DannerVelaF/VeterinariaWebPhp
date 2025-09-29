<div>
    <x-card>
        <div>
            <p class="text-md font-medium">Gestión de lotes</p>
            <p class="text-sm font-normal">Administra todos los lotes de productos en tu inventario.</p>
        </div>
        <livewire:lotes-table />
    </x-card>
    <x-loader />
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Cuando PowerGrid empieza a cargar
            Livewire.on('pg:processing', () => {
                document.getElementById('loader').style.display = 'flex';
            });

            // Cuando PowerGrid termina de cargar
            Livewire.on('pg:processed', () => {
                document.getElementById('loader').style.display = 'none';
            });
        });
    </script>
</div>
