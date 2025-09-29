<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    // Para desactivar los timestamps por defecto
    public $timestamps = true;

    // Decirle a Laravel qué columnas usar en lugar de created_at y updated_at
    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';
    protected $primaryKey = 'id_cita';
    protected $fillable = [
        'id_cita',
        'fecha_programada',
        'motivo',
        'estado',
        'id_cliente',
        'id_trabajador_asignado',
        'id_mascota',
        'fecha_registro',
        'fecha_actualizacion',
    ];

    public function cliente()
    {
        return $this->belongsTo(Clientes::class, "id_cliente");
    }

    public function trabajadorAsignado()
    {
        return $this->belongsTo(Trabajador::class, "id_trabajador_asignado", "id_trabajador");
    }

    public function mascota()
    {
        return $this->belongsTo(Mascota::class, "id_mascota");
    }
}
