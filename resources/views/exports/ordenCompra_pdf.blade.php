<!-- resources/views/exports/ordenCompra_pdf.blade.php -->
<!DOCTYPE html>
<html lang="es">

    <head>
        <meta charset="UTF-8">
        <title>Orden de compra - {{ $compra->codigo }}</title>
        <style>
            body {
                font-family: DejaVu Sans, sans-serif;
                font-size: 12px;
                margin: 20px;
            }

            .title {
                font-size: 20px;
                font-weight: bold;
                text-align: center;
                margin-bottom: 20px;
            }

            .subtitle {
                font-size: 14px;
                font-weight: bold;
                margin-bottom: 10px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 10px;
            }

            th,
            td {
                border: 1px solid #ccc;
                padding: 6px;
                text-align: left;
            }

            th {
                background-color: #f2f2f2;
            }

            .text-end {
                text-align: right;
            }

            .mb-2 {
                margin-bottom: 8px;
            }
        </style>
    </head>

    <body>

        <img src="{{ public_path('images/logo.jpg') }}" alt="logo" style="width: 100px; margin-bottom: 20px;">

        <div class="title">Orden de Compra</div>
        <p><strong>Número de orden:</strong> {{ $compra->codigo }}</p>
        <p><strong>Proveedor:</strong> {{ $compra->proveedor->nombre_proveedor }}</p>
        <p><strong>Nro Factura:</strong> {{ $compra->numero_factura }}</p>
        <p><strong>Estado:</strong class="capitalize"> {{ $compra->estadoCompra->nombre_estado_compra }}</p>
        <p><strong>Fecha de compra:</strong> {{ $compra->fecha_compra }}</p>
        <p><strong>Fecha de registro:</strong> {{ $compra->fecha_registro }}</p>

        <div class="subtitle">Detalles de productos</div>
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario (S/)</th>
                    <th>Total (S/)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($compra->detalleCompra as $detalle)
                    <tr>
                        <td>{{ $detalle->producto->nombre_producto }}</td>
                        <td>{{ $detalle->cantidad }}</td>
                        <td>{{ number_format($detalle->precio_unitario, 2) }}</td>
                        <td>{{ number_format($detalle->sub_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @php
            $subtotal = $compra->total;
            $igvCalc = $subtotal * $IGV;
            $total = $subtotal + $igvCalc;
        @endphp

        <div style="margin-top: 15px;">
            <p class="text-end"><strong>Subtotal:</strong> S/ {{ number_format($subtotal, 2) }}</p>
            <p class="text-end"><strong>IGV ({{ $IGV * 100 }}%):</strong> S/ {{ number_format($igvCalc, 2) }}</p>
            <p class="text-end"><strong>Total:</strong> S/ {{ number_format($total, 2) }}</p>
        </div>

        <div style="margin-top: 15px;">
            <p class="subtitle">Observaciones</p>
            <p>{{ $compra->observacion ?? 'Ninguna' }}</p>
        </div>

    </body>

</html>
