<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    protected $fillable = [
        "id_venta",
        "id_producto",
        "estado",
        "cantidad",
        "precio_unitario",
        "sub_total",
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, "id_venta", "id");
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, "id_producto", "id");
    }
}