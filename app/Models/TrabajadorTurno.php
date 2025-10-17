<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrabajadorTurno extends Model
{
    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';
    protected $primaryKey = 'id_trabajador_turno'; // Llave primaria
    protected $fillable = ['id_turno', 'dia_semana', 'hora_inicio', 'hora_fin', 'es_descanso', 'fecha_registro', 'fecha_actualizacion'];

    public function turno()
    {
        return $this->belongsTo(Turnos::class, 'id_turno');
    }
}
