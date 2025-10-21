<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Roles_permisos extends Model
{

    public $timestamps = true;
    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $fillable = [
        'id_rol',
        'id_permiso',
    ];

    public function role()
    {
        return $this->belongsTo(Roles::class, 'id_rol');
    }

    public function permiso()
    {
        return $this->belongsTo(Permiso::class, 'id_permiso');
    }
}
