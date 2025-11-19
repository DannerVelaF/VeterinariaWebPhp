<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DocumentoController extends Controller
{
    /**
     * Consultar DNI en RENIEC via Decolecta
     */
    public function consultarDNI(Request $request)
    {
        $request->validate([
            'dni' => 'required|digits:8'
        ]);

        try {
            $response = Http::withHeaders([
                "Content-Type" => "application/json",
                'Authorization' => "Bearer " . env("DECOLECTA_API_KEY"),
            ])->withOptions(['verify' => false])
                ->get("https://api.decolecta.com/v1/reniec/dni", [
                    'numero' => $request->dni,
                ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'data' => $response->json()
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => 'Error en la consulta a RENIEC',
                'details' => $response->body()
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error interno del servidor',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Consultar RUC en SUNAT via Decolecta
     */
    public function consultarRUC(Request $request)
    {
        $request->validate([
            'ruc' => 'required|digits:11'
        ]);

        try {
            $response = Http::withHeaders([
                "Content-Type" => "application/json",
                'Authorization' => "Bearer " . env("DECOLECTA_API_KEY"),
            ])->withOptions(['verify' => false])
                ->get("https://api.decolecta.com/v1/sunat/ruc", [
                    'numero' => $request->ruc,
                ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'data' => $response->json()
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => 'Error en la consulta a SUNAT',
                'details' => $response->body()
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error interno del servidor',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
