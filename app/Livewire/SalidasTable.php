<?php

namespace App\Livewire;

use App\Models\InventarioMovimiento;
use App\Models\Producto;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class SalidasTable extends PowerGridComponent
{
    public string $tableName = 'salidas-table';
    protected $listeners = ['salidaRegistrada' => '$refresh'];

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
        return InventarioMovimiento::query()
            ->with(["trabajador", "lote.producto"])
            ->where('tipo_movimiento', 'salida')
            ->orderBy('fecha_movimiento', 'desc');
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id', fn($row) => $row->id)
            ->add('producto', fn($inventario) => $inventario->lote->producto->nombre_producto)
            ->add('cantidad_movimiento', fn($inventario) =>
                '<span class="bg-red-100 text-red-800 px-3 rounded-md text-sm font-medium">
                    -' . $inventario->cantidad_movimiento . '
                  </span>')
           ->add('fecha_movimiento_formatted', fn($inventario) => 
                $inventario->fecha_movimiento?->format('d/m/Y H:i'))
            ->add('ubicacion', function ($salida) {
                $icon = $salida->ubicacion === 'mostrador' 
                    ? '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-cart text-gray-600"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>'
                    : '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-warehouse text-gray-600"><path d="M18 21V10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1v11"/><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V8a2 2 0 0 1 1.132-1.803l7.95-3.974a2 2 0 0 1 1.837 0l7.948 3.974A2 2 0 0 1 22 8z"/><path d="M6 13h12"/><path d="M6 17h12"/></svg>';

                return '<p class="capitalize flex items-center gap-2 text-sm">' . $icon . '<span>' . $salida->ubicacion . '</span></p>';
            })
            ->add('motivo', fn($inventario) => $inventario->motivo)
            ->add('usuario', fn($inventario) => $inventario->trabajador->persona->user->username)
            ->add('lote', fn($inventario) => $inventario->lote->codigo_lote);
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Producto', 'producto')
                ->sortable()
                ->searchable(),

            Column::make('Cantidad', 'cantidad_movimiento')
                ->sortable()
                ->searchable(),

            Column::make('Fecha', 'fecha_movimiento_formatted', 'fecha_movimiento')
                ->sortable(),

            Column::make("Ubicación", "ubicacion"),
            
            Column::make("Motivo", "motivo")
                ->sortable()
                ->searchable(),

            Column::make('Usuario', 'usuario'),
            Column::make('Lote', 'lote'),
            Column::action('Acciones')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::select('ubicacion')
                ->dataSource([
                    ['id' => 'almacen', 'name' => 'Almacén'],
                    ['id' => 'mostrador', 'name' => 'Mostrador'],
                ])
                ->optionValue('id')
                ->optionLabel('name'),
            Filter::select('producto', 'Producto')
                ->dataSource(
                    Producto::query()
                        ->select('id as id', 'nombre_producto as name')
                        ->get()
                        ->toArray()
                )
                ->optionValue('id')
                ->optionLabel('name'),
        ];
    }

    public function actions(InventarioMovimiento $row): array
    {
        return [
            Button::add('ver')
                ->slot('👁 Ver')
                ->id()
                ->class('pg-btn-white dark:bg-pg-primary-700')
                ->dispatch('show-modal', ['rowId' => $row->id]),
        ];
    }
}