<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Turnos extends Model
{
    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';
    protected $primaryKey = 'id_turno';
    protected $fillable = [
        "id_turno",
        "nombre_turno",
        "descripcion",
        "estado",
        "fecha_registro",
        "fecha_actualizacion",
    ];

    public function horarios()
    {
        return $this->hasMany(TurnoHorario::class, 'id_turno', 'id_turno');
    }

    public function trabajadores()
    {
        return $this->belongsToMany(Trabajador::class, 'trabajador_turnos', 'id_turno', 'id_trabajador')
            ->withPivot('fecha_inicio', 'fecha_fin')
            ->withTimestamps();
    }
}
