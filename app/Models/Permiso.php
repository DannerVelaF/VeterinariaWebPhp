<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    protected $table = 'permisos';
    protected $primaryKey = 'id_permiso';
    public $timestamps = true;

    // Decirle a Laravel qué columnas usar en lugar de created_at y updated_at
    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $fillable = [
        'id_permiso',
        'nombre_permiso',
        'fecha_registro',
        'fecha_actualizacion',
    ];

    public function roles()
    {
        return $this->belongsToMany(Roles::class, 'roles_permisos', 'id_permiso', 'id_rol');
    }
}
