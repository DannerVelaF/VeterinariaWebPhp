<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventarioMovimiento extends Model
{
    protected $table = "inventario_movimientos";
    public $timestamps = false;
    protected $fillable = [
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
    ];


    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class, "id_trabajador", "id");
    }

    public function lote()
    {
        return $this->belongsTo(Lotes::class, "id_lote", "id");
    }

    public function movimentable()
    {
        return $this->morphTo();
    }
}
