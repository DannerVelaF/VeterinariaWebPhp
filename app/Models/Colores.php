<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Colores extends Model
{
    protected $table = 'colores';
    protected $primaryKey = 'id_color';
    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $fillable = [
        "id_color",
        "nombre_color",
        "fecha_registro",
        "fecha_actualizacion",
    ];

    public function mascotas()
    {
        return $this->hasMany(Mascota::class, "id_color");
    }
}
