<?php

namespace App\Livewire;

use App\Models\Producto;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use Illuminate\Support\Str;

final class ProductoTable extends PowerGridComponent
{
    public string $tableName = 'producto-table-ghu6nb-table';
    protected $listeners = ['productoRegistrado' => '$refresh'];
    public string $primaryKey = 'id_producto';
    public string $sortField = 'id_producto';
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
        return Producto::query()
            ->with(['categoria_producto', 'proveedor', 'unidad']);
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id_producto')
            ->add('nombre_producto')
            ->add('unidad_nombre', fn($producto) => $producto->unidad?->nombre_unidad ?? '-')
            ->add("estado")
            ->add('estado_boolean', function ($row) {
                return $row->estado === 'activo';
            })
            ->add('codigo_barras')
            ->add('fecha_registro')
            ->add('categoria_nombre', fn($producto) => $producto->categoria_producto?->nombre_categoria_producto)
            ->add('proveedor_nombre', fn($producto) => $producto->proveedor?->nombre_proveedor);
    }


    public function columns(): array
    {
        return [
            Column::make('Id', 'id_producto'),
            Column::make('Nombre producto', 'nombre_producto')
                ->sortable(),
            Column::make('Unidad', 'unidad_nombre')->sortable(),
            Column::make('Codigo barras', 'codigo_barras')->sortable(),
            Column::make('Categoría', 'categoria_nombre'),
            Column::make('Proveedor', 'proveedor_nombre'),
            Column::make('Fecha creación', 'fecha_registro')->sortable(),
            Column::make('Estado', 'estado_boolean')
                ->sortable()
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

    public function actions(Producto $row): array
    {
        return [
            Button::add('editarProducto')
                ->slot('Editar') // <-- aquí defines el texto
                ->class('px-2 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs rounded')
                ->dispatch('editarProducto', ['productoId' => $row->id_producto])
        ];
    }
    public function onUpdatedEditable(string|int $id, string $field, string $value): void
    {
        Producto::find($id)->update([
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
            Producto::find($id)->update([
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
