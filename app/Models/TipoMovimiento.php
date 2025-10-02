<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoMovimiento extends Model
{

    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';
    protected $primaryKey = 'id_tipo_movimiento';
    protected $table = 'tipo_movimientos';

    protected $fillable = [
        'id_tipo_movimiento',
        'nombre_tipo_movimiento',
        'fecha_registro',
        'fecha_actualizacion',
    ];

    public function inventarioMovimientos()
    {
        return $this->hasMany(InventarioMovimiento::class, "id_tipo_movimiento");
    }
}
