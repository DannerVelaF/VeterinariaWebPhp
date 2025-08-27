<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{

    protected $table = 'personas';

    protected $fillable = [
        "numero_documento",
        'nombre',
        "apellido_paterno",
        "apellido_materno",
        "fecha_nacimiento",
        "sexo",
        "nacionalidad",
        "correo_electronico_personal",
        "correo_electronico_secundario",
        "numero_telefono_personal",
        "numero_telefono_secundario",
        "id_tipo_documento",
        "id_direccion",
    ];


    public function tipo_documento()
    {
        return $this->belongsTo(Tipo_documento::class, "id_tipo_documento", "id");
    }

    public function direccion()
    {
        return $this->belongsTo(Direccion::class, "id_direccion", "id");
    }

    public function trabajador()
    {
        return $this->hasOne(Trabajador::class, 'id_persona', 'id');
    }
}
