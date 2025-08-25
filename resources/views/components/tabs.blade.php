<div x-data="{ tab: '{{ $default }}' }" class="w-full">
    <!-- Encabezado de tabs -->
    <ul class="flex border-b mb-4 text-xs font-bold">
        @foreach ($tabs as $key => $title)
            <li>
                <button class="px-4 py-2 border-b-2"
                    :class="tab === '{{ $key }}' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500'"
                    @click="tab = '{{ $key }}'">
                    {{ $title }}
                </button>
            </li>
        @endforeach
    </ul>

    <!-- Contenido dinámico -->
    <div class="mt-2">
        {{ $slot }}
    </div>
</div>
