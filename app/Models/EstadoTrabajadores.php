<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoTrabajadores extends Model
{
    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $table = 'estado_trabajadores';
    protected $primaryKey = 'id_estado_trabajador';
    protected $fillable = [
        'id_estado_trabajador',
        'nombre_estado_trabajador',
        'fecha_registro',
        'fecha_actualizacion',
    ];

    public function trabajadores()
    {
        return $this->hasMany(Trabajador::class, "id_trabajador");
    }
}
