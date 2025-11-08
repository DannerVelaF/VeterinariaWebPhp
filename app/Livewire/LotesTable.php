<?php

namespace App\Livewire;

use App\Models\Lotes;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class LotesTable extends PowerGridComponent
{
    public string $tableName = 'lotes-table-navkq7-table';
    public bool $showFilters = true;
    public string $primaryKey = 'id_lote';
    public string $sortField = 'id_lote';
    use WithExport;

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
                ->showRecordCount(),

        ];
    }

    public function datasource(): Builder
    {
        return Lotes::query(["producto.proveedores"])->orderBy('fecha_registro', 'desc');;
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('producto', fn($lote) => $lote->producto->nombre_producto)
            ->add('cantidad_total')
            ->add('codigo_lote')
            ->add("cantidad_almacenada")
            ->add("cantidad_mostrada")
            ->add('precio_compra', fn($lote) => "$" . number_format($lote->precio_compra, 2))
            ->add('fecha_recepcion')
            ->add('fecha_vencimiento')
            ->add('estado')
            ->add('fecha_registro');
    }

    public function columns(): array
    {
        return [
            Column::make('Codigo lote', 'codigo_lote')
                ->searchable(),

            Column::make('Producto', 'producto')
                ->searchable(),

            Column::make('Stock total', 'cantidad_total')
                ->sortable(),

            Column::make('Almacen', 'cantidad_almacenada')
                ->sortable(),
            Column::make('Mostrador', 'cantidad_mostrada')
                ->sortable(),

            Column::make('Precio compra', 'precio_compra')
                ->sortable()
                ->searchable(),

            Column::make('Fecha recepcion', 'fecha_recepcion')
                ->sortable(),

            Column::make('Fecha vencimiento', 'fecha_vencimiento')
                ->sortable(),

            Column::make('Estado', 'estado')
                ->sortable()
                ->hidden()
                ->searchable(),

            Column::make('Fecha de registro', 'fecha_registro')
                ->sortable()
                ->searchable(),


            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
            // Producto: Lotes tiene columna id_producto, por eso usamos 'id_producto' como columna real
            Filter::select('producto', 'id_producto')
                ->dataSource(Producto::orderBy('nombre_producto')->get()->toArray())
                ->optionValue('id_producto')
                ->optionLabel('nombre_producto'),

            // Proveedor: Lotes no tiene proveedor_id directo, filtramos con whereHas sobre la relación producto
            Filter::select('proveedor', 'proveedor')
                ->dataSource(Proveedor::orderBy('nombre_proveedor')->get()->toArray())
                ->optionValue('id_proveedor')
                ->optionLabel('nombre_proveedor')
                ->builder(function (Builder $query, $value) {
                    // normalizar valor (puede venir string o array)
                    if (is_array($value)) {
                        $v = $value['value'] ?? $value['search'] ?? array_values($value)[0] ?? '';
                    } else {
                        $v = (string) $value;
                    }

                    $v = trim($v);
                    if ($v === '') {
                        return $query;
                    }

                    // Filtrar lotes por proveedor a través de la relación producto
                    return $query->whereHas('producto', function ($q) use ($v) {
                        // suponiendo que la columna FK en productos es id_proveedor
                        $q->where('id_proveedor', $v);
                    });
                }),

            // Estado (select simple sobre la columna estado en lotes)
            Filter::select('estado', 'estado')
                ->dataSource([
                    ['id' => 'activo', 'name' => 'Activo'],
                    ['id' => 'inactivo', 'name' => 'Inactivo'],
                    // agrega más estados que uses en tu app
                ])
                ->optionValue('id')
                ->optionLabel('name')
                ->builder(function (Builder $query, $value) {
                    if (is_array($value)) {
                        $v = $value['value'] ?? $value['search'] ?? array_values($value)[0] ?? '';
                    } else {
                        $v = (string) $value;
                    }

                    $v = trim($v);
                    if ($v === '') {
                        return $query;
                    }

                    return $query->where('estado', $v);
                }),

            // Datepickers para fechas (columna real en lotes)
            Filter::datePicker('fecha_recepcion', 'fecha_recepcion')
                ->params([
                    'dateFormat' => 'Y-m-d',
                    'locale' => 'es',
                    'enableTime' => false,
                ]),

            Filter::datePicker('fecha_vencimiento', 'fecha_vencimiento')
                ->params([
                    'dateFormat' => 'Y-m-d',
                    'locale' => 'es',
                    'enableTime' => false,
                ]),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    public function actions(Lotes $row): array
    {
        return [
            Button::add('edit')
                ->slot('Edit: ' . $row->id_lote)
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('edit', ['rowId' => $row->id_lote])
        ];
    }

    /*
    public function actionRules($row): array
    {
       return [
            // Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($row) => $row->id === 1)
                ->hide(),
        ];
    }
    */
}
