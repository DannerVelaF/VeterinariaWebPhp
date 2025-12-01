<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background-color: #e2e8f0;
            font-weight: bold;
            text-align: left;
            padding: 8px;
            border: 1px solid #000;
            font-size: 11px;
        }

        td {
            padding: 8px;
            border: 1px solid #000;
            vertical-align: top;
        }

        .header {
            margin-bottom: 20px;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .meta-info p {
            margin: 2px 0;
        }

        /* Estilo para el logo */
        .logo {
            width: 150px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
{{-- LOGO DE LA EMPRESA --}}
{{-- public_path es necesario para que DomPDF encuentre la imagen en el servidor --}}
<img src="{{ public_path('images/logo.jpg') }}" alt="logo" class="logo">

<div class="header">
    <div class="title">HOJA DE RUTA / DESPACHO</div>
    <div class="meta-info">
        <p><strong>Fecha de Programación:</strong> {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</p>
        <p><strong>Transportista Asignado:</strong> {{ $transportista }}</p>
    </div>
</div>

<table>
    <thead>
    <tr>
        <th style="width: 60px;">Hora</th>
        <th style="width: 140px;">Cliente</th>
        <th>Dirección de Entrega</th>
        <th style="width: 90px;">Teléfono</th>
        <th style="width: 70px;">Pedido #</th>
        <th style="width: 120px;">Firma / DNI Recibe</th>
    </tr>
    </thead>
    <tbody>
    @foreach($pedidos as $pedido)
        <tr>
            <td>{{ $pedido->fecha_programada ? $pedido->fecha_programada->format('H:i') : '--' }}</td>
            <td>
                <strong>{{ $pedido->venta->cliente->persona->nombre }}</strong><br>
                {{ $pedido->venta->cliente->persona->apellido_paterno }}
            </td>
            <td>
                {{ $pedido->direccion->nombre_calle }} #{{ $pedido->direccion->numero }}
                @if($pedido->direccion->referencia)
                    <br><small>Ref: {{ $pedido->direccion->referencia }}</small>
                @endif
                <br><small><em>{{ $pedido->direccion->ubigeo->distrito ?? '' }}</em></small>
            </td>
            <td>{{ $pedido->venta->cliente->persona->numero_telefono_personal ?? '-' }}</td>
            <td style="text-align: center;"><strong>#{{ $pedido->id_envio_pedido }}</strong></td>
            <td>
                {{-- Espacio en blanco para la firma física --}}
                <div style="height: 40px;"></div>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<div style="margin-top: 30px; font-size: 10px; color: #666; border-top: 1px solid #ccc; padding-top: 5px;">
    Generado el {{ date('d/m/Y H:i') }} - Sistema de Gestión Veterinaria
</div>
</body>
</html>
