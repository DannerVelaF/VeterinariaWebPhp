<div>
    <div
        class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 relative overflow-hidden">
        {{-- Círculos decorativos de fondo --}}
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
            <form wire:submit.prevent="verifyCode"
                class="bg-white/80 backdrop-blur-lg p-8 rounded-2xl shadow-2xl space-y-6 border border-white/20">

                {{-- Icono de seguridad --}}
                <div class="flex justify-center mb-4">
                    <div class="relative">
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 rounded-full blur-lg opacity-40 animate-pulse">
                        </div>
                        <div
                            class="relative w-20 h-20 bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 rounded-full flex items-center justify-center shadow-2xl">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Título y descripción --}}
                <div class="text-center space-y-2">
                    <h2
                        class="text-2xl font-bold bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">
                        Autenticación en dos pasos
                    </h2>
                    <p class="text-sm text-gray-600">
                        Ingrese el código de verificación enviado a su correo electrónico
                    </p>
                </div>

                {{-- Alertas mejoradas --}}
                @if ($alertMessage)
                    <div
                        class="p-4 rounded-lg flex items-center space-x-3 animate-fade-in
                    @if ($alertType === 'success') bg-green-50 border border-green-200 text-green-700
                    @else 
                        bg-red-50 border border-red-200 text-red-700 @endif">
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            @if ($alertType === 'success')
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            @else
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            @endif
                        </svg>
                        <span class="text-sm font-medium">{{ $alertMessage }}</span>
                    </div>
                @endif

                {{-- Campo de código con icono --}}
                <div class="space-y-2">
                    <label for="inputCode" class="block text-sm font-semibold text-gray-700">
                        Código de verificación
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                            </svg>
                        </div>
                        <input type="text" id="inputCode" wire:model.defer="inputCode"
                            placeholder="Ingrese el código de 6 dígitos" maxlength="6"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all bg-white/50 text-center text-2xl font-mono tracking-widest" />
                    </div>
                    <p class="text-xs text-gray-500 text-center mt-2">
                        El código expirará en 10 minutos
                    </p>
                </div>

                {{-- Botón de verificar --}}
                <button type="submit"
                    class="w-full bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 text-white font-semibold py-3 rounded-lg hover:from-blue-700 hover:via-purple-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transform hover:scale-[1.02] transition-all duration-200 shadow-lg">
                    <span class="flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Verificar código</span>
                    </span>
                </button>

                {{-- Separador --}}
                <div class="relative py-4">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-xs uppercase">
                        <span class="bg-white px-2 text-gray-500">o</span>
                    </div>
                </div>

                {{-- Opciones adicionales --}}
                <div class="space-y-3">
                    {{-- Reenviar código --}}
                    {{-- <button type="button" wire:click="resendCode"
                        class="w-full flex items-center justify-center space-x-2 text-sm text-purple-600 hover:text-purple-700 font-medium py-2 px-4 rounded-lg hover:bg-purple-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>Reenviar código</span>
                    </button> --}}

                    {{-- Volver al login --}}
                    <button type="button" wire:click="backToLogin"
                        class="w-full flex items-center justify-center space-x-2 text-sm text-gray-600 hover:text-gray-700 font-medium py-2 px-4 rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span>Volver al inicio de sesión</span>
                    </button>
                </div>

                {{-- Información de seguridad --}}
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-4">
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                        <div class="flex-1">
                            <p class="text-xs text-blue-800 font-medium">Consejo de seguridad</p>
                            <p class="text-xs text-blue-700 mt-1">
                                No comparta este código con nadie. Nuestro equipo nunca le pedirá este código.
                            </p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <style>
        @keyframes blob {
            0% {
                transform: translate(0px, 0px) scale(1);
            }

            33% {
                transform: translate(30px, -50px) scale(1.1);
            }

            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }

            100% {
                transform: translate(0px, 0px) scale(1);
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

        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }
    </style>
</div>
