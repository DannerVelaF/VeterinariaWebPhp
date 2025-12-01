<?php

namespace App\Livewire;

use App\Models\Caja;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class CajaTable extends PowerGridComponent
{
    use WithExport;

    public string $tableName = 'caja-reporte-table';
    public string $primaryKey = 'id_caja';
    public string $sortField = 'id_caja';
    public string $sortDirection = 'desc';

    public function setUp(): array
    {
        return [
            PowerGrid::header()
                ->showToggleColumns(),

            PowerGrid::footer()
                ->showPerPage(25)
                ->showRecordCount(mode: 'full')
                ->includeViewOnTop('components.caja-summary'),

        ];
    }

    public function datasource(): Builder
    {
        return Caja::query()
            ->with(['trabajador.persona']);
    }

    public function relationSearch(): array
    {
        return [
            'trabajador.persona' => [
                'nombre',
                'apellido_paterno',
                'apellido_materno',
                'numero_documento'
            ],
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('trabajador_nombre', function ($caja) {
                $persona = $caja->trabajador?->persona;
                if ($persona) {
                    return $persona->nombre . ' ' . $persona->apellido_paterno . ' ' . $persona->apellido_materno;
                }
                return 'N/A';
            })
            ->add('documento_trabajador', function ($caja) {
                return $caja->trabajador?->persona?->numero_documento ?? 'N/A';
            })
            ->add('monto_inicial_formatted', function ($caja) {
                return 'S/ ' . number_format($caja->monto_inicial, 2);
            })
            ->add('monto_final_formatted', function ($caja) {
                return $caja->monto_final ? 'S/ ' . number_format($caja->monto_final, 2) : '---';
            })
            ->add('ventas_efectivo_formatted', function ($caja) {
                return 'S/ ' . number_format($caja->ventas_efectivo, 2);
            })
            ->add('ventas_tarjeta_formatted', function ($caja) {
                return 'S/ ' . number_format($caja->ventas_tarjeta, 2);
            })
            ->add('ventas_transferencia_formatted', function ($caja) {
                return 'S/ ' . number_format($caja->ventas_transferencia, 2);
            })
            ->add('ventas_digital_formatted', function ($caja) {
                return 'S/ ' . number_format($caja->ventas_digital, 2);
            })
            ->add('total_ventas_formatted', function ($caja) {
                return 'S/ ' . number_format($caja->total_ventas, 2);
            })
            ->add('diferencia_formatted', function ($caja) {
                $diferencia = $caja->diferencia ?? 0;
                $color = $diferencia > 0 ? 'text-green-600' : ($diferencia < 0 ? 'text-red-600' : 'text-gray-600');
                $icon = $diferencia > 0 ? '🔼' : ($diferencia < 0 ? '🔽' : '⚫');
                return "<span class='{$color} font-bold'>{$icon} S/ " . number_format(abs($diferencia), 2) . "</span>";
            })
            ->add('estado_badge', function ($caja) {
                $badge = $caja->estado === 'abierta'
                    ? '<span class="px-2 py-1 text-xs font-bold bg-green-100 text-green-800 rounded-full border border-green-200">🟢 ABIERTA</span>'
                    : '<span class="px-2 py-1 text-xs font-bold bg-gray-100 text-gray-800 rounded-full border border-gray-200">🔒 CERRADA</span>';
                return $badge;
            })
            ->add('fecha_apertura_formatted', function ($caja) {
                return $caja->fecha_apertura?->format('d/m/Y H:i') ?? 'N/A';
            })
            ->add('fecha_cierre_formatted', function ($caja) {
                return $caja->fecha_cierre
                    ? $caja->fecha_cierre->format('d/m/Y H:i')
                    : '---';
            })
            ->add('duracion', function ($caja) {
                if (!$caja->fecha_cierre) return 'En curso';

                $inicio = Carbon::parse($caja->fecha_apertura);
                $fin = Carbon::parse($caja->fecha_cierre);
                $duracion = $inicio->diff($fin);

                return $duracion->h . 'h ' . $duracion->i . 'm';
            })
            ->add('total_esperado_formatted', function ($caja) {
                $totalEsperado = $caja->monto_inicial + $caja->total_ventas;
                return 'S/ ' . number_format($totalEsperado, 2);
            });
    }

    public function columns(): array
    {
        return [
            Column::make('Trabajador', 'trabajador_nombre')
                ->sortable()
                ->searchable()
                ->placeholder('Buscar trabajador...'),

            Column::make('Documento', 'documento_trabajador')
                ->sortable()
                ->searchable()
                ->placeholder('Buscar documento...'),

            Column::make('Estado', 'estado_badge')
                ->sortable(),

            Column::make('Monto Inicial', 'monto_inicial_formatted')
                ->sortable(),

            Column::make('Total Ventas', 'total_ventas_formatted')
                ->sortable(),

            Column::make('Total Esperado', 'total_esperado_formatted')
                ->sortable(),

            Column::make('Monto Final', 'monto_final_formatted')
                ->sortable(),

            Column::make('Diferencia', 'diferencia_formatted')
                ->sortable(),

            Column::make('Efectivo', 'ventas_efectivo_formatted')
                ->sortable(),

            Column::make('Tarjeta', 'ventas_tarjeta_formatted')
                ->sortable(),

            Column::make('Transferencia', 'ventas_transferencia_formatted')
                ->sortable(),

            Column::make('Digital', 'ventas_digital_formatted')
                ->sortable(),

            Column::make('Apertura', 'fecha_apertura_formatted')
                ->sortable(),

            Column::make('Cierre', 'fecha_cierre_formatted')
                ->sortable(),

            Column::make('Duración', 'duracion')
                ->sortable(),

            Column::action('Acciones')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::select('estado', 'estado')
                ->dataSource([
                    ['id' => 'abierta', 'name' => '🟢 Abierta'],
                    ['id' => 'cerrada', 'name' => '🔒 Cerrada'],
                ])
                ->optionValue('id')
                ->optionLabel('name'),

            Filter::datetimepicker('fecha_apertura', 'fecha_apertura')
                ->params([
                    'timezone' => 'America/Lima',
                ]),

            Filter::datetimepicker('fecha_cierre', 'fecha_cierre')
                ->params([
                    'timezone' => 'America/Lima',
                ]),

            // Filtros para trabajador usando relación
            Filter::inputText('trabajador_nombre', 'Nombre completo')
                ->builder(function (Builder $query, array $value) {
                    $search = $value['value'];

                    return $query->whereHas('trabajador.persona', function ($q) use ($search) {
                        $q->where(DB::raw("CONCAT(nombre, ' ', apellido_paterno, ' ', apellido_materno)"), 'like', "%{$search}%")
                            ->orWhere('nombre', 'like', "%{$search}%")
                            ->orWhere('apellido_paterno', 'like', "%{$search}%")
                            ->orWhere('apellido_materno', 'like', "%{$search}%");
                    });
                }),
            Filter::inputText('documento_trabajador', 'Documento')
                ->builder(function (Builder $query, array $value) {
                    $search = $value['value'];

                    return $query->whereHas('trabajador.persona', function ($q) use ($search) {
                        $q->where('numero_documento', 'like', "%{$search}%");
                    });
                }),

        ];
    }

    // Método para manejar la búsqueda personalizada
    public function addColumns(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('trabajador_nombre')
            ->add('documento_trabajador');
    }

    public function actions(Caja $row): array
    {
        return [
            Button::add('ver')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye-icon lucide-eye"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg>')
                ->id()
                ->class('pg-btn-white dark:bg-pg-primary-700')
                ->tooltip('Ver Detalles')
                ->dispatch('verDetalleCaja', ['id_caja' => $row->id_caja]),
        ];
    }
}
