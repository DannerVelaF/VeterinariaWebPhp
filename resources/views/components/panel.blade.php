<div class="mt-5">
    {{-- Título del panel --}}
    <p class="text-sm border-gray-200 border p-2 font-medium bg-white/90">{{ $title }}</p>

    {{-- Contenido del panel --}}
    <div class="border-gray-200 border p-2 bg-white">
        {{ $slot }}
    </div>
</div>
