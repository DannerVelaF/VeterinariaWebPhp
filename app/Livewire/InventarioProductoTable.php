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

final class InventarioProductoTable extends PowerGridComponent
{
    public string $tableName = 'inventario-producto-table-dpdvfe-table';
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
        return Producto::query()->with('lotes.movimientos');
    }


    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('nombre_producto')
            ->add('stock_total', fn($producto) => $producto->lotes->sum('cantidad_almacenada'))
            ->add('stock_almacen', fn($producto) => $producto->lotes->sum(function ($lote) {
                return $lote->movimientos
                    ->where('id_tipo_ubicacion', 1) // 1 = Almacén
                    ->sum('cantidad_movimiento') + ($lote->cantidad_almacenada ?? 0);
            }))
            ->add('stock_mostrador', fn($producto) => $producto->lotes->sum(function ($lote) {
                return $lote->movimientos
                    ->where('id_tipo_ubicacion', 2) // 2 = Mostrador
                    ->sum('cantidad_movimiento') + ($lote->cantidad_mostrada ?? 0);
            }))
            ->add('precio_compra_promedio', fn($producto) => number_format($producto->lotes->avg('precio_compra'), 2));
    }

    public function columns(): array
    {
        return [
            Column::make('Producto', 'nombre_producto')->sortable()->searchable(),
            Column::make('Stock total', 'stock_total')->sortable(),
            Column::make('Stock Almacén', 'stock_almacen')->sortable(),
            Column::make('Stock Mostrador', 'stock_mostrador')->sortable(),
            Column::make('Precio Compra Promedio', 'precio_compra_promedio')->sortable(),
            Column::action('Action'),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::select('id_proveedor', 'Proveedor')->dataSource(\App\Models\Proveedor::all()->pluck('nombre_proveedor', 'id_proveedor')),
            Filter::select('id_categoria_producto', 'Categoría')->dataSource(\App\Models\CategoriaProducto::all()->pluck('nombre', 'id_categoria_producto')),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    public function actions(Producto $row): array
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
