<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ubigeo extends Model
{
    protected $table = 'ubigeos';
    protected $primaryKey = 'codigo_ubigeo';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'codigo_ubigeo',
        'departamento',
        'provincia',
        'distrito',
    ];

    public function direccion()
    {
        return $this->hasMany(Direccion::class, "codigo_ubigeo", "codigo_ubigeo");
    }
}
