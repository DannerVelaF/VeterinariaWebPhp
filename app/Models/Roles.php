<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';
    protected $primaryKey = 'id_rol';
    protected $fillable = [
        'id_rol',
        'nombre_rol',
        'guardia',
        'fecha_registro',
        'fecha_actualizacion',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
    public function permisos()
    {
        return $this->belongsToMany(Permiso::class, 'roles_permisos', 'id_rol', 'id_permiso');
    }

    public function modulos()
    {
        return $this->belongsToMany(
            Modulo::class,
            'modulo_roles',
            'id_rol',      // FK en tabla pivote hacia roles
            'id_modulo'    // FK en tabla pivote hacia modulos
        )->withTimestamps();
    }
}
