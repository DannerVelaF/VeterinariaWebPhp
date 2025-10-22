<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modulo extends Model
{
    public $timestamps = true;
    public $primaryKey = 'id_modulo';
    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $fillable = [
        'id_modulo',
        'nombre_modulo',
        'estado',
        'id_usuario_registro',
        'id_rol',
        'fecha_registro',
        'fecha_actualizacion',
    ];

    public function opciones()
    {
        return $this->hasMany(Modulo_opcion::class, 'id_modulo')
            ->whereNull('id_opcion_padre') // solo las principales
            ->with('subopciones');
    }

    public function roles()
    {
        return $this->belongsToMany(
            Roles::class,
            'Modulo_roles',
            'id_modulo',
            'id_rol'
        )->withTimestamps();
    }
}
