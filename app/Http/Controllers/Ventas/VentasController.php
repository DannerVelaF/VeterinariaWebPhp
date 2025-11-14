<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
use App\Models\MetodoPago;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VentasController extends Controller
{

    use ApiResponse;

    public function registrarVenta(Request $request)
    {
        try {

        } catch (\Exception $e){
            Log::error('Error en regsitar venta: ' . $e->getMessage());
            return $this->serverErrorResponse();
        }

        return response()->json([
            'message' => 'ventas'
        ]);
    }

    public function obtenerMetodosPago()
    {
        $metodos = MetodoPago::where("estado", MetodoPago::ESTADO_ACTIVO)->get();

        return $this->successResponse(
            $metodos,
        );
    }
}
