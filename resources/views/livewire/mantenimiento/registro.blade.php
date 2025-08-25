<div>
    {{-- In work, do what you enjoy. --}}
    <p>Mantenimiento de tablas</p>
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'proveedores' ? 'active' : '' }}" href="#"
                wire:click.prevent="setTab('proveedores')">
                Proveedores
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'productos' ? 'active' : '' }}" href="#"
                wire:click.prevent="setTab('productos')">
                Productos
            </a>
        </li>
    </ul>
    <div class="mt-3">
        @if ($tab === 'proveedores')
            <livewire:mantenimiento.proveedores />
        @elseif($tab === 'productos')
            <p>Productos</p>
        @endif
    </div>
</div>
