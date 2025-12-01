<?php

namespace App\Livewire\Inventario;

use App\Exports\DespachoDetalleExport;
use App\Models\EnvioPedido;
use App\Models\EstadoEnvioPedido;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

// 1. Importar Trait para archivos
use Maatwebsite\Excel\Facades\Excel;

class EntregaPedido extends Component
{
    use WithFileUploads;

    // 2. Usar el Trait

    public $mesActual;
    public $anioActual;

    // Para el modal de detalle del día
    public $modalDiaOpen = false;
    public $diaSeleccionado = null;
    public $pedidosDelDia = [];

    // --- VARIABLES PARA REGISTRAR EVIDENCIA (NUEVO) ---
    public $modalEvidenciaOpen = false;
    public $pedidoEvidenciaId = null;
    public $fotoEvidencia;
    public $observacionEntrega;

    public $evidenciaPedido = null;

    // --------------------------------------------------

    public function mount()
    {
        $this->establecerFechaActual();
    }

    public function establecerFechaActual()
    {
        $now = Carbon::now();
        $this->mesActual = $now->month;
        $this->anioActual = $now->year;
    }

    public function irHoy()
    {
        $this->establecerFechaActual();
    }

    public function cambiarMes($direccion)
    {
        $fecha = Carbon::createFromDate($this->anioActual, $this->mesActual, 1);

        if ($direccion === 'sig') {
            $fecha->addMonth();
        } else {
            $fecha->subMonth();
        }

        $this->mesActual = $fecha->month;
        $this->anioActual = $fecha->year;
    }

    public function seleccionarDia($dia)
    {
        $fechaObj = Carbon::createFromDate($this->anioActual, $this->mesActual, $dia);

        // Obtener pedidos para verificar si hay algo que mostrar
        $this->cargarPedidosDelDia($fechaObj);

        // VALIDACIÓN: Si no hay pedidos, NO abrimos el modal
        if ($this->pedidosDelDia->isEmpty()) {
            return;
        }

        $this->diaSeleccionado = $fechaObj;
        $this->modalDiaOpen = true;
    }

    // Método auxiliar para recargar los pedidos (útil tras guardar evidencia)
    public function cargarPedidosDelDia($fechaObj)
    {
        $this->pedidosDelDia = EnvioPedido::with(['venta.cliente.persona', 'direccion', 'estadoEnvio'])
            ->where('id_trabajador', Auth::user()->persona->trabajador->id_trabajador)
            ->whereDate('fecha_programada', $fechaObj->format('Y-m-d'))
            ->get();
    }

    // --- NUEVAS FUNCIONES PARA EVIDENCIA ---

    public function abrirModalEvidencia($idPedido)
    {
        $this->pedidoEvidenciaId = $idPedido;
        $this->reset(['fotoEvidencia', 'observacionEntrega']); // Limpiar campos anteriores
        $this->modalEvidenciaOpen = true;
    }

    public function cerrarModalEvidencia()
    {
        $this->modalEvidenciaOpen = false;
        $this->reset(['fotoEvidencia', 'observacionEntrega', 'pedidoEvidenciaId']);
    }

    public function guardarEvidencia()
    {
        $this->validate([
            'fotoEvidencia' => 'required|image|max:10240', // Máximo 10MB
            'observacionEntrega' => 'nullable|string|max:500',
        ], [
            'fotoEvidencia.required' => 'La foto de evidencia es obligatoria.',
            'fotoEvidencia.image' => 'El archivo debe ser una imagen válida.',
            'fotoEvidencia.max' => 'La imagen no debe pesar más de 10MB.'
        ]);

        $pedido = EnvioPedido::find($this->pedidoEvidenciaId);

        if ($pedido) {
            // 1. Guardar la imagen en storage/app/public/evidencias
            $path = $this->fotoEvidencia->store('evidencias_entregas', 'public');

            // 2. Obtener estado 'entregado'
            $estadoEntregado = EstadoEnvioPedido::where('nombre_estado_envio_pedido', 'entregado')->first();

            // 3. Actualizar base de datos
            $datosUpdate = [
                'foto_evidencia' => $path, // Guardamos la ruta relativa
                'observaciones_entrega' => $this->observacionEntrega,
                'fecha_entrega_real' => now(),
                'fecha_actualizacion' => now()
            ];

            if ($estadoEntregado) {
                $datosUpdate['id_estado_envio_pedido'] = $estadoEntregado->id_estado_envio_pedido;
            }

            $pedido->update($datosUpdate);

            // 4. Feedback y cierre
            $this->dispatch('notify',
                title: 'Entrega Registrada',
                description: 'La evidencia se ha guardado correctamente.',
                type: 'success'
            );

            $this->cerrarModalEvidencia();

            // 5. Recargar la lista del día para que se actualice el estado en la vista
            if ($this->diaSeleccionado) {
                $this->cargarPedidosDelDia($this->diaSeleccionado);
            }
        }
    }

    // ---------------------------------------

    public function descargarHojaRuta()
    {
        if (!$this->diaSeleccionado) return;

        $trabajador = Auth::user()->persona->trabajador;
        if (!$trabajador) return;

        $fecha = $this->diaSeleccionado->format('Y-m-d');

        return Excel::download(
            new DespachoDetalleExport($fecha, $trabajador->id_trabajador),
            'hoja_ruta_' . $fecha . '.pdf',
            \Maatwebsite\Excel\Excel::DOMPDF
        );
    }

    public function verEvidencia($id)
    {
        $pedido = EnvioPedido::with(['venta.cliente.persona', 'trabajador.persona'])->find($id);

        if ($pedido) {
            $this->evidenciaPedido = $pedido;
            $this->modalEvidenciaOpen = true;
        }
    }


    public function render()
    {
        $trabajador = Auth::user()->persona->trabajador;

        if (!$trabajador) {
            return view('livewire.inventario.entrega-pedido', [
                'pedidosMes' => collect(),
                'diasEnMes' => 0,
                'espaciosVacios' => 0,
                'nombreMes' => ''
            ]);
        }

        $trabajadorId = $trabajador->id_trabajador;

        $pedidosMes = EnvioPedido::with(['venta.cliente.persona', 'estadoEnvio'])
            ->where('id_trabajador', $trabajadorId)
            ->whereMonth('fecha_programada', $this->mesActual)
            ->whereYear('fecha_programada', $this->anioActual)
            ->get();

        $fechaInicio = Carbon::createFromDate($this->anioActual, $this->mesActual, 1);
        $diasEnMes = $fechaInicio->daysInMonth;
        $diaSemanaInicio = $fechaInicio->dayOfWeek;

        // Ajuste para Lunes = 1
        $diaSemanaInicio = ($diaSemanaInicio == 0) ? 7 : $diaSemanaInicio;
        $espaciosVacios = $diaSemanaInicio - 1;

        return view('livewire.inventario.entrega-pedido', [
            'pedidosMes' => $pedidosMes,
            'diasEnMes' => $diasEnMes,
            'espaciosVacios' => $espaciosVacios,
            'nombreMes' => $fechaInicio->locale('es')->monthName
        ]);
    }
}
