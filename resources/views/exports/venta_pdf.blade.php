<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte General de Ventas</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            margin: 15px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }

        .subtitle {
            font-size: 12px;
            color: #666;
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 10px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .venta-header {
            background: #e9ecef;
            padding: 8px;
            margin: 15px 0 8px 0;
            border-left: 4px solid #007bff;
            font-weight: bold;
        }

        .summary {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-top: 10px;
        }

        .summary-item {
            text-align: center;
            padding: 10px;
            background: white;
            border-radius: 3px;
            border: 1px solid #dee2e6;
        }

        .summary-value {
            font-size: 14px;
            font-weight: bold;
            color: #007bff;
        }

        .page-break {
            page-break-after: always;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin: 10px 0;
            font-size: 10px;
        }

        .info-item {
            padding: 5px;
            border: 1px solid #dee2e6;
            border-radius: 3px;
            background: #fafafa;
        }

        .info-label {
            font-weight: bold;
            color: #495057;
        }

        .payment-info {
            background: #e7f3ff;
            padding: 8px;
            margin: 8px 0;
            border-radius: 3px;
            border-left: 4px solid #007bff;
        }

        .status-badge {
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
        }

        .status-completado {
            background: #d4edda;
            color: #155724;
        }

        .status-pendiente {
            background: #fff3cd;
            color: #856404;
        }

        .status-cancelado {
            background: #f8d7da;
            color: #721c24;
        }

        .payment-status-completado {
            background: #d1ecf1;
            color: #0c5460;
        }

        .payment-status-pendiente {
            background: #ffeaa7;
            color: #856404;
        }

        .payment-status-fallido {
            background: #f5c6cb;
            color: #721c24;
        }
    </style>
</head>
<body>
<!-- Encabezado del Reporte -->
<div class="header">
    <h1 class="title">REPORTE GENERAL DE VENTAS</h1>
    <div class="subtitle">
        Periodo: {{ \Carbon\Carbon::now()->format('d/m/Y') }} |
        Generado por: {{ auth()->user()->name ?? 'Sistema' }}
    </div>
</div>

<!-- Resumen Estadístico -->
<div class="summary">
    <strong>RESUMEN ESTADÍSTICO</strong>
    <div class="summary-grid">
        <div class="summary-item">
            <div>Total Ventas</div>
            <div class="summary-value">{{ $ventas->count() }}</div>
        </div>
        <div class="summary-item">
            <div>Ventas Completadas</div>
            <div class="summary-value">
                @php
                    $completadas = $ventas->filter(function($venta) {
                        return $venta->estadoVenta &&
                               $venta->estadoVenta->nombre_estado_venta_fisica === 'completado';
                    })->count();
                @endphp
                {{ $completadas }}
            </div>
        </div>
        <div class="summary-item">
            <div>Ventas Pendientes</div>
            <div class="summary-value">
                @php
                    $pendientes = $ventas->filter(function($venta) {
                        return $venta->estadoVenta &&
                               $venta->estadoVenta->nombre_estado_venta_fisica === 'pendiente';
                    })->count();
                @endphp
                {{ $pendientes }}
            </div>
        </div>
        <div class="summary-item">
            <div>Total Recaudado</div>
            <div class="summary-value">
                @php
                    $totalRecaudado = $ventas->filter(function($venta) {
                        return $venta->estadoVenta &&
                               $venta->estadoVenta->nombre_estado_venta_fisica === 'completado';
                    })->sum('total');
                @endphp
                S/ {{ number_format($totalRecaudado, 2) }}
            </div>
        </div>
    </div>
</div>

<!-- Detalle de Ventas -->
@foreach($ventas as $venta)
    <div class="venta-header">
        VENTA: {{ $venta->codigo ?? 'N/A' }} |
        FECHA: {{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y H:i') }} |
        ESTADO:
        <span class="status-badge
                @if($venta->estadoVenta->nombre_estado_venta_fisica === 'completado') status-completado
                @elseif($venta->estadoVenta->nombre_estado_venta_fisica === 'pendiente') status-pendiente
                @else status-cancelado @endif">
                {{ strtoupper($venta->estadoVenta->nombre_estado_venta_fisica ?? 'N/A') }}
            </span>
    </div>

    <!-- Información General -->
    <div class="info-grid">
        <div class="info-item">
            <span class="info-label">Cliente:</span><br>
            @if($venta->cliente && $venta->cliente->persona)
                {{ $venta->cliente->persona->nombre }}
                {{ $venta->cliente->persona->apellido_paterno ?? '' }}
                {{ $venta->cliente->persona->apellido_materno ?? '' }}<br>
                <small>DNI: {{ $venta->cliente->persona->numero_documento ?? 'N/A' }}</small><br>
                <small>Tel: {{ $venta->cliente->persona->numero_telefono_personal ?? 'N/A' }}</small>
            @else
                Cliente no disponible
            @endif
        </div>

        <div class="info-item">
            <span class="info-label">Vendedor:</span><br>
            @if($venta->trabajador && $venta->trabajador->persona)
                {{ $venta->trabajador->persona->nombre }}
                {{ $venta->trabajador->persona->apellido_paterno ?? '' }}<br>
                <small>DNI: {{ $venta->trabajador->persona->numero_documento ?? 'N/A' }}</small>
            @else
                Vendedor no disponible
            @endif
        </div>
    </div>

    <!-- Información de Pago -->
    @if($venta->transaccionPago)
        <div class="payment-info">
            <strong>INFORMACIÓN DE PAGO</strong><br>
            <div style="margin-top: 5px;">
                <strong>Método:</strong> {{ $venta->transaccionPago->metodoPago->nombre_metodo ?? 'N/A' }} |
                <strong>Monto Pagado:</strong> S/ {{ number_format($venta->transaccionPago->monto, 2) }} |
                <strong>Estado:</strong>
                <span class="status-badge
                    @if($venta->transaccionPago->estado === 'completado') payment-status-completado
                    @elseif($venta->transaccionPago->estado === 'pendiente') payment-status-pendiente
                    @else payment-status-fallido @endif">
                    {{ strtoupper($venta->transaccionPago->estado) }}
                </span>
                @if($venta->transaccionPago->referencia)
                    | <strong>Referencia:</strong> {{ $venta->transaccionPago->referencia }}
                @endif
                @if($venta->transaccionPago->fecha_pago)
                    | <strong>Fecha
                        Pago:</strong> {{ \Carbon\Carbon::parse($venta->transaccionPago->fecha_pago)->format('d/m/Y H:i') }}
                @endif
            </div>
            @if($venta->transaccionPago->comprobante_url)
                <div style="margin-top: 3px;">
                    <strong>Comprobante:</strong> {{ basename($venta->transaccionPago->comprobante_url) }}
                </div>
            @endif
        </div>
    @endif

    <!-- Detalle de Productos/Servicios -->
    <table>
        <thead>
        <tr>
            <th width="5%">#</th>
            <th width="40%">DESCRIPCIÓN</th>
            <th width="15%">TIPO</th>
            <th width="10%" class="text-center">CANT.</th>
            <th width="15%" class="text-right">P. UNIT.</th>
            <th width="15%" class="text-right">SUBTOTAL</th>
        </tr>
        </thead>
        <tbody>
        @foreach($venta->detalleVentas as $index => $detalle)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    @if($detalle->tipo_item == 'producto')
                        {{ $detalle->producto->nombre_producto ?? 'Producto no disponible' }}
                        @if($detalle->producto && $detalle->producto->codigo_barras)
                            <br><small>Código: {{ $detalle->producto->codigo_barras }}</small>
                        @endif
                    @else
                        {{ $detalle->servicio->nombre_servicio ?? 'Servicio no disponible' }}
                        @if($detalle->servicio && $detalle->servicio->duracion_estimada)
                            <br><small>Duración: {{ $detalle->servicio->duracion_estimada }}</small>
                        @endif
                    @endif
                </td>
                <td>
                        <span style="text-transform: uppercase; font-weight: bold;">
                            {{ $detalle->tipo_item }}
                        </span>
                </td>
                <td class="text-center">{{ number_format($detalle->cantidad, 2) }}</td>
                <td class="text-right">S/ {{ number_format($detalle->precio_unitario, 2) }}</td>
                <td class="text-right">S/ {{ number_format($detalle->subtotal, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr style="background: #f8f9fa;">
            <td colspan="4"></td>
            <td class="text-right"><strong>Subtotal:</strong></td>
            <td class="text-right"><strong>S/ {{ number_format($venta->subtotal, 2) }}</strong></td>
        </tr>
        @if($venta->descuento > 0)
            <tr style="background: #f8f9fa;">
                <td colspan="4"></td>
                <td class="text-right"><strong>Descuento:</strong></td>
                <td class="text-right"><strong>- S/ {{ number_format($venta->descuento, 2) }}</strong></td>
            </tr>
        @endif
        <tr style="background: #f8f9fa;">
            <td colspan="4"></td>
            <td class="text-right"><strong>IGV (18%):</strong></td>
            <td class="text-right"><strong>S/ {{ number_format($venta->impuesto, 2) }}</strong></td>
        </tr>
        <tr style="background: #e9ecef; font-weight: bold;">
            <td colspan="4"></td>
            <td class="text-right"><strong>TOTAL GENERAL:</strong></td>
            <td class="text-right"><strong>S/ {{ number_format($venta->total, 2) }}</strong></td>
        </tr>
        </tfoot>
    </table>

    @if($venta->observacion)
        <div style="margin: 5px 0 15px 0; padding: 8px; background: #fff3cd; border-left: 4px solid #ffc107;">
            <strong>Observación:</strong> {{ $venta->observacion }}
        </div>
    @endif

    <!-- Salto de página cada 2 ventas para mejor legibilidad -->
    @if($loop->iteration % 2 == 0 && !$loop->last)
        <div class="page-break"></div>
    @endif
@endforeach

<!-- Resumen Final -->
<div class="summary">
    <strong>RESUMEN FINAL</strong>
    <div style="margin-top: 10px;">
        <div class="info-grid">
            <div class="info-item">
                <strong>Total General Vendido:</strong><br>
                <span style="font-size: 14px; color: #007bff;">S/ {{ number_format($ventas->sum('total'), 2) }}</span>
            </div>
            <div class="info-item">
                <strong>Total Ventas:</strong><br>
                <span style="font-size: 14px; color: #28a745;">{{ $ventas->count() }}</span>
            </div>
            <div class="info-item">
                <strong>Promedio por Venta:</strong><br>
                <span
                    style="font-size: 14px; color: #6c757d;">S/ {{ number_format($ventas->avg('total') ?? 0, 2) }}</span>
            </div>
            <div class="info-item">
                <strong>Total Descuentos:</strong><br>
                <span
                    style="font-size: 14px; color: #dc3545;">S/ {{ number_format($ventas->sum('descuento'), 2) }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Pie de página -->
<div class="footer">
    <p>Reporte generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }} |
        Sistema de Ventas | Total de páginas: <span class="page-count"></span></p>
</div>

<script type="text/php">
    if (isset($pdf)) {
        $text = "Página {PAGE_NUM} de {PAGE_COUNT}";
        $size = 9;
        $font = $fontMetrics->getFont("DejaVu Sans");
        $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
        $x = ($pdf->get_width() - $width) / 2;
        $y = $pdf->get_height() - 25;
        $pdf->page_text($x, $y, $text, $font, $size);
    }
</script>
</body>
</html>
