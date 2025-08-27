<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaProducto extends Model
{
    protected $table = 'categoria__productos';

    protected $fillable = [
        'nombre',
        'descripccion',
        'estado',
    ];

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}
