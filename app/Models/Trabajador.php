<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trabajador extends Model
{

    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';
    protected $primaryKey = 'id_trabajador';
    protected $table = 'trabajadores';

    protected $fillable = [
        'id_trabajador',
        'fecha_ingreso',
        'fecha_salida',
        'salario',
        'numero_seguro_social',
        'id_puesto_trabajo',
        'id_estado_trabajador',
        "id_persona",
        'fecha_registro',
        'fecha_actualizacion',
    ];


    public function puestoTrabajo()
    {
        return $this->belongsTo(PuestoTrabajador::class, "id_puesto_trabajo");
    }

    public function estadoTrabajador()
    {
        return $this->belongsTo(EstadoTrabajadores::class, "id_estado_trabajador");
    }
    public function persona()
    {
        return $this->belongsTo(Persona::class, "id_persona");
    }

    public function compras()
    {
        return $this->hasMany(Compra::class, "id_trabajador");
    }
}
