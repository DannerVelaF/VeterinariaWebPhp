<x-panel title="Gestión de productos" :breadcrumbs="[
    ['label' => 'Inicio', 'href' => '/', 'icon' => 'home'],
    ['label' => 'Mantenimiento', 'href' => '#'],
    ['label' => 'Mëtodos de pago'],
]">
    <x-tabs
        :tabs="['listado' => '📋 Detalle métodos de pago registrados', 'registro' => '➕ Registrar nuevo método de pago']"
        default="listado">
        <!-- TAB 1: LISTADO -->
        <x-tab name="listado">
            <div class="p-4">
                <livewire:metodos-pago-table/>
            </div>
        </x-tab>

        <!-- TAB 2: REGISTRO -->
        <x-tab name="registro">
            <div class="p-3 bg-gray-50 rounded">
                <form wire:submit.prevent="guardar">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <!-- Nombre del Método -->
                        <div class="col-span-2">
                            <x-flux::input
                                label="Nombre del Método de Pago *"
                                wire:model="MetodoPagoNew.nombre_metodo"
                                placeholder="Ej: Transferencia Bancaria, Yape, Plin, etc."
                                required/>
                        </div>

                        <!-- Tipo de Método -->
                        <div>
                            <x-flux::select
                                label="Tipo de Método *"
                                wire:model="MetodoPagoNew.tipo_metodo"
                                required>
                                <option value="">Seleccionar tipo</option>
                                <option value="transferencia">Transferencia</option>
                                <option value="digital">Digital</option>
                                <option value="efectivo">Efectivo</option>
                            </x-flux::select>
                        </div>

                        <!-- Número de Cuenta -->
                        <div>
                            <x-flux::input
                                label="Número de Cuenta / Teléfono"
                                wire:model="MetodoPagoNew.numero_cuenta"
                                placeholder="Ej: 123456789012"/>
                        </div>

                        <!-- Nombre del Titular -->
                        <div>
                            <x-flux::input
                                label="Nombre del Titular"
                                wire:model="MetodoPagoNew.nombre_titular"
                                placeholder="Nombre completo del titular"/>
                        </div>

                        <!-- Entidad Financiera -->
                        <div>
                            <x-flux::input
                                label="Entidad Financiera / App"
                                wire:model="MetodoPagoNew.entidad_financiera"
                                placeholder="Ej: BCP, Interbank, Yape, Plin"/>
                        </div>

                        <!-- Tipo de Cuenta -->
                        <div>
                            <x-flux::select
                                label="Tipo de Cuenta"
                                wire:model="MetodoPagoNew.tipo_cuenta">
                                <option value="">Seleccionar tipo</option>
                                <option value="ahorros">Ahorros</option>
                                <option value="corriente">Corriente</option>
                                <option value="billetera">Billetera Digital</option>
                                <option value="otro">Otro</option>
                            </x-flux::select>
                        </div>

                        <!-- Orden -->
                        <div>
                            <x-flux::input
                                type="number"
                                label="Orden de Visualización"
                                wire:model="MetodoPagoNew.orden"
                                placeholder="Ej: 1, 2, 3..."
                                min="1"/>
                        </div>

                        <!-- Código QR - Upload de archivo -->
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Código QR (Imagen)
                            </label>
                            <div class="flex items-center space-x-4">
                                <div class="flex-1">
                                    <input type="file"
                                           wire:model="codigo_qr_file"
                                           accept="image/*"
                                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    @error('codigo_qr_file')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                    <p class="text-xs text-gray-500 mt-1">Formatos: JPG, PNG, GIF. Tamaño máximo:
                                        2MB</p>
                                </div>

                                <!-- Vista previa -->
                                @if ($codigo_qr_file)
                                    <div class="flex-shrink-0">
                                        <p class="text-xs text-gray-600 mb-1">Vista previa:</p>
                                        <img src="{{ $codigo_qr_file->temporaryUrl() }}"
                                             class="h-20 w-20 object-cover rounded border">
                                    </div>
                                @endif
                            </div>

                            <!-- Mostrar imagen actual si existe -->
                            @if ($MetodoPagoNew->codigo_qr)
                                <div class="mt-3">
                                    <p class="text-sm text-gray-600 mb-1">Imagen actual:</p>
                                    <img src="{{ asset('storage/' . $MetodoPagoNew->codigo_qr) }}"
                                         class="h-20 w-20 object-cover rounded border">
                                </div>
                            @endif
                        </div>

                        <!-- Instrucciones -->
                        <div class="col-span-2">
                            <x-flux::textarea
                                label="Instrucciones de Pago"
                                wire:model="MetodoPagoNew.instrucciones"
                                rows="3"
                                placeholder="Instrucciones específicas para realizar el pago..."/>
                        </div>

                        <!-- Observación -->
                        <div class="col-span-2">
                            <x-flux::textarea
                                label="Observación"
                                wire:model="MetodoPagoNew.observacion"
                                rows="2"
                                placeholder="Notas adicionales..."/>
                        </div>

                    </div>

                    <!-- Botones de acción -->
                    <div class="mt-6 flex justify-end space-x-3">
                        <x-flux::button
                            type="button"
                            wire:click="resetForm"
                        >
                            Limpiar
                        </x-flux::button>

                        <x-flux::button
                            type="submit"
                        >
                            Guardar Método de Pago
                        </x-flux::button>
                    </div>
                </form>
            </div>
        </x-tab>
    </x-tabs>

    <!-- Modal de edición de Método de Pago con FluxUI -->
    <x-flux::modal wire:model="showModalEdit" class="!max-w-6xl w-full">
        <x-flux::heading>
            <h2 class="text-lg font-bold">Editar Método de Pago</h2>
        </x-flux::heading>

        <div class="px-6 py-6 bg-gray-50 max-h-[70vh] overflow-y-auto">
            <form wire:submit.prevent="actualizar">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Nombre del Método -->
                    <div class="col-span-2">
                        <x-flux::input
                            label="Nombre del Método de Pago *"
                            wire:model="edit_nombre_metodo"
                            placeholder="Ej: Transferencia Bancaria, Yape, Plin, etc."
                            required/>
                    </div>

                    <!-- Tipo de Método y Estado -->
                    <div class="space-y-4">
                        <x-flux::select
                            label="Tipo de Método *"
                            wire:model="edit_tipo_metodo"
                            required>
                            <option value="">Seleccionar tipo</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="digital">Digital</option>
                            <option value="efectivo">Efectivo</option>
                        </x-flux::select>

                        <x-flux::select
                            label="Estado *"
                            wire:model="edit_estado"
                            required>
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </x-flux::select>
                    </div>

                    <!-- Información de Cuenta -->
                    <div class="space-y-4">
                        <x-flux::input
                            label="Número de Cuenta / Teléfono"
                            wire:model="edit_numero_cuenta"
                            placeholder="Ej: 123456789012"/>

                        <x-flux::input
                            label="Nombre del Titular"
                            wire:model="edit_nombre_titular"
                            placeholder="Nombre completo del titular"/>
                    </div>

                    <!-- Entidad y Tipo de Cuenta -->
                    <div class="space-y-4">
                        <x-flux::input
                            label="Entidad Financiera / App"
                            wire:model="edit_entidad_financiera"
                            placeholder="Ej: BCP, Interbank, Yape, Plin"/>

                        <x-flux::select
                            label="Tipo de Cuenta"
                            wire:model="edit_tipo_cuenta">
                            <option value="">Seleccionar tipo</option>
                            <option value="ahorros">💰 Ahorros</option>
                            <option value="corriente">🏦 Corriente</option>
                            <option value="billetera">📱 Billetera Digital</option>
                            <option value="otro">🔧 Otro</option>
                        </x-flux::select>
                    </div>

                    <!-- Orden -->
                    <div>
                        <x-flux::input
                            type="number"
                            label="Orden de Visualización"
                            wire:model="edit_orden"
                            placeholder="Ej: 1, 2, 3..."
                            min="1"/>
                    </div>

                    <!-- Código QR - Upload de archivo -->
                    <div class="col-span-2 bg-white p-4 rounded-lg border border-gray-200">
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            📷 Código QR (Imagen)
                        </label>

                        <div class="flex flex-col md:flex-row gap-6 items-start">
                            <!-- Upload Section -->
                            <div class="flex-1">
                                <div class="flex items-center space-x-4 mb-4">
                                    <label
                                        class="bg-blue-600 text-white px-4 py-2 rounded-lg cursor-pointer hover:bg-blue-700 transition-colors text-sm font-semibold flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20"
                                             fill="currentColor">
                                            <path fill-rule="evenodd"
                                                  d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z"
                                                  clip-rule="evenodd"/>
                                        </svg>
                                        Seleccionar Nueva Imagen
                                        <input type="file" class="hidden" wire:model="codigo_qr_file_edit"
                                               accept="image/*">
                                    </label>
                                </div>

                                @error('codigo_qr_file_edit')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                                <p class="text-xs text-gray-500">Formatos: JPG, PNG, GIF. Tamaño máximo: 2MB</p>
                            </div>

                            <!-- Previews -->
                            <div class="flex gap-4">
                                <!-- Vista previa nueva imagen -->
                                @if ($codigo_qr_file_edit)
                                    <div class="text-center">
                                        <p class="text-xs text-gray-600 mb-1">Nueva imagen:</p>
                                        <img src="{{ $codigo_qr_file_edit->temporaryUrl() }}"
                                             class="h-24 w-24 object-cover rounded-lg border-2 border-blue-400 shadow-sm">
                                    </div>
                                @endif

                                <!-- Imagen actual -->
                                @if ($edit_codigo_qr)
                                    <div class="text-center">
                                        <p class="text-xs text-gray-600 mb-1">Imagen actual:</p>
                                        <img src="{{ asset('storage/' . $edit_codigo_qr) }}"
                                             class="h-24 w-24 object-cover rounded-lg border-2 border-gray-300 shadow-sm">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Instrucciones -->
                    <div class="col-span-2">
                        <x-flux::textarea
                            label="📝 Instrucciones de Pago"
                            wire:model="edit_instrucciones"
                            rows="4"
                            placeholder="Instrucciones específicas para realizar el pago..."/>
                    </div>

                    <!-- Observación -->
                    <div class="col-span-2">
                        <x-flux::textarea
                            label="💡 Observación"
                            wire:model="edit_observacion"
                            rows="3"
                            placeholder="Notas adicionales..."/>
                    </div>

                </div>
            </form>
        </div>
        <x-flux::button wire:click="cerrarModalEdit">Cancelar</x-flux::button>
        <x-flux::button wire:click="actualizar">Actualizar</x-flux::button>
    </x-flux::modal>


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
</x-panel>
