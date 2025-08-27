<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PuestoTrabajador extends Model
{
    protected $table = 'puesto_trabajadores'; // opcional, ya no sería necesario

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
    ];

    public function trabajadores()
    {
        return $this->hasMany(Trabajador::class, "id_puesto_trabajo", "id");
    }
}
