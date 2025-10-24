<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="es">

    <head>
        <meta charset="UTF-8">
        <title>ADELA VETERÍNARIA & SPA</title>
        @vite('resources/css/app.css')
        @livewireStyles
        <link rel="shortcut icon" href="/images/pets.png" type="image/x-icon">

    </head>

    <body class="bg-gray-50 antialiased">
        <div class="flex min-h-screen">

            {{-- Sidebar mejorado --}}
            <aside id="default-sidebar"
                class=" w-72 min-h-screen transition-transform -translate-x-full sm:translate-x-0 bg-white shadow-xl border-r border-gray-200"
                aria-label="Sidebar">

                <div class="h-full flex flex-col">
                    {{-- Header del sidebar --}}
                    <div class="px-6 py-6 border-b border-gray-200 bg-gradient-to-br from-gray-50 to-white">
                        <div class="flex flex-col items-center">
                            <div class="relative group mb-4">
                                <div
                                    class="absolute inset-0 bg-blue-100 rounded-full blur-xl opacity-40 group-hover:opacity-60 transition-opacity duration-300">
                                </div>
                                <img src="/images/logo.jpg" alt="Logo ADELA"
                                    class="relative h-20 w-20 rounded-full border-4 border-white shadow-xl transform group-hover:scale-105 transition-transform duration-300">
                            </div>
                            <h2 class="text-xl font-bold text-gray-800 text-center">ADELA</h2>
                            <p class="text-sm text-gray-600 text-center mt-1">Veterinaria & Spa</p>
                        </div>
                    </div>

                    {{-- Navegación --}}
                    <div class="flex-1 overflow-y-auto px-3 py-4">
                        <livewire:menu-lateral />

                    </div>

                    {{-- Footer del sidebar --}}
                    <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                        <div class="flex items-center text-xs text-gray-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Sistema v1.0</span>
                        </div>
                    </div>
                </div>
            </aside>

            {{-- Contenedor principal --}}
            <div class="flex-1  w-full">
                <main class="min-h-screen flex flex-col">
                    {{-- Header mejorado --}}
                    <header class="sticky top-0 z-30 bg-white shadow-sm border-b border-gray-200">
                        <div class="px-6 py-4 flex items-center justify-between">
                            {{-- Título --}}
                            <div class="flex items-center space-x-3">
                                <div>
                                    <h1 class="text-xl font-bold text-gray-800">
                                        Adela Veterinaria & Spa
                                    </h1>
                                    <p class="text-xs text-gray-500 hidden sm:block">Sistema de Gestión Integral</p>
                                </div>
                            </div>

                            {{-- Sección derecha: Usuario --}}
                            <div class="flex items-center space-x-3">
                                {{-- Información del usuario --}}
                                <div class="hidden md:flex flex-col items-end">
                                    <span class="text-sm font-semibold text-gray-800">
                                        {{ Auth::user()->persona->nombre }}
                                        {{ Auth::user()->persona->apellido_paterno }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        {{ Auth::user()->rol->nombre_rol ?? 'Usuario' }}
                                    </span>
                                </div>

                                {{-- Dropdown de usuario --}}
                                <flux:dropdown position="bottom" align="end">
                                    <button type="button"
                                        class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200 focus:ring-2 focus:ring-blue-300 transition-all group">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2" stroke="currentColor"
                                            class="w-6 h-6 text-gray-700 group-hover:text-gray-900">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15.75 7.5a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0zM4.5 20.25a8.25 8.25 0 1 1 15 0" />
                                        </svg>
                                    </button>

                                    <flux:menu class="min-w-48">
                                        {{-- Información móvil --}}
                                        <div class="md:hidden px-4 py-3 border-b border-gray-100">
                                            <p class="text-sm font-semibold text-gray-800">
                                                {{ Auth::user()->persona->nombre }}
                                                {{ Auth::user()->persona->apellido_paterno }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ Auth::user()->email }}
                                            </p>
                                        </div>

                                        <flux:menu.item icon="arrow-right-start-on-rectangle"
                                            onclick="window.location.href='{{ route('logout') }}'"
                                            class="text-red-600 hover:text-red-700 hover:bg-red-50">
                                            Cerrar sesión
                                        </flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </div>
                        </div>
                    </header>

                    {{-- Contenido --}}
                    <div class="flex-1 p-6 bg-gray-50">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>

        @livewireScripts
        @vite('resources/js/app.js')
        @fluxScripts
        {{-- @powerGridScripts Esto define pgRenderActions y toHtml --}}

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        @stack('scripts')
        <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
        <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
        <script src="https://unpkg.com/lucide@latest"></script>
    </body>

</html>
