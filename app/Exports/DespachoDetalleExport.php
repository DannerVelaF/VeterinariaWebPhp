<?php

namespace App\Exports;

use App\Models\EnvioPedido;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DespachoDetalleExport implements FromView, ShouldAutoSize
{
    protected $fecha;
    protected $idTrabajador;

    // Recibimos los parámetros necesarios en el constructor
    public function __construct($fecha, $idTrabajador)
    {
        $this->fecha = $fecha;
        $this->idTrabajador = $idTrabajador;
    }

    // Cargamos la vista con los datos
    public function view(): View
    {
        $pedidos = EnvioPedido::with(['venta.cliente.persona', 'direccion.ubigeo', 'estadoEnvio'])
            ->where('id_trabajador', $this->idTrabajador)
            ->whereDate('fecha_programada', $this->fecha)
            ->orderBy('fecha_programada', 'asc')
            ->get();

        // Obtenemos el nombre del transportista (usuario actual o basado en el ID)
        // Como este export se llama desde el contexto del usuario logueado, podemos usar Auth o buscar el trabajador
        $transportistaNombre = "Transportista";
        if (Auth::check() && Auth::user()->persona) {
            $transportistaNombre = Auth::user()->persona->nombre . ' ' . Auth::user()->persona->apellido_paterno;
        }

        return view('exports.entrega_pedidos', [
            'pedidos' => $pedidos,
            'fecha' => $this->fecha,
            'transportista' => $transportistaNombre
        ]);
    }
}
