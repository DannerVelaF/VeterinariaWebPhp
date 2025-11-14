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
            CLIENTE: 
            @if($venta->cliente && $venta->cliente->persona)
                {{ $venta->cliente->persona->nombres }} 
                {{ $venta->cliente->persona->apellido_paterno ?? '' }} 
                {{ $venta->cliente->persona->apellido_materno ?? '' }}
            @else
                Cliente no disponible
            @endif
            | 
            FECHA: {{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y') }} | 
            ESTADO: {{ $venta->estadoVenta->nombre_estado_venta_fisica ?? 'N/A' }}
        </div>

        <table>
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="45%">DESCRIPCIÓN</th>
                    <th width="15%">TIPO</th>
                    <th width="10%" class="text-center">CANT.</th>
                    <th width="12%" class="text-right">P. UNIT.</th>
                    <th width="13%" class="text-right">SUBTOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($venta->detalleVentas as $index => $detalle)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        @if($detalle->tipo_item == 'producto')
                            {{ $detalle->producto->nombre_producto ?? 'Producto no disponible' }}
                        @else
                            {{ $detalle->servicio->nombre_servicio ?? 'Servicio no disponible' }}
                        @endif
                    </td>
                    <td>{{ strtoupper($detalle->tipo_item) }}</td>
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
                    <td class="text-right"><strong>IGV:</strong></td>
                    <td class="text-right"><strong>S/ {{ number_format($venta->impuesto, 2) }}</strong></td>
                </tr>
                <tr style="background: #e9ecef; font-weight: bold;">
                    <td colspan="4"></td>
                    <td class="text-right"><strong>TOTAL:</strong></td>
                    <td class="text-right"><strong>S/ {{ number_format($venta->total, 2) }}</strong></td>
                </tr>
            </tfoot>
        </table>

        @if($venta->observacion)
        <div style="margin: 5px 0 15px 0; padding: 8px; background: #fff3cd; border-left: 4px solid #ffc107;">
            <strong>Observación:</strong> {{ $venta->observacion }}
        </div>
        @endif

        <!-- Salto de página cada 3 ventas para mejor legibilidad -->
        @if($loop->iteration % 3 == 0 && !$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

    <!-- Resumen Final -->
    <div class="summary">
        <strong>RESUMEN FINAL</strong>
        <div style="margin-top: 10px;">
            <strong>Total General Vendido:</strong> S/ {{ number_format($ventas->sum('total'), 2) }} | 
            <strong>Total Ventas:</strong> {{ $ventas->count() }} | 
            <strong>Promedio por Venta:</strong> S/ {{ number_format($ventas->avg('total') ?? 0, 2) }}
        </div>
    </div>

    <!-- Pie de página -->
    <div class="footer">
        <p>Reporte generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }} | 
           Sistema de Ventas</p>
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