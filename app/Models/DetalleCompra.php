<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleCompra extends Model
{

    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';
    protected $primaryKey = 'id_detalle_compra';
    protected $fillable = [
        "id_detalle_compra",
        "id_compra",
        "id_producto",
        // FALTA EL DETALLE DEL ESTADO DE LA COMPRA
        "id_estado_detalle_compra",
        "cantidad",
        // "estado",
        "precio_unitario",
        "sub_total",
        "fecha_registro",
        "fecha_actualizacion",
    ];


    public function compra()
    {
        return $this->belongsTo(Compra::class, "id_compra");
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, "id_producto");
    }

    public function movimientos()
    {
        return $this->morphMany(InventarioMovimiento::class, 'movimentable');
    }


    public function estadoDetalleCompra()
    {
        return $this->belongsTo(EstadoDetalleCompra::class, "id_estado_detalle_compra");
    }
}
