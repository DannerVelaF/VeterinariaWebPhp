<?php

namespace App\Livewire;

use App\Models\EstadoTrabajadores;
use App\Models\Trabajador;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class TrabajadorTable extends PowerGridComponent
{
    public string $tableName = 'trabajador-table-w4ssvw-table';
    public array $estados = [];

    public function setUp(): array
    {
        $this->showCheckBox();

        $this->estados = EstadoTrabajadores::pluck('nombre', 'id')->toArray();

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
        // Importante: cargamos relaciones
        return Trabajador::query()
            ->with(['persona', 'puestoTrabajo', 'estadoTrabajador']);
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('documento', fn($trabajador) => $trabajador->persona?->numero_documento)
            ->add(
                'nombre_completo',
                fn($trabajador) =>
                $trabajador->persona
                    ? $trabajador->persona->nombre . ' ' .
                    $trabajador->persona->apellido_paterno . ' ' .
                    $trabajador->persona->apellido_materno
                    : '-'
            )
            ->add('puesto_nombre', fn($trabajador) => $trabajador->puestoTrabajo?->nombre)
            ->add('estado_nombre', fn($trabajador) => $trabajador->estadoTrabajador?->nombre)
            ->add(
                'fecha_ingreso_formatted',
                fn(Trabajador $model) =>
                Carbon::parse($model->fecha_ingreso)->format('d/m/Y')
            )
            ->add(
                'fecha_salida_formatted',
                fn(Trabajador $model) =>
                $model->fecha_salida ? Carbon::parse($model->fecha_salida)->format('d/m/Y') : '-'
            )
            ->add('salario')
            ->add('numero_seguro_social')
            ->add(
                'created_at_formatted',
                fn($model) =>
                Carbon::parse($model->created_at)->format('d/m/Y H:i')
            );
    }

    public function columns(): array
    {
        return [
            Column::make('Documento', 'documento')
                ->searchable(),

            Column::make('Nombre completo', 'nombre_completo')
                ->sortable()
                ->searchable()
                ->editOnClick(),

            Column::make('Puesto', 'puesto_nombre', 'puestoTrabajo.nombre')
                ->sortable()
                ->searchable()
                ->editOnClick(),

            Column::make('Estado', 'estado_nombre', 'estadoTrabajador.nombre')
                ->sortable()
                ->searchable()
                ->editOnClick(),

            Column::make('Fecha ingreso', 'fecha_ingreso_formatted', 'fecha_ingreso')
                ->sortable(),

            Column::make('Fecha salida', 'fecha_salida_formatted', 'fecha_salida')
                ->sortable(),

            Column::make('Salario', 'salario')
                ->sortable()
                ->searchable()
                ->editOnClick(),

            Column::make('N° Seguro Social', 'numero_seguro_social')
                ->sortable()
                ->searchable()
                ->editOnClick(),

            Column::make('Fecha creación', 'created_at_formatted', 'created_at')
                ->sortable(),


            Column::action('Acciones')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::datepicker('fecha_ingreso'),
            Filter::datepicker('fecha_salida'),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    public function actions(Trabajador $row): array
    {
        return [
            Button::add('edit')
                ->slot('Editar')
                ->id()
                ->class('bg-blue-500 text-white px-2 py-1 rounded')
                ->dispatch('edit', ['rowId' => $row->id])
        ];
    }
    public function onUpdatedEditable(string|int $id, string $field, string $value): void
    {
        Trabajador::find($id)->update([
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
            Trabajador::find($id)->update([
                'estado' => $nuevoEstado
            ]);
        }

        // Evitar que Livewire vuelva a renderizar el componente
        $this->skipRender();
    }
}
