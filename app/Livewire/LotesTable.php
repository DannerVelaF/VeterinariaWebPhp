<?php

namespace App\Livewire;

use App\Models\Lotes;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class LotesTable extends PowerGridComponent
{
    public string $tableName = 'lotes-table-navkq7-table';

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::header()
                ->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Lotes::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('producto', fn($lote) => $lote->producto->nombre_producto)
            ->add('cantidad_mostrada')
            ->add('cantidad_almacenada')
            ->add('cantidad_vendida')
            ->add('codigo_lote')
            ->add('precio_compra')
            ->add('fecha_recepcion_formatted', fn(Lotes $model) => Carbon::parse($model->fecha_recepcion)->format('d/m/Y'))
            ->add('fecha_vencimiento_formatted', fn(Lotes $model) => Carbon::parse($model->fecha_vencimiento)->format('d/m/Y'))
            ->add('estado')
            ->add('observacion')
            ->add('created_at')
            ->add('updated_at')
            ->add('created_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Producto', 'producto'),
            Column::make('Cantidad mostrada', 'cantidad_mostrada')
                ->sortable()
                ->searchable(),

            Column::make('Cantidad almacenada', 'cantidad_almacenada')
                ->sortable()
                ->searchable(),

            Column::make('Cantidad vendida', 'cantidad_vendida')
                ->sortable()
                ->searchable(),

            Column::make('Codigo lote', 'codigo_lote')
                ->sortable()
                ->searchable(),

            Column::make('Precio compra', 'precio_compra')
                ->sortable()
                ->searchable(),

            Column::make('Fecha recepcion', 'fecha_recepcion_formatted', 'fecha_recepcion')
                ->sortable(),

            Column::make('Fecha vencimiento', 'fecha_vencimiento_formatted', 'fecha_vencimiento')
                ->sortable(),

            Column::make('Estado', 'estado')
                ->sortable()
                ->searchable(),

            Column::make('Observacion', 'observacion')
                ->sortable()
                ->searchable(),

            Column::make('Created at', 'created_at_formatted', 'created_at')
                ->sortable(),

            Column::make('Created at', 'created_at')
                ->sortable()
                ->searchable(),

            Column::make('Updated at', 'updated_at_formatted', 'updated_at')
                ->sortable(),

            Column::make('Updated at', 'updated_at')
                ->sortable()
                ->searchable(),

            Column::make('Created at', 'created_at_formatted', 'created_at')
                ->sortable(),

            Column::make('Created at', 'created_at')
                ->sortable()
                ->searchable(),

            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::datepicker('fecha_recepcion'),
            Filter::datepicker('fecha_vencimiento'),
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
                ->slot('Edit: ' . $row->id)
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('edit', ['rowId' => $row->id])
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
