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
    public string $primaryKey = 'id_categoria_producto';
    public string $sortField = 'id_categoria_producto';
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
            ->add('id_categoria_producto')
            ->add('nombre_categoria')
            ->add('descripccion')
            ->add('estado')
            ->add("estado")
            ->add('estado_boolean', function ($row) {
                return $row->estado === 'activo';
            })
            ->add('fecha_registro');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id_categoria_producto'),
            Column::make('Nombre', 'nombre_categoria')
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

            Column::make('Fecha de registro', 'fecha_registro')
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
            Button::add('editar-categoria')
                ->slot('Editar')
                ->class('px-2 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs rounded')
                ->dispatch('abrirModalCategoria', ["categoriaId" => $row->id_categoria_producto])
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
        $this->dispatch('categoriaUpdated');
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
