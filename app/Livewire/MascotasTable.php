<?php

namespace App\Livewire;

use App\Models\Mascota;
use App\Models\Raza;
use App\Models\Cliente;
use App\Models\Especie;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class MascotasTable extends PowerGridComponent
{
    public string $tableName = 'mascotas-table';
    protected $listeners = ['mascotaRegistrada' => '$refresh'];
    public string $primaryKey = 'id_mascota';
    public string $sortField = 'id_mascota';

    public array $especies = [];
    public array $razas = [];


    public function setUp(): array
    {
        $this->especies = Especie::select('id_especie as id', 'nombre_especie as name')->get()->toArray();
        $this->razas = Raza::select('id_raza as id', 'nombre_raza as name')->get()->toArray();
        return [
            PowerGrid::header(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Mascota::query()->with(['cliente', 'raza']);
    }

    public function relationSearch(): array
    {
        return [
            'cliente' => ['nombre_cliente'],
            'raza' => ['nombre_raza'],
            'especie' => ['nombre_especie'],
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id_mascota')
            ->add('nombre_mascota')
            ->add('fecha_nacimiento')
            ->add('sexo')
            ->add('color_primario', fn($mascota) => $mascota->color?->nombre_color ?? '-')
            ->add('peso_actual')
            ->add('observacion')
            ->add('estado')
            ->add('estado_boolean', fn($row) => $row->estado === 'activo')
            ->add('fecha_registro')
            ->add('cliente_nombre', fn($mascota) => $mascota->cliente?->persona->nombre . ' ' . $mascota->cliente->persona->apellido_paterno . ' ' . $mascota->cliente->persona->apellido_materno ?? '-')
            ->add('raza_nombre', fn($mascota) => $mascota->raza?->nombre_raza ?? '-')
            ->add('especie_nombre', fn($mascota) => $mascota->raza?->especie->nombre_especie ?? '-');
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id_mascota')->sortable(),

            Column::make('Nombre', 'nombre_mascota')
                ->sortable()
                ->searchable(),

            Column::make('Cliente', 'cliente_nombre')
                ->sortable(),

            Column::make('Raza', 'raza_nombre')
                ->sortable(),
            Column::make('Especie', 'especie_nombre')
                ->sortable(),

            Column::make('Sexo', 'sexo')
                ->sortable(),

            Column::make('Color', 'color_primario')
                ->sortable()
                ->searchable(),

            Column::make('Peso (kg)', 'peso_actual')
                ->sortable(),

            Column::make('Fecha Nacimiento', 'fecha_nacimiento')
                ->sortable(),


            Column::make('Fecha Registro', 'fecha_registro')
                ->sortable(),

            Column::action('Acciones')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('nombre_mascota', 'Nombre'),
            Filter::select('estado', 'Estado')
                ->dataSource([
                    ['id' => 'activo', 'name' => 'activo'],
                    ['id' => 'inactivo', 'name' => 'inactivo'],
                ])
                ->optionValue('id')
                ->optionLabel('name'),
            Filter::select("sexo", "Sexo")
                ->dataSource([
                    ['id' => 'M', 'name' => 'Macho'],
                    ['id' => 'F', 'name' => 'Hembra'],
                ])
                ->optionValue('id')
                ->optionLabel('name'),

        ];
    }

    public function actions(Mascota $row): array
    {
        return [
            Button::add('editarMascota')
                ->slot('Editar')
                ->class('px-2 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs rounded')
                ->dispatch('abrirModalMascota', ['mascotaId' => $row->id_mascota]),
        ];
    }

    /* public function onUpdatedEditable(string|int $id, string $field, string $value): void
    {
        Mascota::find($id)?->update([$field => $value]);
    }

    public function onUpdatedToggleable(string|int $id, string $field, string $value): void
    {
        if ($field === 'estado_boolean') {
            $nuevoEstado = $value ? 'activo' : 'inactivo';
            Mascota::find($id)?->update(['estado' => $nuevoEstado]);
        }

        $this->skipRender();
    } */

    /**
     * Actualiza campos editables directamente en la tabla
     */
    public function onUpdatedEditable(string|int $id, string $field, string $value): void
    {
        Mascota::find($id)?->update([$field => $value]);
        $this->dispatch('mascotaUpdated');
    }

    /**
     * Cambia el estado (activo/inactivo) con toggle
     */
    public function onUpdatedToggleable(string|int $id, string $field, string $value): void
    {
        if ($field === 'estado_boolean') {
            $nuevoEstado = $value ? 'activo' : 'inactivo';
            Mascota::find($id)?->update(['estado' => $nuevoEstado]);
        }

        $this->dispatch('mascotaUpdated');
        $this->skipRender();
    }
}
