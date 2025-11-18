<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{

    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';
    protected $primaryKey = 'id_proveedor';
    protected $table = 'proveedores';
    protected $fillable = [
        'id_proveedor',
        'nombre_proveedor',
        'ruc',
        'correo',
        'pais',
        'estado',
        'id_direccion',
        'telefono_contacto',
        'telefono_secundario',
        'correo_electronico_encargado',
        'correo_electronico_empresa',
        'fecha_registro',
        'fecha_actualizacion',
    ];

    public function direccion()
    {
        return $this->belongsTo(Direccion::class, "id_direccion");
    }

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'producto_proveedores', 'id_proveedor', 'id_producto')
            ->withTimestamps();
    }

    public function compras()
    {
        return $this->hasMany(Compra::class, "id_proveedor");
    }
}
