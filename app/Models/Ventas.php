<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ventas extends Model
{
    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $fillable = [
        "fecha_venta",
        "subtotal",
        "total",
        "descuento",
        "impuesto",
        "observaciones",
        "estado",
        "id_cliente",
        "id_trabajador",
        "fecha_registro",
        "fecha_actualizacion",
    ];

    public function cliente()
    {
        return $this->belongsTo(Clientes::class, "id_cliente", "id");
    }
    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class, "id_trabajador", "id");
    }

    public function detalleVentas()
    {
        return $this->hasMany(DetalleVentas::class, 'id_venta', 'id');
    }
}
