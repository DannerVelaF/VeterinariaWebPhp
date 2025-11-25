<?php

namespace App\Livewire;

use App\Models\Clientes;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class ClientesTable extends PowerGridComponent
{
    public string $tableName = 'clientes-table-aepcjk-table';
    public string $primaryKey = 'id_cliente';
    public string $sortField = 'id_cliente';

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
        return Clientes::query()->orderBy("fecha_registro", "DESC");
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add("numero_documento", fn($cliente) => $cliente->persona->numero_documento)
            ->add('nombre', fn($cliente) => $cliente->persona->nombre . ' ' . $cliente->persona->apellido_paterno . ' ' . $cliente->persona->apellido_materno)
            ->add("correo_personal", fn($cliente) => $cliente->persona->correo_electronico_personal)
            ->add("telefono_personal", fn($cliente) => $cliente->persona->numero_telefono_personal)
            ->add('fecha_registro');
    }

    public function columns(): array
    {
        return [
            Column::make('Número documento', 'numero_documento')
                ->sortable(),
            Column::make('Nombre', 'nombre'),
            Column::make('Correo personal', 'correo_personal'),
            Column::make('Telefono personal', 'telefono_personal'),
            Column::make('Fecha registro', 'fecha_registro')
                ->sortable()
                ->searchable(),


            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('nombre', 'Nombre')
                ->builder(function (Builder $query, array $value) {
                    $search = trim($value['value']);

                    if ($search === '') {
                        return $query;
                    }

                    return $query->whereHas('persona', function ($q) use ($search) {
                        $q->where(DB::raw("CONCAT(nombre, ' ', apellido_paterno, ' ', apellido_materno)"), 'like', "%{$search}%")
                            ->orWhere('nombre', 'like', "%{$search}%")
                            ->orWhere('apellido_paterno', 'like', "%{$search}%")
                            ->orWhere('apellido_materno', 'like', "%{$search}%");
                    });
                }),
            Filter::inputText('correo_personal', 'Correo personal')
                ->builder(function (Builder $query, array $value) {
                    $search = trim($value['value']);

                    if ($search === '') {
                        return $query;
                    }

                    return $query->whereHas('persona', function ($q) use ($search) {
                        $q->where('correo_personal', 'like', "%{$search}%");
                    });
                }),
            Filter::inputText('telefono_personal', 'Telefono personal')
                ->builder(function (Builder $query, array $value) {
                    $search = trim($value['value']);

                    if ($search === '') {
                        return $query;
                    }

                    return $query->whereHas('persona', function ($q) use ($search) {
                        $q->where('numero_telefono_personal', 'like', "%{$search}%");
                    });
                }),
            Filter::inputText('numero_documento', 'Número documento')
                ->builder(function (Builder $query, array $value) {
                    $search = trim($value['value']);

                    if ($search === '') {
                        return $query;
                    }

                    return $query->whereHas('persona', function ($q) use ($search) {
                        $q->where('numero_documento', 'like', "%{$search}%");
                    });
                }),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    public function actions(Clientes $row): array
    {
        return [
            Button::add('edit')
                ->slot("Editar")
                ->class('bg-blue-500 text-white px-3 py-2 m-1 rounded cursor-pointer text-sm')
                ->dispatch('abrirModalCliente', ['clienteId' => $row->id_cliente])
        ];
    }

    /*
    public function actionRules($row): array
    {
       return [
            // Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($row) => $row->id === 1)
                ->hide(),
        ];
    }
    */
}
