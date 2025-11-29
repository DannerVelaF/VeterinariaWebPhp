<?php

namespace App\Livewire\Ventas;

use App\Models\Caja;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CajaGestion extends Component
{
    public $montoInicial = 0;
    public $observacionesApertura = '';

    // Montos para el cierre
    public $montoFinal = 0;
    public $montoTarjetas = 0;
    public $montoTransferencias = 0;
    public $montoDigital = 0; // Yape, Plin, etc.
    public $observacionesCierre = '';
    public $cajaActual = null;

    protected $rules = [
        'montoInicial' => 'required|numeric|min:0',
        'observacionesApertura' => 'nullable|string|max:500'
    ];

    public function mount()
    {
        $this->cargarCajaActual();
    }

    public function cargarCajaActual()
    {
        $this->cajaActual = Caja::with(['trabajador.persona', 'ventas'])
            ->where('id_trabajador', Auth::user()->persona->trabajador->id_trabajador)
            ->abierta()
            ->first();

        // Recalcular totales cada vez que se carga la caja
        if ($this->cajaActual) {
            $this->cajaActual->calcularTotales();

            // ✅ INICIALIZAR LOS MONTOS CON LOS VALORES ACTUALES DE LA CAJA
            $this->montoTarjetas = $this->cajaActual->ventas_tarjeta;
            $this->montoTransferencias = $this->cajaActual->ventas_transferencia;
            $this->montoDigital = $this->cajaActual->ventas_digital;
        }
    }

    public function abrirCaja()
    {
        $this->validate();

        // Verificar si ya hay una caja abierta
        $cajaAbierta = Caja::where('id_trabajador', Auth::user()->persona->trabajador->id_trabajador)
            ->abierta()
            ->exists();

        if ($cajaAbierta) {
            $this->addError('caja', 'Ya tienes una caja abierta.');
            return;
        }

        try {
            $caja = Caja::create([
                'id_trabajador' => Auth::user()->persona->trabajador->id_trabajador,
                'monto_inicial' => $this->montoInicial,
                'observaciones' => $this->observacionesApertura,
                'estado' => 'abierta',
                'fecha_apertura' => now(),
            ]);

            $this->cajaActual = $caja;
            $this->reset(['montoInicial', 'observacionesApertura']);

            $this->dispatch('notify',
                title: 'Caja Abierta',
                description: 'Caja abierta correctamente',
                type: 'success'
            );
            $this->dispatch('cajaAbierta');

        } catch (\Exception $e) {
            $this->addError('caja', 'Error al abrir la caja: ' . $e->getMessage());
        }
    }

    public function cerrarCaja()
    {
        if (!$this->cajaActual) {
            $this->addError('caja', 'No hay caja abierta para cerrar.');
            return;
        }

        $ventasPendientes = $this->cajaActual->ventas()
            ->whereHas('estadoVenta', function ($query) {
                $query->where('nombre_estado_venta_fisica', 'pendiente');
            })
            ->exists();
        if ($ventasPendientes) {
            $this->addError('caja', 'No puedes cerrar caja con ventas pendientes.');
            return;
        }

        // Recalcular totales primero
        $this->cajaActual->calcularTotales();

        // ✅ VALIDACIÓN
        $this->validate([
            'montoFinal' => 'required|numeric|min:0',
            'montoTarjetas' => 'required|numeric|min:0',
            'montoTransferencias' => 'required|numeric|min:0',
            'montoDigital' => 'required|numeric|min:0',
            'observacionesCierre' => 'nullable|string|max:500'
        ]);

        try {
            // 1. CÁLCULOS DE DIFERENCIAS (Preparamos los datos antes de la transacción)

            // Efectivo
            $totalEfectivoEsperado = $this->cajaActual->monto_inicial + $this->cajaActual->ventas_efectivo;
            $diferenciaEfectivo = $this->montoFinal - $totalEfectivoEsperado;

            // Digitales (para el mensaje)
            $diferenciasDigitales = [];

            $diferenciaTarjetas = $this->montoTarjetas - $this->cajaActual->ventas_tarjeta;
            if ($diferenciaTarjetas != 0) $diferenciasDigitales[] = "Tarjetas: " . number_format($diferenciaTarjetas, 2);

            $diferenciaTransferencias = $this->montoTransferencias - $this->cajaActual->ventas_transferencia;
            if ($diferenciaTransferencias != 0) $diferenciasDigitales[] = "Transferencias: " . number_format($diferenciaTransferencias, 2);

            $diferenciaDigital = $this->montoDigital - $this->cajaActual->ventas_digital;
            if ($diferenciaDigital != 0) $diferenciasDigitales[] = "Digital: " . number_format($diferenciaDigital, 2);

            // Total General
            $totalSistema = $this->cajaActual->monto_inicial + $this->cajaActual->total_ventas;
            $totalReal = $this->montoFinal + $this->montoTarjetas + $this->montoTransferencias + $this->montoDigital;
            $diferenciaTotal = $totalReal - $totalSistema;

            // Observaciones
            $observacionesDetalladas = $this->cajaActual->observaciones .
                ($this->cajaActual->observaciones ? "\n" : "") .
                "CIERRE:\n" .
                "• Efectivo contado: S/ " . number_format($this->montoFinal, 2) . " (Diferencia: S/ " . number_format($diferenciaEfectivo, 2) . ")\n" .
                "• Tarjetas reportadas: S/ " . number_format($this->montoTarjetas, 2) . " (Diferencia: S/ " . number_format($diferenciaTarjetas, 2) . ")\n" .
                "• Transferencias reportadas: S/ " . number_format($this->montoTransferencias, 2) . " (Diferencia: S/ " . number_format($diferenciaTransferencias, 2) . ")\n" .
                "• Digitales reportados: S/ " . number_format($this->montoDigital, 2) . " (Diferencia: S/ " . number_format($diferenciaDigital, 2) . ")\n" .
                "• Diferencia total: S/ " . number_format($diferenciaTotal, 2) . "\n" .
                "Observaciones: " . $this->observacionesCierre;

            // 2. TRANSACCIÓN DE BASE DE DATOS
            DB::transaction(function () use ($diferenciaTotal, $observacionesDetalladas) {
                $this->cajaActual->update([
                    'monto_final' => $this->montoFinal,
                    'ventas_tarjeta' => $this->montoTarjetas,
                    'ventas_transferencia' => $this->montoTransferencias,
                    'ventas_digital' => $this->montoDigital,
                    'diferencia' => $diferenciaTotal,
                    'observaciones' => $observacionesDetalladas,
                    'estado' => 'cerrada',
                    'fecha_cierre' => now(),
                ]);

                // Opcional: Si tuvieras que bloquear ventas o actualizar stock crítico, iría aquí.
            });

            // 3. EVENTOS Y NOTIFICACIONES (Solo si la transacción tuvo éxito)
            $mensaje = 'Caja cerrada correctamente. ';
            if ($diferenciaTotal == 0) {
                $mensaje .= '✅ Todo cuadra perfectamente.';
            } else {
                $mensaje .= '⚠️ Hay diferencias: S/ ' . number_format($diferenciaTotal, 2);
                if (!empty($diferenciasDigitales)) {
                    $mensaje .= ' (' . implode(', ', $diferenciasDigitales) . ')';
                }
            }

            $this->dispatch('notify',
                title: 'Caja Cerrada',
                description: $mensaje,
                type: $diferenciaTotal == 0 ? 'success' : 'warning'
            );

            $this->dispatch('cajaCerrada');
            $this->cargarCajaActual();
            $this->reset(['montoFinal', 'montoTarjetas', 'montoTransferencias', 'montoDigital', 'observacionesCierre']);

        } catch (\Exception $e) {
            $this->addError('caja', 'Error al cerrar la caja: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.ventas.caja-gestion');
    }
}
