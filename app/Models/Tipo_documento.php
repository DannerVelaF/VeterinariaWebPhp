<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tipo_documento extends Model
{

    protected $table = 'tipo_documentos';

    protected $fillable = [
        'nombre',
    ];

    public function personas()
    {
        return $this->hasMany(Persona::class, "id_tipo_documento", "id");
    }
}
