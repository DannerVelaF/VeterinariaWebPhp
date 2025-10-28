<!DOCTYPE html>
<html lang="es">

    <head>
        <meta charset="UTF-8">
        <title>Reporte de Compras</title>
        <style>
            body {
                font-family: DejaVu Sans, sans-serif;
                font-size: 12px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }

            th,
            td {
                border: 1px solid #ccc;
                padding: 6px;
                text-align: left;
            }

            th {
                background: #f0f0f0;
            }

            h2 {
                text-align: center;
                margin-bottom: 20px;
            }
        </style>
    </head>

    <body>
        <h2>Reporte de Compras con Detalle</h2>

        @foreach ($compras as $compra)
            <h3>Orden: {{ $compra->codigo }} - Proveedor: {{ $compra->proveedor->nombre_proveedor ?? '---' }}</h3>
            <p>
                Nro Factura: {{ $compra->numero_factura }} <br>
                Fecha Compra: {{ $compra->fecha_compra }} <br>
                Estado: {{ ucfirst($compra->estadoCompra->nombre_estado_compra) }}
            </p>

            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($compra->detalleCompra as $detalle)
                        <tr>
                            <td>{{ $detalle->producto->nombre_producto ?? '---' }}</td>
                            <td>{{ $detalle->cantidad }}</td>
                            <td>s/{{ number_format($detalle->precio_unitario, 2) }}</td>
                            <td>s/{{ number_format($detalle->sub_total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    </body>

</html>
