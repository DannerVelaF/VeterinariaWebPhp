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
    protected $listeners = ['userUpdated' => '$refresh'];
    public string $primaryKey = 'id_usuario';
    public string $sortField = 'id_usuario';
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
            ->add('usuario')
            ->add('trabajador', fn($user) => $user->persona?->nombre)
            ->add('puesto', fn($user) => $user->persona?->trabajador?->puestoTrabajo?->nombre_puesto)
            ->add('ultimo_login', fn($user) => $user->ultimo_login ? Carbon::parse($user->ultimo_login)->format('d/m/Y H:i') : '-')
            ->add('estado')
            ->add('fecha_registro')
            ->add('roles', fn($user) => $user->rol?->nombre_rol ?? '')
            ->add('estado_boolean', function ($row) {
                return $row->estado === 'activo';
            });
    }


    public function columns(): array
    {
        return [
            Column::make('Id', 'id_usuario'),
            Column::make('Usuario', 'usuario')
                ->sortable()
                ->searchable(),

            Column::make('Estado', 'estado')
                ->sortable()
                ->searchable(),

            Column::make('Trabajador', 'trabajador'),
            COlumn::make('Puesto', 'puesto'),

            Column::make('Ultimo login', 'ultimo_login'),

            Column::make('Fecha de registro', 'fecha_registro')
                ->sortable()
                ->searchable(),
            Column::make('Roles', 'roles'),
            Column::action('Acciones')
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

    public function actions(User $row): array
    {
        return [
            Button::add('cambiar-rol')
                ->slot('Editar') // <-- aquí defines el texto
                ->class('px-2 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs rounded')
                ->dispatch('abrirModalRol', ['userId' => $row->id_usuario])
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
