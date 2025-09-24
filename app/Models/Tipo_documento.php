<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tipo_documento extends Model
{

    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $table = 'tipo_documentos';

    protected $fillable = [
        'nombre',
        'fecha_registro',
        'fecha_actualizacion',
    ];

    public function personas()
    {
        return $this->hasMany(Persona::class, "id_tipo_documento", "id");
    }
}
