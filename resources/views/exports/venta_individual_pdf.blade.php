<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de Venta - {{ $venta->codigo ?? 'N/A' }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .title {
            font-size: 20px;
            font-weight: bold;
            margin: 0;
        }

        .subtitle {
            font-size: 14px;
            color: #666;
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .venta-header {
            background: #e9ecef;
            padding: 10px;
            margin: 20px 0 10px 0;
            border-left: 4px solid #007bff;
            font-weight: bold;
            font-size: 14px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin: 15px 0;
        }

        .info-item {
            padding: 10px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            background: #fafafa;
        }

        .info-label {
            font-weight: bold;
            color: #495057;
            display: block;
            margin-bottom: 5px;
        }

        .payment-info {
            background: #e7f3ff;
            padding: 12px;
            margin: 12px 0;
            border-radius: 5px;
            border-left: 4px solid #007bff;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 10px;
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

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .totals {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }

        .total-final {
            font-weight: bold;
            font-size: 14px;
            border-top: 2px solid #007bff;
            padding-top: 8px;
            margin-top: 8px;
        }
    </style>
</head>
<body>
<!-- Encabezado del Comprobante -->
<div class="header">
    <h1 class="title">COMPROBANTE DE VENTA</h1>
    <div class="subtitle">
        Código: {{ $venta->codigo ?? 'N/A' }} |
        Fecha: {{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y H:i') }} |
        Emitido: {{ $fecha_emision }}
    </div>
</div>

<!-- Información de la Venta -->
<div class="venta-header">
    VENTA: {{ $venta->codigo ?? 'N/A' }} |
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
        <span class="info-label">CLIENTE:</span>
        @if($venta->cliente && $venta->cliente->persona)
            {{ $venta->cliente->persona->nombre ?? '' }}
            {{ $venta->cliente->persona->apellido_paterno ?? '' }}
            {{ $venta->cliente->persona->apellido_materno ?? '' }}<br>
            <strong>DNI:</strong> {{ $venta->cliente->persona->numero_documento ?? 'N/A' }}<br>
            <strong>Teléfono:</strong> {{ $venta->cliente->persona->numero_telefono_personal ?? 'N/A' }}<br>
            <strong>Email:</strong> {{ $venta->cliente->persona->correo_electronico_personal ?? 'N/A' }}
        @else
            Cliente no disponible
        @endif
    </div>

    <div class="info-item">
        <span class="info-label">VENDEDOR:</span>
        @if($venta->trabajador && $venta->trabajador->persona)
            {{ $venta->trabajador->persona->nombre ?? '' }}
            {{ $venta->trabajador->persona->apellido_paterno ?? '' }}<br>
            <strong>DNI:</strong> {{ $venta->trabajador->persona->numero_documento ?? 'N/A' }}
        @else
            Vendedor no disponible
        @endif
    </div>
</div>

<!-- Información de Pago -->
@if($venta->transaccionPago)
    <div class="payment-info">
        <strong>INFORMACIÓN DE PAGO</strong><br>
        <div style="margin-top: 8px;">
            <strong>Método:</strong> {{ $venta->transaccionPago->metodoPago->nombre_metodo ?? 'N/A' }} |
            <strong>Monto Pagado:</strong> S/ {{ number_format($venta->transaccionPago->monto, 2) }} |
            <strong>Estado:</strong>
            <span class="status-badge
                    @if($venta->transaccionPago->estado === 'completado') status-completado
                    @elseif($venta->transaccionPago->estado === 'pendiente') status-pendiente
                    @else status-cancelado @endif">
                    {{ strtoupper($venta->transaccionPago->estado) }}
                </span>
            @if($venta->transaccionPago->referencia)
                | <strong>Referencia:</strong> {{ $venta->transaccionPago->referencia }}
            @endif
        </div>
    </div>
@endif

<!-- Detalle de Productos/Servicios -->
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
                    <strong>{{ $detalle->producto->nombre_producto ?? 'Producto no disponible' }}</strong>
                    @if($detalle->producto && $detalle->producto->codigo_barras)
                        <br><small>Código: {{ $detalle->producto->codigo_barras }}</small>
                    @endif
                    @if($detalle->producto && $detalle->producto->descripcion)
                        <br><small>{{ $detalle->producto->descripcion }}</small>
                    @endif
                @else
                    <strong>{{ $detalle->servicio->nombre_servicio ?? 'Servicio no disponible' }}</strong>
                    @if($detalle->servicio && $detalle->servicio->duracion_estimada)
                        <br><small>Duración: {{ $detalle->servicio->duracion_estimada }}</small>
                    @endif
                    @if($detalle->servicio && $detalle->servicio->descripcion)
                        <br><small>{{ $detalle->servicio->descripcion }}</small>
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
</table>

<!-- Totales -->
<div class="totals">
    <div class="total-row">
        <span>Subtotal:</span>
        <span>S/ {{ number_format($venta->subtotal, 2) }}</span>
    </div>

    @if($venta->descuento > 0)
        <div class="total-row">
            <span>Descuento:</span>
            <span>- S/ {{ number_format($venta->descuento, 2) }}</span>
        </div>
    @endif

    <div class="total-row">
        <span>IGV (18%):</span>
        <span>S/ {{ number_format($venta->impuesto, 2) }}</span>
    </div>

    <div class="total-row total-final">
        <span>TOTAL GENERAL:</span>
        <span>S/ {{ number_format($venta->total, 2) }}</span>
    </div>
</div>

@if($venta->observacion)
    <div
        style="margin: 15px 0; padding: 12px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 5px;">
        <strong>Observación:</strong> {{ $venta->observacion }}
    </div>
@endif

<!-- Pie de página -->
<div class="footer">
    <p>Comprobante generado el {{ $fecha_emision }} | Sistema de Ventas</p>
    <p>Este documento es un comprobante de venta generado electrónicamente</p>
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
