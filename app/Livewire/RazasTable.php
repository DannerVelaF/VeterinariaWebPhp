<?php

namespace App\Livewire;

use App\Models\Raza;
use App\Models\Especie;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class RazaTable extends PowerGridComponent
{
    public string $tableName = 'raza-table-xyz123-table';
    protected $listeners = ['razaRegistrada' => '$refresh', 'razaActualizada' => '$refresh'];
    public string $primaryKey = 'id_raza';
    public string $sortField = 'id_raza';

    /**
     * Configuración básica del componente
     */
    public function setUp(): array
    {
        $this->showCheckBox();
        return [
            PowerGrid::header()
                ->showSearchInput(), // 🔍 cuadro de búsqueda
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(), // 📄 paginación
        ];
    }

    /**
     * Fuente de datos
     */
    public function datasource(): Builder
    {
        return Raza::query()
            ->with('especie') // ✅ incluye relación
            ->select('razas.*');
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id_raza')
            ->add('nombre_raza')
            ->add('descripcion')
            ->add('id_especie')
            ->add('especie_nombre', fn($raza) => $raza->especie?->nombre_especie ?? '-')
            ->add('fecha_registro')
            ->add('fecha_actualizacion');
    }

    /**
     * Columnas disponibles para la tabla
     */
    public function columns(): array
    {
        return [
            Column::make('ID', 'id_raza')
                ->sortable()
                ->searchable(),

            Column::make('Nombre de raza', 'nombre_raza')
                ->sortable()
                ->searchable()
                ->editOnClick(),

            Column::make('Descripción', 'descripcion')
                ->sortable()
                ->searchable()
                ->editOnClick(),

            Column::make('Especie', 'especie.nombre_especie', 'id_especie')
                ->sortable()
                ->searchable(),

            Column::make('Fecha de registro', 'fecha_registro')
                ->sortable()
                ->searchable(),

            Column::make('Última actualización', 'fecha_actualizacion')
                ->sortable()
                ->searchable(),

            // ✅ columna para los botones de acción
            Column::action('Acciones'),
        ];
    }

    public function filters(): array
    {
        return [];
    }

    #[\Livewire\Attributes\On('edit')]
    public function Edit(Raza $row): void
    {
        $this->jd('alert(' . $row->id_raza . ')');
    }
    /**
     * Acciones por fila (editar / eliminar)
     */
    public function actions(Raza $row): array
    {
        return [
            Button::add('editar')
                ->slot('<i class="fa-solid fa-pen text-blue-600"></i>')
                ->id('editar-' . $row->id_raza)
                ->class('px-2 py-1 rounded-md hover:bg-blue-100')
                ->dispatch('editarRaza', ['razaId' => $row->id_raza]),
        ];
    }

    /**
     * Actualización en línea (si habilitas editOnClick)
     */
    public function onUpdatedEditable(string|int $id, string $field, string $value): void
    {
        Raza::find($id)->update([
            $field => $value
        ]);
    }

}
