<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Raza extends Model
{
    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $table = 'razas';

    protected $fillable = [
        'nombre_raza',
        'descripcion',
        'fecha_registro',
        'fecha_actualizacion',
    ];

    public function especie()
    {
        return $this->belongsTo(Especie::class, 'id_especie', 'id');
    }

    public function mascotas()
    {
        return $this->hasMany(Mascota::class, 'id_raza', 'id');
    }
}
