<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lotes extends Model
{

    protected $fillable = [
        "producto_id",
        "cantidad_mostrada",
        "cantidad_almacenada",
        "cantidad_vendida",
        "codigo_lote",
        "precio_compra",
        "fecha_recepcion",
        "fecha_vencimiento",
        "estado",
        "observacion",
        "created_at",
        "updated_at",
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id', 'id');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id', 'id');
    }

    public function inventarios()
    {
        return $this->hasMany(InventarioMovimiento::class, 'id_lote', 'id');
    }

    public function getCantidadTotalAttribute()
    {
        return $this->cantidad_almacenada + $this->cantidad_mostrada + $this->cantidad_vendida;
    }
}
