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
        "id_tipo_movimiento",
        "cantidad_movimiento",
        "stock_resultante",
        "id_tipo_ubicacion",
        "motivo",
        "id_lote",
        "id_trabajador",
        "tipo_movimiento_asociado",
        "id_movimiento_asociado",
        "fecha_movimiento",
        "fecha_registro",
        "fecha_actualizacion",
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

    public function movimientoAsociado()
    {
        return $this->morphTo();
    }

    public function tipoMovimiento()
    {
        return $this->belongsTo(TipoMovimiento::class, 'id_tipo_movimiento');
    }

    public function tipoUbicacion()
    {
        return $this->belongsTo(TipoUbicacion::class, 'id_tipo_ubicacion');
    }
}
