<?php

namespace App\Livewire;

use App\Models\Ventas;
use App\Models\Clientes;
use App\Models\EstadoVentas;
Use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class VentasTable extends PowerGridComponent
{
    public string $tableName = 'ventas-table';
    use WithExport;
    public bool $showFiltersButton = false;
    public string $primaryKey = 'id_venta';
    public string $sortField = 'id_venta';
    public $listeners = ['ventasUpdated' => '$refresh'];

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
        return Ventas::query()->with(['cliente', 'trabajador.persona.user', 'estadoVenta']);
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id_venta')
            ->add('fecha_venta_formatted', fn($venta) =>
                Carbon::parse($venta->fecha_venta)->format('d/m/Y')
            )
            ->add('fecha_registro_formatted', fn($venta) =>
                Carbon::parse($venta->fecha_registro)->format('d/m/Y H:i')
            )
            ->add('estado_badge', fn($venta) =>
                $this->getEstadoBadge($venta->estadoVenta->nombre_estado_venta_fisica)
            )
            ->add('subtotal_formatted', fn($venta) =>
                'S/ ' . number_format($venta->subtotal, 2)
            )
            ->add('descuento_formatted', fn($venta) =>
                'S/ ' . number_format($venta->descuento, 2)
            )
            ->add('total_formatted', fn($venta) =>
                'S/ ' . number_format($venta->total, 2)
            )
            ->add('cliente_nombre', fn($venta) =>
                $venta->cliente ? $venta->cliente->persona->nombre : 'N/A'
            )
            ->add('vendedor', fn($venta) =>
                $venta->trabajador && $venta->trabajador->persona && $venta->trabajador->persona->user
                    ? $venta->trabajador->persona->user->usuario
                    : '-'
            );
    }

    private function getEstadoBadge($estado): string
    {
        $badgeClasses = [
            'pendiente' => 'bg-yellow-100 text-yellow-800',
            'completado' => 'bg-green-100 text-green-800',
            'cancelado' => 'bg-red-100 text-red-800',
        ];

        $class = $badgeClasses[$estado] ?? 'bg-gray-100 text-gray-800';

        return '<span class="px-2 py-1 rounded-full text-xs font-medium capitalize ' . $class . '">' . $estado . '</span>';
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id_venta')
                ->sortable()
                ->searchable(),

            Column::make('Fecha Venta', 'fecha_venta_formatted')
                ->sortable(),

            Column::make('Fecha Registro', 'fecha_registro_formatted')
                ->sortable(),

            Column::make('Estado', 'estado_badge')
                ->sortable(),

            Column::make('Subtotal', 'subtotal_formatted')
                ->sortable(),

            Column::make('Descuento', 'descuento_formatted')
                ->sortable(),

            Column::make('Total', 'total_formatted')
                ->sortable(),

            Column::make('Cliente', 'cliente_nombre')
                ->sortable()
                ->searchable(),

            Column::make('Vendedor', 'vendedor')
                ->sortable(),

            Column::action('Acciones')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::select('estado', 'id_estado_venta')
                ->dataSource(EstadoVentas::all()->toArray())
                ->optionValue('id_estado_venta')
                ->optionLabel('nombre_estado_venta')
                ->builder(function (Builder $query, $value) {
                    $v = is_array($value)
                        ? ($value['value'] ?? $value['search'] ?? array_values($value)[0] ?? '')
                        : (string) $value;

                    $v = trim($v);
                    if ($v === '') {
                        return $query;
                    }

                    return $query->where('id_estado_venta', $v);
                }),

            Filter::datePicker('fecha_venta', 'fecha_venta')
                ->params([
                    'dateFormat' => 'Y-m-d',
                    'locale' => 'es',
                    'enableTime' => false,
                ]),

            Filter::select('cliente', 'id_cliente')
                ->dataSource(Clientes::all()->sortBy('nombre_cliente')->values()->toArray())
                ->optionValue('id_cliente')
                ->optionLabel('nombre_cliente')
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
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    public function actions(Ventas $row): array
    {
        return [
            Button::add('ver')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye-icon lucide-eye"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg>')
                ->id()
                ->class('pg-btn-white dark:bg-pg-primary-700')
                ->dispatch('show-modal-venta', ['rowId' => $row->id_venta]),

            Button::add('completar')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="green" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check-icon lucide-check"><path d="M20 6 9 17l-5-5"/></svg>')
                ->id()
                ->class('pg-btn-white dark:bg-pg-primary-700')
                ->dispatch('completar-venta', ['rowId' => $row->id_venta]),

            Button::add('cancelar')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="red" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-x-icon lucide-circle-x"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/></svg>')
                ->id()
                ->class('pg-btn-white dark:bg-pg-primary-700')
                ->dispatch('cancelar-venta', ['rowId' => $row->id_venta]),
        ];
    }

    public function actionRules($row): array
    {
        return [
            // Ocultar botón completar si la venta ya está completada o cancelada
            Rule::button('completar')
                ->when(fn($row) => in_array($row->estadoVenta->nombre_estado_venta_fisica, ['completado', 'cancelado']))
                ->hide(),

            // Ocultar botón cancelar si la venta ya está cancelada o completada
            Rule::button('cancelar')
                ->when(fn($row) => in_array($row->estadoVenta->nombre_estado_venta_fisica, ['completado', 'cancelado']))
                ->hide(),

            // Mostrar solo el botón ver para ventas completadas o canceladas
            Rule::button('ver')
                ->when(fn($row) => in_array($row->estadoVenta->nombre_estado_venta_fisica, ['completado', 'cancelado']))
                ->setAttribute('class', 'pg-btn-white dark:bg-pg-primary-700'),
        ];
    }
}
