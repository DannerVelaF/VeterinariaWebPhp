<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Especie extends Model
{
    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $fillable = [
        'nombre_especie',
        'descripcion',
        'estado',
        'fecha_registro',
        'fecha_actualizacion',
    ];

    public function razas()
    {
        return $this->hasMany(Raza::class);
    }
}
