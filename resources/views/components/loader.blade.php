<div id="loader" wire:loading.flex class="fixed inset-0 bg-black/50 z-50 items-center justify-center">
    <div class="bg-white p-6 rounded-xl shadow-lg flex flex-col items-center gap-4">
        <!-- Icono de perro desde public/images -->
        <img src="{{ asset('images/dogLoader.svg') }}" class="h-16 w-16 animate-spin" alt="Cargando...">
        <p class="text-gray-700 font-medium text-lg">Procesando su consulta...</p>
    </div>
</div>
