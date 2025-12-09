<?php

namespace App\Livewire;

use App\Models\Cita;
use App\Models\Clientes;
use App\Models\EstadoCita;
use App\Models\Trabajador;
use App\Models\Mascota;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Facades\Rule;

final class CitaTable extends PowerGridComponent
{
    public string $tableName = 'cita-table';
    public string $primaryKey = 'id_cita';
    public string $sortField = 'id_cita';
    
    // CAMBIO: Corregir el nombre del evento
    public $listeners = ['citasUpdated' => '$refresh'];

    public function boot(): void
    {
        config(['livewire-powergrid.filter' => 'outside']);
    }

    public function setUp(): array
    {
        return [
            PowerGrid::header(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount()
        ];
    }

    public function datasource(): Builder
    {
        return Cita::query()->with([
            'cliente.persona', 
            'trabajadorAsignado.persona', 
            'mascota', 
            'estadoCita'
        ]);
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id_cita')
            ->add('fecha_programada_formatted', fn($cita) =>
                Carbon::parse($cita->fecha_programada)->format('d/m/Y H:i')
            )
            ->add('fecha_registro_formatted', fn($cita) =>
                Carbon::parse($cita->fecha_registro)->format('d/m/Y H:i')
            )
            ->add('estado_badge', fn($cita) =>
                $this->getEstadoBadge($cita->estadoCita->nombre_estado_cita)
            )
            ->add('cliente_nombre', fn($cita) =>
                $cita->cliente && $cita->cliente->persona 
                    ? $cita->cliente->persona->nombre . ' ' . 
                      $cita->cliente->persona->apellido_paterno . ' ' . 
                      $cita->cliente->persona->apellido_materno 
                    : 'N/A'
            )
            ->add('trabajador_nombre', fn($cita) =>
                $cita->trabajadorAsignado && $cita->trabajadorAsignado->persona 
                    ? $cita->trabajadorAsignado->persona->nombre . ' ' . 
                      $cita->trabajadorAsignado->persona->apellido_paterno 
                    : '-'
            )
            ->add('mascota_nombre', fn($cita) =>
                $cita->mascota ? $cita->mascota->nombre_mascota : '-'
            )
            ->add('motivo_short', fn($cita) =>
                strlen($cita->motivo) > 50 
                    ? substr($cita->motivo, 0, 50) . '...' 
                    : $cita->motivo
            );
    }

    private function getEstadoBadge($estado): string
    {
        $badgeClasses = [
            'Pendiente' => 'bg-yellow-100 text-yellow-800',
            'En progreso' => 'bg-blue-100 text-blue-800',
            'Completada' => 'bg-green-100 text-green-800',
            'Cancelada' => 'bg-red-100 text-red-800',
            'No asistio' => 'bg-gray-100 text-gray-800',
        ];

        $class = $badgeClasses[$estado] ?? 'bg-gray-100 text-gray-800';

        return '<span class="px-2 py-1 rounded-full text-xs font-medium capitalize ' . $class . '">' . $estado . '</span>';
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id_cita')
                ->sortable()
                ->searchable(),

            Column::make('Fecha Programada', 'fecha_programada_formatted')
                ->sortable(),

            Column::make('Fecha Registro', 'fecha_registro_formatted')
                ->sortable(),

            Column::make('Estado', 'estado_badge')
                ->sortable(),

            Column::make('Cliente', 'cliente_nombre')
                ->sortable()
                ->searchable(),

            Column::make('Trabajador', 'trabajador_nombre')
                ->sortable()
                ->searchable(),

            Column::make('Mascota', 'mascota_nombre')
                ->sortable()
                ->searchable(),

            Column::make('Motivo', 'motivo_short')
                ->sortable()
                ->searchable(),

            Column::action('Acciones')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::select('estado', 'id_estado_cita')
                ->dataSource(EstadoCita::all()->toArray())
                ->optionValue('id_estado_cita')
                ->optionLabel('nombre_estado_cita')
                ->builder(function (Builder $query, $value) {
                    $v = is_array($value)
                        ? ($value['value'] ?? $value['search'] ?? array_values($value)[0] ?? '')
                        : (string) $value;

                    $v = trim($v);
                    if ($v === '') {
                        return $query;
                    }

                    return $query->where('id_estado_cita', $v);
                }),

            Filter::datePicker('fecha_programada', 'fecha_programada')
                ->params([
                    'dateFormat' => 'Y-m-d H:i:s',
                    'locale' => 'es',
                    'enableTime' => true,
                ]),

            Filter::select('cliente', 'id_cliente')
                ->dataSource(Clientes::all()->values()->toArray())
                ->optionValue('id_cliente')
                ->optionLabel('id_cliente')
                ->builder(function (Builder $query, $value) {
                    $v = is_array($value)
                        ? ($value['value'] ?? $value['search'] ?? array_values($value)[0] ?? '')
                        : (string) $value;

                    $v = trim($v);
                    if ($v === '') {
                        return $query;
                    }

                    return $query->where('id_cliente', $v);
                }),

            Filter::select('trabajador', 'id_trabajador_asignado')
                ->dataSource(Trabajador::all()->values()->toArray())
                ->optionValue('id_trabajador')
                ->optionLabel('id_trabajador')
                ->builder(function (Builder $query, $value) {
                    $v = is_array($value)
                        ? ($value['value'] ?? $value['search'] ?? array_values($value)[0] ?? '')
                        : (string) $value;

                    $v = trim($v);
                    if ($v === '') {
                        return $query;
                    }

                    return $query->where('id_trabajador_asignado', $v);
                }),

            Filter::select('mascota', 'id_mascota')
                ->dataSource(Mascota::all()->values()->toArray())
                ->optionValue('id_mascota')
                ->optionLabel('nombre_mascota')
                ->builder(function (Builder $query, $value) {
                    $v = is_array($value)
                        ? ($value['value'] ?? $value['search'] ?? array_values($value)[0] ?? '')
                        : (string) $value;

                    $v = trim($v);
                    if ($v === '') {
                        return $query;
                    }

                    return $query->where('id_mascota', $v);
                }),
        ];
    }

