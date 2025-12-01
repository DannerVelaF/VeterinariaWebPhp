<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoEnvioPedido extends Model
{
    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $table = 'estado_envio_pedidos';
    protected $primaryKey = 'id_estado_envio_pedido';

    protected $fillable = [
        'nombre_estado_envio_pedido',
        "fecha_registro",
        "fecha_actualizacion",
    ];


    public function envio_pedidos()
    {
        return $this->hasMany(EnvioPedido::class, "id_envio_pedido");
    }


}
