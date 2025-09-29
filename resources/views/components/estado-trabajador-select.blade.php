@props(['options', 'selected', 'trabajadorId'])

<div>
    <select wire:change="$dispatch('cambiar-estado', { id: {{ $trabajadorId }}, estado: $event.target.value })"
        class="border rounded px-2 py-1 text-sm">
        @foreach ($options as $id => $nombre)
            <option value="{{ $id }}" @selected($id == $selected)>
                {{ $nombre }}
            </option>
        @endforeach
    </select>
</div>
