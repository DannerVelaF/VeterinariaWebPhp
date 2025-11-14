<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetodoPago extends Model
{
    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';
    protected $primaryKey = 'id_metodo_pago';

    protected $fillable = [
        'id_metodo_pago',
        'nombre_metodo',
        'tipo_metodo',
        'numero_cuenta',
        'nombre_titular',
        'entidad_financiera',
        'tipo_cuenta',
        'codigo_qr',
        'instrucciones',
        'estado', // 🔹 Usamos estado en lugar de activo
        'orden',
        'observacion',
        'fecha_registro',
        'fecha_actualizacion',
    ];

    protected $casts = [
        // No necesitamos cast para 'activo' ya que usamos 'estado'
    ];

    // Estados posibles
    const ESTADO_ACTIVO = 'activo';
    const ESTADO_INACTIVO = 'inactivo';

    public function transaccionPagos()
    {
        return $this->hasMany(TransaccionPago::class, "id_metodo_pago");
    }

    // Scope para métodos activos
    public function scopeActivos($query)
    {
        return $query->where('estado', self::ESTADO_ACTIVO);
    }

    // Scope para métodos inactivos
    public function scopeInactivos($query)
    {
        return $query->where('estado', self::ESTADO_INACTIVO);
    }

    // Scope ordenados
    public function scopeOrdenados($query)
    {
        return $query->orderBy('orden')->orderBy('nombre_metodo');
    }

    // Helper methods
    public function estaActivo()
    {
        return $this->estado === self::ESTADO_ACTIVO;
    }

    public function estaInactivo()
    {
        return $this->estado === self::ESTADO_INACTIVO;
    }

    public function activar()
    {
        $this->update(['estado' => self::ESTADO_ACTIVO]);
    }

    public function desactivar()
    {
        $this->update(['estado' => self::ESTADO_INACTIVO]);
    }
}
