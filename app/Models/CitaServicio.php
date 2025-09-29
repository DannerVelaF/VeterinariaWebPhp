<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CitaServicio extends Model
{
    // Para desactivar los timestamps por defecto
    public $timestamps = true;

    // Decirle a Laravel qué columnas usar en lugar de created_at y updated_at
    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';
    protected $primaryKey = 'id_cita_servicio';

    protected $fillable = [
        'id_cita_servicio',
        'id_cita',
        'id_servicio',
        'precio_aplicado',
        'cantidad',
        'diagnostico',
        'medicamentos',
        'recomendaciones',
        'fecha_registro',
        'fecha_actualizacion',
    ];

    public function cita()
    {
        return $this->belongsTo(Cita::class, "id_cita");
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, "id_servicio");
    }
}
