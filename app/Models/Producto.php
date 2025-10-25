<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';
    protected $primaryKey = 'id_producto';
    protected $fillable = [
        'id_producto',
        "ruta_imagen",
        'nombre_producto',
        'descripcion',
        'estado',
        'codigo_barras',
        'id_categoria_producto',
        'id_proveedor',
        "precio_unitario",
        'id_unidad',
        'fecha_registro',
        'fecha_actualizacion',
    ];

    public function categoria_producto()
    {
        return $this->belongsTo(CategoriaProducto::class, "id_categoria_producto");
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, "id_proveedor");
    }

    public function unidad()
    {
        return $this->belongsTo(Unidades::class, "id_unidad");
    }
    public function lotes()
    {
        return $this->hasMany(Lotes::class, 'id_producto', 'id_producto');
    }
}
