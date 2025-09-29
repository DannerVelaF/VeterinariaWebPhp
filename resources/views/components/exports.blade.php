<div class="relative inline-block text-left" x-data="{ open: false }">
    <button @click="open = !open"
        class="inline-flex items-center gap-2 px-4 py-2 h-10 bg-gray-100 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-700 hover:text-white transition">
        <!-- Icono -->
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-download">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
            <polyline points="7 10 12 15 17 10" />
            <line x1="12" x2="12" y1="15" y2="3" />
        </svg>
        Exportar
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <!-- Dropdown -->
    <div x-show="open" @click.outside="open = false"
        class="absolute mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
        <ul class="py-1 text-gray-700">
            <li>
                <button wire:click="exportarExcel" class="flex items-center w-full px-4 py-2 hover:bg-gray-100">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-file-spreadsheet mr-2">
                        <path d="M16 16h6v6h-6z" />
                        <path d="M22 16V4a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h6" />
                        <line x1="6" x2="22" y1="10" y2="10" />
                        <line x1="6" x2="22" y1="6" y2="6" />
                        <line x1="6" x2="10" y1="14" y2="14" />
                        <line x1="6" x2="10" y1="18" y2="18" />
                    </svg>
                    Excel
                </button>
            </li>
            <li>
                <button wire:click="exportarPdf" class="flex items-center w-full px-4 py-2 hover:bg-gray-100">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-file-text mr-2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        <path d="M14 2v6h6" />
                        <line x1="16" x2="8" y1="13" y2="13" />
                        <line x1="16" x2="8" y1="17" y2="17" />
                        <line x1="10" x2="8" y1="9" y2="9" />
                    </svg>
                    PDF
                </button>
            </li>
        </ul>
    </div>
</div>
