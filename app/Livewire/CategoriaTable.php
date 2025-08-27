<?php

namespace App\Livewire;

use App\Models\CategoriaProducto;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class CategoriaTable extends PowerGridComponent
{
    public string $tableName = 'categoria-table-rfu70q-table';
    protected $listeners = ['categoriaRegistrado' => '$refresh'];

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
        return CategoriaProducto::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('nombre')
            ->add('descripccion')
            ->add('estado')
            ->add("estado")
            ->add('estado_boolean', function ($row) {
                return $row->estado === 'activo';
            })
            ->add('created_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Nombre', 'nombre')
                ->sortable()
                ->searchable()
                ->editOnClick(),

            Column::make('Descripccion', 'descripccion')
                ->sortable()
                ->searchable()
                ->editOnClick(),

            Column::make('Estado', 'estado')
                ->sortable()
                ->searchable()
                ->editOnClick(),

            Column::make('Created at', 'created_at_formatted', 'created_at')
                ->sortable(),

            Column::make('Created at', 'created_at')
                ->sortable()
                ->searchable(),

            Column::make('Estado', 'estado_boolean')
                ->sortable()
                ->searchable()
                ->toggleable(
                    trueLabel: 'activo',
                    falseLabel: 'inactivo'
                ),
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

    public function actions(CategoriaProducto $row): array
    {
        return [
            Button::add('edit')
                ->slot('Edit: ' . $row->id)
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('edit', ['rowId' => $row->id])
        ];
    }

    public function onUpdatedEditable(string|int $id, string $field, string $value): void
    {
        CategoriaProducto::find($id)->update([
            $field => $value
        ]);
    }

    public function onUpdatedToggleable(string|int $id, string $field, string $value): void
    {
        // Solo procesar si el campo es estado_boolean
        if ($field === 'estado_boolean') {
            // Convertir el valor boolean a string
            $nuevoEstado = $value ? 'activo' : 'inactivo';

            // Actualizar el campo real 'estado' en la base de datos
            CategoriaProducto::find($id)->update([
                'estado' => $nuevoEstado
            ]);
        }

        // Evitar que Livewire vuelva a renderizar el componente
        $this->skipRender();
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
