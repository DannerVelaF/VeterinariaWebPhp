<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{

    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';
    protected $primaryKey = 'id_compra';
    protected $fillable = [
        "id_compra",
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
        return $this->belongsTo(Proveedor::class, "id_proveedor");
    }
    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class, "id_trabajador", "id_trabajador");
    }
    public function detalleCompra()
    {
        return $this->hasMany(DetalleCompra::class, "id_compra");
    }

    public function usuarioAprobador()
    {
        return $this->belongsTo(User::class, "id_usuario_aprobador", "id_usuario");
    }

    public function estadoCompra()
    {
        return $this->belongsTo(EstadoCompras::class, "id_estado_compra");
    }
}
