<?php

namespace App\Livewire;

use App\Models\InventarioMovimiento;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\TipoUbicacion;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class EntradasTable extends PowerGridComponent
{
    public string $tableName = 'entradas-table-zlucbi-table';
    protected $listeners = ['entradasUpdated' => '$refresh'];
    public bool $showFilters = true;
    public string $primaryKey = 'id_inventario_movimiento';
    public string $sortField = 'id_inventario_movimiento';
    use WithExport;

    public function boot(): void
    {
        config(['livewire-powergrid.filter' => 'outside']);
    }

    public array $ubicaciones = [];
    public array $proveedores = [];
    public array $productos = [];

    public function setUp(): array
    {
        $this->ubicaciones = TipoUbicacion::select('id_tipo_ubicacion as id', 'nombre_tipo_ubicacion as name')->get()->toArray();
        $this->proveedores = Proveedor::select('id_proveedor as id', 'nombre_proveedor as name')->get()->toArray();
        $this->productos = Producto::select('id_producto as id', 'nombre_producto as name')->get()->toArray();

        return [
            PowerGrid::header(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return InventarioMovimiento::query()
            ->join('lotes', 'inventario_movimientos.id_lote', '=', 'lotes.id_lote')
            ->join('productos', 'lotes.id_producto', '=', 'productos.id_producto')
            // ✅ CORRECCIÓN: Usar la tabla pivote para la relación con proveedores
            ->join('producto_proveedores', 'productos.id_producto', '=', 'producto_proveedores.id_producto')
            ->join('proveedores', 'producto_proveedores.id_proveedor', '=', 'proveedores.id_proveedor')
            ->with(['lote.producto.proveedores', 'trabajador.persona.user'])
            ->select(
                'inventario_movimientos.*',
                'productos.id_producto',
                'productos.nombre_producto as producto',
                'proveedores.id_proveedor',
                'proveedores.nombre_proveedor as proveedor',
                'lotes.codigo_lote as lote'
            )
            // ✅ CORRECCIÓN: Agregar distinct para evitar duplicados por la relación muchos a muchos
            ->distinct('inventario_movimientos.id_inventario_movimiento')
            ->orderBy('inventario_movimientos.fecha_movimiento', 'desc');
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('producto')
            ->add('proveedor')
            ->add('cantidad_movimiento', fn($inventario) =>
            '<span class="bg-[#374151] text-white px-3 rounded-md text-sm">
                    +' . $inventario->cantidad_movimiento . '
                  </span>')
            ->add('fecha_movimiento')
            ->add('ubicacion', function ($entrada) {
                $icon = '';

                if ($entrada->tipoUbicacion->nombre_tipo_ubicacion === 'mostrador') {
                    $icon = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-shopping-cart text-gray-600">
                                <circle cx="8" cy="21" r="1" />
                                <circle cx="19" cy="21" r="1" />
                                <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78
                                         a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12" />
                            </svg>';
                } elseif ($entrada->tipoUbicacion->nombre_tipo_ubicacion === 'almacen') {
                    $icon = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-warehouse text-gray-600">
                                <path d="M18 21V10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1v11" />
                                <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V8
                                         a2 2 0 0 1 1.132-1.803l7.95-3.974a2 2 0 0 1
                                         1.837 0l7.948 3.974A2 2 0 0 1 22 8z" />
                                <path d="M6 13h12" />
                                <path d="M6 17h12" />
                            </svg>';
                }

                return '<p class="capitalize flex items-center gap-2 text-sm">'
                    . $icon .
                    '<span>' . $entrada->tipoUbicacion->nombre_tipo_ubicacion . '</span>
                    </p>';
            })
            ->add('usuario', fn($inventario) => $inventario->trabajador->persona->user->usuario)
            ->add('lote')
            ->add(
                'fecha_recepcion',
                fn($lote) =>
                Carbon::parse($lote->fecha_recepcion)->format('Y-m-d H:m:s')
            );
    }

    public function columns(): array
    {
        return [
            Column::make('Producto', 'producto', 'productos.nombre_producto')->sortable(),
            Column::make('Proveedor', 'proveedor', 'proveedores.nombre_proveedor')
                ->sortable(),
            Column::make('Cantidad', 'cantidad_movimiento')
                ->sortable()
                ->searchable(),
            Column::make('Fecha movimiento', 'fecha_movimiento')
                ->sortable(),
            Column::make("Ubicacion", "ubicacion"),
            Column::make('Usuario', 'usuario'),
            Column::make('Lote', 'lote'),
            Column::make('Fecha recepcion', 'fecha_recepcion'),
            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::select('producto', 'productos.id_producto')
                ->dataSource(Producto::orderBy('nombre_producto')->get()->toArray())
                ->optionValue('id_producto')
                ->optionLabel('nombre_producto'),

            Filter::select('proveedor', 'proveedores.id_proveedor')
                ->dataSource(Proveedor::orderBy('nombre_proveedor')->get()->toArray())
                ->optionValue('id_proveedor')
                ->optionLabel('nombre_proveedor'),

            Filter::select('ubicacion', 'inventario_movimientos.id_tipo_ubicacion')
                ->dataSource($this->ubicaciones)
                ->optionValue('id')
                ->optionLabel('name'),

            Filter::datePicker('fecha_movimiento', 'fecha_movimiento')
                ->params([
                    'dateFormat' => 'Y-m-d',
                    'locale'     => 'es',
                    'enableTime' => false,
                ]),
            Filter::inputText('usuario')
                ->placeholder('Usuario (contiene)...')
                ->builder(function (Builder $query, $value) {
                    if (is_array($value)) {
                        $v = $value['value'] ?? $value['search'] ?? array_values($value)[0] ?? '';
                    } else {
                        $v = (string) $value;
                    }

                    $v = trim($v);

                    if ($v === '') {
                        return $query;
                    }

                    return $query->whereHas('trabajador.persona.user', function ($q) use ($v) {
                        $q->where('usuario', 'like', '%' . $v . '%');
                    });
                }),
            Filter::datePicker('fecha_recepcion', 'lotes.fecha_recepcion')
                ->params([
                    'dateFormat' => 'Y-m-d',
                    'locale'     => 'es',
                    'enableTime' => false,
                ]),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    public function actions(InventarioMovimiento $row): array
    {
        return [
            Button::add('ver')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye-icon lucide-eye"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg>')
                ->id()
                ->class('pg-btn-white dark:bg-pg-primary-700')
                ->dispatch('show-modal', ['rowId' => $row->id_inventario_movimiento]),
        ];
    }
}
