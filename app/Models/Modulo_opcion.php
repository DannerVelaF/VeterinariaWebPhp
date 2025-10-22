<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modulo_opcion extends Model
{
    public $timestamps = true;
    public $table = 'modulo_opciones';
    public $primaryKey = 'id_modulo_opcion';
    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $fillable = [
        'id_modulo_opcion',
        'nombre_opcion',
        'estado',
        'id_modulo',
        'ruta_laravel',
        'orden',
        "id_opcion_padre",
        'id_usuario_registro',
        'id_permiso',
        'fecha_registro',
        'fecha_actualizacion',
    ];

    public function modulo()
    {
        return $this->belongsTo(Modulo::class, "id_modulo");
    }

    public function permiso()
    {
        return $this->belongsTo(Permiso::class, "id_permiso");
    }

    public function subopciones()
    {
        return $this->hasMany(Modulo_opcion::class, 'id_opcion_padre')->with('subopciones');
    }
    public function padre()
    {
        return $this->belongsTo(Modulo_opcion::class, 'id_opcion_padre');
    }
}
