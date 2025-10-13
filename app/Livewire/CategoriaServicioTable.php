<?php

namespace App\Livewire;

use App\Models\CategoriaServicio;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class CategoriaServicioTable extends PowerGridComponent
{
    public string $tableName = 'categoria-servicio-table';
    protected $listeners = ['categoriaServicioRegistrado' => '$refresh'];
    public string $primaryKey = 'id_categoria_servicio';
    public string $sortField = 'id_categoria_servicio';

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::header()->showSearchInput(),
            PowerGrid::footer()->showPerPage()->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return CategoriaServicio::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id_categoria_servicio')
            ->add('nombre_categoria_servicio')
            ->add('descripcion')
            ->add('estado')
            ->add('estado_boolean', fn($row) => $row->estado === 'activo')
            ->add('fecha_registro');
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id_categoria_servicio'),
            Column::make('Nombre', 'nombre_categoria_servicio')
                ->sortable()
                ->searchable()
                ->editOnClick(),

            Column::make('Descripción', 'descripcion')
                ->sortable()
                ->searchable()
                ->editOnClick(),

            Column::make('Estado', 'estado')
                ->sortable()
                ->searchable()
                ->editOnClick(),

            Column::make('Fecha de registro', 'fecha_registro')
                ->sortable()
                ->searchable(),

            Column::make('Estado', 'estado_boolean')
                ->sortable()
                ->searchable()
                ->toggleable(trueLabel: 'activo', falseLabel: 'inactivo'),

            Column::action('Acciones')
        ];
    }

    public function filters(): array
    {
        return [];
    }

    public function actions(CategoriaServicio $row): array
    {
        return [
            Button::add('editar-servicio')
                ->slot('Editar')
                ->class('px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded')
                ->dispatch('abrirModalCategoriaServicio', ['categoriaServicioId' => $row->id_categoria_servicio])
        ];
    }

    public function onUpdatedEditable(string|int $id, string $field, string $value): void
    {
        CategoriaServicio::find($id)?->update([$field => $value]);
        $this->dispatch('categoriaServicioUpdated');
    }

    public function onUpdatedToggleable(string|int $id, string $field, string $value): void
    {
        if ($field === 'estado_boolean') {
            $nuevoEstado = $value ? 'activo' : 'inactivo';
            CategoriaServicio::find($id)?->update(['estado' => $nuevoEstado]);
        }

        $this->dispatch('categoriaServicioUpdated');
        $this->skipRender();
    }
}
