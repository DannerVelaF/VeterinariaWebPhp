<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trabajador extends Model
{
    protected $table = 'trabajadores';


    protected $fillable = [
        'fecha_ingreso',
        'fecha_salida',
        'salario',
        'numero_seguro_social',
        'id_puesto_trabajo',
        'id_estado_trabajador',
    ];


    public function puestoTrabajo()
    {
        return $this->BelongsTo(PuestoTrabajador::class, "id_puesto_trabajo", "id");
    }

    public function estadoTrabajador()
    {
        return $this->belongsTo(EstadoTrabajadores::class, "id_estado_trabajador", "id");
    }
    public function persona()
    {
        return $this->belongsTo(Persona::class, "id_persona", "id");
    }
}
