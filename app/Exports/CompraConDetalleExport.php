<?php

namespace App\Exports;

use App\Models\Compra;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CompraConDetalleExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Compra::with(['proveedor', 'detalleCompra.producto'])->get();
    }

    public function map($compra): array
    {
        return $compra->detalleCompra->map(function ($detalle) use ($compra) {
            return [
                $compra->codigo,
                $compra->fecha_compra,
                $compra->proveedor->nombre ?? '',
                $compra->estado,
                $detalle->producto->nombre_producto ?? '',
                $detalle->cantidad,
                $detalle->precio_unitario,
                $detalle->sub_total,
                $compra->trabajador->persona->user->username,
                $compra->observacion ?? '',
            ];
        })->toArray();
    }

    public function headings(): array
    {
        return [
            'Código',
            'Fecha Compra',
            'Proveedor',
            'Estado',
            'Producto',
            'Cantidad',
            'Precio Unitario',
            'Subtotal',
            "Usuario registro",
            'Observación',
        ];
    }
}
