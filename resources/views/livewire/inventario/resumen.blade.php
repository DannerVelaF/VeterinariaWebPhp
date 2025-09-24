<div>
    <div class="grid grid-cols-5 gap-6 mb-4 h-full">
        <x-card>
            <div class="h-[100px] flex flex-col justify-between">
                <div class="flex justify-between items-center">
                    <p>Total lotes</p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-clipboard-clock-icon lucide-clipboard-clock">
                        <path d="M16 14v2.2l1.6 1" />
                        <path d="M16 4h2a2 2 0 0 1 2 2v.832" />
                        <path d="M8 4H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h2" />
                        <circle cx="16" cy="16" r="6" />
                        <rect x="8" y="2" width="8" height="4" rx="1" />
                    </svg>
                </div>
                <p class="font-medium text-3xl">{{ $totalLotes }}</p>
            </div>
        </x-card>
        <x-card>
            <div class="h-[100px] flex flex-col justify-between">
                <div class="flex justify-between items-center">
                    <p>Stock total</p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-clipboard-clock-icon lucide-clipboard-clock">
                        <path d="M16 14v2.2l1.6 1" />
                        <path d="M16 4h2a2 2 0 0 1 2 2v.832" />
                        <path d="M8 4H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h2" />
                        <circle cx="16" cy="16" r="6" />
                        <rect x="8" y="2" width="8" height="4" rx="1" />
                    </svg>
                </div>
                <p class="font-medium text-3xl">{{ $stockTotal }}</p>
            </div>
        </x-card>
        <x-card>
            <div class="h-[100px] flex flex-col justify-between">
                <div class="flex justify-between items-center">
                    <p>Almacen</p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-clipboard-clock-icon lucide-clipboard-clock">
                        <path d="M16 14v2.2l1.6 1" />
                        <path d="M16 4h2a2 2 0 0 1 2 2v.832" />
                        <path d="M8 4H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h2" />
                        <circle cx="16" cy="16" r="6" />
                        <rect x="8" y="2" width="8" height="4" rx="1" />
                    </svg>
                </div>
                <p class="font-medium text-3xl">{{ $stockAlmacen }}</p>
            </div>
        </x-card>
        <x-card>
            <div class="h-[100px] flex flex-col justify-between">
                <div class="flex justify-between items-center">
                    <p>Mostrador</p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-clipboard-clock-icon lucide-clipboard-clock">
                        <path d="M16 14v2.2l1.6 1" />
                        <path d="M16 4h2a2 2 0 0 1 2 2v.832" />
                        <path d="M8 4H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h2" />
                        <circle cx="16" cy="16" r="6" />
                        <rect x="8" y="2" width="8" height="4" rx="1" />
                    </svg>
                </div>
                <p class="font-medium text-3xl">{{ $stockMostrar }}</p>
            </div>
        </x-card>
        <x-card>
            <div class="h-[100px] flex flex-col justify-between">
                <div class="flex justify-between items-center">
                    <p>Vendido</p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-clipboard-clock-icon lucide-clipboard-clock">
                        <path d="M16 14v2.2l1.6 1" />
                        <path d="M16 4h2a2 2 0 0 1 2 2v.832" />
                        <path d="M8 4H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h2" />
                        <circle cx="16" cy="16" r="6" />
                        <rect x="8" y="2" width="8" height="4" rx="1" />
                    </svg>
                </div>
                <p class="font-medium text-3xl">{{ $stockVendido }}</p>
            </div>
        </x-card>
    </div>
    <div class="grid grid-cols-2 gap-4 h-[570px]">
        <x-card class="h-full">
            <div>
                <p class="font-medium text-gray-600 text-xl">Alertas de Stock Bajo</p>
                <p class="text-sm">Productos con menos de 10 unidades.</p>
            </div>

            @if ($stockBajo->count() > 0)
                <div>
                    <span>Productos con stock bajo:</span>
                </div>
            @else
                <div class="text-center flex items-center justify-center flex-col">
                    <span>No hay productos con stock bajo.</span>
                </div>
            @endif

        </x-card>
        <x-card>
            <div>
                <p class="font-medium text-gray-600 text-xl">Actividad Reciente</p>
                <p class="text-sm">Últimas 5 transacciones registradas.</p>
            </div>
        </x-card>
    </div>
</div>
