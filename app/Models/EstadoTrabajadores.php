<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoTrabajadores extends Model
{
    protected $table = 'estado_trabajadores';

    protected $fillable = [
        'nombre',
    ];

    public function trabajadores()
    {
        return $this->hasMany(Trabajador::class);
    }
}
