<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mascota extends Model
{
    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';
    protected $primaryKey = 'id_mascota';
    protected $table = 'mascotas';
    protected $fillable = [
        'id_mascota',
        'id_cliente',
        'id_raza',
        'nombre_mascota',
        'fecha_nacimiento',
        'sexo',
        'color_primario',
        'peso_actual',
        'observacion',
        'estado',
        'fecha_registro',
        'fecha_actualizacion',
    ];

    public function cliente()
    {
        return $this->belongsTo(Clientes::class, "id_cliente");
    }

    public function raza()
    {
        return $this->belongsTo(Raza::class, "id_raza");
    }
}
