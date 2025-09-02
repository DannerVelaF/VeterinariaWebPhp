<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class UserTable extends PowerGridComponent
{
    public string $tableName = 'user-table-hkfcya-table';

    public function setUp(): array
    {
        $this->showCheckBox();

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
        return User::query()->with(['persona.trabajador.puestoTrabajo']);
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('username', fn($user) => $user->username)
            ->add('trabajador', fn($user) => $user->persona?->nombre)
            ->add('puesto', fn($user) => $user->persona?->trabajador?->puestoTrabajo?->nombre)
            ->add('ultimo_login', fn($user) => $user->ultimo_login ? Carbon::parse($user->ultimo_login)->format('d/m/Y H:i') : '-')
            ->add('estado')
            ->add('created_at')
            ->add('estado_boolean', function ($row) {
                return $row->estado === 'activo';
            });
    }


    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Username', 'username')
                ->sortable()
                ->searchable(),

            Column::make('Estado', 'estado')
                ->sortable()
                ->searchable(),

            Column::make('Trabajador', 'trabajador'),
            COlumn::make('Puesto', 'puesto'),

            Column::make('Ultimo login', 'ultimo_login'),

            Column::make('Fecha creaciòn', 'created_at')
                ->sortable()
                ->searchable(),
            Column::make('Estado', 'estado_boolean')
                ->sortable()
                ->searchable()
                ->toggleable(
                    trueLabel: 'activo',
                    falseLabel: 'inactivo'
                ),

        ];
    }

    public function onUpdatedEditable(string|int $id, string $field, string $value): void
    {
        User::find($id)->update([
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
            User::find($id)->update([
                'estado' => $nuevoEstado
            ]);
        }

        // Evitar que Livewire vuelva a renderizar el componente
        $this->skipRender();
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

    /*public function actions(User $row): array
    {
        return [
            Button::add('edit')
                ->slot('Edit: ' . $row->id)
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('edit', ['rowId' => $row->id])
        ];
    }*/

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
