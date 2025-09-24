<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clientes extends Model
{
    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $fillable = [
        "id_persona",
        "fecha_registro",
        "fecha_actualizacion",
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class, "id_persona", "id");
    }

    public function compras()
    {
        return $this->hasMany(Compra::class);
    }
}
