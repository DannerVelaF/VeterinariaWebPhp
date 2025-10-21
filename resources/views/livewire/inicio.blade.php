    {{-- Knowing others is intelligence; knowing yourself is true wisdom. --}}
    <div class="bg-gradient-to-br from-blue-50 via-white to-purple-50 min-h-[90vh] flex items-center justify-center p-8">
        <div class="max-w-4xl w-full">
            {{-- Contenedor principal con animación --}}
            <div class="text-center space-y-8 animate-fade-in">

                {{-- Logo con efecto de sombra y hover --}}
                <div class="flex justify-center mb-6">
                    <div class="relative group">
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-blue-400 to-purple-400 rounded-full blur-lg opacity-30 group-hover:opacity-50 transition-opacity duration-300">
                        </div>
                        <img src="/images/logo.jpg" alt="Logo ADELA"
                            class="relative h-32 w-32 rounded-full border-4 border-white shadow-2xl transform group-hover:scale-105 transition-transform duration-300">
                    </div>
                </div>

                {{-- Título principal --}}
                <div class="space-y-4">
                    <h1
                        class="text-5xl md:text-6xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        Bienvenido
                    </h1>
                    <h2 class="text-3xl md:text-4xl font-semibold text-gray-800">
                        ADELA VETERINARIA & SPA
                    </h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        Sistema integral de gestión veterinaria. Cuidando a tus mejores amigos con amor y
                        profesionalismo.
                    </p>
                </div>

                {{-- Cards informativos --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12">
                    {{-- Card 1 --}}
                    <div
                        class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow duration-300 border border-gray-100">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-800 mb-2">Gestión de Citas</h3>
                        <p class="text-sm text-gray-600">Organiza y administra las consultas de manera eficiente</p>
                    </div>

                    {{-- Card 2 --}}
                    <div
                        class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow duration-300 border border-gray-100">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-800 mb-2">Historial Clínico</h3>
                        <p class="text-sm text-gray-600">Accede al registro completo de cada paciente</p>
                    </div>

                    {{-- Card 3 --}}
                    <div
                        class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow duration-300 border border-gray-100">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-800 mb-2">Control Completo</h3>
                        <p class="text-sm text-gray-600">Supervisa todos los aspectos de tu clínica</p>
                    </div>
                </div>

                {{-- Indicador de navegación --}}
                <div class="mt-12 pt-8 border-t border-gray-200">
                    <p class="text-sm text-gray-500 flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                        Utiliza el menú lateral para navegar por el sistema
                    </p>
                </div>
            </div>
        </div>
        <style>
            @keyframes fade-in {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .animate-fade-in {
                animation: fade-in 0.6s ease-out;
            }
        </style>
    </div>
