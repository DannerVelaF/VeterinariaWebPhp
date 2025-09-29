<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unidades extends Model
{

    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';
    protected $primaryKey = 'id_unidad';
    protected $fillable = [
        'id_unidad',
        "nombre_unidad",
        'fecha_registro',
        'fecha_actualizacion',
    ];

    public function productos()
    {
        return $this->hasMany(Producto::class, "id_unidad");
    }
}
