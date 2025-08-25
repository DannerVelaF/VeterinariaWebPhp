<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{

    protected $table = 'personas';

    protected $fillable = [
        'nombre',
        "apellido_paterno",
        "apellido_materno",
        "fecha_nacimiento",
        "sexo",
        "correo",
        "nacionalidad",
        "fecha registro"
    ];


    public function tipo_documento()
    {
        return $this->belongsTo(Tipo_documento::class, "id_tipo_documento", "id");
    }
}
