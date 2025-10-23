<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrabajadorTurno extends Model
{
    protected $table = 'trabajador_turnos';
    protected $primaryKey = 'id_trabajador_turno';
    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $fillable = [
        'id_trabajador',
        'id_turno',
        'fecha_inicio',
        'fecha_fin',
    ];

    public function turno()
    {
        return $this->belongsTo(Turnos::class, 'id_turno');
    }

    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class, 'id_trabajador');
    }
}
