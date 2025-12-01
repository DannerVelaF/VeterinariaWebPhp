<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnvioPedido extends Model
{
    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $table = 'envio_pedidos';
    protected $primaryKey = 'id_envio_pedido';

    protected $fillable = [
        'id_venta',
        'id_direccion',
        'id_estado_envio_pedido',
        'id_trabajador', // Agregado
        'fecha_programada',
        'fecha_entrega_real',
        'foto_evidencia',
        'observaciones_entrega',
        'fecha_registro',
        'fecha_actualizacion'
    ];

    protected $casts = [
        'fecha_programada' => 'datetime',
        'fecha_entrega_real' => 'datetime',
    ];


    // 1. La Venta original (para ver productos)
    public function venta()
    {
        return $this->belongsTo(Ventas::class, 'id_venta');
    }

    // 2. La Dirección de destino (Tabla direcciones)
    public function direccion()
    {
        return $this->belongsTo(Direccion::class, 'id_direccion');
    }

    // 3. El Estado del envío
    public function estadoEnvio()
    {
        return $this->belongsTo(EstadoEnvioPedido::class, 'id_estado_envio_pedido');
    }

    // 4. El Transportista asignado
    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class, 'id_trabajador');
    }
}
