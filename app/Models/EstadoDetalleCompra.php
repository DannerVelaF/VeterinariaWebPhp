<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoDetalleCompra extends Model
{
    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $table = 'estado_detalle_compras';
    protected $primaryKey = 'id_estado_detalle_compra';

    protected $fillable = [
        'id_estado_detalle_compra',
        'nombre_estado_detalle_compra',
        'fecha_registro',
        'fecha_actualizacion',
    ];

    public function detalle_compras()
    {
        return $this->hasMany(DetalleCompra::class, "id_detalle_compra");
    }
}
