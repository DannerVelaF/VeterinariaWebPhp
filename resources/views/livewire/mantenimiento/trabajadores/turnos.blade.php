<x-panel title="Gestión de puestos" :breadcrumbs="[
    ['label' => 'Inicio', 'href' => '/', 'icon' => 'home'],
    ['label' => 'Trabajadores', 'href' => route('mantenimiento.trabajadores'), 'icon' => 'ellipsis-horizontal'],
    ['label' => 'Gestión de turnos', 'href' => route('mantenimiento.trabajadores.turnos')],
]">

    <x-tabs :tabs="['turnos' => '📋 Turnos', 'asignar' => '➕ Asignar horario']" default="turnos">

        <x-tab name="turnos">
            {{-- Botón de nuevo turno --}}
            <flux:modal.trigger name="nuevo-turno">
                <flux:button wire:click="abrirModal">Nuevo turno</flux:button>
            </flux:modal.trigger>

            <flux:modal name="nuevo-turno" class="md:w-1/2" wire:model="modalVisible">
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
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Asignar turnos a trabajadores</flux:heading>
                    <flux:text class="mt-1">Selecciona un turno para asignar o quitar trabajadores.</flux:text>
                </div>

                {{-- Selector de turno + tabla de horario en 2 filas --}}
                <div class="w-full">
                    <div class="flex flex-col lg:flex-row items-start lg:items-center gap-4">
                        {{-- Selector --}}
                        <div class="">
                            <flux:label class="mb-2">Seleccionar turno</flux:label>
                            <select wire:model.live="turnoSeleccionadoAsignar"
                                class="w-[300px] border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <option value="">Seleccione un turno...</option>
                                @foreach ($turnos as $t)
                                    <option value="{{ $t->id_turno }}">{{ $t->nombre_turno }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Tabla resumen de horario (2 filas) --}}
                        @if ($turnoSeleccionadoAsignar && count($horarioTurnoAsignar) > 0)
                            <div class="flex-1 overflow-x-auto border border-gray-200 rounded-lg shadow-sm bg-white">
                                <table class="min-w-full text-center text-sm">
                                    <thead class="bg-gray-100 text-gray-700">
                                        <tr>
                                            @foreach (['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'] as $dia)
                                                <th class="px-3 py-2 font-semibold">{{ $dia }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            @foreach (['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'] as $dia)
                                                @php
                                                    $h = $horarioTurnoAsignar[$dia] ?? null;
                                                @endphp
                                                <td
                                                    class="px-3 py-2 text-xs 
            {{ $h && $h->es_descanso ? 'bg-red-100 text-red-700 font-semibold' : 'text-gray-700' }}">
                                                    @if ($h)
                                                        @if ($h->es_descanso)
                                                            Descanso
                                                        @else
                                                            {{ \Carbon\Carbon::parse($h->hora_inicio)->format('H:i') }}
                                                            -
                                                            {{ \Carbon\Carbon::parse($h->hora_fin)->format('H:i') }}
                                                        @endif
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @elseif($turnoSeleccionadoAsignar)
                            <div
                                class="flex-1 border border-gray-200 rounded-lg shadow-sm bg-white flex items-center justify-center h-20">
                                <p class="text-gray-500 text-sm">Este turno no tiene horarios registrados</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Panel de asignación --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Gestión de trabajadores</h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    Asigna o quita trabajadores del turno seleccionado
                                </p>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if ($turnoSeleccionadoAsignar)
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                        {{ count($trabajadoresConTurno) }} asignados
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            {{-- Trabajadores sin turno --}}
                            <div class="border border-gray-200 rounded-lg overflow-hidden bg-white">
                                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                                    <h4 class="font-medium text-gray-800 flex items-center">
                                        <x-icon name="user-group" class="h-5 w-5 text-gray-500 mr-2" />
                                        Sin turno asignado
                                        <span class="ml-2 bg-gray-200 text-gray-700 rounded-full px-2 py-0.5 text-xs">
                                            {{ count($trabajadoresSinTurno) }}
                                        </span>
                                    </h4>
                                </div>
                                <div class="max-h-80 overflow-y-auto">
                                    @if (count($trabajadoresSinTurno) > 0)
                                        <table class="w-full text-sm">
                                            <tbody>
                                                @foreach ($trabajadoresSinTurno as $t)
                                                    <tr
                                                        class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                                        <td class="px-4 py-3">
                                                            <label class="flex items-center space-x-3 cursor-pointer">
                                                                <input type="checkbox"
                                                                    wire:model.live="selectedSinTurno"
                                                                    value="{{ $t->id_trabajador }}"
                                                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                                                <div class="flex-1 min-w-0">
                                                                    <p class="font-medium text-gray-900 truncate">
                                                                        {{ $t->persona?->nombre }}
                                                                        {{ $t->persona?->apellido_paterno }}
                                                                    </p>
                                                                    <p class="text-xs text-gray-500 truncate">
                                                                        {{ $t->persona?->numero_documento ?? 'Sin documento' }}
                                                                    </p>
                                                                </div>
                                                            </label>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="text-center py-8 px-4">
                                            <x-icon name="user-group" class="h-10 w-10 text-gray-300 mx-auto mb-2" />
                                            <p class="text-gray-500 text-sm">No hay trabajadores sin turno</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Controles de acción --}}
                            <div
                                class="flex lg:flex-col items-center justify-center space-y-4 lg:space-y-4 space-x-4 lg:space-x-0">
                                <flux:button wire:click="asignarTrabajadores" icon="chevron-right"
                                    class="w-full justify-center" :disabled="count($selectedSinTurno) === 0">
                                    Asignar
                                </flux:button>

                                <flux:button wire:click="quitarTrabajadores" icon="chevron-left" variant="outline"
                                    class="w-full justify-center" :disabled="count($selectedConTurno) === 0">
                                    Quitar
                                </flux:button>

                                <div class="hidden lg:block border-t border-gray-200 my-2 w-full"></div>

                                <flux:button wire:click="$set('selectedSinTurno', [])" size="sm" variant="ghost"
                                    class="w-full justify-center text-xs" :disabled="count($selectedSinTurno) === 0">
                                    Limpiar selección
                                </flux:button>
                            </div>

                            {{-- Trabajadores con turno --}}
                            <div class="border border-gray-200 rounded-lg overflow-hidden bg-white">
                                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                                    <h4 class="font-medium text-gray-800 flex items-center">
                                        <x-icon name="check-circle" class="h-5 w-5 text-green-500 mr-2" />
                                        Con turno asignado
                                        <span
                                            class="ml-2 bg-green-100 text-green-800 rounded-full px-2 py-0.5 text-xs">
                                            {{ count($trabajadoresConTurno) }}
                                        </span>
                                    </h4>
                                </div>
                                <div class="max-h-80 overflow-y-auto">
                                    @if (count($trabajadoresConTurno) > 0)
                                        <table class="w-full text-sm">
                                            <tbody>
                                                @foreach ($trabajadoresConTurno as $t)
                                                    <tr
                                                        class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                                        <td class="px-4 py-3">
                                                            <label class="flex items-center space-x-3 cursor-pointer">
                                                                <input type="checkbox"
                                                                    wire:model.live="selectedConTurno"
                                                                    value="{{ $t->id_trabajador }}"
                                                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                                                <div class="flex-1 min-w-0">
                                                                    <p class="font-medium text-gray-900 truncate">
                                                                        {{ $t->persona?->nombre }}
                                                                        {{ $t->persona?->apellido_paterno }}
                                                                    </p>
                                                                    <p class="text-xs text-gray-500 truncate">
                                                                        {{ $t->persona?->numero_documento ?? 'Sin documento' }}
                                                                    </p>
                                                                </div>
                                                            </label>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="text-center py-8 px-4">
                                            <x-icon name="user-group" class="h-10 w-10 text-gray-300 mx-auto mb-2" />
                                            <p class="text-gray-500 text-sm">
                                                @if ($turnoSeleccionadoAsignar)
                                                    No hay trabajadores asignados a este turno
                                                @else
                                                    Selecciona un turno para ver trabajadores asignados
                                                @endif
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Resumen --}}
                        <div class="mt-6 pt-4 border-t border-gray-200">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div class="text-sm text-gray-600">
                                    @if (count($selectedSinTurno) > 0 || count($selectedConTurno) > 0)
                                        <span class="font-medium">
                                            {{ count($selectedSinTurno) + count($selectedConTurno) }}
                                            trabajadores seleccionados
                                        </span>
                                        <span class="mx-2">•</span>
                                        <span>{{ count($selectedSinTurno) }} para asignar</span>
                                        <span class="mx-2">•</span>
                                        <span>{{ count($selectedConTurno) }} para quitar</span>
                                    @else
                                        <span>Selecciona trabajadores para asignar o quitar del turno</span>
                                    @endif
                                </div>

                                <div class="flex space-x-2">
                                    @if (count($selectedSinTurno) > 0)
                                        <flux:button wire:click="asignarTrabajadores" size="sm"
                                            icon="chevron-right">
                                            Asignar {{ count($selectedSinTurno) }} trabajador(es)
                                        </flux:button>
                                    @endif

                                    @if (count($selectedConTurno) > 0)
                                        <flux:button wire:click="quitarTrabajadores" size="sm" variant="outline"
                                            icon="chevron-left">
                                            Quitar {{ count($selectedConTurno) }} trabajador(es)
                                        </flux:button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
    <x-loader />
</x-panel>
