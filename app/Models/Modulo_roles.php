<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modulo_roles extends Model
{
    public $timestamps = true;
    public $table = 'modulo_roles';
    public $primaryKey = 'id_modulo_roles';
    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $fillable = [
        'id_modulo_roles',
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
