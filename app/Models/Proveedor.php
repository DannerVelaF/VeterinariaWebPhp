<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedores';
    protected $fillable = [
        'nombre',
        'ruc',
        'telefono',
        'correo',
        'pais',
        'estado',
    ];

    public function direccion()
    {
        return $this->belongsTo(Direccion::class);
    }
}
