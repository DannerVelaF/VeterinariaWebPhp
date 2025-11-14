<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaccionPago extends Model
{
    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';
    protected $primaryKey = 'id_transaccion_pago';

    protected $fillable = [
        "id_transaccion_pago",
        "id_venta",
        "id_metodo_pago", // 🔹 Cambiar a id_metodo_pago para consistencia
        "monto",
        "referencia",
        "estado",
        "fecha_pago", // 🔹 Nueva: fecha cuando se realizó el pago
        "comprobante_url", // 🔹 Nueva: URL del comprobante subido
        "datos_adicionales", // JSON con info adicional
        "fecha_registro",
        "fecha_actualizacion",
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_pago' => 'datetime',
        'datos_adicionales' => 'array', // 🔹 Cast a array para JSON
    ];

    // Estados posibles
    const ESTADO_PENDIENTE = 'pendiente';
    const ESTADO_COMPLETADO = 'completado';
    const ESTADO_FALLIDO = 'fallido';
    const ESTADO_REFUNDIDO = 'refundido';

    public function venta()
    {
        return $this->belongsTo(Ventas::class, "id_venta");
    }

    public function metodoPago() // 🔹 Cambiar nombre para consistencia
    {
        return $this->belongsTo(MetodoPago::class, "id_metodo_pago");
    }

    // Scopes útiles
    public function scopePendientes($query)
    {
        return $query->where('estado', self::ESTADO_PENDIENTE);
    }

    public function scopeCompletados($query)
    {
        return $query->where('estado', self::ESTADO_COMPLETADO);
    }

    // Helper methods
    public function estaPendiente()
    {
        return $this->estado === self::ESTADO_PENDIENTE;
    }

    public function estaCompletado()
    {
        return $this->estado === self::ESTADO_COMPLETADO;
    }

    public function marcarComoCompletado($referencia = null, $comprobanteUrl = null)
    {
        $this->update([
            'estado' => self::ESTADO_COMPLETADO,
            'fecha_pago' => now(),
            'referencia' => $referencia ?? $this->referencia,
            'comprobante_url' => $comprobanteUrl ?? $this->comprobante_url,
        ]);
    }
}
