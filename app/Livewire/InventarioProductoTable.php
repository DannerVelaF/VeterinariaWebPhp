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
        return [
            PowerGrid::header(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Producto::query()->with(['lotes.inventarios', 'proveedor', 'categoria_producto']);
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('nombre_producto')
            ->add('proveedor', fn($producto) => $producto->proveedor->nombre_proveedor ?? 'N/A')
            ->add('categoria', fn($producto) => $producto->categoria_producto->nombre_categoria_producto ?? 'N/A')
            ->add('stock_total', fn($producto) => $producto->lotes->sum('cantidad_almacenada') + $producto->lotes->sum('cantidad_mostrada'))
            ->add('stock_almacen', fn($producto) => $producto->lotes->sum('cantidad_almacenada'))
            ->add('stock_mostrador', fn($producto) => $producto->lotes->sum('cantidad_mostrada'));
    }

    public function columns(): array
    {
        return [
            Column::make('Producto', 'nombre_producto')->sortable()->searchable(),
            Column::make('Proveedor', 'proveedor')->sortable(),
            Column::make('Categoría', 'categoria')->sortable(),
            Column::make('Stock Total', 'stock_total')->sortable(),
            Column::make('Stock Almacén', 'stock_almacen')->sortable(),
            Column::make('Stock Mostrador', 'stock_mostrador')->sortable(),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::select('producto', 'productos.id_producto')
                ->dataSource(\App\Models\Producto::all()->toArray())
                ->optionValue('id_producto')
                ->optionLabel('nombre_producto'),

            Filter::select('proveedor', 'productos.id_proveedor')
                ->dataSource(\App\Models\Proveedor::all()->toArray())
                ->optionValue('id_proveedor')
                ->optionLabel('nombre_proveedor'),

            Filter::select('categoria', 'productos.id_categoria_producto')
                ->dataSource(\App\Models\CategoriaProducto::all()->toArray())
                ->optionValue('id_categoria_producto')
                ->optionLabel('nombre_categoria_producto'),
        ];
    }
}
