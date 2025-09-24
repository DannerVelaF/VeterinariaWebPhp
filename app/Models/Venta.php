<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $fillable = [
        "id_cliente",
        "id_trabajador",
        "codigo",
        "numero_factura",
        "fecha_venta",
        "fecha_actualizacion",
        "estado",
        "cantidad_total",
        "total",
        "observacion",
        "id_usuario_aprobador",
        "created_at",
        "updated_at",
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, "id_cliente", "id");
    }

    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class, "id_trabajador", "id");
    }

    public function detalleVenta()
    {
        return $this->hasMany(DetalleVenta::class, 'id_venta', 'id');
    }

    public function usuarioAprobador()
    {
        return $this->belongsTo(User::class, "id_usuario_aprobador", "id");
    }
}