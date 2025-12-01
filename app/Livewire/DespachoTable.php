<?php

namespace App\Livewire;

use App\Models\EnvioPedido;
use App\Models\EstadoEnvioPedido;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class DespachoTable extends PowerGridComponent
{
    public string $tableName = 'despacho-table-treagb-table';
    public string $primaryKey = 'id_envio_pedido';
    public string $sortField = 'id_envio_pedido';
    public string $sortDirection = 'desc';

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

    public function header(): array
    {
        return [
            Button::add('asignar-masivo')
                ->slot('<svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg> Asignar Ruta Masiva')
                ->class('pg-btn-white dark:bg-indigo-600 dark:text-white dark:hover:bg-indigo-700 flex items-center font-bold cursor-pointer')

                // CAMBIO IMPORTANTE: Usamos wire:click para llamar a un método local primero
                ->dispatch('evaluarSeleccionMasiva', [])
        ];
    }

    #[\Livewire\Attributes\On('evaluarSeleccionMasiva')]
    public function evaluarSeleccionMasiva()
    {
        // $this->checkboxValues contiene los IDs seleccionados (propiedad nativa de PowerGrid)
        $ids = $this->checkboxValues;

        if (count($ids) === 0) {
            // Si no hay nada seleccionado, mostramos alerta
            $this->dispatch('notify', title: 'Atención', description: 'Debes seleccionar al menos un pedido.', type: 'warning');
            return;
        }

        // Enviamos los IDs al componente PADRE (Despacho)
        // Asegúrate de que la ruta del componente sea correcta ('inventario.despacho')
        $this->dispatch('iniciarAsignacionMasiva', ids: $ids)->to('inventario.despacho');
    }

    public function datasource(): Builder
    {
        return EnvioPedido::query()
            ->with([
                'venta.cliente',
                'direccion',
                'estadoEnvio',
                'trabajador.persona'
            ])
            // FILTRO CLAVE: Solo mostrar pedidos WEB que estén pendientes
            // Asumiendo que 'web' es un tipo de venta en la tabla ventas
            ->whereHas('venta', function ($q) {
                $q->where('tipo_venta', 'web'); // Ajusta según tu base de datos
            });
    }

    public function relationSearch(): array
    {
        // Permite buscar escribiendo el nombre del cliente
        return [
            'venta.cliente' => [
                'nombre_cliente',
                'apellido_cliente', // Ajusta según tus campos reales en Clientes
                'numero_documento'
            ],
            'trabajador.persona' => [ // Buscar por chofer
                'nombre',
                'apellido_paterno'
            ]
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id_envio_pedido')

            // 1. Nombre del Cliente (Concatenado o campo simple)
            ->add('cliente_nombre', function ($row) {
                return $row->venta->cliente->persona->nombre ?? 'Cliente Eliminado';
            })

            // 2. Dirección Completa
            ->add('direccion_full', function ($row) {
                if (!$row->direccion) return '<span class="text-red-500">Sin dirección</span>';

                // Ajusta los campos según tu tabla 'direcciones'
                return $row->direccion->calle . ' #' . $row->direccion->numero .
                    ($row->direccion->urbanizacion ? ' - ' . $row->direccion->urbanizacion : '');
            })

            // 3. Transportista (Con manejo de nulos)
            ->add('transportista_nombre', function ($row) {
                if (!$row->trabajador) {
                    return '<span class="text-gray-400 italic">-- Sin Asignar --</span>';
                }
                return $row->trabajador->persona->nombre . ' ' . $row->trabajador->persona->apellido_paterno;
            })

            // 4. Badge de Estado con Colores
            ->add('estado_badge', function ($row) {
                $estado = strtolower($row->estadoEnvio->nombre_estado_envio_pedido ?? '');

                $colors = [
                    'pendiente' => 'bg-gray-100 text-gray-800 border-gray-200',
                    'asignado' => 'bg-blue-100 text-blue-800 border-blue-200',
                    'en_ruta' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                    'entregado' => 'bg-green-100 text-green-800 border-green-200',
                    'fallido' => 'bg-red-100 text-red-800 border-red-200',
                ];

                $clase = $colors[$estado] ?? 'bg-gray-50 text-gray-500';

                return "<span class='px-2 py-1 rounded-full text-xs font-bold border {$clase}'>"
                    . ucfirst($estado) . "</span>";
            })
            ->add('fecha_programada_formatted', fn(EnvioPedido $model) => $model->fecha_programada ? Carbon::parse($model->fecha_programada)->format('d/m/Y H:i') : '--')
            ->add('fecha_entrega_real_formatted', fn(EnvioPedido $model) => $model->fecha_entrega_real ? Carbon::parse($model->fecha_entrega_real)->format('d/m/Y H:i') : '--');
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id_envio_pedido')->sortable(),

            Column::make('Cliente', 'cliente_nombre'),

            Column::make('Dirección de Entrega', 'direccion_full')
                ->searchable(),

            Column::make('Transportista', 'transportista_nombre'),

            Column::make('Estado', 'estado_badge')
                ->sortable(),

            Column::make('Programado', 'fecha_programada_formatted', 'fecha_programada')
                ->sortable(),

            Column::make('Entregado', 'fecha_entrega_real_formatted', 'fecha_entrega_real')
                ->sortable(),

            Column::action('Acciones')
        ];
    }

    public function filters(): array
    {
        return [
            // Filtro por Estado (Dropdown)
            Filter::select('estado_badge', 'id_estado_envio_pedido')
                ->dataSource(EstadoEnvioPedido::all())
                ->optionValue('id_estado_envio_pedido')
                ->optionLabel('nombre_estado_envio_pedido'),

            // Filtro por Fechas
            Filter::datetimepicker('fecha_programada', 'fecha_programada'),
        ];
    }

    public function actions(EnvioPedido $row): array
    {
        return [
            Button::add('gestionar')
                ->slot('<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Gestionar')
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700 flex items-center')
                // ESTO ES CLAVE: Dispara el evento al componente PADRE para abrir el modal
                ->dispatch('abrirModalDespacho', ['id_envio' => $row->id_envio_pedido])
        ];
    }
}
