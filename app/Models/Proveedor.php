<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedores';
    protected $fillable = [
        'nombre',
        'ruc',
        'correo',
        'pais',
        'estado',
        'id_direccion',
        'telefono_contacto',
        'telefono_secundario',
        'correo_electronico_encargado',
        'correo_electronico_empresa',
    ];

    public function direccion()
    {
        return $this->belongsTo(Direccion::class);
    }

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }

    public function compras()
    {
        return $this->hasMany(Compra::class);
    }
}
