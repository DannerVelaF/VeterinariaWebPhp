<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoDirecto extends Model
{
    protected $fillable = [
        'tipo',
        'observaciones',
    ];

    protected $table = 'movimientos_directos';
}