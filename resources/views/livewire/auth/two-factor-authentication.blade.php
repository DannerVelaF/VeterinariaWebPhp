<div class="min-h-screen flex items-center justify-center bg-gray-100 relative">

    <form wire:submit.prevent="verifyCode" class="w-sm bg-white p-6 rounded-md shadow space-y-4">

        <h2 class="text-xl font-semibold text-center mb-4">Autenticación en dos pasos</h2>
        <p class="text-center text-gray-600 mb-4">Ingrese el código enviado a su correo</p>

        {{-- Alertas --}}
        @if ($alertMessage)
            <div
                class="p-2 mb-4 text-sm rounded @if ($alertType === 'success') bg-green-100 text-green-700 @else bg-red-100 text-red-700 @endif">
                {{ $alertMessage }}
            </div>
        @endif

        <input type="text" wire:model.defer="inputCode" placeholder="Código de verificación"
            class="w-full border rounded p-2 focus:outline-none focus:ring focus:ring-green-200" />

        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
            Verificar
        </button>
    </form>
</div>
