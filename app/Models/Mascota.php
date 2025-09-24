<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mascota extends Model
{
    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $fillable = [
        'id_cliente',
        'id_raza',
        'nombre_mascota',
        'fecha_nacimiento',
        'sexo',
        'color_primario',
        'peso_actual',
        'observacion',
        'fecha_registro',
        'fecha_actualizacion',
    ];

    public function cliente()
    {
        return $this->belongsTo(Clientes::class, "id_cliente", "id");
    }

    public function raza()
    {
        return $this->belongsTo(Raza::class, "id_raza", "id");
    }
}
