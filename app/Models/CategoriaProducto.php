<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaProducto extends Model
{

    public $timestamps = true;
    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $table = 'categoria_productos';

    protected $fillable = [
        'nombre_categoria',
        'descripcion',
        'estado',
        'fecha_registro',
        'fecha_actualizacion',
    ];

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}
