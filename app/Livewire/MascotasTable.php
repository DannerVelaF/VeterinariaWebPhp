<?php

namespace App\Livewire;

use App\Models\Mascota;
use App\Models\Raza;
use App\Models\Cliente;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;


final class MascotasTable extends PowerGridComponent
{
    public string $tableName = 'mascota-table-abc456-table';
    public bool $showFilters = true;
    public string $primaryKey = 'id_mascota';
    public string $sortField = 'id_mascota';

    protected $listeners = ['mascotaRegistrada' => '$refresh', 'mascotaActualizada' => '$refresh'];

    /**
     * Configuración general del componente PowerGrid
     */
    public function setUp(): array
    {
        $this->showCheckBox();
        return [
            PowerGrid::header()
                ->showSearchInput(), // 🔍 Búsqueda general
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(), // 📊 Paginación y total
        ];
    }

    /**
     * Fuente de datos principal
     */
    public function datasource(): Builder
    {
        return Mascota::query()
            ->with(['cliente', 'raza.especie']) // ✅ incluye relaciones
            ->select('mascotas.*');
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id_mascota')
            ->add('nombre_mascota')
            ->add('id_cliente')
            ->add('cliente_nombre', fn($mascota) => $mascota->cliente?->nombre_cliente ?? '-')
            ->add('id_raza')
            ->add('raza_nombre', fn($mascota) => $mascota->raza?->nombre_raza ?? '-')
            ->add('raza_especie_nombre', fn($mascota) => $mascota->raza?->especie?->nombre_especie ?? '-')
            ->add('fecha_nacimiento')
            ->add('sexo')
            ->add('color_primario')
            ->add('peso_actual')
            ->add('observacion');
    }

    /**
     * Columnas visibles en la tabla
     */
    public function columns(): array
    {
        return [
            Column::make('ID', 'id_mascota')
                ->sortable()
                ->searchable(),

            Column::make('Nombre de Mascota', 'nombre_mascota')
                ->sortable()
                ->searchable()
                ->editOnClick(),

            Column::make('Cliente', 'cliente.nombre_cliente', 'id_cliente')
                ->sortable()
                ->searchable(),

            Column::make('Raza', 'raza.nombre_raza', 'id_raza')
                ->sortable()
                ->searchable(),

            Column::make('Especie', 'raza.especie.nombre_especie')
                ->sortable()
                ->searchable(),

            Column::make('Fecha Nacimiento', 'fecha_nacimiento')
                ->sortable()
                ->searchable(),

            Column::make('Sexo', 'sexo')
                ->sortable()
                ->searchable(),

            Column::make('Color Primario', 'color_primario')
                ->sortable()
                ->searchable(),

            Column::make('Peso Actual', 'peso_actual')
                ->sortable()
                ->searchable(),

            Column::make('Observación', 'observacion')
                ->searchable(),

            Column::make('Fecha de Registro', 'fecha_registro')
                ->sortable()
                ->searchable(),

            // ✅ Columna de botones (acciones)
            Column::action('Acciones'),
        ];
    }


    public function filters(): array
    {
        return [];
    }
    
    #[\Livewire\Attributes\On('edit')]
    public function Edit(Mascota $row): void
    {
        $this->jd('alert(' . $row->id_mascota . ')');   
    }

    public function actions(Mascota $row): array
    {
        return [
            Button::add('editar')
                ->slot('<i class="fa-solid fa-pen text-blue-600"></i>')
                ->id('editar-' . $row->id_mascota)
                ->class('px-2 py-1 rounded-md hover:bg-blue-100')
                ->dispatch('editarMascota', ['mascotaId' => $row->id_mascota]),
        ];
    }

    public function onUpdatedEditable(string|int $id, string $field, string $value): void
    {
        Mascota::find($id)->update([
            $field => $value
        ]);
    }
}
