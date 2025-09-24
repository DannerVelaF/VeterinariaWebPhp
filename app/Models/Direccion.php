<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $table = 'direcciones';
    protected $fillable = [
        'zona',
        'tipo_calle',
        'nombre_calle',
        'numero',
        'codigo_postal',
        'referencia',
        'codigo_ubigeo',
        'fecha_registro',
        'fecha_actualizacion',
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function ubigeo()
    {
        return $this->belongsTo(Ubigeo::class, "codigo_ubigeo", "codigo_ubigeo");
    }
}
