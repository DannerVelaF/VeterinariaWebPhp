<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoUbicacion extends Model
{

    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';
    protected $primaryKey = 'id_tipo_ubicacion';
    protected $table = 'tipo_ubicacion';

    protected $fillable = [
        'id_tipo_ubicacion',
        'nombre_tipo_ubicacion',
        'fecha_registro',
        'fecha_actualizacion',
    ];

    public function inventarioMovimientos()
    {
        return $this->hasMany(InventarioMovimiento::class, "id_tipo_ubicacion");
    }
}
