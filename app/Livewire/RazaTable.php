<?php

namespace App\Livewire;

use App\Models\Raza;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class RazaTable extends PowerGridComponent
{
    public string $tableName = 'raza-table';
    protected $listeners = ['razaRegistrado' => '$refresh'];
    public string $primaryKey = 'id_raza';
    public string $sortField = 'id_raza';

    public function setUp(): array
    {

        return [
            PowerGrid::header(),
            PowerGrid::footer()->showPerPage()->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Raza::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id_raza')
            ->add('nombre_raza')
            ->add('descripcion')
            ->add('estado')
            ->add('estado_boolean', fn($row) => $row->estado === 'activo')
            ->add('fecha_registro');
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id_raza')
                ->sortable()
                ->searchable(),

            Column::make('Nombre', 'nombre_raza')
                ->sortable()
                ->searchable(),

            Column::make('Fecha Registro', 'fecha_registro')
                ->sortable()
                ->searchable(),

            Column::action('Acciones')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('nombre_raza', 'Nombre'),

        ];
    }

    public function actions(Raza $row): array
    {
        return [
            Button::add('edit')
                ->slot('Editar')
                ->class('bg-blue-500 text-white px-3 py-2 m-1 rounded cursor-pointer text-sm')
                ->dispatch('abrirModalRaza', ['razaId' => $row->id_raza]),
        ];
    }

    public function onUpdatedEditable(String|int $id, String $field, String $value): void
    {
        Raza::find($id)->update([$field => $value]);
        $this->dispatch('razaUpdated', ['id' => $id]);
    }

    public function onUpdatedToggleable(String|int $id, String $field, String $value): void
    {
        if ($field === 'estado_boolean') {
            $nuevoEstado = $value ? 'activo' : 'inactivo';
            Raza::find($id)->update(['estado' => $nuevoEstado]);
            $this->dispatch('razaUpdated', ['id' => $id]);
        }

        $this->dispatch('razaUpdated', ['id' => $id]);
        $this->skipRender();
    }
}
