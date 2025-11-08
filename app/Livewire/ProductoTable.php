<?php

namespace App\Livewire;

use App\Models\CategoriaProducto;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Unidades;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class ProductoTable extends PowerGridComponent
{
    public string $tableName = 'producto-table-ghu6nb-table';
    protected $listeners = ['productoRegistrado' => '$refresh'];
    public string $primaryKey = 'id_producto';
    public string $sortField = 'id_producto';

    // Arrays para filtros dinámicos
    public array $categorias = [];
    public array $proveedores = [];
    public array $unidades = [];
    public array $estados = [];

    public function setUp(): array
    {
        // Cargar data para filtros dinámicos
        $this->categorias = CategoriaProducto::select('id_categoria_producto as id', 'nombre_categoria_producto as name')->get()->toArray();
        $this->unidades = Unidades::select('id_unidad as id', 'nombre_unidad as name')->get()->toArray();

        // Estados predefinidos
        $this->estados = [
            ['id' => 'activo', 'name' => 'Activo'],
            ['id' => 'inactivo', 'name' => 'Inactivo'],
        ];

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
            ->with(['categoria_producto', 'proveedores', 'unidad']);
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('nombre_producto')
            ->add(
                'nombre_producto_display',
                fn($producto) =>
                '<span title="' . e($producto->nombre_producto) . '" class="cursor-help whitespace-nowrap overflow-hidden text-ellipsis max-w-xs block">' .
                    (strlen($producto->nombre_producto) > 15 ? substr($producto->nombre_producto, 0, 15) . '...' : $producto->nombre_producto) .
                    '</span>'
            )
            ->add('unidad_nombre', fn($producto) => $producto->unidad?->nombre_unidad ?? '-')
            ->add('categoria_nombre', fn($producto) => $producto->categoria_producto?->nombre_categoria_producto ?? '-')
            ->add('precio_unitario')
            ->add('codigo_barras')
            ->add('fecha_registro')
            ->add('estado')
            ->add('estado_boolean', fn($row) => $row->estado === 'activo');
    }

    public function columns(): array
    {
        return [
            Column::make('Nombre producto', 'nombre_producto_display')
                ->sortable()
                ->searchable()
                ->headerAttribute('text-left'),
            Column::make('Unidad', 'unidad_nombre')->sortable()->searchable(),
            Column::make('Precio unitario (PEN)', 'precio_unitario')->sortable(),
            Column::make('Código de barras', 'codigo_barras')->sortable()->searchable(),
            Column::make('Categoría', 'categoria_nombre')->sortable()->searchable(),
            Column::make('Fecha creación', 'fecha_registro')->sortable(),
            Column::make('Estado', 'estado')->sortable(),
            Column::action('Acciones'),
        ];
    }

    public function filters(): array
    {
        return [
            // 🔹 Filtro por nombre
            Filter::inputText('nombre_producto', 'Nombre producto'),

            // 🔹 Filtro por estado
            Filter::select('estado', 'Estado')
                ->dataSource($this->estados)
                ->optionValue('id')
                ->optionLabel('name')
                ->builder(function (Builder $query, $value) {
                    return $query->where('estado', $value);
                }),


            // 🔹 Filtro por unidad
            Filter::select('unidad_nombre', 'Unidad')
                ->dataSource($this->unidades)
                ->optionValue('id')
                ->optionLabel('name')
                ->builder(function (Builder $query, $value) {
                    return $query->whereHas('unidad', function ($q) use ($value) {
                        $q->where('id_unidad', $value);
                    });
                }),

            // 🔹 Filtro por categoría
            Filter::select('categoria_nombre', 'Categoría')
                ->dataSource($this->categorias)
                ->optionValue('id')
                ->optionLabel('name')
                ->builder(function (Builder $query, $value) {
                    return $query->whereHas('categoria_producto', function ($q) use ($value) {
                        $q->where('id_categoria_producto', $value);
                    });
                }),

            // 🔹 Filtro por código de barras
            Filter::inputText('codigo_barras', 'Código de barras'),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js("alert('Editar producto: ' + {$rowId})");
    }

    public function actions(Producto $row): array
    {
        return [
            Button::add('editarProducto')
                ->slot('Editar')
                ->class('px-2 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs rounded')
                ->dispatch('editarProducto', ['productoId' => $row->id_producto]),
        ];
    }

    public function onUpdatedEditable(string|int $id, string $field, string $value): void
    {
        Producto::find($id)?->update([$field => $value]);
    }

    public function onUpdatedToggleable(string|int $id, string $field, string $value): void
    {
        if ($field === 'estado_boolean') {
            $nuevoEstado = $value ? 'activo' : 'inactivo';
            Producto::find($id)?->update(['estado' => $nuevoEstado]);
        }

        $this->skipRender();
    }
}
