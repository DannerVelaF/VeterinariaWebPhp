<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    protected $table = 'direcciones';
    protected $fillable = [
        'zona',
        'tipo_calle',
        'nombre_calle',
        'numero',
        'codigo_postal',
        'referencia',
        'codigo_ubigeo',
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
