<?php

namespace App\Livewire;

use App\Models\Especie;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class EspeciesTable extends PowerGridComponent
{
    public string $tableName = 'especies-table';
    protected $listeners = ['especieRegistrado' => '$refresh'];
    public string $primaryKey = 'id_especie';
    public string $sortField = 'id_especie';

    public function setUp(): array
    {

        return [
            PowerGrid::header(),
            PowerGrid::footer()->showPerPage()->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Especie::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id_especie')
            ->add('nombre_especie')
            ->add('descripcion')
            ->add('estado')
            ->add('estado_boolean', fn($row) => $row->estado === 'activo')
            ->add('fecha_registro');
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id_especie'),
            Column::make('Nombre', 'nombre_especie')
                ->sortable()
                ->searchable(),
            Column::make('Estado', 'estado')
                ->sortable()
                ->searchable(),
            Column::make('Fecha de Registro', 'fecha_registro')
                ->searchable()
                ->sortable(),

            Column::make('Estado', 'estado_boolean')
                ->sortable()
                ->searchable()
                ->toggleable('activo', 'inactivo'),
            Column::action('Acciones')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('nombre_especie', 'Nombre'),
            Filter::select('estado', 'Estado')
                ->dataSource([
                    ['id' => 'activo', 'name' => 'activo'],
                    ['id' => 'inactivo', 'name' => 'inactivo'],
                ])
                ->optionValue('id')
                ->optionLabel('name'),
        ];
    }

    public function actions(Especie $row): array
    {
        return [
            Button::add('editar-especie')
                ->slot('Editar')
                ->class('px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded')
                ->dispatch('abrirModalEspecie', ['especieId' => $row->id_especie])
        ];
    }

    public function onUpdatedEditable(string|int $id, string $field, string $value): void
    {
        Especie::find($id)?->update([$field => $value]);
        $this->dispatch('especieUpdated');
    }

    public function onUpdatedToggleable(string|int $id, string $field, string $value): void
    {
        if ($field === 'estado_boolean') {
            $nuevoEstado = $value ? 'activo' : 'inactivo';
            Especie::find($id)?->update(['estado' => $nuevoEstado]);
        }

        $this->dispatch('especieUpdated');
        $this->skipRender();
    }
}