    // NUEVO: Método para manejar la edición de citas
    public function editarCitaHandler($citaId): void
    {
        $this->dispatch('editar-cita-event', $citaId);
    }

    #[\Livewire\Attributes\On('cambiar-estado-cita')]
    public function cambiarEstadoCitaHandler($params): void
    {
        $this->dispatch('cambiar-estado-cita-event', $params);
    }


    public function actions(Cita $row): array
    {
        $actions = [
            // CORRECCIÓN: Usar 'citaId' en lugar de 'rowId'
            Button::add('editar')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="blue" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>')
                ->id()
                ->class('pg-btn-white dark:bg-pg-primary-700 hover:bg-blue-50')
                ->dispatch('editar-cita-event', ['citaId' => $row->id_cita]) 
                ->tooltip('Editar cita'),
        ];

        // Estado: Pendiente
        if ($row->estadoCita->nombre_estado_cita === 'Pendiente') {
            $actions[] = Button::add('en-progreso')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="blue" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="6 3 20 12 6 21 6 3"/></svg>')
                ->id()
                ->class('pg-btn-white dark:bg-pg-primary-700 hover:bg-blue-50')
                ->dispatch('cambiar-estado-cita-event', ['citaId' => $row->id_cita, 'nuevoEstado' => 'En progreso'])
                ->tooltip('Marcar como En progreso');

            $actions[] = Button::add('cancelar')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="red" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/></svg>')
                ->id()
                ->class('pg-btn-white dark:bg-pg-primary-700 hover:bg-red-50')
                ->dispatch('cambiar-estado-cita-event', ['citaId' => $row->id_cita, 'nuevoEstado' => 'Cancelada'])
                ->tooltip('Cancelar cita');

            $actions[] = Button::add('no-asistio')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="orange" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="m17 8 5 5"/><path d="m22 8-5 5"/></svg>')
                ->id()
                ->class('pg-btn-white dark:bg-pg-primary-700 hover:bg-orange-50')
                ->dispatch('cambiar-estado-cita-event', ['citaId' => $row->id_cita, 'nuevoEstado' => 'No asistio'])
                ->tooltip('Marcar como No asistió');
        }

        // Estado: En progreso
        if ($row->estadoCita->nombre_estado_cita === 'En progreso') {
            $actions[] = Button::add('cancelar')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="red" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/></svg>')
                ->id()
                ->class('pg-btn-white dark:bg-pg-primary-700 hover:bg-red-50')
                ->dispatch('cambiar-estado-cita-event', ['citaId' => $row->id_cita, 'nuevoEstado' => 'Cancelada'])
                ->tooltip('Cancelar cita');

            $actions[] = Button::add('no-asistio')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="orange" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="m17 8 5 5"/><path d="m22 8-5 5"/></svg>')
                ->id()
                ->class('pg-btn-white dark:bg-pg-primary-700 hover:bg-orange-50')
                ->dispatch('cambiar-estado-cita-event', ['citaId' => $row->id_cita, 'nuevoEstado' => 'No asistio'])
                ->tooltip('Marcar como No asistió');
        }

        return $actions;
    }
    
    public function actionRules($row): array
    {
        $rules = [];

        // Ocultar botón editar si la cita está completada, cancelada o no asistió
        $estadosFinales = ['Completada', 'Cancelada', 'No asistio'];
        if (in_array($row->estadoCita->nombre_estado_cita, $estadosFinales)) {
            $rules[] = Rule::button('editar')
                ->when(fn($row) => true)
                ->hide();
        }

        return $rules;
    }
}