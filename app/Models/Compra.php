<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $fillable = [
        "id_proveedor",
        "id_trabajador",
        "codigo",
        "numero_factura",
        "fecha_compra",
        "fecha_actualizacion",
        "estado",
        "cantidad_total",
        "total",
        "observacion",
        "id_usuario_aprobador",
        "created_at",
        "updated_at",
    ];


    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, "id_proveedor", "id");
    }
    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class, "id_trabajador", "id");
    }
    public function detalleCompra()
    {
        return $this->hasMany(DetalleCompra::class, 'id_compra', 'id');
    }

    public function usuarioAprobador()
    {
        return $this->belongsTo(User::class, "id_usuario_aprobador", "id");
    }
}
