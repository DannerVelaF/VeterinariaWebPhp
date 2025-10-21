<x-panel title="Gestión de puestos" :breadcrumbs="[
    ['label' => 'Inicio', 'href' => '/', 'icon' => 'home'],
    ['label' => 'Trabajadores', 'href' => route('mantenimiento.trabajadores'), 'icon' => 'ellipsis-horizontal'],
    ['label' => 'Gestión de turnos', 'href' => route('mantenimiento.trabajadores.turnos')],
]">

    <x-tabs :tabs="['turnos' => '📋 Turnos', 'asignar' => '➕ Asignar horario']" default="turnos">

        <x-tab name="turnos">
            {{-- Botón de nuevo turno --}}
            <flux:modal.trigger name="nuevo-turno">
                <flux:button>Nuevo turno</flux:button>
            </flux:modal.trigger>

            <flux:modal name="nuevo-turno" class="md:w-1/2">
                <form wire:submit.prevent="guardarTurno">
                    <div class="space-y-6">
                        <div>
                            <flux:heading size="lg">Crear nuevo turno</flux:heading>
                            <flux:text class="mt-2">Completa los campos requeridos para crear un nuevo turno.
                            </flux:text>
                        </div>

                        <flux:input label="Nombre del turno" placeholder="Turno" wire:model.defer="nombre_turno" />
                        <flux:textarea resize="none" label="Descripción" placeholder="Descripción del turno"
                            wire:model.defer="descripcion" />

                        {{-- Tabla de horarios --}}
                        <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
                            <thead class="bg-gray-100 text-gray-700">
                                <tr>
                                    <th class="px-3 py-2 text-left">Día</th>
                                    <th class="px-3 py-2 text-center">Inicio</th>
                                    <th class="px-3 py-2 text-center">Fin</th>
                                    <th class="px-3 py-2 text-center">Descanso</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (array_keys($horarios) as $dia)
                                    <tr wire:key="crear-{{ $dia }}" class="border-b hover:bg-gray-50">
                                        <td class="px-3 py-2">{{ $dia }}</td>
                                        <td class="px-3 py-2 text-center">
                                            <input type="time" wire:model.defer="horarios.{{ $dia }}.inicio"
                                                class="border rounded-md px-2 py-1 w-28 text-center">
                                        </td>
                                        <td class="px-3 py-2 text-center">
                                            <input type="time" wire:model.defer="horarios.{{ $dia }}.fin"
                                                class="border rounded-md px-2 py-1 w-28 text-center">
                                        </td>
                                        <td class="px-3 py-2 text-center">
                                            <input type="checkbox"
                                                wire:model.defer="horarios.{{ $dia }}.descanso"
                                                class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="flex justify-end mt-4">
                            <flux:button type="submit" variant="primary">Guardar</flux:button>
                        </div>
                    </div>
                </form>
            </flux:modal>

            <div class="mt-6 overflow-x-auto">
                <table class="w-full text-sm border border-gray-200 rounded-lg">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th></th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Fecha registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($turnos as $turno)
                            <tr class="hover:bg-gray-50 cursor-pointer"
                                wire:click="rowSelected({{ $turno->id_turno }})">
                                <td class="text-center">
                                    @if ($turnoSeleccionado && $turnoSeleccionado->id_turno == $turno->id_turno)
                                        <x-icon name="chevron-down" />
                                    @else
                                        <x-icon name="chevron-right" />
                                    @endif
                                </td>
                                <td>{{ $turno->nombre_turno }}</td>
                                <td>{{ $turno->descripcion }}</td>
                                <td>{{ ucfirst($turno->estado) }}</td>
                                <td>{{ \Carbon\Carbon::parse($turno->fecha_registro)->format('d/m/Y H:i') }}</td>
                                <td class="space-x-2 text-center">
                                    {{-- Abrir modal de edición con Livewire --}}

                                    <flux:button size="sm" variant="danger"
                                        wire:click.stop="deleteTurno({{ $turno->id_turno }})">
                                        Anular
                                    </flux:button>
                                </td>
                            </tr>

                            {{-- Fila expandida --}}
                            @if ($turnoSeleccionado && $turnoSeleccionado->id_turno == $turno->id_turno)
                                <tr class="bg-gray-50">
                                    <td></td>
                                    <td colspan="5" class="p-4">
                                        <div class="border rounded-lg bg-white shadow-sm p-3">
                                            <h4 class="font-semibold mb-2">Horarios del turno</h4>
                                            <table class="w-full text-xs border border-gray-200 rounded-md">
                                                <thead class="bg-gray-100 text-gray-700">
                                                    <tr>
                                                        <th class="px-2 py-1 text-left">Día</th>
                                                        <th class="px-2 py-1 text-center">Inicio</th>
                                                        <th class="px-2 py-1 text-center">Fin</th>
                                                        <th class="px-2 py-1 text-center">Descanso</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($horarioTurno as $h)
                                                        <tr class="border-b">
                                                            <td class="px-2 py-1">{{ $h->dia_semana }}</td>
                                                            <td class="px-2 py-1 text-center">
                                                                {{ $h->hora_inicio ?? '-' }}
                                                            </td>
                                                            <td class="px-2 py-1 text-center">{{ $h->hora_fin ?? '-' }}
                                                            </td>
                                                            <td class="px-2 py-1 text-center">
                                                                {{ $h->es_descanso ? 'Sí' : 'No' }}
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="4" class="text-center py-2 text-gray-500">
                                                                No hay horarios registrados.
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-tab>
        <x-tab name="asignar">

        </x-tab>
    </x-tabs>

    @push('scripts')
        <script>
            Livewire.on('notify', (data) => {
                Swal.fire({
                    title: data.title,
                    text: data.description,
                    icon: data.type,
                    timer: 2500,
                    showConfirmButton: false
                });
            });
        </script>
    @endpush

</x-panel>
