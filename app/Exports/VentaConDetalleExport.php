<?php

namespace App\Exports;

use App\Models\Ventas;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class VentaConDetalleExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Ventas::with([
            'cliente.persona',
            'detalleVentas.producto', 
            'detalleVentas.servicio', 
            'estadoVenta',
            'trabajador.persona'
        ])->get();
    }

    public function map($venta): array
    {
        // Si la venta no tiene detalles, mostrar al menos la venta
        if ($venta->detalleVentas->isEmpty()) {
            return [
                $venta->codigo ?? 'N/A',
                $venta->fecha_venta,
                $this->getNombreCliente($venta),
                $venta->estadoVenta->nombre_estado_venta_fisica ?? 'N/A',
                '', // Producto/Servicio vacío
                '', // Tipo vacío
                '', // Cantidad vacía
                '', // Precio unitario vacío
                '', // Subtotal detalle vacío
                $venta->subtotal,
                $venta->descuento,
                $venta->impuesto,
                $venta->total,
                $this->getVendedor($venta),
                $venta->observacion ?? '',
            ];
        }

        // Mapear CADA DETALLE como fila separada (igual que compras)
        return $venta->detalleVentas->map(function ($detalle) use ($venta) {
            return [
                $venta->codigo ?? 'N/A',
                $venta->fecha_venta,
                $this->getNombreCliente($venta),
                $venta->estadoVenta->nombre_estado_venta_fisica ?? 'N/A',
                $this->getNombreItem($detalle),
                $detalle->tipo_item,
                $detalle->cantidad,
                $detalle->precio_unitario,
                $detalle->subtotal,
                $venta->subtotal,
                $venta->descuento,
                $venta->impuesto,
                $venta->total,
                $this->getVendedor($venta),
                $venta->observacion ?? '',
            ];
        })->toArray();
    }

    public function headings(): array
    {
        return [
            'Código Venta',
            'Fecha Venta',
            'Cliente',
            'Estado Venta',
            'Producto/Servicio',
            'Tipo Item',
            'Cantidad',
            'Precio Unitario',
            'Subtotal Detalle',
            'Subtotal Venta',
            'Descuento Venta',
            'Impuesto Venta',
            'Total Venta',
            'Vendedor',
            'Observación'
        ];
    }

    /**
     * Helper para obtener nombre completo del cliente
     */
    private function getNombreCliente($venta): string
    {
        if (!$venta->cliente || !$venta->cliente->persona) {
            return 'Cliente no disponible';
        }
        
        $persona = $venta->cliente->persona;
        return trim($persona->nombres . ' ' . 
                   ($persona->apellido_paterno ?? '') . ' ' . 
                   ($persona->apellido_materno ?? ''));
    }

    /**
     * Helper para obtener nombre del producto/servicio
     */
    private function getNombreItem($detalle): string
    {
        if ($detalle->tipo_item == 'producto') {
            return $detalle->producto->nombre_producto ?? 'Producto no disponible';
        } else {
            return $detalle->servicio->nombre_servicio ?? 'Servicio no disponible';
        }
    }

    /**
     * Helper para obtener nombre completo del vendedor
     */
    private function getVendedor($venta): string
    {
        if (!$venta->trabajador || !$venta->trabajador->persona) {
            return 'Vendedor no disponible';
        }
        
        $persona = $venta->trabajador->persona;
        return trim($persona->nombres . ' ' . 
                   ($persona->apellido_paterno ?? '') . ' ' . 
                   ($persona->apellido_materno ?? ''));
    }
}