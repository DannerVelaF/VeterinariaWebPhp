<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VentasController extends Controller
{
    public function registrarVenta(Request $request)
    {
        return response()->json([
            'message' => 'ventas'
        ]);
    }
}
