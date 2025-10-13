<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';
    protected $primaryKey = 'id_persona';
    protected $table = 'personas';

    protected $fillable = [
        'id_persona',
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
        "fecha_registro",
        "fecha_actualizacion",
    ];


    public function tipo_documento()
    {
        return $this->belongsTo(Tipo_documento::class, "id_tipo_documento");
    }

    public function direccion()
    {
        return $this->belongsTo(Direccion::class, "id_direccion");
    }

    public function trabajador()
    {
        return $this->hasOne(Trabajador::class, "id_persona");
    }
    public function user()
    {
        return $this->hasOne(User::class, 'id_persona', 'id_persona');
    }
}
