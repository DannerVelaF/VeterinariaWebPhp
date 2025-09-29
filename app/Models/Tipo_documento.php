<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tipo_documento extends Model
{

    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';
    protected $primaryKey = 'id_tipo_documento';
    protected $table = 'tipo_documentos';

    protected $fillable = [
        'id_tipo_documento',
        'nombre_tipo_documento',
        'fecha_registro',
        'fecha_actualizacion',
    ];

    public function personas()
    {
        return $this->hasMany(Persona::class, "id_tipo_documento");
    }
}
