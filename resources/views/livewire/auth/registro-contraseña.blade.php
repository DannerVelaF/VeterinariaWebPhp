<div>
    <div
        class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 relative overflow-hidden">
        {{-- Fondos decorativos --}}
        <div
            class="absolute top-0 left-0 w-72 h-72 bg-blue-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob">
        </div>
        <div
            class="absolute top-0 right-0 w-72 h-72 bg-purple-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-2000">
        </div>
        <div
            class="absolute -bottom-8 left-20 w-72 h-72 bg-pink-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-4000">
        </div>

        {{-- Contenedor del formulario --}}
        <div class="relative z-10 w-full max-w-md px-6">
            <form wire:submit.prevent="guardar"
                class="bg-white/80 backdrop-blur-lg p-8 rounded-2xl shadow-2xl space-y-6 border border-white/20">

                {{-- Logo y título --}}
                <div class="text-center space-y-4">
                    <div class="flex justify-center mb-4">
                        <div class="relative group">
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 rounded-full blur-lg opacity-40 group-hover:opacity-60 transition-opacity duration-300">
                            </div>
                            <img src="/images/logo.jpg" alt="Logo ADELA"
                                class="relative h-24 w-24 rounded-full border-4 border-white shadow-2xl transform group-hover:scale-110 transition-transform duration-300">
                        </div>
                    </div>
                    <h1
                        class="text-3xl font-bold bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">
                        ¡Bienvenido!
                    </h1>
                    <p class="text-sm text-gray-600">ADELA Veterinaria & Spa</p>
                    <p class="text-xs text-gray-500">Crea tu contraseña para continuar</p>
                </div>

                {{-- Campo de nueva contraseña --}}
                <div>
                    <label for="newPassword" class="block text-sm font-semibold text-gray-700">Nueva contraseña</label>
                    <input type="password" id="newPassword" wire:model.live="newPassword"
                        placeholder="Mínimo 8 caracteres"
                        class="w-full pl-3 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 bg-white/50" />
                    @error('newPassword')
                        <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Confirmación --}}
                <div>
                    <label for="newPassword_confirmation" class="block text-sm font-semibold text-gray-700">
                        Confirmar contraseña
                    </label>
                    <input type="password" id="newPassword_confirmation" wire:model.live="newPassword_confirmation"
                        placeholder="Repite tu contraseña"
                        class="w-full pl-3 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 bg-white/50" />
                    @error('newPassword_confirmation')
                        <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Indicador de seguridad --}}
                @if ($newPassword)
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span>Seguridad:</span>
                            <span
                                class="font-semibold 
                                @if ($passwordStrength === 'débil') text-red-600
                                @elseif($passwordStrength === 'media') text-yellow-600
                                @else text-green-600 @endif">
                                {{ ucfirst($passwordStrength) }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 h-2 rounded-full">
                            <div
                                class="h-2 rounded-full transition-all duration-300
                                @if ($passwordStrength === 'débil') bg-red-500 w-1/3
                                @elseif($passwordStrength === 'media') bg-yellow-500 w-2/3
                                @else bg-green-500 w-full @endif">
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Botón --}}
                <button type="submit"
                    class="w-full bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 text-white font-semibold py-3 rounded-lg hover:from-blue-700 hover:via-purple-700 hover:to-pink-700 transition-all shadow-lg disabled:opacity-50"
                    {{ $newPassword && $newPassword_confirmation && $newPassword === $newPassword_confirmation ? '' : 'disabled' }}>
                    Crear contraseña
                </button>
            </form>
        </div>
    </div>

    {{-- Script para SweetAlert --}}
    @push('scripts')
        <script>
            Livewire.on('notify', (data) => {
                Swal.fire({
                    title: data.title,
                    text: data.description,
                    icon: data.type,
                    timer: 2500,
                    showConfirmButton: false,
                    customClass: {
                        popup: 'rounded-lg',
                        title: 'text-lg font-semibold',
                        htmlContainer: 'text-sm'
                    }
                });
            });
        </script>
    @endpush

    {{-- Animaciones --}}
    <style>
        @keyframes blob {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            33% {
                transform: translate(30px, -50px) scale(1.1);
            }

            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }
        }

        .animate-blob {
            animation: blob 7s infinite;
        }

        .animation-delay-2000 {
            animation-delay: 2s;
        }

        .animation-delay-4000 {
            animation-delay: 4s;
        }
    </style>
    <x-loader />
</div>
