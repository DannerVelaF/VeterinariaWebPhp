<?php

namespace App\Livewire;

use App\Models\MetodoPago;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class MetodosPagoTable extends PowerGridComponent
{
    public string $tableName = 'metodos-pago-table-4djvp7-table';
    public string $primaryKey = 'id_metodo_pago';
    public string $sortField = 'id_metodo_pago';

    protected function getListeners()
    {
        return array_merge(
            parent::getListeners(),
            [
                'refresh-table' => '$refresh',
            ]
        );
    }

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
        return MetodoPago::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('nombre_metodo')
            ->add('tipo_metodo')
            ->add('numero_cuenta')
            ->add('nombre_titular')
            ->add('entidad_financiera')
            ->add('tipo_cuenta')
            ->add('estado_formatted', function ($model) {
                return $model->estado ?
                    '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Activo</span>' :
                    '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Inactivo</span>';
            });
    }

    public function columns(): array
    {
        return [
            Column::make('Nombre metodo', 'nombre_metodo')
                ->sortable()
                ->searchable(),

            Column::make('Tipo metodo', 'tipo_metodo')
                ->sortable()
                ->searchable(),

            Column::make('Numero cuenta', 'numero_cuenta')
                ->sortable()
                ->searchable(),

            Column::make('Nombre titular', 'nombre_titular')
                ->sortable()
                ->searchable(),

            Column::make('Entidad financiera', 'entidad_financiera')
                ->sortable()
                ->searchable(),

            Column::make('Tipo cuenta', 'tipo_cuenta')
                ->sortable()
                ->searchable(),

            Column::make('Estado', 'estado_formatted')
                ->sortable(),

            Column::action('Acciones')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('nombre_metodo'),
            Filter::inputText('nombre_titular'),
            Filter::inputText("numero_cuenta"),
            Filter::boolean('estado')->label('Activo', 'Inactivo'),
        ];
    }

    public function actions(MetodoPago $row): array
    {
        return [
            Button::add('editar')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>')
                ->id()
                ->class('pg-btn-white dark:bg-pg-primary-700')
                ->dispatch('edit-metodo-pago', ['rowId' => $row->id_metodo_pago])
        ];
    }
}
