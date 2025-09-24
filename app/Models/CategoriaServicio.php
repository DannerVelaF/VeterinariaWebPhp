<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaServicio extends Model
{
    // Para desactivar los timestamps por defecto
    public $timestamps = true;

    // Decirle a Laravel qué columnas usar en lugar de created_at y updated_at
    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $fillable = [
        'nombre_categoria',
        'descripcion',
        'estado',
        'fecha_registro',
        'fecha_actualizacion',
    ];

    public function servicios()
    {
        return $this->hasMany(Servicio::class);
    }
}
