<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Ubigeo;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class UbigeoController extends Controller
{
    use ApiResponse;

    /**
     * Obtener todos los departamentos únicos
     */
    public function getDepartamentos()
    {
        try {
            $departamentos = Ubigeo::select('departamento')
                ->distinct()
                ->orderBy('departamento')
                ->get()
                ->map(function ($item) {
                    return [
                        'departamento' => $item->departamento,
                        'codigo_ubigeo' => substr($item->codigo_ubigeo, 0, 2) . '0000'
                    ];
                });

            return $this->successResponse($departamentos, "Departamentos obtenidos correctamente");

        } catch (\Exception $e) {
            Log::error('Error obteniendo departamentos: ' . $e->getMessage());
            return $this->serverErrorResponse('Error al obtener los departamentos');
        }
    }

    /**
     * Obtener provincias por departamento
     */
    public function getProvincias($departamento)
    {
        try {
            $provincias = Ubigeo::where('departamento', $departamento)
                ->select('provincia')
                ->distinct()
                ->orderBy('provincia')
                ->get()
                ->map(function ($item) {
                    return [
                        'provincia' => $item->provincia,
                        'codigo_ubigeo' => substr($item->codigo_ubigeo, 0, 4) . '00'
                    ];
                });

            if ($provincias->isEmpty()) {
                return $this->notFoundResponse("No se encontraron provincias para el departamento: $departamento");
            }

            return $this->successResponse($provincias, "Provincias obtenidas correctamente");

        } catch (\Exception $e) {
            Log::error('Error obteniendo provincias - Departamento: ' . $departamento . ' - Error: ' . $e->getMessage());
            return $this->serverErrorResponse('Error al obtener las provincias');
        }
    }

    /**
     * Obtener distritos por provincia
     */
    public function getDistritos($provincia)
    {
        try {
            $distritos = Ubigeo::where('provincia', $provincia)
                ->select('codigo_ubigeo', 'departamento', 'provincia', 'distrito')
                ->orderBy('distrito')
                ->get();

            if ($distritos->isEmpty()) {
                return $this->notFoundResponse("No se encontraron distritos para la provincia: $provincia");
            }

            return $this->successResponse($distritos, "Distritos obtenidos correctamente");

        } catch (\Exception $e) {
            Log::error('Error obteniendo distritos - Provincia: ' . $provincia . ' - Error: ' . $e->getMessage());
            return $this->serverErrorResponse('Error al obtener los distritos');
        }
    }

    /**
     * Obtener ubigeo completo por código
     */
    public function getUbigeo($codigo_ubigeo)
    {
        try {
            $ubigeo = Ubigeo::where('codigo_ubigeo', $codigo_ubigeo)->first();

            if (!$ubigeo) {
                return $this->notFoundResponse("Ubigeo no encontrado");
            }

            return $this->successResponse($ubigeo, "Ubigeo obtenido correctamente");

        } catch (\Exception $e) {
            Log::error('Error obteniendo ubigeo - Código: ' . $codigo_ubigeo . ' - Error: ' . $e->getMessage());
            return $this->serverErrorResponse('Error al obtener el ubigeo');
        }
    }
}
