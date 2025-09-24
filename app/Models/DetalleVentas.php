<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleVentas extends Model
{

    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $fillable = [
        "cantidad",
        "precio_unitario",
        "subtotal",
        "tipo_item",
        "id_venta",
        "id_producto",
        "id_servicio",
        "fecha_registro",
        "fecha_actualizacion",
    ];

    public function venta()
    {
        return $this->belongsTo(Ventas::class, "id_venta", "id");
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, "id_producto", "id");
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, "id_servicio", "id");
    }

    public function movimientos()
    {
        return $this->morphMany(InventarioMovimiento::class, 'movimentable');
    }
}
