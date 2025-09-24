<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaccionPago extends Model
{
    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $fillable = [
        "id_venta",
        "id_metodo",
        "monto",
        "referencia",
        "estado",
        "datos_adicionales",
        "fecha_registro",
        "fecha_actualizacion",
    ];

    public function venta()
    {
        return $this->belongsTo(Ventas::class, "id_venta", "id");
    }

    public function metodo()
    {
        return $this->belongsTo(MetodoPago::class, "id_metodo", "id");
    }
}
