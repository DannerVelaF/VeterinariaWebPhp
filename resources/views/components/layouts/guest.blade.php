<!DOCTYPE html>
<html lang="es">

    <head>
        <meta charset="UTF-8">
        <title>{{ $title ?? "Mi App" }}</title>
        @vite("resources/css/app.css") <!-- si usas Vite -->
        @livewireStyles
        <script src="//unpkg.com/alpinejs" defer></script>
    </head>

    <body class="bg-gray-100 max-h-screen">
        {{ $slot }}
    </body>
    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

</html>
