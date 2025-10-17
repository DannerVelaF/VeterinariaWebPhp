<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoCompras extends Model
{
    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';
    protected $primaryKey = 'id_estado_compra'; // Llave primaria
    protected $fillable = ['id_estado_compra', 'nombre_estado_compra', 'fecha_registro', 'fecha_actualizacion'];

    public function compras()
    {
        return $this->hasMany(Compra::class, 'id_estado_compra');
    }
}
