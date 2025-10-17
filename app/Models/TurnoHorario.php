<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TurnoHorario extends Model
{
    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';
    protected $primaryKey = 'id_turno_horario';
    protected $fillable = [
        "id_turno_horario",
        "dia_semana",
        "hora_inicio",
        "hora_fin",
        "es_descanso",
        "id_turno",
        "fecha_registro",
        "fecha_actualizacion",
    ];

    public function turno()
    {
        return $this->belongsTo(Turnos::class, 'id_turno');
    }
}
