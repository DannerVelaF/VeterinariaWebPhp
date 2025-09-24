<?php

namespace App\Livewire;

use App\Models\Compra;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class ComprasTable extends PowerGridComponent
{
    public string $tableName = 'compras-table-aeu87r-table';

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
        return Compra::query(["trabajador", "proveedor"]);
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('codigo')
            ->add('fecha_compra')
            ->add('fecha_registro')
            ->add('estado', fn($compra) =>
            '<span class="capitalize">' . $compra->estado . '</span>')
            ->add('cantidad_total')
            ->add('total')
            ->add("usuario", fn($compra) => $compra->trabajador->persona->user->username)
            ->add('proveedor', fn($compra) => $compra->proveedor->nombre);
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Orden Compra', 'codigo')
                ->sortable()
                ->searchable(),

            Column::make('Fecha compra', 'fecha_compra')
                ->sortable(),

            Column::make('Fecha registro', 'fecha_registro')
                ->sortable(),

            Column::make('Estado', 'estado')
                ->sortable()
                ->searchable(),

            Column::make('Total', 'total')
                ->sortable()
                ->searchable(),
            Column::make("Cantidad total", "cantidad_total")
                ->sortable()
                ->searchable(),

            Column::make('Usuario', 'usuario'),
            Column::make('Proveedor', 'proveedor'),
            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    public function actions(Compra $row): array
    {
        return [
            Button::add('ver')
                ->slot('👁 Ver')
                ->id()
                ->class('pg-btn-white dark:bg-pg-primary-700')
                ->dispatch('show-modal', ['rowId' => $row->id]),
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
