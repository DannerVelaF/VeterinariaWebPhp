<?php

namespace App\Livewire;

use App\Models\Compra;
use App\Models\Proveedor;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class ComprasTable extends PowerGridComponent
{
    public string $tableName = 'compras-table-aeu87r-table';
    use WithExport;

    public bool $showFiltersButton = false; // 👈 oculta el botón
    public string $primaryKey = 'id_compra';
    public string $sortField = 'id_compra';
    public $listeners = ['comprasUpdated' => '$refresh'];

    public function boot(): void
    {
        config(['livewire-powergrid.filter' => 'outside']);
    }

    public function setUp(): array
    {

        return [
            PowerGrid::header(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount()

        ];
    }

    public function datasource(): Builder
    {
        return Compra::query(["trabajador", "proveedor"])->orderBy("compras.fecha_registro", "desc");
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id_compra')
            ->add('codigo')
            ->add('fecha_compra')
            ->add('fecha_registro')
            ->add('estado', fn($compra) => '<span class="capitalize">' . $compra->estadoCompra->nombre_estado_compra . '</span>')
            ->add('cantidad_total')
            ->add('total')
            ->add(
                "usuario",
                fn($compra) => $compra->trabajador && $compra->trabajador->persona && $compra->trabajador->persona->user
                    ? $compra->trabajador->persona->user->usuario
                    : '-'
            )
            ->add('proveedor', fn($compra) => $compra->proveedor->nombre_proveedor);
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id_compra'),
            Column::make('Orden Compra', 'codigo')
                ->sortable()
                ->searchable(),

            Column::make('Fecha compra', 'fecha_compra')
                ->sortable(),

            Column::make('Fecha registro', 'fecha_registro')
                ->sortable(),

            Column::make('Estado', 'estado')
                ->sortable()
                ->searchable(),

            Column::make('Total', 'total')
                ->sortable()
                ->searchable(),
            Column::make("Cantidad total", "cantidad_total")
                ->sortable()
                ->searchable(),

            Column::make('Usuario', 'usuario'),
            Column::make('Proveedor', 'proveedor'),
            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::select('estado', 'estado')
                ->dataSource([
                    ['id' => 'pendiente', 'name' => 'Pendiente'],
                    ['id' => 'aprobado', 'name' => 'Aprobado'],
                    ['id' => 'recibido', 'name' => 'Recibido'],
                    ['id' => 'cancelado', 'name' => 'Cancelado'],
                ])
                ->optionValue('id')
                ->optionLabel('name'),

            Filter::datePicker('fecha_compra', 'fecha_compra')
                ->params([
                    'dateFormat' => 'Y-m-d',
                    'locale' => 'es',
                    'enableTime' => false,
                ]),
            Filter::select('proveedor', 'id_proveedor')
                ->dataSource(Proveedor::orderBy('nombre_proveedor')->get()->toArray())
                ->optionValue('id_proveedor')
                ->optionLabel('nombre_proveedor')
                ->builder(function (Builder $query, $value) {
                    $v = is_array($value)
                        ? ($value['value'] ?? $value['search'] ?? array_values($value)[0] ?? '')
                        : (string)$value;

                    $v = trim($v);
                    if ($v === '') {
                        return $query;
                    }

                    return $query->where('id_proveedor', $v);
                }),

        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    public function actions(Compra $row): array
    {
        return [
            Button::add('ver')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye-icon lucide-eye"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg>')
                ->id()
                ->class('pg-btn-white dark:bg-pg-primary-700')
                ->dispatch('show-modal', ['rowId' => $row->id_compra]),

            Button::add('aprobar')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="green" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check-icon lucide-check"><path d="M20 6 9 17l-5-5"/></svg>')
                ->id()
                ->class('pg-btn-white dark:bg-pg-primary-700')
                ->dispatch('aprobar-compra', ['rowId' => $row->id_compra]),

            Button::add('rechazar')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="red" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-x-icon lucide-circle-x"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/></svg>')
                ->id()
                ->class('pg-btn-white dark:bg-pg-primary-700')
                ->dispatch('rechazar-compra', ['rowId' => $row->id_compra]),

            // Redirige a la ruta de entradas pasando el código de la compra como parámetro 'ordenCompra'
            Button::add('recepcionar')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-package-plus text-blue-600"><path d="M16 16h6"/><path d="M19 13v6"/><path d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14"/><path d="M16.5 9.4 7.55 4.24"/><polyline points="3.29 7 12 12 20.71 7"/><line x1="12" x2="12" y1="22" y2="12"/></svg>')
                ->id()
                ->class('pg-btn-white dark:bg-pg-primary-700')
                // ✅ CORREGIDO: Solo usamos route(), al no poner target se abre en la misma pestaña por defecto.
                ->route('inventario.entradas', ['ordenCompra' => $row->codigo]),
        ];
    }

    public function actionRules($row): array
    {
        return [
            // Ocultar botones aprobar/rechazar si ya está procesado
            Rule::button('aprobar')
                ->when(fn($row) => in_array($row->estadoCompra->nombre_estado_compra, ['recibido', 'cancelado', 'aprobado']) || !auth()->user()->tienePermiso('aprobacion-compras'))
                ->hide(),

            Rule::button('rechazar')
                ->when(fn($row) => in_array($row->estadoCompra->nombre_estado_compra, ['recibido', 'cancelado', 'aprobado']) || !auth()->user()->tienePermiso('aprobacion-compras'))
                ->hide(),

            // ✅ REGLA NUEVA: Mostrar botón "Recepcionar" SOLO si está "aprobado" (o parcialmente recibido)
            Rule::button('recepcionar')
                ->when(fn($row) => $row->estadoCompra->nombre_estado_compra !== 'aprobado')
                ->hide(),
        ];
    }
}
