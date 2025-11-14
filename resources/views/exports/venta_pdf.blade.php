<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de Venta - {{ $venta->codigo ?? $venta->id_venta }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .logo {
            width: 120px;
        }

        .company-info {
            text-align: center;
            flex-grow: 1;
        }

        .title {
            font-size: 20px;
            font-weight: bold;
            margin: 0;
        }

        .subtitle {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            background: #f5f5f5;
            padding: 8px;
            border-left: 4px solid #333;
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

        .totals {
            margin-top: 20px;
            width: 300px;
            margin-left: auto;
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            border-bottom: 1px solid #eee;
        }

        .totals-row.final {
            font-weight: bold;
            border-top: 2px solid #333;
            margin-top: 5px;
            padding-top: 5px;
        }

        .customer-info {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 15px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <!-- Encabezado -->
    <div class="header">
        <div>
            @if(file_exists(public_path('images/logo.jpg')))
                <img src="{{ public_path('images/logo.jpg') }}" alt="Logo" class="logo">
            @else
                <div style="width: 120px; height: 80px; background: #f0f0f0; text-align: center; line-height: 80px; border: 1px solid #ddd;">
                    LOGO
                </div>
            @endif
        </div>
        <div class="company-info">
            <h1 class="title">COMPROBANTE DE VENTA</h1>
            <p style="margin: 5px 0;">RUC: 20123456789</p>
            <p style="margin: 5px 0;">Av. Principal 123 - Lima, Perú</p>
            <p style="margin: 5px 0;">Tel: (01) 234-5678</p>
        </div>
        <div style="text-align: right;">
            <p><strong>N° Venta:</strong> {{ $venta->codigo ?? 'N/A' }}</p>
            <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y') }}</p>
            <p><strong>Estado:</strong> {{ $venta->estadoVenta->nombre_estado_venta_fisica ?? 'N/A' }}</p>
        </div>
    </div>

    <!-- Información del Cliente -->
    <div class="customer-info">
        <div class="subtitle">INFORMACIÓN DEL CLIENTE</div>
        <div class="info-grid">
            <div>
                <strong>Cliente:</strong> 
                @if($venta->cliente && $venta->cliente->persona)
                    {{ $venta->cliente->persona->nombres }} 
                    {{ $venta->cliente->persona->apellido_paterno ?? '' }} 
                    {{ $venta->cliente->persona->apellido_materno ?? '' }}
                @else
                    Cliente no disponible
                @endif
            </div>
            <div>
                <strong>Documento:</strong> 
                @if($venta->cliente && $venta->cliente->persona)
                    {{ $venta->cliente->persona->numero_documento ?? 'N/A' }}
                @else
                    N/A
                @endif
            </div>
            <div>
                <strong>Teléfono:</strong> 
                @if($venta->cliente && $venta->cliente->persona)
                    {{ $venta->cliente->persona->telefono ?? 'N/A' }}
                @else
                    N/A
                @endif
            </div>
            <div>
                <strong>Dirección:</strong> 
                @if($venta->cliente && $venta->cliente->persona)
                    {{ $venta->cliente->persona->direccion ?? 'N/A' }}
                @else
                    N/A
                @endif
            </div>
        </div>
    </div>

    <!-- Detalles de la Venta -->
    <div class="subtitle">DETALLES DE LA VENTA</div>
    <table>
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="40%">DESCRIPCIÓN</th>
                <th width="15%">TIPO</th>
                <th width="10%" class="text-center">CANTIDAD</th>
                <th width="15%" class="text-right">PRECIO UNIT.</th>
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
                        @if($detalle->motivo_salida)
                            <br><small><strong>Motivo:</strong> {{ $detalle->motivo_salida }}</small>
                        @endif
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
    </table>

    <!-- Totales -->
    <div class="totals">
        <div class="totals-row">
            <span>Subtotal:</span>
            <span>S/ {{ number_format($venta->subtotal, 2) }}</span>
        </div>
        @if($venta->descuento > 0)
        <div class="totals-row">
            <span>Descuento:</span>
            <span>- S/ {{ number_format($venta->descuento, 2) }}</span>
        </div>
        @endif
        <div class="totals-row">
            <span>IGV ({{ ($IGV ?? 0.18) * 100 }}%):</span>
            <span>S/ {{ number_format($venta->impuesto, 2) }}</span>
        </div>
        <div class="totals-row final">
            <span>TOTAL:</span>
            <span>S/ {{ number_format($venta->total, 2) }}</span>
        </div>
    </div>

    <!-- Observaciones -->
    @if($venta->observacion)
    <div style="margin-top: 20px;">
        <div class="subtitle">OBSERVACIONES</div>
        <p>{{ $venta->observacion }}</p>
    </div>
    @endif

    <!-- Información del Vendedor -->
    <div style="margin-top: 30px;">
        <div class="info-grid">
            <div>
                <strong>Vendedor:</strong><br>
                @if($venta->trabajador && $venta->trabajador->persona)
                    {{ $venta->trabajador->persona->nombres ?? 'N/A' }} 
                    {{ $venta->trabajador->persona->apellido_paterno ?? '' }}
                    {{ $venta->trabajador->persona->apellido_materno ?? '' }}
                @else
                    Vendedor no disponible
                @endif
            </div>
            <div>
                <strong>Fecha de Emisión:</strong><br>
                {{ \Carbon\Carbon::parse($venta->fecha_registro)->format('d/m/Y H:i') }}
            </div>
        </div>
    </div>

    <!-- Pie de página -->
    <div class="footer">
        <p>Documento generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }} | 
           Sistema de Ventas | Página 1 de 1</p>
    </div>
</body>
</html>