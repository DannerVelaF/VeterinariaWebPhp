<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetodoPago extends Model
{

    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $fillable = [
        'nombre_metodo',
        'observacion',
        'fecha_registro',
        'fecha_actualizacion',
    ];

    public function transaccionPagos()
    {
        return $this->hasMany(TransaccionPago::class);
    }
}
