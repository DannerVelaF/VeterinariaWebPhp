<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PuestoTrabajador extends Model
{
    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';
    protected $primaryKey = 'id_puesto_trabajo';
    protected $table = 'puesto_trabajadores'; // opcional, ya no sería necesario

    protected $fillable = [
        'id_puesto_trabajo',
        'nombre_puesto',
        'descripcion',
        'estado',
        'fecha_registro',
        'fecha_actualizacion',
    ];

    public function trabajadores()
    {
        return $this->hasMany(Trabajador::class, "id_trabajador");
    }
}
