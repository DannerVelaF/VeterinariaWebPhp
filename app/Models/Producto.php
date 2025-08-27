<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = [
        'nombre_producto',
        'descripcion',
        'precio_unitario',
        'stock',
        'estado',
        'codigo_barras',
        'id_categoria_producto',
        'id_proveedor',
        "created_at",
        "updated_at",
    ];

    public function categoria_producto()
    {
        return $this->belongsTo(CategoriaProducto::class, 'id_categoria_producto', 'id');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor', 'id');
    }
}
