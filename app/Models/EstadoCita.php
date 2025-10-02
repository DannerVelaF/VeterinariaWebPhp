<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoCita extends Model
{
    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $table = 'estado_citas';
    protected $primaryKey = 'id_estado_cita';
    protected $fillable = [
        'id_estado_cita',
        'nombre_estado_cita',
        'fecha_registro',
        'fecha_actualizacion',
    ];

    public function citas()
    {
        return $this->hasMany(Cita::class, "id_cita");
    }
}
