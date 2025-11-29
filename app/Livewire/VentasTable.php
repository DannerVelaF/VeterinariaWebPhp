<?php

namespace App\Livewire;

use App\Models\Caja;
use App\Models\Ventas;
use App\Models\Clientes;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use App\Models\EstadoVentas;

final class VentasTable extends PowerGridComponent
{
    public string $tableName = 'ventas-table';
    use WithExport;

    public bool $showFiltersButton = false;
    public string $primaryKey = 'id_venta';
    public string $sortField = 'id_venta';
    public $listeners = ['ventasUpdated' => '$refresh'];

    // Array para filtro de estados
    public array $estadosVenta = [];

    public function boot(): void
    {
        config(['livewire-power-grid.filter' => 'outside']);
    }

    public function setUp(): array
    {
        // Cargar estados de venta para el filtro
        $this->estadosVenta = EstadoVentas::select('id_estado_venta_fisica as id', 'nombre_estado_venta_fisica as name')
            ->get()
            ->toArray();

        // CORRECCIÓN: Agregar $ a la variable
        Log::info('Estados de venta cargados:', $this->estadosVenta);

        return [
            PowerGrid::header(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount()
        ];
    }

    public function datasource(): Builder
    {
        // 1. Base de la consulta (siempre mantenemos el select para evitar bugs)
        $query = Ventas::query()
            ->select('ventas.*')
            ->with(['cliente', 'trabajador.persona.user', 'estadoVenta']);

        $user = Auth::user();

        // 2. Verificamos si es trabajador
        if ($user && $user->persona && $user->persona->trabajador) {
            $idTrabajador = $user->persona->trabajador->id_trabajador;

            // Buscamos si tiene caja abierta
            $cajaAbierta = Caja::where('id_trabajador', $idTrabajador)
                ->where('estado', 'abierta')
                ->first();

            // 3. LÓGICA CONDICIONAL
            if ($cajaAbierta) {
                // ✅ CASO A: Hay caja abierta -> Filtramos SOLO las ventas de esa caja
                $query->where('ventas.id_caja', $cajaAbierta->id_caja);
            }

        }

        return $query->orderBy("ventas.fecha_registro", "desc");
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id_venta')
            ->add('fecha_venta_formatted', fn($venta) => Carbon::parse($venta->fecha_venta)->format('d/m/Y')
            )
            ->add('fecha_registro_formatted', fn($venta) => Carbon::parse($venta->fecha_registro)->format('d/m/Y H:i')
            )
            ->add('estado_badge', fn($venta) => $this->getEstadoBadge($venta->estadoVenta->nombre_estado_venta_fisica)
            )
            ->add('subtotal_formatted', fn($venta) => 'S/ ' . number_format($venta->subtotal, 2)
            )
            ->add('descuento_formatted', fn($venta) => 'S/ ' . number_format($venta->descuento, 2)
            )
            ->add('total_formatted', fn($venta) => 'S/ ' . number_format($venta->total, 2)
            )
            ->add('cliente_nombre', fn($venta) => $venta->cliente ? $venta->cliente->persona->nombre : 'N/A'
            )
            ->add('vendedor', fn($venta) => $venta->trabajador && $venta->trabajador->persona && $venta->trabajador->persona->user
                ? $venta->trabajador->persona->user->usuario
                : 'Automático'
            )
            ->add("codigo")
            ->add("tipo_venta", fn($venta) => Str::ucfirst(Str::lower($venta->tipo_venta)));
    }

    private function getEstadoBadge($estado): string
    {
        $badgeClasses = [
            'pendiente' => 'bg-yellow-100 text-yellow-800',
            'completado' => 'bg-green-100 text-green-800',
            'cancelado' => 'bg-red-100 text-red-800',
        ];

        $class = $badgeClasses[$estado] ?? 'bg-gray-100 text-gray-800';

        return '<span class="px-2 py-1 rounded-full text-xs font-medium capitalize ' . $class . '">' . $estado . '</span>';
    }

    public function columns(): array
    {
        return [
            Column::make('Codigo venta', 'codigo')
                ->sortable()
                ->searchable(),

            Column::make('Fecha Venta', 'fecha_venta_formatted')
                ->sortable(),

            Column::make('Fecha Registro', 'fecha_registro_formatted')
                ->sortable(),

            Column::make('Estado', 'estado_badge')
                ->sortable(),

            Column::make('Subtotal', 'subtotal_formatted')
                ->sortable(),

            Column::make('Descuento', 'descuento_formatted')
                ->sortable(),

            Column::make('Total', 'total_formatted')
                ->sortable(),

            Column::make('Cliente', 'cliente_nombre')
                ->sortable()
                ->searchable(),

            Column::make('Vendedor', 'vendedor')
                ->sortable(),

            Column::make("Tipo", "tipo_venta"),

            Column::action('Acciones')
        ];
    }

    public function filters(): array
    {
        return [
            // 🔹 FILTRO DE ESTADO - CORREGIDO
            Filter::select('estado', 'Estado')
                ->dataSource($this->estadosVenta)
                ->optionValue('id')
                ->optionLabel('name')
                ->builder(function (Builder $query, $value) {
                    if (!empty($value)) {
                        $query->where('id_estado_venta', $value);
                    }
                    return $query;
                }),

            // 🔹 FILTRO DE CLIENTE - CORREGIDO
            Filter::inputText('cliente_nombre', 'Nombre')
                ->builder(function (Builder $query, array $value) {
                    $search = trim($value['value']);

                    if ($search === '') {
                        return $query;
                    }

                    return $query->whereHas('cliente.persona', function ($q) use ($search) {
                        $q->where(DB::raw("CONCAT(nombre, ' ', apellido_paterno, ' ', apellido_materno)"), 'like', "%{$search}%")
                            ->orWhere('nombre', 'like', "%{$search}%")
                            ->orWhere('apellido_paterno', 'like', "%{$search}%")
                            ->orWhere('apellido_materno', 'like', "%{$search}%");
                    });
                }),

            // 🔹 FILTRO DE VENDEDOR/TRABAJADOR - NUEVO
            Filter::inputText('vendedor', 'Vendedor')
                ->builder(function (Builder $query, $value) {
                    $search = $value['value'] ?? '';
                    if (!empty($search)) {
                        $query->whereHas('trabajador.persona.user', function ($q) use ($search) {
                            $q->where('usuario', 'like', "%{$search}%");
                        });
                    }
                    return $query;
                }),

            Filter::datePicker('fecha_venta', 'fecha_venta')
                ->params([
                    'dateFormat' => 'Y-m-d',
                    'locale' => 'es',
                    'enableTime' => false,
                ]),

            Filter::select('tipo_venta', 'Tipo de venta')
                ->dataSource([
                    ['id' => 'presencial', 'name' => 'Presencial'],
                    ['id' => 'web', 'name' => 'Web'],
                ])
                ->optionValue('id')
                ->optionLabel('name')
                ->builder(function (Builder $query, $value) {
                    if (!empty($value)) {
                        $query->where('tipo_venta', $value);
                    }
                    return $query;
                }),
            Filter::datePicker('fecha_venta', 'fecha_venta_formatted')
                ->params([
                    'dateFormat' => 'Y-m-d',
                    'locale' => 'es',
                    'enableTime' => false,
                ]),
            Filter::inputText("codigo", "Código de venta"),
            Filter::select('estado_badge', 'ventas.id_estado_venta')
                ->dataSource(EstadoVentas::all()->toArray())
                ->optionValue('id_estado_venta_fisica')
                ->optionLabel('nombre_estado_venta_fisica')
            ,
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    public function actions(Ventas $row): array
    {
        $actions = [
            Button::add('ver')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye-icon lucide-eye"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg>')
                ->id()
                ->class('pg-btn-white dark:bg-pg-primary-700')
                ->dispatch('show-modal-venta', ['rowId' => $row->id_venta]),
        ];

        // Solo agregar botones de completar/cancelar para ventas presenciales pendientes
        if ($row->tipo_venta === 'presencial' && $row->estadoVenta->nombre_estado_venta_fisica === 'pendiente') {
            $actions[] = Button::add('completar')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="green" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check-icon lucide-check"><path d="M20 6 9 17l-5-5"/></svg>')
                ->id()
                ->class('pg-btn-white dark:bg-pg-primary-700')
                ->dispatch('completar-venta', ['rowId' => $row->id_venta]);

            $actions[] = Button::add('cancelar')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="red" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-x-icon lucide-circle-x"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/></svg>')
                ->id()
                ->class('pg-btn-white dark:bg-pg-primary-700')
                ->dispatch('cancelar-venta', ['rowId' => $row->id_venta]);
        }

        // Botón especial para revisar ventas web pendientes
        if ($row->tipo_venta === 'web' && $row->estadoVenta->nombre_estado_venta_fisica === 'pendiente') {
            $actions[] = Button::add('revisar-web')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="blue" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-credit-card"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>')
                ->id()
                ->class('pg-btn-white dark:bg-pg-primary-700 bg-blue-50 hover:bg-blue-100')
                ->dispatch('revisar-venta-web', ['rowId' => $row->id_venta]);
        }

        return $actions;
    }

    public function actionRules($row): array
    {
        return [
            // Ocultar botón completar si la venta ya está completada, cancelada o es web
            Rule::button('completar')
                ->when(fn($row) => in_array($row->estadoVenta->nombre_estado_venta_fisica, ['completado', 'cancelado']) || $row->tipo_venta === 'web')
                ->hide(),

            // Ocultar botón cancelar si la venta ya está cancelada, completada o es web
            Rule::button('cancelar')
                ->when(fn($row) => in_array($row->estadoVenta->nombre_estado_venta_fisica, ['completado', 'cancelado']) || $row->tipo_venta === 'web')
                ->hide(),

            // Ocultar botón revisar-web si no es web o ya no está pendiente
            Rule::button('revisar-web')
                ->when(fn($row) => $row->tipo_venta !== 'web' || $row->estadoVenta->nombre_estado_venta_fisica !== 'pendiente')
                ->hide(),

            // Mostrar solo el botón ver para ventas completadas o canceladas
            Rule::button('ver')
                ->when(fn($row) => in_array($row->estadoVenta->nombre_estado_venta_fisica, ['completado', 'cancelado']))
                ->setAttribute('class', 'pg-btn-white dark:bg-pg-primary-700'),
        ];
    }
}
