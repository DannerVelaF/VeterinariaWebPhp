<?php

namespace App\Livewire;

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
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use Illuminate\Support\Str;

final class ProveedorTable extends PowerGridComponent
{
    public string $tableName = 'proveedor-table-dedggx-table';
    use WithExport;
    protected $listeners = ['proveedorRegistrado' => '$refresh'];

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::header()
                ->showSearchInput(),

            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
            PowerGrid::exportable(fileName: 'DetalleProveedores')
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV)
        ];
    }

    public function datasource(): Builder
    {
        return Proveedor::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('nombre')
            ->add('ruc')
            ->add('telefono_contacto')
            ->add('correo_electronico_empresa')
            ->add('pais', fn($proveedor) => Str::ucfirst(Str::lower($proveedor->pais)))
            ->add('fecha_registro')
            ->add("estado")
            ->add('estado_boolean', function ($row) {
                return $row->estado === 'activo';
            });
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Nombre', 'nombre')
                ->sortable()
                ->searchable(),

            Column::make('Ruc', 'ruc')
                ->sortable()
                ->searchable()
                ->editOnClick(),

            Column::make('Telefono', 'telefono_contacto')
                ->sortable()
                ->searchable()
                ->editOnClick(),

            Column::make('Correo', 'correo_electronico_empresa')
                ->sortable()
                ->searchable()
                ->editOnClick(),

            Column::make('Pais', 'pais')
                ->sortable()
                ->searchable(),
            Column::make('Fecha Registro', 'fecha_registro')
                ->sortable(),
            Column::make('Estado', 'estado_boolean')
                ->sortable()
                ->searchable()
                ->toggleable(
                    trueLabel: 'activo',
                    falseLabel: 'inactivo'
                ),

            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    public function actions(Proveedor $row): array
    {
        return [
            Button::add('edit')
                ->slot('Edit: ' . $row->id)
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('edit', ['rowId' => $row->id])
        ];
    }


    public function onUpdatedEditable(string|int $id, string $field, string $value): void
    {
        Proveedor::find($id)->update([
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
            Proveedor::find($id)->update([
                'estado' => $nuevoEstado
            ]);
        }

        // Evitar que Livewire vuelva a renderizar el componente
        $this->skipRender();
    }
}
