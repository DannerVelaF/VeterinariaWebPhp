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
    protected $listeners = ['trabajadoresUpdated' => '$refresh'];
    public array $estados = [];
    public string $primaryKey = 'id_trabajador';
    public string $sortField = 'id_trabajador';
    public function setUp(): array
    {
        $this->showCheckBox();

        $this->estados = EstadoTrabajadores::pluck('nombre_estado_trabajador', 'id_estado_trabajador')->toArray() ?? [];

        return [
            PowerGrid::header(),
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
            ->add('puesto_nombre', fn($trabajador) => $trabajador->puestoTrabajo?->nombre_puesto)
            ->add('estado_nombre', fn($trabajador) => $trabajador->estadoTrabajador?->nombre_estado_trabajador)
            ->add('fecha_ingreso')
            ->add('fecha_salida')
            ->add('salario')
            ->add('numero_seguro_social')
            ->add('fecha_registro');
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

            Column::make('Puesto', 'puesto_nombre')
                ->sortable()
                ->searchable()
                ->editOnClick(),

            Column::make('Estado', 'estado_nombre')
                ->sortable()
                ->searchable()
                ->editOnClick(),

            Column::make('Fecha ingreso', 'fecha_ingreso')
                ->sortable(),

            Column::make('Fecha salida',  'fecha_salida')
                ->sortable(),

            Column::make('Salario', 'salario')
                ->sortable()
                ->searchable()
                ->editOnClick(),

            Column::make('N° Seguro Social', 'numero_seguro_social')
                ->sortable()
                ->searchable()
                ->editOnClick(),

            Column::make('Fecha creación', 'fecha_registro')
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
            Button::add('editar-trabajador')
                ->slot('Editar')
                ->class('px-2 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs rounded')
                ->dispatch('abrirModalTrabajador', ['trabajadorId' => $row->id_trabajador])
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
