<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unidades extends Model
{
    protected $fillable = [
        "nombre"
    ];

    public function productos()
    {
        return $this->hasMany(Producto::class, 'id_unidad', 'id');
    }
}
