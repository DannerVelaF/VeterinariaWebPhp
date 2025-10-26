@props(['target' => null])

<div id="loader" wire:loading.flex @if ($target) wire:target="{{ $target }}" @endif
    class="fixed inset-0 bg-black/50 z-[99999] items-center justify-center" style="z-index: 99999 !important;">
    <div class="bg-white p-6 rounded-xl shadow-lg flex flex-col items-center gap-4">
        <!-- Icono de perro desde public/images -->
        <img src="{{ asset('images/dogLoader.svg') }}" class="h-16 w-16 animate-spin" alt="Cargando...">
        <p class="text-gray-700 font-medium text-lg">Procesando su consulta...</p>
    </div>
</div>
