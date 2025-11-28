<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    use HasFactory;

    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $table = 'cajas';
    protected $primaryKey = 'id_caja';

    protected $fillable = [
        'id_trabajador',
        'monto_inicial',
        'monto_final',
        'ventas_efectivo',
        'ventas_tarjeta',
        'ventas_transferencia',
        'ventas_digital',
        'total_ventas',
        'diferencia',
        'estado',
        'observaciones',
        'fecha_apertura',
        'fecha_cierre'
    ];

    protected $casts = [
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
    ];

    // Relaciones
    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class, 'id_trabajador');
    }

    public function ventas()
    {
        return $this->hasMany(Ventas::class, 'id_caja');
    }

    // Scope para caja abierta
    public function scopeAbierta($query)
    {
        return $query->where('estado', 'abierta');
    }

    // Método para calcular totales CORREGIDO
    public function calcularTotales()
    {
        // Esto evita repetir el código del whereHas
        $sumarPorTipo = function ($tipo) {
            return $this->ventas()
                ->whereHas('transaccionPago.metodoPago', function ($query) use ($tipo) {
                    $query->where('tipo_metodo', $tipo);
                })
                // Aseguramos que solo sume ventas completadas/pagadas si manejas estados
                // ->where('estado', 'completado')
                ->sum('total');
        };

        // Ejecutamos las consultas optimizadas (SUM directo en SQL)
        $this->ventas_efectivo = $sumarPorTipo('efectivo');
        $this->ventas_tarjeta = $sumarPorTipo('tarjeta');
        $this->ventas_transferencia = $sumarPorTipo('transferencia');
        $this->ventas_digital = $sumarPorTipo('digital');

        // Calcular total general
        $this->total_ventas = $this->ventas_efectivo +
            $this->ventas_tarjeta +
            $this->ventas_transferencia +
            $this->ventas_digital;

        $this->save();
    }

    // Accessor para el total esperado
    public function getTotalEsperadoAttribute()
    {
        return $this->monto_inicial + $this->total_ventas;
    }

    // Accessor para la diferencia
    public function getDiferenciaCalculadaAttribute()
    {
        if ($this->monto_final) {
            return $this->monto_final - $this->total_esperado;
        }
        return 0;
    }
}
