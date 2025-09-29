<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';
    protected $primaryKey = 'id_servicio';
    protected $fillable = [
        'id_servicio',
        'nombre_servicio',
        'descripcion',
        'duracion_estimada',
        'precio_unitario',
        'estado',
        'id_categoria_servicio',
        'fecha_registro',
        'fecha_actualizacion',
    ];

    public function categoriaServicio()
    {
        return $this->belongsTo(CategoriaServicio::class, "id_categoria_servicio");
    }
}
