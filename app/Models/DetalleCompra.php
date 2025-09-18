<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleCompra extends Model
{
    protected $fillable = [
        "id_compra",
        "id_producto",
        "cantidad",
        "estado",
        "precio_unitario",
        "sub_total",
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
