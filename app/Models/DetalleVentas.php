<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleVentas extends Model
{

    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';
    protected $primaryKey = 'id_detalle_venta';
    protected $fillable = [
        "id_detalle_venta",
        "estado",
        "cantidad",
        "precio_unitario",
        "subtotal",
        "tipo_item",
        "id_venta",
        "id_producto",
        "motivo_salida",
        "id_servicio",
        "fecha_registro",
        "fecha_actualizacion",
    ];

    public function venta()
    {
        return $this->belongsTo(Ventas::class, "id_venta");
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, "id_producto");
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, "id_servicio");
    }

    public function movimientos()
    {
        return $this->morphMany(InventarioMovimiento::class, 'movimentable');
    }
}
