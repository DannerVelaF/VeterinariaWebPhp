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

    public string $tipoUsuario = 'sistema'; // 'sistema' o 'clientes'

    // Usar boot en lugar de mount para parámetros
    public function boot(string $tipoUsuario = 'sistema'): void
    {
        $this->tipoUsuario = $tipoUsuario;
        $this->tableName = "user-table-{$tipoUsuario}-" . uniqid();
    }

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
        $query = User::query()->with(['persona.trabajador.puestoTrabajo', 'persona.cliente']);

        // 🔍 FILTRAR POR TIPO DE USUARIO
        if ($this->tipoUsuario === 'sistema') {
            $query->whereHas('persona.trabajador');
        } elseif ($this->tipoUsuario === 'clientes') {
            $query->whereHas('persona.cliente')
                ->whereDoesntHave('persona.trabajador'); // Excluir trabajadores
        }

        return $query;
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id_usuario')
            ->add('usuario')
            ->add('trabajador', fn($user) => $user->persona?->nombre_completo ??
                $user->persona?->nombre . ' ' . $user->persona?->apellido_paterno . ' ' . ($user->persona?->apellido_materno ?? '')
            )
            ->add('puesto', fn($user) => $user->persona?->trabajador?->puestoTrabajo?->nombre_puesto ?? 'Cliente')
            ->add('tipo_usuario', fn($user) => $user->persona?->trabajador ? 'Trabajador' : 'Cliente')
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
        $baseColumns = [
            Column::make('Id', 'id_usuario'),
            Column::make('Usuario', 'usuario')
                ->sortable()
                ->searchable(),

            Column::make('Estado', 'estado')
                ->sortable()
                ->searchable(),

            Column::make('Persona', 'trabajador')
                ->sortable()
                ->searchable(),

            Column::make('Tipo', 'tipo_usuario'),

            Column::make('Ultimo login', 'ultimo_login'),

            Column::make('Fecha de registro', 'fecha_registro')
                ->sortable()
                ->searchable(),
        ];

        // 🔄 COLUMNAS CONDICIONALES
        if ($this->tipoUsuario === 'sistema') {
            $baseColumns[] = Column::make('Puesto', 'puesto');
            $baseColumns[] = Column::make('Roles', 'roles');
        }

        $baseColumns[] = Column::action('Acciones');

        return $baseColumns;
    }

    public function onUpdatedEditable(string|int $id, string $field, string $value): void
    {
        User::find($id)->update([
            $field => $value
        ]);
    }

    public function onUpdatedToggleable(string|int $id, string $field, string $value): void
    {
        if ($field === 'estado_boolean') {
            $nuevoEstado = $value ? 'activo' : 'inactivo';
            User::find($id)->update(['estado' => $nuevoEstado]);
        }
        $this->skipRender();
    }

    public function filters(): array
    {
        return [];
    }

    public function actions(User $row): array
    {
        $actions = [];

        // 🔄 ACCIONES CONDICIONALES
        if ($this->tipoUsuario === 'sistema') {
            $actions[] = Button::add('cambiar-rol')
                ->slot('Editar Rol')
                ->class('px-2 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs rounded')
                ->dispatch('abrirModalRol', ['userId' => $row->id_usuario]);
        }

        // Acción común para ambos tipos
        $actions[] = Button::add('editar')
            ->slot('Editar')
            ->class('px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded')
            ->dispatch('abrirModalUsuario', [
                'userId' => $row->id_usuario,
                'tipoUsuario' => $this->tipoUsuario
            ]);

        return $actions;
    }
}
