<div class="min-h-screen flex items-center justify-center bg-gray-100 relative">
    <form wire:submit.prevent="login" class="w-full max-w-sm bg-white p-6 rounded-md shadow space-y-4">
        <div class="text-center">
            <p class="text-xl font-semibold">Bienvenido</p>
            <p class="text-sm text-gray-600">Ingresa tus credenciales para ingresar al sistema</p>
        </div>

        {{-- Alertas --}}
        @if ($alertMessage)
            <div
                class="p-2 mb-4 text-sm rounded @if ($alertType === "success") bg-green-100 text-green-700 @else bg-red-100 text-red-700 @endif">
                {{ $alertMessage }}
            </div>
        @endif

        <div class="w-full">
            <label for="username" class="block text-sm font-medium mb-1">Nombre de usuario</label>
            <input type="text" id="username" name="username" wire:model="username"
                placeholder="Ingresa tu nombre de usuario"
                class="w-full border rounded p-2 focus:outline-none focus:ring focus:ring-green-200" />
            @error("username")
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div class="w-full">
            <label for="password" class="block text-sm font-medium mb-1">Contraseña</label>
            <input type="password" id="password" name="password" wire:model="password"
                placeholder="Ingresa tu contraseña"
                class="w-full border rounded p-2 focus:outline-none focus:ring focus:ring-green-200" />
            @error("password")
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700 transition">
            Continuar
        </button>
    </form>
</div>
