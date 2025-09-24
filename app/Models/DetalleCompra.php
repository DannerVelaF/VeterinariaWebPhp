<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleCompra extends Model
{

    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $fillable = [
        "id_compra",
        "id_producto",
        "cantidad",
        "estado",
        "precio_unitario",
        "sub_total",
        "fecha_registro",
        "fecha_actualizacion",
    ];


    public function compra()
    {
        return $this->belongsTo(Compra::class, "id_compra", "id");
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, "id_producto", "id");
    }

    public function movimientos()
    {
        return $this->morphMany(InventarioMovimiento::class, 'movimentable');
    }
}
