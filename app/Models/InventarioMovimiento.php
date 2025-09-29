<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventarioMovimiento extends Model
{
    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';
    protected $primaryKey = 'id_inventario_movimiento';
    protected $table = "inventario_movimientos";
    protected $fillable = [
        "id_inventario_movimiento",
        "tipo_movimiento",
        "cantidad_movimiento",
        "stock_resultante",
        "fecha_movimiento",
        "fecha_registro",
        "id_trabajador",
        "id_lote",
        "ubicacion",
        "movimentable_type",
        "movimentable_id",
        "fecha_actualizacion",
        "fecha_registro",
    ];

    protected $casts = [
        'fecha_movimiento' => 'datetime',
        'fecha_registro' => 'datetime',
    ];

    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class, "id_trabajador");
    }

    public function lote()
    {
        return $this->belongsTo(Lotes::class, "id_lote");
    }

    public function movimentable()
    {
        return $this->morphTo();
    }
}
