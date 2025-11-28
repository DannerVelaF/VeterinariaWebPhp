<div class="bg-white rounded-xl shadow-lg border border-gray-200 flex flex-col h-full max-h-[calc(100vh-6rem)]">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50 rounded-t-xl shrink-0">
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            💰 Control de Caja
        </h3>
        <div
            class="flex items-center gap-2 px-3 py-1 rounded-full {{ $cajaActual ? 'bg-green-100 border border-green-200' : 'bg-red-100 border border-red-200' }}">
            <div class="w-2.5 h-2.5 rounded-full {{ $cajaActual ? 'bg-green-500' : 'bg-red-500' }}"></div>
            <span
                class="text-xs font-bold uppercase tracking-wide {{ $cajaActual ? 'text-green-700' : 'text-red-700' }}">
                {{ $cajaActual ? 'Abierta' : 'Cerrada' }}
            </span>
        </div>
    </div>

    <div class="p-6 overflow-y-auto flex-1 custom-scrollbar space-y-6">

        @if(!$cajaActual)
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-5 shadow-sm">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-blue-100 rounded-lg text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </div>
                    <h4 class="font-bold text-blue-900">Iniciar Operaciones</h4>fe
                </div>

                <form wire:submit="abrirCaja" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-blue-800 uppercase tracking-wider mb-1">Monto
                            Inicial</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-bold">S/</span>
                            <input type="number" step="0.01" wire:model="montoInicial"
                                   class="w-full pl-9 pr-4 py-2.5 bg-white border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-bold text-gray-800 transition-all placeholder-gray-300"
                                   placeholder="0.00">
                        </div>
                        @error('montoInicial') <span
                            class="text-red-500 text-xs font-medium mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label
                            class="block text-xs font-bold text-blue-800 uppercase tracking-wider mb-1">Observaciones</label>
                        <textarea wire:model="observacionesApertura" rows="2"
                                  class="w-full px-4 py-2 bg-white border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 transition-all text-sm"
                                  placeholder="Ej: Inicio de turno mañana..."></textarea>
                    </div>

                    <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-bold shadow-md hover:shadow-lg transition-all transform active:scale-95 flex items-center justify-center gap-2">
                        <span>🚀 Abrir Caja Ahora</span>
                    </button>
                </form>
            </div>
        @else
            <div class="grid grid-cols-2 gap-3">
                <div
                    class="col-span-2 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-4 border border-gray-200">
                    <p class="text-xs text-gray-500 font-medium uppercase text-center mb-1">Total Esperado en Caja</p>
                    <p class="text-3xl font-black text-center text-gray-800 tracking-tight">
                        S/ {{ number_format($cajaActual->monto_inicial + $cajaActual->total_ventas, 2) }}
                    </p>
                </div>

                <div class="bg-green-50 p-3 rounded-lg border border-green-100 text-center">
                    <p class="text-[10px] uppercase font-bold text-green-600 mb-0.5">Monto Inicial</p>
                    <p class="font-bold text-green-700 text-lg">
                        S/ {{ number_format($cajaActual->monto_inicial, 2) }}</p>
                </div>
                <div class="bg-orange-50 p-3 rounded-lg border border-orange-100 text-center">
                    <p class="text-[10px] uppercase font-bold text-orange-600 mb-0.5">Total Ventas</p>
                    <p class="font-bold text-orange-700 text-lg">
                        S/ {{ number_format($cajaActual->total_ventas, 2) }}</p>
                </div>
            </div>

            <div class="space-y-2">
                <h5 class="text-xs font-bold text-gray-400 uppercase tracking-widest text-center">Desglose de
                    Ventas</h5>
                <div class="grid grid-cols-4 gap-2">
                    <div class="flex flex-col items-center p-2 bg-blue-50 rounded-lg border border-blue-100">
                        <span class="text-[10px] text-blue-500 font-bold">Efectivo</span>
                        <span
                            class="text-sm font-bold text-blue-700">{{ number_format($cajaActual->ventas_efectivo, 2) }}</span>
                    </div>
                    <div class="flex flex-col items-center p-2 bg-purple-50 rounded-lg border border-purple-100">
                        <span class="text-[10px] text-purple-500 font-bold">Digital</span>
                        <span
                            class="text-sm font-bold text-purple-700">{{ number_format($cajaActual->ventas_digital, 2) }}</span>
                    </div>
                    <div class="flex flex-col items-center p-2 bg-green-50 rounded-lg border border-green-100">
                        <span class="text-[10px] text-green-500 font-bold">Tarjeta</span>
                        <span
                            class="text-sm font-bold text-green-700">{{ number_format($cajaActual->ventas_tarjeta, 2) }}</span>
                    </div>
                    <div class="flex flex-col items-center p-2 bg-yellow-50 rounded-lg border border-yellow-100">
                        <span class="text-[10px] text-yellow-600 font-bold">Transf.</span>
                        <span
                            class="text-sm font-bold text-yellow-700">{{ number_format($cajaActual->ventas_transferencia, 2) }}</span>
                    </div>
                </div>
            </div>

            <hr class="border-gray-100">

            <div>
                <h4 class="font-bold text-gray-800 mb-3 flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Realizar Arqueo
                </h4>

                <form wire:submit="cerrarCaja" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div class="group">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Efectivo en Caja *</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold">S/</span>
                                <input type="number" step="0.01" wire:model="montoFinal"
                                       class="w-full pl-8 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all font-semibold text-gray-800"
                                       placeholder="0.00">
                            </div>
                            <div>
                                <div class="mt-1 flex justify-between text-xs">
        <span class="text-gray-500">
            Base ({{ $cajaActual->monto_inicial }}) + Ventas Efectivo ({{ $cajaActual->ventas_efectivo }})
        </span>
                                    <span
                                        class="font-bold {{ ($montoFinal - ($cajaActual->monto_inicial + $cajaActual->ventas_efectivo)) < 0 ? 'text-red-500' : 'text-green-600' }}">
             Esperado: S/ {{ number_format($cajaActual->monto_inicial + $cajaActual->ventas_efectivo, 2) }}
        </span>
                                </div>
                            </div>
                            @error('montoFinal') <span
                                class="text-red-500 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="group">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Voucher Tarjetas *</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold">S/</span>
                                <input type="number" step="0.01" wire:model="montoTarjetas"
                                       class="w-full pl-8 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all font-semibold text-gray-800">
                            </div>
                            <p class="text-[10px] text-gray-400 mt-0.5 text-right">
                                Sis: {{ number_format($cajaActual->ventas_tarjeta, 2) }}</p>
                            @error('montoTarjetas') <span
                                class="text-red-500 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="group">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Transferencias *</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold">S/</span>
                                <input type="number" step="0.01" wire:model="montoTransferencias"
                                       class="w-full pl-8 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all font-semibold text-gray-800">
                            </div>
                            <p class="text-[10px] text-gray-400 mt-0.5 text-right">
                                Sis: {{ number_format($cajaActual->ventas_transferencia, 2) }}</p>
                            @error('montoTransferencias') <span
                                class="text-red-500 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="group">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Yape/Plin/Otros *</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold">S/</span>
                                <input type="number" step="0.01" wire:model="montoDigital"
                                       class="w-full pl-8 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all font-semibold text-gray-800">
                            </div>
                            <p class="text-[10px] text-gray-400 mt-0.5 text-right">
                                Sis: {{ number_format($cajaActual->ventas_digital, 2) }}</p>
                            @error('montoDigital') <span
                                class="text-red-500 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="pt-4 flex gap-3">
                        <button type="submit"
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2.5 rounded-lg font-bold shadow-md hover:shadow-lg transition-all transform active:scale-95 text-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Cerrar Caja
                        </button>
                        <button type="button" wire:click="$refresh"
                                class="px-4 bg-gray-100 hover:bg-gray-200 text-gray-600 border border-gray-200 rounded-lg font-medium transition-colors text-sm"
                                title="Actualizar montos">
                            🔄
                        </button>
                    </div>
                </form>
            </div>
        @endif

        @error('caja')
        <div class="bg-red-50 border-l-4 border-red-500 p-3 rounded-r-lg animate-pulse">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                              d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                              clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700 font-medium">{{ $message }}</p>
                </div>
            </div>
        </div>
        @enderror
    </div>
</div>
