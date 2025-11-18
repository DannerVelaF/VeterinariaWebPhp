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
            <form wire:submit.prevent="login"
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

                    <div>
                        <h1
                            class="text-3xl font-bold bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">
                            Bienvenido
                        </h1>
                        <p class="text-sm text-gray-600 mt-2">ADELA Veterinaria & Spa</p>
                        <p class="text-xs text-gray-500 mt-1">Ingresa tus credenciales para acceder al sistema</p>
                    </div>
                </div>

                {{-- Alertas mejoradas --}}
                {{-- Alertas mejoradas --}}
                @if ($alertMessage)
                    <div
                        x-data="{
                            show: true,
                            init() {
                                // Ocultar automáticamente después de 5 segundos
                                setTimeout(() => {
                                    this.show = false;
                                }, 5000);
                            }
                        }"
                        x-show="show"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform scale-95"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 transform scale-100"
                        x-transition:leave-end="opacity-0 transform scale-95"
                        class="p-4 rounded-lg flex items-center space-x-3 animate-fade-in
                        @if ($alertType === 'success') bg-green-50 border border-green-200 text-green-700
                        @else
            bg-red-50 border border-red-200 text-red-700 @endif">
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            @if ($alertType === 'success')
                                <path fill-rule="evenodd"
                                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                      clip-rule="evenodd"/>
                            @else
                                <path fill-rule="evenodd"
                                      d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                      clip-rule="evenodd"/>
                            @endif
                        </svg>
                        <span class="text-sm font-medium">{{ $alertMessage }}</span>

                        {{-- Botón para cerrar manualmente --}}
                        <button
                            @click="show = false"
                            class="ml-auto text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                @endif

                {{-- Campo de usuario --}}
                <div class="space-y-2">
                    <label for="username" class="block text-sm font-semibold text-gray-700">
                        Nombre de usuario
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <input type="text" id="username" name="username" wire:model="username"
                               placeholder="Ingresa tu nombre de usuario"
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all bg-white/50"/>
                    </div>
                    @error('username')
                    <div class="flex items-center space-x-1 text-red-600 text-sm mt-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                  clip-rule="evenodd"/>
                        </svg>
                        <span>{{ $message }}</span>
                    </div>
                    @enderror
                </div>

                {{-- Campo de contraseña --}}
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-semibold text-gray-700">
                        Contraseña
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input
                            type="{{ $viewPassword ? 'text' : 'password' }}"
                            id="password"
                            name="password"
                            wire:model="password"
                            placeholder="Ingresa tu contraseña"
                            class="w-full pl-10 pr-12 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all bg-white/50"/>

                        {{-- Botón para mostrar/ocultar contraseña --}}
                        <button
                            type="button"
                            wire:click="verContraseña"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                            @if($viewPassword)
                                {{-- Icono de ojo cerrado (contraseña visible) --}}
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                     fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <path d="m15 18-.722-3.25"/>
                                    <path d="M2 8a10.645 10.645 0 0 0 20 0"/>
                                    <path d="m20 15-1.726-2.05"/>
                                    <path d="m4 15 1.726-2.05"/>
                                    <path d="m9 18 .722-3.25"/>
                                </svg>
                            @else
                                {{-- Icono de ojo abierto (contraseña oculta) --}}
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                     fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            @endif
                        </button>
                    </div>
                    @error('password')
                    <div class="flex items-center space-x-1 text-red-600 text-sm mt-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                  clip-rule="evenodd"/>
                        </svg>
                        <span>{{ $message }}</span>
                    </div>
                    @enderror
                </div>

                {{-- Botón de continuar --}}
                <button type="submit"
                        class="w-full bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 text-white font-semibold py-3 rounded-lg hover:from-blue-700 hover:via-purple-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transform hover:scale-[1.02] transition-all duration-200 shadow-lg">
                    <span class="flex items-center justify-center space-x-2">
                        <span>Iniciar sesión</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </span>
                </button>

                {{-- Texto de ayuda --}}
                <div class="text-center pt-4 border-t border-gray-200">

                    <p class="text-xs text-gray-500">
                        Olvidaste tu contraseña? <a
                            class="text-purple-600 hover:text-purple-700 font-medium transition-all ease-in-out"
                            href="{{route("restablecer.contrasena")}}">Restablece tu
                            contraseña</a>
                    </p>
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
    <x-loader target="login"/>
</div>
