<?php

namespace App\Livewire;

use App\Models\PuestoTrabajador;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class PuestosTable extends PowerGridComponent
{
    public string $tableName = 'puestos-table-nctcot-table';
    public string $primaryKey = 'id_puesto_trabajo';
    public string $sortField = 'id_puesto_trabajo';
    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::header(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return PuestoTrabajador::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('nombre_puesto')
            ->add('descripcion')
            ->add('estado')
            ->add('estado_boolean', function ($row) {
                return $row->estado === 'activo';
            })
            ->add('fecha_registro')
            ->add('fecha_actualizacion');
    }


    public function columns(): array
    {
        return [

            Column::make('Nombre', 'nombre_puesto')
                ->sortable()
                ->searchable(),

            Column::make('Descripción', 'descripcion')
                ->sortable()
                ->searchable(),

            Column::make('Estado', 'estado')
                ->sortable()
                ->searchable(),

            Column::make('Fecha de registro', 'fecha_registro')
                ->sortable()
                ->searchable(),
            Column::make('Fecha de actualización', 'fecha_actualizacion')
                ->sortable()
                ->searchable(),
            Column::make('Cambiar Estado', 'estado_boolean')
                ->toggleable(
                    trueLabel: 'activo',
                    falseLabel: 'inactivo'
                ),
            Column::action('Acciones')
                ->hidden(),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('nombre_puesto')
                ->placeholder('Buscar por nombre'),
                Filter::select('estado', 'Estado')
                    ->dataSource([
                        ['id' => 'activo', 'name' => 'activo'],
                        ['id' => 'inactivo', 'name' => 'inactivo'],
                    ])
                    ->optionValue('id')
                    ->optionLabel('name'),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }


    public function onUpdatedEditable(string|int $id, string $field, string $value): void
    {
        PuestoTrabajador::find($id)->update([
            $field => $value
        ]);
    }

    public function onUpdatedToggleable(string|int $id, string $field, string $value): void
    {
        if ($field === 'estado_boolean') {
            $nuevoEstado = $value ? 'activo' : 'inactivo';

            PuestoTrabajador::find($id)->update([
                'estado' => $nuevoEstado
            ]);
        }

        $this->skipRender();
    }
    public function actions(PuestoTrabajador $row): array
    {
        return [
            Button::add('edit')
                ->slot('Edit: ' . $row->id)
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('edit', ['rowId' => $row->id])
        ];
    }

    #[\Livewire\Attributes\On('puestosUpdated')]
    public function refreshTable(): void
    {
        $this->refresh(); // <- Método de PowerGrid que recarga la data
    }

    #[\Livewire\Attributes\On('pg:eventRefresh-default')]
    public function handleRefresh(): void
    {
        $this->fillData();
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
