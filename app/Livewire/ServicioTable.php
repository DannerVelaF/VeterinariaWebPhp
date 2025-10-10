<?php

namespace App\Livewire;

use App\Models\Servicio;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class ServicioTable extends PowerGridComponent
{
    public string $tableName = 'servicio-table-xyz';
    protected $listeners = ['servicioRegistrado' => '$refresh'];
    public string $primaryKey = 'id_servicio';
    public string $sortField = 'id_servicio';

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::header(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Servicio::query()->with(['categoriaServicio']);
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id_servicio')
            ->add('nombre_servicio')
            ->add('descripcion')
            ->add('duracion_estimada')
            ->add('precio_unitario')
            ->add('estado')
            ->add('estado_boolean', fn($row) => $row->estado === 'activo')
            ->add('fecha_registro')
            ->add('categoria_nombre', fn($servicio) => $servicio->categoriaServicio?->nombre_categoria_servicio ?? '-');
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id_servicio')->sortable(),
            Column::make('Nombre del servicio', 'nombre_servicio')->sortable()->searchable(),
            Column::make('Descripción', 'descripcion')->sortable()->searchable(),
            Column::make('Duración estimada', 'duracion_estimada')->sortable(),
            Column::make('Precio unitario', 'precio_unitario')->sortable(),
            Column::make('Categoría', 'categoria_nombre')->sortable()->searchable(),
            Column::make('Fecha de registro', 'fecha_registro')->sortable(),
            Column::make('Estado', 'estado_boolean')
                ->sortable()
                ->toggleable(trueLabel: 'activo', falseLabel: 'inactivo'),
            Column::action('Acciones')
        ];
    }

    public function filters(): array
    {
        return [];
    }

    public function actions(Servicio $row): array
    {
        return [
            Button::add('editarServicio')
                ->slot('Editar')
                ->class('px-2 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs rounded')
                ->dispatch('editarServicio', ['servicioId' => $row->id_servicio]),
        ];
    }

    public function onUpdatedEditable(string|int $id, string $field, string $value): void
    {
        Servicio::find($id)?->update([$field => $value]);
    }

    public function onUpdatedToggleable(string|int $id, string $field, string $value): void
    {
        if ($field === 'estado_boolean') {
            $nuevoEstado = $value ? 'activo' : 'inactivo';
            Servicio::find($id)?->update(['estado' => $nuevoEstado]);
        }

        $this->skipRender();
    }
}
