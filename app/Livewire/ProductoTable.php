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
        return Producto::query()
            ->with(['categoria_producto', 'proveedor']);
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('nombre_producto')
            ->add('descripcion_resumen', fn($producto) => Str::limit($producto->descripcion, 50))
            ->add('descripcion_completa', fn($producto) => $producto->descripcion)
            ->add('precio_unitario')
            ->add('stock')
            ->add("estado")
            ->add('estado_boolean', function ($row) {
                return $row->estado === 'activo';
            })
            ->add('codigo_barras')
            ->add('created_at_formatted', fn($producto) => Carbon::parse($producto->created_at)->format('d/m/Y H:i'))
            ->add('categoria_nombre', fn($producto) => $producto->categoria_producto?->nombre)
            ->add('proveedor_nombre', fn($producto) => $producto->proveedor?->nombre);
    }


    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Nombre producto', 'nombre_producto')
                ->sortable()
                ->searchable()
                ->editOnClick(),
            Column::make('Descripcion', 'descripcion_resumen')
                ->sortable()
                ->searchable(),
            Column::make('Precio unitario', 'precio_unitario')->sortable()->searchable()->editOnClick(),
            Column::make('Stock', 'stock')->sortable()->searchable(),
            Column::make('Codigo barras', 'codigo_barras')->sortable()->searchable(),
            Column::make('Categoría', 'categoria_nombre', 'categoria_producto.nombre'),
            Column::make('Proveedor', 'proveedor_nombre', 'proveedor.nombre'),
            Column::make('Fecha creación', 'created_at_formatted', 'created_at')->sortable(),
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

    public function actions(Producto $row): array
    {
        return [
            Button::add('verDescripcion')
                ->slot('Ver Detalle')
                ->id()
                ->class('bg-blue-500 text-white px-2 py-1 rounded')
                ->dispatch('mostrarDescripcion', [
                    'id' => $row->id,
                    'descripcion' => $row->descripcion,
                ])
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
