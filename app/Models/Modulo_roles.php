<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modulo_roles extends Model
{
    public $timestamps = true;
    public $primaryKey = 'id_Modulo_roles';
    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $fillable = [
        'id_Modulo_roles',
        'id_modulo',
        'id_rol',
        'fecha_registro',
        'fecha_actualizacion',
    ];

    public function modulo()
    {
        return $this->belongsTo(Modulo::class, "id_modulo");
    }

    public function rol()
    {
        return $this->belongsTo(Roles::class, "id_rol");
    }
}
