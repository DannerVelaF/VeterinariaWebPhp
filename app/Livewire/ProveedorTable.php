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
    public string $primaryKey = 'id_proveedor';
    public string $sortField = 'id_proveedor';

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
        return Proveedor::query()->orderBy('id_proveedor', 'asc');
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id_proveedor')
            ->add('nombre_proveedor')
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
            Column::make('Id', 'id_proveedor'),
            Column::make('Nombre', 'nombre_proveedor')
                ->sortable()
                ->searchable(),

            Column::make('Ruc', 'ruc')
                ->sortable()
                ->searchable(),

            Column::make('Telefono', 'telefono_contacto')
                ->sortable()
                ->searchable(),

            Column::make('Correo', 'correo_electronico_empresa')
                ->sortable()
                ->searchable(),

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
            Button::add('editarProveedor')
                ->slot('Editar') // <-- aquí defines el texto
                ->class('px-2 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs rounded')
                ->dispatch('editarProveedor', ['proveedorId' => $row->id_proveedor])
        ];
    }


    public function onUpdatedEditable(string|int $id_proveedor, string $field, string $value): void
    {
        Proveedor::find($id_proveedor)->update([
            $field => $value
        ]);
    }

    public function onUpdatedToggleable(string|int $id_proveedor, string $field, string $value): void
    {
        // Solo procesar si el campo es estado_boolean
        if ($field === 'estado_boolean') {
            // Convertir el valor boolean a string
            $nuevoEstado = $value ? 'activo' : 'inactivo';

            // Actualizar el campo real 'estado' en la base de datos
            Proveedor::find($id_proveedor)->update([
                'estado' => $nuevoEstado
            ]);
        }

        // Evitar que Livewire vuelva a renderizar el componente
        $this->skipRender();
    }
}
