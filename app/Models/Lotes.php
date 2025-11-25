<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lotes extends Model
{

    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';
    protected $primaryKey = 'id_lote';
    protected $fillable = [
        "id_lote",
        "id_producto",
        "codigo_lote",
        "cantidad_mostrada",
        "cantidad_almacenada",
        "cantidad_vendida",
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
        return $this->belongsTo(Producto::class, "id_producto");
    }

    public function inventarios()
    {
        return $this->hasMany(InventarioMovimiento::class, "id_lote");
    }

    public function getCantidadTotalAttribute()
    {
        return $this->cantidad_almacenada + $this->cantidad_mostrada + $this->cantidad_vendida;
    }


}
