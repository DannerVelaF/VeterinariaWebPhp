<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoVentas extends Model
{
    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $table = 'estado_ventas_fisicas';
    protected $primaryKey = 'id_estado_venta_fisica';
    protected $fillable = [
        'id_estado_venta_fisica',
        'nombre_estado_venta_fisica',
        'fecha_registro',
        'fecha_actualizacion',
    ];

    public function ventas()
    {
        return $this->hasMany(Ventas::class, 'id_estado_venta', 'id_estado_venta_fisica');
    }
}
