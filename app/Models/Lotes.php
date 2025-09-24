<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lotes extends Model
{

    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

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
        "fecha_registro",
        "fecha_actualizacion",
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id', 'id');
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
