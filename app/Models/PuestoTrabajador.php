<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PuestoTrabajador extends Model
{
    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $table = 'puesto_trabajadores'; // opcional, ya no sería necesario

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
        'fecha_registro',
        'fecha_actualizacion',
    ];

    public function trabajadores()
    {
        return $this->hasMany(Trabajador::class, "id_puesto_trabajo", "id");
    }
}
