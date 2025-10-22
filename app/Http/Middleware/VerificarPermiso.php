<?php

namespace App\Http\Middleware;

use App\Models\modulo_opcion;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificarPermiso
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        $rutaActual = $request->route()->getName();

        // Buscar la opción asociada a la ruta
        $opcion = modulo_opcion::where('ruta_laravel', $rutaActual)
            ->where('estado', 'activo')
            ->first();

        // 🚫 Si no existe la opción o no tiene permiso asignado, bloquear
        if (!$opcion || !$opcion->permiso) {
            return redirect()
                ->route('inicio')
                ->with('error', 'Acceso denegado: esta ruta no tiene permiso asignado.');
        }

        $permisoRequerido = $opcion->permiso->nombre_permiso;

        // 🚫 Si el usuario no tiene el permiso requerido, bloquear
        if (!$user->tienePermiso($permisoRequerido)) {
            return redirect()
                ->route('inicio')
                ->with('error', 'No tienes permiso para acceder a esta sección.');
        }

        // ✅ Permitir acceso
        return $next($request);
    }
}
